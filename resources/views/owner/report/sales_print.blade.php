@php
    /** @var 'owner'|'platform' $reportScope */
    $reportScope = $reportScope ?? 'owner';

    /** @var bool $showOwnerColumn */
    $showOwnerColumn = $showOwnerColumn ?? false;
@endphp<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings"
        :title="$reportScope === 'platform' ? __('admin.report.sales.platform_print_title') : __('owner.sales_report.print_title')"
    />

    @if ($showReportInfo ?? true)
    <x-report-info :settings="$settings" :from-date="$from" :to-date="$to">
        <x-slot:additionalInfo>
            <div class="info-row">
                <div class="info-item">
                    <span class="label">{{ __('owner.sales_report.from_date') }}:</span>
                    <span class="value">{{ $from ? \Illuminate\Support\Carbon::parse($from)->format('Y-m-d') : __('owner.sales_report.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('owner.sales_report.to_date') }}:</span>
                    <span class="value">{{ $to ? \Illuminate\Support\Carbon::parse($to)->format('Y-m-d') : __('owner.sales_report.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('owner.sales_report.status_filter') }}:</span>
                    <span class="value">{{ $status ? ($status == 1 ? __('owner.sales_report.status_ongoing') : __('owner.sales_report.status_completed')) : __('owner.sales_report.all_status') }}</span>
                </div>
            </div>
        </x-slot:additionalInfo>
    </x-report-info>
    @endif

    @php $allDetails = $sales->flatMap(fn ($sale) => $sale->details); @endphp

    <x-report-stats :items="[
        ['label' => __('owner.sales_report.total_sales'), 'value' => $totalSales],
        ['label' => __('owner.sales_report.total_revenue'), 'value' => $totalRevenue, 'money' => true],
        ['label' => __('owner.sales_report.total_weight'), 'value' => formatWeightByUnit($allDetails)],
        ['label' => __('owner.sales_report.net_owner_amount'), 'value' => $netOwnerAmount, 'money' => true, 'color' => '#16a085'],
    ]" />

@if ($reportScope === 'platform' && $showOwnerColumn)
        @if($sales->isEmpty())
            <div class="alert alert-warning">
                <strong>{{ __('owner.reports.no_data_found') }}</strong>
                <p class="mb-0 text-muted">{{ __('owner.reports.try_adjust_filters') }}</p>
            </div>
        @else
            <table class="report-table block">
                <thead>
                    <tr>
                        <th style="width:4%;">#</th>
                        <th class="col-text" style="width:13%;">{{ __('owner.sales_report.invoice_number') }}</th>
                        <th class="col-text" style="width:13%;">{{ __('admin.report.sales.owner') }}</th>
                        <th class="col-text" style="width:9%;">{{ __('owner.sales_report.status') }}</th>
                        <th class="col-text" style="width:15%;">{{ __('owner.sales_report.customer') }}</th>
                        <th class="col-text" style="width:12%;">{{ __('owner.sales_report.payment_method') }}</th>
                        <th class="col-num" style="width:10%;">{{ __('owner.sales_report.weight') }}</th>
                        <th class="col-num" style="width:12%;">{{ __('owner.sales_report.total_price') }}</th>
                        <th class="col-text" style="width:12%;">{{ __('owner.sales_report.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $index => $sale)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="col-text">{{ $sale->number }}</td>
                            <td class="col-text">{{ optional($sale->seller)->name ?? '---' }}</td>
                            <td class="col-text">
                                <span class="badge {{ $sale->status == 2 ? 'bg-success' : ($sale->status == 1 ? 'bg-warning' : 'bg-secondary') }}">{{ \App\Models\Sale::statusText($sale->status) }}</span>
                            </td>
                            <td class="col-text">{{ $sale->customer_name ?? optional($sale->customer)->name ?? '---' }}</td>
                            <td class="col-text">{{ optional($sale->paymentMethod)->name ?? '---' }}</td>
                            <td class="col-num">{{ formatWeightByUnit($sale->details) }}</td>
                            <td class="col-num"><x-report-money :amount="$sale->total_price" /></td>
                            <td class="col-text">{{ $sale->sale_datetime ? \Illuminate\Support\Carbon::parse($sale->sale_datetime)->format('Y-m-d') : '---' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">{{ __('owner.sales.total') }}</td>
                        <td class="col-num">{{ formatWeightByUnit($allDetails) }}</td>
                        <td class="col-num"><x-report-money :amount="$totalRevenue" /></td>
                        <td class="col-text"></td>
                    </tr>
                </tfoot>
            </table>
        @endif
@else
    <x-report-table :headers="[
        '#',
        __('owner.sales_report.invoice_number'),
        __('owner.sales_report.status'),
        __('owner.sales_report.customer'),
        __('owner.sales_report.payment_method'),
        __('owner.sales_report.weight'),
        __('owner.sales_report.total_price'),
        __('owner.sales_report.date'),
    ]" :data="$sales">
        @foreach($sales as $index => $sale)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $sale->number }}</td>
                <td>
                    <span class="badge {{ $sale->status == 2 ? 'bg-success' : ($sale->status == 1 ? 'bg-warning' : 'bg-secondary') }}">{{ \App\Models\Sale::statusText($sale->status) }}</span>
                </td>
                <td>{{ $sale->customer_name ?? optional($sale->customer)->name ?? '---' }}</td>
                <td>{{ optional($sale->paymentMethod)->name ?? '---' }}</td>
                <td>{{ formatWeightByUnit($sale->details) }}</td>
                <td><x-report-money :amount="$sale->total_price" /></td>
                <td>{{ $sale->sale_datetime ? \Illuminate\Support\Carbon::parse($sale->sale_datetime)->format('Y-m-d') : '---' }}</td>
            </tr>
        @endforeach
    </x-report-table>@endif

    @if ($showReportSummary ?? true)
    <x-report-summary :qr-code="$settings['qr_code'] ?? null">
        <x-report-summary-row
            :label="__('owner.sales_report.total_sales')"
            :value="$totalSales"
        />
        <x-report-summary-row
            :label="__('owner.sales_report.total_weight')"
            :value="formatWeightByUnit($allDetails)"
        />
        <x-report-summary-row
            :label="__('owner.sales_report.total_revenue')"
            :value="number_format($totalRevenue, 2)"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.sales_report.net_owner_amount')"
            :value="number_format($netOwnerAmount, 2)"
            :showCurrency="true"
            :highlight="true"
        />
    </x-report-summary>
    @endif
</x-report-layout>
