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

            CatchDetail::where('catch_id', $catch->id)->delete();
            FishQuantityStock::where('catch_id', $catch->id)->delete();

            $catch->update([
                'trip_id' => $request->trip_id,
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
                    'fish_name' => optional(Fish::find($fishId))->scientific_name,
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

            DB::commit();

            return redirect()
                ->route('owner.catch.index')
                ->with('success', 'تم تعديل المصيد بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
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
                    'fish_name' => optional(Fish::find($fishId))->scientific_name,
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
        $catch = $this->ownerCatchQuery()->with('trip')->findOrFail($id);

        try {
            DB::beginTransaction();

            $trip = $catch->trip;

            $saleIds = Sale::withTrashed()->where('catch_id', $catch->id)->pluck('id');

            if ($saleIds->isNotEmpty()) {
                if (Schema::hasTable('payments')) {
                    DB::table('payments')->whereIn('sale_id', $saleIds)->delete();
                }
                SaleDetail::whereIn('sale_id', $saleIds)->delete();
                Sale::withTrashed()->whereIn('id', $saleIds)->forceDelete();
            }

            CatchDetail::where('catch_id', $catch->id)->delete();
            FishQuantityStock::where('catch_id', $catch->id)->delete();
            $catch->delete();

            if ($trip && in_array($trip->status, [TripStatus::ReadyToSell, TripStatus::Counted], true)) {
                $trip->update(['status' => TripStatus::Finished]);
            }

            DB::commit();

            return response()->json(['message' => 'تم حذف المصيد والفواتير المرتبطة به بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
