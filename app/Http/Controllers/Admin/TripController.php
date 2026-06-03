<?php

namespace App\Http\Controllers\Admin;

use App\DataTable\TripDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TripRequest;
use App\Models\Boat;
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
        $this->middleware('auth:admin');
        $this->datatable = new TripDataTable;
        $this->rep = new TripRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->rep->getList($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regions = Region::Active()->get();
        $owners = User::Active()->OwnerRole()->select('id', 'name')->get();
        $captains = User::Active()->CaptainRole()->select('id', 'name')->get();

        return view('admin.trips.create', compact('regions', 'owners', 'captains'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TripRequest $request)
    {
        $request['guard'] = 'admin';
        return $this->rep->saveData($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->rep->getDetail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $regions = Region::Active()->orderByDesc('id')->get();
        $owners = User::Active()->OwnerRole()->select('id', 'name')->get();
        $data = Trip::findOrFail($id);
        $captains = User::Active()->CaptainRole()
            ->where('owner_id', $data->owner_id)
            ->select('id', 'name')
            ->get();

        return view('admin.trips.edit', compact('regions', 'owners', 'data', 'captains'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TripRequest $request, $id)
    {
        $request['guard'] = 'admin';
        return $this->rep->updateData($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->rep->deleteData($id);
    }

    public function getTripData(Request $request)
    {
        return $this->datatable->getData($request);
    }
}
