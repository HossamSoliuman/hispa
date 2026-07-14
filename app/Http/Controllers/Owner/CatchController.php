<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\CatchDataTable;
use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CatchRequest;
use App\Models\Boat;
use App\Models\CatchDetail;
use App\Models\CatchModel;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Trip;
use App\Models\Unit;
use App\Services\TripTransitionService;
use App\Traits\CatchStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CatchController extends Controller
{
    use CatchStatistics;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $datatable;

    public function __construct()
    {
        $this->datatable = new CatchDataTable;

    }

    public function index()
    {
        $fish = Fish::Active()->get();
        $boats = Boat::Active()->where('owner_id', auth()->id())->get();

        return view('owner.catch.index', compact('fish', 'boats'));
    }

    public function getCatchData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    public function getFishStats(Request $request)
    {
        return $this->datatable->getFishStats($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getShowDetailStockData(Request $request, $fish_id)
    {
        return $this->datatable->getShowData($request, $fish_id);
    }

    public function show($id)
    {
        $catch = $this->ownerCatchQuery()
            ->with(['trip', 'trip.boat', 'details.fish', 'details.unit'])
            ->findOrFail($id);

        return view('owner.catch.show', compact('catch'));
    }

    /**
     * Catches limited to the logged-in owner's trips.
     *
     * @return \Illuminate\Database\Eloquent\Builder<CatchModel>
     */
    private function ownerCatchQuery()
    {
        return CatchModel::whereHas('trip', function ($trip) {
            $trip->where('owner_id', auth()->id());
        });
    }

    public function create(Request $request)
    {
        $trips = Trip::where('owner_id', auth()->id())->orderByDesc('id')->get();
        $fish = Fish::Active()->get();
        $units = Unit::active()->orderByDesc('is_default')->get();
        $tripId = $request->query('trip_id');

        $selectedTrip = null;
        if ($tripId) {
            $selectedTrip = Trip::where('owner_id', auth()->id())->with('boat')->find($tripId);
        }

        return view('owner.catch.create', compact('trips', 'fish', 'units', 'selectedTrip'));
    }

    public function edit($id)
    {
        $catch = $this->ownerCatchQuery()->with(['trip.boat', 'details.fish'])->findOrFail($id);
        $trips = Trip::where('owner_id', auth()->id())->orderByDesc('id')->get();
        $fish = Fish::Active()->get();
        $units = Unit::active()->orderByDesc('is_default')->get();
        $selectedTrip = $catch->trip;

        return view('owner.catch.edit', compact('catch', 'trips', 'fish', 'units', 'selectedTrip'));
    }

    public function update(CatchRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $catch = $this->ownerCatchQuery()->findOrFail($id);
            $trip = Trip::where('owner_id', auth()->id())->findOrFail($request->trip_id);
            $boatId = $trip->boat_id;

            $existingStocks = FishQuantityStock::where('catch_id', $catch->id)->get();

            // Snapshot each existing line BY POSITION, so editing a catch never
            // resets its sold/paid state even when a row's fish or unit changes.
            // Sold = landed weight minus the stock still remaining for that line.
            $lineSnapshots = CatchDetail::where('catch_id', $catch->id)
                ->orderBy('id')
                ->get()
                ->map(function (CatchDetail $detail) use ($existingStocks): array {
                    $weight = (float) $detail->weight;
                    $remaining = (float) ($existingStocks
                        ->first(fn ($stock) => $stock->fish_id === $detail->fish_id && $stock->unit_id === $detail->unit_id)?->quantity ?? 0);

                    return [
                        'fish_id' => $detail->fish_id,
                        'unit_id' => $detail->unit_id,
                        'sold' => max($weight - $remaining, 0),
                        'fully_sold' => $weight > 0 && $remaining <= 0,
                        'price_per_kg' => (float) $detail->price_per_kg,
                    ];
                })
                ->values()
                ->all();

            CatchDetail::where('catch_id', $catch->id)->delete();
            FishQuantityStock::where('catch_id', $catch->id)->delete();

            $catch->update([
                'trip_id' => $request->trip_id,
                'car_type' => $request->car_type,
                'driver_name' => $request->driver_name,
                'car_plate_number' => $request->car_plate_number,
                'fish_source' => $request->fish_source,
                'temperature' => $request->temperature,
            ]);

            $defaultUnitId = Unit::defaultId();
            $totalWeight = 0;
            $saleLineChanges = [];

            foreach ($request->fish_id as $index => $fishId) {

                $weight = (float) $request->weight[$index];
                $unitId = $request->unit_id[$index] ?? $defaultUnitId;

                $snapshot = $lineSnapshots[$index] ?? null;
                $oldSold = (float) ($snapshot['sold'] ?? 0);
                $fullySold = (bool) ($snapshot['fully_sold'] ?? false);
                $pricePerKg = (float) ($snapshot['price_per_kg'] ?? 0);

                // A fully-sold line follows the new landed weight (its invoice is
                // recomputed to match); a partially-sold line keeps what was sold.
                $sold = $fullySold ? $weight : min($oldSold, $weight);

                CatchDetail::create([
                    'catch_id' => $catch->id,
                    'fish_id' => $fishId,
                    'unit_id' => $unitId,
                    'fish_name' => optional(Fish::find($fishId))->name,
                    'weight' => $weight,
                    'price_per_kg' => $pricePerKg,
                    'total_price' => $pricePerKg * $weight,
                ]);

                $remainingQuantity = max($weight - $sold, 0);

                $stock = FishQuantityStock::firstOrCreate(
                    [
                        'fish_id' => $fishId,
                        'unit_id' => $unitId,
                        'catch_id' => $catch->id,
                        'trip_id' => $request->trip_id,
                        'boat_id' => $boatId,
                    ],
                    [
                        'quantity' => 0,
                    ]
                );
                $stock->increment('quantity', $remainingQuantity);

                if ($snapshot !== null && $oldSold > 0) {
                    $saleLineChanges[] = [
                        'old_fish_id' => $snapshot['fish_id'],
                        'old_unit_id' => $snapshot['unit_id'],
                        'new_fish_id' => $fishId,
                        'new_unit_id' => $unitId,
                        'new_sold' => $sold,
                        'rescale_weight' => $fullySold,
                    ];
                }

                $totalWeight += $weight;
            }

            $catch->update([
                'total_weight' => $totalWeight,
            ]);

            $this->syncSalesWithCatch($catch, $saleLineChanges);

            DB::commit();

            return redirect()
                ->route('owner.catch.index')
                ->with('success', 'تم تعديل المصيد بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cascade catch-line edits (fish, unit, weight) onto the invoices sold from
     * this catch, then recompute each affected invoice's totals so an edit keeps
     * the catch and its sales consistent.
     *
     * @param  array<int, array{old_fish_id: int, old_unit_id: int, new_fish_id: int, new_unit_id: int, new_sold: float, rescale_weight: bool}>  $lineChanges
     */
    private function syncSalesWithCatch(CatchModel $catch, array $lineChanges): void
    {
        if ($lineChanges === []) {
            return;
        }

        $saleIds = Sale::where('catch_id', $catch->id)->pluck('id');
        if ($saleIds->isEmpty()) {
            return;
        }

        $affectedSaleIds = [];

        foreach ($lineChanges as $change) {
            $details = SaleDetail::whereIn('sale_id', $saleIds)
                ->where('fish_id', $change['old_fish_id'])
                ->where('unit_id', $change['old_unit_id'])
                ->get();

            if ($details->isEmpty()) {
                continue;
            }

            $fishName = optional(Fish::find($change['new_fish_id']))->name;
            $currentSold = (float) $details->sum('weight');

            foreach ($details as $detail) {
                $weight = (float) $detail->weight;

                if ($change['rescale_weight'] && $currentSold > 0) {
                    $weight = round($change['new_sold'] * ((float) $detail->weight / $currentSold), 2);
                }

                $detail->update([
                    'fish_id' => $change['new_fish_id'],
                    'unit_id' => $change['new_unit_id'],
                    'fish_name' => $fishName,
                    'weight' => $weight,
                    'total_price' => round((float) $detail->price_per_kilo * $weight, 2),
                ]);

                $affectedSaleIds[$detail->sale_id] = true;
            }
        }

        Sale::with('details')
            ->whereIn('id', array_keys($affectedSaleIds))
            ->get()
            ->each(fn (Sale $sale) => $this->recalculateSaleTotals($sale));
    }

    private function recalculateSaleTotals(Sale $sale): void
    {
        $previouslyPaid = (float) $sale->total_price - (float) $sale->remaining_total;

        $totalPrice = round((float) $sale->details->sum('total_price'), 2);
        $commissionAmount = round($totalPrice * (float) $sale->commission_rate / 100, 2);
        $laborAmount = round($totalPrice * (float) $sale->labor_rate / 100, 2);
        $netOwnerAmount = round($totalPrice - $commissionAmount - $laborAmount, 2);

        $paidAmount = match ($sale->payment_status) {
            'paid' => $totalPrice,
            'partially_paid' => min($previouslyPaid, $totalPrice),
            default => 0.0,
        };

        $sale->updateQuietly([
            'total_price' => $totalPrice,
            'commission_amount' => $commissionAmount,
            'labor_amount' => $laborAmount,
            'net_owner_amount' => $netOwnerAmount,
            'remaining_total' => round(max($totalPrice - $paidAmount, 0), 2),
        ]);
    }

    public function store(CatchRequest $request, TripTransitionService $tripTransition)
    {
        try {
            DB::beginTransaction();

            $trip = Trip::where('owner_id', auth()->id())->findOrFail($request->trip_id);
            $boatId = $trip->boat_id;

            $catch = CatchModel::create([
                'trip_id' => $request->trip_id,
                'owner_id' => auth()->user()->getAuthIdentifier(),
                'catch_date' => now()->format('Y-m-d H:i:s'),
                'total_weight' => 0,
                'total_amount' => 0,
                'car_type' => $request->car_type,
                'driver_name' => $request->driver_name,
                'car_plate_number' => $request->car_plate_number,
                'fish_source' => $request->fish_source,
                'temperature' => $request->temperature,
            ]);

            $defaultUnitId = Unit::defaultId();
            $totalWeight = 0;

            foreach ($request->fish_id as $index => $fishId) {

                $weight = $request->weight[$index];
                $unitId = $request->unit_id[$index] ?? $defaultUnitId;

                CatchDetail::create([
                    'catch_id' => $catch->id,
                    'fish_id' => $fishId,
                    'unit_id' => $unitId,
                    'fish_name' => optional(Fish::find($fishId))->name,
                    'weight' => $weight,
                ]);

                $stock = FishQuantityStock::firstOrCreate(
                    [
                        'fish_id' => $fishId,
                        'unit_id' => $unitId,
                        'catch_id' => $catch->id,
                        'trip_id' => $request->trip_id,
                        'boat_id' => $boatId,
                    ],
                    [
                        'quantity' => 0,
                    ]
                );
                $stock->increment('quantity', $weight);

                $totalWeight += $weight;
            }

            $catch->update([
                'total_weight' => $totalWeight,
            ]);

            if ($trip->status === TripStatus::Finished) {
                $tripTransition->transition($trip, TripStatus::ReadyToSell);
            }

            DB::commit();

            return redirect()
                ->route('owner.catch.index')
                ->with('success', 'تم إضافة المصيد بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function printCatchReport(Request $request, $id = null)
    {
        $catch = $this->ownerCatchQuery()
            ->with(['trip', 'trip.boat', 'details.fish', 'details.unit'])
            ->findOrFail($id);

        $companyName = currentCompany()?->name ?: 'حسبة';
        $settings = ownerCompanySettings([
            'qr_code' => app(\App\Service\Owner\ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);

        $tripNumber = $catch->trip?->number ?? $id;
        $filename = 'catch-'.trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower((string) $tripNumber)), '-').'.pdf';

        return pdf_report(view('owner.reports.print.catch-report', compact('catch', 'settings')), [], $filename);
    }

    /**
     * Print the road/transport delivery note (إيصال التسليم): car + driver
     * details plus the list of catch quantities with their weight units.
     */
    public function printDeliveryNote(Request $request, $id): \Illuminate\Http\Response
    {
        $catch = $this->ownerCatchQuery()
            ->with(['trip', 'trip.boat', 'details.fish', 'details.unit'])
            ->findOrFail($id);

        $companyName = currentCompany()?->name ?: 'حسبة';
        $settings = ownerCompanySettings([
            'qr_code' => app(\App\Service\Owner\ReportQrService::class)->dataUri("Delivery Note: {$companyName} #{$catch->id}"),
        ]);

        $filename = 'delivery-note-'.$catch->id.'.pdf';
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return pdf_report(view('owner.reports.print.delivery-note', compact('catch', 'settings')), [], $filename, $disposition);
    }

    /**
     * Print the product sticker card (بطاقة المنتج) glued on the cold box.
     */
    public function printProductCard(Request $request, $id): \Illuminate\Http\Response
    {
        $catch = $this->ownerCatchQuery()
            ->with(['details.fish', 'details.unit'])
            ->findOrFail($id);

        $companyName = currentCompany()?->name ?: 'حسبة';
        $settings = ownerCompanySettings();

        $seasonStart = now()->startOfYear();
        $seasonEnd = now()->endOfYear();

        $filename = 'product-card-'.$catch->id.'.pdf';
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return pdf_report(view('owner.reports.print.product-card', compact('catch', 'settings', 'seasonStart', 'seasonEnd')), [], $filename, $disposition);
    }

    /**
     * Print the currently filtered catch records as a single PDF report.
     * Mirrors the filters applied on the catch management listing.
     */
    public function printCatchesReport(Request $request): \Illuminate\Http\Response
    {
        $ownerId = auth()->id();

        $query = Trip::with(['boat', 'catches.details.fish', 'catches.details.unit'])
            ->whereNotNull('end_date')
            ->where('owner_id', $ownerId)
            ->when($request->filled('from_date'), fn ($q) => $q->whereDate('start_date', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn ($q) => $q->whereDate('end_date', '<=', $request->to_date))
            ->when($request->filled('boat_id'), fn ($q) => $q->where('boat_id', $request->boat_id))
            ->when($request->filled('fish_id'), fn ($q) => $q->whereHas('catches.details', fn ($d) => $d->where('fish_id', $request->fish_id)));

        if ($request->filled('has_catch')) {
            if ($request->has_catch == '1') {
                $query->whereHas('catches');
            } elseif ($request->has_catch == '0') {
                $query->whereDoesntHave('catches');
            }
        }

        $trips = $query->orderBy('start_date', 'desc')->get();

        $totalWeight = $trips->sum(fn (Trip $trip) => $trip->catches?->total_weight ?? 0);
        $totalRevenue = $trips->sum(fn (Trip $trip) => $trip->catches?->details->sum('total_price') ?? 0);
        $tripsWithCatch = $trips->filter(fn (Trip $trip) => $trip->catches)->count();

        $statistics = [
            'total_trips' => $trips->count(),
            'trips_with_catch' => $tripsWithCatch,
            'total_weight' => $totalWeight,
            'total_revenue' => $totalRevenue,
            'avg_price_per_kg' => $totalWeight > 0 ? $totalRevenue / $totalWeight : 0,
        ];

        $companyName = currentCompany()?->name ?: 'حسبة';
        $settings = ownerCompanySettings([
            'qr_code' => app(\App\Service\Owner\ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);

        $filters = [
            'from_date' => $request->filled('from_date') ? $request->from_date : null,
            'to_date' => $request->filled('to_date') ? $request->to_date : null,
            'boat_id' => $request->filled('boat_id') ? $request->boat_id : null,
            'fish_id' => $request->filled('fish_id') ? $request->fish_id : null,
        ];

        $filename = 'catches-report-'.($filters['from_date'] ?? 'all').'-to-'.($filters['to_date'] ?? 'all').'.pdf';
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return pdf_report(view('owner.reports.print.catches-report', compact(
            'trips',
            'statistics',
            'settings',
            'filters'
        )), [], $filename, $disposition);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $catch = $this->ownerCatchQuery()->findOrFail($id);

        return $this->purgeTripCatch($catch->trip_id);
    }

    public function destroyByTrip($trip): \Illuminate\Http\JsonResponse
    {
        $trip = Trip::where('owner_id', auth()->id())->findOrFail($trip);

        return $this->purgeTripCatch($trip->id);
    }

    private function purgeTripCatch($tripId): \Illuminate\Http\JsonResponse
    {
        $trip = Trip::find($tripId);

        $catchIds = CatchModel::where('trip_id', $tripId)->pluck('id');
        $saleIds = Sale::where('trip_id', $tripId)->pluck('id');

        if ($saleIds->isNotEmpty()) {
            if (Schema::hasTable('payments')) {
                DB::table('payments')->whereIn('sale_id', $saleIds)->delete();
            }
            SaleDetail::whereIn('sale_id', $saleIds)->delete();
            Sale::whereIn('id', $saleIds)->delete();
        }

        if ($catchIds->isNotEmpty()) {
            CatchDetail::whereIn('catch_id', $catchIds)->delete();
        }

        FishQuantityStock::where('trip_id', $tripId)->delete();
        CatchModel::where('trip_id', $tripId)->delete();

        if ($trip && in_array($trip->status, [TripStatus::ReadyToSell, TripStatus::Counted], true)) {
            $trip->update(['status' => TripStatus::Finished]);
        }

        return response()->json(['message' => 'تم حذف المصيد والفواتير المرتبطة به بنجاح']);
    }
}
