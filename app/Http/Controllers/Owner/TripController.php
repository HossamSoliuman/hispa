<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\TripDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\BoatRequest;
use App\Http\Requests\Owner\TripRequest;
use App\Models\Boat;
use App\Models\BoatType;
use App\Models\Region;
use App\Models\Trip;
use App\Models\User;
use App\Repository\Admin\TripRepository;
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
        $data = Trip::with(['fishQuantityStocks.fish'])->find($id);

        if (! $data) {
            return redirect()->back()->with(['error' => 'حدث خطأ ما']);
        }

        $captains = User::Active()->CaptainRole()
            ->where('owner_id', $data->owner_id)
            ->select('id', 'name')
            ->get();

        return view('owner.trips.show', compact('regions', 'owners', 'data', 'captains'));
    }

    public function create()
    {
        $regions = Region::Active()->get();
        // $owners = User::Active()->OwnerRole()->select('id', 'name')->get();
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
        $regions = Region::Active()->orderByDesc('id')->get();
        $data = Boat::find($id);
        $boat_types = BoatType::Active()->get();

        return view('owner.boats.edit', compact('regions', 'data', 'boat_types'));
    }

    public function update(BoatRequest $request, $id)
    {
        $request['guard'] = 'owner';

        return $this->rep->updateData($request, $id);
    }

    public function destroy($id)
    {

        return $this->rep->deleteData($id);
    }

    public function endTrip($id)
    {
        $trip = Trip::find($id);
        if ($trip) {
            Trip::where('id', $id)->update([
                'end_date' => now()->format('Y-m-d H:i:s'),
                'status' => 8,
            ]);

            return response()->json(['message' => 'Data saved successfully'], 200);
        }

        return response()->json(['message' => 'Error Not Found'], 404);
    }

    public function cancelTrip($id)
    {
        $trip = Trip::find($id);
        if ($trip) {
            Trip::where('id', $id)->update([
                'status' => 3,
            ]);

            return response()->json(['message' => 'Data saved successfully'], 200);
        }

        return response()->json(['message' => 'Error Not Found'], 404);
    }
}
