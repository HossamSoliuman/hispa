<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <title>{{ __('landing-page.header.ifesh_market') }} | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="{{ asset('storage/uploads/favicon.ico') }}" type="image/x-icon" />

    <!-- Core CSS -->
    <link href="{{ asset('dashboard/assets/css/vendor.min.css') }}" rel="stylesheet">
    @if(app()->getLocale() == 'ar')
        <link href="{{ asset('dashboard/assets/css/app.min-rtl.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    @else
        <link href="{{ asset('dashboard/assets/css/app.min.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    @endif

    <!-- Bootstrap Icons -->
    <link href="{{ asset('dashboard/assets/plugins/bootstrap-icons/font/bootstrap-icons.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #1a202c; /* Match HUD dark theme bg if needed, or let app.css handle it */
            font-family: 'Tajawal', 'Roboto', sans-serif;
        }
        .marketplace-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .app-header {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(31, 41, 55, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .card {
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(31, 41, 55, 0.5);
            backdrop-filter: blur(5px);
        }
        .card:hover {
            border-color: rgba(255,255,255,0.2);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        .fish-icon-bg {
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 8rem;
            opacity: 0.05;
            transform: rotate(-15deg);
        }
    </style>
    @stack('css')
</head>
<body>
    <div id="app" class="app app-header-fixed app-without-sidebar">
        <!-- Header -->
        <div id="header" class="app-header" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1020; background: rgba(31, 41, 55, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between h-100 w-100">

                    <!-- Logo Section (Left/Right based on RTL) -->
                    <div class="d-flex align-items-center" style="min-width: 200px;">
                        <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none text-white">
                            <span class="navbar-logo me-2">
                                @php
                                    $logoPath = app()->getLocale() === 'ar'
                                        ? asset('logo/arabic/main.png')
                                        : asset('logo/english/main.png');
                                @endphp
                                <img src="{{ $logoPath }}" alt="Logo" style="height: 40px; width: auto;">
                            </span>
                            <span class="fw-bold fs-5">{{ __('landing-page.header.ifesh_market') }}</span>
                        </a>
                    </div>

                    <!-- Search Bar (Center) -->
                    <div class="flex-grow-1 mx-4 d-none d-md-block" style="max-width: 600px;">
                        <form action="{{ route('ifesh.marketplace') }}" method="GET">
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-white bg-opacity-10 text-white">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search"
                                       class="form-control border-0 bg-white bg-opacity-10 text-white placeholder-white-50"
                                       placeholder="{{ __('admin.ifesh.search_placeholder') }}"
                                       value="{{ request('search') }}"
                                       style="box-shadow: none;">
                                <button type="submit" class="btn btn-theme">{{ __('admin.actions.search') }}</button>
                            </div>
                        </form>
                    </div>

                    <!-- Actions Section (Right/Left based on RTL) -->
                    <div class="d-flex align-items-center gap-3" style="min-width: 200px; justify-content: flex-end;">
                        <!-- Home Link -->
                        <a href="{{ url('/') }}" class="text-white text-decoration-none fw-bold d-none d-lg-block">
                            {{ __('landing-page.header.home') }}
                        </a>

                        <!-- Language Switcher -->
                        <div class="dropdown">
                            <a href="#" class="text-white text-decoration-none dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                                <i class="bi bi-translate fs-5 me-1"></i>
                                <span class="d-none d-lg-inline">{{ LaravelLocalization::getCurrentLocaleNative() }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between"
                                           href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                            {{ $properties['native'] }}
                                            @if($localeCode == app()->getLocale())
                                                <i class="bi bi-check-lg text-success ms-2"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Auth Buttons -->
                        @auth
                            <a href="{{ route('frontend.dashboard.user') }}" class="btn btn-theme btn-sm fw-bold d-flex align-items-center">
                                <i class="bi bi-person-circle me-2"></i>
                                {{ __('landing-page.header.profile') }}
                            </a>
                        @else
                            <a href="{{ route('frontend.roles') }}" class="btn btn-outline-light btn-sm fw-bold d-flex align-items-center">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                {{ __('landing-page.header.login') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="content" class="app-content" style="margin-left: 0; padding-top: 80px;">
            <div class="marketplace-container">
                @yield('content')
            </div>
        </div>

        <!-- Scroll to top -->
        <a href="#" data-toggle="scroll-to-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('dashboard/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/app.min.js') }}"></script>

    @stack('scripts')
</body>
</html>
