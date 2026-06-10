@extends('owner.layouts.master')

@section('title', __('owner.analysis_reports.expenses_by_category.title'))

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ __('owner.analysis_reports.expenses_by_category.title') }}</h2>
            <p class="text-muted mb-0">{{ __('owner.analysis_reports.expenses_by_category.description') }}</p>
        </div>
    </div>

    @include('owner.report.partials.filter', [
        'action' => route('owner.reports.expenses-by-category'),
        'printAction' => 'owner.reports.expenses-by-category.print',
    ])

    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('owner.analysis_reports.expenses_by_category.category') }}</th>
                        <th>{{ __('owner.analysis_reports.expenses_by_category.type') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.expenses_by_category.count') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.expenses_by_category.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row['category'] }}</td>
                            <td>{{ $row['type'] ? __('owner.analysis_reports.expenses_by_category.type_'.$row['type']) : '—' }}</td>
                            <td class="text-end">{{ number_format($row['count']) }}</td>
                            <td class="text-end fw-bold">{{ number_format($row['amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">{{ __('owner.analysis_reports.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($rows))
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="3">{{ __('owner.analysis_reports.totals') }}</td>
                            <td class="text-end">{{ number_format($total, 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
