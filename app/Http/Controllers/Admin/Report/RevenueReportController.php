<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RevenueReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_revenue_report', ['only' => ['index', 'print']]);
    }

    /**
     * On-screen subscriptions & revenue report: invoices filtered by date range
     * and payment status, with platform KPI cards.
     */
    public function index(Request $request): View
    {
        [$invoices, $kpis, $filters] = $this->buildReport($request);

        return view('admin.report.revenue', array_merge($kpis, $filters, [
            'invoices' => $invoices,
        ]));
    }

    /**
     * Printable (PDF) subscriptions & revenue report using the shared report layout.
     */
    public function print(Request $request)
    {
        [$invoices, $kpis, $filters] = $this->buildReport($request);

        $settings = $this->getCompanySettings();

        $from = $filters['from'];
        $to = $filters['to'];
        $status = $filters['status'];

        $filename = 'revenue-report-'.($from ?? 'all').'-to-'.($to ?? 'all').'.pdf';

        return pdf_report(view('admin.report.revenue_print', array_merge($kpis, [
            'invoices' => $invoices,
            'settings' => $settings,
            'from' => $from,
            'to' => $to,
            'status' => $status,
        ])), [], $filename);
    }

    /**
     * Build the filtered invoice collection, KPIs, and echoed filters shared by
     * both the on-screen and printable reports.
     *
     * @return array{0: \Illuminate\Database\Eloquent\Collection<int, Invoice>, 1: array<string, mixed>, 2: array<string, mixed>}
     */
    private function buildReport(Request $request): array
    {
        $query = Invoice::with(['user', 'subscription.package']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $invoices = $query->orderBy('created_at', 'desc')->get();

        $kpis = [
            'totalInvoices' => $invoices->count(),
            'paidRevenue' => $invoices->where('payment_status', 'paid')->sum('total_amount'),
            'pendingAmount' => $invoices->where('payment_status', 'pending')->sum('total_amount'),
            'totalVat' => $invoices->sum('vat_amount'),
            'activeSubscriptions' => $this->activeSubscriptionsCount(),
        ];

        $filters = [
            'from' => $request->start_date ?: null,
            'to' => $request->end_date ?: null,
            'status' => $request->payment_status ?: null,
        ];

        return [$invoices, $kpis, $filters];
    }

    /**
     * Count subscriptions that are currently active, mirroring Subscription::isActive().
     */
    private function activeSubscriptionsCount(): int
    {
        return Subscription::query()
            ->where('status', 'active')
            ->where('is_suspended', false)
            ->whereDate('end_date', '>=', now()->toDateString())
            ->count();
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
