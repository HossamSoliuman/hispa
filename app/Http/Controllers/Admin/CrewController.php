<?php

namespace App\Http\Controllers\Admin;

use App\DataTable\CrewDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CrewRequest;
use App\Models\Boat;
use App\Models\Region;
use App\Models\Trip;
use App\Models\User;
use App\Repository\CrewRepository;
use Illuminate\Http\Request;

class CrewController extends Controller
{
    public function __construct(
        private CrewDataTable $datatable,
        private CrewRepository $repository
    ) {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of crew members.
     */
    public function index(Request $request)
    {
        $request->merge(['guard' => 'admin']);

        return $this->repository->getList($request);
    }

    /**
     * Show the form for creating a new crew member.
     */
    public function create()
    {
        $regions = Region::Active()->select('id', 'name')->orderByDesc('id')->get();
        $boats = Boat::Active()->select('id', 'name_ar', 'name_en')->get();
        $owners = User::Active()->OwnerRole()->select('id', 'name')->get();
        $captains = User::Active()->CaptainRole()->select('id', 'name')->get();

        return view('admin.crew.create', compact('regions', 'boats', 'owners', 'captains'));
    }

    /**
     * Store a newly created crew member.
     */
    public function store(CrewRequest $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        $request->merge(['guard' => 'admin']);

        return $this->repository->saveData($request);
    }

    /**
     * Display the specified crew member.
     */
    public function show(string $id)
    {
        $crew = User::CrewRole()
            ->with([
                'owner',
                'boat.owner',
                'boat.captain',
                'boat.crews',
                'region',
                'governorate',
                'port',
            ])
            ->findOrFail($id);

        $trips = collect();
        if ($crew->boat_id) {
            $trips = Trip::where('boat_id', $crew->boat_id)
                ->with(['boat', 'captain', 'region', 'governorate', 'port'])
                ->orderByDesc('start_date')
                ->limit(50)
                ->get();
        }

        $stats = (object) [
            'total_trips' => $crew->boat_id
                ? Trip::where('boat_id', $crew->boat_id)->count()
                : 0,
        ];

        return view('admin.crew.show', [
            'crew' => $crew,
            'stats' => $stats,
            'trips' => $trips,
        ]);
    }

    /**
     * Show the form for editing the specified crew member.
     */
    public function edit(string $id)
    {
        $data = User::CrewRole()->findOrFail($id);
        $regions = Region::Active()->select('id', 'name')->orderByDesc('id')->get();
        $boats = Boat::Active()->select('id', 'name_ar', 'name_en')->get();
        $owners = User::Active()->OwnerRole()->select('id', 'name')->get();
        $captains = User::Active()->CaptainRole()->select('id', 'name')->get();

        return view('admin.crew.edit', compact('regions', 'boats', 'owners', 'captains', 'data'));
    }

    /**
     * Update the specified crew member.
     */
    public function update(CrewRequest $request, string $id)
    {
        $request->merge(['guard' => 'admin']);

        return $this->repository->updateData($request, $id);
    }

    /**
     * Remove the specified crew member.
     */
    public function destroy(string $id)
    {
        return $this->repository->deleteData($id);
    }

    /**
     * DataTables AJAX data for crew listing.
     */
    public function getCrewData(Request $request)
    {
        return $this->datatable->getData($request);
    }
}
