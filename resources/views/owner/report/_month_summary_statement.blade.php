@php
    $netProfit = (float) $f['net_profit'];
    $grossSales = (float) $f['gross_sales'];
    $commissionLabor = (float) $f['commission_labor'];
    $netOwnerRevenue = (float) $f['net_owner_revenue'];
    $tripExpenses = (float) $f['trip_expenses'];
    $generalExpenses = (float) $f['general_expenses'];
    $totalExpenses = (float) $f['total_expenses'];
@endphp

<table class="ms-statement">
    <thead>
        <tr>
            <th class="ms-period-cell" colspan="2">
                {{ __('owner.month_summary.period_label') }}: {{ $from }} — {{ $to }}
            </th>
        </tr>
    </thead>
    <tbody>
        {{-- Revenue --}}
        <tr class="ms-section">
            <td colspan="2">{{ __('owner.month_summary.revenue') }}</td>
        </tr>
        <tr class="ms-line">
            <td class="ms-label">{{ __('owner.month_summary.total_sales') }}</td>
            <td class="ms-amount ms-pos">{{ number_format($grossSales, 2) }}</td>
        </tr>
        <tr class="ms-line">
            <td class="ms-label ms-indent">{{ __('owner.month_summary.less_commission_labor') }}</td>
            <td class="ms-amount ms-neg">({{ number_format($commissionLabor, 2) }})</td>
        </tr>
        <tr class="ms-subtotal">
            <td class="ms-label">{{ __('owner.month_summary.net_owner_revenue') }}</td>
            <td class="ms-amount">{{ number_format($netOwnerRevenue, 2) }}</td>
        </tr>

        {{-- Operating expenses --}}
        <tr class="ms-section">
            <td colspan="2">{{ __('owner.month_summary.operating_expenses') }}</td>
        </tr>
        @forelse ($expenses['operating'] as $row)
            <tr class="ms-line">
                <td class="ms-label ms-indent">{{ $row['category'] }}</td>
                <td class="ms-amount ms-neg">{{ number_format($row['amount'], 2) }}</td>
            </tr>
        @empty
            <tr class="ms-line">
                <td class="ms-label ms-indent ms-muted">{{ __('owner.month_summary.no_expenses') }}</td>
                <td class="ms-amount ms-muted">0.00</td>
            </tr>
        @endforelse
        <tr class="ms-subtotal ms-subtotal-light">
            <td class="ms-label">{{ __('owner.month_summary.total_operating_expenses') }}</td>
            <td class="ms-amount">{{ number_format($tripExpenses, 2) }}</td>
        </tr>

        {{-- General & administrative expenses --}}
        <tr class="ms-section">
            <td colspan="2">{{ __('owner.month_summary.general_expenses') }}</td>
        </tr>
        @forelse ($expenses['general'] as $row)
            <tr class="ms-line">
                <td class="ms-label ms-indent">{{ $row['category'] }}</td>
                <td class="ms-amount ms-neg">{{ number_format($row['amount'], 2) }}</td>
            </tr>
        @empty
            <tr class="ms-line">
                <td class="ms-label ms-indent ms-muted">{{ __('owner.month_summary.no_expenses') }}</td>
                <td class="ms-amount ms-muted">0.00</td>
            </tr>
        @endforelse
        <tr class="ms-subtotal ms-subtotal-light">
            <td class="ms-label">{{ __('owner.month_summary.total_general_expenses') }}</td>
            <td class="ms-amount">{{ number_format($generalExpenses, 2) }}</td>
        </tr>

        <tr class="ms-subtotal">
            <td class="ms-label">{{ __('owner.month_summary.total_expenses') }}</td>
            <td class="ms-amount ms-neg">({{ number_format($totalExpenses, 2) }})</td>
        </tr>

        {{-- Net profit --}}
        <tr class="ms-total {{ $netProfit >= 0 ? 'ms-total-profit' : 'ms-total-loss' }}">
            <td class="ms-label">{{ __('owner.month_summary.net_profit_loss') }}</td>
            <td class="ms-amount">{{ number_format($netProfit, 2) }}</td>
        </tr>
    </tbody>
</table>

{{-- Profit distribution --}}
<table class="ms-statement ms-statement-distribution">
    <thead>
        <tr>
            <th colspan="2">{{ __('owner.month_summary.distribution') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr class="ms-line">
            <td class="ms-label">{{ __('owner.month_summary.owner_share') }} ({{ number_format($f['owner_percent'], 0) }}%)</td>
            <td class="ms-amount ms-pos">{{ number_format($f['owner_share'], 2) }}</td>
        </tr>
        <tr class="ms-line">
            <td class="ms-label">{{ __('owner.month_summary.crew_share') }} ({{ number_format(100 - $f['owner_percent'], 0) }}%)</td>
            <td class="ms-amount">{{ number_format($f['crew_share'], 2) }}</td>
        </tr>
        <tr class="ms-line">
            <td class="ms-label">{{ __('owner.month_summary.crew_count') }}</td>
            <td class="ms-amount">{{ number_format($f['crew_count'], 0) }}</td>
        </tr>
        <tr class="ms-line">
            <td class="ms-label">{{ __('owner.month_summary.per_fisherman') }}</td>
            <td class="ms-amount">{{ number_format($f['per_fisherman'], 2) }}</td>
        </tr>
    </tbody>
</table>
