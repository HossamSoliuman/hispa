<?php

namespace App\DataTable\Owner;

use App\Models\FishQuantityStock;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StockDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = FishQuantityStock::query()
                ->selectRaw('fish_id, unit_id, SUM(quantity) as total_weight')
                ->groupBy('fish_id', 'unit_id')
                ->with(['fish', 'unit'])
                ->orderByDesc('total_weight');

            $data = $query->get();
            $totalItems = $data->pluck('fish_id')->unique()->count();
            $totalWeight = formatWeightByUnit($data->map(fn ($row) => (object) [
                'weight' => $row->total_weight,
                'unit' => $row->unit,
            ]));

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', fn ($row) => $row->fish->name ?? '---')
                ->addColumn('total_weight', fn ($row) => number_format($row->total_weight, 2).' '.($row->unit?->name ?: __('admin.units.kg')))
                ->addColumn('unit', fn ($row) => $row->unit?->name ?: __('admin.units.kg'))
                ->addColumn('details', function ($row) {
                    return '<a href="'.route('admin.stocks.show', $row->fish_id).'" class="btn btn-sm btn-info">'.__('admin.actions.show').'</a>';
                })
                ->with([
                    'total_items' => $totalItems,
                    'total_weight' => $totalWeight,
                ])
                ->rawColumns(['total_weight', 'unit', 'details'])
                ->make(true);
        }

        return null;
    }

    public function getShowData(Request $request, int $fishId)
    {
        if ($request->ajax()) {
            $data = FishQuantityStock::query()
                ->where('fish_id', $fishId)
                ->with(['fish', 'unit', 'trip.boat.captain'])
                ->orderByDesc('created_at')
                ->get();

            $totalWeight = formatWeightByUnit($data->map(fn ($row) => (object) [
                'weight' => $row->quantity,
                'unit' => $row->unit,
            ]));

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', fn ($row) => $row->fish->name ?? '---')
                ->addColumn('captain_name', fn ($row) => $row->trip?->boat?->captain?->name ?? '---')
                ->addColumn('weight_captain', fn ($row) => number_format($row->quantity, 2).' '.($row->unit?->name ?: __('admin.units.kg')))
                ->addColumn('counter_name', fn () => '---')
                ->addColumn('weight_counter', fn () => '---')
                ->addColumn('weight', fn ($row) => number_format($row->quantity, 2).' '.($row->unit?->name ?: __('admin.units.kg')))
                ->addColumn('unit', fn ($row) => $row->unit?->name ?: __('admin.units.kg'))
                ->with([
                    'total_items' => $data->count(),
                    'total_weight' => $totalWeight,
                ])
                ->make(true);
        }

        return null;
    }
}
