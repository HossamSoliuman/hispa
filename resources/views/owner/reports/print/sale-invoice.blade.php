<x-report-layout
    :title="__('owner.sales.invoice_title') . ' #' . $sale->number"
    title-en="Sale Invoice"
    :document-number="'#' . ($sale->number ?? str_pad($sale->id ?? 0, 8, '0', STR_PAD_LEFT))"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? null">

<style>
    /* ── Report standard: dense, bordered, horizontal "grid" layout ──
       Designed for mPDF (no flex/grid): everything is laid out with tables,
       bordered cells and tight spacing so the page reads as a compact grid
       with no large empty areas. */
    body { font-size: 9pt; line-height: 1.5; }

    .currency-symbol { display: inline-flex; align-items: center; justify-content: flex-end; gap: .15rem; }
    .currency-symbol svg { width: .75rem; height: auto; vertical-align: middle; }

    /* Horizontal key-fact strip — one bordered cell per fact */
    table.info-bar { width: 100%; border-collapse: collapse; margin: 0 0 10px; }
    table.info-bar td { border: 1px solid #e0e0e0; padding: 3px 6px; vertical-align: middle; text-align: center; }
    .ib-label { font-size: 8pt; color: #95a5a6; display: block; margin-bottom: 2px; }
    .ib-value { font-size: 9.5pt; font-weight: 700; color: #2c3e50; }

    /* Section heading */
    .section-title { font-size: 10pt; font-weight: 700; color: #1a1a1a; margin: 0 0 5px; padding-bottom: 3px; }

    /* Two sections side-by-side — fixed geometry so both dual rows line up. */
    table.dual { width: 100%; table-layout: fixed; border-collapse: collapse; margin: 0 0 10px; }
    td.dual-col, td.dual-gap { vertical-align: top; padding: 0; border: none; }
    td.dual-gap { width: 16px; }

    /* Clean horizontal-rule data tables: solid black header bar, no vertical
       borders, hairline row separators, a heavier rule above the totals row. */
    table.report-table { table-layout: fixed; width: 100%; border-collapse: collapse; margin: 0; }
    table.report-table th, table.report-table td {
        border: none; border-bottom: 1px solid #e5e5e5; padding: 3px 5px; font-size: 8.5pt;
        word-wrap: break-word; overflow-wrap: break-word; vertical-align: middle; text-align: center;
    }
    table.report-table thead th { background: #1a1a1a; color: #fff; font-weight: 700; border-bottom: none; }
    table.report-table tbody th { font-weight: 700; color: #2c3e50; }
    table.report-table tfoot td, table.report-table tfoot th {
        background: transparent; font-weight: 700; color: #1a1a1a;
        border-top: 2px solid #1a1a1a; border-bottom: none;
    }

    /* Column alignment helpers: text labels start-aligned, numbers end-aligned */
    .col-text { text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; }
    .col-num  { text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; white-space: nowrap; }

    /* KPI cards */
    .report-stats { border-spacing: 6px 0; margin: 0 0 10px; }
    .report-stats td.report-stat-card { border: 1px solid #e0e0e0; border-radius: 4px; padding: 8px 6px; }
    .report-stat-value { font-size: 13pt; }
    .report-stat-label { margin-bottom: 4px; font-size: 8.5pt; }

    .block { margin: 0 0 10px; }

    /* Compact footer: copyright on a single row */
    table.report-footer { width: 100%; border-collapse: collapse; margin: 6px 0 0; border-top: 1px solid #e0e0e0; }
    table.report-footer td { border: none; padding: 8px 4px 0; vertical-align: middle; }
    td.rf-text { text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; font-size: 8pt; color: #95a5a6; }
</style>

    <x-report-header
        :document-number="'#' . ($sale->number ?? str_pad($sale->id ?? 0, 8, '0', STR_PAD_LEFT))"
        :title="__('owner.sales.invoice_title')"
        title-en="Sale Invoice"
        :settings="$settings" />

    @php
        $totalWeight = $sale->details->sum('weight');
        $totalPrice  = $sale->details->sum('total_price');
        $paidAmount  = $totalPrice - $sale->remaining_total;
        $paymentStatusText = match ($sale->payment_status) {
            'paid' => __('owner.sales.paid'),
            'partially_paid' => __('owner.sales.partially_paid'),
            default => __('owner.sales.unpaid'),
        };
    @endphp

    {{-- Key facts — horizontal strip --}}
    <table class="info-bar">
        <tr>
            <td>
                <span class="ib-label">{{ __('owner.sales.customer') }}</span>
                <span class="ib-value">{{ $sale->customer_name ?? optional($sale->customer)->name ?? '---' }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('owner.sales.payment_method_id') }}</span>
                <span class="ib-value">{{ optional($sale->paymentMethod)->name ?? '---' }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('owner.sales.payment_status_label') }}</span>
                <span class="ib-value">{{ $paymentStatusText }}</span>
            </td>
            @if($sale->sale_datetime)
                <td>
                    <span class="ib-label">{{ __('owner.sales.datetime') }}</span>
                    <span class="ib-value">{{ \Illuminate\Support\Carbon::parse($sale->sale_datetime)->format('Y-m-d H:i') }}</span>
                </td>
            @endif
        </tr>
    </table>

    {{-- KPIs --}}
    <x-report-stats :items="[
        ['label' => __('owner.sales.sales_details'), 'value' => $sale->details->count()],
        ['label' => __('owner.sales.weight'), 'value' => number_format($totalWeight, 2)],
        ['label' => __('owner.sales.total_price'), 'value' => number_format($totalPrice, 2)],
    ]" />

    {{-- Line items (left) + payment summary (right) --}}
    <table class="dual">
        <tr>
            <td class="dual-col" valign="top" style="width:62%;">
                <div class="section-title">{{ __('owner.sales.sales_details') }}</div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width:8%;">#</th>
                            <th class="col-text" style="width:34%;">{{ __('owner.sales.fish') }}</th>
                            <th style="width:16%;">{{ __('owner.sales.weight') }}</th>
                            <th style="width:14%;">{{ __('owner.sales.unit') }}</th>
                            <th class="col-num" style="width:14%;">{{ __('owner.sales.price_per_kilo') }}</th>
                            <th class="col-num" style="width:14%;">{{ __('owner.sales.total_price') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sale->details as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="col-text">{{ $detail->fish?->scientific_name ?? ($detail->fish_name ?? '—') }}</td>
                                <td>{{ number_format($detail->weight, 2) }}</td>
                                <td>{{ $detail->unit?->name ?: __('owner.units.kg') }}</td>
                                <td class="col-num">{{ number_format($detail->price_per_kilo, 2) }} <x-riyal-icon /></td>
                                <td class="col-num">{{ number_format($detail->total_price, 2) }} <x-riyal-icon /></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="color:#95a5a6;">{{ __('owner.sales_report.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="col-text">{{ __('owner.sales.total') }}</th>
                            <th>{{ number_format($totalWeight, 2) }}</th>
                            <th></th>
                            <th></th>
                            <th class="col-num">{{ number_format($totalPrice, 2) }} <x-riyal-icon /></th>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td class="dual-gap"></td>
            <td class="dual-col" valign="top">
                <div class="section-title">{{ __('owner.sales.total') }}</div>
                <table class="report-table">
                    <tbody>
                        <tr>
                            <th class="col-text" style="width:55%;">{{ __('owner.sales.total_price') }}</th>
                            <td class="col-num" style="width:45%;">{{ number_format($totalPrice, 2) }} <x-riyal-icon /></td>
                        </tr>
                        @if($sale->commission_amount > 0)
                            <tr>
                                <th class="col-text">{{ __('owner.sales_report.commission') }} ({{ rtrim(rtrim(number_format($sale->commission_rate, 2), '0'), '.') }}%)</th>
                                <td class="col-num">{{ number_format($sale->commission_amount, 2) }} <x-riyal-icon /></td>
                            </tr>
                        @endif
                        @if($sale->labor_amount > 0)
                            <tr>
                                <th class="col-text">{{ __('owner.sales_report.labor') }} ({{ rtrim(rtrim(number_format($sale->labor_rate, 2), '0'), '.') }}%)</th>
                                <td class="col-num">{{ number_format($sale->labor_amount, 2) }} <x-riyal-icon /></td>
                            </tr>
                        @endif
                        @if($sale->commission_amount > 0 || $sale->labor_amount > 0)
                            <tr style="background:#34495e; color:#fff;">
                                <th class="col-text" style="background:#34495e; color:#fff;">{{ __('owner.sales_report.net_owner') }}</th>
                                <td class="col-num" style="color:#fff; font-weight:700;">{{ number_format($sale->net_owner_amount, 2) }} <x-riyal-icon /></td>
                            </tr>
                        @endif
                        @if($sale->payment_status === 'partially_paid')
                            <tr>
                                <th class="col-text">{{ __('owner.sales.paid_amount') }}</th>
                                <td class="col-num">{{ number_format($paidAmount, 2) }} <x-riyal-icon /></td>
                            </tr>
                            <tr>
                                <th class="col-text">{{ __('owner.sales.remaining') }}</th>
                                <td class="col-num">{{ number_format($sale->remaining_total, 2) }} <x-riyal-icon /></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <table class="report-footer">
        <tr>
            <td class="rf-text">
                {{ $settings['title'] ?? $settings['company_name'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}
            </td>
        </tr>
    </table>

</x-report-layout>
