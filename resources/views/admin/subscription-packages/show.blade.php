@extends('admin.layouts.master')
@section('title')
    {{ __('admin.subscription_packages.show.title') }} - {{ $subscriptionPackage->name }}
@endsection
@section('css')
    <style>
        .small-text th, .small-text td { font-size: 12px; text-align: center !important; vertical-align: middle; }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.subscription_packages.show.title') }} - {{ $subscriptionPackage->name }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.subscription-packages.edit', $subscriptionPackage->id) }}" class="btn btn-outline-primary btn-equal">
                <i class="bi bi-pencil"></i> {{ __('admin.actions.edit') }}
            </a>
            <a href="{{ route('admin.subscription-packages.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.actions.cancel') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('admin.subscription_packages.details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscription_packages.name') }} (AR):</strong> {{ $subscriptionPackage->name_ar ?? '--' }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscription_packages.name') }} (EN):</strong> {{ $subscriptionPackage->name_en ?? '--' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscription_packages.boats_count') }}:</strong> {{ $subscriptionPackage->boats_count }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscription_packages.original_price') }}:</strong>
                            {{ number_format($subscriptionPackage->original_price, 2) }} {{ __('admin.units.sar') }}
                        </div>
                    </div>
                    @if($subscriptionPackage->hasOfferPrice())
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscription_packages.offer_price') }}:</strong>
                            <span class="text-decoration-line-through text-muted me-1">{{ number_format($subscriptionPackage->original_price, 2) }}</span>
                            <span class="text-success fw-bold">{{ number_format($subscriptionPackage->price, 2) }}</span>
                            {{ __('admin.units.sar') }}
                        </div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscription_packages.effective_price') }}:</strong>
                            <span class="fw-bold">{{ number_format($subscriptionPackage->effective_price, 2) }}</span> {{ __('admin.units.sar') }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscription_packages.duration_type') }}:</strong> {{ __('admin.subscription_packages.duration_types.' . $subscriptionPackage->duration_type) }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscription_packages.status') }}:</strong>
                            @if($subscriptionPackage->is_active)
                                <span class="badge bg-success">{{ __('admin.subscription_packages.active') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('admin.subscription_packages.inactive') }}</span>
                            @endif
                            @if($subscriptionPackage->is_featured)
                                <span class="badge bg-warning text-dark ms-1">{{ __('admin.subscription_packages.is_featured') }}</span>
                            @endif
                        </div>
                    </div>
                    @php
                        $descAr = $subscriptionPackage->feature_ar;
                        $descEn = $subscriptionPackage->feature_en;
                        $listAr = is_string($descAr) ? json_decode($descAr, true) : $descAr;
                        $listEn = is_string($descEn) ? json_decode($descEn, true) : $descEn;
                        if (!is_array($listAr)) {
                            $listAr = $descAr ? [$descAr] : [];
                        }
                        if (!is_array($listEn)) {
                            $listEn = $descEn ? [$descEn] : [];
                        }
                    @endphp
                    @if(!empty($listAr) || !empty($listEn))
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscription_packages.feature_ar') }}:</strong>
                            @if(!empty($listAr))
                                <ul class="mb-0 ps-3">
                                    @foreach(array_filter($listAr) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mb-0 text-muted">--</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscription_packages.feature_en') }}:</strong>
                            @if(!empty($listEn))
                                <ul class="mb-0 ps-3">
                                    @foreach(array_filter($listEn) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mb-0 text-muted">--</p>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscription_packages.created_at') }}:</strong> {{ $subscriptionPackage->created_at?->format('Y-m-d H:i') ?? '--' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('admin.subscription_packages.statistics') }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>{{ __('admin.subscription_packages.total_subscriptions') }}:</strong> {{ $subscriptionPackage->subscriptions->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('admin.subscription_packages.subscriptions_list') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover text-center small-text">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('admin.subscriptions.user') }}</th>
                            <th>{{ __('admin.subscriptions.start_date') }}</th>
                            <th>{{ __('admin.subscriptions.end_date') }}</th>
                            <th>{{ __('admin.subscriptions.status') }}</th>
                            <th>{{ __('admin.subscription_packages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptionPackage->subscriptions as $sub)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $sub->user->name ?? '--' }}</td>
                            <td>{{ $sub->start_date ? $sub->start_date->format('Y-m-d') : '--' }}</td>
                            <td>{{ $sub->end_date ? $sub->end_date->format('Y-m-d') : '--' }}</td>
                            <td>
                                @if($sub->is_suspended)
                                    <span class="badge bg-secondary">{{ __('admin.subscription_packages.suspended') }}</span>
                                @elseif($sub->status == 'active')
                                    <span class="badge bg-success">{{ __('admin.subscription_packages.active') }}</span>
                                @elseif($sub->status == 'expired')
                                    <span class="badge bg-danger">{{ __('admin.subscription_packages.expired') }}</span>
                                @elseif($sub->status == 'trial')
                                    <span class="badge bg-warning">{{ __('admin.subscription_packages.trial') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $sub->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.subscriptions.show', $sub->id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('admin.subscription_packages.no_packages') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
