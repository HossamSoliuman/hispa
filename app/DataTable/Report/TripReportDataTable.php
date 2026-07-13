<?php

namespace App\DataTable\Report;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class TripReportDataTable extends DataTables
{
    /**
     * Sum weight per unit and render it as a comma-separated breakdown
     * (e.g. "100.00 كجم، 20.00 صندوق"), since weights are never converted between units.
     */
    private function weightBreakdown(Collection $details): string
    {
        $breakdown = $details
            ->groupBy(fn ($detail) => $detail->unit->name ?: __('admin.units.kg'))
            ->map(fn (Collection $group, $unitName) => number_format($group->sum('weight'), 2).' '.$unitName)
            ->implode('، ');

        return $breakdown ?: number_format(0, 2).' '.__('admin.units.kg');
    }

    public function getData(Request $request)
    {
        Cache::forget('sidebar_trip_counts');

        if ($request->ajax()) {
            $query = Trip::with(['owner', 'captain', 'counter', 'port', 'catches.details.unit'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereDate('start_date', '>=', $request->start_date)
                    ->whereDate('start_date', '<=', $request->end_date);
            }
            if ($request->has('status') && in_array($request->status, range(1, 8))) {
                $query->where('status', $request->status);
            }
            if ($request->filled('boat_id')) {
                $query->where('boat_id', $request->boat_id);
            }

            $data = $query->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('number', function (Trip $trip) {
                    $number = $trip->number ?? '--';
                    $url = route('admin.trips.show', $trip->id); // تأكد أن هذا route موجود

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
                ->addColumn('item_weight', function (Trip $trip) {
                    return $this->weightBreakdown($trip->catches?->details ?? collect());
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

                        return "({$diff} ".__('admin.day').')';
                    }

                    return '--';
                })

                ->addColumn('time', function (Trip $trip) {
                    if ($trip->departure_time && $trip->return_time) {
                        $from = Carbon::parse($trip->departure_time)->format('h:i A');
                        $to = Carbon::parse($trip->return_time)->format('h:i A');

                        // Optional: تحويل AM/PM إلى صباحًا/مساءً
                        $from = str_replace(['AM', 'PM'], [__('admin.morning'), __('admin.evening')], $from);
                        $to = str_replace(['AM', 'PM'], [__('admin.morning'), __('admin.evening')], $to);

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
                    'totalWeight' => $this->weightBreakdown(
                        $data->flatMap(fn (Trip $trip) => $trip->catches?->details ?? collect())
                    ),
                ])

                ->rawColumns(['action', 'status', 'name', 'port', 'owner', 'counter', 'captain', 'date', 'time', 'number']) // تأكد أن status أيضًا يحتوي على HTML مثل badges
                ->make(true);
        }
    }
}
