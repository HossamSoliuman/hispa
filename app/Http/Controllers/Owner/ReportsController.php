<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Boat;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function fishQuntity(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $ownerId = Auth::guard('owner')->id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        $boats = Boat::where('owner_id', $ownerId)->get();
        $trips = Trip::where('owner_id', $ownerId)->get();
        $fishs = Fish::active()->get();

        $boatId = $request->input('boat_id');
        $tripId = $request->input('trip_id');
        $fishId = $request->input('fish_id');

        $ownerBoatIds = $boats->pluck('id');
        $ownerTripIds = $trips->pluck('id');

        $stock = FishQuantityStock::query()
            ->with(['fish', 'unit'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->where(function ($q) use ($ownerBoatIds, $ownerTripIds) {
                $q->whereIn('boat_id', $ownerBoatIds)
                    ->orWhereIn('trip_id', $ownerTripIds);
            });

        if ($boatId) {
            $stock->where('boat_id', $boatId);
        }

        if ($tripId) {
            $stock->where('trip_id', $tripId);
        }

        if ($fishId) {
            $stock->where('fish_id', $fishId);
        }

        $stocks = $stock->get();

        return view('owner.reports.fish_quntity', compact('stocks', 'from', 'to', 'boatId', 'boats', 'fishId', 'fishs', 'tripId', 'trips'));
    }

    public function assetDepreciation(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $ownerId = Auth::guard('owner')->id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        $asset_type = $request->input('asset_type');
        $depreciation_method = $request->input('depreciation_method');
        $status = $request->input('status');

        $assets = Asset::with('latestDepreciation')
            ->whereIn('boat_id', Boat::where('owner_id', $ownerId)->pluck('id'))
            ->whereHas('latestDepreciation', function ($q) use ($from, $to) {
                $q->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
            });

        if ($asset_type) {
            $assets->where('asset_type', $asset_type);
        }

        if ($depreciation_method) {
            $assets->where('depreciation_method', $depreciation_method);
        }

        if ($status) {
            $assets->where('status', $status);
        }

        $assets = $assets->get();

        return view('owner.reports.asset_depreciation', compact('assets', 'from', 'to', 'asset_type', 'depreciation_method', 'status'));
    }
}
