<div id="mobileMenuOverlay" class="md:hidden fixed inset-0 z-[60] hidden bg-black/50 backdrop-blur-sm" aria-hidden="true"></div>
<div id="mobileMenuSheet" class="md:hidden fixed right-0 top-0 z-[70] h-full w-[85%] max-w-sm bg-white shadow-2xl closed" role="dialog" aria-modal="true" aria-label="{{ __('site.nav.menu') }}">
    <div class="flex h-16 items-center justify-between border-b border-slate-200 px-6">
        <a href="{{ route('landing-page') }}#home" class="flex items-center gap-2 select-none">
            <img src="{{ asset('site/assets/logo.png') }}" alt="{{ __('site.meta.title') }}" class="h-fit w-20 object-contain" />
        </a>
        <button id="closeMenuBtn" type="button" class="inline-flex items-center justify-center rounded-md p-2 text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="{{ __('site.nav.close_menu') }}">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M18 6L6 18M6 6l12 12" />
            </svg>
        </button>
    </div>
    <div class="flex h-[calc(100%-4rem)] flex-col overflow-y-auto px-6 py-6">
        <nav class="flex-1">
            <ul id="mobileNav" class="flex flex-col gap-1">
                <li class="mobile-nav-item">
                    <a href="{{ route('landing-page') }}#home" class="nav-link flex items-center rounded-lg px-4 py-3 text-base font-medium transition-colors hover:bg-slate-100">{{ __('site.nav.home') }}</a>
                </li>
                <li class="mobile-nav-item">
                    <a href="{{ route('site.about') }}" class="nav-link flex items-center rounded-lg px-4 py-3 text-base font-medium transition-colors hover:bg-slate-100">{{ __('site.nav.about') }}</a>
                </li>
                <li class="mobile-nav-item">
                    <a href="{{ route('site.pricing') }}" class="nav-link flex items-center rounded-lg px-4 py-3 text-base font-medium transition-colors hover:bg-slate-100">{{ __('site.nav.pricing') }}</a>
                </li>
                <li class="mobile-nav-item">
                    <a href="{{ route('site.contact') }}" class="nav-link flex items-center rounded-lg px-4 py-3 text-base font-medium transition-colors hover:bg-slate-100">{{ __('site.nav.contact') }}</a>
                </li>
            </ul>
        </nav>
        <div class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-6">
            <a href="{{ route('frontend.show_login_form') }}" class="mobile-nav-item inline-flex items-center justify-center rounded-lg border border-blue-200 bg-white px-4 py-3 text-base font-semibold text-blue-600 transition-colors hover:bg-blue-50">{{ __('site.nav.login') }}</a>
            <a href="{{ route('frontend.show_register_form') }}" class="mobile-nav-item inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-3 text-base font-semibold text-white shadow-sm transition-colors hover:bg-blue-700">{{ __('site.nav.signup') }}</a>
        </div>
    </div>
</div>
