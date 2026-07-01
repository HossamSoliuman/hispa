@extends('admin.layouts.master')
@section('title')
    {{ __('admin.boats.show.title') }} - {{ $boat->name }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.boats.show.title') }}: {{ $boat->name }}</h2>
            <p class="text-muted mb-0 small">{{ __('admin.boats.owner') }}: {{ $boat->owner->name ?? '--' }}</p>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.boats.edit', $boat->id) }}" class="btn btn-outline-success btn-equal">
                <i class="bi bi-pencil"></i> {{ __('admin.actions.edit') }}
            </a>
            <a href="{{ route('admin.boats.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.menu.boats') }}
            </a>
        </div>
    </div>

    <div class="row mb-3">
        @include('owner.components.stat-card', [
            'title' => __('admin.boats.show.revenue'),
            'value' => new \Illuminate\Support\HtmlString(number_format($revenues ?? 0, 2) . ' ' . __('admin.units.sar')),
            'icon' => 'bi bi-cash-coin',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.boats.show.expenses'),
            'value' => new \Illuminate\Support\HtmlString(number_format($expenses ?? 0, 2) . ' ' . __('admin.units.sar')),
            'icon' => 'bi bi-arrow-down-circle',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.boats.show.salaries'),
            'value' => new \Illuminate\Support\HtmlString(number_format($payrolls ?? 0, 2) . ' ' . __('admin.units.sar')),
            'icon' => 'bi bi-people',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.boats.show.boat_status'),
            'value' => $boat->status == 1 ? __('admin.status.active') : __('admin.status.inactive'),
            'icon' => 'bi bi-toggle-on',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('admin.boats.show.title') }}</h5></div>
        <div class="card-body">
            <table class="table table-bordered align-middle mb-0">
                <tbody>
                    <tr>
                        <th style="width:25%">{{ __('admin.boats.name_ar') }}</th><td>{{ $boat->name_ar ?? $boat->name }}</td>
                        <th style="width:25%">{{ __('admin.boats.name_en') }}</th><td>{{ $boat->name_en ?? '--' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.boats.number') }}</th><td>{{ $boat->number ?? '--' }}</td>
                        <th>{{ __('admin.boats.category') }}</th><td>{{ optional($boat->boatType)->name ?? '--' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.boats.owner') }}</th><td>{{ $boat->owner->name ?? '--' }}</td>
                        <th>{{ __('admin.boats.captain') ?? 'الكابتن' }}</th><td>{{ $boat->captain->name ?? '--' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.boats.length') }}</th><td>{{ $boat->length ?? '--' }}</td>
                        <th>{{ __('admin.boats.width') }}</th><td>{{ $boat->width ?? '--' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.boats.color') }}</th><td>{{ $boat->color ?? '--' }}</td>
                        <th>{{ __('admin.boats.payload') }}</th><td>{{ $boat->payload ?? '--' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.boats.license_date') }}</th><td>{{ $boat->license_date ?? '--' }}</td>
                        <th>{{ __('admin.boats.license_date_expire') }}</th><td>{{ $boat->license_date_expire ?? '--' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.boats.engine_type') }}</th><td>{{ $boat->engine_type ?? '--' }}</td>
                        <th>{{ __('admin.boats.engine_power') }}</th><td>{{ $boat->engine_power ?? '--' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.boats.crew_number') }}</th><td>{{ $boat->crew_number ?? '--' }}</td>
                        <th>{{ __('admin.boats.serial_number') }}</th><td>{{ $boat->serial_number ?? '--' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
