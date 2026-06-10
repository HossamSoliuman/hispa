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
            </div>
        </x-slot:additionalInfo>
    </x-report-info>

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
        @if (count($rows))
            <tr style="font-weight:700;background:#f8f9fa;">
                <td colspan="3">{{ __('owner.analysis_reports.totals') }}</td>
                <td style="text-align:end;">{{ number_format($total, 2) }}</td>
            </tr>
        @endif
    </x-report-table>
</x-report-layout>
