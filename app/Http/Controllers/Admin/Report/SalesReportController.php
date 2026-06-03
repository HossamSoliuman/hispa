<?php

namespace App\Http\Controllers\Admin\Report;

use App\DataTable\Report\SalesReportDataTable;
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
        // Build query (admin can see all sales)
        $query = Sale::with(['details', 'paymentMethod', 'seller', 'customer']);

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

        // If a single sale ID is requested, prefer that
        if ($request->filled('sale_id')) {
            $sales = $query->where('id', $request->sale_id)->get();
        } else {
            $sales = $query->orderBy('created_at', 'desc')->get();
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

        // Return admin printable view
        return view('admin.report.sales_print', compact(
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
     * Get company settings for report header (same as owner)
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
            'qr_code' => null, // admin prints currently don't include QR
        ];
    }
}
