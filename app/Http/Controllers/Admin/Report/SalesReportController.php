<?php

namespace App\Http\Controllers\Admin\Report;

use App\DataTable\Report\SalesReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new SalesReportDataTable;
        $this->middleware('permission:read_sales_report', ['only' => ['index', 'show']]);

    }

    public function index()
    {
        return view('admin.report.sales');
    }

    public function getSalesData(Request $request)
    {
        return $this->datatable->getData($request);

    }

    /**
     * Render printable sales report (HTML) for admin
     * If filters are provided, apply them. This returns the same printable view used by owners.
     */
    public function print(Request $request)
    {
        // Build query (admin oversees every owner's sales)
        $query = Sale::with(['details', 'details.unit', 'paymentMethod', 'seller', 'customer'])
            ->where('seller_type', 'owner');

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

        // If a single sale ID is requested, prefer that
        if ($request->filled('sale_id')) {
            $sales = $query->where('id', $request->sale_id)->get();
        } else {
            $sales = $query->orderBy('sale_datetime', 'desc')->get();
        }

        // Calculate totals
        $totalSales = $sales->count();
        $totalWeight = $sales->sum(function ($sale) {
            return $sale->details->sum('weight');
        });
        $totalRevenue = $sales->sum('total_price');
        $netOwnerAmount = $sales->sum('net_owner_amount');

        // Get company settings
        $settings = $this->getCompanySettings();

        $from = $request->start_date ?? null;
        $to = $request->end_date ?? null;
        $status = $request->status ?? null;

        $filename = 'sales-report-'.($from ?? 'all').'-to-'.($to ?? 'all').'.pdf';

        // Reuse the shared, standard-compliant printable view (x-report-layout).
        return pdf_report(view('owner.report.sales_print', array_merge(compact(
            'sales',
            'totalSales',
            'totalWeight',
            'totalRevenue',
            'netOwnerAmount',
            'settings',
            'from',
            'to',
            'status'
        ), ['showReportInfo' => false, 'showReportSummary' => false])), [], $filename);
    }

    /**
     * Get platform company settings for the report header (admin oversees all owners,
     * so these come from the global Setting table rather than a per-owner company).
     *
     * @return array<string, mixed>
     */
    private function getCompanySettings(): array
    {
        return adminReportCompanySettings();
    }
}
