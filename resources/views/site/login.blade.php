@extends('site.layouts.auth')

@section('title', __('site.login.title') . ' - ' . __('site.meta.title'))

@section('content')
<main class="min-h-screen bg-transparent">
    <div class="grid min-h-screen lg:grid-cols-[0.82fr_1.18fr]">
        <aside class="relative hidden overflow-hidden bg-ocean px-12 py-10 text-white lg:flex lg:flex-col">
            <div class="marine-grid pointer-events-none absolute inset-0 opacity-45"></div>
            <div class="pointer-events-none absolute -start-24 top-32 h-80 w-80 rounded-full bg-tide/20 blur-3xl"></div>

            <a href="{{ route('landing-page') }}" class="relative z-10 inline-flex self-start">
                <img src="{{ asset('site/assets/footer-logo.png') }}" alt="{{ __('site.meta.title') }}" class="h-auto w-24 object-contain" />
            </a>

            <div class="relative z-10 my-auto max-w-md text-start">
                <span class="eyebrow text-tide">{{ __('marketing.preview.label') }}</span>
                <h2 class="mt-5 text-4xl font-extrabold leading-tight tracking-[-0.04em]">{{ __('marketing.hero.title') }}</h2>
                <div class="mt-8 grid gap-3">
                    @foreach([__('marketing.hero.proof_1'), __('marketing.hero.proof_2'), __('marketing.hero.proof_3')] as $proof)
                        <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/[0.06] px-4 py-3.5 text-sm font-medium text-white/72">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-tide/18 text-[#72d8ca]">
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M5 12l4 4L19 6" /></svg>
                            </span>
                            {{ $proof }}
                        </div>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('landing-page') }}#pricing" class="relative z-10 inline-flex items-center gap-2 self-start text-xs font-bold text-white/52 hover:text-white">
                {{ __('marketing.hero.primary_cta') }}
                <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
            </a>
        </aside>

        <section class="flex items-center justify-center px-5 py-10 sm:px-8 lg:px-12">
            <div class="w-full max-w-md">
                <a href="{{ route('landing-page') }}" class="inline-flex lg:hidden">
                    <img src="{{ asset('site/assets/logo.png') }}" alt="{{ __('site.meta.title') }}" class="h-auto w-24 object-contain" />
                </a>

                <span class="eyebrow mt-10 text-ocean lg:mt-0">{{ __('site.login.title') }}</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-[-0.04em] text-ink md:text-4xl">{{ __('site.login.title') }}</h1>
                <p class="mt-3 text-sm leading-7 text-ink/52">
                    {{ __('site.login.no_account') }}
                    <a href="{{ route('landing-page') }}#pricing" class="font-bold text-ocean underline decoration-ocean/25 underline-offset-4">{{ __('marketing.nav.plans') }}</a>
                </p>

                @if($errors->any())
                    <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-start text-sm text-red-700" role="alert">{{ $errors->first() }}</div>
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
                        <input name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-ink/20 text-ocean focus:ring-ocean" {{ old('remember') ? 'checked' : '' }} />
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
