<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings"
        :title="__('owner.dalal_stock_report.print_title')"
    />

    <x-report-info :settings="$settings" :from-date="$from" :to-date="$to">
        <x-slot:additionalInfo>
            <div class="info-row">
                <div class="info-item">
                    <span class="label">{{ __('owner.dalal_stock_report.from_date') }}:</span>
                    <span class="value">{{ $from ? \Alkoumi\LaravelHijriDate\Hijri::Date('d F Y', $from) : __('owner.dalal_stock_report.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('owner.dalal_stock_report.to_date') }}:</span>
                    <span class="value">{{ $to ? \Alkoumi\LaravelHijriDate\Hijri::Date('d F Y', $to) : __('owner.dalal_stock_report.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('owner.dalal_stock_report.dalal') }}:</span>
                    <span class="value">{{ $dalalName ?? __('owner.dalal_stock_report.all_dalals') }}</span>
                </div>
            </div>
        </x-slot:additionalInfo>
    </x-report-info>

    <x-report-stats :items="[
        ['label' => __('owner.dalal_stock_report.total_fish_types'), 'value' => $totalFishCount],
        ['label' => __('owner.dalal_stock_report.total_weight'), 'value' => number_format($totalWeight, 2) . ' ' . __('owner.dalal_stock_report.kg')],
        ['label' => __('owner.dalal_stock_report.total_dalals'), 'value' => $totalDalalCount],
    ]" />

    <x-report-table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('owner.dalal_stock_report.dalal_name') }}</th>
                <th>{{ __('owner.dalal_stock_report.fish_name') }}</th>
                <th>{{ __('owner.dalal_stock_report.total_weight') }}</th>
                <th>{{ __('owner.dalal_stock_report.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $index => $stock)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $stock->dalal_name ?? '---' }}</td>
                <td>{{ $stock->fish_name ?? '---' }}</td>
                <td>{{ number_format($stock->total_weight ?? 0, 2) }} {{ __('owner.dalal_stock_report.kg') }}</td>
                <td>{{ $stock->date ? \Alkoumi\LaravelHijriDate\Hijri::Date('d/m/Y', $stock->date) : '---' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">{{ __('owner.dalal_stock_report.no_data') }}</td>
            </tr>
            @endforelse
        </tbody>
    </x-report-table>

    <x-report-summary :qr-code="$settings['qr_code'] ?? null">
        <x-report-summary-row
            :label="__('owner.dalal_stock_report.total_fish_types')"
            :value="$totalFishCount"
        />
        <x-report-summary-row
            :label="__('owner.dalal_stock_report.total_dalals')"
            :value="$totalDalalCount"
        />
        <x-report-summary-row
            :label="__('owner.dalal_stock_report.total_weight')"
            :value="number_format($totalWeight, 2) . ' ' . __('owner.dalal_stock_report.kg')"
            :highlight="true"
        />
    </x-report-summary>
</x-report-layout>
