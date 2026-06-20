@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.view_employee') }}
@endsection

@section('css')
    <style>
        .info-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .info-title {
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 10px;
        }

        .info-row {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #333;
            display: inline-block;
            min-width: 120px;
        }

        .info-value {
            color: #555;
        }

        [data-bs-theme=dark] .info-card { background: var(--bs-secondary-bg); box-shadow: none; }
        [data-bs-theme=dark] .info-label { color: var(--bs-emphasis-color); }
        [data-bs-theme=dark] .info-value { color: var(--bs-body-color); }
    </style>
@endsection

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ route('owner.employee.index') }}">{{ __('owner.generated.employees_management') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.generated.view_employee') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.generated.view_employee') }} - {{ $user->name }}</h1>
        </div>
    </div>

    <div class="row mb-3">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center bg-primary text-white p-5 h-80">
                    <div class="mb-4">
                        @if ($user->logo)
                            <img src="{{ asset($user->logo) }}" class="rounded-circle shadow" width="120" height="120"
                                alt="Logo">
                        @else
                            <img src="{{ asset('default-avatar.png') }}" class="rounded-circle shadow" width="120"
                                height="120" alt="Default Logo">
                        @endif
                    </div>

                    <h5 class="mb-1 text-white">{{ $user->name }}</h5>
                    <p class="mb-2 text-white">{{ $user->role }}</p>
                    <p>
                        @if (auth()->user()->status)
                            <span class="badge bg-success p-2"><i class="fa fa-clock"></i>
                                {{ __('owner.assets.active') }}</span>
                        @else
                            <span class="badge bg-danger p-2">{{ __('owner.fish.status_inactive') }}</span>
                        @endif
                    </p>
                </div>
                <div class="text-center h-20">
                    <a href="{{ route('owner.employee.edit', $user->id) }}" class="btn btn-primary rounded-4 m-4">
                        <i class="bi bi-pencil"></i> {{ __('owner.actions.edit') }}</a>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-5 text-start h-100">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-2">
                                <i
                                    class="bi bi-envelope  d-inline-block bg-primary-100 p-2 rounded-3 p-4 fs-4 text-center line-height-100 text-primary"></i>
                            </div>
                            <div class="col-md-10">
                                <p class="my-2">{{ __('owner.generated.email_address') }}</p>
                                <p class="m-0"><strong>{{ $user->email }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mt-3">
                                <i
                                    class="bi bi-telephone d-inline-block  bg-success-100 p-2 rounded-3 p-4 fs-4 text-center line-height-100 text-success"></i>
                            </div>
                            <div class="col-md-10 mt-3">
                                <p class="my-2">{{ __('owner.generated.phone_number_1') }}</p>
                                <p class="m-0"><strong>{{ $user->phone ?? '-----' }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mt-3">
                                <i
                                    class="bi bi-pin-fill d-inline-block  bg-warning-100 p-2 rounded-3 p-4 fs-4 text-center line-height-100 text-warning"></i>
                            </div>
                            <div class="col-md-10 mt-3">
                                <p class="my-2">{{ __('owner.generated.region') }}</p>
                                <p class="m-0"><strong>{{ $user->region?->name ?? '-----' }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mt-3">
                                <i
                                    class="bi bi-pin-map d-inline-block  bg-default-100 p-2 rounded-3 p-4 fs-4 text-center line-height-100 text-default"></i>
                            </div>
                            <div class="col-md-10 mt-3">
                                <p class="my-2">{{ __('owner.generated.governorate') }}</p>
                                <p class="m-0"><strong>{{ $user->governorate?->name ?? '-----' }}</strong>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

        </div>
    </div>
@endsection
