<?php

namespace App\DataTable\Report;

use App\Models\FishStockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class FishHistoryReportDataTable extends DataTables
{
    public function getData(Request $request)
    {
        $query = FishStockHistory::with(['fish', 'user'])
            ->select('fish_stock_histories.*', DB::raw('(
            SELECT SUM(fsh2.changed_weight)
            FROM fish_stock_histories fsh2
            WHERE fsh2.fish_id = fish_stock_histories.fish_id
              AND fsh2.created_at <= fish_stock_histories.created_at
        ) as remaining_weight'));

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('fish_id')) {
            $query->where('fish_id', $request->fish_id);
        }

        $totalRecords = $query->count();
        $total_fish_count = $query->distinct('fish_id')->count('fish_id');

        $data = $query->get();

        // مجموع الرصيد المتبقي النهائي لكل صنف (باستخدام نفس الحساب على أحدث سجل لكل صنف)
        $latestRemainingWeights = FishStockHistory::select('fish_id', DB::raw('SUM(changed_weight) as remaining_weight'))
            ->groupBy('fish_id')
            ->get();

        $totalRemainingWeight = $latestRemainingWeights->sum('remaining_weight');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('fish_name', fn ($row) => optional($row->fish)->scientific_name ?? '---')
            ->addColumn('user_name', fn ($row) => optional($row->user)->name ?? '---')
            // return numeric weights (kg) so client formats display and sorting remains numeric
            ->addColumn('changed_weight', fn ($row) => (float) $row->changed_weight)
            ->addColumn('remaining_weight', fn ($row) => (float) $row->remaining_weight)
            ->editColumn('operation_type', fn ($row) => $this->getOperationTypeBadge($row->operation_type))
            ->editColumn('created_at', function ($row) {
                if (! $row->created_at) {
                    return '---';
                }
                try {
                    return formatHijriDate($row->created_at, 'dd/MM/yyyy HH:mm');
                } catch (\Throwable $e) {
                    return $row->created_at->format('d/m/Y H:i');
                }
            })
            ->with([
                'fish_history_count' => $totalRecords,
                'total_fish_count' => $total_fish_count,
                // provide numeric totalRemainingWeight so client formats to ton/kg
                'totalWeight' => (float) $totalRemainingWeight,

            ])
            ->make(true);
    }

    private function getOperationTypeBadge($type)
    {
        $label = __('admin.report.fish_history.operation_types.'.$type);

        return match ($type) {
            'add' => '<span class="badge bg-success">'.$label.'</span>',
            'update' => '<span class="badge bg-primary">'.$label.'</span>',
            'delete' => '<span class="badge bg-danger">'.$label.'</span>',
            'sale' => '<span class="badge bg-warning text-dark">'.$label.'</span>',
            'sale_update' => '<span class="badge bg-info text-dark">'.$label.'</span>',
            'sale_delete' => '<span class="badge bg-secondary">'.$label.'</span>',
            'transfer' => '<span class="badge bg-dark">'.$label.'</span>',
            default => '<span class="badge bg-light text-dark">'.e($label).'</span>',
        };
    }
}
