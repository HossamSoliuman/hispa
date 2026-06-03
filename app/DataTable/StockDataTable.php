<?php

namespace App\DataTable;

use App\Models\FishStock;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StockDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $query = FishStock::selectRaw('fish_id, SUM(weight) as total_weight')
                ->groupBy('fish_id')
                ->with('fish')
                ->orderByDesc('total_weight');

            if ($request->filled('search')) {
                $term = $request->search;
                $query->whereHas('fish', fn ($q) => $q->where('scientific_name', 'like', "%{$term}%"));
            }

            $data = $query->get();

            // إحصائيات
            $totalItems = $data->count(); // عدد الأسماك المجمعة
            $totalWeight = $data->sum('total_weight'); // مجموع كل الأوزان المجمعة

            // متوسط الوزن لكل صنف (total weight divided by number of items)
            $averageWeight = $totalItems ? ($totalWeight / $totalItems) : 0;

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', fn ($row) => $row->fish->scientific_name ?? '---')
                ->addColumn('total_weight', fn ($row) => number_format($row->total_weight, 2).' '.__('admin.units.kg'))
                ->addColumn('unit', fn ($row) => __('admin.units.kg'))
                ->addColumn('details', function ($row) {
                    return '<a href="'.route('admin.stocks.show', $row->fish_id).'" class="btn btn-sm btn-info">'.__('admin.actions.show').'</a>';
                })
                ->with([
                    'total_items' => $totalItems,
                    'total_weight' => $totalWeight,
                    'average_weight' => $averageWeight,
                ])
                ->rawColumns(['total_weight', 'unit', 'details'])
                ->make(true);
        }
    }

    public function getShowData(Request $request, $fish_id)
    {
        if ($request->ajax()) {
            // جلب كل الإدخالات المرتبطة بالسمكة المحددة
            $query = FishStock::where('fish_id', $fish_id)
                ->with(['fish', 'addedBy', 'correctedBy']) // تأكد من العلاقات موجودة
                ->orderByDesc('created_at');

            $data = $query->get();

            // الإحصائيات
            $totalItems = $data->count();
            $totalWeight = $data->sum('weight');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', fn ($row) => $row->fish->scientific_name ?? '---')
                ->addColumn('captain_name', fn ($row) => $row->addedBy->name ?? '---')
                ->addColumn('weight_captain', fn ($row) => $row->weight_captain ? number_format($row->weight_captain, 2).' '.__('admin.units.kg') : '---')
                ->addColumn('counter_name', fn ($row) => $row->correctedBy->name ?? '---')
                ->addColumn('weight_counter', fn ($row) => $row->weight_counter ? number_format($row->weight_counter, 2).' '.__('admin.units.kg') : '---')
                ->addColumn('weight', fn ($row) => number_format($row->weight, 2).' '.__('admin.units.kg'))
                ->addColumn('unit', fn ($row) => $row->unit ?? __('admin.units.kg'))
                ->with([
                    'total_items' => $totalItems,
                    'total_weight' => $totalWeight,
                ])
                ->rawColumns(['details'])
                ->make(true);
        }

    }
}
