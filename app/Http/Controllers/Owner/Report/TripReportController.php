<?php

namespace App\Http\Controllers\Owner\Report;

use App\DataTable\Owner\Report\TripReportDataTable;
use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Models\Fish;
use App\Models\Setting;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TripReportController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new TripReportDataTable;

    }

    public function index()
    {
        $fish = Fish::Active()->select('scientific_name as name', 'id')->get();

        return view('owner.report.tripe', compact('fish'));
    }

    public function getTripData(Request $request)
    {
        return $this->datatable->getData($request);

    }

    public function printTripReport(Request $request, $trip_id = null)
    {
        // For owner area the authenticated user's id is the owner id
        // Some user models may not have owner_id property — use auth()->id() as primary fallback
        $owner_id = auth()->id();

        // Build query
        $query = Trip::with([
            'boat',
            'captain',
            'owner',
            'port',
            'region',
            'governorate',
            'catches.details.fish',
            'sales.details',
        ])->where('owner_id', $owner_id);

        // Filter by specific trip if provided
        if ($trip_id) {
            $query->where('id', $trip_id);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('start_date', '>=', Carbon::parse($request->from_date));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('start_date', '<=', Carbon::parse($request->to_date));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Boat filter
        if ($request->filled('boat_id')) {
            $query->where('boat_id', $request->boat_id);
        }

        $trips = $query->orderBy('start_date', 'desc')->get();

        // Prepare filters for diagnostics and view
        $filters = [
            'trip_id' => $trip_id,
            'from_date' => $request->filled('from_date') ? $request->from_date : null,
            'to_date' => $request->filled('to_date') ? $request->to_date : null,
            'status' => $request->filled('status') ? $request->status : null,
            'boat_id' => $request->filled('boat_id') ? $request->boat_id : null,
        ];

        // If no trips found, log useful debug info to help diagnose empty reports
        if ($trips->isEmpty()) {
            \Illuminate\Support\Facades\Log::debug('TripReportController: no trips found', [
                'owner_id' => $owner_id,
                'filters' => [
                    'trip_id' => $trip_id,
                    'from_date' => $request->filled('from_date') ? $request->from_date : null,
                    'to_date' => $request->filled('to_date') ? $request->to_date : null,
                    'status' => $request->filled('status') ? $request->status : null,
                    'boat_id' => $request->filled('boat_id') ? $request->boat_id : null,
                ],
            ]);
        }

        // Per-trip financial breakdown (catch, revenue, costs, profit), keyed by trip id
        $financials = $trips->mapWithKeys(function ($trip) {
            return [$trip->id => $this->calculateTripFinancials($trip)];
        });

        // Calculate aggregate statistics
        $statistics = [
            'total_trips' => $trips->count(),
            'completed_trips' => $trips->where('status', TripStatus::Sold)->count(),
            'total_boats' => $trips->pluck('boat_id')->unique()->count(),
            'total_catch' => $financials->sum('catch_weight'),
            'total_revenue' => $financials->sum('gross_revenue'),
            'total_costs' => $financials->sum('total_costs'),
            'net_profit' => $financials->sum('net_profit'),
            'total_outstanding' => $financials->sum('outstanding'),
            'fish_types' => $trips->flatMap(function ($trip) {
                return $trip->catches?->details->pluck('fish_id') ?? collect();
            })->unique()->count(),
        ];

        // Get settings for report header
        $settings = [
            'title' => Setting::where('key', 'site_name')->value('value') ?? 'حسبة',
            'address' => Setting::where('key', 'address')->value('value') ?? '',
            'phone' => Setting::where('key', 'phone')->value('value') ?? '',
            'email' => Setting::where('key', 'email')->value('value') ?? '',
            'logo' => Setting::where('key', 'logo')->value('value') ?? '',
        ];

        // Generate QR code payload (TLV) and image using Dalal controller pattern
        $qrPayload = [
            'seller_name' => $settings['title'] ?? 'حسبة',
            'vat_number' => Setting::where('key', 'vat_number')->value('value') ?? '',
            'timestamp' => now()->toIso8601String(),
            'total' => number_format((float) ($statistics['total_revenue'] ?? 0), 2, '.', ''),
            'vat_amount' => number_format(0, 2, '.', ''),
        ];

        $tlvBase64 = $this->generateQRCode($qrPayload);
        $qrCode = $this->generateQRCodeImage($tlvBase64);

        // Date range for display
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->format('Y-m-d') : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->format('Y-m-d') : null;

        // Get single trip for detailed view
        $trip = $trip_id ? $trips->first() : null;

        return view('owner.reports.print.trip-report', compact(
            'trips',
            'statistics',
            'financials',
            'settings',
            'qrCode',
            'fromDate',
            'toDate',
            'trip',
            'owner_id',
            'filters'
        ));
    }

    /**
     * Calculate the financial summary for a single trip from its catch and sales.
     *
     * @return array{catch_weight: float, sold_weight: float, gross_revenue: float, commission: float, labor: float, total_costs: float, net_profit: float, outstanding: float}
     */
    private function calculateTripFinancials(Trip $trip): array
    {
        $catchWeight = (float) ($trip->catches?->total_weight ?? 0);
        if ($catchWeight <= 0 && $trip->catches) {
            $catchWeight = (float) $trip->catches->details->sum('weight');
        }

        $grossRevenue = (float) $trip->sales->sum('total_price');
        $commission = (float) $trip->sales->sum('commission_amount');
        $labor = (float) $trip->sales->sum('labor_amount');
        $netProfit = (float) $trip->sales->sum(function ($sale) {
            return $sale->net_owner_amount ?? ($sale->total_price - ($sale->commission_amount ?? 0) - ($sale->labor_amount ?? 0));
        });

        return [
            'catch_weight' => $catchWeight,
            'sold_weight' => (float) $trip->sales->sum(fn ($sale) => $sale->details->sum('weight')),
            'gross_revenue' => $grossRevenue,
            'commission' => $commission,
            'labor' => $labor,
            'total_costs' => $commission + $labor,
            'net_profit' => $netProfit,
            'outstanding' => (float) $trip->sales->sum('remaining_total'),
        ];
    }

    public function printAllTripsReport(Request $request)
    {
        return $this->printTripReport($request, null);
    }

    /**
     * Generate QR Code image as base64 data URL
     * Uses api.qrserver.com as a fallback when QR library is not installed
     */
    private function generateQRCodeImage($url)
    {
        // Use QR Server API - reliable and free
        try {
            $size = '200';
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size='.$size.'x'.$size.'&data='.urlencode($url);

            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'ignore_errors' => true,
                    'user_agent' => 'Mozilla/5.0 (compatible)',
                ],
            ]);

            $imageData = @file_get_contents($qrUrl, false, $context);

            if ($imageData !== false && ! empty($imageData) && strlen($imageData) > 100) {
                return 'data:image/png;base64,'.base64_encode($imageData);
            }
        } catch (\Throwable $e) {
            // Fall through to SVG fallback
        }

        // If external API fails, create a simple SVG placeholder with URL
        return $this->generateQRPlaceholder($url);
    }

    /**
     * Generate a simple QR placeholder SVG when external services fail
     */
    private function generateQRPlaceholder($url)
    {
        $shortUrl = parse_url($url, PHP_URL_HOST).parse_url($url, PHP_URL_PATH);
        $svg = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
            <rect width="200" height="200" fill="#f8f9fa" stroke="#e0e0e0" stroke-width="2"/>
            <text x="100" y="100" font-family="Arial" font-size="12" text-anchor="middle" fill="#7f8c8d">QR Code</text>
            <text x="100" y="120" font-family="Arial" font-size="8" text-anchor="middle" fill="#95a5a6">'.htmlspecialchars(substr($shortUrl, 0, 30)).'</text>
        </svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Generate QR Code for ZATCA compliance
     * TLV Format: Tag-Length-Value encoding
     */
    private function generateQRCode($data)
    {
        // TLV encoding for ZATCA e-invoice QR
        $tlv = '';

        // Tag 1: Seller name (UTF-8)
        $sellerName = $data['seller_name'] ?? '';
        $tlv .= $this->encodeTLV(1, $sellerName);

        // Tag 2: VAT registration number
        $vatNumber = $data['vat_number'] ?? '';
        $tlv .= $this->encodeTLV(2, $vatNumber);

        // Tag 3: Timestamp (ISO 8601)
        $timestamp = $data['timestamp'] ?? now()->toIso8601String();
        $tlv .= $this->encodeTLV(3, $timestamp);

        // Tag 4: Invoice total (with VAT)
        $total = number_format((float) ($data['total'] ?? 0), 2, '.', '');
        $tlv .= $this->encodeTLV(4, $total);

        // Tag 5: VAT amount
        $vatAmount = number_format((float) ($data['vat_amount'] ?? 0), 2, '.', '');
        $tlv .= $this->encodeTLV(5, $vatAmount);

        // Base64 encode the TLV data
        return base64_encode($tlv);
    }

    /**
     * Encode data in TLV format
     */
    private function encodeTLV($tag, $value)
    {
        $valueBytes = $value;
        $length = strlen($valueBytes);

        return chr($tag).chr($length).$valueBytes;
    }
}
