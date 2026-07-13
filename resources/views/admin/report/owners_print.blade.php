<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings"
        :title="__('admin.report.owners.print_title')"
    />

    <x-report-stats :items="[
        ['label' => __('admin.report.owners.kpi.total_owners'), 'value' => $totalOwners],
        ['label' => __('admin.report.owners.kpi.active_owners'), 'value' => $activeOwners, 'color' => '#16a085'],
        ['label' => __('admin.report.owners.kpi.total_boats'), 'value' => $totalBoats],
        ['label' => __('admin.report.owners.kpi.total_quota'), 'value' => $totalQuota],
    ]" />

    @php
        $statusColors = [
            'active' => 'success',
            'pending' => 'warning',
            'trial' => 'info',
            'expired' => 'danger',
            'suspended' => 'secondary',
        ];
    @endphp

    <x-report-table :headers="[
        '#',
        __('admin.report.owners.owner'),
        __('admin.report.owners.phone'),
        __('admin.report.owners.plan'),
        __('admin.report.owners.status'),
        __('admin.report.owners.boats_quota'),
        __('admin.report.owners.start_date'),
        __('admin.report.owners.end_date'),
    ]" :data="$rows">
        @foreach ($rows as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->name ?? '---' }}</td>
                <td>{{ $row->phone ?? '---' }}</td>
                <td>{{ $row->plan_name ?? '---' }}</td>
                <td style="text-align:center;">
                    @if ($row->status)
                        <span class="badge bg-{{ $statusColors[$row->status] ?? 'secondary' }}">{{ __('admin.report.owners.status_labels.' . $row->status) }}</span>
                    @else
                        <span class="badge bg-secondary">{{ __('admin.report.owners.status_labels.none') }}</span>
                    @endif
                </td>
                <td style="text-align:center;">{{ $row->boats_used }} / {{ $row->quota }}</td>
                <td style="text-align:center;">{{ $row->start_date ? \Illuminate\Support\Carbon::parse($row->start_date)->format('Y-m-d') : '---' }}</td>
                <td style="text-align:center;">{{ $row->end_date ? \Illuminate\Support\Carbon::parse($row->end_date)->format('Y-m-d') : '---' }}</td>
            </tr>
        @endforeach
    </x-report-table>
</x-report-layout>
