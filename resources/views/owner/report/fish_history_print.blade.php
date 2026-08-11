@php
    /** @var 'owner'|'platform' $reportScope */
    $reportScope = $reportScope ?? 'owner';

    /** @var bool $showOwnerColumn */
    $showOwnerColumn = $showOwnerColumn ?? false;
@endphp

<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings"
        :title="$reportScope === 'platform' ? __('admin.report.fish_history.platform_print_title') : __('owner.fish_history_report.title')"
    />

    @if ($showReportInfo ?? true)
    <x-report-info :settings="$settings" :from-date="$from" :to-date="$to">
        <x-slot:additionalInfo>
            <div class="info-row">
                <div class="info-item">
                    <span class="label">{{ __('owner.fish_history_report.from_date') }}:</span>
                    <span class="value">{{ $from ? formatHijriDate($from, 'd MMM yyyy') : __('owner.stock_report.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('owner.fish_history_report.to_date') }}:</span>
                    <span class="value">{{ $to ? formatHijriDate($to, 'd MMM yyyy') : __('owner.stock_report.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('owner.fish_history_report.fish_type') }}:</span>
                    <span class="value">{{ $fishName ?? __('owner.fish_history_report.all') }}</span>
                </div>
            </div>
        </x-slot:additionalInfo>
    </x-report-info>
    @endif

    <x-report-stats :items="[
        ['label' => __('owner.stock_report.total_fish_types'), 'value' => $totalFishTypes ?? 0],
        ['label' => __('owner.stock_report.total_weight'), 'value' => number_format($totalWeight ?? 0, 2) . ' ' . __('owner.stock_report.kg')],
        ['label' => __('owner.stock_report.total_records'), 'value' => $totalRecords ?? ($records ? count($records) : 0)],
        ['label' => __('owner.reports.total_catch'), 'value' => number_format($totalCatch ?? 0, 2)],
    ]" />

    @if ($reportScope === 'platform' && $showOwnerColumn)
        @if($records->isEmpty())
            <div class="alert alert-warning">
                <strong>{{ __('owner.reports.no_data_found') }}</strong>
                <p class="mb-0 text-muted">{{ __('owner.reports.try_adjust_filters') }}</p>
            </div>
        @else
            <table class="report-table block">
                <thead>
                    <tr>
                        <th style="width: 4%;">{{ __('owner.fish_history_report.table.index') }}</th>
                        <th class="col-text" style="width: 10%;">{{ __('owner.fish_history_report.table.date') }}</th>
                        <th class="col-text" style="width: 12%;">{{ __('owner.fish_history_report.table.item') }}</th>
                        <th class="col-text" style="width: 11%;">{{ __('owner.fish_history_report.table.operation') }}</th>
                        <th class="col-num" style="width: 11%;">{{ __('owner.fish_history_report.table.weight') }}</th>
                        <th class="col-num" style="width: 13%;">{{ __('owner.fish_history_report.table.remaining_balance') }}</th>
                        <th class="col-text" style="width: 11%;">{{ __('owner.fish_history_report.table.user') }}</th>
                        <th class="col-text" style="width: 14%;">{{ __('admin.report.fish_history.owner') }}</th>
                        <th class="col-text" style="width: 14%;">{{ __('owner.fish_history_report.table.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $index => $r)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="col-text">{{ $r->created_at ? formatHijriDate($r->created_at) : '---' }}</td>
                            <td class="col-text">{{ $r->fish_name ?? ($r->name ?? '---') }}</td>
                            <td class="col-text">{{ $r->operation_type ?? '---' }}</td>
                            <td class="col-num">{{ number_format($r->changed_weight ?? 0, 2) }} {{ __('owner.stock_report.kg') }}</td>
                            <td class="col-num">{{ number_format($r->remaining_weight ?? 0, 2) }} {{ __('owner.stock_report.kg') }}</td>
                            <td class="col-text">{{ $r->user_name ?? ($r->added_by ?? '---') }}</td>
                            <td class="col-text">{{ $r->owner_name ?? '---' }}</td>
                            <td class="col-text">{{ $r->notes ?? '---' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">{{ __('owner.sales.total') }}</td>
                        <td class="col-num">{{ number_format($totalCatch ?? 0, 2) }} {{ __('owner.stock_report.kg') }}</td>
                        <td class="col-num">{{ number_format($totalWeight ?? 0, 2) }} {{ __('owner.stock_report.kg') }}</td>
                        <td class="col-text" colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        @endif
    @else
        <x-report-table :headers="[
            __('owner.fish_history_report.table.index'),
            __('owner.fish_history_report.table.date'),
            __('owner.fish_history_report.table.item'),
            __('owner.fish_history_report.table.operation'),
            __('owner.fish_history_report.table.weight'),
            __('owner.fish_history_report.table.remaining_balance'),
            __('owner.fish_history_report.table.user'),
            __('owner.fish_history_report.table.notes'),
        ]" :data="$records">
            @foreach($records as $index => $r)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $r->created_at ? formatHijriDate($r->created_at) : '---' }}</td>
                    <td>{{ $r->fish_name ?? ($r->name ?? '---') }}</td>
                    <td>{{ $r->operation_type ?? '---' }}</td>
                    <td>{{ number_format($r->changed_weight ?? 0, 2) }} {{ __('owner.stock_report.kg') }}</td>
                    <td>{{ number_format($r->remaining_weight ?? 0, 2) }} {{ __('owner.stock_report.kg') }}</td>
                    <td>{{ $r->user_name ?? ($r->added_by ?? '---') }}</td>
                    <td>{{ $r->notes ?? '---' }}</td>
                </tr>
            @endforeach
        </x-report-table>
    @endif

    @if ($showReportSummary ?? true)
    <x-report-summary :qr-code="$settings['qr_code'] ?? null">
        <x-report-summary-row
            :label="__('owner.stock_report.total_fish_types')"
            :value="$totalFishTypes ?? 0"
        />
        <x-report-summary-row
            :label="__('owner.stock_report.total_weight')"
            :value="number_format($totalWeight ?? 0, 2) . ' ' . __('owner.stock_report.kg')"
            :highlight="true"
        />
        <x-report-summary-row
            :label="__('owner.stock_report.total_records')"
            :value="$totalRecords ?? ($records ? count($records) : 0)"
        />
    </x-report-summary>
    @endif
</x-report-layout>
