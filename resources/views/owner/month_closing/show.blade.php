@extends('owner.layouts.master')

@section('title', __('owner.month_closing.report_title').' '.sprintf('%02d/%d', $closing->month, $closing->year))

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ __('owner.month_closing.report_title') }} {{ sprintf('%02d/%d', $closing->month, $closing->year) }}</h2>
            <span class="badge bg-secondary"><i class="fa fa-ship me-1"></i>{{ $closing->boat?->name ?? __('owner.profit_loss.all_boats') }}</span>
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
            $operatingExpenses = (float) $closing->total_expenses - (float) $closing->depreciation;
            $cards = [
                ['owner.profit_loss.net_sales', $closing->net_sales, 'success'],
                ['owner.month_closing.expenses', $operatingExpenses, 'danger'],
                ['owner.profit_loss.depreciation', $closing->depreciation, 'secondary'],
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

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('owner.month_closing.revenue_details.title') }}</h5>
            <small class="text-muted">{{ __('owner.month_closing.revenue_details.subtitle') }}</small>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('owner.month_closing.revenue_details.date') }}</th>
                        <th>{{ __('owner.month_closing.revenue_details.number') }}</th>
                        <th>{{ __('owner.month_closing.revenue_details.customer') }}</th>
                        <th class="text-end">{{ __('owner.month_closing.revenue_details.total') }}</th>
                        <th class="text-end">{{ __('owner.month_closing.revenue_details.commission_labor') }}</th>
                        <th class="text-end">{{ __('owner.month_closing.revenue_details.net_owner') }}</th>
                        <th class="text-end">{{ __('owner.month_closing.revenue_details.remaining') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($details['sales'] as $sale)
                        <tr>
                            <td>{{ optional($sale->sale_datetime)->format('Y-m-d') }}</td>
                            <td>{{ $sale->number }}</td>
                            <td>{{ $sale->customer_name ?: $sale->customer->name }}</td>
                            <td class="text-end">{{ number_format((float) $sale->total_price, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $sale->commission_amount + (float) $sale->labor_amount, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $sale->net_owner_amount, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $sale->remaining_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">{{ __('owner.month_closing.revenue_details.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($details['sales']->isNotEmpty())
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="3">{{ __('owner.month_closing.revenue_details.total_label') }}</td>
                            <td class="text-end">{{ number_format((float) $details['sales']->sum('total_price'), 2) }}</td>
                            <td class="text-end">{{ number_format($details['sales']->sum(fn ($s) => (float) $s->commission_amount + (float) $s->labor_amount), 2) }}</td>
                            <td class="text-end">{{ number_format((float) $details['sales']->sum('net_owner_amount'), 2) }}</td>
                            <td class="text-end">{{ number_format((float) $details['sales']->sum('remaining_total'), 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('owner.month_closing.expense_details.title') }}</h5>
            <small class="text-muted">{{ __('owner.month_closing.expense_details.subtitle') }}</small>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('owner.month_closing.expense_details.date') }}</th>
                        <th>{{ __('owner.month_closing.expense_details.number') }}</th>
                        <th>{{ __('owner.month_closing.expense_details.category') }}</th>
                        <th>{{ __('owner.month_closing.expense_details.vendor') }}</th>
                        <th class="text-end">{{ __('owner.month_closing.expense_details.total') }}</th>
                        <th class="text-end">{{ __('owner.month_closing.expense_details.discount') }}</th>
                        <th class="text-end">{{ __('owner.month_closing.expense_details.final') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($details['expenses'] as $expense)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($expense->date)->format('Y-m-d') }}</td>
                            <td>{{ $expense->number }}</td>
                            <td>{{ $expense->category->name }}</td>
                            <td>{{ optional($expense->vendor)->name ?: '-' }}</td>
                            <td class="text-end">{{ number_format((float) $expense->total_price, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $expense->calculated_discount, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $expense->final_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">{{ __('owner.month_closing.expense_details.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($details['expenses']->isNotEmpty())
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="4">{{ __('owner.month_closing.expense_details.total_label') }}</td>
                            <td class="text-end">{{ number_format((float) $details['expenses']->sum('total_price'), 2) }}</td>
                            <td class="text-end">{{ number_format($details['expenses']->sum(fn ($e) => (float) $e->calculated_discount), 2) }}</td>
                            <td class="text-end">{{ number_format((float) $details['expenses']->sum('final_price'), 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    @include('owner.month_closing._assets_table', [
        'assets' => $closing->asset_depreciation_breakdown ?? [],
        'total' => $closing->depreciation,
    ])

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
                        <th>{{ __('owner.month_closing.columns.custom_percent') }}</th>
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
                            <td>{{ $due->custom_share_percent !== null ? '-' : number_format($due->shares, 2) }}</td>
                            <td>{{ $due->custom_share_percent !== null ? number_format($due->custom_share_percent, 2) . '%' : '-' }}</td>
                            <td>{{ number_format($due->due_amount, 2) }}</td>
                            <td>{{ number_format($due->advances, 2) }}</td>
                            <td>{{ number_format($due->paid_amount, 2) }}</td>
                            <td class="fw-bold">{{ number_format($due->remaining, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="4">{{ __('owner.month_closing.columns.shares') }}: {{ number_format($closing->total_shares, 2) }}</td>
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
                        @foreach (['percentage' => 'percentage'] as $key => $label)
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
