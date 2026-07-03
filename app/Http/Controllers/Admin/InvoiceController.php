<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InvoicesExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService
    ) {
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

        return view('admin.invoices.index', compact(
            'invoices',
            'totalInvoices',
            'paidInvoices',
            'pendingInvoices',
            'totalRevenue',
            'pendingRevenue'
        ));
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
            'payment_method' => 'required|in:mada,visa,bank_transfer',
            'payment_status' => 'required|in:pending,paid,cancelled',
            'payment_notes' => 'nullable|string|max:500',
            'bank_transfer_receipt' => 'nullable|string',
        ]);

        $invoice->update([
            'amount' => $validated['amount'],
            'total_amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_status'],
            'payment_notes' => $validated['payment_notes'] ?? null,
            'bank_transfer_receipt' => $validated['bank_transfer_receipt'] ?? null,
            'paid_at' => $validated['payment_status'] === 'paid' && ! $invoice->paid_at ? now() : $invoice->paid_at,
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

        // Confirming payment activates the subscription and (re)starts its paid
        // period, which unlocks the owner's boat quota.
        $subscription = $invoice->subscription;
        if ($subscription && $subscription->status !== 'active') {
            $this->subscriptionService->activate($subscription);
        }

        return redirect()->back()
            ->with('success', __('admin.invoices.payment_confirmed_successfully'));
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
        $filename = 'invoices-'.now()->format('Y-m-d-His').'.xlsx';

        return Excel::download(new InvoicesExport($invoices), $filename);
    }
}
