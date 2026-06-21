@extends('owner.layouts.master')
@section('title')
    {{ __('owner.dashboard.title') }}
@endsection
@section('css')
    <style>
        .dashboard-tight .row.gapless {
            margin-bottom: 0;
        }

        .dashboard-tight .section-gap {
            gap: 1rem;
        }

        .dashboard-tight .card {
            margin-bottom: 1rem;
        }

        .dashboard-tight .chart-sm {
            height: 150px;
            position: relative;
            width: 100%;
        }

        .dashboard-tight .chart-md {
            height: 170px;
            position: relative;
            width: 100%;
        }

        .dashboard-tight .chart-sm canvas,
        .dashboard-tight .chart-md canvas {
            height: 100% !important;
            width: 100% !important;
        }

        .dashboard-tight .scroll-sm {
            max-height: 200px;
            overflow-y: auto;
        }

        .dashboard-tight .scroll-md {
            max-height: 240px;
            overflow-y: auto;
        }

        .dashboard-tight .scroll-lg {
            max-height: 260px;
            overflow-y: auto;
        }

        #overview.tab-pane .card-body {
            min-height: 352px;
        }

        @media (max-width: 991.98px) {

            .dashboard-tight .scroll-sm,
            .dashboard-tight .scroll-md,
            .dashboard-tight .scroll-lg {
                max-height: none;
            }

            .dashboard-tight .chart-sm,
            .dashboard-tight .chart-md {
                height: auto;
            }
        }
    </style>
