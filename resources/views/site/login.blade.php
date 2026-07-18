@extends('site.layouts.auth')

@section('title', __('site.login.title') . ' - ' . __('site.meta.title'))
@section('html-class', 'owner-login-viewport')
@section('body-class', 'owner-login-body')

@section('content')
<main class="owner-login-page">
    <div class="auth-form-card owner-login-shell">
        <aside class="owner-login-showcase" aria-label="{{ __('site.login.features_title') }}">
            <div class="owner-login-showcase-grid" aria-hidden="true"></div>

            <a href="{{ route('landing-page') }}" class="owner-login-brand">
                <img src="{{ asset('site/assets/footer-logo.png') }}" alt="{{ __('site.meta.title') }}" />
            </a>

            <div class="owner-login-showcase-copy">
                <span class="owner-login-kicker"><i></i>{{ __('site.login.features_title') }}</span>
                <h2>{{ __('site.login.feature_1') }}</h2>
            </div>

            <div class="owner-login-radar" aria-hidden="true">
                <span class="owner-login-radar-sweep"></span>
                <span class="owner-login-radar-point"></span>
                <svg viewBox="0 0 90 48" fill="none">
                    <path d="M9 28h59l-8 11H25L9 28Z" fill="currentColor" />
                    <path d="M33 28V17h19l7 11M42 17V9m0 0 13 5M42 9v19" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M4 43c9-5 17 5 26 0s17 5 26 0 17 5 26 0" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" opacity=".62" />
                </svg>
            </div>

            <div class="owner-login-snapshot">
                <div>
                    <small>{{ __('site.login.net_profit') }}</small>
                    <strong dir="ltr">{{ __('site.login.net_profit_sample') }}</strong>
                </div>
                <svg viewBox="0 0 74 32" fill="none" aria-hidden="true">
                    <path d="m3 27 15-9 13 5 14-14 10 6L71 3" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="m61 3h10v10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </aside>

        <section class="owner-login-form-panel">
            <div class="owner-login-form-topbar">
                <a href="{{ route('landing-page') }}" class="owner-login-mobile-brand">
                    <img src="{{ asset('site/assets/logo.png') }}" alt="{{ __('site.meta.title') }}" />
                </a>
                <button
                    type="button"
                    class="auth-theme-toggle auth-theme-toggle-form owner-login-theme-toggle"
                    data-theme-toggle
                    aria-label="{{ __('marketing.nav.toggle_theme') }}"
                    aria-pressed="false"
                >
                    <svg class="theme-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20.5 15.2A8.5 8.5 0 0 1 8.8 3.5 8.5 8.5 0 1 0 20.5 15.2Z" /></svg>
                    <svg class="theme-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="3.5" /><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" /></svg>
                </button>
            </div>

            <div class="owner-login-form-content">
                <span class="owner-login-form-kicker">{{ __('site.login.title') }}</span>
                <h1>{{ __('site.login.title') }}</h1>
                <p class="owner-login-intro">
                    {{ __('site.login.no_account') }}
                    <a href="{{ route('landing-page') }}#pricing">{{ __('marketing.nav.plans') }}</a>
                </p>

                @if($errors->any())
                    <div class="auth-error owner-login-error" role="alert">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 8v5m0 3h.01" /></svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form id="loginForm" action="{{ route('frontend.login') }}" method="post" class="owner-login-form">
                    @csrf

                    <label class="owner-login-field" for="emailInput">
                        <span>{{ __('site.login.email') }}</span>
                        <span class="owner-login-input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3" /><path d="m5 8 7 5 7-5" /></svg>
                            <input id="emailInput" name="email" type="email" value="{{ old('email') }}" placeholder="{{ __('site.login.email_placeholder') }}" dir="ltr" autocomplete="email" required autofocus @class(['is-invalid' => $errors->has('email')]) />
                        </span>
                        @error('email')<small>{{ $message }}</small>@enderror
                    </label>

                    <label class="owner-login-field" for="passwordInput">
                        <span>{{ __('site.login.password') }}</span>
                        <span class="owner-login-input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="3" /><path d="M8 10V7a4 4 0 0 1 8 0v3" /></svg>
                            <input id="passwordInput" name="password" type="password" placeholder="{{ __('site.login.password_placeholder') }}" autocomplete="current-password" required @class(['is-invalid' => $errors->has('password')]) />
                            <button id="togglePassword" type="button" aria-label="{{ __('site.aria.toggle_password') }}" aria-pressed="false">
                                <svg class="owner-login-eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" /><circle cx="12" cy="12" r="3" /></svg>
                                <svg class="owner-login-eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m3 3 18 18M10.6 5.2A10.6 10.6 0 0 1 12 5c6.5 0 10 7 10 7a15.8 15.8 0 0 1-2.2 3.3M6.2 6.2C3.5 8.1 2 12 2 12s3.5 7 10 7a9.8 9.8 0 0 0 3-.5M9.9 9.9a3 3 0 0 0 4.2 4.2" /></svg>
                            </button>
                        </span>
                        @error('password')<small>{{ $message }}</small>@enderror
                    </label>

                    <label class="owner-login-remember">
                        <input name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }} />
                        <span aria-hidden="true"><svg viewBox="0 0 16 16" fill="none"><path d="m3.5 8 3 3 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
                        {{ __('site.login.remember') }}
                    </label>

                    <button type="submit" class="owner-login-submit">
                        <span>{{ __('site.login.submit') }}</span>
                        <svg class="rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                    </button>
                </form>
            </div>
        </section>
    </div>
</main>

@push('scripts')
<script>
    document.getElementById('togglePassword')?.addEventListener('click', function () {
        var input = document.getElementById('passwordInput');

        if (input) {
            var willShowPassword = input.type === 'password';

            input.type = willShowPassword ? 'text' : 'password';
            this.setAttribute('aria-pressed', String(willShowPassword));
        }
    });
</script>
@endpush
@endsection
