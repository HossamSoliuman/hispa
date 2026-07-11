@php
    $months = $schedule['months'];
    $assets = $schedule['assets'];
    $monthNames = __('owner.annual_summary.months');
    $typeLabels = [
        'boat' => __('owner.assets.boat'),
        'fishing_equipment' => __('owner.assets.fishing_equipment'),
        'other' => __('owner.assets.other'),
    ];

    $companyName = $settings['title'] ?? ($settings['company_name'] ?? ($settings['name'] ?? ''));
    $reportTitle = __('owner.assets.depreciation_schedule.title') . ' — ' . $year;
@endphp

<x-report-layout
    :title="$reportTitle"
    :document-number="'DEP-' . $year"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? ''"
    :printable="true"
    orientation="landscape"
>
    <x-report-masthead
        :title="$reportTitle"
        :subtitle="__('owner.assets.depreciation_schedule.subtitle')"
        :settings="$settings"
    />

    {{-- Meta strip --}}
    <div class="meta-row">
        <span class="meta-item">
            <span class="lbl">{{ __('owner.assets.depreciation_schedule.year') }}:</span>
            <span class="val-box">{{ $year }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.assets.depreciation_schedule.depreciating_assets') }}:</span>
            <span class="val-box">{{ count($assets) }}</span>
        </span>
    </div>

    <x-report-stats :items="[
        ['label' => __('owner.assets.depreciation_schedule.annual_total'), 'value' => $schedule['year_total'], 'money' => true],
        ['label' => __('owner.assets.depreciation_schedule.monthly_average'), 'value' => $schedule['monthly_average'], 'money' => true],
        ['label' => __('owner.assets.depreciation_schedule.depreciating_assets'), 'value' => count($assets)],
    ]" />

    {{-- Monthly schedule — each month with its data --}}
    <div class="section-bar">{{ __('owner.assets.depreciation_schedule.title') }}</div>
    <table class="report-table" style="margin-top:8px;margin-bottom:14px;">
        <thead>
            <tr>
                <th class="col-text" style="width:40%">{{ __('owner.assets.depreciation_schedule.month') }}</th>
                <th>{{ __('owner.assets.depreciation_schedule.monthly_depreciation') }}</th>
                <th>{{ __('owner.assets.depreciation_schedule.accumulated') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($months as $m => $row)
                <tr>
                    <td class="col-text">{{ $monthNames[$m] }}</td>
                    <td class="col-num"><x-report-money :amount="$row['total']" /></td>
                    <td class="col-num"><x-report-money :amount="$row['accumulated']" /></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="net-row">
                <td class="col-text">{{ __('owner.assets.depreciation_schedule.total') }}</td>
                <td class="col-num"><x-report-money :amount="$schedule['year_total']" /></td>
                <td class="col-num"><x-report-money :amount="$schedule['year_total']" /></td>
            </tr>
        </tfoot>
    </table>

    {{-- Per-asset breakdown for the year + lifetime depreciation progress --}}
    <div class="section-bar">{{ __('owner.assets.depreciation_schedule.breakdown_title') }}</div>
    <table class="report-table" style="margin-top:8px;margin-bottom:14px;">
        <thead>
            <tr>
                <th style="width:3%">#</th>
                <th class="col-text">{{ __('owner.assets.depreciation_schedule.asset') }}</th>
                <th style="width:9%">{{ __('owner.assets.depreciation_schedule.type') }}</th>
                <th style="width:10%">{{ __('owner.assets.depreciation_schedule.cost') }}</th>
                <th style="width:7%">{{ __('owner.assets.depreciation_schedule.useful_life') }}</th>
                <th style="width:10%">{{ __('owner.assets.depreciation_schedule.monthly_depreciation') }}</th>
                <th style="width:10%">{{ __('owner.assets.depreciation_schedule.paid_so_far') }}</th>
                <th style="width:10%">{{ __('owner.assets.depreciation_schedule.remaining') }}</th>
                <th style="width:9%">{{ __('owner.assets.depreciation_schedule.months_paid') }}</th>
                <th style="width:8%">{{ __('owner.assets.depreciation_schedule.remaining_months') }}</th>
                <th style="width:10%">{{ __('owner.assets.depreciation_schedule.asset_total') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assets as $asset)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="col-text">{{ $asset['name'] }}</td>
                    <td>{{ $typeLabels[$asset['type']] ?? $asset['type'] }}</td>
                    <td class="col-num"><x-report-money :amount="$asset['purchase_cost']" /></td>
                    <td class="col-num">{{ $asset['useful_life_years'] }}</td>
                    <td class="col-num"><x-report-money :amount="$asset['monthly']" /></td>
                    <td class="col-num"><x-report-money :amount="$asset['paid_so_far']" /></td>
                    <td class="col-num"><x-report-money :amount="$asset['remaining']" /></td>
                    <td class="col-num">{{ $asset['months_paid'] }} / {{ $asset['total_months'] }}</td>
                    <td class="col-num">{{ $asset['remaining_months'] }}</td>
                    <td class="col-num"><x-report-money :amount="$asset['year_total']" /></td>
                </tr>
            @empty
                <tr><td colspan="11" style="color:#999;">{{ __('owner.assets.depreciation_schedule.no_data') }}</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="net-row">
                <td class="col-text" colspan="10">{{ __('owner.assets.depreciation_schedule.total') }}</td>
                <td class="col-num"><x-report-money :amount="$schedule['year_total']" /></td>
            </tr>
        </tfoot>
    </table>

    <x-report-amount-words :words="amount_to_words($schedule['year_total'])" />

    <x-report-signatures :items="[
        __('owner.annual_summary.signatures.prepared_by'),
        __('owner.annual_summary.signatures.reviewed_by'),
        __('owner.annual_summary.signatures.owner'),
    ]" />

    <table class="report-footer">
        <tr>
            <td>{{ $companyName }} — {{ __('owner.reports.all_rights_reserved') }} © {{ $year }}</td>
        </tr>
    </table>
</x-report-layout>