@endsection
@section('content')
    <!-- dashboard-build: 2026-06-21 layout-v3 top-five-cards -->
    <div class="dashboard-tight">
        <div class="row">
            <!-- BEGIN page header -->
            <div class="mb-4">
                {{-- <h1 class="h3 mb-1 fw-bold text-dark">{{ __('owner.dashboard.title') }}</h1> --}}
                {{-- <p class="text-muted mb-0">{{ __('owner.dashboard.subtitle') }}</p> --}}
            </div>
            <!-- END page header -->
            <!-- {{ __('owner.generated.item_cf1b9b') }} -->
            {{--    <div class="mb-3 d-flex gap-2 align-items-center"> --}}
            {{--        <select id="dateFilter" class="form-select w-auto form-select-sm"> --}}
            {{--            <option value="today">{{ __('owner.generated.today') }}</option> --}}
            {{--            <option value="week">{{ __('owner.generated.this_week') }}</option> --}}
            {{--            <option value="month" selected>{{ __('owner.dashboard.this_month') }}</option> --}}
            {{--            <option value="year">{{ __('owner.generated.this_year') }}</option> --}}
            {{--            <option value="custom">{{ __('owner.generated.custom_date') }}</option> --}}
            {{--        </select> --}}

            {{--        <input type="{{ __('owner.generated.input_custom_date_range') }}" /> --}}
            {{--    </div> --}}

        </div>
        <div class="row g-3">

            @php
                $monthPeriodFooter = new \Illuminate\Support\HtmlString(
                    '<span class="fw-semibold d-block">' . e($currentMonthLabel) . '</span>' .
                    '<span class="text-muted">' . e($currentMonthRangeLabel) . '</span>'
                );
            @endphp

            {{-- KPI cards as a 2×2 grid beside the alerts panel --}}
            <div class="col-12 col-lg-8 col-xxl-9">
                <div class="row g-3">

            {{-- KPI cards: shared stat-card component (unified month-status style) --}}
            @include('owner.components.stat-card', [
                'title' => __('owner.dashboard.total_revenue'),
                'value' => new \Illuminate\Support\HtmlString(
                    number_format($totalRevenue, 0) .
                        ' ' .
                        view('components.riyal-icon', [
                            'size' => 'sm',
                            'style' =>
                                'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.25rem;',
                        ])->render()),
                'icon' => 'bi bi-cash-coin',
                'badge' => number_format($percentageChange, 1),
                'footer' => $monthPeriodFooter,
                'colClass' => 'col-sm-6',
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.dashboard.total_catch'),
                'value' =>
                    $totalCatch >= 1000
                        ? number_format($totalCatch / 1000, 2) .
                            ' ' .
                            (__('owner.units.ton') !== 'owner.units.ton' ? __('owner.units.ton') : 't')
                        : number_format($totalCatch, 0) . ' ' . __('owner.units.kg'),
                'icon' => 'bi bi-basket2-fill',
                'footer' => $monthPeriodFooter,
                'colClass' => 'col-sm-6',
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.dashboard.active_boats'),
                'value' => $activeBoats,
                'icon' => 'fas fa-ship',
                'colClass' => 'col-sm-6',
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.dashboard.profit_margin'),
                'value' => number_format($profitMargin, 1) . '%',
                'icon' => 'bi bi-graph-up-arrow',
                'footer' => $monthPeriodFooter,
                'colClass' => 'col-sm-6',
            ])

                </div>
            </div>

            {{-- Alerts panel beside the KPI grid (renders on the left in RTL) --}}
            <div class="col-12 col-lg-4 col-xxl-3">
                @include('owner.dashboard._alerts')
            </div>

        </div>

        <!-- END row -->

        {{-- Client's "أهم 5" landing section (plan §4.3) --}}
        @include('owner.dashboard._top_five')

        <!-- BEGIN Tabs -->
        <div class="card mt-3">
            <div class="card-header border-bottom">
                <ul class="nav nav-tabs card-header-tabs justify-content-start" id="dashboardTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                            type="button" role="tab" aria-controls="overview" aria-selected="true">
                            {{ __('owner.dashboard.tab_overview') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial"
                            type="button" role="tab" aria-controls="financial" aria-selected="false">
                            {{ __('owner.dashboard.tab_financial') }}
                        </button>
                    </li>
                    {{-- <li class="nav-item" role="presentation">
                    <button class="nav-link" id="operations-tab" data-bs-toggle="tab" data-bs-target="#operations"
                        type="button" role="tab" aria-controls="operations" aria-selected="false">
                        {{ __('owner.dashboard.tab_operations') }}
                    </button>
                </li> --}}
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics"
                            type="button" role="tab" aria-controls="analytics" aria-selected="false">
                            {{ __('owner.dashboard.tab_analytics') }}
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body tab-content" id="dashboardTabsContent">
                {{-- تبويب نظرة عامة --}}
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <h5 class="mb-3">{{ __('owner.dashboard.overview_title') }}</h5>
                    <div class="row g-3 mb-3">
                        {{-- الإيرادات والأرباح (Revenue & Profit - Redesigned) --}}
                        <div class="col-lg-5 mb-3">
                            <div class="card shadow-sm">
                                @include('owner.partials._card_arrow')
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            <h5 class="card-title mb-1 fw-bold">
                                                <i class="bi bi-bar-chart-line me-2 text-primary"></i>
                                                {{ __('owner.dashboard.revenue_profit_trend') }}
                                            </h5>
                                            <p class="text-muted small mb-0">{{ __('owner.dashboard.monthly_performance') }}
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block">{{ __('owner.dashboard.this_year') }}</small>
                                            <div class="d-flex gap-3 align-items-baseline justify-content-end mt-1">
                                                <div class="text-end">
                                                    <div class="h6 mb-0 text-success fw-bold" id="summaryRevenue">
                                                        {!! number_format($currentMonthRevenue, 0) .
                                                            ' ' .
                                                            view('components.riyal-icon', [
                                                                'size' => 'sm',
                                                                'style' => 'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.25rem;',
                                                                'class' => 'riyal-inline',
                                                            ])->render() !!}</div>
                                                    <small
                                                        class="text-muted">{{ __('owner.dashboard.revenue_label') }}</small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="h6 mb-0 text-info fw-bold" id="summaryProfit">
                                                        {!! number_format($currentMonthProfit, 0) .
                                                            ' ' .
                                                            view('components.riyal-icon', [
                                                                'size' => 'sm',
                                                                'style' => 'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.25rem;',
                                                                'class' => 'riyal-inline',
                                                            ])->render() !!}</div>
                                                    <small
                                                        class="text-muted">{{ __('owner.dashboard.profit_label') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="chart-md">
                                        <canvas id="revenueProfitChart" height="150"></canvas>
                                    </div>

                                    {{-- KPI Summary Row --}}
                                    <div class="row mt-3 text-center gx-2 border-top pt-3">
                                        <div class="col-4">
                                            <div class="small text-muted mb-1">{{ __('owner.dashboard.total_revenue') }}
                                            </div>
                                            <div class="fw-semibold text-success" id="kpiRevenue">{!! number_format($totalRevenue, 0) .
                                                ' ' .
                                                view('components.riyal-icon', [
                                                    'size' => 'sm',
                                                    'style' => 'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.25rem;',
                                                    'class' => 'riyal-inline',
                                                ])->render() !!}
                                            </div>
                                        </div>

                                        <div class="col-4 border-start border-end">
                                            <div class="small text-muted mb-1">{{ __('owner.dashboard.profit_label') }}
                                            </div>
                                            <div class="fw-semibold text-info" id="kpiProfit">{!! number_format($profit, 0) .
                                                ' ' .
                                                view('components.riyal-icon', [
                                                    'size' => 'sm',
                                                    'style' => 'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.25rem;',
                                                    'class' => 'riyal-inline',
                                                ])->render() !!}</div>
                                        </div>

                                        <div class="col-4">
                                            <div class="small text-muted mb-1">{{ __('owner.dashboard.avg_price_kg') }}
                                            </div>
                                            <div class="fw-semibold text-primary" id="kpiAvgPrice">
                                                {{ number_format($averagePricePerKg, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- تكوين المصيد (Catch Composition - Redesigned) --}}
                        <div class="col-lg-3 mb-3">
                            <div class="card shadow-sm">
                                @include('owner.partials._card_arrow')
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            <h5 class="card-title mb-1 fw-bold">
                                                <i class="bi bi-pie-chart me-2 text-primary"></i>
                                                {{ __('owner.dashboard.catch_composition') }}
                                            </h5>
                                            <p class="text-muted small mb-0">{{ __('owner.dashboard.species_breakdown') }}
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <small
                                                class="text-muted d-block">{{ __('owner.dashboard.total_label') }}</small>
                                            <div class="fw-semibold text-primary" id="catchTotal">
                                                {{ $totalCatch >= 1000 ? number_format($totalCatch / 1000, 2) . ' ' . __('owner.units.ton') : number_format($totalCatch, 0) . ' ' . __('owner.units.kg') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div id="catchCompositionContainer" class="mt-2 scroll-md pe-2">
                                        <ul class="list-unstyled mb-0" id="catchLegend">
                                            <li class="text-center text-muted py-3">{{ __('owner.dashboard.loading') }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- الأنشطة الأخيرة (Recent Activities - Redesigned) --}}
                        <div class="col-lg-4 mb-3">
                            <div class="card shadow-sm">
                                @include('owner.partials._card_arrow')
                                <div class="card-body">
                                    <h5 class="card-title mb-1 fw-bold">
                                        <i class="bi bi-activity me-2 text-primary"></i>
                                        {{ __('owner.dashboard.recent_activities') }}
                                    </h5>
                                    <p class="text-muted small mb-3">{{ __('owner.dashboard.latest_updates') }}</p>

                                    <div class="scroll-lg pe-2">
                                        <ul class="list-group list-group-flush" id="recentActivitiesList">
                                            <li class="text-center text-muted py-3">{{ __('owner.dashboard.loading') }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- تبويب المالية --}}
                <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
                    <h5 class="mb-4 fw-bold">{{ __('owner.dashboard.financial_title') }}</h5>

                    {{-- Financial Summary Cards: unified dashboard HUD stat-card style --}}
                    <div class="row mb-3">
                        @include('owner.components.stat-card', [
                            'title' => __('owner.dashboard.total_revenue'),
                            'value' => new \Illuminate\Support\HtmlString('<span id="financialSummaryRevenue">0</span>'),
                            'icon' => 'bi bi-arrow-up-circle',
                            'colClass' => 'col-md-3 col-sm-6 mb-3',
                        ])

                        @include('owner.components.stat-card', [
                            'title' => __('owner.dashboard.total_expenses'),
                            'value' => new \Illuminate\Support\HtmlString('<span id="financialSummaryExpenses">0</span>'),
                            'icon' => 'bi bi-arrow-down-circle',
                            'colClass' => 'col-md-3 col-sm-6 mb-3',
                        ])

                        @include('owner.components.stat-card', [
                            'title' => __('owner.dashboard.net_profit'),
                            'value' => new \Illuminate\Support\HtmlString('<span id="financialSummaryProfit">0</span>'),
                            'icon' => 'bi bi-cash-stack',
                            'colClass' => 'col-md-3 col-sm-6 mb-3',
                        ])

                        @include('owner.components.stat-card', [
                            'title' => __('owner.dashboard.profit_margin'),
                            'value' => new \Illuminate\Support\HtmlString('<span id="financialSummaryMargin">0%</span>'),
                            'icon' => 'bi bi-percent',
                            'colClass' => 'col-md-3 col-sm-6 mb-3',
                        ])
                    </div>

                    <div class="row g-3">
                        {{-- Expenses Distribution --}}
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm">
                                @include('owner.partials._card_arrow')
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            <h5 class="card-title mb-1 fw-bold">
                                                <i class="bi bi-pie-chart-fill me-2 text-primary"></i>
                                                {{ __('owner.dashboard.expenses_distribution') }}
                                            </h5>
                                            <p class="text-muted small mb-0">
                                                {{ __('owner.dashboard.spending_by_category') }}</p>
                                        </div>
                                    </div>

                                    <div id="categoriesContainer" class="scroll-md pe-2">
                                        <div class="text-center text-muted py-3">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">{{ __('owner.dashboard.loading') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Financial Trend Chart --}}
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm">
                                @include('owner.partials._card_arrow')
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            <h5 class="card-title mb-1 fw-bold">
                                                <i class="bi bi-graph-up me-2 text-primary"></i>
                                                {{ __('owner.dashboard.financial_trend') }}
                                            </h5>
                                            <p class="text-muted small mb-0">
                                                {{ __('owner.dashboard.revenue_vs_expenses') }}</p>
                                        </div>
                                    </div>

                                    <div class="chart-md">
                                        <canvas id="financialTrendChart" height="160"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- تبويب العمليات --}}
                {{-- <div class="tab-pane fade" id="operations" role="tabpanel" aria-labelledby="operations-tab">
                <h5 class="mb-4 fw-bold">{{ __('owner.dashboard.operations_title') }}</h5>

                
                <div class="row g-3 mb-3">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card shadow-sm border-0 border-start border-primary border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-ship text-primary fs-2"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1 small">{{ __('owner.dashboard.total_boats') }}</h6>
                                        <h3 class="mb-0 fw-bold" id="totalBoats">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card shadow-sm border-0 border-start border-success border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-check-circle text-success fs-2"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1 small">{{ __('owner.dashboard.active_now') }}</h6>
                                        <h3 class="mb-0 fw-bold" id="activeBoats">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card shadow-sm border-0 border-start border-warning border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-tools text-warning fs-2"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1 small">{{ __('owner.dashboard.maintenance') }}</h6>
                                        <h3 class="mb-0 fw-bold" id="maintenanceBoats">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card shadow-sm border-0 border-start border-info border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-people text-info fs-2"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1 small">{{ __('owner.dashboard.total_crew') }}</h6>
                                        <h3 class="mb-0 fw-bold" id="totalCrew">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    
                    <div class="col-lg-5 mb-4">
                        <div class="card shadow-sm">
                            @include('owner.partials._card_arrow')
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <h5 class="card-title mb-1 fw-bold">
                                            <i class="bi bi-person-badge me-2 text-primary"></i>
                                            {{ __('owner.dashboard.captain_performance') }}
                                        </h5>
                                        <p class="text-muted small mb-0">{{ __('owner.dashboard.top_performers') }}</p>
                                    </div>
                                </div>

                                <div id="sailorsContainer" class="scroll-md pe-2">
                                    <div class="text-center text-muted py-3">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">{{ __('owner.dashboard.loading') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-lg-7 mb-4">
                        <div class="card shadow-sm">
                            @include('owner.partials._card_arrow')
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <h5 class="card-title mb-1 fw-bold">
                                            <i class="bi bi-calendar3 me-2 text-primary"></i>
                                            {{ __('owner.dashboard.daily_operations') }}
                                        </h5>
                                        <p class="text-muted small mb-0">{{ __('owner.dashboard.trips_and_catch_trend') }}</p>
                                    </div>
                                </div>

                                <div class="chart-md">
                                    <canvas id="operationsChart" height="160"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

                {{-- تبويب التحليلات --}}
                <div class="tab-pane fade" id="analytics" role="tabpanel" aria-labelledby="analytics-tab">
                    <h5 class="mb-4 fw-bold">{{ __('owner.dashboard.analytics_title') }}</h5>

                    {{-- Performance KPIs: unified dashboard HUD stat-card style --}}
                    <div class="row mb-3">
                        @include('owner.components.stat-card', [
                            'title' => __('owner.dashboard.avg_catch_per_trip'),
                            'value' => new \Illuminate\Support\HtmlString('<span id="avgCatchPerTrip">0</span>'),
                            'icon' => 'bi bi-basket3',
                            'colClass' => 'col-lg-3 col-md-6 mb-3',
                        ])

                        @include('owner.components.stat-card', [
                            'title' => __('owner.dashboard.avg_revenue_per_trip'),
                            'value' => new \Illuminate\Support\HtmlString('<span id="avgRevenuePerTrip">0</span>'),
                            'icon' => 'bi bi-currency-dollar',
                            'colClass' => 'col-lg-3 col-md-6 mb-3',
                        ])

                        @include('owner.components.stat-card', [
                            'title' => __('owner.dashboard.trips_per_captain'),
                            'value' => new \Illuminate\Support\HtmlString('<span id="avgTripsPerCaptain">0</span>'),
                            'icon' => 'bi bi-pin-map',
                            'colClass' => 'col-lg-3 col-md-6 mb-3',
                        ])

                        @include('owner.components.stat-card', [
                            'title' => __('owner.dashboard.avg_price_per_kg'),
                            'value' => new \Illuminate\Support\HtmlString('<span id="avgPricePerKg">0</span>'),
                            'icon' => 'bi bi-graph-up-arrow',
                            'colClass' => 'col-lg-3 col-md-6 mb-3',
                        ])
                    </div>

                    <div class="row g-3">
                        {{-- Fish Species Analysis --}}
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm">
                                @include('owner.partials._card_arrow')
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            <h5 class="card-title mb-1 fw-bold">
                                                <i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>
                                                {{ __('owner.dashboard.fish_analysis') }}
                                            </h5>
                                            <p class="text-muted small mb-0">
                                                {{ __('owner.dashboard.by_species_and_value') }}</p>
                                        </div>
                                    </div>

                                    <div id="fish-analysis" class="scroll-md pe-2">
                                        <div class="text-center text-muted py-3">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">{{ __('owner.dashboard.loading') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Comparative Chart --}}
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm">
                                @include('owner.partials._card_arrow')
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            <h5 class="card-title mb-1 fw-bold">
                                                <i class="bi bi-graph-up me-2 text-primary"></i>
                                                {{ __('owner.dashboard.performance_comparison') }}
                                            </h5>
                                            <p class="text-muted small mb-0">{{ __('owner.dashboard.catch_vs_revenue') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="chart-md">
                                        <canvas id="performanceComparisonChart" height="160"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>

        </div>
        <!-- END Tabs -->
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Expose the SAR (Riyal) SVG markup rendered by the server and a helper to color it.
        @php
            $riyalSvgContent = view('components.riyal-icon', [
                'size' => 'sm',
                'style' => 'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.25rem;',
            ])->render();
        @endphp
        const riyalSvg = @json($riyalSvgContent);

        function riyalIconHtml(color) {
            // color can be a hex or CSS variable like 'var(--bs-success)'
            return '<span class="riyal-icon-wrapper" style="color:' + color +
                '; display:inline-block; vertical-align:middle;">' + riyalSvg + '</span>';
        }
    </script>
    <script>
        let revenueProfitChartInstance = null;

        $(document).ready(function() {
            $.ajax({
                url: "{{ route('owner.overview.data') }}", // الرابط اللي يرجع الإيرادات والأرباح
                method: 'GET',
                success: function(res) {

                    // --- Update Catch Composition Legend ---
                    const legend = $('#catchLegend');
                    legend.empty();
                    const colors = ['#007bff', '#28a745', '#fd7e14', '#e83e8c', '#6f42c1', '#17a2b8',
                        '#ffc107'
                    ];

                    if (Array.isArray(res.catchComposition) && res.catchComposition.length) {
                        res.catchComposition.forEach((item, index) => {
                            const color = colors[index % colors.length];
                            const value = Math.round(Number(item.total_value || 0))
                                .toLocaleString();
                            const perc = item.percentage || 0;

                            const li = `
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:${color};display:inline-block;"></span>
                                        <span class="fw-semibold small">${item.fish_name}</span>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold small text-primary">${perc}%</div>
                                        <div class="text-muted" style="font-size:0.7rem;">${value} {{ __('owner.units.sar') }}</div>
                                    </div>
                                </li>
                            `;
                            legend.append(li);
                        });
                    } else {
                        legend.html(
                            '<li class="text-muted text-center small py-3">{{ __('owner.dashboard.no_data') }}</li>'
                        );
                    }

                    // Update catch total if provided
                    if (res.totalCatchKg !== undefined) {
                        let totalText = '';
                        if (res.totalCatchKg >= 1000) {
                            totalText = (res.totalCatchKg / 1000).toFixed(2) +
                                ' {{ __('owner.units.ton') }}';
                        } else {
                            totalText = Math.round(Number(res.totalCatchKg)).toLocaleString() +
                                ' {{ __('owner.units.kg') }}';
                        }
                        $('#catchTotal').text(totalText);
                    }

                    // --- Update KPI Summary ---
                    if (res.summary) {
                        if (res.summary.revenue !== undefined) {
                            const rev = Math.round(Number(res.summary.revenue)).toLocaleString();
                            $('#summaryRevenue').html(rev + ' ' + riyalIconHtml('var(--bs-success)'));
                            $('#kpiRevenue').html(rev + ' ' + riyalIconHtml('var(--bs-success)'));
                        }
                        if (res.summary.profit !== undefined) {
                            const prof = Math.round(Number(res.summary.profit)).toLocaleString();
                            $('#summaryProfit').html(prof + ' ' + riyalIconHtml('var(--bs-success)'));
                            $('#kpiProfit').html(prof + ' ' + riyalIconHtml('var(--bs-success)'));
                        }
                        if (res.summary.avgPricePerKg !== undefined) {
                            $('#kpiAvgPrice').text(Number(res.summary.avgPricePerKg).toFixed(2));
                        }
                    }

                    // --- Build Revenue/Profit Chart ---
                    const ctx = document.getElementById('revenueProfitChart').getContext('2d');
                    if (revenueProfitChartInstance) {
                        revenueProfitChartInstance.destroy();
                    }

                    revenueProfitChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: (res.monthly || []).map(m => m.month_name),
                            datasets: [{
                                    label: "{{ __('owner.dashboard.revenue') }}",
                                    data: (res.monthly || []).map(m => Math.round(m
                                        .revenue || 0)),
                                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                    borderColor: 'rgba(40, 167, 69, 0.9)',
                                    borderWidth: 2,
                                    tension: 0.35,
                                    fill: true,
                                    pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                },
                                {
                                    label: "{{ __('owner.dashboard.profit') }}",
                                    data: (res.monthly || []).map(m => Math.round(m
                                        .profit || 0)),
                                    backgroundColor: 'rgba(23, 162, 184, 0.08)',
                                    borderColor: 'rgba(23, 162, 184, 0.9)',
                                    borderWidth: 2,
                                    tension: 0.35,
                                    fill: true,
                                    pointBackgroundColor: 'rgba(23, 162, 184, 1)',
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                }
                            ]
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
                                    titleFont: {
                                        size: 13
                                    },
                                    bodyFont: {
                                        size: 12
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
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
            });
        });
    </script>
    <script>
        function loadRecentActivities() {
            $.ajax({
                url: "{{ route('owner.recent.activities') }}",
                method: "GET",
                success: function(activities) {
                    if (!Array.isArray(activities)) {
                        activities = Object.values(activities);
                    }
                    let html = '';
                    activities.forEach(function(activity) {
                        html += `
                <li class="list-group-item border-0 d-flex justify-content-between align-items-center">
                    <span><i class="bi ${activity.icon} me-2"></i>${activity.message}</span>
                    <span class="badge ${activity.badge_class}">${activity.time}</span>
                </li>
                `;
                    });
                    $('#recentActivitiesList').html(html);
                },
                error: function() {
                    const errorMsg = "{{ __('owner.dashboard.error_loading_activities') }}";
                    $('#recentActivitiesList').html('<li class="text-danger">' + errorMsg + '</li>');
                }
            });
        }

        // Load activities on page load
        $(document).ready(function() {
            loadRecentActivities();

            // Optional: refresh every 30 seconds
            setInterval(loadRecentActivities, 30000);
        });
    </script>
    <script>
        let financialTrendChartInstance = null;

        $(document).ready(function() {
            $('#financial-tab').on('shown.bs.tab', function() {
                const unitSar = "{{ __('owner.units.sar') }}";
                $.ajax({
                    url: '{{ route('owner.financial.summary') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Update financial summary cards (use SAR SVG icon with colors)
                        $('#financialSummaryRevenue').html(Math.round(Number(data.revenue))
                            .toLocaleString() + ' ' + riyalIconHtml('var(--bs-success)'));
                        $('#financialSummaryExpenses').html(Math.round(Number(data.expenses))
                            .toLocaleString() + ' ' + riyalIconHtml('var(--bs-danger)'));
                        $('#financialSummaryProfit').html(Math.round(Number(data.profit))
                            .toLocaleString() + ' ' + riyalIconHtml('var(--bs-success)'));
                        $('#financialSummaryMargin').text(data.margin + '%');

                        // Build expenses categories
                        const categoriesContainer = $('#categoriesContainer');
                        categoriesContainer.empty();

                        if (data.categories && data.categories.length) {
                            const colors = ['primary', 'success', 'warning', 'danger', 'info',
                                'purple', 'teal'
                            ];

                            data.categories.forEach(function(category, index) {
                                const percent = data.expenses > 0 ?
                                    ((category.expenses_sum_final_price / data
                                        .expenses) * 100).toFixed(1) :
                                    0;
                                const color = colors[index % colors.length];
                                const amount = Math.round(Number(category
                                        .expenses_sum_final_price || 0))
                                    .toLocaleString();

                                categoriesContainer.append(`
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-${color} bg-opacity-10 text-${color}">
                                                    <i class="bi bi-tag-fill"></i>
                                                </span>
                                                <span class="fw-semibold">${category.name}</span>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-${color}">${percent}%</div>
                                                <small class="text-muted">${amount} ${riyalIconHtml('var(--bs-' + color + ')')}</small>
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-${color}" style="width: ${percent}%;"></div>
                                        </div>
                                    </div>
                                `);
                            });
                        } else {
                            categoriesContainer.html(
                                '<p class="text-muted text-center py-3">{{ __('owner.dashboard.no_data') }}</p>'
                            );
                        }

                        // Build financial trend chart
                        const ctx = document.getElementById('financialTrendChart').getContext(
                            '2d');
                        if (financialTrendChartInstance) {
                            financialTrendChartInstance.destroy();
                        }

                        const monthlyData = data.monthly || [];

                        financialTrendChartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: monthlyData.map(m => m.month_name || m.month),
                                datasets: [{
                                        label: "{{ __('owner.dashboard.revenue') }}",
                                        data: monthlyData.map(m => m.revenue || 0),
                                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                                        borderColor: 'rgba(40, 167, 69, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: "{{ __('owner.dashboard.expenses') }}",
                                        data: monthlyData.map(m => m.expenses || 0),
                                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                                        borderColor: 'rgba(220, 53, 69, 1)',
                                        borderWidth: 1
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
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
                                                return context.dataset.label +
                                                    ': ' +
                                                    Number(context.parsed.y)
                                                    .toLocaleString() + ' ' +
                                                    unitSar;
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
                    },
                    error: function(err) {
                        console.error(err);
                        $('#categoriesContainer').html(
                            '<p class="text-danger text-center py-3">{{ __('owner.dashboard.error_loading') }}</p>'
                        );
                    }
                });
            });
        });
    </script>
    <script>
        let operationsChartInstance = null;

        function loadOperationsData() {
            $.ajax({
                url: "{{ route('owner.operations.data') }}",
                method: "GET",
                success: function(res) {
                    const t = {
                        trips: "{{ __('owner.dashboard.trips') }}",
                        catch: "{{ __('owner.dashboard.catch') }}",
                        revenue: "{{ __('owner.dashboard.revenue_short') }}",
                        kg: "{{ __('owner.units.kg') }}",
                        sar: "{{ __('owner.units.sar') }}",
                        efficiency: "{{ __('owner.dashboard.efficiency_badge', ['value' => ':value']) }}",
                        dailyTrips: "{{ __('owner.dashboard.daily_trips_count') }}",
                        dailyCatch: "{{ __('owner.dashboard.daily_catch') }}",
                    };

                    // Update fleet status cards
                    $('#totalBoats').text(res.fleetStatus?.total || 0);
                    $('#activeBoats').text(res.fleetStatus?.active || 0);
                    $('#maintenanceBoats').text(res.fleetStatus?.maintenance || 0);
                    $('#totalCrew').text(res.fleetStatus?.crew || 0);

                    // Build captain performance cards
                    const sailorsContainer = $('#sailorsContainer');
                    sailorsContainer.empty();

                    if (res.sailors && res.sailors.length) {
                        res.sailors.forEach(function(sailor, index) {
                            const efficiencyColor = sailor.efficiency >= 80 ? 'success' :
                                sailor.efficiency >= 60 ? 'warning' : 'danger';
                            const rankBadge = index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ?
                                '🥉' : '';

                            sailorsContainer.append(`
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                    <i class="bi bi-person-circle text-primary fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">${rankBadge} ${sailor.name}</h6>
                                                    <small class="text-muted">${sailor.boat_name || ''}</small>
                                                </div>
                                            </div>
                                            <span class="badge bg-${efficiencyColor}">${sailor.efficiency}%</span>
                                        </div>

                                        <div class="row text-center g-2">
                                            <div class="col-4">
                                                <div class="bg-light rounded p-2">
                                                    <div class="small text-muted">${t.trips}</div>
                                                    <div class="fw-bold text-primary">${sailor.trips}</div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="bg-light rounded p-2">
                                                    <div class="small text-muted">${t.catch}</div>
                                                    <div class="fw-bold text-success">${Number(sailor.catch).toLocaleString()} ${t.kg}</div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="bg-light rounded p-2">
                                                    <div class="small text-muted">${t.revenue}</div>
                                                    <div class="fw-bold text-info">${Number(sailor.revenue).toLocaleString()} ${t.sar}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        sailorsContainer.html(
                            '<p class="text-muted text-center py-3">{{ __('owner.dashboard.no_data') }}</p>'
                        );
                    }

                    // Build operations chart (dual-axis: trips + catch)
                    const ctx = document.getElementById('operationsChart').getContext('2d');
                    if (operationsChartInstance) {
                        operationsChartInstance.destroy();
                    }

                    const dailyOps = res.dailyOperations || [];

                    operationsChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: dailyOps.map(d => d.date),
                            datasets: [{
                                    label: t.dailyTrips,
                                    data: dailyOps.map(d => d.trips_count || 0),
                                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true,
                                    yAxisID: 'y',
                                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                },
                                {
                                    label: t.dailyCatch,
                                    data: dailyOps.map(d => Math.round(d.total_catch || 0)),
                                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                    borderColor: 'rgba(40, 167, 69, 1)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true,
                                    yAxisID: 'y1',
                                    pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                }
                            ]
                        },
                        options: {
                            responsive: false, scrollX: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
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
                                    padding: 12
                                }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    position: 'left',
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: t.trips
                                    },
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    position: 'right',
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: t.catch + ' (' + t.kg + ')'
                                    },
                                    grid: {
                                        drawOnChartArea: false
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
                },
                error: function() {
                    $('#sailorsContainer').html(
                        '<p class="text-danger text-center py-3">{{ __('owner.dashboard.error_loading') }}</p>'
                    );
                }
            });
        }

        $('#operations-tab').on('shown.bs.tab', function() {
            loadOperationsData();
        });
    </script>
    <script>
        let performanceComparisonChartInstance = null;

        $('#analytics-tab').on('shown.bs.tab', function() {
            const unitSar = "{{ __('owner.units.sar') }}";
            const unitKg = "{{ __('owner.units.kg') }}";

            $.ajax({
                url: "{{ route('owner.analytics.data') }}",
                method: 'GET',
                success: function(response) {
                    // Build fish analysis list
                    const fishContainer = $('#fish-analysis');
                    fishContainer.empty();

                    if (response.fishAnalysis && response.fishAnalysis.length) {
                        const colors = ['primary', 'success', 'info', 'warning', 'danger', 'purple',
                            'teal'
                        ];

                        response.fishAnalysis.forEach((fish, index) => {
                            const color = colors[index % colors.length];
                            const value = Math.round(Number(fish.total_value || 0))
                                .toLocaleString();
                            const weight = Math.round(Number(fish.total_weight || 0))
                                .toLocaleString();
                            const percentage = fish.percentage || 0;

                            fishContainer.append(`
                                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded" style="width: 4px; height: 40px; background: var(--bs-${color});"></div>
                                        <div>
                                            <div class="fw-semibold">${fish.fish_name}</div>
                                            <small class="text-muted">${weight} ${unitKg}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-${color}">${percentage}%</div>
                                        <small class="text-muted">${value} ${riyalIconHtml('var(--bs-success)')}</small>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        fishContainer.html(
                            '<p class="text-muted text-center py-3">{{ __('owner.dashboard.no_data') }}</p>'
                        );
                    }

                    // Update KPI metrics
                    if (response.metrics) {
                        $('#avgCatchPerTrip').text(Math.round(Number(response.metrics.avgCatchPerTrip ||
                            0)).toLocaleString());
                        $('#avgRevenuePerTrip').html(Math.round(Number(response.metrics
                            .avgRevenuePerTrip || 0)).toLocaleString() + ' ' + riyalIconHtml(
                            'var(--bs-success)'));
                        $('#avgTripsPerCaptain').text(Number(response.metrics.avgTripsPerCaptain || 0)
                            .toFixed(1));
                        $('#avgPricePerKg').text(Number(response.metrics.avgPricePerKg || 0).toFixed(
                            2));
                    }

                    // Build performance comparison chart
                    const ctx = document.getElementById('performanceComparisonChart').getContext('2d');
                    if (performanceComparisonChartInstance) {
                        performanceComparisonChartInstance.destroy();
                    }

                    const comparisonData = response.comparison || [];

                    performanceComparisonChartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: comparisonData.map(d => d.label || d.month),
                            datasets: [{
                                    label: "{{ __('owner.dashboard.catch') }} (" + unitKg +
                                        ")",
                                    data: comparisonData.map(d => d.catch || 0),
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1,
                                    yAxisID: 'y'
                                },
                                {
                                    label: "{{ __('owner.dashboard.revenue') }} (" +
                                        unitSar + ")",
                                    data: comparisonData.map(d => d.revenue || 0),
                                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                                    borderColor: 'rgba(40, 167, 69, 1)',
                                    borderWidth: 1,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: false, scrollX: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
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
                                            return context.dataset.label + ': ' +
                                                Number(context.parsed.y).toLocaleString();
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    position: 'left',
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: "{{ __('owner.dashboard.catch') }}"
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString();
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    position: 'right',
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: "{{ __('owner.dashboard.revenue') }}"
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString();
                                        }
                                    },
                                    grid: {
                                        drawOnChartArea: false
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
                },
                error: function() {
                    $('#fish-analysis').html(
                        '<p class="text-danger text-center py-3">{{ __('owner.dashboard.error_loading') }}</p>'
                    );
                }
            });
        });
    </script>
    <script>
        // Owner alerts card: optional background refresh (mirrors _alert_row markup).
        (function() {
            const listEl = document.getElementById('ownerAlertsList');
            const badgeEl = document.getElementById('ownerAlertsBadge');
            if (!listEl) return;

            const MAX_VISIBLE = {{ (int) config('alerts.max_visible', 6) }};
            const badgeTpl = @json(__('owner.alerts.count_badge', ['count' => '__COUNT__']));
            const allClearHtml =
                `<div class="text-center text-muted py-4"><i class="bi bi-check-circle-fill text-success fs-2 d-block mb-2"></i><span class="small">{{ __('owner.alerts.all_clear') }}</span></div>`;

            function esc(s) {
                return String(s ?? '').replace(/[&<>"']/g, c => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [c]));
            }

            function rowHtml(a) {
                const color = a.severity_color;
                const tag = a.url ? 'a' : 'div';
                const href = a.url ? ` href="${esc(a.url)}"` : '';
                const due = a.due_for_humans ?
                    `<span class="d-block text-${color}" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>${esc(a.due_for_humans)}</span>` :
                    '';
                return `<${tag}${href} class="alert-row d-flex gap-2 align-items-start py-2 text-decoration-none">` +
                    `<span class="alert-bar bg-${color}"></span>` +
                    `<span class="text-${color} flex-shrink-0 pt-1"><i class="bi ${esc(a.icon)}"></i></span>` +
                    `<span class="flex-grow-1 min-w-0">` +
                    `<span class="d-block fw-semibold small text-body">${esc(a.title)}</span>` +
                    `<span class="d-block text-muted lh-sm" style="font-size:.75rem;">${esc(a.message)}</span>` +
                    due + `</span></${tag}>`;
            }

            function refresh() {
                $.ajax({
                    url: "{{ route('owner.alerts.data') }}",
                    method: 'GET',
                    success: function(res) {
                        const alerts = res.alerts || [];
                        const summary = res.summary || {
                            total: 0,
                            critical: 0,
                            warning: 0
                        };

                        listEl.innerHTML = alerts.length ?
                            alerts.slice(0, MAX_VISIBLE).map(rowHtml).join('') :
                            allClearHtml;

                        if (badgeEl) {
                            if (summary.total > 0) {
                                badgeEl.className = 'badge rounded-pill ' + (summary.critical > 0 ?
                                    'bg-danger' : (summary.warning > 0 ? 'bg-warning text-dark' :
                                        'bg-secondary'));
                                badgeEl.textContent = badgeTpl.replace('__COUNT__', summary.total);
                            } else {
                                badgeEl.classList.add('d-none');
                            }
                        }
                    }
                });
            }

            setInterval(refresh, 60000);
        })();
    </script>
@endsection
