<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings"
        :title="__('owner.sales_report.print_title')"
    />

    <x-report-info :settings="$settings" :from-date="$from" :to-date="$to">
        <x-slot:additionalInfo>
            <div class="info-row">
                <div class="info-item">
                    <span class="label">{{ __('owner.sales_report.from_date') }}:</span>
                    <span class="value">{{ $from ? \Alkoumi\LaravelHijriDate\Hijri::Date('d F Y', $from) : __('owner.sales_report.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('owner.sales_report.to_date') }}:</span>
                    <span class="value">{{ $to ? \Alkoumi\LaravelHijriDate\Hijri::Date('d F Y', $to) : __('owner.sales_report.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('owner.sales_report.status_filter') }}:</span>
                    <span class="value">{{ $status ? ($status == 1 ? __('owner.sales_report.status_ongoing') : __('owner.sales_report.status_completed')) : __('owner.sales_report.all_status') }}</span>
                </div>
            </div>
        </x-slot:additionalInfo>
    </x-report-info>

    <x-report-stats :items="[
        ['label' => __('owner.sales_report.total_sales'), 'value' => $totalSales],
        ['label' => __('owner.sales_report.total_revenue'), 'value' => number_format($totalRevenue, 2)],
        ['label' => __('owner.sales_report.total_weight'), 'value' => formatWeight($totalWeight)],
        ['label' => __('owner.sales_report.net_owner_amount'), 'value' => number_format($netOwnerAmount, 2), 'color' => '#16a085'],
    ]" />

    <x-report-table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('owner.sales_report.invoice_number') }}</th>
                <th>{{ __('owner.sales_report.customer') }}</th>
                <th>{{ __('owner.sales_report.payment_method') }}</th>
                <th>{{ __('owner.sales_report.weight') }}</th>
                <th>{{ __('owner.sales_report.commission') }}</th>
                <th>{{ __('owner.sales_report.labor') }}</th>
                <th>{{ __('owner.sales_report.total_price') }}</th>
                <th>{{ __('owner.sales_report.net_owner') }}</th>
                <th>{{ __('owner.sales_report.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $index => $sale)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $sale->number }}</td>
                <td>{{ $sale->customer_name ?? optional($sale->customer)->name ?? '---' }}</td>
                <td>{{ optional($sale->paymentMethod)->name ?? '---' }}</td>
                <td>{{ formatWeight($sale->details->sum('weight')) }}</td>
                <td>{{ $sale->commission_rate }}%</td>
                <td>{{ $sale->labor_rate }}%</td>
                <td><x-money-inline :amount="$sale->total_price" /></td>
                <td><x-money-inline :amount="$sale->net_owner_amount" /></td>
                <td>{{ $sale->sale_datetime ? \Alkoumi\LaravelHijriDate\Hijri::Date('d/m/Y', $sale->sale_datetime) : '---' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">{{ __('owner.sales_report.no_data') }}</td>
            </tr>
            @endforelse
        </tbody>
    </x-report-table>

    <x-report-summary :qr-code="$settings['qr_code'] ?? null">
        <x-report-summary-row
            :label="__('owner.sales_report.total_sales')"
            :value="$totalSales"
        />
        <x-report-summary-row
            :label="__('owner.sales_report.total_weight')"
            :value="formatWeight($totalWeight)"
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
</x-report-layout>
