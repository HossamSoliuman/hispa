<?php

namespace App\Http\Controllers\Admin;

use App\DataTable\CaptainDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CaptainRequest;
use App\Models\Boat;
use App\Models\Region;
use App\Models\Trip;
use App\Models\User;
use App\Repository\CaptainRepository;
use Illuminate\Http\Request;

class CaptainController extends Controller
{
    public function __construct(
        private CaptainDataTable $datatable,
        private CaptainRepository $repository
    ) {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of captains.
     */
    public function index(Request $request)
    {
        $request->merge(['guard' => 'admin']);

        return $this->repository->getList($request);
    }

    /**
     * Show the form for creating a new captain.
     */
    public function create()
    {
        $regions = Region::Active()->select('id', 'name')->orderByDesc('id')->get();
        $boats = Boat::Active()->select('id', 'name_ar', 'name_en')->get();
        $owners = User::Active()->OwnerRole()->select('id', 'name')->get();

        return view('admin.captain.create', compact('regions', 'boats', 'owners'));
    }

    /**
     * Store a newly created captain.
     */
    public function store(CaptainRequest $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        $request->merge(['guard' => 'admin']);

        return $this->repository->saveData($request);
    }

    /**
     * Display the specified captain.
     */
    public function show(string $id)
    {
        $captain = User::CaptainRole()
            ->with([
                'owner',
                'boat.owner',
                'boat.crews',
                'region',
                'governorate',
                'port',
            ])
            ->findOrFail($id);

        $trips = Trip::where('captain_id', $id)
            ->with(['boat', 'region', 'governorate', 'port'])
            ->orderByDesc('start_date')
            ->limit(50)
            ->get();

        $stats = (object) [
            'total_trips' => Trip::where('captain_id', $id)->count(),
            'corrected_items' => $captain->boat
                ? $captain->boat->stocks()->count()
                : 0,
        ];

        return view('admin.captain.show', [
            'captain' => $captain,
            'stats' => $stats,
            'trips' => $trips,
        ]);
    }

    /**
     * Show the form for editing the specified captain.
     */
    public function edit(string $id)
    {
        $data = User::findOrFail($id);
        $regions = Region::Active()->select('id', 'name')->orderByDesc('id')->get();
        $boats = Boat::Active()->select('id', 'name_ar', 'name_en')->get();
        $owners = User::Active()->OwnerRole()->select('id', 'name')->get();

        return view('admin.captain.edit', compact('regions', 'boats', 'owners', 'data'));
    }

    /**
     * Update the specified captain.
     */
    public function update(CaptainRequest $request, string $id)
    {
        $request->merge(['guard' => 'admin']);

        return $this->repository->updateData($request, $id);
    }

    /**
     * Remove the specified captain.
     */
    public function destroy(string $id)
    {
        return $this->repository->deleteData($id);
    }

    /**
     * DataTables AJAX data for captain listing.
     */
    public function getCaptainData(Request $request)
    {
        return $this->datatable->getData($request);
    }
}
