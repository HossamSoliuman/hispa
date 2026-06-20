@extends('owner.layouts.master')
@section('title')
    {{ __('owner.trips.show.title') }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">{{ __('owner.trips.show.breadcrumb_manage') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.trips.show.breadcrumb_show') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.trips.show.page_header', ['number' => $data->number]) }}</h1>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start justify-content-lg-end">
            @include('owner.trips._actions', ['trip' => $data])
        </div>
    </div>

    @php
        $catchWeightDisplay = $financials['catch_weight_by_unit']->isNotEmpty()
            ? $financials['catch_weight_by_unit']
                ->map(fn ($weight, $unit) => number_format(round($weight), 0) . ' ' . $unit)
                ->implode('، ')
            : '0';
    @endphp

    {{-- Trip-wide financial summary cards (sum of all boats) --}}
    <div class="row mb-3">
        @include('owner.components.stat-card', [
            'title'    => __('owner.reports.catch_weight'),
            'value'    => $catchWeightDisplay,
            'icon'     => 'fas fa-weight-hanging',
            'colClass' => 'col-6 col-lg-3 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title'    => __('owner.reports.total_income'),
            'value'    => number_format($financials['total_income'], 2),
            'icon'     => 'fas fa-coins',
            'colClass' => 'col-6 col-lg-3 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title'    => __('owner.reports.depreciation') . ' (' . number_format($financials['depreciation_percent'], 2) . '%)',
            'value'    => number_format($financials['depreciation'], 2),
            'icon'     => 'fas fa-receipt',
            'colClass' => 'col-6 col-lg-3 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title'    => __('owner.reports.net_profit'),
            'value'    => number_format($financials['net_profit'], 2),
            'icon'     => 'fas fa-chart-line',
            'colClass' => 'col-6 col-lg-3 mb-3',
        ])
    </div>

    <div class="row gx-4">
        <div class="col-lg-8">

            {{-- Boats & captains overview --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header fw-bold">
                    <i class="fas fa-anchor me-2"></i> {{ __('owner.trips.show.boats_title') }}
                    <span class="badge bg-primary ms-1">{{ $financials['boats']->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('owner.trips.boats.boat') }}</th>
                                    <th>{{ __('owner.trips.show.boat_number') }}</th>
                                    <th>{{ __('owner.trips.boats.captains') }}</th>
                                    <th>{{ __('owner.trips.show.crew_count') }}</th>
                                    <th>{{ __('owner.reports.net_profit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($financials['boats'] as $boat)
                                    <tr>
                                        <td>{{ $boat['boat_name'] }}</td>
                                        <td>{{ $boat['boat_number'] ?? '—' }}</td>
                                        <td>{{ $boat['captains']->pluck('name')->implode('، ') ?: __('owner.trips.no_captain') }}</td>
                                        <td>{{ $boat['crew_count'] + $boat['captains']->count() }}</td>
                                        <td>{{ number_format($boat['net_profit'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

            {{-- Per-boat financial breakdown --}}
            @foreach($financials['boats'] as $boat)
                @include('owner.trips._boat_breakdown', ['boat' => $boat])
            @endforeach

            {{-- Trip totals --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold">
                    <i class="fas fa-calculator me-2"></i> {{ __('owner.trips.show.trip_totals') }}
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm m-0 text-center">
                        <tbody>
                            <tr>
                                <th class="w-50">{{ __('owner.reports.total_income') }}</th>
                                <td>{{ number_format($financials['total_income'], 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.reports.total_expenses') }}</th>
                                <td>{{ number_format($financials['total_expenses'], 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.reports.outstanding') }}</th>
                                <td>{{ number_format($financials['outstanding'], 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.reports.owner_share') }} (50%)</th>
                                <td>{{ number_format($financials['owner_share'], 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.reports.crew_share') }} (50%)</th>
                                <td>{{ number_format($financials['crew_share'], 2) }}</td>
                            </tr>
                            <tr class="table-success fw-bold">
                                <th>{{ __('owner.reports.net_profit') }}</th>
                                <td>{{ number_format($financials['net_profit'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

        </div>
        <div class="col-lg-4">

            {{-- Trip details --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold">
                    <i class="fas fa-ship me-2"></i> {{ __('owner.trips.show.trip_details') }}
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm m-0 text-center">
                        <tbody>
                            <tr>
                                <th class="w-40">{{ __('owner.trips.show.name') }}</th>
                                <td>{{ $data->name ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.trips.show.license_number') }}</th>
                                <td>{{ $data->license_number ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.trips.show.status') }}</th>
                                <td>
                                    <span class="badge bg-{{ $data->status->color() }}">
                                        {{ $data->status->label() }}
                                    </span>
                                </td>
                            </tr>
                            @if($data->duration_text)
                                <tr>
                                    <th>{{ __('owner.reports.duration') }}</th>
                                    <td>{{ $data->duration_text }}</td>
                                </tr>
                            @endif
                            @php
                                use Illuminate\Support\Carbon;
                                $startDate = $data->start_date ? Carbon::parse($data->start_date)->format('Y/m/d') : '--';
                                $endDate   = $data->end_date   ? Carbon::parse($data->end_date)->format('Y/m/d')   : '--';
                            @endphp
                            <tr>
                                <th>{{ __('owner.trips.show.date_depart_return') }}</th>
                                <td>{{ $startDate }} — {{ $endDate }}</td>
                            </tr>
                            @if($data->port)
                                <tr>
                                    <th>{{ __('owner.reports.port') }}</th>
                                    <td>{{ $data->port->name }}</td>
                                </tr>
                            @endif
                            @if($data->governorate)
                                <tr>
                                    <th>{{ __('owner.reports.governorate') }}</th>
                                    <td>{{ $data->governorate->name }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>{{ __('owner.trips.show.boats_title') }}</th>
                                <td>{{ $financials['boats']->count() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

            {{-- Notes --}}
            @if($data->notes)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header fw-bold">
                        <i class="fas fa-sticky-note me-2"></i> {{ __('owner.trips.show.notes') }}
                    </div>
                    <div class="card-body text-muted">
                        {!! nl2br(e($data->notes)) !!}
                    </div>
                    <div class="card-arrow">
                        <div class="card-arrow-top-left"></div>
                        <div class="card-arrow-top-right"></div>
                        <div class="card-arrow-bottom-left"></div>
                        <div class="card-arrow-bottom-right"></div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('dashboard/assets/plugins/jquery-migrate/dist/jquery-migrate.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function tripTransition(tripId, toStatus, needsReason) {
            let cancelReason = null;

            function doTransition() {
                let postData = { _token: '{{ csrf_token() }}', to: toStatus };
                if (cancelReason) { postData.cancel_reason = cancelReason; }

                $.ajax({
                    url: "{{ route('owner.trips.transition', ['trip' => '__ID__']) }}".replace('__ID__', tripId),
                    type: 'POST',
                    data: postData,
                    success: function(response) {
                        Swal.fire('{{ __('owner.swal.success_title') ?? __('owner.swal.success') }}', response.message, 'success').then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || '{{ __('owner.swal.unexpected_error') }}';
                        Swal.fire('{{ __('owner.swal.error') }}', message, 'error');
                    }
                });
            }

            if (needsReason) {
                Swal.fire({
                    title: '{{ __('owner.trips.confirm_cancel_trip_title') }}',
                    input: 'textarea',
                    inputLabel: '{{ __('trips.errors.cancel_reason_required') }}',
                    inputPlaceholder: '{{ __('trips.errors.cancel_reason_required') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: '{{ __('owner.trips.confirm_cancel_trip_yes') }}',
                    cancelButtonText: '{{ __('owner.trips.confirm_cancel_trip_cancel') }}',
                    preConfirm: (reason) => {
                        if (!reason) {
                            Swal.showValidationMessage('{{ __('trips.errors.cancel_reason_required') }}');
                        }
                        return reason;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        cancelReason = result.value;
                        doTransition();
                    }
                });
            } else {
                Swal.fire({
                    title: '{{ __('owner.swal.confirm_title') }}',
                    text: '{{ __('owner.swal.confirm_text') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __('owner.swal.confirm_yes') }}',
                    cancelButtonText: '{{ __('owner.swal.cancel') }}'
                }).then((result) => {
                    if (result.isConfirmed) { doTransition(); }
                });
            }
        }
    </script>
@endsection
