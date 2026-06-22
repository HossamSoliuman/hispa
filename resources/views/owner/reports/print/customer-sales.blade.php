<x-report-layout
    :title="__('owner.customers.reports.sales')"
    title-en="Customer Sales Report"
    :document-number="'#' . str_pad($statistics['total_sales'], 8, '0', STR_PAD_LEFT)"
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

    /* Two sections side-by-side — fixed geometry so both dual rows line up.
       Aligning off the cell class (not a `> tbody >` child selector) so mPDF
       reliably applies vertical-align:top and the two columns start on the
       same line regardless of which one is taller. */
    table.dual { width: 100%; table-layout: fixed; border-collapse: collapse; margin: 0 0 10px; }
    td.dual-col, td.dual-gap { vertical-align: top; padding: 0; border: none; }
    td.dual-gap { width: 16px; }

    /* Clean horizontal-rule data tables (matching the product-sales summary PDF):
       solid black header bar, no vertical borders, hairline row separators,
       a heavier rule above the totals row. */
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
    .num { text-align: center; }

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
        :document-number="'#' . str_pad($statistics['total_sales'], 8, '0', STR_PAD_LEFT)"
        :title="__('owner.customers.reports.sales')"
        title-en="Customer Sales Report"
        :settings="$settings" />

    {{-- Key facts strip --}}
    <table class="info-bar">
        <tr>
            @if($filters['from_date'])
                <td><span class="ib-label">{{ __('owner.reports.from_date') }}</span><span class="ib-value">{{ $filters['from_date'] }}</span></td>
            @endif
            @if($filters['to_date'])
                <td><span class="ib-label">{{ __('owner.reports.to_date') }}</span><span class="ib-value">{{ $filters['to_date'] }}</span></td>
            @endif
            <td><span class="ib-label">{{ __('owner.sales_report.total_sales') }}</span><span class="ib-value">{{ $statistics['total_sales'] }}</span></td>
            <td><span class="ib-label">{{ __('owner.sales_report.total_weight') }}</span><span class="ib-value">{{ formatWeight($statistics['total_weight']) }}</span></td>
            <td><span class="ib-label">{{ __('owner.sales_report.total_revenue') }}</span><span class="ib-value">{{ number_format($statistics['total_revenue'], 2) }}</span></td>
        </tr>
    </table>

    <x-report-stats :items="[
        ['label' => __('owner.sales_report.total_sales'), 'value' => $statistics['total_sales']],
        ['label' => __('owner.sales_report.total_weight'), 'value' => formatWeight($statistics['total_weight'])],
        ['label' => __('owner.sales_report.total_revenue'), 'value' => number_format($statistics['total_revenue'], 2)],
        ['label' => __('owner.customers.show.cards.remaining'), 'value' => number_format($statistics['total_remaining'], 2), 'color' => $statistics['total_remaining'] > 0 ? '#dc2626' : '#16a34a'],
    ]" />

    @if($sales->isEmpty())
        <div class="alert alert-warning">
            <strong>{{ __('owner.reports.no_data_found') }}</strong>
            <p class="mb-0 text-muted">{{ __('owner.reports.try_adjust_filters') }}</p>
        </div>
    @else
        <table class="report-table block">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th class="col-text" style="width:16%;">{{ __('owner.customers.sales_table.invoice_number') }}</th>
                    <th class="col-text" style="width:18%;">{{ __('owner.customers.sales_table.customer') }}</th>
                    <th style="width:14%;">{{ __('owner.customers.sales_table.payment_method') }}</th>
                    <th style="width:11%;">{{ __('owner.customers.sales_table.total_weight') }}</th>
                    <th class="col-num" style="width:12%;">{{ __('owner.customers.sales_table.total_price') }}</th>
                    <th class="col-num" style="width:12%;">{{ __('owner.customers.show.cards.remaining') }}</th>
                    <th style="width:12%;">{{ __('owner.customers.sales_table.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $i => $sale)
                    @php $paid = $sale->total_price - $sale->remaining_total; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="col-text">{{ $sale->number ?? '—' }}</td>
                        <td class="col-text">{{ $sale->customer_name ?? optional($sale->customer)->name ?? '—' }}</td>
                        <td>{{ optional($sale->paymentMethod)->name ?? '—' }}</td>
                        <td>{{ formatWeight($sale->details->sum('weight')) }}</td>
                        <td class="col-num">{{ number_format($sale->total_price, 2) }} <x-riyal-icon /></td>
                        <td class="col-num">{{ number_format($sale->remaining_total, 2) }} <x-riyal-icon /></td>
                        <td>{{ $sale->sale_datetime ? \Illuminate\Support\Carbon::parse($sale->sale_datetime)->format('Y-m-d') : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="col-text">{{ __('owner.sales.total') }}</td>
                    <td>{{ formatWeight($statistics['total_weight']) }}</td>
                    <td class="col-num">{{ number_format($statistics['total_revenue'], 2) }} <x-riyal-icon /></td>
                    <td class="col-num">{{ number_format($statistics['total_remaining'], 2) }} <x-riyal-icon /></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <table class="report-footer">
        <tr>
            <td class="rf-text">
                {{ $settings['company_name'] ?? $settings['title'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}
            </td>
        </tr>
    </table>

</x-report-layout>
