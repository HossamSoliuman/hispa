<?php

namespace App\DataTable;

use App\Enums\TripStatus;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class TripDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = Trip::with(['fishQuantityStocks', 'owner'])->orderBy('created_at', 'desc');

            if ($request->filled('status') && in_array($request->status, range(1, 8))) {
                $query->where('status', $request->status);
            }
            $searchTerm = is_array($request->search ?? null) ? ($request->search['value'] ?? '') : (string) ($request->input('search') ?? '');
            if ($searchTerm !== '') {
                $term = $searchTerm;
                $query->where(function ($q) use ($term) {
                    $q->where('number', 'like', "%{$term}%")
                        ->orWhereHas('owner', fn ($o) => $o->where('name', 'like', "%{$term}%"));
                });
            }
            if ($request->filled('from_date')) {
                $query->whereDate('end_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('start_date', '<=', $request->to_date);
            }

            Cache::forget('sidebar_trip_counts');

            $data = $query->get();

            // إحصائيات الرحلات
            $totalTrips = Trip::count();
            $completedTrips = Trip::whereIn('status', [7, 8])->count(); // مكتملة
            $activeTrips = Trip::whereIn('status', [2, 3, 4, 5, 6])->count(); // نشطة/قيد التنفيذ
            $cancelledTrips = Trip::where('status', 3)->count(); // ملغاة
            $newTrips = Trip::where('status', 1)->count(); // جديدة

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('number', function (Trip $trip) {
                    $number = $trip->number ?? '--';
                    $url = '#';
                    if (auth('admin')->check() && auth('admin')->user()->can('read_trips')) {
                        $url = route('admin.trips.show', $trip->id);
                    } elseif (auth()->check() && auth()->user()->can('read_trips')) {
                        $url = route('owner.trips.show', $trip->id);
                    }

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
                    $count = $trip->fishQuantityStocks ? $trip->fishQuantityStocks->count() : 0;

                    return $count > 0 ? $count : '--';
                })

                ->addColumn('item_weight', function (Trip $trip) {
                    $weight = $trip->fishQuantityStocks ? $trip->fishQuantityStocks->sum('weight') : 0;

                    return $weight > 0 ? $weight : '--';
                })

                ->addColumn('date', function (Trip $trip) {
                    if ($trip->start_date && $trip->end_date) {
                        // use Hijri formatting helper
                        $start = formatHijriDate($trip->start_date);
                        $end = formatHijriDate($trip->end_date);

                        return $start.' - '.$end;
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

                ->addColumn('action', function (Trip $trip) {
                    $btn = '';

                    if ($trip->status === TripStatus::New) {
                        if (auth()->user()->can('update_trips')) {

                            // زر التعديل
                            $btn .= '<a href="'.route('admin.trips.edit', $trip->id).'"
            class="edit btn btn-primary btn-sm editBtn" title="تعديل">
            <i class="fas fa-edit"></i>
        </a> ';
                        }
                        if (auth()->user()->can('delete_trips')) {

                            // زر الحذف
                            $btn .= '<a href="#" onclick="deleteRecord('.$trip->id.')"
            class="btn btn-danger btn-sm" title="حذف">
            <i class="fas fa-trash"></i>
        </a>';
                        }
                    } else {
                        $btn = __('admin.actions.not_allowed_edit');
                    }

                    return $btn;
                })

                ->with([
                    'trip_count' => $totalTrips,
                    'trip_completed' => $completedTrips,
                    'trip_active' => $activeTrips,
                    'trip_cancelled' => $cancelledTrips,
                    'trip_new' => $newTrips,
                ])
                ->rawColumns(['action', 'status', 'name', 'port', 'owner', 'counter', 'captain', 'date', 'time', 'number']) // تأكد أن status أيضًا يحتوي على HTML مثل badges
                ->make(true);
        }
    }
}
