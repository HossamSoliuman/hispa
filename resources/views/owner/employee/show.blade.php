@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.view_employee') }}
@endsection
@section('content')
    @php
        $sarIcon = view('components.riyal-icon', [
            'size' => 'sm',
            'style' => 'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-inline-start:.2rem;',
        ])->render();
    @endphp

    {{-- Page header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <ul class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('owner.employee.index') }}">{{ __('owner.generated.employees_management') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.generated.view_employee') }}</li>
            </ul>
            <h1 class="h3 fw-bold mb-0">
                <i class="bi bi-person-badge text-primary me-2"></i>{{ $user->name }}
            </h1>
        </div>
    </div>

    {{-- Profile hero --}}
    @include('owner.partials._person_profile', [
        'user' => $user,
        'editRoute' => route('owner.employee.edit', $user->id),
    ])

    {{-- KPI cards --}}
    <div class="row g-3 mb-3">
        @include('owner.components.stat-card', [
            'title' => __('owner.payrolls.statement.total_unpaid'),
            'value' => number_format($stats->unpaid_dues ?? 0, 2) . ' ' . $sarIcon,
            'icon' => 'bi bi-cash-stack',
            'colClass' => 'col-6 col-lg-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('owner.crew_advances.total'),
            'value' => number_format($stats->total_advances ?? 0, 2) . ' ' . $sarIcon,
            'icon' => 'bi bi-wallet2',
            'colClass' => 'col-6 col-lg-3',
        ])
    </div>

    {{-- Advances (السلف) --}}
    @include('owner.crew-advances._profile', ['user' => $user])
    @include('owner.crew-advances._modal', ['people' => collect([$user]), 'selectedUserId' => $user->id])
@endsection
