<?php

namespace App\DataTable\Owner;

use App\Enums\TripStatus;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class TripDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $owner_id = auth()->id();

            $boat_id = $request->boat_id;
            if ($boat_id) {
                $query = Trip::with(['sales', 'owner', 'boat', 'captain', 'catches'])
                    ->where('owner_id', $owner_id)
                    ->where('boat_id', $boat_id)
                    ->orderBy('created_at', 'desc');
            } else {
                $query = Trip::with(['sales', 'owner', 'boat', 'captain', 'catches'])
                    ->where('owner_id', $owner_id)
                    ->orderBy('created_at', 'desc');
            }

            if ($request->has('status') && in_array($request->status, range(1, 8))) {
                $query->where('status', $request->status);
            } else {
                $query->where('status', '!=', TripStatus::Cancelled->value);
            }

            $data = $query->get();

            $trip_count = $data->count();
            $trip_waiting_status = $data->where('status', TripStatus::New)->count();
            $trip_completed_status = $data->where('status', TripStatus::Sold)->count();
            $trip_has_catches = Trip::whereHas('catches')->where('status', '!=', TripStatus::Cancelled->value)->where('owner_id', $owner_id)->count();
            $sales_amount = $data->sum(fn (Trip $trip) => $trip->sales->sum('net_owner_amount'));

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('number', function (Trip $trip) {
                    $number = $trip->number ?? '--';
                    $url = route('owner.trips.show', $trip->id);

                    return "<a href='{$url}' class='text-primary fw-bold'>{$number}</a>";
                })
                ->addColumn('owner', fn (Trip $trip) => $trip->owner->name ?? '--')
                ->addColumn('boat', fn (Trip $trip) => $trip->boat->name ?? '--')
                ->addColumn('captain', fn (Trip $trip) => $trip->captain->name ?? '--')
                ->addColumn('total_sales', function (Trip $trip) {
                    $weight = $trip->sales->sum('net_owner_amount');

                    return $weight > 0 ? $weight : '--';
                })
                ->addColumn('start_date', fn (Trip $trip) => $trip->start_date ? Carbon::parse($trip->start_date)->format('H:i:s Y-m-d') : '--')
                ->addColumn('end_date', function (Trip $trip) {
                    $date = $trip->actual_end_datetime ?? $trip->end_date;

                    return $date ? Carbon::parse($date)->format('H:i:s Y-m-d') : null;
                })
                ->addColumn('status', function (Trip $trip) {
                    $label = e($trip->status->label());
                    $color = $trip->status->color();

                    return '<span class="badge bg-'.$color.' px-2 py-1 rounded">'.$label.'</span>';
                })
                ->addColumn('actions', fn (Trip $trip) => view('owner.trips._actions', ['trip' => $trip])->render())
                ->with([
                    'trip_count' => $trip_count,
                    'trip_waiting_status' => $trip_waiting_status,
                    'trip_completed_status' => $trip_completed_status,
                    'trip_has_catches' => $trip_has_catches,
                    'sales_amount' => $sales_amount,
                ])
                ->rawColumns(['status', 'number', 'actions'])
                ->make(true);
        }
    }
}
