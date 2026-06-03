<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InvoicesExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['user', 'subscription.package', 'confirmedBy', 'coupon']);

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter pending bank transfers
        if ($request->has('pending_bank_transfers') && $request->pending_bank_transfers == '1') {
            $query->where('payment_method', 'bank_transfer')
                ->where('payment_status', 'pending');
        }

        // Search by invoice number or user name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('payment_status', 'paid')->count();
        $pendingInvoices = Invoice::where('payment_status', 'pending')->count();
        $totalRevenue = Invoice::where('payment_status', 'paid')->sum('total_amount');
        $pendingRevenue = Invoice::where('payment_status', 'pending')->sum('total_amount');
        $totalVAT = Invoice::where('payment_status', 'paid')->sum('vat_amount');

        return view('admin.invoices.index', compact(
            'invoices',
            'totalInvoices',
            'paidInvoices',
            'pendingInvoices',
            'totalRevenue',
            'pendingRevenue',
            'totalVAT'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subscriptions = Subscription::with(['user', 'package'])
            ->where('status', 'active')
            ->where('is_suspended', false)
            ->get();
        return view('admin.invoices.create', compact('subscriptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'amount' => 'required|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'payment_method' => 'required|in:mada,visa,bank_transfer',
            'payment_status' => 'required|in:pending,paid,cancelled',
            'payment_notes' => 'nullable|string|max:500',
            'bank_transfer_receipt' => 'nullable|string',
        ]);

        $subscription = Subscription::findOrFail($validated['subscription_id']);
        $vatRate = $validated['vat_rate'] ?? 0;
        $vatAmount = ($validated['amount'] * $vatRate) / 100;
        $totalAmount = $validated['amount'] + $vatAmount;

        $invoice = Invoice::create([
            'subscription_id' => $validated['subscription_id'],
            'user_id' => $subscription->user_id,
            'amount' => $validated['amount'],
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount,
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_status'],
            'payment_notes' => $validated['payment_notes'] ?? null,
            'bank_transfer_receipt' => $validated['bank_transfer_receipt'] ?? null,
            'paid_at' => $validated['payment_status'] === 'paid' ? now() : null,
        ]);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', __('admin.invoices.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['user', 'subscription.package', 'confirmedBy']);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $subscriptions = Subscription::with(['user', 'package'])->get();
        return view('admin.invoices.edit', compact('invoice', 'subscriptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'payment_method' => 'required|in:mada,visa,bank_transfer',
            'payment_status' => 'required|in:pending,paid,cancelled',
            'payment_notes' => 'nullable|string|max:500',
            'bank_transfer_receipt' => 'nullable|string',
        ]);

        $vatRate = $validated['vat_rate'] ?? 0;
        $vatAmount = ($validated['amount'] * $vatRate) / 100;
        $totalAmount = $validated['amount'] + $vatAmount;

        $invoice->update([
            'amount' => $validated['amount'],
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount,
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_status'],
            'payment_notes' => $validated['payment_notes'] ?? null,
            'bank_transfer_receipt' => $validated['bank_transfer_receipt'] ?? null,
            'paid_at' => $validated['payment_status'] === 'paid' && !$invoice->paid_at ? now() : $invoice->paid_at,
        ]);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', __('admin.invoices.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoices.index')
            ->with('success', __('admin.invoices.deleted_successfully'));
    }

    /**
     * Confirm bank transfer payment
     */
    public function confirmPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_notes' => 'nullable|string|max:500',
        ]);

        if ($invoice->payment_method !== 'bank_transfer') {
            return redirect()->back()
                ->with('error', __('admin.invoices.only_bank_transfer_can_be_confirmed'));
        }

        if ($invoice->payment_status === 'paid') {
            return redirect()->back()
                ->with('error', __('admin.invoices.already_paid'));
        }

        $invoice->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_confirmed_at' => now(),
            'payment_confirmed_by' => auth('admin')->id(),
            'payment_notes' => $validated['payment_notes'] ?? $invoice->payment_notes,
        ]);

        // Update subscription if needed
        $subscription = $invoice->subscription;
        if ($subscription && $subscription->status !== 'active') {
            $subscription->update([
                'status' => 'active',
                'is_suspended' => false,
            ]);
        }

        return redirect()->back()
            ->with('success', __('admin.invoices.payment_confirmed_successfully'));
    }

    /**
     * Generate tax report
     */
    public function taxReport(Request $request)
    {
        $query = Invoice::where('payment_status', 'paid');

        // Date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('paid_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('paid_at', '<=', $request->end_date);
        }

        $invoices = $query->with(['user', 'subscription.package'])
            ->orderBy('paid_at', 'desc')
            ->get();

        $totalAmount = $invoices->sum('amount');
        $totalVAT = $invoices->sum('vat_amount');
        $totalRevenue = $invoices->sum('total_amount');

        return view('admin.invoices.tax-report', compact(
            'invoices',
            'totalAmount',
            'totalVAT',
            'totalRevenue'
        ));
    }

    /**
     * Export invoices to Excel (applies same filters as index).
     */
    public function export(Request $request): BinaryFileResponse
    {
        $query = Invoice::with(['user', 'subscription.package']);

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->boolean('pending_bank_transfers')) {
            $query->where('payment_method', 'bank_transfer')->where('payment_status', 'pending');
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q2) => $q2->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->get();
        $filename = 'invoices-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new InvoicesExport($invoices), $filename);
    }
}
