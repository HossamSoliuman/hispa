<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\DalalInvoiceDataTable;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DalalInvoiceController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new DalalInvoiceDataTable;
    }

    /**
     * Display a listing of invoices sent by dalals to this owner
     */
    public function index()
    {
        $ownerId = Auth::id();

        // Get statistics
        $stats = $this->getStatistics($ownerId);

        return view('owner.dalal-invoices.index', $stats);
    }

    /**
     * Get DataTable data for invoices
     */
    public function getInvoiceData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    /**
     * Show the details of a specific invoice
     */
    public function show($id)
    {
        $ownerId = Auth::id();

        // Get the sale with relationships
        $sale = Sale::with([
            'trip.owner',
            'trip.boat',
            'seller',
            'customer',
            'details.fish',
            'paymentMethod',
            'payments',
        ])
            ->whereHas('trip', function ($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->where('seller_type', 'dalal')
            ->whereNotNull('invoice_sent_at')
            ->findOrFail($id);

        // Get payment methods for potential payments
        $payment_methods = PaymentMethod::all();

        return view('owner.dalal-invoices.show', compact('sale', 'payment_methods'));
    }

    /**
     * Accept an invoice (mark as reviewed/acknowledged)
     */
    public function accept(Request $request, $id)
    {
        $ownerId = Auth::id();

        $sale = Sale::whereHas('trip', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })
            ->where('seller_type', 'dalal')
            ->whereNotNull('invoice_sent_at')
            ->findOrFail($id);

        // You can add a custom field to track owner acceptance if needed
        // For now, we'll just add a note to the sale
        $sale->update([
            'notes' => ($sale->notes ?? '')."\n".
                      __('owner.dalal_invoices.accepted_note', [
                          'date' => now()->format('Y-m-d H:i:s'),
                          'owner' => Auth::user()->name,
                      ]),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('owner.dalal_invoices.invoice_accepted'),
        ]);
    }

    /**
     * Reject an invoice with reason
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $ownerId = Auth::id();

        $sale = Sale::whereHas('trip', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })
            ->where('seller_type', 'dalal')
            ->whereNotNull('invoice_sent_at')
            ->findOrFail($id);

        // Add rejection note
        $sale->update([
            'notes' => ($sale->notes ?? '')."\n".
                      __('owner.dalal_invoices.rejected_note', [
                          'date' => now()->format('Y-m-d H:i:s'),
                          'owner' => Auth::user()->name,
                          'reason' => $request->reason,
                      ]),
        ]);

        // TODO: Optionally notify the dalal about the rejection

        return response()->json([
            'success' => true,
            'message' => __('owner.dalal_invoices.invoice_rejected'),
        ]);
    }

    /**
     * Get statistics for dashboard cards
     */
    private function getStatistics($ownerId)
    {
        $query = Sale::whereHas('trip', function ($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })
            ->where('seller_type', 'dalal')
            ->whereNotNull('invoice_sent_at');

        $totalInvoices = $query->count();
        $totalAmount = $query->sum('total_price');
        $pendingAmount = $query->where('payment_status', 0)->sum('total_price');
        $paidAmount = $query->where('payment_status', 1)->sum('total_price');

        // Count invoices sent in last 7 days
        $recentInvoices = Sale::whereHas('trip', function ($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })
            ->where('seller_type', 'dalal')
            ->whereNotNull('invoice_sent_at')
            ->where('invoice_sent_at', '>=', now()->subDays(7))
            ->count();

        return [
            'totalInvoices' => $totalInvoices,
            'totalAmount' => $totalAmount,
            'pendingAmount' => $pendingAmount,
            'paidAmount' => $paidAmount,
            'recentInvoices' => $recentInvoices,
        ];
    }

    /**
     * Get pending invoice count (for sidebar badge)
     */
    public static function getPendingCount()
    {
        $ownerId = Auth::id();

        if (! $ownerId) {
            return 0;
        }

        return Sale::whereHas('trip', function ($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })
            ->where('seller_type', 'dalal')
            ->whereNotNull('invoice_sent_at')
            ->where('payment_status', 0) // Pending payment
            ->count();
    }
}
