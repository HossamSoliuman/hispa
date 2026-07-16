@extends('gov.layouts.master')

@section('title', __('gov.menu.dashboard'))

@section('css')
    <style>
        .gov-dashboard-shell {
            margin-inline: auto;
            max-width: 80rem;
        }

        .gov-banner {
            align-items: center;
            background: linear-gradient(115deg, #188b68 0%, #229a77 100%);
            border: 1px solid rgba(7, 86, 59, .1);
            border-radius: 7px;
            box-shadow: 0 10px 28px rgba(21, 111, 82, .12);
            color: #fff;
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            justify-content: space-between;
            min-height: 5.25rem;
            overflow: hidden;
            padding: 1rem 1.5rem;
            position: relative;
        }

        [data-bs-theme='dark'] .gov-banner {
            background: linear-gradient(115deg, rgba(17, 72, 75, .94) 0%, rgba(11, 50, 67, .96) 100%);
            border-color: rgba(var(--gov-green-rgb), .28);
            box-shadow: 0 16px 36px rgba(0, 0, 0, .25), 0 0 30px rgba(var(--gov-green-rgb), .07);
        }

        .gov-banner::before {
            background-image:
                linear-gradient(90deg, rgba(255, 255, 255, .045) 1px, transparent 1px),
                linear-gradient(rgba(255, 255, 255, .045) 1px, transparent 1px);
            background-size: 24px 24px;
            content: '';
            inset: 0;
            pointer-events: none;
            position: absolute;
        }

        .gov-banner::after {
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 50%;
            content: '';
            height: 10rem;
            inset-inline-end: -4.25rem;
            pointer-events: none;
            position: absolute;
            top: -7rem;
            width: 10rem;
        }

        .gov-banner > * { position: relative; z-index: 1; }

        .gov-banner-title {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 800;
            line-height: 1.35;
            margin: 0 0 .2rem;
        }

        .gov-banner-sub {
            font-size: .78rem;
            margin: 0;
            opacity: .88;
        }

        .gov-banner-time {
            border-inline-start: 1px solid rgba(255, 255, 255, .25);
            font-size: .76rem;
            line-height: 1.75;
            min-width: 12rem;
            padding-inline-start: 1.25rem;
            text-align: start;
        }

        .gov-banner-time .t { font-weight: 700; }

        .gov-card-grid > [class*='col-'] {
            animation: gov-card-enter .48s both cubic-bezier(.22, .75, .25, 1);
        }

        .gov-card-grid > [class*='col-']:nth-child(2) { animation-delay: .04s; }
        .gov-card-grid > [class*='col-']:nth-child(3) { animation-delay: .08s; }
        .gov-card-grid > [class*='col-']:nth-child(4) { animation-delay: .12s; }
        .gov-card-grid > [class*='col-']:nth-child(5) { animation-delay: .16s; }
        .gov-card-grid > [class*='col-']:nth-child(6) { animation-delay: .2s; }

        .gov-stat {
            --stat-accent: #168a59;
            --stat-accent-rgb: 22, 138, 89;
            --stat-tail: #e6a526;
            background: linear-gradient(145deg, rgba(var(--stat-accent-rgb), .09), var(--gov-surface) 38%);
            border: 1px solid rgba(var(--stat-accent-rgb), .34);
            border-radius: 7px;
            box-shadow: 0 14px 30px var(--gov-shadow), 0 0 22px rgba(var(--stat-accent-rgb), .09);
            color: var(--gov-ink);
            height: 100%;
            min-height: 10.4rem;
            overflow: hidden;
            padding: 1.15rem 1.1rem 1rem;
            position: relative;
            transition: box-shadow .2s ease, transform .2s ease;
        }

        .gov-stat::before {
            background: radial-gradient(circle, rgba(var(--stat-accent-rgb), .13), transparent 68%);
            border: 1px solid rgba(var(--stat-accent-rgb), .12);
            border-radius: 50%;
            content: '';
            height: 9rem;
            inset-inline-end: -5rem;
            pointer-events: none;
            position: absolute;
            top: -5.25rem;
            width: 9rem;
        }

        .gov-stat::after {
            background: linear-gradient(90deg, var(--stat-accent) 0 76%, var(--stat-tail) 76% 100%);
            box-shadow: 0 0 16px rgba(var(--stat-accent-rgb), .46);
            content: '';
            height: 4px;
            inset: 0 0 auto;
            pointer-events: none;
            position: absolute;
        }

        [data-bs-theme='dark'] .gov-stat {
            background: linear-gradient(145deg, rgba(var(--stat-accent-rgb), .14), rgba(12, 36, 51, .97) 40%);
            border-color: rgba(var(--stat-accent-rgb), .42);
            box-shadow:
                0 18px 38px rgba(0, 0, 0, .28),
                0 0 26px rgba(var(--stat-accent-rgb), .13),
                inset 0 1px 0 rgba(255, 255, 255, .035);
        }

        .gov-stat:hover {
            border-color: rgba(var(--stat-accent-rgb), .58);
            box-shadow: 0 20px 42px var(--gov-shadow), 0 0 32px rgba(var(--stat-accent-rgb), .18);
            transform: translateY(-3px);
        }

        .gov-stat--green { --stat-accent: #2bc586; --stat-accent-rgb: 43, 197, 134; }
        .gov-stat--cyan  { --stat-accent: #24c7dc; --stat-accent-rgb: 36, 199, 220; }
        .gov-stat--blue  { --stat-accent: #3695f6; --stat-accent-rgb: 54, 149, 246; }
        .gov-stat--red   { --stat-accent: #ef5867; --stat-accent-rgb: 239, 88, 103; }
        .gov-stat--teal  { --stat-accent: #35d2a0; --stat-accent-rgb: 53, 210, 160; }
        .gov-stat--gold  { --stat-accent: #f2b339; --stat-accent-rgb: 242, 179, 57; --stat-tail: #3695f6; }

        .gov-stat-top {
            align-items: flex-start;
            display: flex;
            gap: .75rem;
            justify-content: space-between;
            position: relative;
        }

        .gov-stat-label {
            color: var(--gov-muted);
            font-size: .76rem;
            font-weight: 700;
            margin-bottom: .45rem;
        }

        .gov-stat-value {
            align-items: baseline;
            display: flex;
            font-size: 1.72rem;
            font-weight: 800;
            gap: .3rem;
            line-height: 1;
        }

        .gov-stat-value .unit {
            font-size: .84rem;
            font-weight: 700;
            opacity: .86;
        }

        .gov-stat-ico {
            align-items: center;
            background: rgba(var(--stat-accent-rgb), .12);
            border: 1px solid rgba(var(--stat-accent-rgb), .25);
            border-radius: 6px;
            box-shadow: inset 0 0 18px rgba(var(--stat-accent-rgb), .06);
            color: var(--stat-accent);
            display: flex;
            flex-shrink: 0;
            font-size: 1.3rem;
            height: 2.8rem;
            justify-content: center;
            width: 2.8rem;
        }

        .gov-stat-foot {
            display: flex;
            gap: .35rem;
            margin-top: 1rem;
            position: relative;
        }

        .gov-stat-cell {
            background: rgba(var(--stat-accent-rgb), .06);
            border: 1px solid rgba(var(--stat-accent-rgb), .13);
            border-radius: 4px;
            flex: 1;
            min-width: 0;
            padding: .38rem .45rem;
            text-align: center;
        }

        .gov-stat-cell .k {
            color: var(--gov-muted);
            display: block;
            font-size: .65rem;
        }

        .gov-stat-cell .v {
            display: block;
            font-size: .75rem;
            font-weight: 700;
            margin-top: .1rem;
        }

        .gov-stat-bar {
            align-items: center;
            background: rgba(var(--stat-accent-rgb), .07);
            border: 1px solid rgba(var(--stat-accent-rgb), .14);
            border-radius: 4px;
            display: flex;
            font-size: .69rem;
            font-weight: 600;
            gap: .35rem;
            justify-content: center;
            margin-top: .55rem;
            padding: .35rem .6rem;
            position: relative;
        }

        .gov-stat-progress {
            margin-top: .9rem;
            position: relative;
        }

        .gov-stat-progress .lbl {
            color: var(--gov-muted);
            display: flex;
            font-size: .65rem;
            justify-content: space-between;
            margin-bottom: .28rem;
        }

        .gov-stat-progress .track {
            background: rgba(var(--stat-accent-rgb), .15);
            border-radius: 1rem;
            height: .38rem;
            overflow: hidden;
        }

        .gov-stat-progress .fill {
            background: var(--stat-accent);
            border-radius: 1rem;
            box-shadow: 0 0 12px rgba(var(--stat-accent-rgb), .45);
            height: 100%;
        }

        .gov-chart-card .card-body { padding: 1.25rem 1.35rem; }

        .gov-chart-title {
            color: var(--gov-ink);
            font-size: .98rem;
            font-weight: 800;
            margin: 0;
        }

        .gov-chart-wrap {
            height: 18rem;
            position: relative;
        }

        html[dir='rtl'] .gov-card-trips { order: 1; }
        html[dir='rtl'] .gov-card-sailors { order: 2; }
        html[dir='rtl'] .gov-card-production { order: 3; }
        html[dir='rtl'] .gov-card-sales { order: 4; }
        html[dir='rtl'] .gov-card-seasons { order: 5; }
        html[dir='rtl'] .gov-card-ports { order: 6; }

        @keyframes gov-card-enter {
            from {
                opacity: 0;
                transform: translateY(.75rem);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 575.98px) {
            .gov-banner { align-items: flex-start; padding: 1rem; }
            .gov-banner-title { font-size: 1.05rem; }
            .gov-banner-time {
                border-inline-start: 0;
                border-top: 1px solid rgba(255, 255, 255, .2);
                min-width: 100%;
                padding-inline-start: 0;
                padding-top: .65rem;
            }
            .gov-chart-wrap { height: 15rem; }
        }
    </style>
@endsection

@section('content')
    <div class="gov-dashboard-shell">
    <div class="gov-banner mb-3">
        <div>
            <h1 class="gov-banner-title">{{ __('gov.dashboard.banner_title') }}</h1>
            <p class="gov-banner-sub">{{ __('gov.dashboard.banner_subtitle') }}</p>
        </div>
        <div class="gov-banner-time">
            <div><span class="t">{{ __('gov.dashboard.riyadh_time') }}:</span> {{ $riyadhTime }}</div>
            <div>{{ $hijriDate }}</div>
        </div>
    </div>

    <div class="row g-3 gov-card-grid">
        {{-- Production --}}
        <div class="col-xl-4 col-md-6 gov-card-production">
            <div class="gov-stat gov-stat--green">
                <div class="gov-stat-top">
                    <div>
                        <div class="gov-stat-label">{{ __('gov.dashboard.production_title') }}</div>
                        <div class="gov-stat-value">{{ number_format($annualProduction, 1) }} <span class="unit">{{ __('gov.dashboard.unit_kg') }}</span></div>
                    </div>
                    <div class="gov-stat-ico"><i class="bi bi-bar-chart-line-fill"></i></div>
                </div>
                <div class="gov-stat-foot">
                    <div class="gov-stat-cell"><span class="k">{{ __('gov.dashboard.monthly') }}</span><span class="v">{{ number_format($monthlyProduction, 1) }} {{ __('gov.dashboard.unit_kg') }}</span></div>
                    <div class="gov-stat-cell"><span class="k">{{ __('gov.dashboard.daily') }}</span><span class="v">{{ number_format($dailyProduction, 1) }} {{ __('gov.dashboard.unit_kg') }}</span></div>
                </div>
                <div class="gov-stat-bar">{{ __('gov.dashboard.value') }}: {{ number_format($productionValue, 1) }} <x-riyal-icon size="14" /></div>
            </div>
        </div>

        {{-- Active sailors --}}
        <div class="col-xl-4 col-md-6 gov-card-sailors">
            <div class="gov-stat gov-stat--cyan">
                <div class="gov-stat-top">
                    <div>
                        <div class="gov-stat-label">{{ __('gov.dashboard.sailors_title') }}</div>
                        <div class="gov-stat-value">{{ number_format($activeSailors) }}</div>
                    </div>
                    <div class="gov-stat-ico"><i class="bi bi-people-fill"></i></div>
                </div>
                <div class="gov-stat-foot">
                    <div class="gov-stat-cell"><span class="k">{{ __('gov.dashboard.registered') }}</span><span class="v">{{ number_format($registeredSailors) }}</span></div>
                    <div class="gov-stat-cell"><span class="k">{{ __('gov.dashboard.foreign') }}</span><span class="v">{{ number_format($foreignSailors) }}</span></div>
                    <div class="gov-stat-cell"><span class="k">{{ __('gov.dashboard.saudi') }}</span><span class="v">{{ number_format($saudiSailors) }}</span></div>
                </div>
            </div>
        </div>

        {{-- Fishing trips --}}
        <div class="col-xl-4 col-md-6 gov-card-trips">
            <div class="gov-stat gov-stat--blue">
                <div class="gov-stat-top">
                    <div>
                        <div class="gov-stat-label">{{ __('gov.dashboard.trips_title') }}</div>
                        <div class="gov-stat-value">{{ number_format($totalTrips) }}</div>
                    </div>
                    <div class="gov-stat-ico"><i class="bi bi-compass-fill"></i></div>
                </div>
                <div class="gov-stat-progress">
                    <div class="lbl"><span>{{ __('gov.dashboard.active_now') }}</span><span>{{ $activeTripsPercent }}%</span></div>
                    <div class="track"><div class="fill" style="width: {{ $activeTripsPercent }}%"></div></div>
                </div>
                <div class="gov-stat-foot">
                    <div class="gov-stat-cell"><span class="k">{{ __('gov.dashboard.annually') }}</span><span class="v">{{ number_format($annualTrips) }}</span></div>
                    <div class="gov-stat-cell"><span class="k">{{ __('gov.dashboard.monthly') }}</span><span class="v">{{ number_format($monthlyTrips) }}</span></div>
                </div>
            </div>
        </div>

        {{-- Ports --}}
        <div class="col-xl-4 col-md-6 gov-card-ports">
            <div class="gov-stat gov-stat--red">
                <div class="gov-stat-top">
                    <div>
                        <div class="gov-stat-label">{{ __('gov.dashboard.ports_title') }}</div>
                        <div class="gov-stat-value">{{ number_format($totalPorts) }}</div>
                    </div>
                    <div class="gov-stat-ico"><i class="bi bi-geo-alt-fill"></i></div>
                </div>
                <div class="gov-stat-foot">
                    <div class="gov-stat-cell"><span class="k">{{ __('gov.dashboard.private') }}</span><span class="v">{{ number_format($privatePorts) }}</span></div>
                    <div class="gov-stat-cell"><span class="k">{{ __('gov.dashboard.government') }}</span><span class="v">{{ number_format($govPorts) }}</span></div>
                </div>
                <div class="gov-stat-bar">{{ __('gov.dashboard.total_active_ports') }}</div>
            </div>
        </div>

        {{-- Fishing seasons --}}
        <div class="col-xl-4 col-md-6 gov-card-seasons">
            <div class="gov-stat gov-stat--teal">
                <div class="gov-stat-top">
                    <div>
                        <div class="gov-stat-label">{{ __('gov.dashboard.seasons_title') }}</div>
                        <div class="gov-stat-value">{{ number_format($activeSeasons) }}</div>
                    </div>
                    <div class="gov-stat-ico"><i class="bi bi-calendar3-event"></i></div>
                </div>
                <div class="gov-stat-bar">{{ __('gov.dashboard.total_active_seasons') }}</div>
            </div>
        </div>

        {{-- Sales --}}
        <div class="col-xl-4 col-md-6 gov-card-sales">
            <div class="gov-stat gov-stat--gold">
                <div class="gov-stat-top">
                    <div>
                        <div class="gov-stat-label">{{ __('gov.dashboard.sales_title') }}</div>
                        <div class="gov-stat-value">{{ number_format($totalSales, 2) }} <x-riyal-icon size="20" /></div>
                    </div>
                    <div class="gov-stat-ico"><i class="bi bi-cash-coin"></i></div>
                </div>
                <div class="gov-stat-foot">
                    <div class="gov-stat-cell"><span class="k">{{ __('gov.dashboard.daily') }}</span><span class="v">{{ number_format($dailySales, 2) }} <x-riyal-icon size="12" /></span></div>
                    <div class="gov-stat-cell"><span class="k">{{ __('gov.dashboard.monthly') }}</span><span class="v">{{ number_format($monthlySales, 2) }} <x-riyal-icon size="12" /></span></div>
                </div>
                <div class="gov-stat-bar">{{ __('gov.dashboard.operations_count') }}: {{ number_format($salesCount) }}</div>
            </div>
        </div>
    </div>

    <div class="card gov-chart-card mt-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-graph-up text-success fs-5"></i>
                <h2 class="gov-chart-title">{{ __('gov.dashboard.production_chart_title') }}</h2>
            </div>
            <div class="gov-chart-wrap">
                <canvas id="govProductionChart" aria-label="{{ __('gov.dashboard.production_chart_title') }}"></canvas>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('dashboard/assets/plugins/chart.js/dist/chart.umd.js') }}"></script>
    <script>
        (function () {
            var ctx = document.getElementById('govProductionChart');
            if (!ctx) { return; }

            var labels = @json($productionTrend['labels']);
            var data = @json($productionTrend['data']);
            var isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            var chartTextColor = isDarkMode ? '#91a8b5' : '#617069';
            var chartGridColor = isDarkMode ? 'rgba(137, 184, 201, .12)' : 'rgba(31, 45, 39, .06)';

            var gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(25, 161, 195, 0.2)');
            gradient.addColorStop(1, 'rgba(25, 161, 195, 0.01)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: @json(__('gov.dashboard.production_series')),
                        data: data,
                        borderColor: '#19a1c3',
                        backgroundColor: gradient,
                        borderWidth: 2.25,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#19a1c3',
                        pointRadius: 0,
                        pointHoverRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'center',
                            labels: {
                                boxHeight: 8,
                                boxWidth: 24,
                                color: chartTextColor,
                                font: { family: 'Tajawal', size: 11, weight: '600' },
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: chartGridColor },
                            ticks: { color: chartTextColor, font: { family: 'Tajawal', size: 10 } },
                        },
                        x: {
                            border: { display: false },
                            grid: { display: false },
                            ticks: { color: chartTextColor, font: { family: 'Tajawal', size: 10 } },
                        },
                    }
                }
            });
        })();
    </script>
@endsection
