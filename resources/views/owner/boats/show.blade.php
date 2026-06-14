@extends('owner.layouts.master')

@section('title')
    {{ __('owner.boats.show_title') }}
@endsection

@section('content')
    {{-- <div class="container py-4"> --}}
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 mb-1 fw-bold text-dark">
            <i class="bi bi-ship me-2"></i>{{ __('owner.boats.boat_details', ['number' => $boat->number]) }}
        </h1>
        <p class="text-muted mb-0">{{ $boat->name }}</p>
    </div>

    @php
        $sarIcon = view('components.riyal-icon', [
            'size' => 'sm',
            'style' => 'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.25rem;',
        ])->render();
    @endphp

    <!-- KPI Cards using stat-card component -->
    <div class="row">
        @include('owner.components.stat-card', [
            'title' => __('owner.boats.boat_name'),
            'value' => $boat->name,
            'icon' => 'fas fa-ship',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
            // 'footer' => '<a href="#" data-bs-toggle="modal" data-bs-target="#boatDetailsModal" class="text-white text-decoration-none">' . __('owner.actions.view_current') . ' <i class="bi bi-chevron-left ms-1"></i></a>'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.boats.crew'),
            'value' => $crewStats['total'] . ' ' . __('owner.boats.workers'),
            'icon' => 'bi bi-person-workspace',
            'gradient' => 'linear-gradient(135deg, #20c997, #198754)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
            // 'stats' => [
            //     ['label' => __('owner.boats.active_crew'), 'value' => $crewStats['active']],
            //     ['label' => __('owner.boats.inactive_crew'), 'value' => $crewStats['inactive']]
            // ],
            // 'footer' => '<a href="' . route('owner.boats.crew', $boat->id) . '" class="text-white text-decoration-none">' . __('owner.actions.view_current') . ' <i class="bi bi-chevron-left ms-1"></i></a>'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.boats.salaries'),
            'value' => number_format($payrolls, 0) . ' ' . $sarIcon,
            'icon' => 'bi bi-wallet2',
            'gradient' => 'linear-gradient(135deg, #6c757d, #495057)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.boats.revenues'),
            'value' => number_format($revenues, 0) . ' ' . $sarIcon,
            'icon' => 'bi bi-cash-coin',
            'gradient' => 'linear-gradient(135deg, #ffc107, #fd7e14)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.boats.expenses'),
            'value' => number_format($expenses, 0) . ' ' . $sarIcon,
            'icon' => 'bi bi-currency-exchange',
            'gradient' => 'linear-gradient(135deg, #dc3545, #c82333)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.boats.total_catch'),
            'value' => number_format($totalCatch, 0) . ' ' . __('owner.units.kg'),
            'icon' => 'bi bi-basket2-fill',
            'gradient' => 'linear-gradient(135deg, #17a2b8, #138496)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])
    </div>

    <!-- Tabs Card -->
    <div class="card mt-4">
        @include('owner.partials._card_arrow')
        <div class="card-header border-bottom d-flex align-items-center flex-wrap gap-2">
            <ul class="nav nav-tabs card-header-tabs justify-content-start flex-grow-1" id="boatTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                        type="button" role="tab" aria-controls="overview" aria-selected="true">
                        {{ __('owner.boats.tab_overview') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics"
                        type="button" role="tab" aria-controls="analytics" aria-selected="false">
                        {{ __('owner.boats.tab_analytics') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="trips-tab" data-bs-toggle="tab" data-bs-target="#trips" type="button"
                        role="tab" aria-controls="trips" aria-selected="false">
                        {{ __('owner.boats.tab_trips') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="crew-tab" data-bs-toggle="tab" data-bs-target="#boat-crew"
                        type="button" role="tab" aria-controls="boat-crew" aria-selected="false">
                        {{ __('owner.boats.crew') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="maintenance-tab" data-bs-toggle="tab" data-bs-target="#boat-maintenance"
                        type="button" role="tab" aria-controls="boat-maintenance" aria-selected="false">
                        {{ __('owner.boats.maintenance') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="inspections-tab" data-bs-toggle="tab" data-bs-target="#boat-inspections"
                        type="button" role="tab" aria-controls="boat-inspections" aria-selected="false">
                        {{ __('owner.boats.inspections') }}
                    </button>
                </li>
            </ul>
            <div class="ms-auto d-flex gap-2 me-3 mb-2">
                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                    <i class="bi bi-tools me-1"></i>{{ __('owner.boats.maintenance_schedule') }}
                </button>
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#inspectionModal">
                    <i class="bi bi-plus-circle me-1"></i>{{ __('owner.generated.add_inspection') }}
                </button>
            </div>
        </div>

        <div class="card-body tab-content" id="boatTabsContent">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <div class="row g-3">
                    <!-- Boat Status -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div
                                        class="avatar-circle bg-{{ $boat->status ? 'success' : 'danger' }} bg-opacity-10 text-{{ $boat->status ? 'success' : 'danger' }} me-3">
                                        <i class="bi bi-clipboard2-pulse"></i>
                                    </div>
                                    <h6 class="mb-0 text-muted">{{ __('owner.boats.boat_status') }}</h6>
                                </div>
                                <h4 class="mb-0 fw-bold text-{{ $boat->status ? 'success' : 'danger' }}">
                                    {{ $boat->status ? __('owner.status.active') : __('owner.status.inactive') }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    <!-- Last Trip -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-3">
                                        <i class="bi bi-calendar2-check"></i>
                                    </div>
                                    <h6 class="mb-0 text-muted">{{ __('owner.boats.last_trip') }}</h6>
                                </div>
                                @if ($lastTripData)
                                    <h5 class="mb-1 fw-bold">{{ $lastTripData['number'] }}</h5>
                                    <small class="text-muted d-block">
                                        <i
                                            class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($lastTripData['start_date'])->format('d/m/Y') }}
                                    </small>
                                    <div class="mt-2 pt-2 border-top">
                                        <small class="text-muted d-block">
                                            {{ __('owner.boats.catch_amount') }}:
                                            <strong>{{ number_format($lastTripData['totalCatch'], 0) }}</strong>
                                            {{ __('owner.units.kg') }}
                                        </small>
                                        <small class="text-muted d-block">
                                            {{ __('owner.boats.revenue') }}:
                                            <strong>{{ number_format($lastTripData['revenues'], 0) }}</strong>
                                            {!! $sarIcon !!}
                                        </small>
                                    </div>
                                @else
                                    <p class="mb-0 text-muted">{{ __('owner.boats.no_trips') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Current Port -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar-circle bg-info bg-opacity-10 text-info me-3">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <h6 class="mb-0 text-muted">{{ __('owner.boats.current_port') }}</h6>
                                </div>
                                <h4 class="mb-0 fw-bold">{{ $boat->port->name }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Last Maintenance -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar-circle bg-warning bg-opacity-10 text-warning me-3">
                                        <i class="bi bi-tools"></i>
                                    </div>
                                    <h6 class="mb-0 text-muted">{{ __('owner.boats.last_maintenance') }}</h6>
                                </div>
                                @if ($lastMaintenance)
                                    <h5 class="mb-1 fw-bold">{{ $lastMaintenance->date }}</h5>
                                    <small class="text-muted">{{ $lastMaintenance->description }}</small>
                                @else
                                    <p class="mb-0 text-muted">{{ __('owner.boats.no_maintenance') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Crew Count -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar-circle bg-success bg-opacity-10 text-success me-3">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <h6 class="mb-0 text-muted">{{ __('owner.boats.crew_count') }}</h6>
                                </div>
                                <h4 class="mb-2 fw-bold">{{ $crewStats['total'] }} {{ __('owner.boats.members') }}
                                </h4>
                                <div class="d-flex gap-3">
                                    <small class="text-success">
                                        <i class="bi bi-check-circle me-1"></i>{{ __('owner.boats.active_crew') }}:
                                        {{ $crewStats['active'] }}
                                    </small>
                                    <small class="text-danger">
                                        <i class="bi bi-x-circle me-1"></i>{{ __('owner.boats.inactive_crew') }}:
                                        {{ $crewStats['inactive'] }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Trips -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-3">
                                        <i class="bi bi-flag-fill"></i>
                                    </div>
                                    <h6 class="mb-0 text-muted">{{ __('owner.boats.total_trips') }}</h6>
                                </div>
                                <h4 class="mb-1 fw-bold">{{ $boat->trips()->count() }} {{ __('owner.boats.trip') }}
                                </h4>
                                <small class="text-muted">{{ __('owner.boats.since_start') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Tab -->
            <div class="tab-pane fade" id="analytics" role="tabpanel" aria-labelledby="analytics-tab">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-graph-up-arrow me-2 text-primary"></i>{{ __('owner.boats.boat_analytics') }}
                </h5>
                <div class="row g-3">
                    <!-- Expenses Distribution Chart -->
                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-light border-bottom">
                                <h6 class="mb-0">
                                    <i
                                        class="bi bi-pie-chart-fill me-2 text-primary"></i>{{ __('owner.boats.expenses_distribution') }}
                                </h6>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center"
                                style="min-height: 300px;">
                                <canvas id="expensesDistributionChart" style="max-height: 280px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Income vs Expenses Chart -->
                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-light border-bottom">
                                <h6 class="mb-0">
                                    <i
                                        class="bi bi-bar-chart-fill me-2 text-primary"></i>{{ __('owner.boats.income_vs_expenses') }}
                                </h6>
                            </div>
                            <div class="card-body" style="min-height: 300px;">
                                <canvas id="incomeVsExpensesChart" style="max-height: 280px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trips Tab -->
            <div class="tab-pane fade" id="trips" role="tabpanel" aria-labelledby="trips-tab">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-ship me-2 text-primary"></i>{{ __('owner.boats.boat_trips') }}
                </h5>
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        @include('owner.boats.partials._trips_table', ['trips' => $boat->trips])
                    </div>
                </div>
            </div>

            <!-- Crew Tab -->
            <div class="tab-pane fade" id="boat-crew" role="tabpanel" aria-labelledby="crew-tab">
                @include('owner.boats.crew.table')
            </div>

            <!-- Maintenance Tab -->
            <div class="tab-pane fade" id="boat-maintenance" role="tabpanel" aria-labelledby="maintenance-tab">
                @include('owner.boats.maintenance.table')
            </div>

            <!-- Inspections Tab -->
            <div class="tab-pane fade" id="boat-inspections" role="tabpanel" aria-labelledby="inspections-tab">
                @include('owner.boats.inspections.table')
            </div>
        </div>
    </div>

    @include('owner.boats.partials._modals', ['boat' => $boat])
    @include('owner.boats.crew.show_modal')
    @include('owner.boats.maintenance.modal', ['fixedBoat' => $boat, 'categories' => $categories])
    @include('owner.boats.inspections.modal', ['fixedBoat' => $boat])
    {{-- </div> --}}
@endsection

@section('style')
    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        /* Tab content improvements */
        .tab-pane {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var boat_id = "{{ $boat->id }}";
        window.currentBoatId = {{ $boat->id }};
        window.routes = {
            getTripData: "{{ route('owner.getTripData') }}",
            crewData: "{{ route('owner.getCrewData') }}",
            maintenanceData: "{{ route('owner.maintenance.data') }}",
            maintenanceStore: "{{ route('owner.maintenance.store') }}",
            maintenanceUpdate: "{{ route('owner.maintenance.update', ':id') }}",
            maintenanceDestroy: "{{ route('owner.maintenance.destroy', ':id') }}",
            maintenanceEdit: "{{ route('owner.maintenance.edit', ':id') }}",
            maintenanceShow: "{{ route('owner.maintenance.show', ':id') }}",
            inspectionData: "{{ route('owner.inspections.data') }}",
            inspectionStore: "{{ route('owner.inspections.store') }}",
            inspectionUpdate: "{{ route('owner.inspections.update', ':id') }}",
            inspectionDestroy: "{{ route('owner.inspections.destroy', ':id') }}",
            inspectionEdit: "{{ route('owner.inspections.edit', ':id') }}",
            inspectionShow: "{{ route('owner.inspections.show', ':id') }}",
        };
        let appLocale = '{{ app()->getLocale() }}';
        let languageOptions = {};
        if (appLocale === 'ar') {
            languageOptions = { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" };
        }
        let swalOptions = {
            title: '{{ __('owner.swal.confirm_title') }}',
            text: '{{ __('owner.swal.confirm_text') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __('owner.swal.confirm_yes') }}',
            cancelButtonText: '{{ __('owner.swal.cancel') }}'
        };
    </script>
    <script src="{{ asset('dashboard/assets/js/owner/boats.js') }}?v={{ filemtime(public_path('dashboard/assets/js/owner/boats.js')) }}"></script>
    <script src="{{ asset('dashboard/assets/js/owner/boat-crew.js') }}?v={{ filemtime(public_path('dashboard/assets/js/owner/boat-crew.js')) }}"></script>
    <script src="{{ asset('dashboard/assets/js/owner/maintenance.js') }}?v={{ filemtime(public_path('dashboard/assets/js/owner/maintenance.js')) }}"></script>
    <script src="{{ asset('dashboard/assets/js/owner/inspection.js') }}?v={{ filemtime(public_path('dashboard/assets/js/owner/inspection.js')) }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const expenses = {{ $expenses }};
        const revenues = {{ $revenues }};

        document.addEventListener('DOMContentLoaded', function() {
            let chartsInitialized = false;

            function initBoatAnalyticsCharts() {
                if (chartsInitialized) {
                    return;
                }
                chartsInitialized = true;

                const expensesCategories = @json($expensesCategories);
                const labels = Object.keys(expensesCategories);
                const data = Object.values(expensesCategories);

                // Expenses Distribution Chart (Doughnut)
                new Chart(document.getElementById('expensesDistributionChart'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#0d6efd', '#ffc107', '#dc3545', '#20c997', '#6f42c1',
                            '#17a2b8'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: false, scrollX: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = Number(context.parsed).toLocaleString();
                                    return label + ': ' + value + ' {{ __('owner.units.sar') }}';
                                }
                            }
                        }
                    }
                }
            });

            // Income vs Expenses Chart (Bar)
            new Chart(document.getElementById('incomeVsExpensesChart'), {
                type: 'bar',
                data: {
                    labels: ['{{ __('owner.boats.revenues') }}', '{{ __('owner.boats.expenses') }}'],
                    datasets: [{
                        label: '{{ __('owner.units.sar') }}',
                        data: [revenues, expenses],
                        backgroundColor: ['rgba(40, 167, 69, 0.7)', 'rgba(220, 53, 69, 0.7)'],
                        borderColor: ['rgba(40, 167, 69, 1)', 'rgba(220, 53, 69, 1)'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: false, scrollX: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + Number(context.parsed.y)
                                        .toLocaleString() + ' {{ __('owner.units.sar') }}';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            }

            const analyticsTab = document.getElementById('analytics-tab');
            if (analyticsTab) {
                analyticsTab.addEventListener('shown.bs.tab', initBoatAnalyticsCharts);

                // In case the Analytics tab is already active on load
                if (analyticsTab.classList.contains('active')) {
                    initBoatAnalyticsCharts();
                }
            }
        });
    </script>
@endsection
