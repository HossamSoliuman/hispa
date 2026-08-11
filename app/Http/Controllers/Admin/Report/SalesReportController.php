<?php

namespace App\Http\Controllers\Admin\Report;

use App\DataTable\Report\SalesReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SalesReportController extends Controller
{
    private SalesReportDataTable $datatable;

    public function __construct()
    {
        $this->datatable = new SalesReportDataTable;
        $this->middleware('permission:read_sales_report', ['only' => ['index', 'show']]);
    }

    /**
     * On-screen platform-wide sales report (every owner's sales).
     */
    public function index(): View
    {
        return view('admin.report.sales');
    }

    /**
     * Server-side DataTables payload; null when the request is not an AJAX call.
     */
    public function getSalesData(Request $request): ?JsonResponse
    {
        return $this->datatable->getData($request);
    }

    /**
     * Render printable sales report (HTML) for admin
     * If filters are provided, apply them. This returns the same printable view used by owners.
     */
    public function print(Request $request): Response
    {
        $query = Sale::with(['details', 'details.unit', 'paymentMethod', 'seller', 'customer'])
            ->where('seller_type', 'owner');

        if ($request->filled('start_date')) {
            $query->whereDate('sale_datetime', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sale_datetime', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sale_id')) {
            $sales = $query->where('id', $request->sale_id)->get();
        } else {
            $sales = $query->orderBy('sale_datetime', 'desc')->get();
        }

        $totalSales = $sales->count();
        $totalWeight = $sales->sum(fn (Sale $sale): float => (float) $sale->details->sum('weight'));
        $totalRevenue = $sales->sum('total_price');
        $netOwnerAmount = $sales->sum('net_owner_amount');

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
        ), ['reportScope' => 'platform', 'showOwnerColumn' => true])), [], $filename);
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
