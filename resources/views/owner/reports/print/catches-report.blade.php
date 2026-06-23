<x-report-layout
    :title="__('owner.reports.all_catchs_report')"
    title-en="All Catches Report"
    :document-number="'#' . str_pad($statistics['total_trips'], 8, '0', STR_PAD_LEFT)"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? null">


    <x-report-header
        :document-number="'#' . str_pad($statistics['total_trips'], 8, '0', STR_PAD_LEFT)"
        :title="__('owner.reports.all_catchs_report')"
        title-en="All Catches Report"
        :settings="$settings" />

    {{-- Applied filters / key facts strip --}}
    <table class="info-bar">
        <tr>
            @if($filters['from_date'])
                <td><span class="ib-label">{{ __('owner.reports.from_date') }}</span><span class="ib-value">{{ $filters['from_date'] }}</span></td>
            @endif
            @if($filters['to_date'])
                <td><span class="ib-label">{{ __('owner.reports.to_date') }}</span><span class="ib-value">{{ $filters['to_date'] }}</span></td>
            @endif
            <td><span class="ib-label">{{ __('owner.reports.total_trips') }}</span><span class="ib-value">{{ $statistics['total_trips'] }}</span></td>
            <td><span class="ib-label">{{ __('owner.reports.trips_with_catch') }}</span><span class="ib-value">{{ $statistics['trips_with_catch'] }}</span></td>
        </tr>
    </table>

    <x-report-stats :items="[
        ['label' => __('owner.catch.cards.total_catch'), 'value' => $statistics['trips_with_catch']],
        ['label' => __('owner.catch.cards.total_weight'), 'value' => number_format($statistics['total_weight'], 2) . ' ' . __('owner.units.kg')],
        ['label' => __('owner.catch.cards.total_revenue'), 'value' => number_format($statistics['total_revenue'], 2)],
        ['label' => __('owner.catch.cards.avg_price_per_kg'), 'value' => number_format($statistics['avg_price_per_kg'], 2)],
    ]" />

    @if($trips->isEmpty())
        <div class="alert alert-warning">
            <strong>{{ __('owner.reports.no_data_found') }}</strong>
            <p class="mb-0 text-muted">{{ __('owner.reports.try_adjust_filters') }}</p>
        </div>
    @else
        <table class="report-table block">
            <thead>
                <tr>
                    <th style="width:6%;">#</th>
                    <th class="col-text" style="width:26%;">{{ __('owner.generated.trip') }}</th>
                    <th style="width:18%;">{{ __('owner.catch.boat') }}</th>
                    <th style="width:14%;">{{ __('owner.generated.trip_start') }}</th>
                    <th style="width:14%;">{{ __('owner.generated.trip_end') }}</th>
                    <th style="width:11%;">{{ __('owner.catch.weight') }}</th>
                    <th class="col-num" style="width:11%;">{{ __('owner.reports.total_revenue') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trips as $i => $trip)
                    @php
                        $tripWeight  = $trip->catches?->total_weight ?? 0;
                        $tripRevenue = $trip->catches?->details->sum('total_price') ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="col-text">{{ $trip->name }}</td>
                        <td>{{ $trip->boat?->name ?: '-' }}</td>
                        <td>{{ optional($trip->start_date)->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ optional($trip->end_date)->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ number_format($tripWeight, 2) }} {{ __('owner.units.kg') }}</td>
                        <td class="col-num">{{ number_format($tripRevenue, 2) }} <x-riyal-icon /></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">{{ __('owner.catch.total') }}</td>
                    <td>{{ number_format($statistics['total_weight'], 2) }} {{ __('owner.units.kg') }}</td>
                    <td class="col-num">{{ number_format($statistics['total_revenue'], 2) }} <x-riyal-icon /></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <table class="report-footer">
        <tr>
            <td class="rf-text">
                {{ $settings['company_name'] ?? $settings['title'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}
            </td>
        </tr>
    </table>

</x-report-layout>
