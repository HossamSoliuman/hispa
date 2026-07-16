@php($switchLocale = app()->getLocale() === 'ar' ? 'en' : 'ar')

<header class="sticky top-0 z-50 border-b border-white/15 bg-ocean/95 text-white shadow-[0_5px_18px_rgba(35,74,120,.14)] backdrop-blur-md">
    <div class="site-shell flex h-[4.5rem] items-center justify-between gap-5">
        <a href="{{ route('landing-page') }}" class="inline-flex shrink-0 items-center" aria-label="{{ __('site.meta.title') }}">
            <img src="{{ asset('site/assets/footer-logo.png') }}" alt="{{ __('site.meta.title') }}" class="h-auto w-[5.7rem] object-contain" />
        </a>

        <nav class="hidden items-center gap-6 lg:flex" aria-label="{{ __('site.nav.menu') }}">
            <a href="{{ route('landing-page') }}#features" class="text-sm font-medium text-white/75 hover:text-white">{{ __('marketing.nav.product') }}</a>
            <a href="{{ route('landing-page') }}#about" class="text-sm font-medium text-white/75 hover:text-white">{{ __('site.nav.about') }}</a>
            <a href="{{ route('landing-page') }}#pricing" class="text-sm font-medium text-white/75 hover:text-white">{{ __('site.nav.pricing') }}</a>
            <a href="{{ route('landing-page') }}#contact" class="text-sm font-medium text-white/75 hover:text-white">{{ __('site.nav.contact') }}</a>
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
            <a
                href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($switchLocale, null, [], true) }}"
                class="inline-flex h-10 items-center justify-center gap-2 border border-white/25 bg-white/10 px-3 text-xs font-bold text-white hover:bg-white hover:text-ocean"
                lang="{{ $switchLocale }}"
                hreflang="{{ $switchLocale }}"
                aria-label="{{ __('marketing.nav.change_language') }}"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 5h7M7.5 3v2m1.8 0c-.8 3.2-2.6 5.6-5.3 7m2-3c1.2 1.5 2.6 2.7 4.2 3.6M13 19l4-10 4 10m-6.5-4h5" /></svg>
                <span class="hidden xl:inline">{{ $switchLocale === 'ar' ? 'العربية' : 'English' }}</span>
            </a>

            <button
                type="button"
                class="inline-grid h-10 w-10 place-items-center border border-white/25 bg-white/10 text-white hover:bg-white hover:text-ocean"
                data-theme-toggle
                aria-label="{{ __('marketing.nav.toggle_theme') }}"
                aria-pressed="false"
            >
                <svg class="theme-icon-moon h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20.5 15.2A8.5 8.5 0 0 1 8.8 3.5 8.5 8.5 0 1 0 20.5 15.2Z" /></svg>
                <svg class="theme-icon-sun h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="3.5" /><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" /></svg>
            </button>

            @auth('owner')
                <a href="{{ route('owner.dashboard') }}" class="hidden border border-white/35 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white hover:text-ocean sm:inline-flex">
                    {{ __('site.nav.dashboard') }}
                </a>
            @else
                <a href="{{ route('frontend.show_login_form') }}" class="hidden px-2 py-2.5 text-sm font-semibold text-white/75 hover:text-white sm:inline-flex">
                    {{ __('site.nav.login') }}
                </a>
                <a href="{{ route('landing-page') }}#pricing" class="nav-primary-action hidden items-center gap-2 bg-white px-4 py-2.5 text-sm font-bold text-ocean shadow-[0_8px_22px_rgba(35,74,120,.16)] hover:-translate-y-0.5 hover:bg-mist sm:inline-flex">
                    {{ __('marketing.nav.plans') }}
                    <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                </a>
            @endauth

            <button id="menuBtn" type="button" class="inline-grid h-10 w-10 place-items-center border border-white/30 bg-white/10 text-white hover:bg-white hover:text-ocean lg:hidden" aria-label="{{ __('site.nav.open_menu') }}" aria-expanded="false" aria-controls="mobileMenuSheet">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" /></svg>
            </button>
        </div>
    </div>
</header>
