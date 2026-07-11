@extends('admin.layouts.master')
@section('title')
    {{ __('admin.dashboard.title') }}
@endsection

@php
    $riyal = view('components.riyal-icon')->render();
    $money = fn ($amount) => new \Illuminate\Support\HtmlString(
        number_format((float) $amount, 0) . ' <span class="unit">' . $riyal . '</span>'
    );
@endphp

@section('css')
    <style>
        /* Constrain chart canvases: Chart.js with maintainAspectRatio:false
           sizes to its parent, so the wrapper must have an explicit height. */
        .hud-chart-wrap {
            position: relative;
            width: 100%;
            height: 260px;
        }
        @media (max-width: 575.98px) {
            .hud-chart-wrap { height: 220px; }
        }
    </style>
@endsection

@section('content')
    <div class="hud-dashboard">

        {{-- Page header --}}
        <div class="mb-4">
            <h1 class="h4 fw-bold mb-1">{{ __('admin.dashboard.title') }}</h1>
            <p class="mb-0 text-muted small">{{ __('admin.dashboard.subtitle') }}</p>
        </div>

        {{-- ========== Overview KPIs ========== --}}
        <div class="hud-section-head">
            <span class="ico"><i class="bi bi-grid-1x2"></i></span>
            <h5>{{ __('admin.dashboard.overview_section') }}</h5>
            <span class="hud-line"></span>
        </div>

        <div class="row g-3 mb-4">
            @include('admin.components.hud-stat-card', [
                'title'    => __('admin.dashboard.mrr_value'),
                'value'    => $money($mrr),
                'icon'     => 'bi bi-arrow-repeat',
                'colClass' => 'col-6 col-md-4 col-xl-3',
            ])
            @include('admin.components.hud-stat-card', [
                'title'    => __('admin.dashboard.collected_revenue'),
                'value'    => $money($collectedRevenue),
                'icon'     => 'bi bi-cash-stack',
                'colClass' => 'col-6 col-md-4 col-xl-3',
            ])
            @include('admin.components.hud-stat-card', [
                'title'    => __('admin.dashboard.pending_revenue'),
                'value'    => $money($pendingRevenue),
                'icon'     => 'bi bi-hourglass-split',
                'colClass' => 'col-6 col-md-4 col-xl-3',
            ])
            @include('admin.components.hud-stat-card', [
                'title'    => __('admin.dashboard.active_subscriptions'),
                'value'    => number_format($activeSubscriptions),
                'icon'     => 'bi bi-patch-check',
                'colClass' => 'col-6 col-md-4 col-xl-3',
            ])
            @include('admin.components.hud-stat-card', [
                'title'    => __('admin.dashboard.pending_subscriptions'),
                'value'    => number_format($pendingSubscriptions),
                'icon'     => 'bi bi-person-plus',
                'colClass' => 'col-6 col-md-4 col-xl-3',
            ])
            @include('admin.components.hud-stat-card', [
                'title'    => __('admin.dashboard.expiring_soon'),
                'value'    => number_format($expiringSoon->count()),
                'icon'     => 'bi bi-alarm',
                'colClass' => 'col-6 col-md-4 col-xl-3',
            ])
            @include('admin.components.hud-stat-card', [
                'title'    => __('admin.dashboard.total_owners'),
                'value'    => number_format($totalOwners),
                'icon'     => 'bi bi-people',
                'colClass' => 'col-6 col-md-4 col-xl-3',
            ])
            @include('admin.components.hud-stat-card', [
                'title'    => __('admin.dashboard.active_packages'),
                'value'    => number_format($activePackages),
                'icon'     => 'bi bi-box-seam',
                'colClass' => 'col-6 col-md-4 col-xl-3',
            ])
        </div>

        {{-- ========== Revenue & Analytics ========== --}}
        <div class="hud-section-head">
            <span class="ico"><i class="bi bi-graph-up"></i></span>
            <h5>{{ __('admin.dashboard.revenue_analytics_section') }}</h5>
            <span class="hud-line"></span>
        </div>

        <div class="row g-3 mb-4">
            {{-- MRR trend --}}
            <div class="col-12 col-xl-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold mb-1">
                                    <i class="bi bi-graph-up-arrow me-1 text-primary"></i>
                                    {{ __('admin.dashboard.mrr_trend') }}
                                </h6>
                                <span class="text-muted small">{{ __('admin.dashboard.mrr_description') }}</span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{!! $money($currentMonthRevenue) !!}</div>
                                <small class="text-muted d-block">{{ __('admin.dashboard.revenue_this_month') }}</small>
                                @if($revenueGrowth != 0)
                                    <span class="badge {{ $revenueGrowth >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $revenueGrowth >= 0 ? 'text-success' : 'text-danger' }} mt-1">
                                        <i class="bi bi-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }}"></i>
                                        {{ number_format(abs($revenueGrowth), 1) }}% {{ __('admin.dashboard.vs_last_month') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hud-chart-wrap">
                            <canvas id="mrrChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subscriptions by status --}}
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">
                            <i class="bi bi-activity me-1 text-primary"></i>
                            {{ __('admin.dashboard.subscription_status') }}
                        </h6>
                        <span class="text-muted small">{{ __('admin.dashboard.subscription_status_desc') }}</span>

                        @if(($activeSubscriptions + $pendingSubscriptions + $trialSubscriptions + $expiredSubscriptions) > 0)
                            <div class="hud-chart-wrap mt-3">
                                <canvas id="statusChart"></canvas>
                            </div>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-activity fs-1 d-block mb-2 opacity-50"></i>
                                {{ __('admin.dashboard.no_data') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Monthly collected revenue --}}
            <div class="col-12 col-xl-8">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">
                            <i class="bi bi-bar-chart-line me-1 text-primary"></i>
                            {{ __('admin.dashboard.monthly_revenue') }}
                        </h6>
                        <span class="text-muted small">{{ __('admin.dashboard.monthly_revenue_desc') }}</span>
                        <div class="hud-chart-wrap mt-3">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subscriptions by plan --}}
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">
                            <i class="bi bi-pie-chart me-1 text-primary"></i>
                            {{ __('admin.dashboard.subscriptions_by_plan') }}
                        </h6>
                        <span class="text-muted small">{{ __('admin.dashboard.top_packages_description') }}</span>

                        @if($subscriptionsByPlan->isNotEmpty())
                            <div class="hud-chart-wrap mt-3">
                                <canvas id="plansChart"></canvas>
                            </div>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-pie-chart fs-1 d-block mb-2 opacity-50"></i>
                                {{ __('admin.dashboard.no_data') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== Action needed ========== --}}
        <div class="hud-section-head">
            <span class="ico"><i class="bi bi-bell"></i></span>
            <h5>{{ __('admin.dashboard.action_needed_section') }}</h5>
            <span class="hud-line"></span>
        </div>

        <div class="row g-3">
            {{-- Pending invoices --}}
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-0">
                                <i class="bi bi-receipt-cutoff me-1 text-warning"></i>
                                {{ __('admin.dashboard.pending_invoices') }}
                            </h6>
                            <small class="text-muted">{{ __('admin.dashboard.pending_invoices_desc') }}</small>
                        </div>
                        @if($pendingInvoicesCount > 0)
                            <a href="{{ route('admin.invoices.index', ['payment_status' => 'pending']) }}"
                               class="btn btn-sm btn-outline-primary">
                                {{ __('admin.dashboard.view_all') }} ({{ $pendingInvoicesCount }})
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @if($pendingInvoices->isNotEmpty())
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr class="small text-muted">
                                        <th>{{ __('admin.dashboard.fisherman_name') }}</th>
                                        <th>{{ __('admin.dashboard.invoice_number') }}</th>
                                        <th class="text-end">{{ __('admin.dashboard.amount') }}</th>
                                        <th class="text-center">{{ __('admin.dashboard.review') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingInvoices as $invoice)
                                        <tr>
                                            <td class="fw-medium">{{ $invoice->user->name ?? '--' }}</td>
                                            <td class="small text-muted">{{ $invoice->invoice_number }}</td>
                                            <td class="text-end fw-semibold">{!! $money($invoice->total_amount) !!}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.invoices.show', $invoice->id) }}"
                                                   class="btn btn-sm btn-outline-primary" title="{{ __('admin.dashboard.review') }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-check-circle-fill fs-1 d-block mb-2 text-success"></i>
                                {{ __('admin.dashboard.no_pending_invoices') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Expiring subscriptions --}}
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-0">
                                <i class="bi bi-alarm me-1 text-danger"></i>
                                {{ __('admin.dashboard.renewal_alerts') }}
                            </h6>
                            <small class="text-muted">{{ __('admin.dashboard.expiring_soon') }}</small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($expiringSoon->isNotEmpty())
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr class="small text-muted">
                                        <th>{{ __('admin.dashboard.fisherman_name') }}</th>
                                        <th>{{ __('admin.dashboard.package') }}</th>
                                        <th>{{ __('admin.dashboard.expires_in') }}</th>
                                        <th class="text-center">{{ __('admin.actions.view') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiringSoon as $subscription)
                                        <tr>
                                            <td class="fw-medium">{{ $subscription->user->name ?? '--' }}</td>
                                            <td>
                                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                                    {{ $subscription->package->name ?? '--' }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $daysLeft = (int) round($subscription->end_date->diffInDays(\Carbon\Carbon::today()));
                                                @endphp
                                                <span class="badge {{ $daysLeft <= 3 ? 'bg-danger' : 'bg-warning text-dark' }} bg-opacity-10 {{ $daysLeft <= 3 ? 'text-danger' : '' }}">
                                                    {{ $subscription->end_date->diffForHumans() }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.subscriptions.show', $subscription->id) }}"
                                                   class="btn btn-sm btn-outline-primary" title="{{ __('admin.actions.view') }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-check-circle-fill fs-1 d-block mb-2 text-success"></i>
                                {{ __('admin.dashboard.no_expiring_subscriptions') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') { return; }

            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const accent = '#3675c2';
            const grid   = isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.06)';
            const label  = isDark ? 'rgba(255,255,255,.6)'  : 'rgba(0,0,0,.5)';
            const cardBg = isDark ? '#1b2a41' : '#fff';

            const sar = new Intl.NumberFormat('{{ app()->getLocale() }}', {
                style: 'currency', currency: 'SAR', maximumFractionDigits: 0
            });

            // ---- MRR trend ----
            const mrrCanvas = document.getElementById('mrrChart');
            if (mrrCanvas) {
                new Chart(mrrCanvas, {
                    type: 'line',
                    data: {
                        labels: @json(array_column($mrrHistory, 'label')),
                        datasets: [{
                            label: "{{ __('admin.dashboard.mrr_value') }}",
                            data: @json(array_column($mrrHistory, 'mrr')),
                            borderColor: accent,
                            backgroundColor: 'rgba(54,117,194,.12)',
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointBackgroundColor: accent,
                            tension: .35,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: c => sar.format(c.parsed.y) } }
                        },
                        scales: {
                            x: { grid: { color: grid }, ticks: { color: label, font: { size: 11 } } },
                            y: {
                                beginAtZero: true,
                                grid: { color: grid },
                                ticks: { color: label, font: { size: 11 }, callback: v => sar.format(v) }
                            }
                        }
                    }
                });
            }

            // ---- Monthly collected revenue ----
            const revenueCanvas = document.getElementById('revenueChart');
            if (revenueCanvas) {
                new Chart(revenueCanvas, {
                    type: 'bar',
                    data: {
                        labels: @json(array_column($revenueHistory, 'label')),
                        datasets: [{
                            label: "{{ __('admin.dashboard.collected_revenue') }}",
                            data: @json(array_column($revenueHistory, 'revenue')),
                            backgroundColor: 'rgba(54,117,194,.7)',
                            hoverBackgroundColor: accent,
                            borderRadius: 6,
                            maxBarThickness: 46,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: c => sar.format(c.parsed.y) } }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: label, font: { size: 11 } } },
                            y: {
                                beginAtZero: true,
                                grid: { color: grid },
                                ticks: { color: label, font: { size: 11 }, callback: v => sar.format(v) }
                            }
                        }
                    }
                });
            }

            // ---- Subscriptions by status ----
            const statusCanvas = document.getElementById('statusChart');
            if (statusCanvas) {
                new Chart(statusCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            "{{ __('admin.dashboard.status_active') }}",
                            "{{ __('admin.dashboard.status_pending') }}",
                            "{{ __('admin.dashboard.status_trial') }}",
                            "{{ __('admin.dashboard.status_expired') }}",
                        ],
                        datasets: [{
                            data: [
                                {{ (int) $activeSubscriptions }},
                                {{ (int) $pendingSubscriptions }},
                                {{ (int) $trialSubscriptions }},
                                {{ (int) $expiredSubscriptions }},
                            ],
                            backgroundColor: ['#2e9e6b', '#e0a138', '#3675c2', '#adb5bd'],
                            borderWidth: 1,
                            borderColor: cardBg,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: { position: 'bottom', labels: { color: label, font: { size: 11 }, boxWidth: 10 } },
                            tooltip: {
                                callbacks: {
                                    label: c => c.label + ': ' + c.parsed + ' {{ __('admin.dashboard.subscription_unit') }}'
                                }
                            }
                        }
                    }
                });
            }

            // ---- Subscriptions by plan ----
            const plansCanvas = document.getElementById('plansChart');
            @if($subscriptionsByPlan->isNotEmpty())
            if (plansCanvas) {
                const plans = @json($subscriptionsByPlan);
                const palette = ['#3675c2', '#5b92d4', '#7aaee2', '#9dc6ef', '#c1d9f7', '#adb5bd', '#6c757d'];
                new Chart(plansCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: plans.map(p => p.name + ' (' + p.boats_count + ')'),
                        datasets: [{
                            data: plans.map(p => p.total),
                            backgroundColor: palette.slice(0, plans.length),
                            borderWidth: 1,
                            borderColor: cardBg,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: { position: 'bottom', labels: { color: label, font: { size: 11 }, boxWidth: 10 } },
                            tooltip: {
                                callbacks: {
                                    label: c => c.label + ': ' + c.parsed + ' {{ __('admin.dashboard.subscription_unit') }}'
                                }
                            }
                        }
                    }
                });
            }
            @endif
        });
    </script>
@endsection
