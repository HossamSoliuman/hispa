<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings ?? []"
        :title="__('owner.analysis_reports.production_species.title')"
        titleEn="Production by Species Report"
    />

    <x-report-info :settings="$settings ?? []">
        <x-slot:additionalInfo>
            <div class="period-info" style="background:#f1f5f9;padding:12px;border-radius:6px;margin:15px 0;">
                <strong>{{ __('owner.analysis_reports.from_date') }}:</strong> {{ $from }}
                <strong style="margin-inline-start:20px;">{{ __('owner.analysis_reports.to_date') }}:</strong> {{ $to }}
            </div>
        </x-slot:additionalInfo>
    </x-report-info>

    @php
        $totalCaughtWeight = array_sum(array_column($rows, 'caught_weight'));
        $totalCaughtValue  = array_sum(array_column($rows, 'caught_value'));
        $totalSoldWeight   = array_sum(array_column($rows, 'sold_weight'));
        $totalSoldValue    = array_sum(array_column($rows, 'sold_value'));
    @endphp

    <x-report-stats :items="[
        ['label' => __('owner.analysis_reports.production_species.species_count'), 'value' => count($rows)],
        ['label' => __('owner.analysis_reports.production_species.caught_weight'), 'value' => number_format($totalCaughtWeight, 2)],
        ['label' => __('owner.analysis_reports.production_species.sold_weight'), 'value' => number_format($totalSoldWeight, 2)],
        ['label' => __('owner.analysis_reports.production_species.sold_value'), 'value' => number_format($totalSoldValue, 2)],
    ]" />

    <x-report-table :headers="[
        __('owner.analysis_reports.production_species.fish'),
        __('owner.analysis_reports.production_species.caught_weight'),
        __('owner.analysis_reports.production_species.caught_value'),
        __('owner.analysis_reports.production_species.sold_weight'),
        __('owner.analysis_reports.production_species.sold_value'),
    ]" :data="$rows">
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row['fish_name'] }}</td>
                <td style="text-align:end;">{{ number_format($row['caught_weight'], 2) }}</td>
                <td style="text-align:end;">{{ number_format($row['caught_value'], 2) }}</td>
                <td style="text-align:end;">{{ number_format($row['sold_weight'], 2) }}</td>
                <td style="text-align:end;font-weight:700;">{{ number_format($row['sold_value'], 2) }}</td>
            </tr>
        @endforeach
    </x-report-table>

    <x-report-summary :qr-code="$settings['qr_code'] ?? null">
        <x-report-summary-row
            :label="__('owner.analysis_reports.production_species.caught_weight')"
            :value="number_format($totalCaughtWeight, 2)"
        />
        <x-report-summary-row
            :label="__('owner.analysis_reports.production_species.caught_value')"
            :value="number_format($totalCaughtValue, 2)"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.analysis_reports.production_species.sold_weight')"
            :value="number_format($totalSoldWeight, 2)"
        />
        <x-report-summary-row
            :label="__('owner.analysis_reports.production_species.sold_value')"
            :value="number_format($totalSoldValue, 2)"
            :showCurrency="true"
            :highlight="true"
        />
    </x-report-summary>
</x-report-layout>
