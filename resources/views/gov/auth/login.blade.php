@php $isGovDarkTheme = request()->cookie('gov_theme') !== 'light'; @endphp
@extends('gov.layouts.master-auth')

@section('title')
    {{ __('gov.title') }} - {{ __('gov.auth.login') }}
@endsection

@section('css')
    <style>
        html, body, button, input { font-family: 'Tajawal', sans-serif !important; }
        .invalid-feedback { display: block; }
        .auth-card { max-width: 440px; margin: 0 auto; }
        .login-page {
            min-height: 100vh;
            background:
                radial-gradient(circle at 22% 18%, rgba(46, 209, 155, .18), transparent 28rem),
                linear-gradient(135deg, #edf7f2 0%, #d9eee6 100%);
            position: relative;
            overflow: hidden;
        }
        [data-bs-theme='dark'] .login-page {
            background:
                radial-gradient(circle at 22% 18%, rgba(46, 209, 155, .15), transparent 28rem),
                linear-gradient(135deg, #061620 0%, #0a2739 56%, #0b3c45 100%);
        }
        .login-page::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(75, 140, 151, .07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(75, 140, 151, .07) 1px, transparent 1px);
            background-size: 34px 34px;
        }
        .login-content { position: relative; z-index: 1; }
        .login-card {
            background: #fff; border: 1px solid rgba(42, 141, 232, .28); border-radius: 8px;
            box-shadow: 0 25px 55px rgba(7, 30, 39, .18), 0 0 28px rgba(42, 141, 232, .08);
            overflow: hidden; position: relative;
        }
        .login-card::before {
            background: linear-gradient(90deg, #2a8de8 0 76%, #f2aa3c 76% 100%);
            box-shadow: 0 0 16px rgba(42, 141, 232, .46); content: '';
            height: 4px; inset: 0 0 auto; position: absolute; z-index: 2;
        }
        .login-card .card-body { padding: 2.25rem 2rem; background: transparent; }
        [data-bs-theme='dark'] .login-card {
            background: linear-gradient(145deg, rgba(42, 141, 232, .1), rgba(11, 34, 48, .98) 38%);
            border-color: rgba(42, 141, 232, .42);
            box-shadow: 0 25px 60px rgba(0, 0, 0, .35), 0 0 34px rgba(42, 141, 232, .13);
        }
        .login-logo-wrap { display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 1.25rem; }
        .login-logo { max-height: 64px; width: auto; object-fit: contain; display: block; mix-blend-mode: multiply; }
        [data-bs-theme='dark'] .login-logo { filter: invert(1) grayscale(1) brightness(3); mix-blend-mode: screen; }
        .login-badge {
            display: inline-block;
            background: linear-gradient(135deg, #0a5c3a 0%, #0b7a4b 100%);
            color: #fff; font-size: 0.75rem; font-weight: 600;
            padding: 0.35rem 0.85rem; border-radius: 50px; margin-bottom: 1rem; letter-spacing: 0.02em;
            box-shadow: 0 2px 8px rgba(11, 122, 75, 0.35);
        }
        .login-title { font-size: 1.5rem; font-weight: 700; color: #0a5c3a; margin-bottom: 0.35rem; }
        .login-subtitle { color: #64748b; font-size: 0.95rem; margin-bottom: 1.5rem; }
        .login-page .form-control { background: rgba(255, 255, 255, .82); border-radius: 7px; padding: 0.6rem 0.9rem; border: 1px solid #dbe6e1; }
        .login-page .form-control:focus { border-color: #0b7a4b; box-shadow: 0 0 0 3px rgba(11, 122, 75, 0.2); }
        [data-bs-theme='dark'] .login-title { color: #edf8fb; }
        [data-bs-theme='dark'] .login-subtitle,
        [data-bs-theme='dark'] .login-page .form-label,
        [data-bs-theme='dark'] .login-page .form-check-label { color: #91a8b5; }
        [data-bs-theme='dark'] .login-page .form-control {
            background: rgba(6, 24, 36, .7); border-color: rgba(122, 166, 184, .28); color: #edf8fb;
        }
        [data-bs-theme='dark'] .login-page .form-control::placeholder { color: #718895; }
        [data-bs-theme='dark'] .login-page .form-control:focus { border-color: #2ed19b; box-shadow: 0 0 0 3px rgba(46, 209, 155, .14); }
        .login-page .btn-primary {
            border-radius: 10px; padding: 0.65rem 1rem; font-weight: 600;
            background: linear-gradient(135deg, #0a5c3a 0%, #0b7a4b 100%);
            border: 0; box-shadow: 0 4px 14px rgba(11, 122, 75, 0.4);
        }
        .login-page .btn-primary:hover {
            background: linear-gradient(135deg, #064e3b 0%, #0a5c3a 100%);
            box-shadow: 0 6px 20px rgba(11, 122, 75, 0.45); transform: translateY(-1px);
        }
        .login-page .form-check-input:checked { background-color: #0b7a4b; border-color: #0b7a4b; }
        .login-page .form-label { font-weight: 500; color: #334155; }
        .login-theme-toggle {
            align-items: center; background: rgba(255, 255, 255, .82); border: 1px solid rgba(11, 122, 75, .2);
            border-radius: 50%; color: #0a5c3a; display: flex; font-size: 1.05rem; height: 2.75rem;
            inset-inline-end: 1.25rem; justify-content: center; position: absolute; top: 1.25rem;
            transition: box-shadow .2s ease, transform .2s ease; width: 2.75rem; z-index: 2;
        }
        .login-theme-toggle:hover { box-shadow: 0 0 20px rgba(46, 209, 155, .22); transform: rotate(8deg); }
        [data-bs-theme='dark'] .login-theme-toggle {
            background: rgba(11, 42, 59, .85); border-color: rgba(46, 209, 155, .24); color: #7be7c1;
        }
    </style>
@endsection

@section('content')
    <div class="login-page d-flex align-items-center justify-content-center py-4">
        <button type="button" class="login-theme-toggle" data-gov-theme-toggle onclick="govToggleTheme()"
            title="{{ __('gov.theme.toggle') }}" aria-label="{{ __('gov.theme.toggle') }}"
            aria-pressed="{{ $isGovDarkTheme ? 'true' : 'false' }}">
            <i class="bi {{ $isGovDarkTheme ? 'bi-sun' : 'bi-moon-stars' }}" aria-hidden="true"></i>
        </button>
        <div class="login-content w-100 auth-card px-3">
            <div class="card login-card shadow">
                <div class="card-body">
                    <div class="login-logo-wrap">
                        <span class="login-badge">{{ __('gov.auth.badge') }}</span>
                        <img src="{{ asset('logo/' . (app()->getLocale() == 'ar' ? 'arabic' : 'english') . '/main.png') }}" alt="{{ __('gov.title') }}" class="login-logo" />
                    </div>
                    <h1 class="login-title text-center">{{ __('gov.auth.login') }}</h1>
                    <p class="login-subtitle text-center">{{ __('gov.auth.login_subtitle') }}</p>

                    <form method="POST" action="{{ route('gov.login.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('gov.auth.email') }}</label>
                            <input id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                   placeholder="{{ __('gov.auth.email_placeholder') }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('gov.auth.password') }}</label>
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password"
                                   placeholder="{{ __('gov.auth.password_placeholder') }}">
                            @error('password')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">{{ __('gov.auth.remember_me') }}</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">{{ __('gov.auth.login_button') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
