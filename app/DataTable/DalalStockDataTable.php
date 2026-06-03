<?php

namespace App\DataTable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DalalStockDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $stocks = DB::table('dalal_stock_details as dsd')
                ->join('dalal_stocks as ds', 'dsd.dalal_stock_id', '=', 'ds.id')
                ->join('users as owner', 'ds.owner_id', '=', 'owner.id')
                ->join('users as dalal', 'ds.dalal_id', '=', 'dalal.id')
                ->select(
                    'owner.name as owner_name',
                    'dalal.name as dalal_name',
                    'dsd.fish_name',
                    DB::raw('SUM(dsd.weight) as total_weight'),
                    DB::raw("'".__('admin.units.kg')."' as unit"),
                    'ds.owner_id',
                    'ds.dalal_id'
                )
                ->groupBy('ds.owner_id', 'ds.dalal_id', 'dsd.fish_name', 'owner.name', 'dalal.name')
                ->get();

            // استخراج الإحصائيات
            $totalOwners = $stocks->pluck('owner_name')->unique()->count();
            $totalDalals = $stocks->pluck('dalal_name')->unique()->count();
            $totalItems = $stocks->pluck('fish_name')->unique()->count();
            $totalWeight = $stocks->sum('total_weight');

            return DataTables::of($stocks)
                ->addIndexColumn()
                ->addColumn('details', function ($row) {
                    return '<a href="'.route('admin.dalal-stock.show', $row->dalal_id).'" class="btn btn-sm btn-info">'.__('admin.actions.show').'</a>';
                })
                ->with([
                    'total_owners' => $totalOwners,
                    'total_dalals' => $totalDalals,
                    'total_items' => $totalItems,
                    'total_weight' => number_format($totalWeight, 2),
                ])
                ->rawColumns(['details'])
                ->make(true);
        }
    }

    public function getShowData(Request $request, $dalal_id)
    {
        $stocks = DB::table('dalal_stock_details as dsd')
            ->join('dalal_stocks as ds', 'dsd.dalal_stock_id', '=', 'ds.id')
            ->join('users as owner', 'ds.owner_id', '=', 'owner.id')
            ->join('users as dalal', 'ds.dalal_id', '=', 'dalal.id')
            ->where('ds.dalal_id', $dalal_id)
            ->select(
                'owner.name as owner_name',
                'dalal.name as dalal_name',
                'dsd.fish_name',
                'dsd.weight',
                'dsd.created_at',
                DB::raw("'".__('admin.units.kg')."' as unit"),
            )
            ->get();

        // خذ اسم الدلال من أول عنصر أو --- لو فاضي
        $dalalName = optional($stocks->first())->dalal_name ?? '---';

        // حساب القيم الإجمالية
        $totalWeight = $stocks->sum('weight');
        $totalItems = $stocks->pluck('fish_name')->unique()->count();

        return DataTables::of($stocks)
            ->addIndexColumn()
            ->addColumn('created_at', fn ($row) => \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i'))
            ->with([
                'dalal_name' => $dalalName,
                'total_items' => $totalItems,
                'total_weight' => number_format($totalWeight, 2),
            ])
            ->make(true);
    }
}
