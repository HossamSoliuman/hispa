<x-report-layout
    :title="$trip ? __('owner.reports.trip_report') . ' #' . $trip->number : __('owner.reports.all_trips_report')"
    :title-en="$trip ? 'Trip Report #' . $trip->number : 'All Trips Report'"
    :document-number="'#' . str_pad($statistics['total_trips'], 8, '0', STR_PAD_LEFT)"
    :settings="$settings"
    :qr-code="$qrCode">

<style>
    /* Currency symbol styling */
    .currency-symbol {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: .18rem;
        font-size: 0.95rem;
    }
    .currency-symbol x-riyal-icon,
    .currency-symbol svg {
        width: 0.8rem;
        height: auto;
        display: inline-block;
        vertical-align: middle;
    }
    /* Table layout */
    table.report-table {
        table-layout: auto;
        width: 100%;
    }
    table.report-table th,
    table.report-table td {
        word-wrap: break-word;
        overflow-wrap: break-word;
        vertical-align: middle;
        white-space: normal;
        padding: .45rem .8rem;
    }
    /* Right-align numeric columns */
    table.report-table th:nth-child(5),
    table.report-table td:nth-child(5),
    table.report-table th:nth-child(6),
    table.report-table td:nth-child(6) {
        text-align: right;
    }
</style>

    <x-report-header
        :document-number="'#' . str_pad($statistics['total_trips'], 8, '0', STR_PAD_LEFT)"
        :title="$trip ? __('owner.reports.trip_report') : __('owner.reports.all_trips_report')"
        :title-en="$trip ? 'Trip Report' : 'All Trips Report'"
        :settings="$settings" />

    <x-report-info
        :settings="$settings"
        :from-date="$fromDate"
        :to-date="$toDate">
        <x-slot:additionalInfo>
            @if($trip)
                <p><strong>{{ __('owner.trips.trip_number') }}:</strong> #{{ $trip->number }}</p>
                @if($trip->boat)
                    <p><strong>{{ __('owner.trips.boat_name') }}:</strong> {{ $trip->boat->name }}</p>
                @endif
                @if($trip->captain)
                    <p><strong>{{ __('owner.trips.captain_name') }}:</strong> {{ $trip->captain->name }}</p>
                @endif
                @if($trip->start_date)
                    <p><strong>{{ __('owner.trips.departure_date') }}:</strong> {{ $trip->start_date->format('Y-m-d') }}</p>
                @endif
                @if($trip->end_date)
                    <p><strong>{{ __('owner.trips.return_date') }}:</strong> {{ $trip->end_date->format('Y-m-d') }}</p>
                @endif
            @endif
        </x-slot:additionalInfo>
    </x-report-info>

    @if(isset($trips) && $trips->isEmpty())
        <div class="alert alert-warning">
            <h5 class="mb-2">{{ __('owner.reports.no_data_found') ?? 'No trips found for the selected filters.' }}</h5>
            <p class="mb-1"><strong>{{ __('owner.reports.owner_id') ?? 'Owner ID' }}:</strong> {{ $owner_id ?? '-' }}</p>
            <p class="mb-1"><strong>{{ __('owner.reports.applied_filters') ?? 'Applied filters' }}:</strong></p>
            <ul>
                <li>{{ __('owner.trips.trip_number') }}: {{ $filters['trip_id'] ?? '-' }}</li>
                <li>{{ __('owner.reports.from_date') ?? 'From' }}: {{ $filters['from_date'] ?? '-' }}</li>
                <li>{{ __('owner.reports.to_date') ?? 'To' }}: {{ $filters['to_date'] ?? '-' }}</li>
                <li>{{ __('owner.trips.status') }}: {{ $filters['status'] ?? '-' }}</li>
                <li>{{ __('owner.trips.boat_name') }}: {{ $filters['boat_id'] ?? '-' }}</li>
            </ul>
            <p class="mb-0 small text-muted">{{ __('owner.reports.try_adjust_filters') ?? 'Try adjusting the filters or check that trips exist for this owner.' }}</p>
        </div>
    @endif

    <x-report-table
        :headers="[
            '#',
            __('owner.trips.trip_number'),
            __('owner.trips.boat_name'),
            __('owner.trips.captain_name'),
            __('owner.trips.departure_date'),
            __('owner.trips.status'),
            __('owner.trips.total_catch'),
        ]"
        :data="$trips">

        <x-slot:metadata>
            <div class="meta-item">
                <span class="meta-label">{{ __('owner.reports.total_trips') }}:</span>
                <span class="meta-value">{{ $statistics['total_trips'] }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">{{ __('owner.reports.completed_trips') }}:</span>
                <span class="meta-value">{{ $statistics['completed_trips'] }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">{{ __('owner.reports.total_catch') }}:</span>
                <span class="meta-value">{{ number_format(round($statistics['total_catch']), 0) }} {{ __('owner.units.kg') }}</span>
            </div>

            <div class="meta-item">
                <span class="meta-label">{{ __('owner.reports.fish_types') }}:</span>
                <span class="meta-value">{{ $statistics['fish_types'] }}</span>
            </div>
        </x-slot:metadata>

        @php
            $rowNumber = 0;
        @endphp
        @foreach($trips as $tripItem)
            @php
                $rowNumber++;
                $totalCatch = $tripItem->fishStocks->sum('weight');
                $totalRevenue = $tripItem->fishStocks->sum(function($stock) {
                    return $stock->fish ? ($stock->weight * ($stock->fish->price ?? 0)) : 0;
                });
            @endphp
            <tr>
                <td>{{ $rowNumber }}</td>
                <td>#{{ $tripItem->number }}</td>
                <td>{{ $tripItem->boat->name ?? __('owner.trips.no_boat') }}</td>
                <td>{{ $tripItem->captain->name ?? __('owner.trips.no_captain') }}</td>
                <td>{{ $tripItem->start_date ? $tripItem->start_date->format('Y-m-d') : '-' }}</td>
                <td>
                    <span class="badge bg-{{ $tripItem->status->color() }}">{{ $tripItem->status->label() }}</span>
                </td>
                <td>{{ number_format(round($totalCatch), 0) }} {{ __('owner.units.kg') }}</td>

            </tr>
        @endforeach
    </x-report-table>

    @if($trip)
        {{-- Single trip detailed breakdown --}}
        <div class="summary-section mt-4">
            <h5 class="fw-bold mb-3">{{ __('owner.reports.catch_breakdown') }}</h5>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.reports.fish_name') }}</th>
                        <th>{{ __('owner.reports.weight') }}</th>
                        <!-- Price per KG and Total Value removed as requested -->
                    </tr>
                </thead>
                <tbody>
                    @php $detailNum = 0; @endphp
                    @foreach($trip->fishStocks as $stock)
                        @php
                            $detailNum++;
                            $pricePerKg = $stock->fish->price ?? 0;
                            $totalValue = $stock->weight * $pricePerKg;
                        @endphp
                        <tr>
                            <td>{{ $detailNum }}</td>
                            <td>{{ $stock->fish->name ?? __('owner.reports.unknown_fish') }}</td>
                            <td>{{ number_format(round($stock->weight), 0) }} {{ __('owner.units.kg') }}</td>
                            <!-- Price per KG and Total Value removed as requested -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="qr-box" style="text-align:center; margin:18px 0;">
        @if(!empty($qrCode))
            <img src="{{ $qrCode }}" alt="QR Code" style="width:110px; height:110px;" />
        @endif
    </div>

    <div class="footer">
        <p>{{ $settings['title'] }} - {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}</p>
        <p>{{ __('owner.reports.thank_you') }}</p>
    </div>

</x-report-layout>
