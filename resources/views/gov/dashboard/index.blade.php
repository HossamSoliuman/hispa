@extends('gov.layouts.master')

@section('title', __('gov.menu.dashboard'))

@section('css')
    <style>
        .gov-dashboard-shell {
            margin-inline: auto;
            max-width: 86rem;
        }

        .gov-banner {
            --frame-rgb: var(--gov-green-rgb);
            align-items: center;
            background: var(--gov-panel-strong);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(136, 177, 191, .25);
            border-radius: 4px;
            box-shadow: 0 12px 32px var(--gov-shadow), inset 0 1px rgba(255, 255, 255, .025);
            color: var(--gov-ink);
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            justify-content: space-between;
            min-height: 6rem;
            padding: 1.25rem 1.4rem;
            position: relative;
        }

        .gov-banner::before {
            background: var(--gov-green);
            border-radius: 1rem;
            content: '';
            height: 2.5rem;
            inset-inline-start: -.1rem;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
        }

        .gov-banner::after,
        .gov-stat::after,
        .gov-chart-card::after {
            background:
                linear-gradient(rgb(var(--frame-rgb)) 0 0) top left / 16px 2px no-repeat,
                linear-gradient(rgb(var(--frame-rgb)) 0 0) top left / 2px 16px no-repeat,
                linear-gradient(rgb(var(--frame-rgb)) 0 0) top right / 16px 2px no-repeat,
                linear-gradient(rgb(var(--frame-rgb)) 0 0) top right / 2px 16px no-repeat,
                linear-gradient(rgb(var(--frame-rgb)) 0 0) bottom left / 16px 2px no-repeat,
                linear-gradient(rgb(var(--frame-rgb)) 0 0) bottom left / 2px 16px no-repeat,
                linear-gradient(rgb(var(--frame-rgb)) 0 0) bottom right / 16px 2px no-repeat,
                linear-gradient(rgb(var(--frame-rgb)) 0 0) bottom right / 2px 16px no-repeat;
            content: '';
            inset: -1px;
            opacity: .42;
            pointer-events: none;
            position: absolute;
            z-index: 2;
        }

        .gov-banner-title {
            color: var(--gov-ink);
            font-size: 1.35rem;
            font-weight: 800;
            line-height: 1.35;
            margin: 0 0 .3rem;
        }

        .gov-banner-sub {
            font-size: .78rem;
            margin: 0;
            color: var(--gov-muted);
        }

        .gov-banner-time {
            background: rgba(92, 140, 155, .08);
            border: 1px solid rgba(136, 177, 191, .18);
            border-radius: 3px;
            color: var(--gov-muted);
            font-size: .76rem;
            line-height: 1.7;
            min-width: 12rem;
            padding: .6rem .85rem;
            text-align: start;
        }

        .gov-banner-time .t {
            color: var(--gov-ink);
            font-weight: 700;
        }

        .gov-card-grid > [class*='col-'] {
            animation: gov-card-enter .48s both cubic-bezier(.22, .75, .25, 1);
        }

        .gov-card-grid > [class*='col-']:nth-child(2) { animation-delay: .04s; }
        .gov-card-grid > [class*='col-']:nth-child(3) { animation-delay: .08s; }
        .gov-card-grid > [class*='col-']:nth-child(4) { animation-delay: .12s; }
        .gov-card-grid > [class*='col-']:nth-child(5) { animation-delay: .16s; }
        .gov-card-grid > [class*='col-']:nth-child(6) { animation-delay: .2s; }

        .gov-stat {
            --stat-accent: var(--gov-green);
            --stat-accent-rgb: var(--gov-green-rgb);
            --frame-rgb: var(--stat-accent-rgb);
            background: var(--gov-panel);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(136, 177, 191, .22);
            border-radius: 4px;
            box-shadow: 0 10px 26px var(--gov-shadow), inset 0 1px rgba(255, 255, 255, .025);
            color: var(--gov-ink);
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 11rem;
            overflow: hidden;
            padding: 1.2rem;
            position: relative;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .gov-stat:hover {
            border-color: rgba(var(--stat-accent-rgb), .4);
            box-shadow: 0 14px 34px var(--gov-shadow), 0 0 22px rgba(var(--stat-accent-rgb), .07);
            transform: translateY(-2px);
        }

        .gov-stat--green,
        .gov-stat--teal {
            --stat-accent: #39b989;
            --stat-accent-rgb: 57, 185, 137;
        }

        .gov-stat--cyan,
        .gov-stat--blue {
            --stat-accent: #479fc0;
            --stat-accent-rgb: 71, 159, 192;
        }

        .gov-stat--red {
            --stat-accent: #c87872;
            --stat-accent-rgb: 200, 120, 114;
        }

        .gov-stat--gold {
            --stat-accent: #c69a45;
            --stat-accent-rgb: 198, 154, 69;
        }

        .gov-stat-top {
            align-items: flex-start;
            display: flex;
            gap: .75rem;
            justify-content: space-between;
            position: relative;
        }

        .gov-stat-label {
            color: var(--gov-muted);
            font-size: .75rem;
            font-weight: 700;
            margin-bottom: .55rem;
        }

        .gov-stat-value {
            align-items: baseline;
            display: flex;
            font-size: 1.8rem;
            font-weight: 800;
            gap: .3rem;
            line-height: 1.05;
        }

        .gov-stat-value .unit {
            font-size: .84rem;
            font-weight: 700;
            opacity: .86;
        }

        .gov-stat-ico {
            align-items: center;
            background: rgba(var(--stat-accent-rgb), .1);
            border: 1px solid rgba(var(--stat-accent-rgb), .15);
            border-radius: 3px;
            color: var(--stat-accent);
            display: flex;
            flex-shrink: 0;
            font-size: 1.15rem;
            height: 2.55rem;
            justify-content: center;
            width: 2.55rem;
        }

        .gov-stat-foot {
            border-top: 1px solid var(--gov-border);
            display: flex;
            margin-top: auto;
            padding-top: .85rem;
        }

        .gov-stat-cell {
            flex: 1;
            min-width: 0;
            padding: 0 .5rem;
            text-align: center;
        }

        .gov-stat-cell + .gov-stat-cell {
            border-inline-start: 1px solid var(--gov-border);
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
            border-top: 1px solid var(--gov-border);
            color: var(--gov-muted);
            display: flex;
            font-size: .69rem;
            font-weight: 600;
            gap: .35rem;
            justify-content: flex-start;
            margin-top: .75rem;
            padding-top: .7rem;
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
            background: var(--gov-surface-soft);
            border-radius: 1rem;
            height: .3rem;
            overflow: hidden;
        }

        .gov-stat-progress .fill {
            background: var(--stat-accent);
            border-radius: 1rem;
            height: 100%;
        }

        .gov-chart-card .card-body { padding: 1.35rem 1.5rem; }

        .gov-chart-card {
            --frame-rgb: var(--gov-green-rgb);
            background: var(--gov-panel);
            border-color: rgba(136, 177, 191, .22) !important;
            border-radius: 4px;
            box-shadow: 0 14px 36px var(--gov-shadow) !important;
        }

        .gov-chart-heading {
            border-bottom: 1px solid var(--gov-border);
            margin: 0 0 1rem;
            padding-bottom: 1rem;
        }

        .gov-chart-heading i { color: var(--gov-green); }

        .gov-chart-title {
            color: var(--gov-ink);
            font-size: .98rem;
            font-weight: 800;
            margin: 0;
        }

        .gov-chart-wrap {
            height: 17rem;
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
                min-width: 100%;
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
            <div class="gov-chart-heading d-flex align-items-center gap-2">
                <i class="bi bi-graph-up fs-5"></i>
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
            var chartTextColor = isDarkMode ? '#8fa49d' : '#6f7e77';
            var chartGridColor = isDarkMode ? 'rgba(165, 193, 184, .09)' : 'rgba(28, 43, 37, .055)';

            var gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(57, 185, 137, 0.14)');
            gradient.addColorStop(1, 'rgba(57, 185, 137, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: @json(__('gov.dashboard.production_series')),
                        data: data,
                        borderColor: '#39b989',
                        backgroundColor: gradient,
                        borderWidth: 2.25,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#39b989',
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
                            display: false,
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
