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
            __('owner.reports.total_revenue'),
            __('owner.reports.total_costs'),
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
                <span class="meta-label">{{ __('owner.reports.total_revenue') }}:</span>
                <span class="meta-value">{{ number_format($statistics['total_revenue'], 2) }} <x-riyal-icon /></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">{{ __('owner.reports.total_costs') }}:</span>
                <span class="meta-value">{{ number_format($statistics['total_costs'], 2) }} <x-riyal-icon /></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">{{ __('owner.reports.net_profit') }}:</span>
                <span class="meta-value">{{ number_format($statistics['net_profit'], 2) }} <x-riyal-icon /></span>
            </div>
        </x-slot:metadata>

        @php
            $rowNumber = 0;
        @endphp
        @foreach($trips as $tripItem)
            @php
                $rowNumber++;
                $tripFinancials = $financials[$tripItem->id];
            @endphp
            <tr>
                <td>{{ $rowNumber }}</td>
                <td>#{{ $tripItem->number }}</td>
                <td>{{ $tripItem->boat->name ?? __('owner.trips.no_boat') }}</td>
                <td>{{ $tripItem->captain->name ?? __('owner.trips.no_captain') }}</td>
                <td>{{ $tripItem->start_date ? $tripItem->start_date->format('Y-m-d') : '-' }}</td>
                <td>{{ $tripItem->status->label() }}</td>
                <td>{{ number_format(round($tripFinancials['catch_weight']), 0) }} {{ __('owner.units.kg') }}</td>
                <td>{{ number_format($tripFinancials['gross_revenue'], 2) }} <x-riyal-icon /></td>
                <td>{{ number_format($tripFinancials['total_costs'], 2) }} <x-riyal-icon /></td>
            </tr>
        @endforeach
    </x-report-table>

    @if($trip)
        @php
            $f = $financials[$trip->id];
            $catchWeightDisplay = $f['catch_weight_by_unit']->isNotEmpty()
                ? $f['catch_weight_by_unit']
                    ->map(fn ($weight, $unit) => number_format(round($weight), 0) . ' ' . $unit)
                    ->implode('، ')
                : '0';
            $catchDetails = $trip->catches?->details ?? collect();
            $depart = $trip->start_date ? $trip->start_date->format('Y-m-d') : '-';
            $departTime = $trip->departure_time ? $trip->departure_time->format('H:i') : null;
            $returnDate = $trip->end_date ? $trip->end_date->format('Y-m-d') : '-';
            $returnTime = $trip->return_time ? $trip->return_time->format('H:i') : null;
        @endphp

        {{-- Financial summary cards --}}
        <x-report-stats :items="[
            ['label' => __('owner.reports.catch_weight'), 'value' => $catchWeightDisplay],
            ['label' => __('owner.reports.total_income'), 'value' => number_format($f['total_income'], 2)],
            ['label' => __('owner.reports.depreciation') . ' (' . number_format($f['depreciation_percent'], 2) . '%)', 'value' => number_format($f['depreciation'], 2)],
            ['label' => __('owner.reports.net_profit'), 'value' => number_format($f['net_profit'], 2), 'color' => $f['net_profit'] >= 0 ? '#16a34a' : '#dc2626'],
        ]" />

        {{-- Trip information & schedule --}}
        <div class="info-section">
            <div class="info-col">
                <div class="info-label">{{ __('owner.reports.trip_info') }}</div>
                <p><strong>{{ __('owner.trips.trip_number') }}:</strong> #{{ $trip->number }}</p>
                @if($trip->name)
                    <p><strong>{{ __('owner.trips.show.name') }}:</strong> {{ $trip->name }}</p>
                @endif
                @if($trip->license_number)
                    <p><strong>{{ __('owner.trips.show.license_number') }}:</strong> {{ $trip->license_number }}</p>
                @endif
                <p><strong>{{ __('owner.reports.permit_type') }}:</strong> {{ $trip->permit_type ?? __('owner.reports.not_available') }}</p>
                <p><strong>{{ __('owner.trips.status') }}:</strong> {{ $trip->status->label() }}</p>
            </div>
            <div class="info-col">
                <div class="info-label">{{ __('owner.reports.schedule') }}</div>
                @if($trip->duration_text)
                    <p><strong>{{ __('owner.reports.duration') }}:</strong> {{ $trip->duration_text }}</p>
                @endif
                <p><strong>{{ __('owner.reports.departure') }}:</strong> {{ $depart }}@if($departTime) — {{ $departTime }}@endif</p>
                <p><strong>{{ __('owner.reports.return') }}:</strong> {{ $returnDate }}@if($returnTime) — {{ $returnTime }}@endif</p>
            </div>
            <div class="info-col">
                <div class="info-label">{{ __('owner.reports.crew') }} &amp; {{ __('owner.reports.location') }}</div>
                <p><strong>{{ __('owner.trips.show.captain') }}:</strong> {{ $trip->captain?->name ?? __('owner.trips.no_captain') }}</p>
                @if($trip->captain?->phone)
                    <p>{{ $trip->captain->phone }}</p>
                @endif
                <p><strong>{{ __('owner.reports.crew_count') }}:</strong> {{ ($trip->crew_count ?? 0) + ($trip->captain ? 1 : 0) }}</p>
                @if($trip->port)
                    <p><strong>{{ __('owner.reports.port') }}:</strong> {{ $trip->port->name }}</p>
                @endif
                @if($trip->governorate)
                    <p><strong>{{ __('owner.reports.governorate') }}:</strong> {{ $trip->governorate->name }}</p>
                @endif
            </div>
        </div>

        {{-- Boat information --}}
        <div class="summary-section mt-4">
            <h5 class="info-label">{{ __('owner.trips.show.boat_info') }}</h5>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>{{ __('owner.trips.boat_name') }}</th>
                        <th>{{ __('owner.trips.show.boat_number') }}</th>
                        <th>{{ __('owner.trips.show.boat_color') }}</th>
                        <th>{{ __('owner.trips.show.boat_length') }}</th>
                        <th>{{ __('owner.trips.show.boat_width') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $trip->boat_name ?? ($trip->boat->name ?? '-') }}</td>
                        <td>{{ $trip->boat_number ?? '-' }}</td>
                        <td>{{ $trip->boat_color ?? '-' }}</td>
                        <td>{{ $trip->boat_length ?? '-' }}</td>
                        <td>{{ $trip->boat_width ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Catch breakdown --}}
        <div class="summary-section mt-4">
            <h5 class="info-label">{{ __('owner.reports.catch_breakdown') }}</h5>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.reports.fish_name') }}</th>
                        <th>{{ __('owner.reports.weight') }}</th>
                        <th>{{ __('owner.catch.unit') }}</th>
                        <th>{{ __('owner.reports.price_per_kg') }}</th>
                        <th>{{ __('owner.reports.total_value') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($catchDetails as $i => $detail)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $detail->fish->name ?? ($detail->fish_name ?? __('owner.reports.unknown_fish')) }}</td>
                            <td>{{ number_format(round($detail->weight), 0) }}</td>
                            <td>{{ $detail->unit->name ?: __('owner.units.kg') }}</td>
                            <td>{{ number_format($detail->price_per_kg, 2) }} <x-riyal-icon /></td>
                            <td>{{ number_format($detail->total_price, 2) }} <x-riyal-icon /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:#95a5a6;">{{ __('owner.trips.show.no_catch_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Sales breakdown --}}
        <div class="summary-section mt-4">
            <h5 class="info-label">{{ __('owner.reports.sales_breakdown') }}</h5>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.reports.sale_number') }}</th>
                        <th>{{ __('owner.reports.customer') }}</th>
                        <th>{{ __('owner.reports.sale_date') }}</th>
                        <th>{{ __('owner.reports.amount') }}</th>
                        <th>{{ __('owner.reports.payment_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trip->sales as $i => $sale)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $sale->number }}</td>
                            <td>{{ $sale->customer_name ?? ($sale->customer->name ?? '-') }}</td>
                            <td>{{ $sale->sale_datetime ? $sale->sale_datetime->format('Y-m-d') : '-' }}</td>
                            <td>{{ number_format($sale->total_price, 2) }} <x-riyal-icon /></td>
                            <td>{{ \App\Models\Sale::paymentStatusText($sale->payment_status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:#95a5a6;">{{ __('owner.reports.no_sales') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Financial summary --}}
        <x-report-summary :qr-code="$qrCode">
            <x-report-summary-row
                :label="__('owner.reports.total_income')"
                :value="number_format($f['total_income'], 2)"
                :showCurrency="true"
            />
            <x-report-summary-row
                :label="__('owner.reports.depreciation_percent')"
                :value="number_format($f['depreciation_percent'], 2) . '%'"
            />
            <x-report-summary-row
                :label="__('owner.reports.total_expenses')"
                :value="number_format($f['total_expenses'], 2)"
                :showCurrency="true"
            />
            <x-report-summary-row
                :label="__('owner.reports.outstanding')"
                :value="number_format($f['outstanding'], 2)"
                :showCurrency="true"
            />
            <x-report-summary-row
                :label="__('owner.reports.net_profit')"
                :value="number_format($f['net_profit'], 2)"
                :showCurrency="true"
                :highlight="true"
            />
        </x-report-summary>

        {{-- Profit distribution --}}
        <div class="summary-section mt-4">
            <h5 class="info-label">{{ __('owner.reports.profit_distribution') }}</h5>
            <table class="report-table">
                <tbody>
                    <tr>
                        <th>{{ __('owner.reports.net_profit') }}</th>
                        <td>{{ number_format($f['net_profit'], 2) }} <x-riyal-icon /></td>
                    </tr>
                    <tr>
                        <th>{{ __('owner.reports.owner_share') }} (50%)</th>
                        <td>{{ number_format($f['owner_share'], 2) }} <x-riyal-icon /></td>
                    </tr>
                    <tr>
                        <th>{{ __('owner.reports.crew_share') }} (50%)</th>
                        <td>{{ number_format($f['crew_share'], 2) }} <x-riyal-icon /></td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Crew salaries --}}
        <div class="summary-section mt-4">
            <h5 class="info-label">{{ __('owner.reports.crew_salaries') }}</h5>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>{{ __('owner.reports.member_name') }}</th>
                        <th>{{ __('owner.reports.percentage') }}</th>
                        <th>{{ __('owner.reports.amount') }}</th>
                        <th>{{ __('owner.reports.signature') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($f['crew_members'] as $member)
                        <tr>
                            <td>{{ $member['name'] }}</td>
                            <td>{{ number_format($member['percent'], 2) }}%</td>
                            <td>{{ number_format($member['due'], 2) }} <x-riyal-icon /></td>
                            <td></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; color:#95a5a6;">{{ __('owner.reports.no_crew') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($trip->notes)
            <div class="summary-section mt-4">
                <h5 class="info-label">{{ __('owner.reports.notes') }}</h5>
                <p style="color:#5a6c7d;">{!! nl2br(e($trip->notes)) !!}</p>
            </div>
        @endif
    @endif

    {{-- For the all-trips view (no single $trip) show the QR on its own. --}}
    @if(!$trip && !empty($qrCode))
        <div class="qr-box" style="text-align:center; margin:18px 0;">
            <img src="{{ $qrCode }}" alt="QR Code" style="width:110px; height:110px;" />
        </div>
    @endif

    <div class="footer">
        <p>{{ $settings['title'] ?? ($settings['company_name'] ?? '') }} - {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}</p>
        <p>{{ __('owner.reports.thank_you') }}</p>
    </div>

</x-report-layout>
