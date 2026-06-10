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
</x-report-layout>
