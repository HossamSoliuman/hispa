@php
    $documentNumber = '#' . str_pad($catch->id ?? 0, 8, '0', STR_PAD_LEFT);
    $totalWeight = $catch->details->sum('weight');
@endphp

<x-report-layout
    :title="__('owner.reports.delivery_note') . ' ' . $documentNumber"
    :document-number="$documentNumber"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? null">

    <x-report-masthead
        :title="__('owner.reports.delivery_note')"
        :settings="$settings" />

    {{-- Document number + date --}}
    <table class="info-bar" style="width:60%; margin-left:auto; margin-right:auto;">
        <tr>
            <td>
                <span class="ib-label">{{ __('owner.reports.delivery_note') }}</span>
                <span class="ib-value">{{ $documentNumber }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('owner.reports.date_label') }}</span>
                <span class="ib-value">{{ \Illuminate\Support\Carbon::parse($catch->catch_date ?? $catch->created_at)->format('Y-m-d') }}</span>
            </td>
        </tr>
    </table>

    {{-- Transport details --}}
    <table class="report-table" style="margin-bottom:10px;">
        <thead><tr><th colspan="4">{{ __('owner.catch.transport_details') }}</th></tr></thead>
        <tbody>
            <tr>
                <th class="col-text" style="width:20%;">{{ __('owner.catch.car_type') }}</th>
                <td class="col-text" style="width:30%;">{{ $catch->car_type ?: '—' }}</td>
                <th class="col-text" style="width:20%;">{{ __('owner.catch.driver_name') }}</th>
                <td class="col-text" style="width:30%;">{{ $catch->driver_name ?: '—' }}</td>
            </tr>
            <tr>
                <th class="col-text">{{ __('owner.catch.car_plate_number') }}</th>
                <td class="col-text">{{ $catch->car_plate_number ?: '—' }}</td>
                @if($catch->trip?->boat)
                    <th class="col-text">{{ __('owner.catch.boat_name') }}</th>
                    <td class="col-text">{{ $catch->trip->boat->name }}</td>
                @else
                    <th class="col-text"></th>
                    <td class="col-text"></td>
                @endif
            </tr>
        </tbody>
    </table>

    {{-- Catch quantities with weight units --}}
    <table class="report-table">
        <thead>
            <tr>
                <th style="width:8%;">#</th>
                <th class="col-text" style="width:56%;">{{ __('owner.catch.fish') }}</th>
                <th style="width:20%;">{{ __('owner.catch.weight') }}</th>
                <th style="width:16%;">{{ __('owner.catch.unit') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($catch->details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="col-text">{{ $detail->fish?->scientific_name ?? ($detail->fish_name ?? '—') }}</td>
                    <td>{{ number_format($detail->weight, 2) }}</td>
                    <td>{{ $detail->unit?->name ?: __('owner.units.kg') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="color:#888;">{{ __('owner.reports.no_data_found') }}</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="col-text">{{ __('owner.catch.total') }}</th>
                <th>{{ number_format($totalWeight, 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <x-report-signatures :items="[
        __('owner.reports.sig_received_by'),
        __('owner.catch.driver_name'),
        __('owner.reports.sig_responsible_manager'),
    ]" />

    <table class="report-footer">
        <tr>
            <td>{{ $settings['title'] ?? $settings['company_name'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}</td>
        </tr>
    </table>

</x-report-layout>
