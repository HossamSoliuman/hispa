<div id="header" class="app-header">

    <!-- BEGIN desktop-toggler -->
    <div class="desktop-toggler">
        <button type="button" class="menu-toggler" data-toggle-class="app-sidebar-collapsed"
            data-dismiss-class="app-sidebar-toggled" data-toggle-target=".app">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
    <!-- END desktop-toggler -->

    <!-- BEGIN mobile-toggler -->
    <div class="mobile-toggler">
        <button type="button" class="menu-toggler" data-toggle-class="app-sidebar-mobile-toggled"
            data-toggle-target=".app">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
    <!-- END mobile-toggler -->

    <!-- BEGIN brand -->
    <div class="brand">
        <a href="{{ route('gov.dashboard') }}" class="brand-logo d-flex align-items-center gap-2">
            @php
                $locale = app()->getLocale();
                $logoPath = $locale === 'ar' ? asset('logo/arabic/main.png') : asset('logo/english/main.png');
            @endphp
            <img src="{{ $logoPath }}" alt="{{ __('gov.title') }}">
            <span class="gov-brand-name d-none d-sm-inline">{{ __('gov.title') }}</span>
        </a>
    </div>
    <!-- END brand -->

    <!-- BEGIN menu -->
    <div class="menu">
        {{-- Persistent portal theme --}}
        <div class="menu-item">
            <button type="button" class="menu-link gov-theme-toggle" data-gov-theme-toggle
                onclick="govToggleTheme()" title="{{ __('gov.theme.toggle') }}"
                aria-label="{{ __('gov.theme.toggle') }}" aria-pressed="{{ $govTheme === 'dark' ? 'true' : 'false' }}">
                <span class="menu-icon">
                    <i class="bi {{ $govTheme === 'dark' ? 'bi-sun' : 'bi-moon-stars' }} nav-icon" aria-hidden="true"></i>
                </span>
            </button>
        </div>

        {{-- Language --}}
        <div class="menu-item dropdown dropdown-mobile-full">
            <a href="#" data-bs-toggle="dropdown" data-bs-display="static" class="menu-link">
                <div class="menu-icon"><i class="bi bi-translate nav-icon"></i></div>
                <div class="menu-text d-sm-block d-none">{{ LaravelLocalization::getCurrentLocaleNative() }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-end me-lg-3 fs-11px mt-1">
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <a class="dropdown-item d-flex align-items-center"
                        href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                        {{ $properties['native'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Notifications (placeholder bell) --}}
        <div class="menu-item">
            <a href="{{ route('gov.analytics') }}" class="menu-link" title="{{ __('gov.menu.analytics') }}">
                <div class="menu-icon position-relative">
                    <i class="bi bi-bell nav-icon"></i>
                    <span class="gov-notification-dot" aria-hidden="true"></span>
                </div>
            </a>
        </div>

        {{-- Supervisor identity --}}
        <div class="menu-item dropdown dropdown-mobile-full">
            <a href="#" data-bs-toggle="dropdown" data-bs-display="static" class="menu-link d-flex align-items-center gap-2">
                <span class="gov-avatar">{{ mb_strtoupper(mb_substr(auth('gov')->user()->name ?? 'G', 0, 1)) }}</span>
                <span class="menu-text d-sm-block d-none w-auto fw-semibold">{{ auth('gov')->user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end me-lg-3 fs-11px mt-1">
                <a class="dropdown-item d-flex align-items-center" href="{{ route('gov.profile') }}">
                    <i class="bi bi-person-circle me-2"></i>{{ __('gov.menu.profile') }}
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item d-flex align-items-center" href="{{ route('gov.logout') }}"
                    onclick="event.preventDefault(); document.getElementById('gov-logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-2"></i>{{ __('gov.menu.logout') }}
                </a>
            </div>
        </div>
        <form id="gov-logout-form" action="{{ route('gov.logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
    <!-- END menu -->
</div>
