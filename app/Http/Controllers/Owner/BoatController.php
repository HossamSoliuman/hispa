<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\BoatDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\BoatRequest;
use App\Models\Boat;
use App\Models\BoatType;
use App\Models\Region;
use App\Models\Trip;
use App\Models\User;
use App\Repository\BoatRepository;
use Illuminate\Http\Request;

class BoatController extends Controller
{
    private $datatable;

    private $rep;

    public function __construct()
    {
        $this->datatable = new BoatDatatable;
        $this->rep = new BoatRepository;
    }

    public function index(Request $request)
    {
        $request['guard'] = 'owner';

        return $this->rep->getList($request);
    }

    public function getBoatData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    public function create()
    {
        $regions = Region::Active()->orderByDesc('id')->get();
        $boat_types = BoatType::Active()->get();

        return view('owner.boats.create', compact('regions', 'boat_types'));
    }

    public function store(BoatRequest $request)
    {
        $request['guard'] = 'owner';

        return $this->rep->saveData($request);
    }

    public function show($id)
    {
        $data = $this->rep->getDetail($id);

        return view('owner.boats.show', $data);
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

    public function crew($id)
    {
        $boat = Boat::with('captain', 'crews')->findOrFail($id);

        return view('owner.boats.crew', compact('boat'));
    }

    public function getBoatInfo($captain_id)
    {

        $captain = User::CaptainRole()
            ->where('id', $captain_id)
            ->with('boat')
            ->first();

        if (! $captain || ! $captain->boat) {
            return response()->json(null, 404);
        }

        return response()->json([
            'boat_id' => $captain->boat?->id,
            'boat_name' => $captain->boat?->name, // or name_en if needed
        ]);
    }

    public function getBoatInfoByTrip($trip_id)
    {

        $trip = Trip::find($trip_id);

        if (! $trip) {
            return response()->json(null, 404);
        }

        return response()->json([
            'boat_id' => $trip->boat_id,
            'boat_name' => $trip->boat_name,
        ]);
    }

    public function getAssets() {}
}
