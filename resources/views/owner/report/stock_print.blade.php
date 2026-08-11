@php
    /** @var 'owner'|'platform' $reportScope */
    $reportScope = $reportScope ?? 'owner';

    /** @var bool $showOwnerColumn */
    $showOwnerColumn = $showOwnerColumn ?? false;

    $isPlatformReport = $reportScope === 'platform' && $showOwnerColumn;
    $captainWeightTotal = formatWeightByUnit($stocks->map(fn (object $stock): object => (object) [
        'weight' => $stock->weight_captain ?? 0,
        'unit' => $stock->unit,
    ]));
    $counterWeightTotal = formatWeightByUnit($stocks
        ->filter(fn (object $stock): bool => $stock->weight_counter !== null)
        ->map(fn (object $stock): object => (object) [
            'weight' => $stock->weight_counter,
            'unit' => $stock->unit,
        ]));
    $stockWeightTotal = formatWeightByUnit($stocks->map(fn (object $stock): object => (object) [
        'weight' => $stock->total_weight ?? 0,
        'unit' => $stock->unit,
    ]));
    $differenceWeightTotal = formatWeightByUnit($stocks
        ->filter(fn (object $stock): bool => $stock->weight_difference !== null)
        ->map(fn (object $stock): object => (object) [
            'weight' => $stock->weight_difference,
            'unit' => $stock->unit,
        ]));
@endphp
<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings"
        :title="$reportScope === 'platform' ? __('admin.report.stock.platform_print_title') : __('owner.stock_report.print_title')"
    />

    @if ($showReportInfo ?? true)
    <x-report-info :settings="$settings" :from-date="$from" :to-date="$to">
        <x-slot:additionalInfo>
            <div class="info-row">
                <div class="info-item">
                    <span class="label">{{ __('owner.stock_report.from_date') }}:</span>
                    <span class="value">{{ $from ? (class_exists('\\Alkoumi\\LaravelHijriDate\\Hijri') ? \Alkoumi\LaravelHijriDate\Hijri::Date('d F Y', $from) : \Carbon\Carbon::parse($from)->format('d F Y')) : __('owner.stock_report.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('owner.stock_report.to_date') }}:</span>
                    <span class="value">{{ $to ? (class_exists('\\Alkoumi\\LaravelHijriDate\\Hijri') ? \Alkoumi\LaravelHijriDate\Hijri::Date('d F Y', $to) : \Carbon\Carbon::parse($to)->format('d F Y')) : __('owner.stock_report.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('owner.stock_report.fish_type') }}:</span>
                    <span class="value">{{ $fishName ?? __('owner.stock_report.all_fish') }}</span>
                </div>
            </div>
        </x-slot:additionalInfo>
    </x-report-info>
    @endif

    <x-report-stats :items="[
        ['label' => __('owner.stock_report.total_fish_types'), 'value' => $totalFishCount],
        ['label' => __('owner.stock_report.total_weight'), 'value' => $totalWeight],
    ]" />

    @if($stocks->isEmpty())
        <div class="alert alert-warning">
            <strong>{{ __('owner.reports.no_data_found') }}</strong>
            <p class="mb-0 text-muted">{{ __('owner.reports.try_adjust_filters') }}</p>
        </div>
    @else
        <table class="report-table block">
            <thead>
                <tr>
                    <th style="width:4%;">#</th>
                    <th class="col-text" style="width:{{ $isPlatformReport ? '12%' : '14%' }};">{{ __('owner.stock_report.fish_name') }}</th>
                    @if($isPlatformReport)
                        <th class="col-text" style="width:12%;">{{ __('admin.report.stock.owner') }}</th>
                    @endif
                    <th class="col-num" style="width:{{ $isPlatformReport ? '10%' : '11%' }};">{{ __('owner.stock_report.captain_weight') }}</th>
                    <th class="col-num" style="width:{{ $isPlatformReport ? '10%' : '11%' }};">{{ __('owner.stock_report.counter_weight') }}</th>
                    <th class="col-num" style="width:{{ $isPlatformReport ? '10%' : '11%' }};">{{ __('owner.stock_report.total_weight') }}</th>
                    <th class="col-num" style="width:{{ $isPlatformReport ? '10%' : '11%' }};">{{ __('owner.stock_report.difference') }}</th>
                    <th class="col-text" style="width:{{ $isPlatformReport ? '10%' : '11%' }};">{{ __('owner.stock_report.added_by') }}</th>
                    <th class="col-text" style="width:{{ $isPlatformReport ? '10%' : '11%' }};">{{ __('owner.stock_report.corrected_by') }}</th>
                    <th class="col-text" style="width:{{ $isPlatformReport ? '12%' : '16%' }};">{{ __('owner.stock_report.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $index => $stock)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="col-text">{{ $stock->name }}</td>
                        @if($isPlatformReport)
                            <td class="col-text">{{ $stock->owner_name ?? '---' }}</td>
                        @endif
                        <td class="col-num">{{ number_format($stock->weight_captain ?? 0, 2) }} {{ $stock->unit_display ?? __('owner.stock_report.kg') }}</td>
                        <td class="col-num">{{ $stock->weight_counter === null ? '---' : number_format($stock->weight_counter, 2) . ' ' . ($stock->unit_display ?? __('owner.stock_report.kg')) }}</td>
                        <td class="col-num">{{ number_format($stock->total_weight ?? 0, 2) }} {{ $stock->unit_display ?? __('owner.stock_report.kg') }}</td>
                        <td class="col-num">{{ $stock->weight_difference === null ? '---' : number_format($stock->weight_difference, 2) . ' ' . ($stock->unit_display ?? __('owner.stock_report.kg')) }}</td>
                        <td class="col-text">{{ $stock->added_by ?? '---' }}</td>
                        <td class="col-text">{{ $stock->correct_by ?? '---' }}</td>
                        <td class="col-text">{{ $stock->date ? (class_exists('\\Alkoumi\\LaravelHijriDate\\Hijri') ? \Alkoumi\LaravelHijriDate\Hijri::Date('d/m/Y', $stock->date) : \Carbon\Carbon::parse($stock->date)->format('d/m/Y')) : '---' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="col-text" colspan="{{ $isPlatformReport ? 3 : 2 }}">{{ __('owner.sales.total') }}</td>
                    <td class="col-num">{{ $captainWeightTotal }}</td>
                    <td class="col-num">{{ $counterWeightTotal }}</td>
                    <td class="col-num">{{ $stockWeightTotal }}</td>
                    <td class="col-num">{{ $differenceWeightTotal }}</td>
                    <td class="col-text" colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($showReportSummary ?? true)
    <x-report-summary :qr-code="$settings['qr_code'] ?? null">
        <x-report-summary-row
            :label="__('owner.stock_report.total_fish_types')"
            :value="$totalFishCount"
        />
        <x-report-summary-row
            :label="__('owner.stock_report.total_weight')"
            :value="$totalWeight"
            :highlight="true"
        />
    </x-report-summary>
    @endif
</x-report-layout>
