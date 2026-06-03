@extends('admin.layouts.master')
@section('title')
    {{ __('admin.menu.subscription_packages') }}
@endsection
@section('css')
    <style>
        .small-text th, .small-text td {
            font-size: 12px;
            text-align: center !important;
            vertical-align: middle;
        }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.menu.subscription_packages') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.subscription-packages.create') }}" class="btn btn-outline-theme btn-equal">
                <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.subscription_packages.add') }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Package Statistics -->
        @include('owner.components.stat-card', [
            'title' => __('admin.subscription_packages.total_packages') ?? __('admin.menu.subscription_packages'),
            'value' => $totalPackages ?? 0,
            'icon' => 'bi bi-box-seam',
            'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.subscription_packages.active_packages') ?? __('admin.status.active'),
            'value' => $activePackages ?? 0,
            'icon' => 'bi bi-check-circle-fill',
            'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.subscription_packages.inactive_packages') ?? __('admin.status.inactive'),
            'value' => $inactivePackages ?? 0,
            'icon' => 'bi bi-x-circle-fill',
            'gradient' => 'linear-gradient(135deg, #c0392b, #e74c3c)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

    
    </div>

    @php
        $packageStatusOptions = [
            ['value' => '', 'label' => __('admin.filters.all')],
            ['value' => '1', 'label' => __('admin.status.active')],
            ['value' => '0', 'label' => __('admin.status.inactive')],
        ];
    @endphp
    <x-admin.components.datatable-filters
        formId="subscriptionPackagesFilters"
        formAction="{{ route('admin.subscription-packages.index') }}"
        formMethod="get"
        :showSearchButton="true"
        :showResetButton="true"
        :filters="[
            ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.subscription_packages.name'), 'value' => request('search')],
            ['type' => 'select-static', 'id' => 'status', 'name' => 'status', 'label' => __('admin.subscription_packages.status'), 'options' => $packageStatusOptions, 'selected' => request('status')],
        ]"
        :showArrow="false"
    />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.table.id') }}</th>
                            <th>{{ __('admin.subscription_packages.name') }}</th>
                            <th>{{ __('admin.subscription_packages.boats_count') }}</th>
                            <th>{{ __('admin.subscription_packages.price') }}</th>
                            <th>{{ __('admin.subscription_packages.duration_type') }}</th>
                            <th>{{ __('admin.subscription_packages.status') }}</th>
                            <th>{{ __('admin.subscription_packages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $package->name }}
                                @if($package->is_featured)
                                    <span class="badge bg-warning text-dark ms-1">{{ __('admin.subscription_packages.is_featured') }}</span>
                                @endif
                            </td>
                            <td>{{ $package->boats_count }}</td>
                            <td>
                                @if($package->hasOfferPrice())
                                    <span class="text-decoration-line-through text-muted small">{{ number_format($package->original_price, 2) }}</span>
                                    <span class="text-success">{{ number_format($package->price, 2) }}</span>
                                @else
                                    {{ number_format($package->original_price, 2) }}
                                @endif
                                {{ __('admin.units.sar') }}
                            </td>
                            <td>{{ __('admin.subscription_packages.duration_types.' . $package->duration_type) }}</td>
                            <td>
                                @if($package->is_active)
                                    <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.subscription-packages.show', $package->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.subscription-packages.edit', $package->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($package->subscriptions()->count() == 0)
                                <form action="{{ route('admin.subscription-packages.destroy', $package->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.swal.confirm_text') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ __('admin.subscription_packages.no_packages') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
