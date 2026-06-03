<?php

namespace App\DataTable\Report;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DalalStockReportDataTable extends DataTables
{
    public function getData(Request $request)
    {
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
                    DB::raw('DATE(ds.created_at) as created_date'),  // هنا نحول التاريخ لليوم فقط بدون وقت
                    DB::raw('MIN(dsd.created_at) as first_created_at')
                )
                ->groupBy(
                    'ds.owner_id',
                    'ds.dalal_id',
                    'dsd.fish_name',
                    'owner.name',
                    'dalal.name',
                    DB::raw('DATE(ds.created_at)') // تجميع حسب التاريخ فقط
                );

            // فلتر التاريخ كما هو
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('ds.created_at', [$request->start_date, $request->end_date]);
            }

            if ($request->filled('dalal_id_filter')) {
                $query->where('ds.dalal_id', $request->dalal_id_filter);
            }

            $stocks = $query->get();

            return DataTables::of($stocks)
                ->addIndexColumn()
                ->addColumn('fish_name', fn ($row) => $row->fish_name ?? '---')
                ->addColumn('dalal_name', fn ($row) => $row->dalal_name ?? '---')
                // return numeric weight so client-side can format for display and sorting
                ->addColumn('total_weight', fn ($row) => (float) $row->total_weight)
                ->addColumn('date', function ($row) {
                    $source = $row->first_created_at ?? $row->created_date ?? null;
                    if (! $source) {
                        return '---';
                    }
                    try {
                        return formatHijriDate($source, 'dd/MM/yyyy');
                    } catch (\Throwable $e) {
                        return \Carbon\Carbon::parse($source)->format('d/m/Y');
                    }
                })

                ->with([
                    'total_fish_count' => $stocks->count(),
                    'total_dalal_count' => $stocks->pluck('dalal_id')->unique()->count(),
                    // totalWeight as numeric (kg) so client formats it to ton/kg
                    'totalWeight' => (float) $stocks->sum('total_weight'),

                ])
                ->make(true);
        }
    }
}
