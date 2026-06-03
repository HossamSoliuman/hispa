<header class="w-full border-b border-slate-200 bg-white sticky top-0 z-50">
    <div class="mx-auto max-w-6xl px-6">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-10">
                <a href="{{ route('landing-page') }}#home" class="flex items-center gap-2 select-none">
                    <img src="{{ asset('site/assets/logo.png') }}" alt="{{ __('site.meta.title') }}" class="h-fit w-20 object-contain" />
                </a>
                <nav class="hidden md:block">
                    <ul id="mainNav" class="flex items-center gap-8 text-sm">
                        <li><a href="{{ route('landing-page') }}#home" class="nav-link">{{ __('site.nav.home') }}</a></li>
                        <li><a href="{{ route('site.about') }}" class="nav-link">{{ __('site.nav.about') }}</a></li>
                        <li><a href="{{ route('site.pricing') }}" class="nav-link">{{ __('site.nav.pricing') }}</a></li>
                        <li><a href="{{ route('site.contact') }}" class="nav-link">{{ __('site.nav.contact') }}</a></li>
                    </ul>
                </nav>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('frontend.show_register_form') }}"
                    class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                    {{ __('site.nav.signup') }}
                </a>
                <a href="{{ route('frontend.show_login_form') }}" class="text-sm font-medium text-primary hover:text-primary-dark">
                    {{ __('site.nav.login') }}
                </a>
                <button id="menuBtn" type="button"
                    class="md:hidden inline-flex items-center justify-center rounded-md p-2 text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                    aria-label="{{ __('site.nav.open_menu') }}" aria-expanded="false">
                    <svg id="menuIcon" class="h-5 w-5 transition-all duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="closeIcon" class="h-5 w-5 transition-all duration-200 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>
