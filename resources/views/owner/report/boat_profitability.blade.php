@extends('owner.layouts.master')

@section('title', __('owner.analysis_reports.boat_profitability.title'))

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ __('owner.analysis_reports.boat_profitability.title') }}</h2>
            <p class="text-muted mb-0">{{ __('owner.analysis_reports.boat_profitability.description') }}</p>
        </div>
    </div>

    @include('owner.report.partials.filter', [
        'action' => route('owner.reports.boat-profitability'),
        'printAction' => 'owner.reports.boat-profitability.print',
        'showBoat' => false,
    ])

    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('owner.analysis_reports.boat_profitability.boat') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.gross_sales') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.net_sales') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.expenses') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.net_profit') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.margin') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row['boat_name'] }}</td>
                            <td class="text-end">{{ number_format($row['gross_sales'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['net_sales'], 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($row['expenses'], 2) }}</td>
                            <td class="text-end fw-bold {{ $row['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($row['net_profit'], 2) }}
                            </td>
                            <td class="text-end">{{ number_format($row['margin'], 1) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">{{ __('owner.analysis_reports.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($rows))
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td>{{ __('owner.analysis_reports.totals') }}</td>
                            <td class="text-end">{{ number_format($totals['gross_sales'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_sales'], 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($totals['expenses'], 2) }}</td>
                            <td class="text-end {{ $totals['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($totals['net_profit'], 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
