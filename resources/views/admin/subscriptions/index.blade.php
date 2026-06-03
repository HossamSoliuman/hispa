@extends('admin.layouts.master')
@section('title')
    {{ __('admin.menu.subscriptions') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <style>
        .small-text th, .small-text td {
            font-size: 12px;
            text-align: center !important;
            vertical-align: middle;
        }
        .subscription-stat-card {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
        }
        .subscription-stat-card:hover {
            color: inherit;
        }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.menu.subscriptions') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-outline-theme btn-equal">
                <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.subscriptions.add') }}
            </a>
        </div>
    </div>

    {{-- Subscription statistics boxes --}}
    @php
        $totalSubscriptions = $activeCount + $expiredCount + $trialCount + $suspendedCount;
    @endphp
    <div class="row mb-4 g-3">
        <div class="col-6 col-md-4 col-lg">
            <a href="{{ route('admin.subscriptions.index') }}" class="subscription-stat-card">
                @include('owner.components.stat-card', [
                    'title' => __('admin.subscriptions.all'),
                    'value' => $totalSubscriptions,
                    'icon' => 'bi bi-collection',
                    'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
                    'colClass' => 'col-12',
                ])
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <a href="{{ route('admin.subscriptions.index', ['status' => 'active']) }}" class="subscription-stat-card">
                @include('owner.components.stat-card', [
                    'title' => __('admin.subscriptions.active'),
                    'value' => $activeCount,
                    'icon' => 'bi bi-check-circle-fill',
                    'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
                    'colClass' => 'col-12',
                ])
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <a href="{{ route('admin.subscriptions.index', ['status' => 'expired']) }}" class="subscription-stat-card">
                @include('owner.components.stat-card', [
                    'title' => __('admin.subscriptions.expired'),
                    'value' => $expiredCount,
                    'icon' => 'bi bi-x-circle-fill',
                    'gradient' => 'linear-gradient(135deg, #c0392b, #e74c3c)',
                    'colClass' => 'col-12',
                ])
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <a href="{{ route('admin.subscriptions.index', ['status' => 'trial']) }}" class="subscription-stat-card">
                @include('owner.components.stat-card', [
                    'title' => __('admin.subscriptions.trial'),
                    'value' => $trialCount,
                    'icon' => 'bi bi-clock-history',
                    'gradient' => 'linear-gradient(135deg, #f39c12, #f1c40f)',
                    'colClass' => 'col-12',
                ])
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <a href="{{ route('admin.subscriptions.index', ['suspended' => '1']) }}" class="subscription-stat-card">
                @include('owner.components.stat-card', [
                    'title' => __('admin.subscriptions.suspended'),
                    'value' => $suspendedCount,
                    'icon' => 'bi bi-pause-circle-fill',
                    'gradient' => 'linear-gradient(135deg, #7f8c8d, #95a5a6)',
                    'colClass' => 'col-12',
                ])
            </a>
        </div>
    </div>

    @php
        $subscriptionStatusOptions = [
            ['value' => '', 'label' => __('admin.subscriptions.all')],
            ['value' => 'active', 'label' => __('admin.subscriptions.active')],
            ['value' => 'expired', 'label' => __('admin.subscriptions.expired')],
            ['value' => 'trial', 'label' => __('admin.subscriptions.trial')],
        ];
        $subscriptionSuspendedOptions = [
            ['value' => '', 'label' => __('admin.filters.all')],
            ['value' => '1', 'label' => __('admin.subscriptions.suspended')],
        ];
    @endphp
    <x-admin.components.datatable-filters
        formId="subscriptionsFilters"
        formAction="{{ route('admin.subscriptions.index') }}"
        formMethod="get"
        :showSearchButton="true"
        :showResetButton="true"
        :filters="[
            ['type' => 'select-static', 'id' => 'status', 'name' => 'status', 'label' => __('admin.subscriptions.status'), 'options' => $subscriptionStatusOptions, 'selected' => request('status')],
            ['type' => 'select-static', 'id' => 'suspended', 'name' => 'suspended', 'label' => __('admin.subscriptions.suspended'), 'options' => $subscriptionSuspendedOptions, 'selected' => request('suspended')],
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
                            <th>{{ __('admin.subscriptions.user') }}</th>
                            <th>{{ __('admin.subscriptions.package') }}</th>
                            <th>{{ __('admin.subscriptions.start_date') }}</th>
                            <th>{{ __('admin.subscriptions.end_date') }}</th>
                            <th>{{ __('admin.subscriptions.status') }}</th>
                            <th>{{ __('admin.subscriptions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $subscription)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $subscription->user->name ?? '--' }}</td>
                            <td>{{ $subscription->package->name ?? '--' }}</td>
                            <td>{{ $subscription->start_date ? \Carbon\Carbon::parse($subscription->start_date)->format('Y-m-d') : '--' }}</td>
                            <td>{{ $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('Y-m-d') : '--' }}</td>
                            <td>
                                @if($subscription->is_suspended)
                                    <span class="badge bg-secondary">{{ __('admin.subscriptions.suspended') }}</span>
                                @elseif($subscription->status == 'active' && $subscription->end_date >= \Carbon\Carbon::today())
                                    <span class="badge bg-success">{{ __('admin.subscriptions.active') }}</span>
                                @elseif($subscription->status == 'expired' || $subscription->end_date < \Carbon\Carbon::today())
                                    <span class="badge bg-danger">{{ __('admin.subscriptions.expired') }}</span>
                                @elseif($subscription->status == 'trial')
                                    <span class="badge bg-warning">{{ __('admin.subscriptions.trial') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $subscription->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ __('admin.subscriptions.no_subscriptions') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>
@endsection
