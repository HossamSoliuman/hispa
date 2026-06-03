@extends('admin.layouts.master')
@section('title')
    {{ __('admin.dashboard.title') }}
@endsection

@section('css')
<style>
/* ============================================================
   HUD Dashboard — light mode, squared/sharp, notepad-grid style
   Accent: brand blue #3675c2
   ============================================================ */

/* ---- tokens ---- */
:root {
    --hud-accent:       #3675c2;
    --hud-accent-rgb:   54, 117, 194;
    --hud-border:       rgba(0,0,0,.14);
    --hud-border-inner: rgba(0,0,0,.12);
    --hud-bg-card:      #ffffff;
    --hud-text:         #1a1a2e;
    --hud-text-muted:   rgba(0,0,0,.45);
    --hud-grid:         rgba(0,0,0,.06);
}

/* ---- page wrapper ---- */
.hud-dashboard {
    padding-top: .25rem;
}

/* ---- page header ---- */
.hud-page-header {
    margin-bottom: 1.75rem;
}
.hud-page-header h1 {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--hud-text);
    margin-bottom: .15rem;
}
.hud-page-header .breadcrumb {
    font-size: 12px;
    margin-bottom: 0;
    --bs-breadcrumb-divider-color: var(--hud-text-muted);
}
.hud-page-header .breadcrumb-item,
.hud-page-header .breadcrumb-item.active { color: var(--hud-text-muted); }
.hud-page-header .breadcrumb-item + .breadcrumb-item::before {
    color: var(--hud-text-muted);
}

/* ---- section head (reference §-head) ---- */
.hud-section-head {
    display: flex;
    align-items: center;
    gap: .65rem;
    margin: .25rem 0 1.1rem;
}
.hud-section-head .ico {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(var(--hud-accent-rgb), .1);
    border: 1px solid var(--hud-accent);
    color: var(--hud-accent);
    font-size: 15px;
    flex-shrink: 0;
}
.hud-section-head h5 {
    margin: 0;
    font-size: 14px;
    font-weight: 700;
    color: var(--hud-text);
    letter-spacing: .25px;
}
.hud-section-head .hud-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, var(--hud-border-inner), transparent);
}
.hud-section-head small {
    color: var(--hud-text-muted);
    font-size: 11.5px;
}

/* ============================================================
   HUD CARD — the core design element
   Transparent background, thin full-rectangle border,
   squared corners, 4 corner bracket accents
   ============================================================ */
.hud-card {
    position: relative;
    background: var(--hud-bg-card);
    border-radius: 0;
    border: none;
}

/* Thin horizontal border lines (top + bottom, inset) */
.hud-card::before {
    content: '';
    position: absolute;
    left:   12px;
    right:  12px;
    top:    0;
    bottom: 0;
    border-top:    1px solid var(--hud-border);
    border-bottom: 1px solid var(--hud-border);
    pointer-events: none;
}

/* Thin vertical border lines (left + right, inset) */
.hud-card::after {
    content: '';
    position: absolute;
    top:    12px;
    bottom: 12px;
    left:   0;
    right:  0;
    border-left:  1px solid var(--hud-border);
    border-right: 1px solid var(--hud-border);
    pointer-events: none;
}

