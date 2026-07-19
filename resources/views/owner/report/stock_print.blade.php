<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings"
        :title="__('owner.stock_report.print_title')"
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

    <x-report-table :headers="[
        '#',
        __('owner.stock_report.fish_name'),
        __('owner.stock_report.captain_weight'),
        __('owner.stock_report.counter_weight'),
        __('owner.stock_report.total_weight'),
        __('owner.stock_report.difference'),
        __('owner.stock_report.added_by'),
        __('owner.stock_report.corrected_by'),
        __('owner.stock_report.date'),
    ]" :data="$stocks">
        @foreach($stocks as $index => $stock)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $stock->name }}</td>
                <td>{{ number_format($stock->weight_captain ?? 0, 2) }} {{ $stock->unit_display ?? __('owner.stock_report.kg') }}</td>
                <td>{{ $stock->weight_counter === null ? '---' : number_format($stock->weight_counter, 2) . ' ' . ($stock->unit_display ?? __('owner.stock_report.kg')) }}</td>
                <td>{{ number_format($stock->total_weight ?? 0, 2) }} {{ $stock->unit_display ?? __('owner.stock_report.kg') }}</td>
                <td>{{ $stock->weight_difference === null ? '---' : number_format($stock->weight_difference, 2) . ' ' . ($stock->unit_display ?? __('owner.stock_report.kg')) }}</td>
                <td>{{ $stock->added_by ?? '---' }}</td>
                <td>{{ $stock->correct_by ?? '---' }}</td>
                <td>{{ $stock->date ? (class_exists('\\Alkoumi\\LaravelHijriDate\\Hijri') ? \Alkoumi\LaravelHijriDate\Hijri::Date('d/m/Y', $stock->date) : \Carbon\Carbon::parse($stock->date)->format('d/m/Y')) : '---' }}</td>
            </tr>
        @endforeach
    </x-report-table>

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
