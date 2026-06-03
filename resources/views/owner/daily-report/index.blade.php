@extends('owner.layouts.master')

@section('title')
{{ __('owner.generated.daily_report') }}
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="bi bi-calendar2-day-fill me-2 text-primary"></i>{{ __('owner.generated.daily_report') }}</h4>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary"><i class="bi bi-download me-1"></i> {{ __('owner.generated.export') }}CSV</button>
            <button class="btn btn-outline-primary"><i class="bi bi-printer me-1"></i> {{ __('owner.generated.print_report') }}</button>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-funnel-fill me-2 text-muted"></i>{{ __('owner.generated.report_filters') }}</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('owner.reports.report_date') }}</label>
                    <input type="date" class="form-control" value="2025-07-29">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('owner.payrolls.table.boat') }}</label>
                    <select class="form-select">
                        <option>{{ __('owner.generated.all_ships') }}</option>
                        <option>Al Bahar 1</option>
                        <option>Al Bahar 2</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('owner.generated.report_type') }}</label>
                    <select class="form-select">
                        <option>{{ __('owner.generated.summary') }}</option>
                        <option>{{ __('owner.generated.detailed') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <!-- Cards Summary -->
    <div class="row row-cols-1 row-cols-md-4 g-3 mb-4 text-white">
        <div class="col">
            <div class="card bg-success shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-cash-stack me-2"></i>{{ __('owner.generated.total_revenue') }}</h6>
                    <h4 class="fw-bold text-white"> {{ __('owner.generated.amount_0_sar') }}</h4>
                    <small class="text-white">{{ __('owner.generated.trips_0') }}• {{ __('owner.generated.amount_0_sar') }}/{{ __('owner.boats.trip') }}</small>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card bg-danger shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-graph-down me-2"></i>{{ __('owner.dashboard.net_profit') }}</h6>
                    <h4 class="fw-bold text-white">{{ __('owner.generated.amount_0_sar') }}</h4>
                    <small class="text-white">{{ __('owner.generated.margin_00') }}%</small>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card bg-primary shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-basket2-fill me-2"></i>{{ __('owner.reports.total_catch') }}</h6>
                    <h4 class="fw-bold text-white"> {{ __('owner.generated.weight_0_lbs') }}</h4>
                    <small class="text-white">{{ __('owner.generated.fish_0') }}• {{ __('owner.generated.amount_0_sar') }}/{{ __('owner.generated.lbs') }}</small>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card bg-warning shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-activity me-2"></i>{{ __('owner.generated.active_operations') }}</h6>
                    <h4 class="fw-bold text-white">0</h4>
                    <small class="text-white">{{ __('owner.generated.ships_0') }}• {{ __('owner.generated.trips_0') }}</small>
                </div>
            </div>
        </div>
    </div>


    <ul class="nav nav-tabs mb-4" id="dailyReportTabs" role="tablist">
        <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link active w-100" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                <i class="bi bi-grid-fill me-1"></i> {{ __('owner.boats.show.overview') }}</button>
        </li>
        <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link w-100" id="catches-tab" data-bs-toggle="tab" data-bs-target="#catches" type="button" role="tab">
                <i class="bi bi-basket-fill me-1"></i> {{ __('owner.dashboard.catch') }}</button>
        </li>
        <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link w-100" id="expenses-tab" data-bs-toggle="tab" data-bs-target="#expenses" type="button" role="tab">
                <i class="bi bi-receipt-cutoff me-1"></i> {{ __('owner.payrolls.table.total_expenses') }}</button>
        </li>
        <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link w-100" id="vessels-tab" data-bs-toggle="tab" data-bs-target="#vessels" type="button" role="tab">
                <i class="bi bi-boat-fill me-1"></i> {{ __('owner.generated.ships') }}</button>
        </li>
        <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link w-100" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab">
                <i class="bi bi-bar-chart-fill me-1"></i> {{ __('owner.catch.tabs.analytics') }}</button>
        </li>
    </ul>

    <div class="tab-content" id="dailyReportTabsContent">
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
                    <div class="col">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <h6 class="fw-bold mb-2"><i class="bi bi-basket2-fill me-2"></i>{{ __('owner.generated.catch_detail') }}</h6>
                                <p class="text-muted mb-0">{{ __('owner.generated.no_catch_data_for_date') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <h6 class="fw-bold mb-2"><i class="bi bi-receipt-cutoff me-2"></i>{{ __('owner.generated.expenses_detail') }}</h6>
                                <p class="text-muted mb-0">{{ __('owner.generated.no_expenses_data_for_date') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-md-4 g-3">
                    <div class="col">
                        <div class="card text-white text-center shadow-sm">
                            <div class="card-body">
                                <h6 class="mb-1">{{ __('owner.trips.title') }}</h6>
                                <h4 class="fw-bold">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card text-white text-center shadow-sm">
                            <div class="card-body">
                                <h6 class="mb-1">{{ __('owner.generated.caught_species') }}</h6>
                                <h4 class="fw-bold">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card text-white text-center shadow-sm">
                            <div class="card-body">
                                <h6 class="mb-1">{{ __('owner.generated.active_ships') }}</h6>
                                <h4 class="fw-bold">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card text-white text-center shadow-sm">
                            <div class="card-body">
                                <h6 class="mb-1">{{ __('owner.generated.expenses_entries') }}</h6>
                                <h4 class="fw-bold">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="tab-pane fade" id="catches" role="tabpanel" aria-labelledby="catches-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-basket-fill me-2"></i>{{ __('owner.generated.catch_logs') }}- 2025-07-29</h5>
                <button class="btn btn-outline-primary">
                    <i class="bi bi-download me-1"></i> {{ __('owner.generated.export') }}</button>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-info-circle fs-1 mb-3 d-block"></i>
                    <h6 class="mb-2">{{ __('owner.generated.no_catch_records_2025') }}-07-29</h6>
                </div>
            </div>

            <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-line me-2"></i>{{ __('owner.generated.species_performance') }}</h5>

            <div class="card shadow-sm border-0">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-bar-chart-line fs-1 mb-3 d-block"></i>
                    <h6 class="mb-2">{{ __('owner.generated.no_species_performance_data') }}</h6>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="expenses" role="tabpanel" aria-labelledby="expenses-tab">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-receipt-cutoff me-2"></i>{{ __('owner.generated.expenses_records') }}- 2025-07-29</h5>
                <button class="btn btn-outline-primary">
                    <i class="bi bi-download me-1"></i> {{ __('owner.generated.export') }}</button>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-info-circle fs-1 mb-3 d-block"></i>
                    <h6 class="mb-2">{{ __('owner.generated.no_expenses_records_2025') }}-07-29</h6>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="vessels" role="tabpanel" aria-labelledby="vessels-tab">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-boat-fill me-2"></i>{{ __('owner.generated.ship_records') }}- 2025-07-29</h5>
                <button class="btn btn-outline-primary">
                    <i class="bi bi-download me-1"></i> {{ __('owner.generated.export') }}</button>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-info-circle fs-1 mb-3 d-block"></i>
                    <h6 class="mb-2">{{ __('owner.generated.no_ship_records_2025') }}-07-29</h6>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="analytics" role="tabpanel" aria-labelledby="analytics-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-graph-up-arrow me-2"></i>{{ __('owner.generated.today_analytics') }}</h5>
                <button class="btn btn-outline-success">
                    <i class="bi bi-download me-1"></i> {{ __('owner.generated.export') }}</button>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-line-fill me-2"></i>{{ __('owner.generated.activity_by_hour') }}- {{ __('owner.generated.catch_and_revenue') }}</h6>
                    <div style="height: 250px;">
                        <canvas id="hourlyActivityChart"></canvas>
                    </div>
                </div>
            </div>


            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="bi bi-speedometer2 me-2"></i>{{ __('owner.dashboard.kpis') }}</h6>
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ __('owner.generated.revenue_goal_achievement') }}</span>
                                    <span class="fw-bold text-success">85%</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ __('owner.generated.cost_efficiency') }}</span>
                                    <span class="fw-bold text-primary">{{ __('owner.generated.good') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ __('owner.generated.vessels_utilization') }}</span>
                                    <span class="fw-bold text-warning">75%</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb-fill me-2"></i>{{ __('owner.generated.key_insights') }}</h6>
                            <ul class="list-unstyled small mb-0">
                                <li class="mb-2"><strong>{{ __('owner.generated.top_performing_species') }}</strong> {{ __('owner.reports.not_available') }}</li>
                                <li class="mb-2"><strong>{{ __('owner.generated.highest_revenue_ship') }}</strong> {{ __('owner.reports.not_available') }}</li>
                                <li><strong>{{ __('owner.generated.avg_price_per_lb') }}</strong> {{ __('owner.generated.amount_0_sar') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('hourlyActivityChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                "01:00", "02:00", "03:00", "04:00", "05:00", "06:00", "07:00",
                "09:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00",
                "19:00", "20:00", "22:00", "23:00"
            ],
            datasets: [{
                    label: 'Catches',
                    data: [0, 2, 3, 1, 4, 2, 1, 0, 2, 3, 2, 4, 3, 2, 1, 0, 0, 0],
                    backgroundColor: '#0d6efd'
                },
                {
                    label: 'Revenue (SAR)',
                    data: [0, 350, 700, 200, 1400, 800, 500, 0, 300, 600, 850, 1100, 1300, 800, 400, 100, 0, 0],
                    backgroundColor: '#20c997'
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'SAR ' + value;
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label === 'Revenue (SAR)') {
                                return label + ': SAR ' + context.parsed.y;
                            }
                            return label + ': ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
