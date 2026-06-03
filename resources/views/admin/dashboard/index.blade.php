@extends('admin.layouts.master')
@section('title')
    {{ __('admin.dashboard.title') }}
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

        .stat-section {
            margin-bottom: 2.5rem;
            animation: fadeInUp 0.5s ease-out;
        }

        .stat-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e9ecef;
            color: #2c3e50;
            display: flex;
            align-items: center;
        }

        .stat-section-title i {
            color: #0d6efd;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.08);
        }

        .dashboard-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .dashboard-card .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .dashboard-card .card-title {
            font-weight: 600;
            color: #2c3e50;
        }

        /* Charts section - side by side, equal height */
        .charts-row .chart-card {
            height: 100%;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .charts-row .chart-card .card-body {
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .charts-row .chart-card .card-body canvas {
            max-height: 260px;
            width: 100% !important;
        }
        .charts-row .chart-card .card-header {
            padding: 0.85rem 1rem;
            font-size: 0.95rem;
        }

        /* Stagger animation for stat cards */
        .stat-section .row > div {
            animation: fadeInUp 0.5s ease-out;
            animation-fill-mode: both;
        }

        .stat-section .row > div:nth-child(1) { animation-delay: 0.05s; }
        .stat-section .row > div:nth-child(2) { animation-delay: 0.08s; }
        .stat-section .row > div:nth-child(3) { animation-delay: 0.11s; }
        .stat-section .row > div:nth-child(4) { animation-delay: 0.14s; }
        .stat-section .row > div:nth-child(5) { animation-delay: 0.17s; }
        .stat-section .row > div:nth-child(6) { animation-delay: 0.2s; }
        .stat-section .row > div:nth-child(7) { animation-delay: 0.23s; }
        .stat-section .row > div:nth-child(8) { animation-delay: 0.26s; }
        .stat-section .row > div:nth-child(9) { animation-delay: 0.29s; }
        .stat-section .row > div:nth-child(10) { animation-delay: 0.32s; }
        .stat-section .row > div:nth-child(11) { animation-delay: 0.35s; }
        .stat-section .row > div:nth-child(12) { animation-delay: 0.38s; }
        .stat-section .row > div:nth-child(13) { animation-delay: 0.41s; }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .stat-section {
                margin-bottom: 1.5rem;
            }
            
            .stat-section-title {
                font-size: 1.1rem;
            }
        }

        /* Table improvements */
        .table-responsive {
            border-radius: 0.375rem;
        }

        .table-hover tbody tr {
            transition: background-color 0.15s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        /* Badge improvements */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        /* Form improvements */
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        /* Quick actions improvements */
        .quick-actions .btn {
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .quick-actions .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Payroll Models Cards */
        .payroll-model-card {
            transition: all 0.3s ease;
        }

        .payroll-model-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
        }

        .payroll-model-card .icon-circle {
            transition: all 0.3s ease;
        }

        .payroll-model-card:hover .icon-circle {
            transform: scale(1.1);
        }

        .payroll-model-card code {
            background: rgba(0, 0, 0, 0.05);
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.85rem;
        }

        .border-primary { border-color: #0d6efd !important; }
        .border-success { border-color: #198754 !important; }
        .border-warning { border-color: #ffc107 !important; }

        /* Compact stat cards - less height like reference layout (4 per row) */
        .dashboard-tight .stat-section .card.h-100 {
            height: auto !important;
        }
        .dashboard-tight .stat-section .card {
            min-height: unset;
        }
        .dashboard-tight .stat-section .card-body {
            padding: 0.5rem 0.65rem !important;
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }
        .dashboard-tight .stat-section .stat-card-hover .small-title {
            font-size: 0.8rem !important;
            margin-bottom: 0.2rem !important;
            line-height: 1.2;
        }
        .dashboard-tight .stat-section .stat-card-hover .stat-value {
            font-size: 1.05rem !important;
            line-height: 1.15;
        }
        .dashboard-tight .stat-section .stat-card-hover .stat-value .unit {
            font-size: 0.75rem !important;
        }
        .dashboard-tight .stat-section .stat-card-hover .icon-circle {
            width: 28px !important;
            height: 28px !important;
        }
        .dashboard-tight .stat-section .stat-card-hover .stat-icon {
            font-size: 1rem !important;
        }
        .dashboard-tight .stat-section .row.g-3 {
            --bs-gutter-y: 0.5rem;
            --bs-gutter-x: 0.5rem;
        }
        @media (min-width: 1200px) {
            .dashboard-tight .stat-section .stat-card-hover .stat-value {
                font-size: 1.15rem !important;
            }
        }
    </style>
@endsection
@section('content')
    <div class="dashboard-tight">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1 fw-bold text-dark">{{ __('admin.dashboard.title') }}</h1>
                        <p class="text-muted mb-0">{{ __('admin.dashboard.subtitle') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== مؤشرات لوحة التحكم ========== --}}
        <div class="stat-section">
            <div class="row g-3 mb-4">
                @include('owner.components.stat-card', [
                    'title' => __('admin.dashboard.revenue_bank_transfer'),
                    'value' => new \Illuminate\Support\HtmlString(
                        view('admin.components.currency-value', ['amount' => $revenueByBankTransfer])->render()
                    ),
                    'icon' => 'bi bi-bank',
                    'gradient' => 'linear-gradient(135deg, #16a085, #1abc9c)',
                    'colClass' => 'col-6 col-md-3 mb-0',
                ])
                @include('owner.components.stat-card', [
                    'title' => __('admin.dashboard.total_subscriptions'),
                    'value' => number_format($totalSubscriptions),
                    'icon' => 'bi bi-people-fill',
                    'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
                    'colClass' => 'col-6 col-md-3 mb-0',
                ])
                @include('owner.components.stat-card', [
                    'title' => __('admin.dashboard.active_subscriptions'),
                    'value' => number_format($activeSubscriptions),
                    'icon' => 'bi bi-check-circle-fill',
                    'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
                    'colClass' => 'col-6 col-md-3 mb-0',
                ])
                @include('owner.components.stat-card', [
                    'title' => __('admin.dashboard.total_invoices'),
                    'value' => number_format($totalInvoices),
                    'icon' => 'bi bi-file-earmark-text',
                    'gradient' => 'linear-gradient(135deg, #3498db, #2980b9)',
                    'colClass' => 'col-6 col-md-3 mb-0',
                ])
                @include('owner.components.stat-card', [
                    'title' => __('admin.dashboard.paid_invoices'),
                    'value' => number_format($paidInvoices),
                    'icon' => 'bi bi-check-circle-fill',
                    'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
                    'colClass' => 'col-6 col-md-3 mb-0',
                ])
                @include('owner.components.stat-card', [
                    'title' => __('admin.dashboard.total_fishermen'),
                    'value' => number_format($totalFishermen),
                    'icon' => 'bi bi-person-badge',
                    'gradient' => 'linear-gradient(135deg, #9b59b6, #8e44ad)',
                    'colClass' => 'col-6 col-md-3 mb-0',
                ])
                @include('owner.components.stat-card', [
                    'title' => __('admin.dashboard.active_fishermen'),
                    'value' => number_format($activeFishermen),
                    'icon' => 'bi bi-person-check-fill',
                    'gradient' => 'linear-gradient(135deg, #16a085, #1abc9c)',
                    'colClass' => 'col-6 col-md-3 mb-0',
                ])
                @include('owner.components.stat-card', [
                    'title' => __('admin.dashboard.total_trips'),
                    'value' => number_format($totalTrips),
                    'icon' => 'bi bi-ship',
                    'gradient' => 'linear-gradient(135deg, #3498db, #2980b9)',
                    'colClass' => 'col-6 col-md-3 mb-0',
                ])
                @include('owner.components.stat-card', [
                    'title' => __('admin.dashboard.total_boats'),
                    'value' => number_format($totalBoats),
                    'icon' => 'bi bi-water',
                    'gradient' => 'linear-gradient(135deg, #3498db, #2980b9)',
                    'colClass' => 'col-6 col-md-3 mb-0',
                ])
                @include('owner.components.stat-card', [
                    'title' => __('admin.dashboard.mrr_title'),
                    'value' => new \Illuminate\Support\HtmlString(
                        view('admin.components.currency-value', ['amount' => $mrr ?? 0])->render()
                    ),
                    'icon' => 'bi bi-cash-stack',
                    'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
                    'colClass' => 'col-6 col-md-3 mb-0',
                ])
                @include('owner.components.stat-card', [
                    'title' => __('admin.dashboard.churn_rate_title'),
                    'value' => number_format($churnRate ?? 0, 2) . '%',
                    'icon' => 'bi bi-exclamation-triangle',
                    'gradient' => 'linear-gradient(135deg, #e74c3c, #c0392b)',
                    'colClass' => 'col-6 col-md-3 mb-0',
                ])
            </div>
        </div>

        {{-- ========== تطور MRR + أكثر الباقات مبيعاً (جنباً إلى جنب) ========== --}}
        <div class="row g-3 mb-4 charts-row align-items-stretch">
            <div class="col-12 col-lg-6">
                <div class="card dashboard-card chart-card h-100">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-graph-up me-2"></i>{{ __('admin.dashboard.mrr_chart_title') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(isset($mrrHistory) && count($mrrHistory) > 0)
                            <canvas id="mrrChart" height="220"></canvas>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center text-muted py-5">
                                <i class="bi bi-graph-up display-4 mb-2 opacity-50"></i>
                                <p class="mb-0">{{ __('admin.dashboard.no_data') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card dashboard-card chart-card h-100">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-pie-chart me-2"></i>{{ __('admin.dashboard.top_packages_title') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(isset($packageSales) && $packageSales->count() > 0)
                            <canvas id="packagesChart" height="220"></canvas>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center text-muted py-5">
                                <i class="bi bi-pie-chart display-4 mb-2 opacity-50"></i>
                                <p class="mb-0">{{ __('admin.dashboard.no_data') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== تنبيهات التجديد ========== --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-bell-fill me-2"></i>{{ __('admin.dashboard.renewal_alerts') }}
                        </h6>
                        <span class="badge bg-warning text-dark">{{ $expiringSoon->count() }}</span>
                    </div>
                    <div class="card-body">
                        @if($expiringSoon->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
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
                                                    <span class="badge bg-info">{{ $subscription->package->name ?? '--' }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $daysLeft = \Carbon\Carbon::parse($subscription->end_date)->diffInDays(\Carbon\Carbon::today());
                                                    @endphp
                                                    <span class="badge bg-{{ $daysLeft <= 3 ? 'danger' : 'warning' }}">
                                                        {{ \Carbon\Carbon::parse($subscription->end_date)->diffForHumans() }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" 
                                                       class="btn btn-sm btn-primary" 
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
                            <div class="text-center py-4">
                                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0 mt-2">{{ __('admin.dashboard.no_expiring_subscriptions') }}</p>
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
        document.addEventListener('DOMContentLoaded', function() {
            @if(isset($mrrHistory) && count($mrrHistory) > 0)
                const mrrCtx = document.getElementById('mrrChart');
                if (mrrCtx && typeof Chart !== 'undefined') {
                    const mrrChart = new Chart(mrrCtx, {
                        type: 'line',
                        data: {
                            labels: @json(array_column($mrrHistory, 'month_label')),
                            datasets: [{
                                label: '{{ __('admin.dashboard.mrr_value') }}',
                                data: @json(array_column($mrrHistory, 'mrr')),
                                borderColor: 'rgb(13, 110, 253)',
                                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: { display: true, position: 'top' },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return '{{ __('admin.dashboard.mrr_value') }}: ' +
                                                new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR', minimumFractionDigits: 0 }).format(value);
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            @endif

            @if(isset($packageSales) && $packageSales->count() > 0)
                const packagesCtx = document.getElementById('packagesChart');
                if (packagesCtx && typeof Chart !== 'undefined') {
                    const packagesData = @json($packageSales);
                    const packagesChart = new Chart(packagesCtx, {
                        type: 'doughnut',
                        data: {
                            labels: packagesData.map(function(p) {
                                return p.package_name + ' (' + p.boats_count + ' {{ __('admin.dashboard.boats_count') }})';
                            }),
                            datasets: [{
                                data: packagesData.map(function(p) { return p.sales_count; }),
                                backgroundColor: [
                                    'rgba(13, 110, 253, 0.8)',
                                    'rgba(25, 135, 84, 0.8)',
                                    'rgba(255, 193, 7, 0.8)',
                                    'rgba(220, 53, 69, 0.8)',
                                    'rgba(108, 117, 125, 0.8)',
                                    'rgba(13, 202, 240, 0.8)',
                                ],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: { display: true, position: 'bottom' },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            return label + ': ' + value + ' {{ __('admin.dashboard.sales_count') }}';
                                        }
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