<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\PaymentReceiptRequest;
use App\Models\Invoice;
use App\Models\Setting;
use App\Service\Owner\ReportQrService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function __construct(private readonly ReportQrService $qr)
    {
        $this->middleware(['auth:owner', 'role:owner']);
    }

    /**
     * Step 2 of checkout: show the platform bank-transfer details (managed from
     * the admin settings) and let the owner upload their transfer receipt for
     * their pending subscription invoice.
     */
    public function show(): View
    {
        $invoice = $this->pendingInvoice();
        $bank = $this->bankDetails();
        $qrCode = $bank['account_number'] !== ''
            ? $this->qr->dataUri($bank['account_number'])
            : null;

        return view('site.payment', compact('invoice', 'bank', 'qrCode'));
    }

    /**
     * Attach the uploaded receipt to the pending invoice and mark it as a bank
     * transfer. The subscription stays pending until an admin confirms payment.
     */
    public function store(PaymentReceiptRequest $request): RedirectResponse
    {
        $invoice = $this->pendingInvoice();

        if (! $invoice) {
            return redirect()->route('site.payment');
        }

        $path = UploadFile($request->file('bank_transfer_receipt'), 'uploads/receipts');

        $invoice->update([
            'payment_method' => 'bank_transfer',
            'bank_transfer_receipt' => $path,
        ]);

        session()->flash('pending_subscription', [
            'package' => $invoice->subscription?->package?->name,
            'duration' => $invoice->subscription?->package?->duration_type,
            'boats_count' => $invoice->subscription?->package?->boats_count,
            'invoice_number' => $invoice->invoice_number,
        ]);

        return redirect()->route('site.processing')
            ->with('success', __('marketing.payment.success'));
    }

    /**
     * The authenticated owner's most recent invoice that is still awaiting
     * payment, with its subscription and plan eager loaded.
     */
    private function pendingInvoice(): ?Invoice
    {
        return Invoice::with('subscription.package')
            ->where('user_id', auth('owner')->id())
            ->where('payment_status', 'pending')
            ->latest('id')
            ->first();
    }

    /**
     * Platform bank-transfer details as configured in the admin settings.
     *
     * @return array{bank_name: string, account_name: string, account_number: string, instructions: string}
     */
    private function bankDetails(): array
    {
        $values = Setting::whereIn('key', [
            'bank_name',
            'bank_account_name',
            'bank_account_number',
            'payment_instructions',
        ])->pluck('value', 'key');

        return [
            'bank_name' => (string) ($values['bank_name'] ?? ''),
            'account_name' => (string) ($values['bank_account_name'] ?? ''),
            'account_number' => (string) ($values['bank_account_number'] ?? ''),
            'instructions' => (string) ($values['payment_instructions'] ?? ''),
        ];
    }
}
