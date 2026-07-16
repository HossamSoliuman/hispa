@extends('gov.layouts.master')

@section('title', $pageTitle)

@section('css')
    <style>
        .coming-soon-wrap { min-height: 62vh; display: flex; align-items: center; justify-content: center; }
        .coming-soon-card {
            max-width: 560px; width: 100%; text-align: center;
            background: linear-gradient(145deg, rgba(var(--gov-green-rgb), .07), var(--gov-surface) 38%);
            border: 1px solid rgba(var(--gov-green-rgb), .28); border-radius: 8px;
            overflow: hidden; padding: 3rem 2.25rem; position: relative;
            box-shadow: 0 18px 38px var(--gov-shadow), 0 0 28px rgba(var(--gov-green-rgb), .1);
        }
        .coming-soon-card::before {
            background: linear-gradient(90deg, #2a8de8 0 76%, var(--gov-gold) 76% 100%);
            box-shadow: 0 0 16px rgba(42, 141, 232, .4); content: '';
            height: 4px; inset: 0 0 auto; position: absolute;
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
        .coming-soon-card h2 { font-weight: 800; color: var(--gov-ink); margin-bottom: .5rem; }
        .coming-soon-card p { color: var(--gov-muted); margin-bottom: 1.75rem; }
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
