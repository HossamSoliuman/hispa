@extends('owner.layouts.master')

@section('title', __('owner.annual_summary.title'))

@section('content')
    @php
        $t = $summary['totals'];
        $months = $summary['months'];
        $monthNames = __('owner.annual_summary.months');

        $stats = [
            ['label' => __('owner.annual_summary.stats.sales'), 'value' => $t['gross_sales'], 'icon' => 'fa-cash-register', 'accent' => 'primary'],
            ['label' => __('owner.annual_summary.stats.revenue'), 'value' => $t['net_owner_revenue'], 'icon' => 'fa-sack-dollar', 'accent' => 'info'],
            ['label' => __('owner.annual_summary.stats.expenses'), 'value' => $t['total_expenses'], 'icon' => 'fa-receipt', 'accent' => 'danger'],
            ['label' => __('owner.annual_summary.stats.depreciation'), 'value' => $t['depreciation'], 'icon' => 'fa-arrow-trend-down', 'accent' => 'warning'],
            ['label' => __('owner.annual_summary.stats.net_profit'), 'value' => $t['net_profit'], 'icon' => 'fa-chart-line', 'accent' => $t['net_profit'] >= 0 ? 'success' : 'danger'],
        ];
    @endphp

    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ __('owner.annual_summary.title') }} — {{ $year }}</h2>
            <p class="text-muted mb-0">{{ __('owner.annual_summary.subtitle') }}</p>
        </div>
        <div class="ms-auto">
            <a href="{{ route('owner.reports.annual-summary.print', request()->all()) }}" target="_blank"
                class="btn btn-outline-info btn-border-radius">
                <i class="fa fa-print me-2"></i>{{ __('owner.annual_summary.print') }}
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('owner.reports.annual-summary') }}">
                <div class="row align-items-end gy-2">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">{{ __('owner.annual_summary.year') }}</label>
                        <input type="number" name="year" class="form-control" value="{{ $year }}" min="2000" max="2100" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">{{ __('owner.annual_summary.boat') }}</label>
                        <select name="boat_id" class="form-select">
                            <option value="">{{ __('owner.annual_summary.all_boats') }}</option>
                            @foreach ($boats as $boat)
                                <option value="{{ $boat->id }}" {{ $boatId == $boat->id ? 'selected' : '' }}>
                                    {{ $boat->name ?? $boat->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa fa-search me-2"></i>{{ __('owner.annual_summary.update') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Year KPI cards --}}
    <div class="row g-3 mb-4">
        @foreach ($stats as $stat)
            <div class="col-6 col-md-4 col-xl">
                <div class="card shadow-sm border-0 h-100 border-top border-3 border-{{ $stat['accent'] }}">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-2 mb-1 text-muted">
                            <i class="fa {{ $stat['icon'] }} text-{{ $stat['accent'] }}"></i>
                            <span class="small">{{ $stat['label'] }}</span>
                        </div>
                        <div class="fs-4 fw-bold text-{{ $stat['accent'] }}">{{ number_format($stat['value'], 2) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Monthly breakdown --}}
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">{{ __('owner.annual_summary.breakdown_title') }}</h5>
            <span class="badge bg-secondary-subtle text-secondary-emphasis">
                {{ __('owner.annual_summary.stats.closed_months') }}: {{ $summary['closed_count'] }} / 12
            </span>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-start">{{ __('owner.annual_summary.columns.month') }}</th>
                        <th>{{ __('owner.annual_summary.columns.status') }}</th>
                        <th class="text-end">{{ __('owner.annual_summary.columns.sales') }}</th>
                        <th class="text-end">{{ __('owner.annual_summary.columns.revenue') }}</th>
                        <th class="text-end">{{ __('owner.annual_summary.columns.expenses') }}</th>
                        <th class="text-end">{{ __('owner.annual_summary.columns.depreciation') }}</th>
                        <th class="text-end">{{ __('owner.annual_summary.columns.net_profit') }}</th>
                        <th class="text-end">{{ __('owner.annual_summary.columns.crew_share') }}</th>
                        <th class="text-end">{{ __('owner.annual_summary.columns.owner_share') }}</th>
                        <th>{{ __('owner.annual_summary.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($months as $m => $closing)
                        <tr @if (! $closing) class="text-muted" @endif>
                            <td class="text-start fw-semibold">{{ $monthNames[$m] }}</td>
                            <td>
                                @if ($closing)
                                    <span class="badge bg-success-subtle text-success-emphasis">{{ __('owner.month_closing.status_closed') }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ __('owner.annual_summary.not_closed') }}</span>
                                @endif
                            </td>
                            @if ($closing)
                                <td class="text-end">{{ number_format((float) $closing->gross_sales, 2) }}</td>
                                <td class="text-end">{{ number_format((float) $closing->net_owner_revenue, 2) }}</td>
                                <td class="text-end text-danger">{{ number_format((float) $closing->total_expenses, 2) }}</td>
                                <td class="text-end">{{ number_format((float) $closing->depreciation, 2) }}</td>
                                <td class="text-end fw-bold {{ (float) $closing->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format((float) $closing->net_profit, 2) }}
                                </td>
                                <td class="text-end">{{ number_format((float) $closing->crew_share, 2) }}</td>
                                <td class="text-end">{{ number_format((float) $closing->owner_share, 2) }}</td>
                                <td>
                                    <a href="{{ route('owner.month-closing.show', $closing) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            @else
                                <td class="text-end">—</td>
                                <td class="text-end">—</td>
                                <td class="text-end">—</td>
                                <td class="text-end">—</td>
                                <td class="text-end">—</td>
                                <td class="text-end">—</td>
                                <td class="text-end">—</td>
                                <td>—</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td class="text-start" colspan="2">{{ __('owner.annual_summary.columns.total') }}</td>
                        <td class="text-end">{{ number_format($t['gross_sales'], 2) }}</td>
                        <td class="text-end">{{ number_format($t['net_owner_revenue'], 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($t['total_expenses'], 2) }}</td>
                        <td class="text-end">{{ number_format($t['depreciation'], 2) }}</td>
                        <td class="text-end {{ $t['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($t['net_profit'], 2) }}
                        </td>
                        <td class="text-end">{{ number_format($t['crew_share'], 2) }}</td>
                        <td class="text-end">{{ number_format($t['owner_share'], 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
