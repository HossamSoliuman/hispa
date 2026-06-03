<?php

namespace App\DataTable\Owner\Report;

use App\Models\FishStockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class FishHistoryReportDataTable extends DataTables
{
    public function getData(Request $request)
    {
        $owner_id = auth()->user()->id;

        $query = FishStockHistory::with(['fish', 'stock.trip.owner'])
            ->whereHas('stock.trip', function ($q) use ($owner_id) {
                $q->where('owner_id', $owner_id); // فلترة حسب الصيّاد
            })
            ->select('fish_stock_histories.*', DB::raw('(
            SELECT SUM(fsh2.changed_weight)
            FROM fish_stock_histories fsh2
            JOIN fish_stocks fs2 ON fs2.id = fsh2.fish_stock_id
            JOIN trips t2 ON t2.id = fs2.trip_id
            WHERE fsh2.fish_id = fish_stock_histories.fish_id
              AND t2.owner_id = '.$owner_id.'
              AND fsh2.created_at <= fish_stock_histories.created_at
        ) as remaining_weight'));

        // فلترة حسب التاريخ إذا موجود
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // فلترة حسب نوع السمك إذا موجود
        if ($request->filled('fish_id')) {
            $query->where('fish_stock_histories.fish_id', $request->fish_id);
        }

        $data = $query->get();

        // مجموع الرصيد المتبقي النهائي لكل صنف للصيّاد
        $latestRemainingWeights = FishStockHistory::select(
            'fish_stock_histories.fish_id',
            DB::raw('SUM(fish_stock_histories.changed_weight) as remaining_weight')
        )
            ->join('fish_stocks as fs', 'fs.id', '=', 'fish_stock_histories.fish_stock_id')
            ->join('trips as t', 't.id', '=', 'fs.trip_id')
            ->where('t.owner_id', $owner_id)
            ->groupBy('fish_stock_histories.fish_id')
            ->get();

        $totalRemainingWeight = $latestRemainingWeights->sum('remaining_weight');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('fish_name', fn ($row) => optional($row->fish)->scientific_name ?? '---')
            ->addColumn('user_name', fn ($row) => optional($row->stock->trip->owner)->name ?? '---')
            ->addColumn('changed_weight', fn ($row) => number_format($row->changed_weight, 2))
            ->addColumn('remaining_weight', fn ($row) => number_format($row->remaining_weight, 2))
            ->editColumn('operation_type', fn ($row) => $this->getOperationTypeBadge($row->operation_type))
            ->editColumn('created_at', fn ($row) => $row->created_at->format('Y-m-d H:i'))
            ->with([
                'fish_history_count' => $data->count(),
                'total_fish_count' => $data->pluck('fish_id')->unique()->count(),
                'totalWeight' => number_format($totalRemainingWeight, 2).' كغم',
            ])
            ->rawColumns(['operation_type'])
            ->make(true);
    }

    private function getOperationTypeBadge($type)
    {
        return match ($type) {
            'add' => '<span class="badge bg-success">إضافة</span>',
            'update' => '<span class="badge bg-primary">تعديل</span>',
            'delete' => '<span class="badge bg-danger">حذف</span>',
            'sale' => '<span class="badge bg-warning text-dark">بيع</span>',
            'sale_update' => '<span class="badge bg-info text-dark">تعديل بيع</span>',
            'sale_delete' => '<span class="badge bg-secondary">حذف بيع</span>',
            'transfer' => '<span class="badge bg-dark">تحويل</span>',
            default => '<span class="badge bg-light text-dark">'.e($type).'</span>',
        };
    }
}
