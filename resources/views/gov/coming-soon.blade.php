@extends('gov.layouts.master')

@section('title', $pageTitle)

@section('css')
    <style>
        .coming-soon-wrap { min-height: 62vh; display: flex; align-items: center; justify-content: center; }
        .coming-soon-card {
            max-width: 560px; width: 100%; text-align: center;
            background: #fff; border: 1px solid var(--gov-border); border-radius: 16px;
            padding: 3rem 2.25rem; box-shadow: 0 10px 30px -18px rgba(0, 0, 0, .25);
        }
        .coming-soon-ico {
            width: 92px; height: 92px; border-radius: 50%; margin: 0 auto 1.5rem;
            display: flex; align-items: center; justify-content: center;
            background: rgba(var(--gov-green-rgb), .1); color: var(--gov-green); font-size: 2.6rem;
        }
        .coming-soon-badge {
            display: inline-block; margin-bottom: 1rem; padding: .3rem .9rem; border-radius: 50px;
            background: rgba(var(--gov-green-rgb), .12); color: var(--gov-green); font-weight: 700; font-size: .8rem;
        }
        .coming-soon-card h2 { font-weight: 800; color: #1e293b; margin-bottom: .5rem; }
        .coming-soon-card p { color: #64748b; margin-bottom: 1.75rem; }
        .coming-soon-card .btn-back {
            background: var(--gov-green); color: #fff; border: 0; border-radius: 10px;
            padding: .6rem 1.4rem; font-weight: 600;
        }
        .coming-soon-card .btn-back:hover { background: var(--gov-green-dark); color: #fff; }
    </style>
@endsection

@section('content')
    <div class="coming-soon-wrap">
        <div class="coming-soon-card">
            <div class="coming-soon-ico"><i class="bi bi-cone-striped"></i></div>
            <span class="coming-soon-badge">{{ $pageTitle }}</span>
            <h2>{{ __('gov.coming_soon.title') }}</h2>
            <p>{{ __('gov.coming_soon.message') }}</p>
            <a href="{{ route('gov.dashboard') }}" class="btn btn-back">
                <i class="bi bi-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-1"></i>
                {{ __('gov.coming_soon.back') }}
            </a>
        </div>
    </div>
@endsection
