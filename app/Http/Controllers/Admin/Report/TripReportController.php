<?php

namespace App\Http\Controllers\Admin\Report;

use App\DataTable\Report\TripReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\Fish;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TripReportController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new TripReportDataTable;
        $this->middleware('permission:read_trip_report', ['only' => ['index', 'show']]);

    }

    public function index()
    {
        $fish = Fish::Active()->select('scientific_name as name', 'id')->get();

        return view('admin.report.tripe', compact('fish'));
    }

    public function getTripData(Request $request)
    {
        return $this->datatable->getData($request);

    }

    /**
     * Print trip report (openable in new tab)
     * Accepts optional filters: start_date, end_date, status, boat_id, trip_id
     */
    public function printTripReport(Request $request, $trip_id = null)
    {
        // Build query for trips according to filters
        $query = \App\Models\Trip::with(['boat', 'captain', 'fishStocks.fish']);

        if ($trip_id) {
            $query->where('id', $trip_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', Carbon::parse($request->start_date));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('start_date', '<=', Carbon::parse($request->end_date));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('boat_id')) {
            $query->where('boat_id', $request->boat_id);
        }

        $trips = $query->orderBy('start_date', 'desc')->get();

        // Prepare filters for diagnostics and view
        $filters = [
            'trip_id' => $trip_id,
            'from_date' => $request->filled('start_date') ? $request->start_date : null,
            'to_date' => $request->filled('end_date') ? $request->end_date : null,
            'status' => $request->filled('status') ? $request->status : null,
            'boat_id' => $request->filled('boat_id') ? $request->boat_id : null,
        ];

        // Calculate statistics
        $statistics = [
            'total_trips' => $trips->count(),
            'total_catch' => $trips->sum(function ($trip) {
                return $trip->fishStocks->sum('weight');
            }),
            'total_revenue' => $trips->sum(function ($trip) {
                return $trip->fishStocks->sum(function ($stock) {
                    return $stock->fish ? ($stock->weight * ($stock->fish->price ?? 0)) : 0;
                });
            }),
            'completed_trips' => $trips->where('status', 'completed')->count(),
            'total_boats' => $trips->pluck('boat_id')->unique()->count(),
            'fish_types' => $trips->flatMap(function ($trip) {
                return $trip->fishStocks->pluck('fish_id');
            })->unique()->count(),
        ];

        // Get settings for report header
        $settings = [
            'title' => \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'حسبة',
            'address' => \App\Models\Setting::where('key', 'address')->value('value') ?? '',
            'phone' => \App\Models\Setting::where('key', 'phone')->value('value') ?? '',
            'email' => \App\Models\Setting::where('key', 'email')->value('value') ?? '',
            'logo' => \App\Models\Setting::where('key', 'logo')->value('value') ?? '',
        ];

        // Do not generate QR code for admin report prints (leave $qrCode empty so view won't render it)
        $qrCode = null;

        $fromDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $toDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->format('Y-m-d') : null;

        $trip = $trip_id ? $trips->first() : null;

        return view('owner.reports.print.trip-report', compact(
            'trips',
            'statistics',
            'settings',
            'qrCode',
            'fromDate',
            'toDate',
            'trip',
            'filters'
        ));
    }

    // QR helpers removed for admin controller — owner controller retains its own helpers

}
