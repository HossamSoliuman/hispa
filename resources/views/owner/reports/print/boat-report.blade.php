<x-report-layout
    :title="$boat_id ? __('owner.reports.boat_report') . ' #' . ($boat_id) : __('owner.reports.all_boats_report')"
    :title-en="$boat_id ? 'Boat Report #' . ($boat_id) : 'All Boats Report'"
    :document-number="'#' . str_pad($statistics['total_boats'], 8, '0', STR_PAD_LEFT)"
    :settings="$settings"
    :qr-code="$qrCode">

    <style>
        table.report-table { width:100%; }
        table.report-table th, table.report-table td { padding:.45rem .6rem; }
        .text-center { text-align:center; }
    </style>

    <x-report-header :document-number="'#' . str_pad($statistics['total_boats'], 8, '0', STR_PAD_LEFT)" :title="$boat_id ? __('owner.reports.boat_report') : __('owner.reports.all_boats_report')" :title-en="$boat_id ? 'Boat Report' : 'All Boats Report'" :settings="$settings" />

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
        <x-slot:metadata>
            <div class="meta-item"><span class="meta-label">{{ __('owner.reports.total_boats') }}:</span> <span class="meta-value">{{ $statistics['total_boats'] }}</span></div>
            <div class="meta-item"><span class="meta-label">{{ __('owner.reports.active_boats') }}:</span> <span class="meta-value">{{ $statistics['active_boats'] }}</span></div>
            <div class="meta-item"><span class="meta-label">{{ __('owner.reports.total_maintenance_cost') }}:</span> <span class="meta-value">{{ number_format($statistics['total_maintenance_cost'], 2) }} {!! view("components.riyal-icon", ['size' => 'sm', 'style' => 'width:0.6rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.2rem;'])->render() !!}</span></div>
            <div class="meta-item"><span class="meta-label">{{ __('owner.reports.total_payload') }}:</span> <span class="meta-value">{{ number_format($statistics['total_payload'], 2) }} {{ __('owner.units.kg') }}</span></div>
        </x-slot:metadata>

        @php $i = 0; @endphp
        @foreach($boats as $boat)
            @php $i++;
                $lastMaintenance = $boat->maintenances->sortByDesc('date')->first();
                $boatMaintenanceCost = $boat->maintenances->sum('cost');
            @endphp
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $boat->name }}</td>
                <td>{{ $boat->boat_type->name ?? $boat->type ?? '-' }}</td>
                <td>{{ $boat->serial_number ?? '-' }}</td>
                <td>{{ $boat->body_number ?? '-' }}</td>
                <td>{{ $boat->payload ?? '-' }}</td>
                <td>{{ $boat->captain->name ?? '-' }}@if(!empty($boat->captain->phone))<br/><small>{{ $boat->captain->phone }}</small>@endif</td>
                <td class="text-center">@if($boat->status==1)<span class="badge bg-success">{{ __('owner.status.active') }}</span>@else<span class="badge bg-danger">{{ __('owner.status.inactive') }}</span>@endif</td>
                <td class="text-center">{{ optional($boat->license_date_expire)->format('Y-m-d') ?? '-' }}</td>
                <td class="text-center">{{ number_format($boatMaintenanceCost, 2) }} {!! view("components.riyal-icon", ['size' => 'sm', 'style' => 'width:0.6rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.2rem;'])->render() !!}</td>
            </tr>
        @endforeach

    </x-report-table>

    <div class="qr-box" style="text-align:center; margin:18px 0;">
        @if(!empty($qrCode))
            <img src="{{ $qrCode }}" alt="QR Code" style="width:110px; height:110px;" />
        @endif
    </div>

</x-report-layout>
