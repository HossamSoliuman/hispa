<?php

namespace App\Http\Controllers\Admin;

use App\DataTable\BoatDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BoatRequest;
use App\Models\Boat;
use App\Models\BoatType;
use App\Models\Region;
use App\Models\User;
use App\Repository\BoatRepository;
use Illuminate\Http\Request;

class BoatController extends Controller
{
    private $datatable;
    private $rep;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->datatable = new BoatDataTable;
        $this->rep = new BoatRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request['guard'] = 'admin';
        return $this->rep->getList($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regions = Region::Active()->orderByDesc('id')->get();
        $boat_types = BoatType::Active()->get();
        $owners = User::Active()->OwnerRole()->select('id', 'name')->get();

        return view('admin.boats.create', compact('regions', 'boat_types', 'owners'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BoatRequest $request)
    {
        $request['guard'] = 'admin';
        return $this->rep->saveData($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = $this->rep->getDetail($id);
        return view('admin.boats.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $regions = Region::Active()->orderByDesc('id')->get();
        $data = Boat::findOrFail($id);
        $boat_types = BoatType::Active()->get();
        $owners = User::Active()->OwnerRole()->select('id', 'name')->get();

        return view('admin.boats.edit', compact('regions', 'data', 'boat_types', 'owners'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BoatRequest $request, $id)
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

    public function getBoatData(Request $request)
    {
        return $this->datatable->getData($request);
    }
}
