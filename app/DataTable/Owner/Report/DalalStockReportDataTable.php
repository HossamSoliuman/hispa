<?php

namespace App\DataTable\Owner\Report;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DalalStockReportDataTable extends DataTables
{
    public function getData(Request $request)
    {
        $owner_id = auth()->user()->id; // الصيّاد الحالي
        if ($request->ajax()) {

            $query = DB::table('dalal_stock_details as dsd')
                ->join('dalal_stocks as ds', 'dsd.dalal_stock_id', '=', 'ds.id')
                ->join('users as owner', 'ds.owner_id', '=', 'owner.id')
                ->join('users as dalal', 'ds.dalal_id', '=', 'dalal.id')
                ->select(
                    'owner.name as owner_name',
                    'dalal.name as dalal_name',
                    'dsd.fish_name',
                    DB::raw('SUM(dsd.weight) as total_weight'),
                    DB::raw('"كغم" as unit'),
                    'ds.owner_id',
                    'ds.dalal_id',
                    DB::raw('DATE(ds.created_at) as created_date'),
                    DB::raw('MIN(dsd.created_at) as first_created_at')
                )
                ->where('ds.owner_id', $owner_id) // <-- فلترة حسب الصيّاد
                ->groupBy(
                    'ds.owner_id',
                    'ds.dalal_id',
                    'dsd.fish_name',
                    'owner.name',
                    'dalal.name',
                    DB::raw('DATE(ds.created_at)')
                );

            // فلتر التاريخ إذا موجود
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('ds.created_at', [$request->start_date, $request->end_date]);
            }

            // فلتر حسب الدلال إذا موجود
            if ($request->filled('dalal_id_filter')) {
                $query->where('ds.dalal_id', $request->dalal_id_filter);
            }

            $stocks = $query->get();

            return DataTables::of($stocks)
                ->addIndexColumn()
                ->addColumn('fish_name', fn ($row) => $row->fish_name ?? '---')
                ->addColumn('dalal_name', fn ($row) => $row->dalal_name ?? '---')
                ->addColumn('total_weight', fn ($row) => number_format($row->total_weight, 2).' كغم')
                ->addColumn('date', function ($row) {
                    return $row->first_created_at
                        ? \Carbon\Carbon::parse($row->first_created_at)->format('Y-m-d')
                        : '---';
                })
                ->with([
                    'total_fish_count' => $stocks->count(),
                    'total_dalal_count' => $stocks->pluck('dalal_id')->unique()->count(),
                    'totalWeight' => number_format($stocks->sum('total_weight'), 2).' كغم',
                    'total_records' => $stocks->count(),
                ])
                ->make(true);
        }
    }
}
