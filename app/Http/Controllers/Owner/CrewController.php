<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\CrewDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CrewRequest;
use App\Models\Boat;
use App\Models\Region;
use App\Models\Trip;
use App\Models\User;
use App\Repository\CrewRepository;
use Illuminate\Http\Request;

class CrewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $datatable;

    private $rep;

    public function __construct()
    {
        $this->datatable = new CrewDataTable;
        $this->rep = new CrewRepository;
        //
        //        $this->middleware('permission:read_settings', ['only' => ['index', 'show']]);
        //        $this->middleware('permission:create_settings', ['only' => ['create', 'store']]);
        //        $this->middleware('permission:update_settings', ['only' => ['edit', 'update']]);
        //        $this->middleware('permission:delete_settings', ['only' => ['destroy']]);
    }

    public function index()
    {

        $request['guard'] = 'owner';

        return $this->rep->getList($request);
    }

    public function getCrewData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    public function showCrewData(Request $request, $id)
    {
        return $this->datatable->showData($request, $id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $regions = Region::Active()->select('id', 'name')->get();
        $boats = Boat::Active()->select('id', 'name_ar', 'name_en')->get();

        return view('owner.crew.create', compact('regions', 'boats'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CrewRequest $request)
    {

        $request['guard'] = 'owner';

        return $this->rep->saveData($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if (! $user) {
            return redirect()->back()->with(['error' => 'الصفحة غير موجودة']);
        }
        $tripCount = Trip::where('captain_id', $id)->count();

        $stats = (object) [
            'total_trips' => $tripCount,
        ];

        return view('owner.crew.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $owner_id = auth()->user()->id;
        $regions = Region::Active()->select('id', 'name')->get();
        $captains = User::Active()->CaptainRole()->where('owner_id', $owner_id)->select('id', 'name')->get();
        $boats = Boat::Active()->select('id', 'name_ar', 'name_en')->get();
        $data = User::CrewRole()->find($id);
        if (! $data) {
            return redirect()->back()->with(['error' => 'حدث خطأ ما']);
        }

        return view('owner.crew.edit', compact('regions', 'captains', 'data', 'boats'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CrewRequest $request, $id)
    {
        $request['guard'] = 'owner';

        return $this->rep->updateData($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->rep->deleteData($id);
    }
}
