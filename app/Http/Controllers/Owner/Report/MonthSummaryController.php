<?php

namespace App\Http\Controllers\Owner\Report;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Service\Owner\MonthlyFinancialsService;
use App\Service\Owner\MonthlyReportsService;
use App\Service\Owner\ReportQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Month financial summary (#23): the full monthly profit-and-loss statement on a
 * single printable page. Every figure comes from {@see MonthlyFinancialsService}
 * so it reconciles line-by-line with the rest of the owner panel; the operating
 * expense lines are grouped by category via {@see MonthlyReportsService}.
 */
class MonthSummaryController extends Controller
{
    public function __construct(
        private MonthlyFinancialsService $financials,
        private MonthlyReportsService $reports,
    ) {}

    public function index(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);

        $f = $this->financials->compute($ownerId, $from, $to, $boatId);
        $expenses = $this->groupedExpenses($ownerId, $from, $to, $boatId);

        return view('owner.report.month_summary', compact('from', 'to', 'boatId', 'boats', 'f', 'expenses'));
    }

    public function print(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);

        $f = $this->financials->compute($ownerId, $from, $to, $boatId);
        $expenses = $this->groupedExpenses($ownerId, $from, $to, $boatId);
        $settings = $this->companySettings();

        $filename = 'month-summary-'.$from.'-to-'.$to.'.pdf';

        return pdf_report(view('owner.report.month_summary_print', compact('from', 'to', 'boatId', 'boats', 'f', 'expenses', 'settings')), [], $filename);
    }

    /**
     * Operating and general expense categories for the period, split into the
     * same two buckets the financial waterfall uses so each sub-total matches
     * {@see MonthlyFinancialsService::compute()} exactly.
     *
     * @return array{operating: array<int, array<string, mixed>>, general: array<int, array<string, mixed>>}
     */
    private function groupedExpenses(int $ownerId, string $from, string $to, ?int $boatId): array
    {
        $rows = collect($this->reports->expensesByCategory($ownerId, $from, $to, $boatId));

        return [
            'operating' => $rows->whereIn('type', ['operating', 'maintenance'])->values()->all(),
            'general' => $rows->whereIn('type', ['general', 'government'])->values()->all(),
        ];
    }

    /**
     * @return array{0: int, 1: string, 2: string, 3: int|null, 4: \Illuminate\Database\Eloquent\Collection}
     */
    private function context(Request $request): array
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $ownerId = Auth::guard('owner')->id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        $boats = Boat::where('owner_id', $ownerId)->get();
        $boatId = $request->filled('boat_id') ? (int) $request->input('boat_id') : null;

        return [$ownerId, $from, $to, $boatId, $boats];
    }

    /**
     * @return array<string, mixed>
     */
    private function companySettings(): array
    {
        $companyName = currentCompany()?->name ?: 'N/A';

        return ownerCompanySettings([
            'qr_code' => app(ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);
    }
}
