@extends('owner.layouts.master')

@section('title', '{{ __('owner.generated.item_da29f8') }}')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">📊 {{ __('owner.catch.tabs.analytics') }}</h4>
            <p class="text-muted mb-0">{{ __('owner.generated.advanced_reports_analytics') }}</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-filter-circle me-2"></i> {{ __('owner.generated.report_filters') }}</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.dalal_invoices.filters.from_date') }}</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.dalal_invoices.filters.to_date') }}</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.payrolls.table.boat') }}</label>
                    <select class="form-select">
                        <option selected>{{ __('owner.generated.all_vessels') }}</option>
                        <option>{{ __('owner.generated.sea_1') }}</option>
                        <option>{{ __('owner.generated.sea_2') }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle me-1"></i> {{ __('owner.dalal.filters.clear') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-4 g-3 mb-4 text-white">
        <div class="col">
            <div class="card bg-success shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-cash-stack me-2"></i>{{ __('owner.reports.total_revenue') }}</h6>
                    <h4 class="fw-bold text-white"> {{ __('owner.generated.amount_7920_sar') }}</h4>
                    <small class="text-white">{{ __('owner.generated.trips_2') }}• SAR 3960/{{ __('owner.boats.trip') }}</small>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card bg-danger shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-graph-down me-2"></i>{{ __('owner.dashboard.net_profit') }}</h6>
                    <h4 class="fw-bold text-white">-{{ __('owner.generated.amount_15580_sar') }}</h4>
                    <small class="text-white">{{ __('owner.dashboard.margin') }}-196.7%</small>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card bg-primary shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-basket2-fill me-2"></i>{{ __('owner.reports.total_catch') }}</h6>
                    <h4 class="fw-bold text-white">{{ __('owner.generated.weight_770_lbs') }}</h4>
                    <small class="text-white">{{ __('owner.generated.weight_385_lbs') }}/{{ __('owner.boats.trip') }}</small>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card bg-warning shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-credit-card-2-front-fill me-2"></i>{{ __('owner.profit_loss.total_expenses') }}</h6>
                    <h4 class="fw-bold text-white">{{ __('owner.generated.amount_23500_sar') }}</h4>
                    <small class="text-white">{{ __('owner.generated.entries_expenses_2') }}</small>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1">📈 {{ __('owner.generated.financial_analytics') }}</h4>
                <p class="text-muted mb-0">{{ __('owner.generated.overview_revenue_expenses_performance') }}</p>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4" id="financialTabs" role="tablist">
            <li class="nav-item col-3 text-center" role="presentation">
                <button class="nav-link active w-100" id="trends-tab" data-bs-toggle="tab" data-bs-target="#trends" type="button" role="tab">{{ __('owner.generated.financial_trends') }}</button>
            </li>
            <li class="nav-item col-3 text-center" role="presentation">
                <button class="nav-link w-100" id="species-tab" data-bs-toggle="tab" data-bs-target="#species" type="button" role="tab">{{ __('owner.dashboard.fish_analysis') }}</button>
            </li>
            <li class="nav-item col-3 text-center" role="presentation">
                <button class="nav-link w-100" id="vessels-tab" data-bs-toggle="tab" data-bs-target="#vessels" type="button" role="tab">{{ __('owner.generated.vessels_performance') }}</button>
            </li>
            <li class="nav-item col-3 text-center" role="presentation">
                <button class="nav-link w-100" id="expenses-tab" data-bs-toggle="tab" data-bs-target="#expenses" type="button" role="tab">{{ __('owner.generated.expenses_breakdown') }}</button>
            </li>
        </ul>

        <div class="tab-content" id="financialTabsContent">
            <div class="tab-pane fade show active" id="trends" role="tabpanel" aria-labelledby="trends-tab">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill me-2"></i>{{ __('owner.generated.revenue_vs_expenses_over_time') }}</h5>
                        <canvas id="financialChart" height="120"></canvas>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="bi bi-table me-2"></i>{{ __('owner.generated.monthly_financial_summary') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('owner.generated.month') }}</th>
                                        <th>{{ __('owner.payrolls.table.total_revenues') }}</th>
                                        <th>{{ __('owner.generated.expenses') }}</th>
                                        <th>{{ __('owner.generated.profit') }}</th>
                                        <th>{{ __('owner.generated.margin') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ __('owner.generated.january_2024') }}</td>
                                        <td>{{ __('owner.generated.amount_7920_sar') }}</td>
                                        <td>{{ __('owner.generated.amount_23500_sar') }}</td>
                                        <td class="text-danger">-{{ __('owner.generated.amount_15580_sar') }}</td>
                                        <td class="text-danger">-196.7%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="species" role="tabpanel" aria-labelledby="species-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart-fill me-2"></i>{{ __('owner.dashboard.fish_analysis') }}</h5>
                    <button class="btn btn-outline-success"><i class="bi bi-download me-1"></i> {{ __('owner.generated.export') }}</button>
                </div>

                <div class="row">
                    <!-- {{ __('owner.generated.item_d0fd96') }} -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">{{ __('owner.generated.revenue_by_type') }}</h6>
                                <div style="max-width: 100%; height: 250px;">
                                    <canvas id="speciesRevenueChart" style="max-height: 220px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- {{ __('owner.generated.item_3678bb') }} -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill me-2"></i>{{ __('owner.generated.species_performance') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle text-center mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('owner.assets.type') }}</th>
                                                <th>{{ __('owner.expenses.print.quantity') }}</th>
                                                <th>{{ __('owner.assets.weight') }}({{ __('owner.generated.lbs') }})</th>
                                                <th>{{ __('owner.boats.revenue') }}</th>
                                                <th>{{ __('owner.generated.average_price') }}/{{ __('owner.generated.lbs') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ __('owner.generated.hamour') }}</td>
                                                <td>150</td>
                                                <td>330.0</td>
                                                <td>{{ __('owner.generated.amount_396000_sar') }}</td>
                                                <td>{{ __('owner.generated.amount_1200_sar') }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('owner.generated.kanaad') }}</td>
                                                <td>200</td>
                                                <td>440.0</td>
                                                <td>{{ __('owner.generated.amount_396000_sar') }}</td>
                                                <td>{{ __('owner.generated.amount_900_sar') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="vessels" role="tabpanel" aria-labelledby="vessels-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-ship me-2"></i>{{ __('owner.generated.vessels_performance') }}</h5>
                    <button class="btn btn-outline-success"><i class="bi bi-download me-1"></i> {{ __('owner.generated.export') }}</button>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">{{ __('owner.generated.vessels_financial_performance_comparison') }}</h6>
                        <div style="height: 300px;">
                            <canvas id="vesselPerformanceChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="bi bi-table me-2"></i>{{ __('owner.generated.vessels_performance_details') }}</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('owner.payrolls.table.boat') }}</th>
                                        <th>{{ __('owner.generated.trips_count') }}</th>
                                        <th>{{ __('owner.payrolls.table.total_revenues') }}</th>
                                        <th>{{ __('owner.payrolls.table.total_expenses') }}</th>
                                        <th>{{ __('owner.dashboard.profit_label') }}</th>
                                        <th>{{ __('owner.generated.average_revenue') }}/{{ __('owner.boats.trip') }}</th>
                                        <th>{{ __('owner.sales.weight') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ __('owner.generated.sea_1') }}</td>
                                        <td>1</td>
                                        <td>{{ __('owner.generated.amount_396000_sar') }}</td>
                                        <td>{{ __('owner.generated.amount_000_sar') }}</td>
                                        <td>{{ __('owner.generated.amount_396000_sar') }}</td>
                                        <td>{{ __('owner.generated.amount_396000_sar') }}</td>
                                        <td>{{ __('owner.generated.weight_3300_lbs') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('owner.generated.sea_2') }}</td>
                                        <td>1</td>
                                        <td>{{ __('owner.generated.amount_396000_sar') }}</td>
                                        <td>{{ __('owner.generated.amount_000_sar') }}</td>
                                        <td>{{ __('owner.generated.amount_396000_sar') }}</td>
                                        <td>{{ __('owner.generated.amount_396000_sar') }}</td>
                                        <td>{{ __('owner.generated.weight_4400_lbs') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('owner.unknown') }}</td>
                                        <td>0</td>
                                        <td>{{ __('owner.generated.amount_00_sar') }}</td>
                                        <td>{{ __('owner.generated.amount_2350000_sar') }}</td>
                                        <td>-{{ __('owner.generated.amount_2350000_sar') }}</td>
                                        <td>{{ __('owner.generated.amount_00_sar') }}</td>
                                        <td>{{ __('owner.generated.weight_00_lbs') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="expenses" role="tabpanel" aria-labelledby="expenses-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart-fill me-2"></i>{{ __('owner.generated.expenses_analysis_by_category') }}</h5>
                    <button class="btn btn-outline-success"><i class="bi bi-download me-1"></i> {{ __('owner.generated.export') }}</button>
                </div>

                <div class="row g-3">
                    <!-- Chart Section -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">{{ __('owner.generated.expenses_by_category') }}</h6>
                                <canvas id="expenseCategoryChart" height="120" style="max-height: 200px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3"><i class="bi bi-list-ul me-2"></i>{{ __('owner.generated.categories_details') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle text-center mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('owner.expenses.sections.categories.table.category') }}</th>
                                                <th>{{ __('owner.generated.value') }}</th>
                                                <th>% {{ __('owner.generated.of_total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ __('owner.generated.equipment') }}</td>
                                                <td>{{ __('owner.generated.amount_1500000_sar') }}</td>
                                                <td>63.8%</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('owner.generated.supplies') }}</td>
                                                <td>{{ __('owner.generated.amount_850000_sar') }}</td>
                                                <td>36.2%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
    const ctx = document.getElementById('financialChart').getContext('2d');
    const financialChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['{{ __('owner.generated.january_2024') }}'],
            datasets: [{
                    label: '{{ __('owner.generated.item_cd39bd') }}',
                    data: [7920],
                    backgroundColor: '#198754',
                },
                {
                    label: '{{ __('owner.generated.expenses') }}',
                    data: [23500],
                    backgroundColor: '#dc3545',
                },
                {
                    label: '{{ __('owner.generated.profit') }}',
                    data: [-15580],
                    backgroundColor: '#0d6efd',
                }
            ]
        },
        options: {
            responsive: false, scrollX: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + '{{ __('owner.generated.item_93fe61') }}';
                        }
                    }
                }
            }
        }
    });
