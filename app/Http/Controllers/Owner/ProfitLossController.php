<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Service\Owner\AssetDepreciationService;
use App\Service\Owner\MonthlyFinancialsService;
use App\Service\Owner\ReportQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ProfitLossController extends Controller
{
    public function __construct(
        private MonthlyFinancialsService $financials,
        private AssetDepreciationService $assetDepreciation,
    ) {}

    public function index(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);

        $f = $this->financials->compute($ownerId, $from, $to, $boatId, $this->depreciation($ownerId, $from, $boatId));

        return view('owner.report.profit_loss_new', compact('from', 'to', 'boatId', 'boats', 'f'));
    }

    public function print(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);

        $f = $this->financials->compute($ownerId, $from, $to, $boatId, $this->depreciation($ownerId, $from, $boatId));
        $settings = $this->companySettings();

        $filename = 'profit-loss-'.$from.'-to-'.$to.'.pdf';

        return pdf_report(view('owner.report.profit_loss_print', compact('from', 'to', 'boatId', 'boats', 'f', 'settings')), [], $filename);
    }

    /**
     * Straight-line asset depreciation for the report's month, derived from the
     * start date so the figure reconciles with the month close for the same
     * calendar month.
     */
    private function depreciation(int $ownerId, string $from, ?int $boatId): float
    {
        $date = Carbon::parse($from);

        return (float) $this->assetDepreciation->forMonth($ownerId, $date->year, $date->month, $boatId)['total'];
    }

    /**
     * Resolve the shared request context for both screen and print.
     *
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
