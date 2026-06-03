<?php

namespace App\Http\Controllers\Admin;

use App\DataTable\OwnerStockDataTable;
use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Models\Fish;
use App\Models\Trip;
use App\Models\User;
use App\Services\Admin\OwnerStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerStockController extends Controller
{
    public function __construct(
        private readonly OwnerStockDataTable $ownerStockDataTable,
        private readonly OwnerStockService $ownerStockService
    ) {
        $this->middleware('auth:admin');
    }

    /**
     * Display owner-stock index: list all owners with their fish-quantity stock summary.
     */
    public function index(Request $request): View
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());
        $owners = $this->ownerStockService->getOwnersWithStock();

        return view('admin.owner-stock.index', [
            'from' => $from,
            'to' => $to,
            'owners' => $owners,
        ]);
    }

    /**
     * Display fish-quantity stock detail for one owner (same format as owner/fish-quntity).
     */
    public function show(Request $request, string $id): View
    {
        $owner = User::query()->ownerRole()->findOrFail($id);
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $boats = Boat::query()
            ->where('owner_id', $owner->id)
            ->orderBy(app()->getLocale() === 'en' ? 'name_en' : 'name_ar')
            ->get();
        $trips = Trip::query()->where('owner_id', $owner->id)->orderBy('start_date', 'desc')->get();
        $fishs = Fish::query()->active()->orderBy('scientific_name')->get();

        $boatId = $request->input('boat_id');
        $tripId = $request->input('trip_id');
        $fishId = $request->input('fish_id');

        return view('admin.owner-stock.show', [
            'owner' => $owner,
            'from' => $from,
            'to' => $to,
            'boats' => $boats,
            'trips' => $trips,
            'fishs' => $fishs,
            'boatId' => $boatId,
            'tripId' => $tripId,
            'fishId' => $fishId,
        ]);
    }

    /**
     * DataTable AJAX: owners list with fish-quantity stock summary.
     */
    public function getOwnerStockData(Request $request): JsonResponse
    {
        return $this->ownerStockDataTable->getData($request);
    }

    /**
     * DataTable AJAX: fish-quantity stock rows for one owner (same structure as owner/fish-quntity).
     */
    public function getOwnerStockDetailData(Request $request, string $id): JsonResponse
    {
        return $this->ownerStockDataTable->getShowData($request, (int) $id);
    }
}
