<x-report-layout
    :title="__('owner.customers.reports.customers')"
    :titleEn="'Customers Report'"
    :documentNumber="'CUS-' . now()->format('Ymd-His')"
    :settings="$settings ?? []"
>

    <x-slot name="extraStyles">
        .stats-section { margin: 24px 0; }
        .currency-symbol { display: inline-flex; align-items: center; gap: 5px; }
        .currency-symbol svg { width: 14px; height: 14px; fill: currentColor; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 3px; font-size: 8pt; font-weight: 600; }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
    </x-slot>

    {{-- Report Header --}}
    <x-report-header
        :documentNumber="'CUS-' . now()->format('Ymd-His')"
        :title="__('owner.customers.reports.customers')"
        :titleEn="'Customers Report'"
        :settings="$settings ?? []"
    />

    {{-- Company Info & Report Info --}}
    <x-report-info
        :settings="$settings ?? []"
        :fromDate="null"
        :toDate="null"
    >
        <x-slot name="additionalInfo">
            <p><strong>{{ __('owner.reports.owner_id') }}:</strong> #{{ $owner->id }}</p>
            <p><strong>{{ __('owner.reports.owner_name') }}:</strong> {{ $owner->name ?? __('owner.reports.not_available') }}</p>
        </x-slot>
    </x-report-info>

    {{-- Statistics Cards --}}
    <x-report-stats :items="[
        ['label' => __('owner.customers.cards.total'), 'value' => number_format($totalCustomers)],
        ['label' => __('owner.customers.cards.active'), 'value' => number_format($activeCustomers)],
        ['label' => __('owner.customers.cards.total_sales'), 'value' => number_format($totalRevenue, 2)],
        ['label' => __('owner.customers.cards.total_orders'), 'value' => number_format($totalOrders)],
    ]" />

    {{-- Metadata Row --}}
    <div class="metadata">
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.reports.total_customers') }}:</span>
            <span class="meta-value">{{ number_format($totalCustomers) }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.reports.active_customers') }}:</span>
            <span class="meta-value">{{ number_format($activeCustomers) }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.reports.report_date') }}:</span>
            <span class="meta-value">@hijri(now())</span>
        </div>
    </div>

    {{-- Customers Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">{{ __('owner.customers.table.name') }}</th>
                <th style="width: 18%;">{{ __('owner.customers.table.email') }}</th>
                <th style="width: 12%;">{{ __('owner.customers.table.phone') }}</th>
                <th style="width: 12%;">{{ __('owner.customers.table.type') }}</th>
                <th style="width: 10%;">{{ __('owner.customers.table.order_count') }}</th>
                <th style="width: 13%;">{{ __('owner.customers.table.total_sales') }}</th>
                <th style="width: 10%;">{{ __('owner.customers.table.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $customer->name ?? __('owner.reports.not_available') }}</td>
                <td>{{ $customer->email ?? '-' }}</td>
                <td>{{ $customer->phone ?? '-' }}</td>
                <td>{{ $customer->type ?? '-' }}</td>
                <td>{{ number_format($customer->sales_count ?? 0) }}</td>
                <td>
                    <span class="currency-symbol">
                        {{ number_format($customer->total_sales ?? 0, 2) }}
                        <x-riyal-icon size="sm" />
                    </span>
                </td>
                <td>
                    @if($customer->status == 1)
                        <span class="status-badge status-active">{{ __('owner.status.active') }}</span>
                    @else
                        <span class="status-badge status-inactive">{{ __('owner.status.inactive') }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px; color: #95a5a6;">
                    {{ __('owner.reports.no_customers') }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Summary Section with QR Code --}}
    <x-report-summary :qrCode="$qrCode ?? ''">
        <div class="summary-row">
            <span>{{ __('owner.reports.total_customers') }}</span>
            <span>{{ number_format($totalCustomers) }}</span>
        </div>
        <div class="summary-row">
            <span>{{ __('owner.reports.active_customers') }}</span>
            <span>{{ number_format($activeCustomers) }}</span>
        </div>
        <div class="summary-row">
            <span>{{ __('owner.reports.inactive_customers') }}</span>
            <span>{{ number_format($totalCustomers - $activeCustomers) }}</span>
        </div>
        <div class="summary-row">
            <span>{{ __('owner.reports.total_orders') }}</span>
            <span>{{ number_format($totalOrders) }}</span>
        </div>
        <div class="summary-row">
            <span>{{ __('owner.reports.total_revenue') }}</span>
            <span class="currency-symbol">
                {{ number_format($totalRevenue, 2) }}
                <x-riyal-icon size="sm" />
            </span>
        </div>
    </x-report-summary>

    <div class="footer">
        <p>{{ __('owner.reports.all_rights_reserved') }} © {{ now()->year }}</p>
        <p>{{ __('owner.reports.thank_you') }}</p>
    </div>

</x-report-layout>
