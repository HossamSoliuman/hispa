@php
    $subscription = $invoice->subscription;
    // Fall back to the subscription's owner if the invoice's direct link is missing.
    $user = $invoice->user ?? $subscription?->user;
    $package = $subscription->package ?? null;

    $recipientEmail = $user?->email;
    $recipientPhone = $user?->phone;
    $recipientRegion = $user?->region_id ? $user->region->name : null;
    $recipientGovernorate = $user?->governorate_id ? $user->governorate->name : null;
    $recipientDoc = $user?->cr_number ?: $user?->id_number;

    $paymentStatusText = match ($invoice->payment_status) {
        'paid' => __('admin.invoices.paid'),
        'cancelled' => __('admin.invoices.cancelled'),
        default => __('admin.invoices.pending'),
    };

    $subscriptionStatus = $subscription
        ? __('admin.invoices.subscription_statuses.' . $subscription->status)
        : null;

    $discount = (float) ($invoice->discount_amount ?? 0);
    $vat = (float) ($invoice->vat_amount ?? 0);
@endphp

<x-report-layout
    :title="__('admin.invoices.invoice_title') . ' #' . $invoice->invoice_number"
    :document-number="'#' . $invoice->invoice_number"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? null">

    <x-report-masthead
        :title="__('admin.invoices.invoice_title')"
        :settings="$settings" />

    {{-- Invoice number + date + method + status --}}
    <table class="info-bar">
        <tr>
            <td>
                <span class="ib-label">{{ __('admin.invoices.invoice_number') }}</span>
                <span class="ib-value">{{ $invoice->invoice_number }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('admin.invoices.invoice_date') }}</span>
                <span class="ib-value">{{ \Illuminate\Support\Carbon::parse($invoice->created_at)->format('Y-m-d') }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('admin.invoices.payment_method') }}</span>
                <span class="ib-value">{{ __('admin.invoices.payment_methods.' . $invoice->payment_method) }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('admin.invoices.payment_status') }}</span>
                <span class="ib-value">{{ $paymentStatusText }}</span>
            </td>
            @if($invoice->paid_at)
                <td>
                    <span class="ib-label">{{ __('admin.invoices.payment_date') }}</span>
                    <span class="ib-value">{{ \Illuminate\Support\Carbon::parse($invoice->paid_at)->format('Y-m-d') }}</span>
                </td>
            @endif
        </tr>
    </table>

    {{-- Recipient (all user info) + subscription details --}}
    <table class="dual">
        <tr>
            <td class="dual-col" valign="top" style="width:50%;">
                <table class="report-table">
                    <thead><tr><th colspan="2">{{ __('admin.invoices.bill_to') }}</th></tr></thead>
                    <tbody>
                        <tr><th class="col-text" style="width:42%;">{{ __('admin.invoices.name') }}</th><td class="col-text">{{ $user?->name ?? '--' }}</td></tr>
                        <tr class="net-row"><th class="col-text">{{ __('admin.invoices.send_to_email') }}</th><td class="col-text">{{ $recipientEmail ?: '--' }}</td></tr>
                        @if($recipientPhone)<tr><th class="col-text">{{ __('admin.invoices.phone') }}</th><td class="col-text">{{ $recipientPhone }}</td></tr>@endif
                        @if($user?->owner_type)<tr><th class="col-text">{{ __('admin.invoices.account_type') }}</th><td class="col-text">{{ __('admin.invoices.owner_types.' . $user->owner_type) }}</td></tr>@endif
                        @if($recipientDoc)<tr><th class="col-text">{{ __('admin.invoices.id_number') }}</th><td class="col-text">{{ $recipientDoc }}</td></tr>@endif
                        @if($recipientRegion || $recipientGovernorate)<tr><th class="col-text">{{ __('admin.invoices.region') }}</th><td class="col-text">{{ collect([$recipientRegion, $recipientGovernorate])->filter()->implode(' - ') }}</td></tr>@endif
                        @if($user?->address)<tr><th class="col-text">{{ __('admin.invoices.address') }}</th><td class="col-text">{{ $user->address }}</td></tr>@endif
                    </tbody>
                </table>
            </td>
            <td class="dual-gap"></td>
            <td class="dual-col" valign="top" style="width:50%;">
                <table class="report-table">
                    <thead><tr><th colspan="2">{{ __('admin.invoices.subscription_details') }}</th></tr></thead>
                    <tbody>
                        <tr><th class="col-text" style="width:42%;">{{ __('admin.invoices.subscription') }}</th><td class="col-text">{{ $package->name ?? '--' }}</td></tr>
                        @if($package)<tr><th class="col-text">{{ __('admin.invoices.boats_allowed') }}</th><td class="col-text">{{ $package->boatsLabel() }}</td></tr>@endif
                        @if($subscriptionStatus)<tr><th class="col-text">{{ __('admin.invoices.subscription_status') }}</th><td class="col-text">{{ $subscriptionStatus }}</td></tr>@endif
                        @if($subscription?->start_date)<tr><th class="col-text">{{ __('admin.invoices.start_date') }}</th><td class="col-text">{{ \Illuminate\Support\Carbon::parse($subscription->start_date)->format('Y-m-d') }}</td></tr>@endif
                        @if($subscription?->end_date)<tr><th class="col-text">{{ __('admin.invoices.end_date') }}</th><td class="col-text">{{ \Illuminate\Support\Carbon::parse($subscription->end_date)->format('Y-m-d') }}</td></tr>@endif
                        @if($package?->duration_type)<tr><th class="col-text">{{ __('admin.invoices.duration') }}</th><td class="col-text">{{ __('admin.invoices.durations.' . $package->duration_type) }}</td></tr>@endif
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- Amount in words + totals --}}
    <table class="dual">
        <tr>
            <td class="dual-col" valign="top" style="width:50%;">
                <x-report-amount-words :words="amount_to_words($invoice->total_amount)" />
            </td>
            <td class="dual-gap"></td>
            <td class="dual-col" valign="top" style="width:50%;">
                <table class="report-table">
                    <tbody>
                        <tr>
                            <th class="col-text" style="width:55%;">{{ __('admin.invoices.amount') }}</th>
                            <td class="col-num"><x-report-money :amount="$invoice->amount" /></td>
                        </tr>
                        @if($discount > 0)
                            <tr>
                                <th class="col-text">{{ __('admin.invoices.discount') }}@if($invoice->coupon) ({{ $invoice->coupon->code }})@endif</th>
                                <td class="col-num">- <x-report-money :amount="$discount" /></td>
                            </tr>
                        @endif
                        @if($vat > 0)
                            <tr>
                                <th class="col-text">{{ __('admin.invoices.vat') }}@if($invoice->vat_rate) ({{ rtrim(rtrim(number_format($invoice->vat_rate, 2), '0'), '.') }}%)@endif</th>
                                <td class="col-num"><x-report-money :amount="$vat" /></td>
                            </tr>
                        @endif
                        <tr class="net-row">
                            <th class="col-text">{{ __('admin.invoices.total_amount') }}</th>
                            <td class="col-num"><x-report-money :amount="$invoice->total_amount" /></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <table class="report-footer">
        <tr>
            <td>{{ $settings['title'] ?? $settings['company_name'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}</td>
        </tr>
    </table>

</x-report-layout>
