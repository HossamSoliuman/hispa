<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings"
        :title="__('owner.fish_history_report.title')"
    />

    <x-report-info :settings="$settings" :from-date="$from" :to-date="$to">
        <x-slot:additionalInfo>
            <div class="info-row">
                <div class="info-item">
                    <span class="label">{{ __('owner.fish_history_report.from_date') }}:</span>
                    <span class="value">{{ $from ? \Alkoumi\LaravelHijriDate\Hijri::Date('d F Y', $from) : __('owner.stock_report.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('owner.fish_history_report.to_date') }}:</span>
                    <span class="value">{{ $to ? \Alkoumi\LaravelHijriDate\Hijri::Date('d F Y', $to) : __('owner.stock_report.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('owner.fish_history_report.fish_type') }}:</span>
                    <span class="value">{{ $fishName ?? __('owner.fish_history_report.all') }}</span>
                </div>
            </div>
        </x-slot:additionalInfo>
    </x-report-info>

    <x-report-stats :items="[
        ['label' => __('owner.stock_report.total_fish_types'), 'value' => $totalFishTypes ?? 0],
        ['label' => __('owner.stock_report.total_weight'), 'value' => number_format($totalWeight ?? 0, 2) . ' ' . __('owner.stock_report.kg')],
        ['label' => __('owner.stock_report.total_records'), 'value' => $totalRecords ?? ($records ? count($records) : 0)],
        ['label' => __('owner.reports.total_catch'), 'value' => number_format($totalCatch ?? 0, 2)],
    ]" />

    <x-report-table>
        <thead>
            <tr>
                <th>{{ __('owner.fish_history_report.table.index') }}</th>
                <th>{{ __('owner.fish_history_report.table.date') }}</th>
                <th>{{ __('owner.fish_history_report.table.item') }}</th>
                <th>{{ __('owner.fish_history_report.table.operation') }}</th>
                <th>{{ __('owner.fish_history_report.table.weight') }}</th>
                <th>{{ __('owner.fish_history_report.table.remaining_balance') }}</th>
                <th>{{ __('owner.fish_history_report.table.user') }}</th>
                <th>{{ __('owner.fish_history_report.table.notes') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $r)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $r->created_at ? \Alkoumi\LaravelHijriDate\Hijri::Date('d/m/Y', $r->created_at) : '---' }}</td>
                    <td>{{ $r->fish_name ?? ($r->name ?? '---') }}</td>
                    <td>{{ $r->operation_type ?? '---' }}</td>
                    <td>{{ number_format($r->changed_weight ?? 0, 2) }} {{ __('owner.stock_report.kg') }}</td>
                    <td>{{ number_format($r->remaining_weight ?? 0, 2) }} {{ __('owner.stock_report.kg') }}</td>
                    <td>{{ $r->user_name ?? ($r->added_by ?? '---') }}</td>
                    <td>{{ $r->notes ?? '---' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">{{ __('owner.fish_history_report.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-report-table>

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
</x-report-layout>
