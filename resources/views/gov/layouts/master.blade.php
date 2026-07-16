<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
    data-bs-theme="light">

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

    {{-- Government operations theme: compact, formal, and reference-led. --}}
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
            --gov-border: #e4e9e5;
            --gov-header-height: 4rem;
            --gov-sidebar-width: 13.5rem;
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
                linear-gradient(rgba(31, 45, 39, .018) 1px, transparent 1px),
                linear-gradient(90deg, rgba(31, 45, 39, .018) 1px, transparent 1px);
            background-size: 34px 34px;
            color: var(--gov-ink);
        }

        .app {
            background: transparent;
            padding-top: var(--gov-header-height);
        }

        .app-header {
            height: var(--gov-header-height);
            background: #fff !important;
            border-bottom: 1px solid var(--gov-border);
            box-shadow: 0 4px 18px rgba(32, 56, 45, .045);
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
            object-fit: contain;
        }

        .app-header .menu-toggler .bar { background: var(--gov-green) !important; }

        .app-header .menu-toggler:hover .bar { background: var(--gov-green-dark) !important; }

        .app-header .menu .menu-item .menu-link {
            min-height: var(--gov-header-height);
            padding: .5rem .8rem;
        }

        .app-header .menu .menu-item .menu-link .menu-icon,
        .app-header .menu .menu-item .menu-link .menu-text { color: #334155; }

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
            border: 3px solid #fff7df;
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
            border: 2px solid #fff;
            border-radius: 50%;
            height: .55rem;
            inset-inline-end: -.1rem;
            position: absolute;
            top: -.15rem;
            width: .55rem;
        }

        .app-sidebar {
            background: rgba(255, 255, 255, .97);
            border-inline-end: 1px solid var(--gov-border);
            box-shadow: -8px 0 26px rgba(32, 56, 45, .025);
            inset-inline-end: auto;
            inset-inline-start: 0;
            top: var(--gov-header-height);
            width: var(--gov-sidebar-width) !important;
        }

        .app-sidebar .menu { padding: .75rem 0 1.25rem; }

        .app-sidebar .menu .menu-header {
            color: #9aa59f;
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .03em;
            padding: .75rem 1.2rem .45rem;
        }

        .app-sidebar .menu .menu-item .menu-link {
            border-inline-start: 3px solid transparent;
            border-radius: 5px;
            color: #637069;
            font-size: .82rem;
            font-weight: 500;
            justify-content: flex-start;
            margin: .1rem .65rem;
            min-height: 2.55rem;
            padding: .45rem .7rem;
        }

        .app-sidebar .menu .menu-item .menu-link .menu-icon {
            color: #8d9993;
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
            box-shadow: 0 7px 22px rgba(37, 57, 48, .045) !important;
        }

        .app-footer {
            color: #94a09a;
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

    @yield('script')
</body>

</html>
