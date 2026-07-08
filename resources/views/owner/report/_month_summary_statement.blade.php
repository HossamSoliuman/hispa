@php
    $grossSales = (float) $f['gross_sales'];
    $tripExpenses = (float) $f['trip_expenses'];
    $generalExpenses = (float) $f['general_expenses'];
    $depreciation = (float) $f['depreciation'];
    $totalExpenses = (float) $f['total_expenses'];
    $netProfit = (float) $f['net_profit'];
    $ownerPercent = (float) $f['owner_percent'];
    $ownerShare = (float) $f['owner_share'];
    $crewShare = (float) $f['crew_share'];
    $crewCount = (int) $f['crew_count'];
    $perFisherman = (float) $f['per_fisherman'];

    $riyalIcon = view('components.riyal-icon', [
        'size' => 'sm',
        'style' => 'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.25rem;',
        'class' => 'riyal-inline',
    ])->render();
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
            <td class="ms-amount">
                {!! number_format($grossSales, 2) . ' ' . $riyalIcon !!}
            </td>
        </tr>

        {{-- Depreciation --}}
        <tr class="ms-section">
            <td colspan="2">{{ __('owner.month_summary.depreciation') }}</td>
        </tr>
        <tr class="ms-line">
            <td class="ms-label ms-indent">{{ __('owner.month_summary.depreciation') }}</td>
            <td class="ms-amount ms-neg">
                {!! number_format($depreciation, 2) . ' ' . $riyalIcon !!}
            </td>
        </tr>

        {{-- Operating expenses --}}
        <tr class="ms-section">
            <td colspan="2">{{ __('owner.month_summary.operating_expenses') }}</td>
        </tr>
        @forelse ($expenses['operating'] as $row)
            <tr class="ms-line">
                <td class="ms-label ms-indent">{{ $row['category'] }}</td>
                <td class="ms-amount ms-neg">
                    {!! number_format($row['amount'], 2) . ' ' . $riyalIcon !!}
                </td>
            </tr>
        @empty
            <tr class="ms-line">
                <td class="ms-label ms-indent ms-muted">{{ __('owner.month_summary.no_expenses') }}</td>
                <td class="ms-amount ms-muted">
                    {!! number_format(0, 2) . ' ' . $riyalIcon !!}
                </td>
            </tr>
        @endforelse
        <tr class="ms-subtotal ms-subtotal-light">
            <td class="ms-label">{{ __('owner.month_summary.total_operating_expenses') }}</td>
            <td class="ms-amount">
                {!! number_format($tripExpenses, 2) . ' ' . $riyalIcon !!}
            </td>
        </tr>

        {{-- General & administrative expenses --}}
        <tr class="ms-section">
            <td colspan="2">{{ __('owner.month_summary.general_expenses') }}</td>
        </tr>
        @forelse ($expenses['general'] as $row)
            <tr class="ms-line">
                <td class="ms-label ms-indent">{{ $row['category'] }}</td>
                <td class="ms-amount ms-neg">
                    {!! number_format($row['amount'], 2) . ' ' . $riyalIcon !!}
                </td>
            </tr>
        @empty
            <tr class="ms-line">
                <td class="ms-label ms-indent ms-muted">{{ __('owner.month_summary.no_expenses') }}</td>
                <td class="ms-amount ms-muted">
                    {!! number_format(0, 2) . ' ' . $riyalIcon !!}
                </td>
            </tr>
        @endforelse
        <tr class="ms-subtotal ms-subtotal-light">
            <td class="ms-label">{{ __('owner.month_summary.total_general_expenses') }}</td>
            <td class="ms-amount">
                {!! number_format($generalExpenses, 2) . ' ' . $riyalIcon !!}
            </td>
        </tr>

        <tr class="ms-subtotal">
            <td class="ms-label">{{ __('owner.month_summary.total_expenses') }}</td>
            <td class="ms-amount ms-neg">
                {!! '(' . number_format($totalExpenses, 2) . ') ' . $riyalIcon !!}
            </td>
        </tr>

        {{-- Net profit --}}
        <tr class="ms-total">
            <td class="ms-label" style="color:#000 !important; font-weight:700;">
                {{ __('owner.month_summary.net_profit_loss') }}
            </td>
            <td class="ms-amount" style=" font-weight:700;">
                {!! number_format($netProfit, 2) . ' ' . $riyalIcon !!}
            </td>
        </tr>
    </tbody>
</table>

{{-- Profit distribution --}}
<table class="ms-statement ms-statement-distribution">
    <thead>
        <tr>
            <th colspan="2" style="background-color:#00000023 !important; font-weight:700;">
                {{ __('owner.month_summary.distribution') }}
            </th>
        </tr>
    </thead>
    <tbody>
        <tr class="ms-line">
            <td class="ms-label">
                {{ __('owner.month_summary.owner_share') }} ({{ number_format($ownerPercent, 0) }}%)
            </td>
            <td class="ms-amount">
                {!! number_format($ownerShare, 2) . ' ' . $riyalIcon !!}
            </td>
        </tr>
        <tr class="ms-line">
            <td class="ms-label">
                {{ __('owner.month_summary.crew_share') }} ({{ number_format(100 - $ownerPercent, 0) }}%)
            </td>
            <td class="ms-amount">
                {!! number_format($crewShare, 2) . ' ' . $riyalIcon !!}
            </td>
        </tr>
        <tr class="ms-line">
            <td class="ms-label">{{ __('owner.month_summary.crew_count') }}</td>
            <td class="ms-amount">{{ number_format($crewCount, 0) }}</td>
        </tr>
        <tr class="ms-line">
            <td class="ms-label">{{ __('owner.month_summary.per_fisherman') }}</td>
            <td class="ms-amount">
                {!! number_format($perFisherman, 2) . ' ' . $riyalIcon !!}
            </td>
        </tr>
    </tbody>
</table>
