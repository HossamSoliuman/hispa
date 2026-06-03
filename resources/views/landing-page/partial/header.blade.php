<div id="header" class="app-header navbar navbar-expand-lg p-0 shadow-sm">
    <div class="container-xl px-3 px-lg-5">
        <button class="navbar-toggler border-0 p-0 ms-3 fs-24px shadow-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarContent">
            <span class="h-2px w-25px bg-gray-500 d-block mb-1"></span>
            <span class="h-2px w-25px bg-gray-500 d-block"></span>
        </button>

        <a class="navbar-brand d-flex align-items-center position-relative ms-auto brand px-0 w-auto"
            href="{{ route('landing-page') }}">
            <span class="brand-logo d-flex">
                @php
                    $locale = app()->getLocale();
                    // Use the same locale-specific logo approach as admin dashboard
                    $logoPath = $locale === 'ar' ? asset('logo/arabic/main.png') : asset('logo/english/main.png');
                @endphp
                <img src="{{ $logoPath }}" alt="{{ $settings['title'] ?? 'حسبة' }}"
                    style="height: 120px; width: auto; object-fit: contain;">
            </span>
        </a>

        <div class="collapse navbar-collapse" id="navbarContent">
            <div class="w-100 d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                <div
                    class="navbar-nav d-flex flex-column flex-lg-row align-items-center text-uppercase small fw-semibold gap-2">
                    <a href="#home" class="nav-link link-body-emphasis">{{ __('landing-page.header.home') }}</a>
                    <a href="#about" class="nav-link link-body-emphasis">{{ __('landing-page.header.about') }}</a>
                    <a href="#features" class="nav-link link-body-emphasis">{{ __('landing-page.header.features') }}</a>
                    <a href="#contact" class="nav-link link-body-emphasis">{{ __('landing-page.header.contact') }}</a>
                </div>

                <div class="d-flex flex-column flex-lg-row align-items-center gap-2 gap-lg-3 p-2">
                    {{-- Language Dropdown --}}
                    <div class="menu-item dropdown">
                        <a href="#" data-bs-toggle="dropdown" data-bs-display="static"
                            class="menu-link d-flex align-items-center">
                            <div class="menu-icon"><i class="bi bi-translate nav-icon"></i></div>
                            <div class="menu-text ms-1 d-sm-block d-none">
                                {{ LaravelLocalization::getCurrentLocaleNative() }}
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end fs-12px mt-1">
                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                <a class="dropdown-item d-flex align-items-center py-1"
                                    href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                    {{ $properties['native'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Auth Buttons --}}
                    <div class="d-flex flex-wrap gap-2 mt-1 mt-lg-0">
                        {{-- Ifesh Market Button with New Items Count --}}
                        @php
                            $newIfeshItemsCount = 0;
                        @endphp
                        <a href="{{ route('ifesh.marketplace') }}"
                            class="btn btn-outline-theme btn-sm fw-semibold text-uppercase px-3 py-1 fs-11px position-relative">
                            <i class="bi bi-hammer me-1"></i>
                            {{ __('landing-page.header.ifesh_market') }}
                            @if ($newIfeshItemsCount > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 9px; padding: 2px 5px;">
                                    {{ $newIfeshItemsCount }}
                                    <span class="visually-hidden">new items</span>
                                </span>
                            @endif
                        </a>

                        @auth
                            <a href="{{ route('frontend.dashboard.user') }}"
                                class="btn btn-outline-theme btn-sm fw-semibold text-uppercase px-3 py-1 fs-11px">
                                {{ __('landing-page.header.profile') }} <i class="fa fa-user ms-1"></i>
                            </a>
                        @else
                            <a href="{{ route('frontend.roles') }}"
                                class="btn btn-outline-theme btn-sm fw-semibold text-uppercase px-3 py-1 fs-11px">
                                {{ __('landing-page.home.cta') }} <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                            <a href="{{ route('frontend.register') }}"
                                class="btn btn-outline-theme btn-sm fw-semibold text-uppercase px-3 py-1 fs-11px">
                                {{ __('landing-page.header.register') }} <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        @endauth
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
