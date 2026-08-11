@php
    $reportScope = $reportScope ?? 'owner';
    $showOwnerColumn = $showOwnerColumn ?? false;
    $isPlatformReport = $reportScope === 'platform';
@endphp

<x-report-layout :settings="$settings">
    <x-report-header
        :documentNumber="$boat_id ? ('#' . str_pad($boat_id, 8, '0', STR_PAD_LEFT)) : ('#' . str_pad($statistics['total_boats'], 8, '0', STR_PAD_LEFT))"
        :title="$isPlatformReport ? __('admin.report.boats.platform_print_title') : ($boat_id ? __('owner.reports.boat_report') : __('owner.reports.all_boats_report'))"
        :titleEn="$isPlatformReport ? 'Platform Boats Report' : ($boat_id ? 'Boat Report' : 'All Boats Report')"
        :settings="$settings"
    />

    @if ($showReportInfo ?? true)
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
    @endif

    <x-report-stats :items="[
        ['label' => __('owner.reports.total_boats'), 'value' => $statistics['total_boats']],
        ['label' => __('owner.reports.active_boats'), 'value' => $statistics['active_boats']],
        ['label' => __('owner.reports.total_maintenance_cost'), 'value' => $statistics['total_maintenance_cost'], 'money' => true],
        ['label' => __('owner.reports.total_payload'), 'value' => number_format($statistics['total_payload'], 2)],
    ]" />

    @if ($boats->isEmpty())
        <div class="alert alert-warning">
            <strong>{{ __('owner.reports.no_data_found') }}</strong>
            <p class="mb-0 text-muted">{{ __('owner.reports.try_adjust_filters') }}</p>
        </div>
    @else
        <table class="report-table block">
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    @if ($isPlatformReport && $showOwnerColumn)
                        <th class="col-text" style="width: 11%;">{{ __('admin.report.boats.owner') }}</th>
                    @endif
                    <th class="col-text" style="width: {{ $isPlatformReport && $showOwnerColumn ? 10 : 13 }}%;">{{ __('owner.boats.name') }}</th>
                    <th class="col-text" style="width: {{ $isPlatformReport && $showOwnerColumn ? 8 : 10 }}%;">{{ __('owner.boats.class') }}</th>
                    <th class="col-text" style="width: {{ $isPlatformReport && $showOwnerColumn ? 8 : 10 }}%;">{{ __('owner.boats.serial_number') }}</th>
                    <th class="col-text" style="width: {{ $isPlatformReport && $showOwnerColumn ? 8 : 10 }}%;">{{ __('owner.boats.body_number') }}</th>
                    <th class="col-num" style="width: {{ $isPlatformReport && $showOwnerColumn ? 7 : 8 }}%;">{{ __('owner.boats.cargo_capacity') }}</th>
                    <th class="col-text" style="width: {{ $isPlatformReport && $showOwnerColumn ? 11 : 13 }}%;">{{ __('owner.boats.captain') }}</th>
                    <th style="width: {{ $isPlatformReport && $showOwnerColumn ? 7 : 8 }}%;">{{ __('owner.boats.status') }}</th>
                    <th style="width: 12%;">{{ __('owner.boats.next_inspection') }}</th>
                    <th class="col-num" style="width: {{ $isPlatformReport && $showOwnerColumn ? 14 : 12 }}%;">{{ __('owner.reports.maintenance_cost') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($boats as $i => $boat)
                    @php $boatMaintenanceCost = $boat->maintenances->sum('cost'); @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        @if ($isPlatformReport && $showOwnerColumn)
                            <td class="col-text">{{ $boat->owner->name ?? '-' }}</td>
                        @endif
                        <td class="col-text">{{ $boat->name }}</td>
                        <td class="col-text">{{ $boat->boat_type->name ?? $boat->type ?? '-' }}</td>
                        <td class="col-text">{{ $boat->serial_number ?? '-' }}</td>
                        <td class="col-text">{{ $boat->body_number ?? '-' }}</td>
                        <td class="col-num">{{ $boat->payload ?? '-' }}</td>
                        <td class="col-text">
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
                        <td class="col-num"><x-report-money :amount="$boatMaintenanceCost" /></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="{{ $isPlatformReport && $showOwnerColumn ? 6 : 5 }}">
                        {{ __('owner.reports.total_boats') }}: {{ $statistics['total_boats'] }}
                    </td>
                    <td class="col-num">{{ number_format($statistics['total_payload'], 2) }}</td>
                    <td colspan="3"></td>
                    <td class="col-num"><x-report-money :amount="$statistics['total_maintenance_cost']" /></td>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($showReportSummary ?? true)
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
    @endif

</x-report-layout>
