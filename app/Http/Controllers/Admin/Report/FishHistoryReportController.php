<?php

namespace App\Http\Controllers\Admin\Report;

use App\DataTable\Report\FishHistoryReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\Fish;
use Illuminate\Http\Request;

class FishHistoryReportController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new FishHistoryReportDataTable;
        $this->middleware('permission:read_fish_stock_history_report', ['only' => ['index', 'show']]);

    }

    public function index()
    {
        $fish = Fish::Active()->get();

        return view('admin.report.fish_history', compact('fish'));
    }

    public function getFishHistoryData(Request $request)
    {
        return $this->datatable->getData($request);

    }

    /**
     * Print fish stock history report (openable in new tab)
     * Accepts optional filters: start_date, end_date, fish_id
     */
    public function print(Request $request)
    {
        $query = \App\Models\FishStockHistory::with(['fish', 'user'])
            ->select('fish_stock_histories.*')
            ->selectRaw('(
                SELECT SUM(fsh2.changed_weight)
                FROM fish_stock_histories fsh2
                WHERE fsh2.fish_id = fish_stock_histories.fish_id
                  AND fsh2.created_at <= fish_stock_histories.created_at
            ) as remaining_weight');

        if ($request->filled('start_date')) {
            $query->whereDate('fish_stock_histories.created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('fish_stock_histories.created_at', '<=', $request->end_date);
        }

        if ($request->filled('fish_id')) {
            $query->where('fish_stock_histories.fish_id', $request->fish_id);
        }

        $records = $query->orderBy('fish_stock_histories.created_at', 'desc')->get();

        // prepare operation badge html for each record (reuse same labels as DataTable)
        $records->transform(function ($rec) {
            $label = trans('admin.report.fish_history.operation_types.'.($rec->operation_type ?? ''));
            $badge = '<span class="badge bg-light text-dark">'.e($label).'</span>';
            switch ($rec->operation_type) {
                case 'add':
                    $badge = '<span class="badge bg-success">'.e($label).'</span>';
                    break;
                case 'update':
                    $badge = '<span class="badge bg-primary">'.e($label).'</span>';
                    break;
                case 'delete':
                    $badge = '<span class="badge bg-danger">'.e($label).'</span>';
                    break;
                case 'sale':
                    $badge = '<span class="badge bg-warning text-dark">'.e($label).'</span>';
                    break;
                case 'sale_update':
                    $badge = '<span class="badge bg-info text-dark">'.e($label).'</span>';
                    break;
                case 'sale_delete':
                    $badge = '<span class="badge bg-secondary">'.e($label).'</span>';
                    break;
                case 'transfer':
                    $badge = '<span class="badge bg-dark">'.e($label).'</span>';
                    break;
            }
            $rec->op_badge = $badge;

            return $rec;
        });

        $totalRecords = $records->count();
        $totalFishTypes = $records->pluck('fish_id')->filter()->unique()->count();
        $totalCatch = $records->filter(fn ($r) => $r->changed_weight > 0)->sum('changed_weight');

        // calculate total remaining weight across latest remaining weights per fish
        $latestRemainingWeights = \App\Models\FishStockHistory::select('fish_id', \Illuminate\Support\Facades\DB::raw('SUM(changed_weight) as remaining_weight'))
            ->groupBy('fish_id')
            ->get();
        $totalRemainingWeight = $latestRemainingWeights->sum('remaining_weight');

        $settings = [
            'title' => \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'حسبة',
            'address' => \App\Models\Setting::where('key', 'address')->value('value') ?? '',
            'phone' => \App\Models\Setting::where('key', 'phone')->value('value') ?? '',
            'email' => \App\Models\Setting::where('key', 'email')->value('value') ?? '',
            'logo' => \App\Models\Setting::where('key', 'logo')->value('value') ?? '',
        ];

        $from = $request->start_date ?? null;
        $to = $request->end_date ?? null;
        $fishName = null;
        if ($request->filled('fish_id')) {
            $fishName = \App\Models\Fish::find($request->fish_id)->scientific_name ?? null;
        }

        return view('admin.report.fish_history_print', compact(
            'records',
            'totalRecords',
            'totalFishTypes',
            'totalCatch',
            'totalRemainingWeight',
            'settings',
            'from',
            'to',
            'fishName'
        ));
    }
}
