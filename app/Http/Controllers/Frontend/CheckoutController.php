<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckoutRegisterRequest;
use App\Http\Requests\Frontend\PaymentReceiptRequest;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Service\Owner\ReportQrService;
use App\Services\Owner\OwnerMasterDataService;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CheckoutController extends Controller
{
    public function __construct(private readonly ReportQrService $qr) {}

    /**
     * Show the focused account, bank-transfer, and confirmation checkout.
     */
    public function show(Request $request): View
    {
        $packages = $this->activePackages();
        $bank = $this->bankDetails();
        $qrCode = $bank['account_number'] !== '' ? $this->qr->dataUri($bank['account_number']) : null;

        $invoice = Auth::guard('owner')->check() ? $this->pendingInvoice() : null;

        $selectedPackage = $invoice?->subscription?->package
            ?? $packages->firstWhere('id', (int) $request->query('package_id'))
            ?? $packages->first();

        $startStep = match (true) {
            $invoice && $invoice->bank_transfer_receipt !== null => 3,
            $invoice !== null => 2,
            default => 1,
        };

        return view('site.checkout', [
            'packages' => $packages,
            'selectedPackage' => $selectedPackage,
            'bank' => $bank,
            'qrCode' => $qrCode,
            'startStep' => $startStep,
            'invoicePayload' => $invoice ? $this->invoicePayload($invoice) : null,
            'owner' => Auth::guard('owner')->user(),
        ]);
    }

    /**
     * Create the owner account and pending order, or add an order for the
     * authenticated owner, then continue to the transfer step.
     */
    public function register(CheckoutRegisterRequest $request): JsonResponse|RedirectResponse
    {
        if (Auth::guard('owner')->check() && ($existing = $this->pendingInvoice())) {
            return $this->accountResponse($request, $existing);
        }

        $data = $request->validated();
        $user = Auth::guard('owner')->user();

        if (! $user) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'role' => 'owner',
                'status' => 1,
                'password' => Hash::make($data['password']),
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('owner');
            }

            app(OwnerMasterDataService::class)->seedFor($user);
        }

        $subscription = app(SubscriptionService::class)->create([
            'user_id' => $user->id,
            'package_id' => (int) $data['package_id'],
            'start_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        if (! Auth::guard('owner')->check()) {
            Auth::guard('owner')->login($user);
            $request->session()->regenerate();
        }

        $invoice = Invoice::with('subscription.package')
            ->where('subscription_id', $subscription->id)
            ->firstOrFail();

        return $this->accountResponse($request, $invoice);
    }

    /**
     * Attach the transfer receipt while the subscription awaits confirmation.
     */
    public function payment(PaymentReceiptRequest $request): JsonResponse|RedirectResponse
    {
        $invoice = $this->pendingInvoice();

        if (! $invoice) {
            if ($request->expectsJson()) {
                return response()->json(['message' => __('marketing.payment.no_invoice_title')], 422);
            }

            return redirect()->route('site.checkout')
                ->withErrors(['bank_transfer_receipt' => __('marketing.payment.no_invoice_title')]);
        }

        $path = UploadFile($request->file('bank_transfer_receipt'), 'uploads/receipts');

        $invoice->update([
            'payment_method' => 'bank_transfer',
            'bank_transfer_receipt' => $path,
        ]);

        $payload = $this->invoicePayload($invoice->fresh('subscription.package')) + [
            'success' => __('marketing.payment.success'),
        ];

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return redirect()->route('site.checkout')->with('success', $payload['success']);
    }

    /**
     * Active plans in their configured public order.
     *
     * @return \Illuminate\Support\Collection<int, SubscriptionPackage>
     */
    private function activePackages(): \Illuminate\Support\Collection
    {
        return SubscriptionPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * The authenticated owner's most recent invoice still awaiting payment.
     */
    private function pendingInvoice(): ?Invoice
    {
        return Invoice::with('subscription.package')
            ->where('user_id', Auth::guard('owner')->id())
            ->where('payment_status', 'pending')
            ->latest('id')
            ->first();
    }

    /**
     * Build the order summary rendered after each checkout action.
     *
     * @return array{invoice_number: string, package_id: ?int, total: float, total_display: string, currency: string, package: ?string, duration: ?string, duration_label: string, boats_count: ?int, boats_label: string, dashboard_url: string}
     */
    private function invoicePayload(Invoice $invoice): array
    {
        $package = $invoice->subscription?->package;
        $duration = $package?->duration_type;
        $total = (float) $invoice->total_amount;

        return [
            'invoice_number' => (string) $invoice->invoice_number,
            'package_id' => $package?->id,
            'total' => $total,
            'total_display' => number_format($total, 0),
            'currency' => __('site.pricing.currency'),
            'package' => $package?->name,
            'duration' => $duration,
            'duration_label' => $duration ? __('site.pricing.durations.'.$duration) : '',
            'boats_count' => $package?->boats_count,
            'boats_label' => $package?->boatsLabel() ?? '',
            'dashboard_url' => route('owner.dashboard'),
        ];
    }

    private function accountResponse(Request $request, Invoice $invoice): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json($this->invoicePayload($invoice));
        }

        return redirect()->route('site.checkout');
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
