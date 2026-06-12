@extends('owner.layouts.master')

@section('title', __('owner.month_closing.report_title').' '.sprintf('%02d/%d', $closing->month, $closing->year))

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ __('owner.month_closing.report_title') }} {{ sprintf('%02d/%d', $closing->month, $closing->year) }}</h2>
            <span class="badge bg-success">{{ __('owner.month_closing.status_closed') }}</span>
            <small class="text-muted ms-2">{{ __('owner.month_closing.closed_at') }}: {{ optional($closing->closed_at)->format('Y-m-d H:i') }}</small>
        </div>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('owner.month-closing.print', $closing) }}" target="_blank" class="btn btn-outline-info">
                <i class="fa fa-print me-1"></i>{{ __('owner.month_closing.print') }}
            </a>
            <a href="{{ route('owner.month-closing.index') }}" class="btn btn-outline-secondary">
                {{ __('owner.month_closing.title') }}
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['owner.profit_loss.net_sales', $closing->net_sales, 'success'],
                ['owner.profit_loss.total_expenses', $closing->total_expenses, 'danger'],
                ['owner.profit_loss.net_profit', $closing->net_profit, $closing->net_profit >= 0 ? 'success' : 'danger'],
                ['owner.generated.owner_ratio', $closing->owner_share, 'primary'],
                ['owner.profit_loss.crew_share', $closing->crew_share, 'warning'],
            ];
        @endphp
        @foreach ($cards as [$label, $value, $color])
            <div class="col-md">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="small text-muted mb-1">{{ __($label) }}</div>
                        <div class="h5 fw-bold text-{{ $color }} mb-0">
                            {{ number_format($value, 2) }} <x-riyal-icon size="sm" />
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('owner.month_closing.distribution') }}</h5>
            <span class="badge bg-secondary">
                {{ __('owner.month_closing.columns.share_value') }}: {{ number_format($closing->share_value, 2) }}
            </span>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('owner.month_closing.columns.member') }}</th>
                        <th>{{ __('owner.month_closing.columns.role') }}</th>
                        <th>{{ __('owner.month_closing.columns.shares') }}</th>
                        <th>{{ __('owner.month_closing.columns.due') }}</th>
                        <th>{{ __('owner.month_closing.columns.advances') }}</th>
                        <th>{{ __('owner.month_closing.columns.paid') }}</th>
                        <th>{{ __('owner.month_closing.columns.remaining') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($closing->dues as $due)
                        <tr>
                            <td>{{ $due->member_name }}</td>
                            <td>{{ $due->role }}</td>
                            <td>{{ number_format($due->shares, 2) }}</td>
                            <td>{{ number_format($due->due_amount, 2) }}</td>
                            <td>{{ number_format($due->advances, 2) }}</td>
                            <td>{{ number_format($due->paid_amount, 2) }}</td>
                            <td class="fw-bold">{{ number_format($due->remaining, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="3">{{ __('owner.month_closing.columns.shares') }}: {{ number_format($closing->total_shares, 2) }}</td>
                        <td>{{ number_format($closing->dues->sum('due_amount'), 2) }}</td>
                        <td>{{ number_format($closing->dues->sum('advances'), 2) }}</td>
                        <td>{{ number_format($closing->dues->sum('paid_amount'), 2) }}</td>
                        <td>{{ number_format($closing->dues->sum('remaining'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @isset($payrollSummary)
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('owner.month_closing.payroll_summary.title') }}</h5>
                <small class="text-muted">{{ __('owner.month_closing.payroll_summary.subtitle') }}</small>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('owner.month_closing.payroll_summary.type') }}</th>
                            <th>{{ __('owner.month_closing.payroll_summary.people') }}</th>
                            <th>{{ __('owner.month_closing.payroll_summary.net_total') }}</th>
                            <th>{{ __('owner.month_closing.payroll_summary.paid') }}</th>
                            <th>{{ __('owner.month_closing.payroll_summary.status') }}</th>
                            <th>{{ __('owner.month_closing.payroll_summary.paid_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['salary' => 'fixed', 'percentage' => 'percentage'] as $key => $label)
                            @php
                                $row = $payrollSummary[$key];
                                $badge = ['fully_paid' => 'success', 'partially_paid' => 'info', 'unpaid' => 'warning', 'not_created' => 'secondary'][$row['status']] ?? 'secondary';
                            @endphp
                            <tr>
                                <td>{{ __('owner.month_closing.payroll_summary.'.$label) }}</td>
                                <td>{{ $row['paid_count'] }} / {{ $row['count'] }}</td>
                                <td>{{ number_format($row['net_total'], 2) }} <x-riyal-icon size="sm" /></td>
                                <td class="fw-bold">{{ number_format($row['paid_amount'], 2) }} <x-riyal-icon size="sm" /></td>
                                <td><span class="badge bg-{{ $badge }}">{{ __('owner.month_closing.payroll_summary.'.$row['status']) }}</span></td>
                                <td>{{ optional($row['paid_at'])->format('Y-m-d') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endisset
@endsection
