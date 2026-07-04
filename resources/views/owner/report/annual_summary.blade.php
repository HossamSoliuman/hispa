@extends('owner.layouts.master')

@section('title', __('owner.annual_summary.title'))

@section('content')
    @php $monthNames = __('owner.annual_summary.months'); @endphp

    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ __('owner.annual_summary.title') }}</h2>
            <p class="text-muted mb-0">{{ __('owner.annual_summary.list_subtitle') }}</p>
        </div>
    </div>

    {{-- Boat filter --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('owner.reports.annual-summary') }}">
                <div class="row align-items-end gy-2">
                    <div class="col-md-9">
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

    {{-- Closed years --}}
    <div class="card shadow-sm border-0">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('owner.annual_summary.closed_years') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('owner.annual_summary.year') }}</th>
                            <th>{{ __('owner.annual_summary.stats.closed_months') }}</th>
                            <th>{{ __('owner.annual_summary.columns.sales') }}</th>
                            <th>{{ __('owner.annual_summary.columns.expenses') }}</th>
                            <th>{{ __('owner.annual_summary.columns.net_profit') }}</th>
                            <th>{{ __('owner.annual_summary.columns.crew_share') }}</th>
                            <th>{{ __('owner.annual_summary.columns.status') }}</th>
                            <th>{{ __('owner.annual_summary.columns.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($years as $row)
                            @php $t = $row['summary']['totals']; @endphp
                            <tr>
                                <td class="fw-semibold">{{ $row['year'] }}</td>
                                <td>{{ $row['summary']['closed_count'] }} / 12</td>
                                <td>{{ number_format($t['gross_sales'], 2) }}</td>
                                <td class="text-danger">{{ number_format($t['total_expenses'], 2) }}</td>
                                <td class="fw-bold {{ $t['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($t['net_profit'], 2) }}
                                </td>
                                <td>{{ number_format($t['crew_share'], 2) }}</td>
                                <td>
                                    @if ($t['net_profit'] >= 0)
                                        <span class="badge bg-success-subtle text-success-emphasis">{{ __('owner.annual_summary.verdict_profit') }}</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger-emphasis">{{ __('owner.annual_summary.verdict_loss') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('owner.reports.annual-summary.print', ['year' => $row['year'], 'boat_id' => $boatId]) }}"
                                        target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-print me-1"></i>{{ __('owner.annual_summary.print') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">{{ __('owner.annual_summary.no_closings') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
