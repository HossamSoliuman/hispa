@extends('site.layouts.auth')

@section('title', __('site.login.title') . ' - ' . __('site.meta.title'))

@section('content')
<main class="auth-page min-h-screen bg-transparent">
    <div class="min-h-screen">
        <section class="auth-form-panel flex items-center justify-center px-5 py-8 sm:px-8 lg:px-12">
            <div class="auth-form-card w-full max-w-md border border-ocean/15 bg-white/35 p-6 backdrop-blur-[6px] sm:p-8">
                <div class="flex items-center justify-between gap-5">
                    <a href="{{ route('landing-page') }}" class="inline-flex">
                        <img src="{{ asset('site/assets/logo.png') }}" alt="{{ __('site.meta.title') }}" class="theme-logo-light h-auto w-24 object-contain" />
                        <img src="{{ asset('site/assets/footer-logo.png') }}" alt="" class="theme-logo-dark h-auto w-24 object-contain" aria-hidden="true" />
                    </a>
                    <button
                        type="button"
                        class="auth-theme-toggle auth-theme-toggle-form inline-grid h-11 w-11 place-items-center border border-ocean/20 bg-white/55 text-ocean hover:border-ocean/40"
                        data-theme-toggle
                        aria-label="{{ __('marketing.nav.toggle_theme') }}"
                        aria-pressed="false"
                    >
                        <svg class="theme-icon-moon h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20.5 15.2A8.5 8.5 0 0 1 8.8 3.5 8.5 8.5 0 1 0 20.5 15.2Z" /></svg>
                        <svg class="theme-icon-sun h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="3.5" /><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" /></svg>
                    </button>
                </div>

                <span class="eyebrow mt-10 text-ocean">{{ __('site.login.title') }}</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-[-0.04em] text-ink md:text-4xl">{{ __('site.login.title') }}</h1>
                <p class="mt-3 text-sm leading-7 text-ink/52">
                    {{ __('site.login.no_account') }}
                    <a href="{{ route('landing-page') }}#pricing" class="font-bold text-ocean underline decoration-ocean/25 underline-offset-4">{{ __('marketing.nav.plans') }}</a>
                </p>

                @if($errors->any())
                    <div class="auth-error mt-6 border border-red-200 bg-red-50 px-4 py-3 text-start text-sm text-red-700" role="alert">{{ $errors->first() }}</div>
                @endif

                <form id="loginForm" action="{{ route('frontend.login') }}" method="post" class="mt-8 grid gap-5">
                    @csrf
                    <div>
                        <label for="emailInput" class="mb-2 block text-xs font-bold text-ink/72">{{ __('site.login.email') }}</label>
                        <input id="emailInput" name="email" type="email" value="{{ old('email') }}" placeholder="{{ __('site.login.email_placeholder') }}" class="checkout-field text-left" dir="ltr" autocomplete="email" required autofocus />
                        @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="passwordInput" class="mb-2 block text-xs font-bold text-ink/72">{{ __('site.login.password') }}</label>
                        <div class="relative">
                            <input id="passwordInput" name="password" type="password" placeholder="{{ __('site.login.password_placeholder') }}" class="checkout-field pe-11" autocomplete="current-password" required />
                            <button id="togglePassword" type="button" class="absolute inset-y-0 end-2 grid w-9 place-items-center text-ink/38 hover:text-ocean" aria-label="{{ __('site.aria.toggle_password') }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" /><circle cx="12" cy="12" r="3" /></svg>
                            </button>
                        </div>
                        @error('password')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-medium text-ink/58">
                        <input name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-ink/20 accent-ocean focus:ring-ocean" {{ old('remember') ? 'checked' : '' }} />
                        {{ __('site.login.remember') }}
                    </label>

                    <button type="submit" class="inline-flex min-h-13 w-full items-center justify-center gap-2 bg-ocean px-6 py-3.5 text-sm font-bold text-white shadow-[0_12px_28px_rgba(54,117,194,.16)] hover:-translate-y-0.5 hover:bg-ocean-deep">
                        {{ __('site.login.submit') }}
                        <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
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
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    });
</script>
@endpush
@endsection
