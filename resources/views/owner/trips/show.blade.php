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

    {{-- Financial summary cards --}}
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
            'title'    => __('owner.reports.total_costs'),
            'value'    => number_format($financials['total_costs'], 2),
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

            {{-- Catch breakdown --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-fish me-2"></i>
                        {{ __('owner.trips.show.catch_title', ['count' => $data->catches?->details->count() ?? 0]) }}
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($data->catches && $data->catches->details->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('owner.catch.fish') }}</th>
                                        <th>{{ __('owner.catch.weight') }}</th>
                                        <th>{{ __('owner.catch.unit') }}</th>
                                        <th>{{ __('owner.sales.price_per_kilo') }}</th>
                                        <th>{{ __('owner.catch.total_price') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data->catches->details as $i => $detail)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $detail->fish->scientific_name ?? '---' }}</td>
                                            <td>{{ number_format($detail->weight, 2) }}</td>
                                            <td>{{ $detail->unit->name ?? '—' }}</td>
                                            <td>{{ number_format($detail->price_per_kg, 2) }}</td>
                                            <td>{{ number_format($detail->total_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-secondary text-center mb-0 m-3">
                            {{ __('owner.trips.show.no_catch_data') }}
                        </div>
                    @endif
                </div>
                <div class="card-footer text-muted text-center small">
                    {{ __('owner.trips.show.total_weight') }}: {{ $catchWeightDisplay }}
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

            {{-- Sales breakdown --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header fw-bold">
                    <i class="fas fa-cash-register me-2"></i> {{ __('owner.reports.sales_breakdown') }}
                </div>
                <div class="card-body p-0">
                    @if($data->sales->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-center mb-0">
                                <thead class="table-light">
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
                                    @foreach($data->sales as $i => $sale)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $sale->number }}</td>
                                            <td>{{ $sale->customer_name ?? ($sale->customer->name ?? '-') }}</td>
                                            <td>{{ $sale->sale_datetime ? $sale->sale_datetime->format('Y-m-d') : '-' }}</td>
                                            <td>{{ number_format($sale->total_price, 2) }}</td>
                                            <td>{{ \App\Models\Sale::paymentStatusText($sale->payment_status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-secondary text-center mb-0 m-3">
                            {{ __('owner.reports.no_sales') }}
                        </div>
                    @endif
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

            {{-- Financial breakdown --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold">
                    <i class="fas fa-calculator me-2"></i> {{ __('owner.reports.financial_summary') }}
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm m-0 text-center">
                        <tbody>
                            <tr>
                                <th class="w-50">{{ __('owner.reports.total_income') }}</th>
                                <td>{{ number_format($financials['total_income'], 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.reports.total_costs') }}</th>
                                <td>{{ number_format($financials['total_expenses'], 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.reports.outstanding') }}</th>
                                <td>{{ number_format($financials['outstanding'], 2) }}</td>
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

            {{-- Crew salaries --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header fw-bold">
                    <i class="fas fa-users me-2"></i> {{ __('owner.reports.crew_salaries') }}
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm m-0 text-center">
                        <tbody>
                            <tr>
                                <th class="w-50">{{ __('owner.reports.owner_share') }} (50%)</th>
                                <td>{{ number_format($financials['owner_share'], 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.reports.crew_share') }} (50%)</th>
                                <td>{{ number_format($financials['crew_share'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-sm table-bordered text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('owner.reports.member_name') }}</th>
                                <th>{{ __('owner.reports.percentage') }}</th>
                                <th>{{ __('owner.reports.amount') }}</th>
                                <th>{{ __('owner.reports.signature') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($financials['crew_members'] as $member)
                                <tr>
                                    <td>{{ $member['name'] }}</td>
                                    <td>{{ number_format($member['percent'], 2) }}%</td>
                                    <td>{{ number_format($member['due'], 2) }}</td>
                                    <td></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted">{{ __('owner.reports.no_crew') }}</td>
                                </tr>
                            @endforelse
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
                                <th>{{ __('owner.trips.show.crew_count') }}</th>
                                <td>{{ ($data->crew_count ?? 0) + ($data->captain ? 1 : 0) }}</td>
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

            {{-- Boat info --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold">
                    <i class="fas fa-anchor me-2"></i> {{ __('owner.trips.show.boat_info') }}
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm m-0 text-center">
                        <tbody>
                            <tr>
                                <th class="w-40">{{ __('owner.trips.boat_name') }}</th>
                                <td>{{ $data->boat_name ?? ($data->boat?->name ?? '---') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.trips.show.boat_number') }}</th>
                                <td>{{ $data->boat_number ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.trips.show.boat_color') }}</th>
                                <td>{{ $data->boat_color ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.trips.show.boat_length') }}</th>
                                <td>{{ $data->boat_length ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.trips.show.boat_width') }}</th>
                                <td>{{ $data->boat_width ?? '---' }}</td>
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

            {{-- Captain --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header fw-bold">
                    <i class="fas fa-user-ninja me-2"></i> {{ __('owner.trips.show.captain') }}
                </div>
                <div class="card-body d-flex align-items-center">
                    <img src="{{ asset($data->captain->logo ?? 'assets/img/avatar.png') }}"
                        alt="{{ __('owner.generated.item_0a9699') }}" class="rounded-circle border" width="50" height="50">
                    <div class="ms-3">
                        <div class="fw-bold">{{ $data->captain?->name ?? '---' }}</div>
                        <div class="text-muted small">{{ $data->captain?->phone ?? '---' }}</div>
                    </div>
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
