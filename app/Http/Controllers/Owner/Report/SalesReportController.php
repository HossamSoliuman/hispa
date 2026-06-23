<?php

namespace App\Http\Controllers\Owner\Report;

use App\DataTable\Owner\Report\SalesReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\Sale;
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
            ->where('seller_type', 'owner')
            ->where('seller_id', $owner_id);

        // Date range filter (on the sale date, not the created_at timestamp)
        if ($request->filled('start_date')) {
            $query->whereDate('sale_datetime', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sale_datetime', '<=', $request->end_date);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sales = $query->orderBy('sale_datetime', 'desc')->get();

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

        $filename = 'sales-report-'.($from ?? 'all').'-to-'.($to ?? 'all').'.pdf';

        return pdf_report(view('owner.report.sales_print', compact(
            'sales',
            'totalSales',
            'totalWeight',
            'totalRevenue',
            'netOwnerAmount',
            'settings',
            'from',
            'to',
            'status'
        )), [], $filename);
    }

    /**
     * Get company settings for report header
     */
    private function getCompanySettings()
    {
        return ownerCompanySettings([
            'qr_code' => $this->generateQRCodeImage(),
        ]);
    }

    /**
     * Generate QR code image for the report (locally, never via external HTTP).
     */
    private function generateQRCodeImage(): ?string
    {
        $companyName = currentCompany()?->name ?: 'حسبة';

        return app(\App\Service\Owner\ReportQrService::class)
            ->dataUri("Company: {$companyName}");
    }
}
