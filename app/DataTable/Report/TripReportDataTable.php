<?php

namespace App\DataTable\Report;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class TripReportDataTable extends DataTables
{
    public function getData(Request $request)
    {
        Cache::forget('sidebar_trip_counts');

        if ($request->ajax()) {
            $query = Trip::orderBy('created_at', 'desc');

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
                ->addColumn('item_count', function (Trip $trip) {
                    return $trip->fishStocks->count() > 0 ? $trip->fishStocks->count() : '--';
                })

                // return numeric weight (kg) so client formats display and sorting remains numeric
                ->addColumn('item_weight', function (Trip $trip) {
                    $weight = $trip->fishStocks->sum('weight');

                    return (float) $weight;
                })

                ->addColumn('date', function (Trip $trip) {
                    if ($trip->start_date && $trip->end_date) {
                        try {
                            $start = formatHijriDate($trip->start_date, 'dd/MM/yyyy');
                            $end = formatHijriDate($trip->end_date, 'dd/MM/yyyy');

                            return $start.' - '.$end;
                        } catch (\Throwable $e) {
                            $start = Carbon::parse($trip->start_date)->format('d/m/Y');
                            $end = Carbon::parse($trip->end_date)->format('d/m/Y');

                            return $start.' - '.$end;
                        }
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
                    'total_fish_count' => $data->sum(fn ($trip) => $trip->fishStocks->count()),
                    // provide numeric totalWeight (kg) so client formats to ton/kg
                    'totalWeight' => $data->sum(fn ($trip) => $trip->fishStocks->sum('weight')),
                ])

                ->rawColumns(['action', 'status', 'name', 'port', 'owner', 'counter', 'captain', 'date', 'time', 'number']) // تأكد أن status أيضًا يحتوي على HTML مثل badges
                ->make(true);
        }
    }
}
