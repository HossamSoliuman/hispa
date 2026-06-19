<x-report-layout :settings="$settings">
    <x-report-header
        :documentNumber="$boat_id ? ('#' . str_pad($boat_id, 8, '0', STR_PAD_LEFT)) : ('#' . str_pad($statistics['total_boats'], 8, '0', STR_PAD_LEFT))"
        :title="$boat_id ? __('owner.reports.boat_report') : __('owner.reports.all_boats_report')"
        :titleEn="$boat_id ? 'Boat Report' : 'All Boats Report'"
        :settings="$settings"
    />

    <x-report-info :settings="$settings">
        <x-slot:additionalInfo>
            @if ($boat_id && isset($boats) && $boats->first())
                @php $b = $boats->first(); @endphp
                <div class="info-row">
                    <div class="info-item">
                        <span class="label">{{ __('owner.boats.name') }}:</span>
                        <span class="value">{{ $b->name ?? $b->name_en }}</span>
                    </div>
                    @if ($b->captain)
                        <div class="info-item">
                            <span class="label">{{ __('owner.boats.captain') }}:</span>
                            <span class="value">{{ $b->captain->name }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </x-slot:additionalInfo>
    </x-report-info>

    <x-report-stats :items="[
        ['label' => __('owner.reports.total_boats'), 'value' => $statistics['total_boats']],
        ['label' => __('owner.reports.active_boats'), 'value' => $statistics['active_boats']],
        ['label' => __('owner.reports.total_maintenance_cost'), 'value' => number_format($statistics['total_maintenance_cost'], 2)],
        ['label' => __('owner.reports.total_payload'), 'value' => number_format($statistics['total_payload'], 2)],
    ]" />

    <x-report-table :headers="[
        '#',
        __('owner.boats.name'),
        __('owner.boats.class'),
        __('owner.boats.serial_number'),
        __('owner.boats.body_number'),
        __('owner.boats.cargo_capacity'),
        __('owner.boats.captain'),
        __('owner.boats.status'),
        __('owner.boats.next_inspection'),
        __('owner.reports.maintenance_cost'),
    ]" :data="$boats">
        @php $i = 0; @endphp
        @foreach ($boats as $boat)
            @php
                $i++;
                $boatMaintenanceCost = $boat->maintenances->sum('cost');
            @endphp
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $boat->name }}</td>
                <td>{{ $boat->boat_type->name ?? $boat->type ?? '-' }}</td>
                <td>{{ $boat->serial_number ?? '-' }}</td>
                <td>{{ $boat->body_number ?? '-' }}</td>
                <td>{{ $boat->payload ?? '-' }}</td>
                <td>
                    {{ $boat->captain->name ?? '-' }}
                    @if (!empty($boat->captain->phone))
                        <br><small>{{ $boat->captain->phone }}</small>
                    @endif
                </td>
                <td style="text-align:center;">
                    @if ($boat->status == 1)
                        <span class="badge bg-success">{{ __('owner.status.active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('owner.status.inactive') }}</span>
                    @endif
                </td>
                <td style="text-align:center;">{{ optional($boat->license_date_expire)->format('Y-m-d') ?? '-' }}</td>
                <td style="text-align:center;">{{ number_format($boatMaintenanceCost, 2) }}</td>
            </tr>
        @endforeach
    </x-report-table>

    <x-report-summary :qr-code="$qrCode">
        <x-report-summary-row
            :label="__('owner.reports.total_boats')"
            :value="$statistics['total_boats']"
        />
        <x-report-summary-row
            :label="__('owner.reports.active_boats')"
            :value="$statistics['active_boats']"
        />
        <x-report-summary-row
            :label="__('owner.reports.total_maintenance_cost')"
            :value="number_format($statistics['total_maintenance_cost'], 2)"
            :showCurrency="true"
            :highlight="true"
        />
    </x-report-summary>

</x-report-layout>
