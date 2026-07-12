<?php

namespace App\Http\Controllers\Admin\Report;

use App\DataTable\Report\TripReportDataTable;
use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Models\Fish;
use App\Models\Setting;
use App\Models\Trip;
use App\Service\Owner\TripFinancialsService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class TripReportController extends Controller
{
    private TripReportDataTable $datatable;

    public function __construct()
    {
        $this->datatable = new TripReportDataTable;
        $this->middleware('permission:read_trip_report', ['only' => ['index', 'show']]);
    }

    public function index(): View
    {
        $fish = Fish::Active()->get(['id', 'name_ar', 'name_en']);
        $boats = Boat::orderBy('id', 'desc')->get(['id', 'name_ar', 'name_en']);

        return view('admin.report.trips', compact('fish', 'boats'));
    }

    public function getTripData(Request $request): ?JsonResponse
    {
        return $this->datatable->getData($request);
    }

    /**
     * Render the platform-wide printable trip report (all owners) using the shared
     * standard-compliant view. Figures come from {@see TripFinancialsService} so the
     * printed report matches the on-screen numbers, with no owner_id scoping applied.
     */
    public function print(Request $request, ?int $trip_id = null): Response
    {
        $query = Trip::with([
            'boat',
            'captain',
            'owner',
            'port',
            'catches.details.fish',
            'catches.details.unit',
            'sales.customer',
            'sales.details',
            'expenses.category',
        ]);

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

        $filters = [
            'trip_id' => $trip_id,
            'from_date' => $request->filled('start_date') ? $request->start_date : null,
            'to_date' => $request->filled('end_date') ? $request->end_date : null,
            'status' => $request->filled('status') ? $request->status : null,
            'boat_id' => $request->filled('boat_id') ? $request->boat_id : null,
        ];

        $tripFinancials = app(TripFinancialsService::class);
        $financials = $trips->mapWithKeys(function (Trip $trip) use ($tripFinancials): array {
            return [$trip->id => $tripFinancials->compute($trip)];
        });

        $statistics = [
            'total_trips' => $trips->count(),
            'completed_trips' => $trips->filter(fn (Trip $trip): bool => $trip->status === TripStatus::Sold)->count(),
            'total_boats' => $trips->pluck('boat_id')->filter()->unique()->count(),
            'total_catch' => $financials->sum('catch_weight'),
            'total_catch_by_unit' => $financials->reduce(function (Collection $carry, array $trip): Collection {
                foreach ($trip['catch_weight_by_unit'] as $unit => $weight) {
                    $carry[$unit] = ($carry[$unit] ?? 0) + $weight;
                }

                return $carry;
            }, collect()),
            'total_revenue' => $financials->sum('gross_revenue'),
            'total_costs' => $financials->sum('total_costs'),
            'net_profit' => $financials->sum('net_profit'),
            'total_outstanding' => $financials->sum('outstanding'),
            'fish_types' => $trips->flatMap(function (Trip $trip): Collection {
                return $trip->catches?->details->pluck('fish_id') ?? collect();
            })->unique()->count(),
        ];

        $settings = $this->getCompanySettings();
        $qrCode = $settings['qr_code'] ?? null;

        $fromDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $toDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->format('Y-m-d') : null;

        $trip = $trip_id ? $trips->first() : null;

        $filename = $trip_id
            ? 'trip-'.($trips->first()?->number ?? $trip_id).'.pdf'
            : 'trips-report-'.($fromDate ?? 'all').'-to-'.($toDate ?? 'all').'.pdf';

        return pdf_report(view('owner.reports.print.trip-report', compact(
            'trips',
            'statistics',
            'financials',
            'settings',
            'qrCode',
            'fromDate',
            'toDate',
            'trip',
            'filters'
        )), [], $filename);
    }

    /**
     * Get platform company settings for the report header (admin oversees all owners,
     * so these come from the global Setting table rather than a per-owner company).
     *
     * @return array<string, mixed>
     */
    private function getCompanySettings(): array
    {
        $companyName = Setting::where('key', 'site_name')->value('value') ?? config('app.name');

        return [
            'name' => $companyName,
            'title' => $companyName,
            'company_name' => $companyName,
            'address' => Setting::where('key', 'address')->value('value') ?? '',
            'phone' => Setting::where('key', 'phone')->value('value') ?? '',
            'email' => Setting::where('key', 'email')->value('value') ?? '',
            'logo' => Setting::where('key', 'logo')->value('value') ?? '',
            'qr_code' => app(\App\Service\Owner\ReportQrService::class)->dataUri("Company: {$companyName}"),
        ];
    }
}
