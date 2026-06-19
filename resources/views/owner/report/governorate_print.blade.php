<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings"
        :title="__('owner.list_reports.governorate_title')"
    />

    <x-report-info :settings="$settings" />

    <x-report-table>
        <thead>
            <tr>
                <th>{{ __('owner.list_reports.serial') }}</th>
                <th>{{ __('owner.list_reports.name_ar') }}</th>
                <th>{{ __('owner.list_reports.name_en') }}</th>
                <th>{{ __('owner.list_reports.region') }}</th>
                <th>{{ __('owner.list_reports.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($governorates as $index => $governorate)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $governorate->name_ar ?? '---' }}</td>
                    <td>{{ $governorate->name_en ?? '---' }}</td>
                    <td>{{ optional($governorate->region)->name ?? '---' }}</td>
                    <td>
                        <span class="badge {{ $governorate->status ? 'bg-success' : 'bg-secondary' }}">
                            {{ $governorate->status ? __('owner.list_reports.active') : __('owner.list_reports.inactive') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">{{ __('owner.list_reports.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-report-table>

    <x-report-summary :qr-code="$settings['qr_code'] ?? null">
        <x-report-summary-row
            :label="__('owner.list_reports.total')"
            :value="$total"
        />
        <x-report-summary-row
            :label="__('owner.list_reports.total_active')"
            :value="$active"
            :highlight="true"
        />
    </x-report-summary>
</x-report-layout>
