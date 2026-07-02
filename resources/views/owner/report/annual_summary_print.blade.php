@php
    $t = $summary['totals'];
    $months = $summary['months'];
    $monthNames = __('owner.annual_summary.months');

    $selectedBoat = isset($boats) ? $boats->firstWhere('id', $boatId) : null;
    $boatLabel = $selectedBoat ? ($selectedBoat->name ?? $selectedBoat->name_ar) : __('owner.annual_summary.all_boats');
    $companyName = $settings['title'] ?? ($settings['company_name'] ?? ($settings['name'] ?? ''));
@endphp

<x-report-layout
    :title="__('owner.annual_summary.title')"
    :document-number="'AS-' . $year"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? ''"
    :printable="true"
>
    <x-report-masthead :title="__('owner.annual_summary.title') . ' — ' . $year" :subtitle="__('owner.annual_summary.subtitle')" :settings="$settings" />

    {{-- Meta strip --}}
    <div class="meta-row">
        <span class="meta-item">
            <span class="lbl">{{ __('owner.annual_summary.year') }}:</span>
            <span class="val-box">{{ $year }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.annual_summary.boat') }}:</span>
            <span class="val-box">{{ $boatLabel }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.annual_summary.stats.closed_months') }}:</span>
            <span class="val-box">{{ $summary['closed_count'] }} / 12</span>
        </span>
    </div>

    {{-- Year KPI cards --}}
    <x-report-stats :items="[
        ['label' => __('owner.annual_summary.stats.sales'), 'value' => number_format($t['gross_sales'], 2), 'accent' => '#0d6efd'],
        ['label' => __('owner.annual_summary.stats.revenue'), 'value' => number_format($t['net_owner_revenue'], 2), 'accent' => '#0dcaf0'],
        ['label' => __('owner.annual_summary.stats.expenses'), 'value' => number_format($t['total_expenses'], 2), 'accent' => '#dc3545'],
        ['label' => __('owner.annual_summary.stats.depreciation'), 'value' => number_format($t['depreciation'], 2), 'accent' => '#d97706'],
        ['label' => __('owner.annual_summary.stats.net_profit'), 'value' => number_format($t['net_profit'], 2), 'accent' => '#198754', 'color' => $t['net_profit'] >= 0 ? '#198754' : '#dc3545'],
    ]" />

    {{-- Monthly breakdown --}}
    <div class="section-bar">{{ __('owner.annual_summary.breakdown_title') }}</div>
    <table class="report-table" style="margin-bottom:14px;">
        <thead>
            <tr>
                <th class="col-text" style="width:11%">{{ __('owner.annual_summary.columns.month') }}</th>
                <th style="width:9%">{{ __('owner.annual_summary.columns.status') }}</th>
                <th>{{ __('owner.annual_summary.columns.sales') }}</th>
                <th>{{ __('owner.annual_summary.columns.revenue') }}</th>
                <th>{{ __('owner.annual_summary.columns.trip_expenses') }}</th>
                <th>{{ __('owner.annual_summary.columns.general_expenses') }}</th>
                <th>{{ __('owner.annual_summary.columns.depreciation') }}</th>
                <th>{{ __('owner.annual_summary.columns.deferred') }}</th>
                <th>{{ __('owner.annual_summary.columns.expenses') }}</th>
                <th>{{ __('owner.annual_summary.columns.net_profit') }}</th>
                <th>{{ __('owner.annual_summary.columns.crew_share') }}</th>
                <th>{{ __('owner.annual_summary.columns.owner_share') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($months as $m => $closing)
                <tr>
                    <td class="col-text">{{ $monthNames[$m] }}</td>
                    @if ($closing)
                        <td>{{ __('owner.month_closing.status_closed') }}</td>
                        <td class="col-num">{{ number_format((float) $closing->gross_sales, 2) }}</td>
                        <td class="col-num">{{ number_format((float) $closing->net_owner_revenue, 2) }}</td>
                        <td class="col-num">{{ number_format((float) $closing->trip_expenses, 2) }}</td>
                        <td class="col-num">{{ number_format((float) $closing->general_expenses, 2) }}</td>
                        <td class="col-num">{{ number_format((float) $closing->depreciation, 2) }}</td>
                        <td class="col-num">{{ number_format((float) $closing->depreciation_deferred, 2) }}</td>
                        <td class="col-num">{{ number_format((float) $closing->total_expenses, 2) }}</td>
                        <td class="col-num">{{ number_format((float) $closing->net_profit, 2) }}</td>
                        <td class="col-num">{{ number_format((float) $closing->crew_share, 2) }}</td>
                        <td class="col-num">{{ number_format((float) $closing->owner_share, 2) }}</td>
                    @else
                        <td style="color:#999;">{{ __('owner.annual_summary.not_closed') }}</td>
                        <td class="col-num" style="color:#bbb;">—</td>
                        <td class="col-num" style="color:#bbb;">—</td>
                        <td class="col-num" style="color:#bbb;">—</td>
                        <td class="col-num" style="color:#bbb;">—</td>
                        <td class="col-num" style="color:#bbb;">—</td>
                        <td class="col-num" style="color:#bbb;">—</td>
                        <td class="col-num" style="color:#bbb;">—</td>
                        <td class="col-num" style="color:#bbb;">—</td>
                        <td class="col-num" style="color:#bbb;">—</td>
                        <td class="col-num" style="color:#bbb;">—</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="net-row">
                <td class="col-text" colspan="2">{{ __('owner.annual_summary.columns.total') }}</td>
                <td class="col-num">{{ number_format($t['gross_sales'], 2) }}</td>
                <td class="col-num">{{ number_format($t['net_owner_revenue'], 2) }}</td>
                <td class="col-num">{{ number_format($t['trip_expenses'], 2) }}</td>
                <td class="col-num">{{ number_format($t['general_expenses'], 2) }}</td>
                <td class="col-num">{{ number_format($t['depreciation'], 2) }}</td>
                <td class="col-num">{{ number_format($t['depreciation_deferred'], 2) }}</td>
                <td class="col-num">{{ number_format($t['total_expenses'], 2) }}</td>
                <td class="col-num">{{ number_format($t['net_profit'], 2) }}</td>
                <td class="col-num">{{ number_format($t['crew_share'], 2) }}</td>
                <td class="col-num">{{ number_format($t['owner_share'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if ($t['depreciation_deferred'] > 0)
        <p style="font-size:11px;color:#555;margin:0 0 10px;">
            {{ __('owner.annual_summary.deferred_note') }}
        </p>
    @endif

    <table class="report-footer">
        <tr>
            <td>{{ $companyName }} — {{ __('owner.reports.all_rights_reserved') }} © {{ $year }}</td>
        </tr>
    </table>
</x-report-layout>
