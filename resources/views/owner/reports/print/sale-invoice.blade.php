@php
    $invoiceNo = $sale->number ?? str_pad($sale->id ?? 0, 8, '0', STR_PAD_LEFT);

    $totalPrice = $sale->details->sum('total_price');
    $paidAmount = $totalPrice - $sale->remaining_total;

    $customerName = $sale->customer_name ?? optional($sale->customer)->name ?? '---';
    $customerPhone = optional($sale->customer)->phone;
    $customerCity = optional(optional($sale->customer)->region)->name;

    $paymentStatusText = match ($sale->payment_status) {
        'paid' => __('owner.sales.paid'),
        'partially_paid' => __('owner.sales.partially_paid'),
        default => __('owner.sales.unpaid'),
    };
@endphp

<x-report-layout
    :title="__('owner.sales.invoice_title') . ' #' . $invoiceNo"
    :document-number="'#' . $invoiceNo"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? null">

    <x-report-masthead
        :title="__('owner.sales.invoice_title')"
        :settings="$settings" />

    {{-- Invoice number + date --}}
    <table class="info-bar" style="width:60%; margin-left:auto; margin-right:auto;">
        <tr>
            <td>
                <span class="ib-label">{{ __('owner.sales.invoice_number') }}</span>
                <span class="ib-value">{{ $invoiceNo }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('owner.sales.invoice_date') }}</span>
                <span class="ib-value">{{ \Illuminate\Support\Carbon::parse($sale->sale_datetime ?? $sale->created_at)->format('Y-m-d') }}</span>
            </td>
        </tr>
    </table>

    {{-- Customer details + other details --}}
    <table class="dual">
        <tr>
            <td class="dual-col" style="width:50%;">
                <table class="report-table">
                    <thead><tr><th colspan="2">{{ __('owner.sales.customer_data') }}</th></tr></thead>
                    <tbody>
                        <tr><th class="col-text" style="width:36%;">{{ __('owner.sales.customer') }}</th><td class="col-text">{{ $customerName }}</td></tr>
                        @if($customerCity)<tr><th class="col-text">{{ __('owner.sales.customer_city') }}</th><td class="col-text">{{ $customerCity }}</td></tr>@endif
                        @if($customerPhone)<tr><th class="col-text">{{ __('owner.sales.customer_phone') }}</th><td class="col-text">{{ $customerPhone }}</td></tr>@endif
                    </tbody>
                </table>
            </td>
            <td class="dual-gap"></td>
            <td class="dual-col" style="width:50%;">
                <table class="report-table">
                    <thead><tr><th colspan="2">{{ __('owner.sales.other_data') }}</th></tr></thead>
                    <tbody>
                        <tr><th class="col-text" style="width:36%;">{{ __('owner.sales.payment_method_id') }}</th><td class="col-text">{{ optional($sale->paymentMethod)->name ?? '---' }}</td></tr>
                        <tr><th class="col-text">{{ __('owner.sales.payment_status_label') }}</th><td class="col-text">{{ $paymentStatusText }}</td></tr>
                        @if($sale->sale_datetime)<tr><th class="col-text">{{ __('owner.sales.datetime') }}</th><td class="col-text">{{ \Illuminate\Support\Carbon::parse($sale->sale_datetime)->format('Y-m-d H:i') }}</td></tr>@endif
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- Line items --}}
    <table class="report-table">
        <thead>
            <tr>
                <th style="width:6%;">#</th>
                <th class="col-text" style="width:36%;">{{ __('owner.sales.fish') }}</th>
                <th style="width:16%;">{{ __('owner.sales.weight') }}</th>
                <th style="width:12%;">{{ __('owner.sales.unit') }}</th>
                <th class="col-num" style="width:15%;">{{ __('owner.sales.price_per_unit') }}</th>
                <th class="col-num" style="width:15%;">{{ __('owner.sales.total_price') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sale->details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="col-text">{{ $detail->fish?->name ?? ($detail->fish_name ?? '—') }}</td>
                    <td>{{ number_format($detail->weight, 2) }}</td>
                    <td>{{ $detail->unit?->name ?: __('owner.units.kg') }}</td>
                    <td class="col-num"><x-report-money :amount="$detail->price_per_kilo" /></td>
                    <td class="col-num"><x-report-money :amount="$detail->total_price" /></td>
                </tr>
            @empty
                <tr><td colspan="6" style="color:#888;">{{ __('owner.sales_report.no_data') }}</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="col-text">{{ __('owner.sales.total') }}</th>
                <th>{{ formatWeightByUnit($sale->details) }}</th>
                <th></th>
                <th></th>
                <th class="col-num"><x-report-money :amount="$totalPrice" /></th>
            </tr>
        </tfoot>
    </table>

    {{-- Amount in words + totals --}}
    <table class="dual">
        <tr>
            <td class="dual-col" valign="top" style="width:52%;">
                <x-report-amount-words :words="amount_to_words($totalPrice)" />
            </td>
            <td class="dual-gap"></td>
            <td class="dual-col" valign="top" style="width:48%;">
                <table class="report-table">
                    <tbody>
                        <tr>
                            <th class="col-text" style="width:55%;">{{ __('owner.sales.total_price') }}</th>
                            <td class="col-num"><x-report-money :amount="$totalPrice" /></td>
                        </tr>
                        @if($sale->commission_amount > 0)
                            <tr>
                                <th class="col-text">{{ __('owner.sales_report.commission') }} ({{ rtrim(rtrim(number_format($sale->commission_rate, 2), '0'), '.') }}%)</th>
                                <td class="col-num"><x-report-money :amount="$sale->commission_amount" /></td>
                            </tr>
                        @endif
                        @if($sale->labor_amount > 0)
                            <tr>
                                <th class="col-text">{{ __('owner.sales_report.labor') }} ({{ rtrim(rtrim(number_format($sale->labor_rate, 2), '0'), '.') }}%)</th>
                                <td class="col-num"><x-report-money :amount="$sale->labor_amount" /></td>
                            </tr>
                        @endif
                        @if($sale->commission_amount > 0 || $sale->labor_amount > 0)
                            <tr class="net-row">
                                <th class="col-text">{{ __('owner.sales_report.net_owner') }}</th>
                                <td class="col-num"><x-report-money :amount="$sale->net_owner_amount" /></td>
                            </tr>
                        @endif
                        @if($sale->payment_status === 'partially_paid')
                            <tr>
                                <th class="col-text">{{ __('owner.sales.paid_amount') }}</th>
                                <td class="col-num"><x-report-money :amount="$paidAmount" /></td>
                            </tr>
                            <tr>
                                <th class="col-text">{{ __('owner.sales.remaining') }}</th>
                                <td class="col-num"><x-report-money :amount="$sale->remaining_total" /></td>
                            </tr>
                        @endif
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
