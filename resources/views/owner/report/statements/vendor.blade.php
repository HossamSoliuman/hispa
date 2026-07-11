@extends('owner.layouts.master')

@section('title', __('owner.analysis_reports.account_statements.vendor.title'))

@section('content')
    <div class="mb-3">
        <h2 class="mb-1">{{ __('owner.analysis_reports.account_statements.vendor.title') }}</h2>
        <p class="text-muted mb-0">{{ __('owner.analysis_reports.account_statements.vendor.description') }}</p>
    </div>

    @include('owner.report.partials.statement-filter', [
        'action' => route('owner.reports.vendor-statement'),
        'printAction' => 'owner.reports.vendor-statement.print',
        'entityParam' => 'vendor_id',
        'entityLabel' => __('owner.analysis_reports.account_statements.vendor.entity_label'),
        'placeholder' => __('owner.analysis_reports.account_statements.vendor.placeholder'),
        'selectedId' => $vendorId,
        'from' => $from,
        'to' => $to,
        'groups' => [['label' => null, 'items' => $vendors]],
    ])

    @if (!$vendor)
        <div class="alert alert-info border-0 shadow-sm">
            <i class="bi bi-info-circle me-1"></i>{{ __('owner.analysis_reports.account_statements.choose_prompt') }}
        </div>
    @else
        <div class="row g-3 mb-3">
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">{{ __('owner.vendors.cards.total_expenses') }}</div>
                        <div class="h4 mb-0 text-secondary">{{ number_format($totalExpenses, 2) }} <x-riyal-icon size="sm" /></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">{{ __('owner.vendors.cards.pending_amount') }}</div>
                        <div class="h4 mb-0 {{ $totalDue > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($totalDue, 2) }} <x-riyal-icon size="sm" /></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 fw-bold">{{ $vendor->name }}</div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('owner.vendors.table.category') }}</th>
                            <th>{{ __('owner.vendors.table.trip') }}</th>
                            <th>{{ __('owner.vendors.table.date') }}</th>
                            <th>{{ __('owner.vendors.table.status') }}</th>
                            <th class="text-end">{{ __('owner.vendors.table.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $exp)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $exp->category?->name ?: '—' }}</td>
                                <td>{{ $exp->trip?->name ?: '—' }}</td>
                                <td>{{ optional($exp->created_at)->format('Y-m-d') ?? '—' }}</td>
                                <td>
                                    @if ($exp->status === 'pending')
                                        <span class="badge bg-warning text-dark">{{ ucfirst($exp->status) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($exp->status ?? '—') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($exp->final_price ?? 0, 2) }} <x-riyal-icon size="sm" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">{{ __('owner.analysis_reports.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                    @if ($expenses->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="5">{{ __('owner.analysis_reports.totals') }}</td>
                                <td class="text-end">{{ number_format($totalExpenses, 2) }} <x-riyal-icon size="sm" /></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @endif
@endsection
