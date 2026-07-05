@extends('owner.layouts.master')

@section('title', __('owner.analysis_reports.account_statements.crew.title'))

@section('content')
    @php
        $dueOf = fn ($d) => (float) ($d->final_salary ?? 0);
        $paidOf = fn ($d) => $d->is_paid ? (float) ($d->paid_amount ?? $d->final_salary ?? 0) : 0.0;
        $unpaidOf = fn ($d) => $d->is_paid ? 0.0 : (float) ($d->final_salary ?? 0);
        $totalDue = (float) $details->sum($dueOf);
        $totalPaid = (float) $details->sum($paidOf);
        $totalUnpaid = (float) $details->sum($unpaidOf);
    @endphp

    <div class="mb-3">
        <h2 class="mb-1">{{ __('owner.analysis_reports.account_statements.crew.title') }}</h2>
        <p class="text-muted mb-0">{{ __('owner.analysis_reports.account_statements.crew.description') }}</p>
    </div>

    @include('owner.report.partials.statement-filter', [
        'action' => route('owner.reports.crew-statement'),
        'printAction' => 'owner.reports.crew-statement.print',
        'entityParam' => 'person_id',
        'entityLabel' => __('owner.analysis_reports.account_statements.crew.entity_label'),
        'placeholder' => __('owner.analysis_reports.account_statements.crew.placeholder'),
        'selectedId' => $personId,
        'from' => $from,
        'to' => $to,
        'groups' => [
            ['label' => __('owner.analysis_reports.account_statements.crew.group_captains'), 'items' => $captains],
            ['label' => __('owner.analysis_reports.account_statements.crew.group_crew'), 'items' => $fishers],
        ],
    ])

    @if (!$user)
        <div class="alert alert-info border-0 shadow-sm">
            <i class="bi bi-info-circle me-1"></i>{{ __('owner.analysis_reports.account_statements.choose_prompt') }}
        </div>
    @else
        <div class="row g-3 mb-3">
            @foreach ([
                ['label' => __('owner.payrolls.statement.total_due'), 'value' => number_format($totalDue, 2), 'color' => 'secondary'],
                ['label' => __('owner.payrolls.statement.total_paid'), 'value' => number_format($totalPaid, 2), 'color' => 'success'],
                ['label' => __('owner.payrolls.statement.total_unpaid'), 'value' => number_format($totalUnpaid, 2), 'color' => $totalUnpaid > 0 ? 'danger' : 'success'],
                ['label' => __('owner.payrolls.statement.months_count'), 'value' => (string) $details->count(), 'color' => 'primary'],
            ] as $card)
                <div class="col-6 col-lg-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-1">{{ $card['label'] }}</div>
                            <div class="h4 mb-0 text-{{ $card['color'] }}">{{ $card['value'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 fw-bold d-flex justify-content-between">
                <span>{{ $user->name }}</span>
                <span class="text-muted small">{{ $user->boat?->name ?? '—' }}</span>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('owner.payrolls.statement.period') }}</th>
                            <th class="text-end">{{ __('owner.payrolls.statement.due') }}</th>
                            <th class="text-end">{{ __('owner.payrolls.statement.paid') }}</th>
                            <th class="text-end">{{ __('owner.payrolls.statement.unpaid') }}</th>
                            <th>{{ __('owner.payrolls.statement.paid_date') }}</th>
                            <th>{{ __('owner.payrolls.payment_col') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($details as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ sprintf('%02d / %04d', (int) ($detail->payroll?->month ?? 0), (int) ($detail->payroll?->year ?? 0)) }}</td>
                                <td class="text-end">{{ number_format($dueOf($detail), 2) }}</td>
                                <td class="text-end">{{ number_format($paidOf($detail), 2) }}</td>
                                <td class="text-end {{ $unpaidOf($detail) > 0 ? 'text-danger' : '' }}">{{ number_format($unpaidOf($detail), 2) }}</td>
                                <td>{{ $detail->is_paid && $detail->paid_at ? optional($detail->paid_at)->format('Y-m-d') : '—' }}</td>
                                <td>
                                    @if ($detail->is_paid)
                                        <span class="badge bg-success">{{ __('owner.status.paid') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('owner.status.unpaid') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">{{ __('owner.analysis_reports.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                    @if ($details->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2">{{ __('owner.payrolls.statement.totals') }}</td>
                                <td class="text-end">{{ number_format($totalDue, 2) }}</td>
                                <td class="text-end">{{ number_format($totalPaid, 2) }}</td>
                                <td class="text-end">{{ number_format($totalUnpaid, 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @endif
@endsection
