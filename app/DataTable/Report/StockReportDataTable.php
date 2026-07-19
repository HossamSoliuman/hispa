<?php

namespace App\DataTable\Report;

use App\Models\FishQuantityStock;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StockReportDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if (! $request->ajax()) {
            return null;
        }

        $query = FishQuantityStock::query()
            ->with(['fish', 'unit', 'trip.boat.captain'])
            ->orderByDesc('created_at');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('fish_type')) {
            $query->where('fish_id', $request->fish_type);
        }

        $data = $query->get();
        $totalWeight = formatWeightByUnit($data->map(fn ($row) => (object) [
            'weight' => $row->quantity,
            'unit' => $row->unit,
        ]));

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('name', fn ($row) => $row->fish?->name ?? '---')
            ->addColumn('total_weight', fn ($row) => number_format($row->quantity, 2).' '.($row->unit?->name ?: __('admin.units.kg')))
            ->addColumn('added_by', fn ($row) => $row->trip?->boat?->captain?->name ?? '---')
            ->addColumn('weight_captain', fn ($row) => number_format($row->quantity, 2).' '.($row->unit?->name ?: __('admin.units.kg')))
            ->addColumn('weight_counter', fn () => '---')
            ->addColumn('weight_difference', fn () => '<span class="text-muted">---</span>')
            ->addColumn('correct_by', fn () => '---')
            ->addColumn('date', fn ($row) => $row->created_at?->format('Y-m-d h:i A') ?? '---')
            ->with([
                'total_fish_count' => $data->pluck('fish_id')->unique()->count(),
                'totalWeight' => $totalWeight,
            ])
            ->rawColumns(['weight_difference'])
            ->make(true);
    }
}
