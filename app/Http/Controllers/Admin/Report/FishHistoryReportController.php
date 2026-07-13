<?php

namespace App\Http\Controllers\Admin\Report;

use App\DataTable\Report\FishHistoryReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\Fish;
use App\Models\FishStockHistory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FishHistoryReportController extends Controller
{
    private FishHistoryReportDataTable $datatable;

    public function __construct()
    {
        $this->datatable = new FishHistoryReportDataTable;
        $this->middleware('permission:read_fish_stock_history_report', ['only' => ['index', 'show']]);
    }

    public function index(): View
    {
        $fish = Fish::Active()->get(['id', 'name_ar', 'name_en']);

        return view('admin.report.fish_history', compact('fish'));
    }

    public function getFishHistoryData(Request $request): ?JsonResponse
    {
        return $this->datatable->getData($request);
    }

    /**
     * Render the platform-wide printable fish stock history report (all owners) using
     * the shared standard-compliant view. Mirrors the owner report but without the
     * owner_id scoping, so the admin sees every owner's stock movements. The running
     * "remaining_weight" balance is computed platform-wide per fish.
     */
    public function print(Request $request): Response
    {
        $query = FishStockHistory::with(['fish', 'user'])
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

        $records = $query->orderBy('fish_stock_histories.created_at', 'desc')->get()
            ->map(function (FishStockHistory $record): FishStockHistory {
                $record->fish_name = optional($record->fish)->name ?? '---';
                $record->user_name = optional($record->user)->name ?? '---';

                return $record;
            });

        $totalRecords = $records->count();
        $totalFishTypes = $records->pluck('fish_id')->filter()->unique()->count();
        $totalCatch = $records->filter(fn (FishStockHistory $record): bool => $record->changed_weight > 0)->sum('changed_weight');
        $totalWeight = $this->calculateTotalWeight($request);

        $settings = $this->getCompanySettings();

        $from = $request->start_date ?? null;
        $to = $request->end_date ?? null;
        $fishName = $request->filled('fish_id')
            ? optional(Fish::find($request->fish_id))->name
            : null;

        $filename = 'fish-history-'.($from ?? 'all').'-to-'.($to ?? 'all').'.pdf';

        return pdf_report(view('owner.report.fish_history_print', compact(
            'records',
            'totalRecords',
            'totalFishTypes',
            'totalCatch',
            'totalWeight',
            'settings',
            'from',
            'to',
            'fishName'
        )), [], $filename);
    }

    /**
     * Platform-wide net remaining weight over the applied filters (all owners).
     */
    private function calculateTotalWeight(Request $request): float
    {
        $query = FishStockHistory::query();

        if ($request->filled('fish_id')) {
            $query->where('fish_id', $request->fish_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return (float) $query->sum('changed_weight');
    }

    /**
     * Get platform company settings for the report header (admin oversees all owners,
     * so these come from the global Setting table rather than a per-owner company).
     *
     * @return array<string, mixed>
     */
    private function getCompanySettings(): array
    {
        return adminReportCompanySettings();
    }
}
