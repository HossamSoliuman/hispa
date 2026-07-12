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

    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.report.owners.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <button type="button" onclick="printReport()" class="btn btn-outline-theme btn-equal">
                <i class="bi bi-printer me-1"></i> {{ __('admin.report.owners.print') }}
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @include('owner.components.stat-card', [
            'title' => __('admin.report.owners.kpi.total_owners'),
            'value' => new \Illuminate\Support\HtmlString('<span>' . number_format($totalOwners) . '</span>'),
            'icon' => 'bi bi-people',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.owners.kpi.active_owners'),
            'value' => new \Illuminate\Support\HtmlString('<span>' . number_format($activeOwners) . '</span>'),
            'icon' => 'bi bi-patch-check',
            'gradient' => 'linear-gradient(135deg, #198754, #157347)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.owners.kpi.total_boats'),
            'value' => new \Illuminate\Support\HtmlString('<span>' . number_format($totalBoats) . '</span>'),
            'icon' => 'bi bi-water',
            'gradient' => 'linear-gradient(135deg, #fd7e14, #ea5d0a)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.owners.kpi.total_quota'),
            'value' => new \Illuminate\Support\HtmlString('<span>' . number_format($totalQuota) . '</span>'),
            'icon' => 'bi bi-diagram-3',
            'gradient' => 'linear-gradient(135deg, #0dcaf0, #0aa2c0)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
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
            window.open('{{ route('admin.owner-report.print') }}', '_blank');
        }
    </script>
@endsection