</script>

<script>
    const speciesCtx = document.getElementById('speciesRevenueChart').getContext('2d');
    const speciesRevenueChart = new Chart(speciesCtx, {
        type: 'pie',
        data: {
            labels: ['{{ __('owner.generated.hamour') }}', '{{ __('owner.generated.kanaad') }}'],
            datasets: [{
                data: [3960, 3960],
                backgroundColor: ['#0d6efd', '#198754'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: false, scrollX: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

<script>
    const vesselChartCtx = document.getElementById('vesselPerformanceChart').getContext('2d');
    new Chart(vesselChartCtx, {
        type: 'bar',
        data: {
            labels: ['{{ __('owner.generated.sea_1') }}', '{{ __('owner.generated.sea_2') }}', '{{ __('owner.generated.item_6b5e6d') }}'],
            datasets: [{
                    label: '{{ __('owner.generated.item_cd39bd') }}',
                    data: [3960, 3960, 0],
                    backgroundColor: '#0d6efd'
                },
                {
                    label: '{{ __('owner.generated.item_698fe7') }}',
                    data: [0, 0, 23500],
                    backgroundColor: '#dc3545'
                },
                {
                    label: '{{ __('owner.generated.item_e3a4db') }}',
                    data: [3960, 3960, -23500],
                    backgroundColor: '#198754'
                }
            ]
        },
        options: {
            responsive: false, scrollX: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<script>
    const ctxExpense = document.getElementById('expenseCategoryChart').getContext('2d');
    new Chart(ctxExpense, {
        type: 'pie',
        data: {
            labels: ['{{ __('owner.generated.equipment') }}', '{{ __('owner.generated.supplies') }}'],
            datasets: [{
                label: 'SAR',
                data: [15000, 8500],
                backgroundColor: ['#0d6efd', '#ffc107'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: false, scrollX: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>


@endsection
