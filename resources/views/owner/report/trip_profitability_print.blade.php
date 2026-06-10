<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings ?? []"
        :title="__('owner.analysis_reports.trip_profitability.title')"
        titleEn="Trip Profitability Report"
    />

    <x-report-info :settings="$settings ?? []">
        <x-slot:additionalInfo>
            <div class="period-info" style="background:#f1f5f9;padding:12px;border-radius:6px;margin:15px 0;">
                <strong>{{ __('owner.analysis_reports.from_date') }}:</strong> {{ $from }}
                <strong style="margin-inline-start:20px;">{{ __('owner.analysis_reports.to_date') }}:</strong> {{ $to }}
            </div>
        </x-slot:additionalInfo>
    </x-report-info>

    <x-report-table :headers="[
        __('owner.analysis_reports.trip_profitability.trip_number'),
        __('owner.analysis_reports.trip_profitability.boat'),
        __('owner.analysis_reports.trip_profitability.captain'),
        __('owner.analysis_reports.trip_profitability.start_date'),
        __('owner.analysis_reports.net_sales'),
        __('owner.analysis_reports.expenses'),
        __('owner.analysis_reports.net_profit'),
        __('owner.analysis_reports.margin'),
    ]" :data="$rows">
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row['number'] }}</td>
                <td>{{ $row['boat_name'] }}</td>
                <td>{{ $row['captain_name'] }}</td>
                <td>{{ $row['start_date'] }}</td>
                <td style="text-align:end;">{{ number_format($row['net_sales'], 2) }}</td>
                <td style="text-align:end;">{{ number_format($row['expenses'], 2) }}</td>
                <td style="text-align:end;font-weight:700;">{{ number_format($row['net_profit'], 2) }}</td>
                <td style="text-align:end;">{{ number_format($row['margin'], 1) }}%</td>
            </tr>
        @endforeach
        @if (count($rows))
            <tr style="font-weight:700;background:#f8f9fa;">
                <td colspan="4">{{ __('owner.analysis_reports.totals') }}</td>
                <td style="text-align:end;">{{ number_format($totals['net_sales'], 2) }}</td>
                <td style="text-align:end;">{{ number_format($totals['expenses'], 2) }}</td>
                <td style="text-align:end;">{{ number_format($totals['net_profit'], 2) }}</td>
                <td></td>
            </tr>
        @endif
    </x-report-table>

    <div style="margin-top:15px;"><small style="color:#64748b;">{{ __('owner.analysis_reports.trip_profitability.footer_note') }}</small></div>
</x-report-layout>
