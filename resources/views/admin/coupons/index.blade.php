@extends('admin.layouts.master')
@section('title')
    {{ __('admin.coupons.title') }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.coupons.page_header') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-outline-theme btn-equal">
                <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.coupons.add') }}
            </a>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-md-4">
            @include('owner.components.stat-card', [
                'title' => __('admin.coupons.total'),
                'value' => $totalCoupons ?? 0,
                'icon' => 'bi bi-tag',
                'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
                'colClass' => 'col-12',
            ])
        </div>
        <div class="col-md-4">
            @include('owner.components.stat-card', [
                'title' => __('admin.coupons.active'),
                'value' => $activeCoupons ?? 0,
                'icon' => 'bi bi-check-circle-fill',
                'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
                'colClass' => 'col-12',
            ])
        </div>
        <div class="col-md-4">
            @include('owner.components.stat-card', [
                'title' => __('admin.coupons.inactive'),
                'value' => $inactiveCoupons ?? 0,
                'icon' => 'bi bi-x-circle-fill',
                'gradient' => 'linear-gradient(135deg, #c0392b, #e74c3c)',
                'colClass' => 'col-12',
            ])
        </div>
    </div>

    @php
        $statusOptions = [
            ['value' => '', 'label' => __('admin.filters.all')],
            ['value' => '1', 'label' => __('admin.status.active')],
            ['value' => '0', 'label' => __('admin.status.inactive')],
        ];
    @endphp
    <x-admin.components.datatable-filters
        formId="couponsFilters"
        formAction="{{ route('admin.coupons.index') }}"
        formMethod="get"
        :showSearchButton="true"
        :showResetButton="true"
        :filters="[
            ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.coupons.code') . ' / ' . __('admin.coupons.name'), 'value' => request('search')],
            ['type' => 'select-static', 'id' => 'status', 'name' => 'status', 'label' => __('admin.coupons.status'), 'options' => $statusOptions, 'selected' => request('status')],
        ]"
        :showArrow="false"
    />

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.table.id') }}</th>
                            <th>{{ __('admin.coupons.name') }}</th>
                            <th>{{ __('admin.coupons.code') }}</th>
                            <th>{{ __('admin.coupons.type') }}</th>
                            <th>{{ __('admin.coupons.value') }}</th>
                            <th>{{ __('admin.coupons.usage_limit') }}</th>
                            <th>{{ __('admin.coupons.times_used') }}</th>
                            <th>{{ __('admin.coupons.valid_from') }}</th>
                            <th>{{ __('admin.coupons.valid_until') }}</th>
                            <th>{{ __('admin.coupons.status') }}</th>
                            <th>{{ __('admin.coupons.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                        <tr>
                            <td>{{ $coupon->id }}</td>
                            <td>{{ $coupon->name ?? '—' }}</td>
                            <td><code class="bg-light px-2 py-1 rounded">{{ $coupon->code }}</code></td>
                            <td>{{ $coupon->type === 'percentage' ? __('admin.coupons.percentage') : __('admin.coupons.fixed') }}</td>
                            <td>{{ $coupon->formatted_value }}</td>
                            <td>{{ $coupon->usage_limit ?? __('admin.coupons.unlimited') }}</td>
                            <td>{{ $coupon->times_used }}</td>
                            <td>{{ $coupon->valid_from ? $coupon->valid_from->format('Y-m-d') : '—' }}</td>
                            <td>{{ $coupon->valid_until ? $coupon->valid_until->format('Y-m-d') : '—' }}</td>
                            <td>
                                @if($coupon->is_active)
                                    <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.coupons.show', $coupon) }}" class="btn btn-sm btn-outline-primary" title="{{ __('admin.actions.view') }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-success" title="{{ __('admin.actions.edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($coupon->invoices()->count() == 0)
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.swal.confirm_text') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('admin.actions.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">{{ __('admin.coupons.no_coupons') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($coupons->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $coupons->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
