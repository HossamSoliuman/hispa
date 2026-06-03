<x-report-layout
    :title="__('owner.customers.reports.sales')"
    :titleEn="'Customer Sales Report'"
    :documentNumber="'SAL-' . now()->format('Ymd-His')"
    :settings="$settings ?? []"
>

    <x-slot name="extraStyles">
        .table thead th { text-align: center; }
        .table tbody td { text-align: center; }
    </x-slot>

    <x-report-header
        :documentNumber="'SAL-' . now()->format('Ymd-His')"
        :title="__('owner.customers.reports.sales')"
        :titleEn="'Customer Sales Report'"
        :settings="$settings ?? []"
    />

    <x-report-info :settings="$settings ?? []">
        <x-slot name="additionalInfo">
            <p><strong>{{ __('owner.reports.owner_id') }}:</strong> #{{ $owner->id ?? '-' }}</p>
            <p><strong>{{ __('owner.reports.owner_name') }}:</strong> {{ $owner->name ?? __('owner.reports.not_available') }}</p>
        </x-slot>
    </x-report-info>

    {{-- metadata / summary --}}
    <div class="metadata">
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.reports.report_date') }}:</span>
            <span class="meta-value">@hijri(now())</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.reports.applied_filters') }}:</span>
            <span class="meta-value">-</span>
        </div>
    </div>

    {{-- sales table --}}
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('owner.customers.sales_table.number') ?? 'Number' }}</th>
                <th>{{ __('owner.customers.sales_table.customer') ?? 'Customer' }}</th>
                <th>{{ __('owner.customers.sales_table.payment_method') ?? 'Payment' }}</th>
                <th>{{ __('owner.customers.sales_table.total_weight') ?? 'Weight' }}</th>
                <th>{{ __('owner.customers.sales_table.total_price') ?? 'Total' }}</th>
                <th>{{ __('owner.customers.sales_table.date') ?? 'Date' }}</th>
                <th>{{ __('owner.customers.sales_table.status') ?? 'Status' }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $salesList = $sales ?? collect();
            @endphp
            @forelse($salesList as $sale)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $sale->number ?? '-' }}</td>
                <td>{{ $sale->customer_name ?? ($sale->customer->name ?? '-') }}</td>
                <td>{{ $sale->payment_method ?? '-' }}</td>
                <td>{{ number_format($sale->total_weight ?? 0, 2) }} {{ __('owner.units.kg') ?? 'KG' }}</td>
                <td>
                    <span class="currency-symbol">{{ number_format($sale->total_price ?? 0, 2) }} <x-riyal-icon size="sm" /></span>
                </td>
                <td>{{ optional($sale->date ?? $sale->created_at)->format('Y-m-d') }}</td>
                <td>{{ $sale->status_label ?? ucfirst($sale->status ?? '-') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">{{ __('owner.reports.no_data_found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- summary and qr --}}
    <x-report-summary :qrCode="$qrCode ?? ''">
        <div class="summary-row">
            <span>{{ __('owner.reports.total_revenue') }}</span>
            <span class="currency-symbol">{{ number_format($totalRevenue ?? 0, 2) }} <x-riyal-icon size="sm" /></span>
        </div>
        <div class="summary-row">
            <span>{{ __('owner.reports.total_trips') ?? 'Total Sales' }}</span>
            <span>{{ number_format($salesList->count()) }}</span>
        </div>
    </x-report-summary>

    <div class="footer">
        <p>{{ __('owner.reports.all_rights_reserved') }} © {{ now()->year }}</p>
    </div>

</x-report-layout>
