@extends('admin.layouts.master')

@section('title')
    {{ __('admin.custom_features.title') }}
@endsection

@section('css')
    <style>
        .feature-control-hero {
            position: relative;
            overflow: hidden;
            padding: 1.75rem;
            border: 1px solid rgba(54, 117, 194, .28);
            background: transparent;
        }

        .feature-control-hero::after {
            content: '';
            position: absolute;
            inset-inline-end: -45px;
            top: -70px;
            width: 190px;
            height: 190px;
            border: 34px solid rgba(54, 117, 194, .08);
            border-radius: 50%;
        }

        .feature-kicker {
            color: #3675c2;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .feature-control-card {
            display: block;
            height: 100%;
            color: inherit;
            text-decoration: none;
            border: 1px solid var(--hud-border);
            background: transparent;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .feature-control-card:hover {
            color: inherit;
            transform: translateY(-4px);
            border-color: #3675c2;
            box-shadow: 0 16px 32px rgba(25, 55, 95, .12);
        }

        .feature-card-band {
            height: 7px;
            background: linear-gradient(90deg, #3675c2 0 78%, #ef8b32 78% 100%);
        }

        .feature-icon {
            width: 58px;
            height: 58px;
            display: grid;
            place-items: center;
            color: #3675c2;
            border: 1px solid rgba(54, 117, 194, .3);
            background: rgba(54, 117, 194, .08);
            font-size: 1.45rem;
        }

        .feature-metric {
            min-width: 88px;
            padding: .7rem .8rem;
            border-inline-start: 2px solid rgba(54, 117, 194, .32);
            background: rgba(54, 117, 194, .045);
        }

        .feature-metric strong {
            display: block;
            font-size: 1.2rem;
            line-height: 1;
        }

        [data-bs-theme="dark"] .feature-control-card {
            background-color: transparent;
        }

    </style>
@endsection

@section('content')
    <div class="feature-control-hero mb-4">
        <div class="position-relative z-1">
            <div class="feature-kicker mb-2">{{ __('admin.custom_features.control_center') }}</div>
            <h2 class="fw-bold mb-2">{{ __('admin.custom_features.title') }}</h2>
            <p class="text-body-secondary mb-0 col-lg-7">{{ __('admin.custom_features.subtitle') }}</p>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 mb-3">
        <span class="feature-icon" style="width: 34px; height: 34px; font-size: .9rem;"><i class="bi bi-grid-1x2"></i></span>
        <div>
            <h5 class="fw-bold mb-0">{{ __('admin.custom_features.available_features') }}</h5>
            <small class="text-body-secondary">{{ __('admin.custom_features.available_features_hint') }}</small>
        </div>
    </div>

    <div class="row g-4">
        @foreach ($features as $item)
            @php($feature = $item['feature'])
            <div class="col-xl-5 col-lg-6">
                <a class="feature-control-card" href="{{ route('admin.custom-features.show', $feature) }}">
                    <div class="feature-card-band"></div>
                    <div class="p-4">
                        <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <span class="feature-icon"><i class="bi bi-diagram-3"></i></span>
                                <div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle mb-2">{{ __('admin.custom_features.admin_controlled') }}</span>
                                    <h4 class="fw-bold mb-1">{{ __('admin.custom_features.features.'.$feature->value.'.name') }}</h4>
                                    <p class="text-body-secondary small mb-0">{{ __('admin.custom_features.features.'.$feature->value.'.description') }}</p>
                                </div>
                            </div>
                            <i class="bi bi-arrow-up-right fs-5 text-primary"></i>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <div class="feature-metric">
                                <strong class="text-success">{{ $item['active_count'] }}</strong>
                                <small class="text-body-secondary">{{ __('admin.custom_features.active_owners') }}</small>
                            </div>
                            <div class="feature-metric">
                                <strong class="text-warning">{{ $item['paused_count'] }}</strong>
                                <small class="text-body-secondary">{{ __('admin.custom_features.paused_owners') }}</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
