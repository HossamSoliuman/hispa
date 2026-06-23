<?php

namespace App\Http\Controllers\Owner\Report;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Service\Owner\MonthlyReportsService;
use App\Service\Owner\ReportQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * P0 analysis reports: trip profitability (#6), boat profitability (#7) and
 * production by species (#2/#20). Every money figure comes from
 * {@see MonthlyReportsService}.
 */
class ProfitabilityReportController extends Controller
{
    public function __construct(private MonthlyReportsService $reports) {}

    public function tripProfitability(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);
        $rows = $this->reports->tripProfitability($ownerId, $from, $to, $boatId);
        $totals = $this->totals($rows);

        return view('owner.report.trip_profitability', compact('from', 'to', 'boatId', 'boats', 'rows', 'totals'));
    }

    public function tripProfitabilityPrint(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);
        $rows = $this->reports->tripProfitability($ownerId, $from, $to, $boatId);
        $totals = $this->totals($rows);
        $settings = $this->companySettings();

        $filename = 'trip-profitability-'.$from.'-to-'.$to.'.pdf';

        return pdf_report(view('owner.report.trip_profitability_print', compact('from', 'to', 'boatId', 'boats', 'rows', 'totals', 'settings')), [], $filename);
    }

    public function boatProfitability(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);
        $rows = $this->reports->boatProfitability($ownerId, $from, $to);
        $totals = $this->totals($rows);

        return view('owner.report.boat_profitability', compact('from', 'to', 'boats', 'rows', 'totals'));
    }

    public function boatProfitabilityPrint(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);
        $rows = $this->reports->boatProfitability($ownerId, $from, $to);
        $totals = $this->totals($rows);
        $settings = $this->companySettings();

        $filename = 'boat-profitability-'.$from.'-to-'.$to.'.pdf';

        return pdf_report(view('owner.report.boat_profitability_print', compact('from', 'to', 'boats', 'rows', 'totals', 'settings')), [], $filename);
    }

    public function productionBySpecies(Request $request)
    {
        [$ownerId, $from, $to] = $this->context($request);
        $rows = $this->reports->productionBySpecies($ownerId, $from, $to);

        return view('owner.report.production_species', compact('from', 'to', 'rows'));
    }

    public function productionBySpeciesPrint(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);
        $rows = $this->reports->productionBySpecies($ownerId, $from, $to);
        $settings = $this->companySettings();

        $filename = 'production-species-'.$from.'-to-'.$to.'.pdf';

        return pdf_report(view('owner.report.production_species_print', compact('from', 'to', 'rows', 'settings')), [], $filename);
    }

    public function expensesByCategory(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);
        $rows = $this->reports->expensesByCategory($ownerId, $from, $to, $boatId);
        $total = round(array_sum(array_column($rows, 'amount')), 2);

        return view('owner.report.expenses_by_category', compact('from', 'to', 'boatId', 'boats', 'rows', 'total'));
    }

    public function expensesByCategoryPrint(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);
        $rows = $this->reports->expensesByCategory($ownerId, $from, $to, $boatId);
        $total = round(array_sum(array_column($rows, 'amount')), 2);
        $settings = $this->companySettings();

        $filename = 'expenses-category-'.$from.'-to-'.$to.'.pdf';

        return pdf_report(view('owner.report.expenses_by_category_print', compact('from', 'to', 'boatId', 'boats', 'rows', 'total', 'settings')), [], $filename);
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
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{gross_sales: float, net_sales: float, expenses: float, net_profit: float}
     */
    private function totals(array $rows): array
    {
        return [
            'gross_sales' => round(array_sum(array_column($rows, 'gross_sales')), 2),
            'net_sales' => round(array_sum(array_column($rows, 'net_sales')), 2),
            'expenses' => round(array_sum(array_column($rows, 'expenses')), 2),
            'net_profit' => round(array_sum(array_column($rows, 'net_profit')), 2),
        ];
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
