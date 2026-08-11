@php $govTheme = request()->cookie('gov_theme') === 'light' ? 'light' : 'dark'; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
    data-bs-theme="{{ $govTheme }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('gov.title') }} | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="{{ __('gov.meta.description') }}" />
    <link rel="icon" href="{{ $platformFaviconUrl }}" type="image/png" />
    <link rel="apple-touch-icon" href="{{ $platformAppleTouchIconUrl }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ================== BEGIN core-css ================== -->
    <link href="{{ asset('dashboard/assets/css/vendor.min.css') }}" rel="stylesheet">
    @if (app()->getLocale() == 'ar')
        <link href="{{ asset('dashboard/assets/css/app.min-rtl.css') }}" rel="stylesheet">
    @else
        <link href="{{ asset('dashboard/assets/css/app.min.css') }}" rel="stylesheet">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- ================== END core-css ================== -->

    {{-- Government operations theme: a maritime intelligence workspace with blueprint depth. --}}
    <style>
        :root {
            --gov-green: #167a5a;
            --gov-green-rgb: 22, 122, 90;
            --gov-green-dark: #0f6047;
            --gov-gold: #b98524;
            --gov-ink: #1c2b25;
            --gov-muted: #6f7e77;
            --gov-canvas: #f3f6f4;
            --gov-surface: #ffffff;
            --gov-surface-soft: #f0f4f2;
            --gov-surface-raised: #f8faf9;
            --gov-panel: rgba(255, 255, 255, .86);
            --gov-panel-strong: rgba(255, 255, 255, .93);
            --gov-border: #dfe6e2;
            --gov-header: rgba(255, 255, 255, .97);
            --gov-sidebar: #f8faf9;
            --gov-shadow: rgba(18, 47, 36, .07);
            --gov-header-height: 4.25rem;
            --gov-sidebar-width: 14.5rem;
        }

        [data-bs-theme='dark'] {
            color-scheme: dark;
            --gov-green: #43c796;
            --gov-green-rgb: 67, 199, 150;
            --gov-green-dark: #76d9b5;
            --gov-gold: #d6a246;
            --gov-ink: #eef5f3;
            --gov-muted: #8fa49d;
            --gov-canvas: #09151c;
            --gov-surface: #101f27;
            --gov-surface-soft: #152a34;
            --gov-surface-raised: #172932;
            --gov-panel: rgba(14, 34, 45, .76);
            --gov-panel-strong: rgba(14, 34, 45, .88);
            --gov-border: rgba(165, 193, 184, .15);
            --gov-header: rgba(11, 25, 33, .97);
            --gov-sidebar: #0d1b23;
            --gov-shadow: rgba(0, 0, 0, .22);
        }

        html,
        body,
        button,
        input,
        select,
        textarea {
            font-family: 'Tajawal', sans-serif !important;
        }

        html::before,
        html::after,
        body::before {
            background-image: none !important;
            content: none !important;
        }

        body {
            background-color: var(--gov-canvas);
            background-image:
                radial-gradient(circle at 72% -8%, rgba(var(--gov-green-rgb), .07), transparent 32rem),
                radial-gradient(circle at 8% 95%, rgba(38, 111, 136, .035), transparent 26rem);
            background-attachment: fixed;
            color: var(--gov-ink);
            transition: background-color .2s ease, color .2s ease;
        }

        [data-bs-theme='dark'] body {
            background-image:
                linear-gradient(rgba(6, 19, 27, .84), rgba(6, 19, 27, .92)),
                linear-gradient(rgba(122, 167, 184, .04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(122, 167, 184, .04) 1px, transparent 1px),
                url('{{ asset('dashboard/assets/css/images/cover-dark.jpg') }}');
            background-position: center, center, center, center 4rem;
            background-size: auto, 36px 36px, 36px 36px, cover;
        }

        .app {
            background: transparent;
            padding-top: var(--gov-header-height);
        }

        .app-header {
            height: var(--gov-header-height);
            background: var(--gov-header) !important;
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--gov-border);
            box-shadow: 0 2px 12px var(--gov-shadow);
        }

        .app-header .desktop-toggler {
            margin-inline-end: 0;
            margin-inline-start: auto;
        }

        .app-header .brand {
            width: calc(var(--gov-sidebar-width) - 3rem);
            padding: 0 .75rem;
        }

        .app-header .brand .brand-logo {
            gap: .55rem !important;
            letter-spacing: 0;
            min-width: 0;
        }

        .app-header .brand .brand-logo img {
            width: 3.65rem;
            height: 2.5rem !important;
            margin: 0;
            mix-blend-mode: multiply;
            object-fit: contain;
        }

        [data-bs-theme='dark'] .app-header .brand .brand-logo img {
            filter: invert(1) grayscale(1) brightness(3);
            mix-blend-mode: screen;
        }

        .app-header .menu-toggler .bar { background: var(--gov-green) !important; }

        .app-header .menu-toggler:hover .bar { background: var(--gov-green-dark) !important; }

        .app-header .menu .menu-item .menu-link {
            min-height: var(--gov-header-height);
            padding: .5rem .7rem;
        }

        .app-header .menu .menu-item .menu-link .menu-icon,
        .app-header .menu .menu-item .menu-link .menu-text { color: var(--gov-ink); }

        .app-header .menu .menu-item .menu-link:hover .menu-icon,
        .app-header .menu .menu-item .menu-link:hover .menu-text { color: var(--gov-green); }

        .gov-brand-name {
            color: var(--gov-green-dark);
            font-size: .82rem;
            font-weight: 800;
            letter-spacing: 0;
            line-height: 1.25;
            white-space: nowrap;
        }

        .gov-avatar {
            align-items: center;
            background: var(--gov-surface-soft);
            border: 1px solid var(--gov-border);
            border-radius: 50%;
            color: var(--gov-green-dark);
            display: inline-flex;
            flex: 0 0 auto;
            font-size: .85rem;
            font-weight: 800;
            height: 2.35rem;
            justify-content: center;
            width: 2.35rem;
        }

        .app-sidebar {
            background: var(--gov-sidebar);
            border-inline-end: 1px solid var(--gov-border);
            box-shadow: none;
            inset-inline-end: auto;
            inset-inline-start: 0;
            top: var(--gov-header-height);
            width: var(--gov-sidebar-width) !important;
        }

        .app-sidebar .menu { padding: .85rem .7rem 1.5rem; }

        .app-sidebar .menu .menu-header {
            color: var(--gov-muted);
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .025em;
            padding: .85rem .75rem .35rem;
        }

        .app-sidebar .menu .gov-menu-section:first-child .menu-header {
            padding-top: .25rem;
        }

        .app-sidebar .menu .menu-item .menu-link {
            border: 1px solid transparent;
            border-radius: 8px;
            color: var(--gov-muted);
            font-size: .78rem;
            font-weight: 500;
            justify-content: flex-start;
            margin: .05rem 0;
            min-height: 2.35rem;
            padding: .35rem .65rem;
        }

        .app-sidebar .menu .menu-item .menu-link .menu-icon {
            color: var(--gov-muted);
            font-size: .92rem;
            height: 1.65rem;
            margin: 0;
            margin-inline-end: .6rem;
            width: 1.65rem;
        }

        .app-sidebar .menu .menu-item .menu-link:hover {
            background: rgba(var(--gov-green-rgb), .06);
            color: var(--gov-green-dark);
        }

        .app-sidebar .menu .menu-item .menu-link:hover .menu-icon { color: var(--gov-green); }

        .app-sidebar .menu .menu-item.active > .menu-link {
            background: rgba(var(--gov-green-rgb), .1);
            border-color: rgba(var(--gov-green-rgb), .14);
            color: var(--gov-green-dark);
            font-weight: 700;
        }

        .app-sidebar .menu .menu-item.active > .menu-link .menu-icon { color: var(--gov-green); }

        .app-sidebar .menu .gov-menu-separator {
            border-top: 1px solid var(--gov-border);
            margin: 1rem .5rem .65rem;
        }

        .app-content {
            min-width: 0;
            padding: 2rem 2.25rem 2.75rem;
        }

        .card {
            background: var(--gov-panel);
            backdrop-filter: blur(12px);
            border: 1px solid var(--gov-border) !important;
            border-radius: 12px;
            box-shadow: 0 10px 28px var(--gov-shadow) !important;
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: none;
        }

        .dropdown-menu {
            background: var(--gov-surface-raised);
            border-color: var(--gov-border);
            box-shadow: 0 14px 32px var(--gov-shadow);
            color: var(--gov-ink);
        }

        .dropdown-item { color: var(--gov-ink); }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background: rgba(var(--gov-green-rgb), .1);
            color: var(--gov-green-dark);
        }

        .dropdown-divider { border-color: var(--gov-border); }

        .gov-theme-toggle {
            background: transparent;
            border: 0;
            width: auto;
        }

        .gov-theme-toggle .menu-icon {
            align-items: center;
            background: var(--gov-surface-soft);
            border: 1px solid var(--gov-border);
            border-radius: 50%;
            display: flex;
            height: 2.25rem;
            justify-content: center;
            transition: background-color .2s ease, box-shadow .2s ease, transform .2s ease;
            width: 2.25rem;
        }

        .gov-theme-toggle:hover .menu-icon {
            background: rgba(var(--gov-green-rgb), .11);
            transform: rotate(6deg);
        }

        .app-footer {
            color: var(--gov-muted);
            font-size: .75rem;
            font-weight: 500;
        }

        a { color: var(--gov-green); }

        :focus-visible {
            outline: 3px solid rgba(var(--gov-green-rgb), .24);
            outline-offset: 2px;
        }

        @media (min-width: 768px) {
            .app-content,
            .app-sidebar-toggled .app-content {
                margin-inline-end: 0;
                margin-inline-start: var(--gov-sidebar-width);
            }

            .app-footer,
            .app-sidebar-toggled .app-footer {
                margin-inline-end: 2rem;
                margin-inline-start: calc(var(--gov-sidebar-width) + 2rem);
            }

            .app-sidebar-collapsed .app-sidebar {
                margin-inline-start: calc(var(--gov-sidebar-width) * -1);
                opacity: 0;
            }

            .app-sidebar-collapsed .app-content {
                margin-inline-start: 0;
            }

            .app-sidebar-collapsed .app-footer {
                margin-inline-start: 2rem;
            }
        }

        @media (max-width: 767.98px) {
            .app-header .menu .menu-item .menu-link { padding-inline: .6rem; }

            .app-sidebar {
                inset-inline-start: calc(var(--gov-sidebar-width) * -1);
                top: var(--gov-header-height);
            }

            .app-sidebar-mobile-toggled .app-sidebar {
                inset-inline-start: 0;
            }

            .app-content {
                margin: 0;
                padding: 1.15rem;
            }

            .app-footer { margin-inline: 1rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>

    @yield('css')
</head>

<body>
    <div id="app" class="app app-footer-fixed">
        @include('gov.partial.header')
        @include('gov.partial.sidebar')

        <button class="app-sidebar-mobile-backdrop" data-toggle-target=".app"
            data-toggle-class="app-sidebar-mobile-toggled"></button>

        <div id="content" class="app-content">
            @include('gov.partial.alert')
            @yield('content')
        </div>

        @include('gov.partial.footer')

        <a href="#" data-toggle="scroll-to-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>
    </div>

    <!-- ================== BEGIN core-js ================== -->
    <script src="{{ asset('dashboard/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/app.min.js') }}"></script>
    <!-- ================== END core-js ================== -->

    <script>
        function govToggleTheme() {
            var html = document.documentElement;
            var nextTheme = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';

            document.cookie = 'gov_theme=' + nextTheme + ';path=/;max-age=31536000;SameSite=Lax';
            window.location.reload();
        }
    </script>

    @yield('script')
</body>

</html>
