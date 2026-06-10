<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\CatchDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CatchRequest;
use App\Models\Boat;
use App\Models\CatchDetail;
use App\Models\CatchModel;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\Trip;
use App\Traits\CatchStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $boats = Boat::Active()->get();

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
        $catch = CatchModel::with(['trip', 'trip.boat', 'details.fish'])
            ->findOrFail($id);

        return view('owner.catch.show', compact('catch'));
    }

    public function create(Request $request)
    {
        $trips = Trip::where('owner_id', auth()->id())->orderByDesc('id')->get();
        $fish = Fish::Active()->get();
        $tripId = $request->query('trip_id');

        $selectedTrip = null;
        if ($tripId) {
            $selectedTrip = Trip::with('boat')->find($tripId);
        }

        return view('owner.catch.create', compact('trips', 'fish', 'selectedTrip'));
    }

    public function edit($id)
    {
        $catch = CatchModel::with(['trip.boat', 'details.fish'])->findOrFail($id);
        $trips = Trip::where('owner_id', auth()->id())->orderByDesc('id')->get();
        $fish = Fish::Active()->get();
        $selectedTrip = $catch->trip;

        return view('owner.catch.edit', compact('catch', 'trips', 'fish', 'selectedTrip'));
    }

    public function update(CatchRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $catch = CatchModel::findOrFail($id);

            CatchDetail::where('catch_id', $catch->id)->delete();
            FishQuantityStock::where('catch_id', $catch->id)->delete();

            $catch->update([
                'trip_id' => $request->trip_id,
            ]);

            $totalWeight = 0;

            foreach ($request->fish_id as $index => $fishId) {

                $weight = $request->weight[$index];

                CatchDetail::create([
                    'catch_id' => $catch->id,
                    'fish_id' => $fishId,
                    'fish_name' => optional(Fish::find($fishId))->scientific_name,
                    'weight' => $weight,
                ]);

                $stock = FishQuantityStock::firstOrCreate(
                    [
                        'fish_id' => $fishId,
                        'catch_id' => $catch->id,
                        'trip_id' => $request->trip_id,
                        'boat_id' => $request->boat_id,
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

    public function store(CatchRequest $request)
    {
        try {
            DB::beginTransaction();

            $catch = CatchModel::create([
                'trip_id' => $request->trip_id,
                'owner_id' => auth()->user()->getAuthIdentifier(),
                'catch_date' => now()->format('Y-m-d H:i:s'),
                'total_weight' => 0,
                'total_amount' => 0,
            ]);

            $totalWeight = 0;

            foreach ($request->fish_id as $index => $fishId) {

                $weight = $request->weight[$index];

                CatchDetail::create([
                    'catch_id' => $catch->id,
                    'fish_id' => $fishId,
                    'fish_name' => optional(Fish::find($fishId))->scientific_name,
                    'weight' => $weight,
                ]);

                $stock = FishQuantityStock::firstOrCreate(
                    [
                        'fish_id' => $fishId,
                        'catch_id' => $catch->id,
                        'trip_id' => $request->trip_id,
                        'boat_id' => $request->boat_id,
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
                ->with('success', 'تم إضافة المصيد بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function printCatchReport(Request $request, $id = null)
    {
        $catch = CatchModel::with(['trip', 'trip.boat', 'details.fish'])
            ->findOrFail($id);

        return view('owner.reports.print.catch-report', compact('catch'));
    }
}
