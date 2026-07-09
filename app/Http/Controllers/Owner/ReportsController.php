<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Models\CatchDetail;
use App\Models\Fish;
use App\Models\Trip;
use App\Service\Owner\ReportQrService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function fishQuntity(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $ownerId = Auth::guard('owner')->id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        $boats = Boat::where('owner_id', $ownerId)->get();
        $trips = Trip::where('owner_id', $ownerId)->get();
        $fishs = Fish::active()->get();

        $boatId = $request->input('boat_id');
        $tripId = $request->input('trip_id');
        $fishId = $request->input('fish_id');

        $stocks = $this->fishStocks($ownerId, $from, $to, $boatId, $tripId, $fishId);

        return view('owner.reports.fish_quntity', compact('stocks', 'from', 'to', 'boatId', 'boats', 'fishId', 'fishs', 'tripId', 'trips'));
    }

    public function fishQuntityPrint(Request $request): \Illuminate\Http\Response
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $ownerId = Auth::guard('owner')->id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        $boatId = $request->input('boat_id');
        $tripId = $request->input('trip_id');
        $fishId = $request->input('fish_id');

        $stocks = $this->fishStocks($ownerId, $from, $to, $boatId, $tripId, $fishId);

        $totalPrice = $stocks->sum(fn (CatchDetail $stock) => $stock->weight * $stock->price_per_kg);

        $boat = $boatId ? Boat::where('owner_id', $ownerId)->find($boatId) : null;
        $trip = $tripId ? Trip::where('owner_id', $ownerId)->find($tripId) : null;
        $fish = $fishId ? Fish::find($fishId) : null;

        $filters = [
            'from' => $from,
            'to' => $to,
            'boat' => $boat?->name ?? $boat?->name_ar,
            'trip' => $trip?->name ?? $trip?->name_ar,
            'fish' => $fish?->name ?? $fish?->name_ar,
        ];

        $companyName = currentCompany()?->name ?: 'حسبة';
        $settings = ownerCompanySettings([
            'qr_code' => app(ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);

        $filename = 'fish-stock-report-'.$from.'-to-'.$to.'.pdf';
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return pdf_report(view('owner.reports.print.fish-quantity', compact(
            'stocks',
            'totalPrice',
            'filters',
            'settings'
        )), [], $filename, $disposition);
    }

    /**
     * Catch details for the fish stock report, filtered by date range, boat, trip and fish.
     *
     * @return Collection<int, CatchDetail>
     */
    private function fishStocks(int $ownerId, string $from, string $to, ?string $boatId, ?string $tripId, ?string $fishId): Collection
    {
        $catches = CatchDetail::query()
            ->with(['fish', 'unit'])
            ->whereHas('catch', function ($q) use ($ownerId, $from, $to, $boatId, $tripId) {
                $q->where('owner_id', $ownerId)
                    ->whereBetween(DB::raw('DATE(catch_date)'), [$from, $to]);

                if ($tripId) {
                    $q->where('trip_id', $tripId);
                }

                if ($boatId) {
                    $q->whereHas('trip', function ($trip) use ($boatId) {
                        $trip->where('boat_id', $boatId);
                    });
                }
            });

        if ($fishId) {
            $catches->where('fish_id', $fishId);
        }

        return $catches->get();
    }
}
