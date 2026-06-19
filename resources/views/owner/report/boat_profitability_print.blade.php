<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings ?? []"
        :title="__('owner.analysis_reports.boat_profitability.title')"
        titleEn="Boat Profitability Report"
    />

    <x-report-info :settings="$settings ?? []">
        <x-slot:additionalInfo>
            <div class="period-info" style="background:#f1f5f9;padding:12px;border-radius:6px;margin:15px 0;">
                <strong>{{ __('owner.analysis_reports.from_date') }}:</strong> {{ $from }}
                <strong style="margin-inline-start:20px;">{{ __('owner.analysis_reports.to_date') }}:</strong> {{ $to }}
            </div>
        </x-slot:additionalInfo>
    </x-report-info>

    <x-report-stats :items="[
        ['label' => __('owner.analysis_reports.boat_profitability.boats_count'), 'value' => count($rows)],
        ['label' => __('owner.analysis_reports.gross_sales'), 'value' => number_format($totals['gross_sales'], 2)],
        ['label' => __('owner.analysis_reports.expenses'), 'value' => number_format($totals['expenses'], 2)],
        ['label' => __('owner.analysis_reports.net_profit'), 'value' => number_format($totals['net_profit'], 2), 'color' => $totals['net_profit'] >= 0 ? '#16a34a' : '#dc2626'],
    ]" />

    <x-report-table :headers="[
        __('owner.analysis_reports.boat_profitability.boat'),
        __('owner.analysis_reports.gross_sales'),
        __('owner.analysis_reports.net_sales'),
        __('owner.analysis_reports.expenses'),
        __('owner.analysis_reports.net_profit'),
        __('owner.analysis_reports.margin'),
    ]" :data="$rows">
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row['boat_name'] }}</td>
                <td style="text-align:end;">{{ number_format($row['gross_sales'], 2) }}</td>
                <td style="text-align:end;">{{ number_format($row['net_sales'], 2) }}</td>
                <td style="text-align:end;">{{ number_format($row['expenses'], 2) }}</td>
                <td style="text-align:end;font-weight:700;color:{{ $row['net_profit'] >= 0 ? '#16a34a' : '#dc2626' }};">{{ number_format($row['net_profit'], 2) }}</td>
                <td style="text-align:end;">{{ number_format($row['margin'], 1) }}%</td>
            </tr>
        @endforeach
    </x-report-table>

    <x-report-summary :qr-code="$settings['qr_code'] ?? null">
        <x-report-summary-row
            :label="__('owner.analysis_reports.gross_sales')"
            :value="number_format($totals['gross_sales'], 2)"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.analysis_reports.net_sales')"
            :value="number_format($totals['net_sales'], 2)"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.analysis_reports.expenses')"
            :value="number_format($totals['expenses'], 2)"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.analysis_reports.net_profit')"
            :value="number_format($totals['net_profit'], 2)"
            :showCurrency="true"
            :highlight="true"
        />
    </x-report-summary>
</x-report-layout>
