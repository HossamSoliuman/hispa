<?php

namespace App\Http\Controllers\Owner\Report;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Service\Owner\MonthClosingService;
use App\Service\Owner\ReportQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Annual summary (الملخص السنوي): a year-level roll-up of the owner's monthly
 * closings — total sales, owner revenue, expenses and depreciation across the
 * year, plus a month-by-month breakdown. Only closed months carry figures; the
 * data is read from the frozen {@see \App\Models\MonthClosing} snapshots so it
 * reconciles with each month's own closing report. Defaults to the current year.
 */
class AnnualSummaryController extends Controller
{
    public function __construct(private MonthClosingService $service) {}

    public function index(Request $request)
    {
        [$ownerId, $year, $boatId, $boats] = $this->context($request);

        $summary = $this->service->annualSummary($ownerId, $year, $boatId);

        return view('owner.report.annual_summary', compact('summary', 'year', 'boatId', 'boats'));
    }

    public function print(Request $request)
    {
        [$ownerId, $year, $boatId, $boats] = $this->context($request);

        $summary = $this->service->annualSummary($ownerId, $year, $boatId);
        $settings = $this->companySettings();

        $filename = 'annual-summary-'.$year.'.pdf';

        return pdf_report(view('owner.report.annual_summary_print', compact('summary', 'year', 'boatId', 'boats', 'settings')), [], $filename);
    }

    /**
     * Resolve the authenticated owner and the report's year/boat selection.
     *
     * @return array{0: int, 1: int, 2: int|null, 3: \Illuminate\Database\Eloquent\Collection<int, \App\Models\Boat>}
     */
    private function context(Request $request): array
    {
        $ownerId = Auth::guard('owner')->id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        $year = (int) $request->input('year', now()->year);
        $boats = Boat::where('owner_id', $ownerId)->get();
        $boatId = $request->filled('boat_id') ? (int) $request->input('boat_id') : null;

        return [$ownerId, $year, $boatId, $boats];
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
