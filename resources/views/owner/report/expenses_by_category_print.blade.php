<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings ?? []"
        :title="__('owner.analysis_reports.expenses_by_category.title')"
        titleEn="Trip Expenses by Category"
    />

    <x-report-info :settings="$settings ?? []">
        <x-slot:additionalInfo>
            <div class="period-info" style="background:#f1f5f9;padding:12px;border-radius:6px;margin:15px 0;">
                <strong>{{ __('owner.analysis_reports.from_date') }}:</strong> {{ $from }}
                <strong style="margin-inline-start:20px;">{{ __('owner.analysis_reports.to_date') }}:</strong> {{ $to }}
                @if ($boatId && isset($boats))
                    @php $selectedBoat = $boats->firstWhere('id', $boatId); @endphp
                    @if ($selectedBoat)
                        <br><strong>{{ __('owner.analysis_reports.boat') ?? 'القارب' }}:</strong> {{ $selectedBoat->name ?? $selectedBoat->name_ar }}
                    @endif
                @endif
            </div>
        </x-slot:additionalInfo>
    </x-report-info>

    <x-report-stats :items="[
        ['label' => __('owner.analysis_reports.expenses_by_category.categories_count'), 'value' => count($rows)],
        ['label' => __('owner.analysis_reports.expenses_by_category.total_count'), 'value' => array_sum(array_column($rows, 'count'))],
        ['label' => __('owner.analysis_reports.expenses_by_category.total_amount'), 'value' => number_format($total, 2)],
    ]" />

    <x-report-table :headers="[
        __('owner.analysis_reports.expenses_by_category.category'),
        __('owner.analysis_reports.expenses_by_category.type'),
        __('owner.analysis_reports.expenses_by_category.count'),
        __('owner.analysis_reports.expenses_by_category.amount'),
    ]" :data="$rows">
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row['category'] }}</td>
                <td>{{ $row['type'] ? __('owner.analysis_reports.expenses_by_category.type_'.$row['type']) : '—' }}</td>
                <td style="text-align:end;">{{ number_format($row['count']) }}</td>
                <td style="text-align:end;font-weight:700;">{{ number_format($row['amount'], 2) }}</td>
            </tr>
        @endforeach
    </x-report-table>

    <x-report-summary :qr-code="$settings['qr_code'] ?? null">
        <x-report-summary-row
            :label="__('owner.analysis_reports.expenses_by_category.categories_count')"
            :value="count($rows)"
        />
        <x-report-summary-row
            :label="__('owner.analysis_reports.expenses_by_category.total_count')"
            :value="array_sum(array_column($rows, 'count'))"
        />
        <x-report-summary-row
            :label="__('owner.analysis_reports.totals')"
            :value="number_format($total, 2)"
            :showCurrency="true"
            :highlight="true"
        />
    </x-report-summary>
</x-report-layout>
