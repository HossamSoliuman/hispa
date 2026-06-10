<?php

namespace App\DataTable\Owner\Report;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class TripReportDataTable extends DataTables
{
    public function getData(Request $request)
    {
        $owner_id = auth()->user()->id;
        if ($request->ajax()) {
            $query = Trip::with(['catches.details', 'sales'])->orderBy('created_at', 'desc');
            $query->where('owner_id', $owner_id);
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }
            if ($request->has('status') && in_array($request->status, range(1, 8))) {
                $query->where('status', $request->status);
            }

            $data = $query->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('number', function (Trip $trip) {
                    $number = $trip->number ?? '--';
                    $url = route('owner.trips.show', $trip->id);

                    return "<a href='{$url}' class='text-primary fw-bold'>{$number}</a>";
                })

                ->addColumn('owner', function (Trip $trip) {
                    return $trip->owner->name ?? '--';
                })
                ->addColumn('captain', function (Trip $trip) {
                    return $trip->captain->name ?? '--';
                })
                ->addColumn('counter', function (Trip $trip) {
                    return $trip->counter->name ?? '--';
                })

                ->addColumn('port', function (Trip $trip) {
                    return $trip->port->name ?? '--';
                })
                ->addColumn('item_count', function (Trip $trip) {
                    $count = $trip->catches?->details->count() ?? 0;

                    return $count > 0 ? $count : '--';
                })

                ->addColumn('item_weight', function (Trip $trip) {
                    $weight = (float) ($trip->catches?->details->sum('weight') ?? 0);

                    return $weight > 0 ? $weight : '--';
                })
                ->addColumn('sales_total', function (Trip $trip) {
                    $total = (float) $trip->sales->where('seller_type', 'owner')->sum('total_price');

                    return $total > 0 ? number_format($total, 2) : '--';
                })
                ->addColumn('net_owner_amount', function (Trip $trip) {
                    $net = (float) $trip->sales->where('seller_type', 'owner')->sum('net_owner_amount');

                    return $net > 0 ? number_format($net, 2) : '--';
                })

                ->addColumn('date', function (Trip $trip) {
                    if ($trip->start_date && $trip->end_date) {
                        $start = Carbon::parse($trip->start_date)->format('d/m/Y');
                        $end = Carbon::parse($trip->end_date)->format('d/m/Y');

                        return $start.' - '.$end;
                    }

                    return '--';
                })
                ->addColumn('date_count', function (Trip $trip) {
                    if ($trip->start_date && $trip->end_date) {
                        $start = \Carbon\Carbon::parse($trip->start_date);
                        $end = \Carbon\Carbon::parse($trip->end_date);
                        $diff = $start->diffInDays($end); // عدد الأيام بين التاريخين

                        return " ({$diff} يوم)";
                    }

                    return '--';
                })

                ->addColumn('time', function (Trip $trip) {
                    if ($trip->departure_time && $trip->return_time) {
                        $from = Carbon::parse($trip->departure_time)->format('h:i A');
                        $to = Carbon::parse($trip->return_time)->format('h:i A');

                        // Optional: تحويل AM/PM إلى صباحًا/مساءً
                        $from = str_replace(['AM', 'PM'], ['صباحًا', 'مساءً'], $from);
                        $to = str_replace(['AM', 'PM'], ['صباحًا', 'مساءً'], $to);

                        return "$from - $to";
                    }

                    return '--';
                })
                ->addColumn('status', function (Trip $trip) {
                    $label = e($trip->status->label());
                    $color = $trip->status->color();

                    return '<span class="badge bg-'.$color.' px-2 py-1 rounded">'.$label.'</span>';
                })

                ->with([
                    'trip_count' => $data->count(),
                    'total_fish_count' => $data->sum(fn ($trip) => $trip->catches?->details->count() ?? 0),
                    'totalWeight' => $data->sum(fn ($trip) => (float) ($trip->catches?->details->sum('weight') ?? 0)).' كجم',
                    'total_sales' => number_format($data->sum(fn ($trip) => (float) $trip->sales->where('seller_type', 'owner')->sum('total_price')), 2),
                ])

                ->rawColumns(['action', 'status', 'name', 'port', 'owner', 'counter', 'captain', 'date', 'time', 'number']) // تأكد أن status أيضًا يحتوي على HTML مثل badges
                ->make(true);

        }

    }
}
