<x-report-layout
    :title="__('owner.customers.reports.sales')"
    title-en="Customer Sales Report"
    :document-number="'#' . str_pad($statistics['total_sales'], 8, '0', STR_PAD_LEFT)"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? null">


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
                        <td class="col-num">{{ number_format($sale->total_price, 2) }}</td>
                        <td class="col-num">{{ number_format($sale->remaining_total, 2) }}</td>
                        <td>{{ $sale->sale_datetime ? \Illuminate\Support\Carbon::parse($sale->sale_datetime)->format('Y-m-d') : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="col-text">{{ __('owner.sales.total') }}</td>
                    <td>{{ formatWeight($statistics['total_weight']) }}</td>
                    <td class="col-num">{{ number_format($statistics['total_revenue'], 2) }}</td>
                    <td class="col-num">{{ number_format($statistics['total_remaining'], 2) }}</td>
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
