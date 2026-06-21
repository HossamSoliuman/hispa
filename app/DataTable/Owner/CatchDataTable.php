<?php

namespace App\DataTable\Owner;

use App\Models\CatchModel;
use App\Models\FishQuantityStock;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CatchDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $owner = auth()->user();
            $owner_id = $owner->id;
            $trips = Trip::with('catches', 'catches.details')
                ->whereNotNull('end_date')
                ->where('owner_id', $owner_id)
                ->when($request->filled('from_date'), fn ($q) => $q->whereDate('start_date', '>=', $request->from_date))
                ->when($request->filled('to_date'), fn ($q) => $q->whereDate('end_date', '<=', $request->to_date))
                ->orderBy('start_date', 'desc');
            if ($request->filled('has_catch')) {
                if ($request->has_catch == '1') {
                    $trips->whereHas('catches');
                } elseif ($request->has_catch == '0') {
                    $trips->whereDoesntHave('catches');
                }
            }
            if ($request->filled('fish_id')) {
                $trips->whereHas('catches.details', function ($q) use ($request) {
                    $q->where('fish_id', $request->fish_id);
                });
            }
            if ($request->filled('boat_id')) {
                $trips->where('boat_id', $request->boat_id);
            }
            $trips = $trips->get();

            $total_trips = $trips->whereNotNull('catches')->count();
            $total_revenue = $trips->sum(fn ($trip) => $trip->catches?->total_amount ?? 0);
            $total_weight = $trips->sum(fn ($trip) => $trip->catches?->total_weight ?? 0);

            $summary = [
                'total_trips' => $total_trips,
                'total_fish_types' => 0,
                'total_revenue' => $total_revenue,
                'avg_revenue_per_trip' => $total_trips > 0 ? $total_revenue / $total_trips : 0,
                'total_weight_kg' => $total_weight,
                'avg_weight_per_trip_kg' => $total_trips > 0 ? $total_weight / $total_trips : 0,
                'avg_price_per_kg' => $total_weight > 0 ? $total_revenue / $total_weight : 0,
            ];

            return datatables()->of($trips)
                ->addIndexColumn()
                ->addColumn('trip', fn ($row) => $row->name)
                ->addColumn('boat', fn ($row) => $row->boat->name ?: '-')
                ->addColumn('total_weight', fn ($row) => number_format($row->catches?->total_weight ?? 0, 2))
                ->addColumn('total_amount', fn ($row) => number_format($row->catches?->total_amount ?? 0, 2))
                ->addColumn('start_date', fn ($row) => optional($row->start_date)->format('Y-m-d'))
                ->addColumn('end_date', fn ($row) => optional($row->end_date)->format('Y-m-d'))
                ->addColumn('action', function ($row) {
                    if (blank($row->catches)) {
                        return '<a href="'.route('owner.catch.create', ['trip_id' => $row->id]).'" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-plus"></i>'.__('owner.catch.add_catch').'
                                </a>';
                    } else {
                        $catch = CatchModel::where('trip_id', $row->id)->first();
                        $remaining = 0;
                        if ($catch) {
                            $remaining = FishQuantityStock::where('catch_id', $row->catches->id)
                                ->where('trip_id', $row->id)
                                ->sum('quantity');
                        }
                        $actions = '<a href="'.route('owner.catch.show', $row->catches?->id).'" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-eye"></i> '.__('owner.catch.view').'
                                    </a>
                                    <a href="'.route('owner.catch.edit', $row->catches?->id).'" class="btn btn-sm btn-outline-warning mx-1" title="'.__('owner.actions.edit').'">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="'.route('owner.printCatchReport', ['id' => $row->catches?->id]).'"  target="_blank" class="btn btn-sm btn-outline-secondary mx-1" title="طباعه">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <a href="#" onclick="deleteRecord('.$row->catches?->id.')" class="btn btn-sm btn-outline-danger" title="'.__('owner.actions.delete').'">
                                        <i class="bi bi-trash"></i>
                                    </a>';

                        if ($remaining > 0) {
                            $actions .= '<a href="'.route('owner.sales.create', ['trip_id' => $row->id]).'" 
                                        class="btn btn-sm btn-outline-primary mx-1" 
                                        title="بيع المصيد">
                                            <i class="bi bi-cart"></i>
                                        </a>';
                        }

                        return $actions;
                    }
                })
                ->rawColumns(['action'])
                ->with(['summary' => $summary])
                ->make(true);

        }

    }

    public function getFishStats(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('sale_details')
                ->select(
                    'fish_id',
                    'fish_name',
                    DB::raw('COUNT(DISTINCT sale_id) as catch_count'),
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(weight) as total_weight_kg'),
                    DB::raw('SUM(sale_details.total_price) as total_revenue'),
                    DB::raw('AVG(price_per_kilo) as avg_price_per_kg'),
                    DB::raw('MAX(sales.created_at) as date')
                )
                ->join('sales', 'sale_details.sale_id', '=', 'sales.id');

            if ($request->filled('fish_id')) {
                $query->where('sale_details.fish_id', $request->fish_id);
            }

            if ($request->filled('boat_id')) {
                $query->join('trips', 'trips.id', '=', 'sales.trip_id')
                    ->where('trips.boat_id', $request->boat_id);
            }

            if ($request->filled('from_date')) {
                $query->whereDate('sales.created_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('sales.created_at', '<=', $request->to_date);
            }

            $query->groupBy('fish_id', 'fish_name');

            $data = $query->get();

            $average_weight_kg = $data->avg(fn ($item) => $item->total_weight_kg);

            $data->transform(function ($item) use ($average_weight_kg) {
                $item->total_weight_kg_formatted = number_format($item->total_weight_kg, 2);
                $item->avg_price_per_kg_formatted = number_format($item->avg_price_per_kg, 2);
                $item->total_revenue_formatted = number_format($item->total_revenue, 2);

                if ($item->total_weight_kg > $average_weight_kg * 1.2) {
                    $item->performance = '<span class="bg-success text-white px-2 py-1" style="font-weight: bold;">ممتاز</span>';
                } elseif ($item->total_weight_kg >= $average_weight_kg * 0.8) {
                    $item->performance = '<span class="bg-warning text-white px-2 py-1" style="font-weight: bold;">جيد</span>';
                } else {
                    $item->performance = '<span class="bg-danger text-white px-2 py-1" style="font-weight: bold;">ضعيف</span>';
                }

                return $item;
            });

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('type', fn ($row) => $row->fish_name)
                ->addColumn('catch_count', fn ($row) => $row->catch_count)
                ->addColumn('total_quantity', fn ($row) => $row->total_quantity)
                ->addColumn('total_weight_kg', fn ($row) => $row->total_weight_kg_formatted)
                ->addColumn('avg_price_per_kg', fn ($row) => $row->avg_price_per_kg_formatted)
                ->addColumn('total_revenue', fn ($row) => $row->total_revenue_formatted)
                ->addColumn('performance', fn ($row) => $row->performance)
                ->rawColumns(['performance'])
                ->make(true);
        }
    }
}
