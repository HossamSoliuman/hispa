<x-report-layout
    :title="__('owner.customers.statement.title') . ' — ' . $customer->name"
    :title-en="'Customer Statement'"
    :document-number="'#' . str_pad($customer->id, 8, '0', STR_PAD_LEFT)"
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
        :document-number="'#' . str_pad($customer->id, 8, '0', STR_PAD_LEFT)"
        :title="__('owner.customers.statement.title')"
        :title-en="'Customer Statement'"
        :settings="$settings" />

    {{-- Customer key facts --}}
    <table class="info-bar">
        <tr>
            <td>
                <span class="ib-label">{{ __('owner.customers.statement.customer_info') }}</span>
                <span class="ib-value">{{ $customer->name ?: '—' }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('owner.customers.show.phone') }}</span>
                <span class="ib-value">{{ $customer->phone ?: '—' }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('owner.customers.show.email') }}</span>
                <span class="ib-value">{{ $customer->email ?: '—' }}</span>
            </td>
            @if($customer->type)
                <td>
                    <span class="ib-label">{{ __('owner.customers.show.type') }}</span>
                    <span class="ib-value">{{ $customer->type }}</span>
                </td>
            @endif
            <td>
                <span class="ib-label">{{ __('owner.customers.show.registered_at') }}</span>
                <span class="ib-value">{{ optional($customer->created_at)->format('Y-m-d') ?? '—' }}</span>
            </td>
        </tr>
    </table>

    {{-- KPIs --}}
    <x-report-stats :items="[
        ['label' => __('owner.customers.show.cards.orders'), 'value' => number_format($statistics['total_orders'])],
        ['label' => __('owner.customers.show.cards.purchases'), 'value' => number_format($statistics['total_purchases'], 2)],
        ['label' => __('owner.customers.show.cards.paid'), 'value' => number_format($statistics['total_paid'], 2)],
        ['label' => __('owner.customers.show.cards.remaining'), 'value' => number_format($statistics['total_remaining'], 2), 'color' => $statistics['total_remaining'] > 0 ? '#dc2626' : '#16a34a'],
    ]" />

    {{-- Invoices listing --}}
    <div class="section-title">{{ __('owner.customers.show.invoices_title') }}</div>
    <table class="report-table block">
        <thead>
            <tr>
                <th style="width:6%;">#</th>
                <th style="width:18%;">{{ __('owner.customers.show.table.invoice_number') }}</th>
                <th style="width:13%;">{{ __('owner.customers.show.table.date') }}</th>
                <th style="width:15%;">{{ __('owner.customers.show.table.payment_method') }}</th>
                <th style="width:14%;">{{ __('owner.customers.show.table.payment_status') }}</th>
                <th class="col-num" style="width:11%;">{{ __('owner.customers.show.table.total_price') }}</th>
                <th class="col-num" style="width:11%;">{{ __('owner.customers.show.table.paid') }}</th>
                <th class="col-num" style="width:12%;">{{ __('owner.customers.show.table.remaining') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customer->sales as $sale)
                @php $paid = $sale->total_price - $sale->remaining_total; @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $sale->number }}</td>
                    <td>{{ optional($sale->sale_datetime)->format('Y-m-d') ?? '—' }}</td>
                    <td>{{ optional($sale->paymentMethod)->name ?: '—' }}</td>
                    <td>{{ \App\Models\Sale::paymentStatusText($sale->payment_status) }}</td>
                    <td class="col-num">{{ number_format($sale->total_price, 2) }} <x-riyal-icon /></td>
                    <td class="col-num">{{ number_format($paid, 2) }} <x-riyal-icon /></td>
                    <td class="col-num">{{ number_format($sale->remaining_total, 2) }} <x-riyal-icon /></td>
                </tr>
            @empty
                <tr><td colspan="8" style="color:#95a5a6;">{{ __('owner.customers.show.no_invoices') }}</td></tr>
            @endforelse
        </tbody>
        @if($customer->sales->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="5" class="col-text">{{ __('owner.sales.total') }}</td>
                    <td class="col-num">{{ number_format($statistics['total_purchases'], 2) }} <x-riyal-icon /></td>
                    <td class="col-num">{{ number_format($statistics['total_paid'], 2) }} <x-riyal-icon /></td>
                    <td class="col-num">{{ number_format($statistics['total_remaining'], 2) }} <x-riyal-icon /></td>
                </tr>
            </tfoot>
        @endif
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
