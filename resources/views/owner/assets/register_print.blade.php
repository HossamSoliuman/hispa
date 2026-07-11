@php
    $assets = $register['assets'];
    $totals = $register['totals'];
    $typeLabels = [
        'boat' => __('owner.assets.boat'),
        'fishing_equipment' => __('owner.assets.fishing_equipment'),
        'other' => __('owner.assets.other'),
    ];
    $statusLabels = [
        'active' => __('owner.assets.active'),
        'sold' => __('owner.assets.sold'),
        'damaged' => __('owner.assets.damaged'),
    ];

    $companyName = $settings['title'] ?? ($settings['company_name'] ?? ($settings['name'] ?? ''));
    $reportTitle = __('owner.analysis_reports.assets_register.title');
    $reportDate = now()->format('Y-m-d');
@endphp

<x-report-layout
    :title="$reportTitle"
    :document-number="'AST-' . now()->format('Ymd')"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? ''"
    :printable="true"
    orientation="landscape"
>
    <x-report-masthead
        :title="$reportTitle"
        :subtitle="__('owner.analysis_reports.assets_register.subtitle')"
        :settings="$settings"
    />

    {{-- Meta strip --}}
    <div class="meta-row">
        <span class="meta-item">
            <span class="lbl">{{ __('owner.analysis_reports.assets_register.report_date') }}:</span>
            <span class="val-box">{{ $reportDate }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.analysis_reports.assets_register.assets_count') }}:</span>
            <span class="val-box">{{ $totals['count'] }}</span>
        </span>
    </div>

    <x-report-stats :items="[
        ['label' => __('owner.analysis_reports.assets_register.stats.total_cost'), 'value' => $totals['cost'], 'money' => true],
        ['label' => __('owner.analysis_reports.assets_register.stats.accumulated'), 'value' => $totals['accumulated'], 'money' => true],
        ['label' => __('owner.analysis_reports.assets_register.stats.book_value'), 'value' => $totals['book_value'], 'money' => true],
        ['label' => __('owner.analysis_reports.assets_register.stats.count'), 'value' => $totals['count']],
    ]" />

    <div class="section-bar">{{ $reportTitle }}</div>
    <table class="report-table" style="margin-top:8px;margin-bottom:14px;">
        <thead>
            <tr>
                <th style="width:3%">#</th>
                <th class="col-text">{{ __('owner.analysis_reports.assets_register.columns.asset') }}</th>
                <th style="width:9%">{{ __('owner.analysis_reports.assets_register.columns.type') }}</th>
                <th style="width:9%">{{ __('owner.analysis_reports.assets_register.columns.boat') }}</th>
                <th style="width:9%">{{ __('owner.analysis_reports.assets_register.columns.purchase_date') }}</th>
                <th style="width:10%">{{ __('owner.analysis_reports.assets_register.columns.cost') }}</th>
                <th style="width:9%">{{ __('owner.analysis_reports.assets_register.columns.salvage') }}</th>
                <th style="width:6%">{{ __('owner.analysis_reports.assets_register.columns.useful_life') }}</th>
                <th style="width:9%">{{ __('owner.analysis_reports.assets_register.columns.monthly') }}</th>
                <th style="width:10%">{{ __('owner.analysis_reports.assets_register.columns.accumulated') }}</th>
                <th style="width:10%">{{ __('owner.analysis_reports.assets_register.columns.book_value') }}</th>
                <th style="width:7%">{{ __('owner.analysis_reports.assets_register.columns.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assets as $asset)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="col-text">{{ $asset['name'] }}</td>
                    <td>{{ $typeLabels[$asset['type']] ?? $asset['type'] }}</td>
                    <td>{{ $asset['boat'] ?? '—' }}</td>
                    <td>{{ $asset['purchase_date'] ?? '—' }}</td>
                    <td class="col-num"><x-report-money :amount="$asset['purchase_cost']" /></td>
                    <td class="col-num"><x-report-money :amount="$asset['salvage_value']" /></td>
                    <td class="col-num">{{ $asset['useful_life_years'] }}</td>
                    <td class="col-num"><x-report-money :amount="$asset['monthly']" /></td>
                    <td class="col-num"><x-report-money :amount="$asset['accumulated']" /></td>
                    <td class="col-num"><x-report-money :amount="$asset['book_value']" /></td>
                    <td>{{ $statusLabels[$asset['status']] ?? $asset['status'] }}</td>
                </tr>
            @empty
                <tr><td colspan="12" style="color:#999;">{{ __('owner.analysis_reports.assets_register.no_data') }}</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="net-row">
                <td class="col-text" colspan="5">{{ __('owner.analysis_reports.assets_register.total') }}</td>
                <td class="col-num"><x-report-money :amount="$totals['cost']" /></td>
                <td class="col-num"><x-report-money :amount="$totals['salvage']" /></td>
                <td></td>
                <td></td>
                <td class="col-num"><x-report-money :amount="$totals['accumulated']" /></td>
                <td class="col-num"><x-report-money :amount="$totals['book_value']" /></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <x-report-amount-words :words="amount_to_words($totals['book_value'])" />

    <x-report-signatures :items="[
        __('owner.annual_summary.signatures.prepared_by'),
        __('owner.annual_summary.signatures.reviewed_by'),
        __('owner.annual_summary.signatures.owner'),
    ]" />

    <table class="report-footer">
        <tr>
            <td>{{ $companyName }} — {{ __('owner.reports.all_rights_reserved') }} © {{ now()->year }}</td>
        </tr>
    </table>
</x-report-layout>
