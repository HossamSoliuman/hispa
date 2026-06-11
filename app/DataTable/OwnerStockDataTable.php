<?php

namespace App\DataTable;

use App\Models\FishQuantityStock;
use App\Models\User;
use App\Services\Admin\OwnerStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class OwnerStockDataTable extends DataTables
{
    public function __construct(
        private readonly OwnerStockService $ownerStockService
    ) {}

    /**
     * DataTable: list owners with their fish-quantity stock summary (for admin/owner-stock index).
     */
    public function getData(Request $request): JsonResponse
    {
        if (! $request->ajax()) {
            return response()->json([], 400);
        }

        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $query = FishQuantityStock::query()
            ->select([
                DB::raw('COALESCE(trips.owner_id, boats.owner_id) as owner_id'),
                DB::raw('SUM(fish_quantity_stocks.quantity) as total_quantity'),
                DB::raw('SUM(fish_quantity_stocks.quantity * fish_quantity_stocks.price_per_kg) as total_value'),
            ])
            ->leftJoin('trips', 'fish_quantity_stocks.trip_id', '=', 'trips.id')
            ->leftJoin('boats', 'fish_quantity_stocks.boat_id', '=', 'boats.id')
            ->whereRaw('DATE(fish_quantity_stocks.created_at) >= ?', [$from])
            ->whereRaw('DATE(fish_quantity_stocks.created_at) <= ?', [$to])
            ->where(function ($q) {
                $q->whereNotNull('trips.owner_id')->orWhereNotNull('boats.owner_id');
            })
            ->groupBy(DB::raw('COALESCE(trips.owner_id, boats.owner_id)'));

        if ($request->filled('owner_id')) {
            $query->having('owner_id', '=', $request->input('owner_id'));
        }
        if ($request->filled('boat_id')) {
            $query->where('fish_quantity_stocks.boat_id', $request->input('boat_id'));
        }
        if ($request->filled('trip_id')) {
            $query->where('fish_quantity_stocks.trip_id', $request->input('trip_id'));
        }
        if ($request->filled('fish_id')) {
            $query->where('fish_quantity_stocks.fish_id', $request->input('fish_id'));
        }

        $rows = $query->get();
        $ownerIds = $rows->pluck('owner_id')->filter()->unique()->values();
        $owners = User::query()
            ->ownerRole()
            ->whereIn('id', $ownerIds)
            ->get()
            ->keyBy('id');

        $totalQuantity = $rows->sum('total_quantity');
        $totalValue = $rows->sum('total_value');

        return DataTables::of($rows)
            ->addIndexColumn()
            ->addColumn('owner_name', function ($row) use ($owners) {
                $owner = $owners->get($row->owner_id);
                $name = $owner ? $owner->name : '---';
                $url = route('admin.owner-stock.show', $row->owner_id);

                return '<a href="'.e($url).'" class="text-decoration-none fw-medium">'.e($name).'</a>';
            })
            ->addColumn('total_quantity', function ($row) {
                return number_format((float) $row->total_quantity, 2);
            })
            ->addColumn('total_value', function ($row) {
                return number_format((float) $row->total_value, 2);
            })
            ->addColumn('details', function ($row) {
                $url = route('admin.owner-stock.show', $row->owner_id);
                $label = __('admin.owner_stocks.table.details');

                return '<a href="'.e($url).'" class="btn btn-sm btn-info">'.e($label).'</a>';
            })
            ->with([
                'total_owners' => $rows->count(),
                'total_quantity' => $totalQuantity,
                'total_value' => $totalValue,
            ])
            ->rawColumns(['owner_name', 'details'])
            ->make(true);
    }

    /**
     * DataTable: fish-quantity stock rows for one owner (same format as owner/fish-quntity).
     */
    public function getShowData(Request $request, int $ownerId): JsonResponse
    {
        if (! $request->ajax()) {
            return response()->json([], 400);
        }

        $query = $this->ownerStockService->baseQuery($ownerId);
        $this->ownerStockService->applyFilters($query, $request);

        $data = $query->with(['fish', 'unit'])->orderByDesc('created_at')->get();

        $totalQuantity = (float) $data->sum('quantity');
        $totalValue = (float) $data->sum(fn ($row) => (float) $row->quantity * (float) $row->price_per_kg);

        $unit = __('admin.stocks_admin.unit_kg');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('boat_name', fn ($row) => $row->boat ? ($row->boat->name ?? '---') : '---')
            ->addColumn('trip_name', function ($row) {
                if (! $row->trip) {
                    return '---';
                }

                return $row->trip->name ?? $row->trip->number ?? '---';
            })
            ->addColumn('stock_date', fn ($row) => $row->created_at ? $row->created_at->format('Y-m-d H:i') : '---')
            ->addColumn('fish_name', fn ($row) => $row->fish ? ($row->fish->name ?? $row->fish->scientific_name ?? '---') : '---')
            ->addColumn('unit_name', fn ($row) => $row->unit_id ? $row->unit->name : $unit)
            ->addColumn('quantity', fn ($row) => number_format((float) $row->quantity, 2))
            ->addColumn('price_per_kg', fn ($row) => number_format((float) $row->price_per_kg, 2))
            ->addColumn('total_price', function ($row) {
                $total = (float) $row->quantity * (float) $row->price_per_kg;

                return number_format($total, 2);
            })
            ->with([
                'total_quantity' => $totalQuantity,
                'total_value' => $totalValue,
                'unit' => $unit,
            ])
            ->make(true);
    }
}