/* Corner brackets */
.hud-card-arrow {
    position: absolute;
    inset: 0;
    pointer-events: none;
}
.hud-card-arrow > div {
    position: absolute;
    width: 10px;
    height: 10px;
}
.hud-card-arrow > div::before {
    content: '';
    position: absolute;
    width: 2px;
    height: 9px;
    background: rgba(var(--hud-accent-rgb), .6);
}
.hud-card-arrow > div::after {
    content: '';
    position: absolute;
    width: 9px;
    height: 2px;
    background: rgba(var(--hud-accent-rgb), .6);
}
.hud-arrow-tl           { top: 0;    left: 0;  }
.hud-arrow-tl::before   { top: 2px;  left: 0;  }
.hud-arrow-tl::after    { top: 0;    left: 0;  }
.hud-arrow-tr           { top: 0;    right: 0; }
.hud-arrow-tr::before   { top: 2px;  right: 0; }
.hud-arrow-tr::after    { top: 0;    right: 0; }
.hud-arrow-bl           { bottom: 0; left: 0;  }
.hud-arrow-bl::before   { bottom: 2px; left: 0; }
.hud-arrow-bl::after    { bottom: 0;  left: 0; }
.hud-arrow-br           { bottom: 0; right: 0; }
.hud-arrow-br::before   { bottom: 2px; right: 0; }
.hud-arrow-br::after    { bottom: 0;  right: 0; }

/* Card body */
.hud-card-body {
    padding: 1rem 1.1rem;
    position: relative;
    z-index: 1;
}

/* Card label (small header line inside card) */
.hud-card-label {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--hud-text-muted);
    flex: 1;
    line-height: 1.3;
    margin-bottom: .6rem;
}

/* Stat value */
.hud-stat-value {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--hud-text);
    line-height: 1.1;
}
.hud-stat-value .unit {
    font-size: .9rem;
    font-weight: 600;
    opacity: .55;
}

/* Icon box inside stat card */
.hud-icon-box {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(var(--hud-accent-rgb), .08);
    border: 1px solid rgba(var(--hud-accent-rgb), .25);
    color: var(--hud-accent);
    font-size: 15px;
    flex-shrink: 0;
}

/* ---- Chart card (uses same hud-card frame) ---- */
.hud-chart-card .hud-chart-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--hud-text);
    margin-bottom: .2rem;
}
.hud-chart-card .hud-chart-sub {
    font-size: 11px;
    color: var(--hud-text-muted);
    margin-bottom: .6rem;
}

/* ---- Table card ---- */
.hud-table-card .hud-table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .75rem 1.1rem;
    border-bottom: 1px solid var(--hud-border);
    position: relative;
    z-index: 1;
}
.hud-table-card .hud-table-header h6 {
    margin: 0;
    font-size: 13px;
    font-weight: 700;
    color: var(--hud-text);
}
.hud-table-card .table {
    margin-bottom: 0;
    font-size: 13px;
}
.hud-table-card .table thead th {
    font-weight: 700;
    font-size: 12px;
    color: var(--hud-text-muted);
    border-bottom: 1px solid var(--hud-border);
    background: rgba(0,0,0,.025);
    white-space: nowrap;
}
.hud-table-card .table td {
    vertical-align: middle;
    border-color: var(--hud-grid);
}
.hud-table-card .hud-table-body {
    padding: 0 1.1rem 1rem;
    position: relative;
    z-index: 1;
}

