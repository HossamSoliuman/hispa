@extends('admin.layouts.master')
@section('title')
    {{ __('admin.report.owners.title') }}
@endsection
@section('content')
    @php
        $statusColors = [
            'active' => 'success',
            'pending' => 'warning',
            'trial' => 'info',
            'expired' => 'danger',
            'suspended' => 'secondary',
        ];
    @endphp

    <div class="mb-4 d-flex align-items-start">
        <div>
            <h2 class="mb-1">{{ __('admin.report.owners.title') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.report.owners.subtitle') }}</p>
        </div>
        <div class="ms-auto">
            <button type="button" onclick="printReport()" class="btn btn-outline-theme">
                <i class="bi bi-printer me-1"></i> {{ __('admin.report.print') }}
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <x-stat-card
            :title="__('admin.report.owners.kpi.total_owners')"
            :value="number_format($totalOwners)"
            icon="bi bi-people"
            gradient="linear-gradient(135deg, #0d6efd, #0b5ed7)"
            col-class="col-md-6 col-lg-3"
        />
        <x-stat-card
            :title="__('admin.report.owners.kpi.active_owners')"
            :value="number_format($activeOwners)"
            icon="bi bi-patch-check"
            gradient="linear-gradient(135deg, #198754, #157347)"
            col-class="col-md-6 col-lg-3"
        />
        <x-stat-card
            :title="__('admin.report.owners.kpi.total_boats')"
            :value="number_format($totalBoats)"
            icon="bi bi-water"
            gradient="linear-gradient(135deg, #fd7e14, #ea5d0a)"
            col-class="col-md-6 col-lg-3"
        />
        <x-stat-card
            :title="__('admin.report.owners.kpi.total_quota')"
            :value="number_format($totalQuota)"
            icon="bi bi-diagram-3"
            gradient="linear-gradient(135deg, #0dcaf0, #0aa2c0)"
            col-class="col-md-6 col-lg-3"
        />
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form id="ownersFilters" method="GET" action="{{ route('admin.owner-report') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="status" class="form-label">{{ __('admin.report.owners.status') }}</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">{{ __('admin.filters.all') }}</option>
                            @foreach (['active', 'pending', 'trial', 'expired', 'suspended'] as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>
                                    {{ __('admin.report.owners.status_labels.' . $status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-1"></i> {{ __('admin.actions.filter') }}
                        </button>
                        <a href="{{ route('admin.owner-report') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('admin.actions.reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover text-center align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('admin.report.owners.id') }}</th>
                            <th>{{ __('admin.report.owners.owner') }}</th>
                            <th>{{ __('admin.report.owners.phone') }}</th>
                            <th>{{ __('admin.report.owners.plan') }}</th>
                            <th>{{ __('admin.report.owners.status') }}</th>
                            <th>{{ __('admin.report.owners.boats_quota') }}</th>
                            <th>{{ __('admin.report.owners.start_date') }}</th>
                            <th>{{ __('admin.report.owners.end_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row->name ?? '---' }}</td>
                                <td>{{ $row->phone ?? '---' }}</td>
                                <td>{{ $row->plan_name ?? '---' }}</td>
                                <td>
                                    @if ($row->status)
                                        <span class="badge bg-{{ $statusColors[$row->status] ?? 'secondary' }}">
                                            {{ __('admin.report.owners.status_labels.' . $row->status) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('admin.report.owners.status_labels.none') }}</span>
                                    @endif
                                </td>
                                <td>{{ $row->boats_used }} / {{ $row->quota }}</td>
                                <td>{{ $row->start_date ? \Illuminate\Support\Carbon::parse($row->start_date)->format('Y-m-d') : '---' }}</td>
                                <td>{{ $row->end_date ? \Illuminate\Support\Carbon::parse($row->end_date)->format('Y-m-d') : '---' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-muted">{{ __('admin.report.owners.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function printReport() {
            const form = document.getElementById('ownersFilters');
            const query = new URLSearchParams(new FormData(form)).toString();
            const printUrl = '{{ route('admin.owner-report.print') }}';

            window.open(query ? `${printUrl}?${query}` : printUrl, '_blank');
        }
    </script>
@endsection
