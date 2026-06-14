<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\TripDataTable;
use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\TripRequest;
use App\Http\Requests\Owner\TripTransitionRequest;
use App\Models\Region;
use App\Models\Trip;
use App\Models\User;
use App\Repository\Admin\TripRepository;
use App\Services\TripTransitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripController extends Controller
{
    private $datatable;

    private $rep;

    public function __construct()
    {
        $this->datatable = new TripDataTable;
        $this->rep = new TripRepository;
    }

    public function index()
    {
        $captains = User::Active()->CaptainRole()
            ->where('owner_id', auth()->id())
            ->select('id', 'name')
            ->get();

        return view('owner.trips.index', compact('captains'));
    }

    public function getTripData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    public function show($id)
    {
        $regions = Region::Active()->select('id', 'name')->get();
        $owners = User::Active()->OwnerRole()->select('id', 'name')->get();
        $data = Trip::with([
            'fishQuantityStocks.fish',
            'catches.details.fish',
            'catches.details.unit',
            'sales.details',
            'boat',
            'captain',
            'owner',
            'port',
            'region',
            'governorate',
        ])->find($id);

        if (! $data) {
            return redirect()->back()->with(['error' => 'حدث خطأ ما']);
        }

        $captains = User::Active()->CaptainRole()
            ->where('owner_id', $data->owner_id)
            ->select('id', 'name')
            ->get();

        $financials = $this->calculateTripFinancials($data);

        return view('owner.trips.show', compact('regions', 'owners', 'data', 'captains', 'financials'));
    }

    private function calculateTripFinancials(Trip $trip): array
    {
        $catchWeight = (float) ($trip->catches?->total_weight ?? 0);
        if ($catchWeight <= 0 && $trip->catches) {
            $catchWeight = (float) $trip->catches->details->sum('weight');
        }

        $grossRevenue = (float) $trip->sales->sum('total_price');
        $commission = (float) $trip->sales->sum('commission_amount');
        $labor = (float) $trip->sales->sum('labor_amount');
        $netProfit = (float) $trip->sales->sum(function ($sale) {
            return $sale->net_owner_amount ?? ($sale->total_price - ($sale->commission_amount ?? 0) - ($sale->labor_amount ?? 0));
        });

        return [
            'catch_weight' => $catchWeight,
            'gross_revenue' => $grossRevenue,
            'commission' => $commission,
            'labor' => $labor,
            'total_costs' => $commission + $labor,
            'net_profit' => $netProfit,
            'outstanding' => (float) $trip->sales->sum('remaining_total'),
        ];
    }

    public function create()
    {
        $regions = Region::Active()->get();
        $captains = User::Active()->CaptainRole()
            ->where('owner_id', auth()->id())
            ->select('id', 'name')
            ->get();

        return view('owner.trips.create', compact('regions', 'captains'));
    }

    public function store(TripRequest $request)
    {
        $request['guard'] = 'owner';

        return $this->rep->saveData($request);
    }

    public function edit($id)
    {
        $trip = Trip::where('owner_id', auth()->id())->find($id);

        if (! $trip) {
            return redirect()->route('owner.trips.index')->with(['error' => __('owner.swal.error')]);
        }

        $captains = User::Active()->CaptainRole()
            ->where('owner_id', auth()->id())
            ->select('id', 'name')
            ->get();

        return view('owner.trips.edit', ['trip' => $trip, 'captains' => $captains]);
    }

    public function update(TripRequest $request, $id)
    {
        $trip = Trip::where('owner_id', auth()->id())->find($id);

        if (! $trip) {
            return redirect()->route('owner.trips.index')->with(['error' => __('owner.swal.error')]);
        }

        $data = [
            'name' => $request->name,
            'name_en' => $request->name_en,
            'license_number' => $request->license_number,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
            'updated_by' => auth()->user()->name ?? 'Owner',
        ];
        $data = fillBoatAndCrewData($data, $request->boat_id, $request->captain_id);

        $trip->update($data);

        return redirect()->route('owner.trips.index')->with('success', __('owner.swal.success'));
    }

    public function destroy($id)
    {
        return $this->rep->deleteData($id);
    }

    public function transition(TripTransitionRequest $request, Trip $trip, TripTransitionService $service): JsonResponse
    {
        if ($trip->owner_id !== auth()->id()) {
            return response()->json(['message' => __('owner.swal.error')], 403);
        }

        try {
            $service->transition($trip, TripStatus::from($request->to), $request->cancel_reason, $request->actual_end_date);

            return response()->json(['message' => __('owner.swal.success')]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