/* ---- Badge overrides — restrained ---- */
.hud-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    padding: .2em .55em;
    border-radius: 0;
    background: transparent;
    border: 1px solid var(--hud-border);
    color: var(--hud-text);
}
.hud-badge-danger  { border-color: #dc3545; color: #dc3545; }
.hud-badge-warning { border-color: #e6a817; color: #8a6200; }
.hud-badge-info    { border-color: var(--hud-accent); color: var(--hud-accent); }

/* ---- Empty state ---- */
.hud-empty {
    text-align: center;
    padding: 2.5rem 1rem;
    color: var(--hud-text-muted);
}
.hud-empty i {
    font-size: 2.5rem;
    display: block;
    margin-bottom: .5rem;
    color: var(--hud-accent);
    opacity: .4;
}

/* ---- Row gaps ---- */
.hud-row {
    --bs-gutter-x: .9rem;
    --bs-gutter-y: .9rem;
}
</style>
@endsection

@section('content')
<div class="hud-dashboard">

    {{-- Page Header --}}
    <div class="hud-page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">{{ __('admin.title') }}</li>
                <li class="breadcrumb-item active">{{ __('admin.dashboard.title') }}</li>
            </ol>
        </nav>
        <h1>{{ __('admin.dashboard.title') }}</h1>
        <p class="mb-0" style="font-size:13px;color:var(--hud-text-muted);">{{ __('admin.dashboard.subtitle') }}</p>
    </div>

    {{-- ========== Section: KPI Stats ========== --}}
    <div class="hud-section-head">
        <span class="ico"><i class="bi bi-bar-chart-line"></i></span>
        <h5>{{ __('admin.dashboard.title') }}</h5>
        <span class="hud-line"></span>
    </div>

    <div class="row hud-row mb-4">
        @include('admin.components.hud-stat-card', [
            'title'    => __('admin.dashboard.revenue_bank_transfer'),
            'value'    => '<span class="unit">ر.س</span> ' . number_format($revenueByBankTransfer, 0),
            'icon'     => 'bi bi-bank',
            'colClass' => 'col-6 col-md-4 col-xl-3 mb-0',
        ])
        @include('admin.components.hud-stat-card', [
            'title'    => __('admin.dashboard.total_subscriptions'),
            'value'    => number_format($totalSubscriptions),
            'icon'     => 'bi bi-people-fill',
            'colClass' => 'col-6 col-md-4 col-xl-3 mb-0',
        ])
        @include('admin.components.hud-stat-card', [
            'title'    => __('admin.dashboard.active_subscriptions'),
            'value'    => number_format($activeSubscriptions),
            'icon'     => 'bi bi-check-circle',
            'colClass' => 'col-6 col-md-4 col-xl-3 mb-0',
        ])
        @include('admin.components.hud-stat-card', [
            'title'    => __('admin.dashboard.total_invoices'),
            'value'    => number_format($totalInvoices),
            'icon'     => 'bi bi-file-earmark-text',
            'colClass' => 'col-6 col-md-4 col-xl-3 mb-0',
        ])
        @include('admin.components.hud-stat-card', [
            'title'    => __('admin.dashboard.paid_invoices'),
            'value'    => number_format($paidInvoices),
            'icon'     => 'bi bi-receipt',
            'colClass' => 'col-6 col-md-4 col-xl-3 mb-0',
        ])
        @include('admin.components.hud-stat-card', [
            'title'    => __('admin.dashboard.total_fishermen'),
            'value'    => number_format($totalFishermen),
            'icon'     => 'bi bi-person-badge',
            'colClass' => 'col-6 col-md-4 col-xl-3 mb-0',
        ])
        @include('admin.components.hud-stat-card', [
            'title'    => __('admin.dashboard.active_fishermen'),
            'value'    => number_format($activeFishermen),
            'icon'     => 'bi bi-person-check',
            'colClass' => 'col-6 col-md-4 col-xl-3 mb-0',
        ])
        @include('admin.components.hud-stat-card', [
            'title'    => __('admin.dashboard.total_trips'),
            'value'    => number_format($totalTrips),
            'icon'     => 'bi bi-ship',
            'colClass' => 'col-6 col-md-4 col-xl-3 mb-0',
        ])
        @include('admin.components.hud-stat-card', [
            'title'    => __('admin.dashboard.total_boats'),
            'value'    => number_format($totalBoats),
            'icon'     => 'bi bi-water',
            'colClass' => 'col-6 col-md-4 col-xl-3 mb-0',
        ])
        @include('admin.components.hud-stat-card', [
            'title'    => __('admin.dashboard.mrr_title'),
            'value'    => '<span class="unit">ر.س</span> ' . number_format($mrr ?? 0, 0),
            'icon'     => 'bi bi-cash-stack',
            'colClass' => 'col-6 col-md-4 col-xl-3 mb-0',
        ])
        @include('admin.components.hud-stat-card', [
            'title'    => __('admin.dashboard.churn_rate_title'),
            'value'    => number_format($churnRate ?? 0, 2) . '<span class="unit">%</span>',
            'icon'     => 'bi bi-exclamation-triangle',
            'colClass' => 'col-6 col-md-4 col-xl-3 mb-0',
        ])
    </div>

    {{-- ========== Section: Charts ========== --}}
    <div class="hud-section-head">
        <span class="ico"><i class="bi bi-graph-up"></i></span>
        <h5>{{ __('admin.dashboard.mrr_chart_title') }}</h5>
        <span class="hud-line"></span>
    </div>

    <div class="row hud-row mb-4">
        {{-- MRR Chart --}}
        <div class="col-12 col-lg-6">
            <div class="hud-card hud-chart-card h-100">
                <div class="hud-card-body">
                    <div class="hud-chart-title">
                        <i class="bi bi-graph-up me-1" style="color:var(--hud-accent);"></i>
                        {{ __('admin.dashboard.mrr_chart_title') }}
                    </div>
                    <div class="hud-chart-sub">{{ __('admin.dashboard.mrr_value') }}</div>

                    @if(isset($mrrHistory) && count($mrrHistory) > 0)
                        <canvas id="mrrChart" height="220"></canvas>
                    @else
                        <div class="hud-empty">
                            <i class="bi bi-graph-up"></i>
                            <span>{{ __('admin.dashboard.no_data') }}</span>
                        </div>
                    @endif
                </div>
                <div class="hud-card-arrow">
                    <div class="hud-arrow-tl"></div>
                    <div class="hud-arrow-tr"></div>
                    <div class="hud-arrow-bl"></div>
                    <div class="hud-arrow-br"></div>
                </div>
            </div>
        </div>

        {{-- Packages Chart --}}
        <div class="col-12 col-lg-6">
            <div class="hud-card hud-chart-card h-100">
                <div class="hud-card-body">
                    <div class="hud-chart-title">
                        <i class="bi bi-pie-chart me-1" style="color:var(--hud-accent);"></i>
                        {{ __('admin.dashboard.top_packages_title') }}
                    </div>
                    <div class="hud-chart-sub">{{ __('admin.dashboard.top_packages_title') }}</div>

                    @if(isset($packageSales) && $packageSales->count() > 0)
                        <canvas id="packagesChart" height="220"></canvas>
                    @else
                        <div class="hud-empty">
                            <i class="bi bi-pie-chart"></i>
                            <span>{{ __('admin.dashboard.no_data') }}</span>
                        </div>
                    @endif
                </div>
                <div class="hud-card-arrow">
                    <div class="hud-arrow-tl"></div>
                    <div class="hud-arrow-tr"></div>
                    <div class="hud-arrow-bl"></div>
                    <div class="hud-arrow-br"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== Section: Renewal Alerts ========== --}}
    <div class="hud-section-head">
        <span class="ico"><i class="bi bi-bell"></i></span>
        <h5>{{ __('admin.dashboard.renewal_alerts') }}</h5>
        <span class="hud-line"></span>
        <small>{{ $expiringSoon->count() }} {{ __('admin.dashboard.renewal_alerts') }}</small>
    </div>

    <div class="row hud-row mb-4">
        <div class="col-12">
            <div class="hud-card hud-table-card">
                <div class="hud-table-header">
                    <h6>
                        <i class="bi bi-bell-fill me-1" style="color:var(--hud-accent);"></i>
                        {{ __('admin.dashboard.renewal_alerts') }}
                    </h6>
                    <span class="hud-badge hud-badge-warning">{{ $expiringSoon->count() }}</span>
                </div>
                <div class="hud-table-body">
                    @if($expiringSoon->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('admin.dashboard.fisherman_name') }}</th>
                                        <th>{{ __('admin.dashboard.phone') }}</th>
                                        <th>{{ __('admin.dashboard.package') }}</th>
                                        <th>{{ __('admin.dashboard.expires_in') }}</th>
                                        <th class="text-center">{{ __('admin.actions.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiringSoon as $subscription)
                                        <tr>
                                            <td class="fw-medium">{{ $subscription->user->name ?? '--' }}</td>
                                            <td>{{ $subscription->user->phone ?? '--' }}</td>
                                            <td>
                                                <span class="hud-badge hud-badge-info">
                                                    {{ $subscription->package->name ?? '--' }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $daysLeft = \Carbon\Carbon::parse($subscription->end_date)
                                                        ->diffInDays(\Carbon\Carbon::today());
                                                @endphp
                                                <span class="hud-badge {{ $daysLeft <= 3 ? 'hud-badge-danger' : 'hud-badge-warning' }}">
                                                    {{ \Carbon\Carbon::parse($subscription->end_date)->diffForHumans() }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.subscriptions.show', $subscription->id) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   style="border-radius:0;"
                                                   title="{{ __('admin.actions.view') }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="hud-empty">
                            <i class="bi bi-check-circle"></i>
                            <span>{{ __('admin.dashboard.no_expiring_subscriptions') }}</span>
                        </div>
                    @endif
                </div>

                <div class="hud-card-arrow">
                    <div class="hud-arrow-tl"></div>
                    <div class="hud-arrow-tr"></div>
                    <div class="hud-arrow-bl"></div>
                    <div class="hud-arrow-br"></div>
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
    const ACCENT = '#3675c2';
    const ACCENT_MUTED = 'rgba(54,117,194,.12)';
    const GRID   = 'rgba(0,0,0,.06)';
    const LABEL  = 'rgba(0,0,0,.45)';

    /* ---- MRR line chart ---- */
    @if(isset($mrrHistory) && count($mrrHistory) > 0)
    (function () {
        const ctx = document.getElementById('mrrChart');
        if (!ctx || typeof Chart === 'undefined') { return; }
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json(array_column($mrrHistory, 'month_label')),
                datasets: [{
                    label: '{{ __('admin.dashboard.mrr_value') }}',
                    data: @json(array_column($mrrHistory, 'mrr')),
                    borderColor: ACCENT,
                    backgroundColor: ACCENT_MUTED,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointBackgroundColor: ACCENT,
                    tension: .35,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return new Intl.NumberFormat('ar-SA', {
                                    style: 'currency', currency: 'SAR', minimumFractionDigits: 0
                                }).format(ctx.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: GRID },
                        ticks: { color: LABEL, font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: GRID },
                        ticks: {
                            color: LABEL,
                            font: { size: 11 },
                            callback: function (v) {
                                return new Intl.NumberFormat('ar-SA', {
                                    style: 'currency', currency: 'SAR', minimumFractionDigits: 0
                                }).format(v);
                            }
                        }
                    }
                }
            }
        });
    }());
    @endif

    /* ---- Packages doughnut ---- */
    @if(isset($packageSales) && $packageSales->count() > 0)
    (function () {
        const ctx = document.getElementById('packagesChart');
        if (!ctx || typeof Chart === 'undefined') { return; }
        const data = @json($packageSales);

        /* Restrained blue-grey palette — no rainbow */
        const palette = [
            '#3675c2', '#5b92d4', '#7aaee2', '#9dc6ef',
            '#c1d9f7', '#ddeafc', '#adb5bd', '#6c757d'
        ];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(function (p) {
                    return p.package_name + ' (' + p.boats_count + ')';
                }),
                datasets: [{
                    data: data.map(function (p) { return p.sales_count; }),
                    backgroundColor: palette.slice(0, data.length),
                    borderWidth: 1,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '62%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: { font: { size: 11 }, color: LABEL, boxWidth: 10 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.label + ': ' + ctx.parsed
                                    + ' {{ __('admin.dashboard.sales_count') }}';
                            }
                        }
                    }
                }
            }
        });
    }());
    @endif
});
</script>
@endsection
