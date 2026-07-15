@php($mobileSwitchLocale = app()->getLocale() === 'ar' ? 'en' : 'ar')

<div id="mobileMenuOverlay" class="fixed inset-0 z-[60] hidden bg-ink/55 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>
<aside id="mobileMenuSheet" class="marine-grid fixed inset-y-0 end-0 z-[70] flex w-[88%] max-w-sm {{ app()->getLocale() === 'ar' ? '-translate-x-full' : 'translate-x-full' }} flex-col bg-shell shadow-2xl transition-transform duration-300 lg:hidden" role="dialog" aria-modal="true" aria-label="{{ __('site.nav.menu') }}">
    <div class="flex h-[4.75rem] items-center justify-between border-b border-ink/10 px-5">
        <a href="{{ route('landing-page') }}" class="inline-flex items-center">
            <img src="{{ asset('site/assets/logo.png') }}" alt="{{ __('site.meta.title') }}" class="theme-logo-light h-auto w-[5.7rem] object-contain" />
            <img src="{{ asset('site/assets/footer-logo.png') }}" alt="" class="theme-logo-dark h-auto w-[5.7rem] object-contain" aria-hidden="true" />
        </a>
        <button id="closeMenuBtn" type="button" class="grid h-10 w-10 place-items-center rounded-xl border border-ink/10 bg-white text-ink" aria-label="{{ __('site.nav.close_menu') }}">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" /></svg>
        </button>
    </div>

    <nav class="flex flex-1 flex-col gap-2 overflow-y-auto px-5 py-6">
        <div class="mb-4 grid grid-cols-2 gap-3 border-b border-ocean/15 pb-6">
            <a
                href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($mobileSwitchLocale, null, [], true) }}"
                class="inline-flex min-h-11 items-center justify-center gap-2 border border-ocean/25 bg-white px-3 text-sm font-bold text-ocean"
                lang="{{ $mobileSwitchLocale }}"
                hreflang="{{ $mobileSwitchLocale }}"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 5h7M7.5 3v2m1.8 0c-.8 3.2-2.6 5.6-5.3 7m2-3c1.2 1.5 2.6 2.7 4.2 3.6M13 19l4-10 4 10m-6.5-4h5" /></svg>
                {{ $mobileSwitchLocale === 'ar' ? 'العربية' : 'English' }}
            </a>
            <button
                type="button"
                class="inline-flex min-h-11 items-center justify-center gap-2 border border-ocean/25 bg-white px-3 text-sm font-bold text-ocean"
                data-theme-toggle
                aria-label="{{ __('marketing.nav.toggle_theme') }}"
                aria-pressed="false"
            >
                <svg class="theme-icon-moon h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20.5 15.2A8.5 8.5 0 0 1 8.8 3.5 8.5 8.5 0 1 0 20.5 15.2Z" /></svg>
                <svg class="theme-icon-sun h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="3.5" /><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" /></svg>
                {{ __('marketing.nav.theme') }}
            </button>
        </div>

        <a href="{{ route('landing-page') }}#features" class="rounded-xl px-4 py-3.5 text-base font-semibold text-ink hover:bg-white hover:text-ocean">{{ __('marketing.nav.product') }}</a>
        <a href="{{ route('site.pricing') }}" class="rounded-xl px-4 py-3.5 text-base font-semibold text-ink hover:bg-white hover:text-ocean">{{ __('site.nav.pricing') }}</a>
        <a href="{{ route('site.contact') }}" class="rounded-xl px-4 py-3.5 text-base font-semibold text-ink hover:bg-white hover:text-ocean">{{ __('site.nav.contact') }}</a>
    </nav>

    <div class="grid gap-3 border-t border-ink/10 p-5">
        @auth('owner')
            <a href="{{ route('owner.dashboard') }}" class="inline-flex items-center justify-center bg-ocean px-4 py-3.5 text-sm font-bold text-white">{{ __('site.nav.dashboard') }}</a>
        @else
            <a href="{{ route('site.pricing') }}" class="inline-flex items-center justify-center bg-ocean px-4 py-3.5 text-sm font-bold text-white">{{ __('marketing.nav.plans') }}</a>
            <a href="{{ route('frontend.show_login_form') }}" class="inline-flex items-center justify-center rounded-xl border border-ink/12 bg-white px-4 py-3.5 text-sm font-bold text-ink">{{ __('site.nav.login') }}</a>
        @endauth
    </div>
</aside>
