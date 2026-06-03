<?php

namespace App\Http\Controllers\Owner\Report;

use App\DataTable\Owner\Report\SalesReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new SalesReportDataTable;

    }

    public function index()
    {
        return view('owner.report.sales');
    }

    public function getSalesData(Request $request)
    {
        return $this->datatable->getData($request);

    }

    public function print(Request $request)
    {
        $owner_id = auth()->user()->id;

        // Build query
        $query = Sale::with(['details', 'paymentMethod', 'seller', 'customer'])
            ->where('seller_id', $owner_id);

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sales = $query->orderBy('created_at', 'desc')->get();

        // Calculate totals
        $totalSales = $sales->count();
        $totalWeight = $sales->sum(function ($sale) {
            return $sale->details->sum('weight');
        });
        $totalRevenue = $sales->sum('total_price');
        $netOwnerAmount = $sales->sum('net_owner_amount');

        // Get company settings
        $settings = $this->getCompanySettings();

        // Get filter values for display
        $from = $request->start_date ?? null;
        $to = $request->end_date ?? null;
        $status = $request->status ?? null;

        return view('owner.report.sales_print', compact(
            'sales',
            'totalSales',
            'totalWeight',
            'totalRevenue',
            'netOwnerAmount',
            'settings',
            'from',
            'to',
            'status'
        ));
    }

    /**
     * Get company settings for report header
     */
    private function getCompanySettings()
    {
        $companyName = Setting::where('key', 'site_name')->value('value') ?? 'حسبة';

        return [
            'name' => $companyName,
            'company_name' => $companyName,
            'address' => Setting::where('key', 'address')->value('value') ?? '',
            'phone' => Setting::where('key', 'phone')->value('value') ?? '',
            'email' => Setting::where('key', 'email')->value('value') ?? '',
            'logo' => Setting::where('key', 'logo')->value('value') ?? '',
            'qr_code' => $this->generateQRCodeImage(),
        ];
    }

    /**
     * Generate QR code image for the report
     */
    private function generateQRCodeImage()
    {
        $companyName = Setting::where('key', 'site_name')->value('value') ?? 'حسبة';
        $vatNumber = Setting::where('key', 'vat_number')->value('value') ?? '';

        $qrData = "Company: {$companyName}\nVAT: {$vatNumber}";

        try {
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='.urlencode($qrData);
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'ignore_errors' => true,
                ],
            ]);

            $imageData = @file_get_contents($qrUrl, false, $context);

            if ($imageData !== false && ! empty($imageData)) {
                return 'data:image/png;base64,'.base64_encode($imageData);
            }
        } catch (\Exception $e) {
            // Fallback to placeholder
        }

        return 'data:image/svg+xml;base64,'.base64_encode('<svg width="200" height="200"><rect fill="#f0f0f0" width="200" height="200"/></svg>');
    }
}
