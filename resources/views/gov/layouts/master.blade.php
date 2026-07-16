@php $govTheme = request()->cookie('gov_theme') === 'light' ? 'light' : 'dark'; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
    data-bs-theme="{{ $govTheme }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('gov.title') }} | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="{{ __('gov.meta.description') }}" />
    <link rel="icon" href="{{ asset('storage/uploads/favicon.ico') }}" type="image/x-icon" />
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

    {{-- Government operations theme: a maritime command surface with luminous card rails. --}}
    <style>
        :root {
            --gov-green: #16865f;
            --gov-green-rgb: 22, 134, 95;
            --gov-green-dark: #0d6346;
            --gov-gold: #e6a526;
            --gov-ink: #1f2d27;
            --gov-muted: #74817b;
            --gov-canvas: #f6f8f5;
            --gov-surface: #ffffff;
            --gov-surface-raised: #f8fbf9;
            --gov-border: #e4e9e5;
            --gov-header: rgba(255, 255, 255, .96);
            --gov-sidebar: rgba(255, 255, 255, .97);
            --gov-grid: rgba(31, 45, 39, .022);
            --gov-shadow: rgba(19, 55, 42, .08);
            --gov-header-height: 4rem;
            --gov-sidebar-width: 13.5rem;
        }

        [data-bs-theme='dark'] {
            color-scheme: dark;
            --gov-green: #2ed19b;
            --gov-green-rgb: 46, 209, 155;
            --gov-green-dark: #7be7c1;
            --gov-gold: #f2aa3c;
            --gov-ink: #edf8fb;
            --gov-muted: #91a8b5;
            --gov-canvas: #071722;
            --gov-surface: #0c2433;
            --gov-surface-raised: #112d3e;
            --gov-border: rgba(122, 166, 184, .22);
            --gov-header: rgba(10, 43, 68, .96);
            --gov-sidebar: rgba(7, 27, 40, .97);
            --gov-grid: rgba(100, 170, 194, .045);
            --gov-shadow: rgba(0, 0, 0, .34);
        }

        html,
        body,
        button,
        input,
        select,
        textarea {
            font-family: 'Tajawal', sans-serif !important;
        }

        body {
            background-color: var(--gov-canvas);
            background-image:
                radial-gradient(circle at 58% 16%, rgba(var(--gov-green-rgb), .09), transparent 30rem),
                linear-gradient(var(--gov-grid) 1px, transparent 1px),
                linear-gradient(90deg, var(--gov-grid) 1px, transparent 1px);
            background-attachment: fixed;
            background-size: auto, 34px 34px, 34px 34px;
            color: var(--gov-ink);
            transition: background-color .2s ease, color .2s ease;
        }

        .app {
            background: transparent;
            padding-top: var(--gov-header-height);
        }

        .app-header {
            height: var(--gov-header-height);
            background: var(--gov-header) !important;
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--gov-border);
            box-shadow: 0 8px 28px var(--gov-shadow);
        }

        .app-header .desktop-toggler {
            margin-inline-end: 0;
            margin-inline-start: auto;
        }

        .app-header .brand {
            width: calc(var(--gov-sidebar-width) - 3.5rem);
            padding: 0 .5rem;
        }

        .app-header .brand .brand-logo {
            gap: .55rem !important;
            letter-spacing: 0;
            min-width: 0;
        }

        .app-header .brand .brand-logo img {
            width: 3.9rem;
            height: 2.75rem !important;
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
            padding: .5rem .8rem;
        }

        .app-header .menu .menu-item .menu-link .menu-icon,
        .app-header .menu .menu-item .menu-link .menu-text { color: var(--gov-ink); }

        .app-header .menu .menu-item .menu-link:hover .menu-icon,
        .app-header .menu .menu-item .menu-link:hover .menu-text { color: var(--gov-green); }

        .gov-brand-name {
            color: var(--gov-green-dark);
            font-size: .86rem;
            font-weight: 800;
            letter-spacing: 0;
            line-height: 1.25;
            white-space: nowrap;
        }

        .gov-avatar {
            align-items: center;
            background: var(--gov-gold);
            border: 3px solid rgba(255, 247, 223, .86);
            border-radius: 50%;
            color: #fff;
            display: inline-flex;
            flex: 0 0 auto;
            font-size: .85rem;
            font-weight: 800;
            height: 2.25rem;
            justify-content: center;
            width: 2.25rem;
        }

        .gov-notification-dot {
            background: var(--gov-gold);
            border: 2px solid var(--gov-header);
            border-radius: 50%;
            height: .55rem;
            inset-inline-end: -.1rem;
            position: absolute;
            top: -.15rem;
            width: .55rem;
        }

        .app-sidebar {
            background: var(--gov-sidebar);
            backdrop-filter: blur(18px);
            border-inline-end: 1px solid var(--gov-border);
            box-shadow: -8px 0 30px var(--gov-shadow);
            inset-inline-end: auto;
            inset-inline-start: 0;
            top: var(--gov-header-height);
            width: var(--gov-sidebar-width) !important;
        }

        .app-sidebar .menu { padding: .75rem 0 1.25rem; }

        .app-sidebar .menu .menu-header {
            color: var(--gov-muted);
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .03em;
            padding: .75rem 1.2rem .45rem;
        }

        .app-sidebar .menu .menu-item .menu-link {
            border-inline-start: 3px solid transparent;
            border-radius: 5px;
            color: var(--gov-muted);
            font-size: .82rem;
            font-weight: 500;
            justify-content: flex-start;
            margin: .1rem .65rem;
            min-height: 2.55rem;
            padding: .45rem .7rem;
        }

        .app-sidebar .menu .menu-item .menu-link .menu-icon {
            color: var(--gov-muted);
            font-size: .98rem;
            height: 1.75rem;
            margin: 0;
            margin-inline-end: .7rem;
            width: 1.75rem;
        }

        .app-sidebar .menu .menu-item .menu-link:hover {
            background: rgba(var(--gov-green-rgb), .055);
            color: var(--gov-green-dark);
        }

        .app-sidebar .menu .menu-item .menu-link:hover .menu-icon { color: var(--gov-green); }

        .app-sidebar .menu .menu-item.active > .menu-link {
            background: rgba(var(--gov-green-rgb), .095);
            border-inline-start-color: var(--gov-green);
            color: var(--gov-green-dark);
            font-weight: 700;
        }

        .app-sidebar .menu .menu-item.active > .menu-link .menu-icon { color: var(--gov-green); }

        .app-sidebar .menu .gov-menu-separator {
            border-top: 1px solid var(--gov-border);
            margin: .75rem 1rem;
        }

        .app-content {
            min-width: 0;
            padding: 1.75rem 2.25rem 2.5rem;
        }

        .card {
            background: var(--gov-surface);
            border: 1px solid var(--gov-border) !important;
            border-radius: 8px;
            box-shadow: 0 14px 34px var(--gov-shadow), 0 0 24px rgba(var(--gov-green-rgb), .045) !important;
            overflow: hidden;
            position: relative;
        }

        .card::before {
            background: linear-gradient(90deg, #2a8de8 0 76%, var(--gov-gold) 76% 100%);
            box-shadow: 0 0 15px rgba(42, 141, 232, .32);
            content: '';
            height: 3px;
            inset: 0 0 auto;
            pointer-events: none;
            position: absolute;
            z-index: 2;
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
            background: rgba(var(--gov-green-rgb), .08);
            border: 1px solid rgba(var(--gov-green-rgb), .18);
            border-radius: 50%;
            display: flex;
            height: 2.25rem;
            justify-content: center;
            transition: background-color .2s ease, box-shadow .2s ease, transform .2s ease;
            width: 2.25rem;
        }

        .gov-theme-toggle:hover .menu-icon {
            background: rgba(var(--gov-green-rgb), .15);
            box-shadow: 0 0 18px rgba(var(--gov-green-rgb), .18);
            transform: rotate(8deg);
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
                padding: 1rem;
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
