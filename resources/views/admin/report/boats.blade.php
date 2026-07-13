@extends('admin.layouts.master')
@section('title')
    {{ __('admin.report.boats.title') }}
@endsection
@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.reports-hub') }}">{{ __('admin.report.boats.report') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.report.boats.title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.report.boats.title') }}</h1>
        </div>
        <div class="ms-auto">
            <button type="button" onclick="printReport()" class="btn btn-outline-theme btn-equal">
                <i class="bi bi-printer me-1"></i> {{ __('admin.report.boats.print') }}
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @include('owner.components.stat-card', [
            'title' => __('admin.report.boats.kpi.total_boats'),
            'value' => new \Illuminate\Support\HtmlString('<span>' . number_format($totalBoats) . '</span>'),
            'icon' => 'bi bi-water',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-6 col-lg-4',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.boats.kpi.active_boats'),
            'value' => new \Illuminate\Support\HtmlString('<span>' . number_format($activeBoats) . '</span>'),
            'icon' => 'bi bi-check-circle',
            'gradient' => 'linear-gradient(135deg, #198754, #157347)',
            'colClass' => 'col-md-6 col-lg-4',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.boats.kpi.owners_covered'),
            'value' => new \Illuminate\Support\HtmlString('<span>' . number_format($ownersCovered) . '</span>'),
            'icon' => 'bi bi-people',
            'gradient' => 'linear-gradient(135deg, #0dcaf0, #0aa2c0)',
            'colClass' => 'col-md-6 col-lg-4',
        ])
    </div>

    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover text-center align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('admin.report.boats.id') }}</th>
                            <th>{{ __('admin.report.boats.name') }}</th>
                            <th>{{ __('admin.report.boats.owner') }}</th>
                            <th>{{ __('admin.report.boats.boat_type') }}</th>
                            <th>{{ __('admin.report.boats.captain') }}</th>
                            <th>{{ __('admin.report.boats.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($boats as $index => $boat)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $boat->name }}</td>
                                <td>{{ optional($boat->owner)->name ?? '---' }}</td>
                                <td>{{ optional($boat->boat_type)->name ?? ($boat->type ?? '---') }}</td>
                                <td>{{ optional($boat->captain)->name ?? '---' }}</td>
                                <td>
                                    @if ($boat->status == 1)
                                        <span class="badge bg-success">{{ __('admin.report.boats.status_active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('admin.report.boats.status_inactive') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted">{{ __('admin.report.boats.no_data') }}</td>
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
            window.open('{{ route('admin.boat-report.print') }}', '_blank');
        }
    </script>
@endsection
