<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Service\Owner\MonthlyFinancialsService;
use App\Service\Owner\ReportQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfitLossController extends Controller
{
    public function __construct(private MonthlyFinancialsService $financials) {}

    public function index(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);

        $f = $this->financials->compute($ownerId, $from, $to, $boatId);

        return view('owner.report.profit_loss_new', compact('from', 'to', 'boatId', 'boats', 'f'));
    }

    public function print(Request $request)
    {
        [$ownerId, $from, $to, $boatId, $boats] = $this->context($request);

        $f = $this->financials->compute($ownerId, $from, $to, $boatId);
        $settings = $this->companySettings();

        $filename = 'profit-loss-'.$from.'-to-'.$to.'.pdf';

        return pdf_report(view('owner.report.profit_loss_print', compact('from', 'to', 'boatId', 'boats', 'f', 'settings')), [], $filename);
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
