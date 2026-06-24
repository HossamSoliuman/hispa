<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Models\CatchDetail;
use App\Models\Fish;
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

        $catches = CatchDetail::query()
            ->with(['fish', 'unit'])
            ->whereHas('catch', function ($q) use ($ownerId, $from, $to, $boatId, $tripId) {
                $q->where('owner_id', $ownerId)
                    ->whereBetween(DB::raw('DATE(catch_date)'), [$from, $to]);

                if ($tripId) {
                    $q->where('trip_id', $tripId);
                }

                if ($boatId) {
                    $q->whereHas('trip', function ($trip) use ($boatId) {
                        $trip->where('boat_id', $boatId);
                    });
                }
            });

        if ($fishId) {
            $catches->where('fish_id', $fishId);
        }

        $stocks = $catches->get();

        return view('owner.reports.fish_quntity', compact('stocks', 'from', 'to', 'boatId', 'boats', 'fishId', 'fishs', 'tripId', 'trips'));
    }
}
