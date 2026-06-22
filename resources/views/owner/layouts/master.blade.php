@php $ownerTheme = request()->cookie('owner_theme') === 'dark' ? 'dark' : 'light'; @endphp
<!DOCTYPE html dir="rtl">
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
    data-bs-theme="{{ $ownerTheme }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('owner.title') }} | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="{{ __('owner.generated.meta_desc_hasbah') }}" />
    <meta name="author" content="{{ __('owner.generated.meta_author') }}" />
    <meta name="keywords" content="{{ __('owner.generated.meta_keywords') }}" />
    <link rel="icon" href="{{ asset('storage/uploads/favicon.ico') }}" type="image/x-icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ================== BEGIN core-css ================== -->
    <link href="{{ asset('dashboard/assets/css/vendor.min.css') }}" rel="stylesheet">
    @if (app()->getLocale() == 'ar')
        <link href="{{ asset('dashboard/assets/css/app.min-rtl.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    @else
        <link href="{{ asset('dashboard/assets/css/app.min.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    @endif
    <!-- ================== END core-css ================== -->

    <!-- ================== BEGIN page-css ================== -->
    <link href="{{ asset('dashboard/assets/plugins/jvectormap-next/jquery-jvectormap.css') }}" rel="stylesheet">
    <!-- ================== END page-css ================== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- DataTables -->
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link
        href="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css') }}" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        .invalid-feedback,
        .text-danger {
            color: red;
        }

        .error {
            border: 1px solid red;
        }

        .error-message {
            color: red;
            font-size: 13px;
            margin-top: 5px;
        }

        .menu-submenu .menu-submenu {
            padding-inline-start: 0 !important;
        }

        .menu-submenu .menu-submenu .menu-item {
            padding-inline-start: 15px !important;
        }

        .app-header {
            background: #3675c2;
        }

        .app-header .brand .brand-logo img {
            height: 65px !important;
            filter: brightness(0) invert(1);
        }

        .app-header .menu-toggler .bar {
            background: #fff !important;
        }

        .app-header .menu .menu-item .menu-link .menu-icon,
        .app-header .menu .menu-item .menu-link .menu-text {
            color: #fff;
        }

        th {
            text-align: center !important;
        }
    </style>
    <!-- ================== END core-css ================== -->

    <!-- ── HUD Global Theme ───────────────────────────────────── -->
    <style>
        /* ── tokens ── */
        :root {
            --hud-accent:     #3675c2;
            --hud-accent-rgb: 54, 117, 194;
            --hud-border:     rgba(0, 0, 0, .11);
        }

        /* ── sharp corners everywhere ── */
        .card, .card-header, .card-footer,
        .modal-content, .modal-header, .modal-footer,
        .dropdown-menu, .badge, .alert,
        .form-control, .form-select,
        .input-group > *, .input-group-text,
        .nav-tabs .nav-link,
        .pagination .page-link,
        .progress, .progress-bar,
        .list-group-item {
            border-radius: 0 !important;
        }
        .btn:not(.btn-pill):not(.btn-rounded):not([class*="btn-icon"]) {
            border-radius: 0 !important;
        }

        /* ── cards: flat, thin border, no shadow ── */
        .card {
            box-shadow: none !important;
            border: 1px solid var(--hud-border) !important;
            background: #fff;
            position: relative;
        }
        .card-header {
            background: rgba(0, 0, 0, .025) !important;
            border-bottom: 1px solid var(--hud-border) !important;
        }

        /* ── corner bracket arrows: full definition + brand-blue color ── */
        .card-arrow {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }
        .card-arrow > div {
            width: 10px;
            height: 10px;
            position: absolute;
        }
        .card-arrow > div::before {
            content: '';
            position: absolute;
            width: 2px;
            height: 9px;
            background: rgba(var(--hud-accent-rgb), .5);
        }
        .card-arrow > div::after {
            content: '';
            position: absolute;
            width: 9px;
            height: 2px;
            background: rgba(var(--hud-accent-rgb), .5);
        }
        .card-arrow-top-left           { top: 0;    left: 0;  }
        .card-arrow-top-left::before   { top: 2px;  left: 0;  }
        .card-arrow-top-left::after    { top: 0;    left: 0;  }
        .card-arrow-top-right          { top: 0;    right: 0; }
        .card-arrow-top-right::before  { top: 2px;  right: 0; }
        .card-arrow-top-right::after   { top: 0;    right: 0; }
        .card-arrow-bottom-left        { bottom: 0; left: 0;  }
        .card-arrow-bottom-left::before  { bottom: 2px; left: 0; }
        .card-arrow-bottom-left::after   { bottom: 0;   left: 0; }
        .card-arrow-bottom-right       { bottom: 0; right: 0; }
        .card-arrow-bottom-right::before { bottom: 2px; right: 0; }
        .card-arrow-bottom-right::after  { bottom: 0;   right: 0; }

        /* card content stays above the arrow overlay */
        .card > *:not(.card-arrow) { position: relative; z-index: 1; }

        /* ── section-head utility (usable on any page) ── */
        .hud-section-head {
            display: flex;
            align-items: center;
            gap: .65rem;
            margin: .25rem 0 1.1rem;
        }
        .hud-section-head .ico {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(var(--hud-accent-rgb), .1);
            border: 1px solid var(--hud-accent);
            color: var(--hud-accent);
            font-size: 15px;
            flex-shrink: 0;
        }
        .hud-section-head h5 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .25px;
        }
        .hud-section-head .hud-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(0,0,0,.12), transparent);
        }
        .hud-section-head small { font-size: 11.5px; color: rgba(0,0,0,.45); }

        /* ── HUD flat stat card (used via hud-stat-card component) ── */
        .hud-card {
            position: relative;
            background: #fff;
            border: 1px solid var(--hud-border) !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        .hud-card-body { padding: 1rem 1.1rem; position: relative; z-index: 1; }
        .hud-card-label { font-size: 12.5px; font-weight: 600; color: rgba(0,0,0,.45); margin-bottom: .55rem; }
        .hud-stat-value { font-size: 1.55rem; font-weight: 800; color: #1a1a2e; line-height: 1.1; }
        .hud-stat-value .unit { font-size: .85rem; font-weight: 600; opacity: .5; }
        .hud-icon-box {
            width: 30px; height: 30px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(var(--hud-accent-rgb), .08);
            border: 1px solid rgba(var(--hud-accent-rgb), .22);
            color: var(--hud-accent);
            font-size: 15px; flex-shrink: 0;
        }
        .hud-card-arrow { position: absolute; inset: 0; pointer-events: none; }
        .hud-arrow-tl, .hud-arrow-tr, .hud-arrow-bl, .hud-arrow-br { position: absolute; width: 10px; height: 10px; }
        .hud-arrow-tl::before, .hud-arrow-tr::before, .hud-arrow-bl::before, .hud-arrow-br::before { content:''; position:absolute; width:2px; height:9px; background:rgba(var(--hud-accent-rgb),.55); }
        .hud-arrow-tl::after,  .hud-arrow-tr::after,  .hud-arrow-bl::after,  .hud-arrow-br::after  { content:''; position:absolute; width:9px; height:2px; background:rgba(var(--hud-accent-rgb),.55); }
        .hud-arrow-tl { top:0; left:0; }   .hud-arrow-tl::before { top:2px; left:0; }   .hud-arrow-tl::after { top:0; left:0; }
        .hud-arrow-tr { top:0; right:0; }  .hud-arrow-tr::before { top:2px; right:0; }  .hud-arrow-tr::after { top:0; right:0; }
        .hud-arrow-bl { bottom:0; left:0; } .hud-arrow-bl::before { bottom:2px; left:0; } .hud-arrow-bl::after { bottom:0; left:0; }
        .hud-arrow-br { bottom:0; right:0; } .hud-arrow-br::before { bottom:2px; right:0; } .hud-arrow-br::after { bottom:0; right:0; }
    </style>
    <!-- ── end HUD Global Theme ─────────────────────────────── -->

    @yield('css')

    <style>
        #datatableDefault_filter,
        .dataTables_filter {
            justify-self: end !important;
        }

        table.table.dataTable {
            max-width: 100% !important;
        }

        #datatableDefault_paginate,
        .pagination,
        .dataTables_paginate {
            margin-top: 15px;
            justify-self: end !important;
        }

        .dataTables_info,
        #datatableDefault_info {
            margin-top: 15px;
        }

        div.dt-container div.dt-search input {
            margin-left: 0 !important;
        }

        .small-text th,
        .small-text td {
            font-size: 12px;
            /* {{ __('owner.generated.or') }} 13px {{ __('owner.generated.item_4cc9e8') }} */
            text-align: center !important;
            vertical-align: middle;
            font-weight: bold;
        }

        .small-text th {
            font-weight: bold !important;
            color: #000 !important;
        }

        .btn-outline-danger,
        .btn-outline-success,
        .btn-outline-primary,
        .btn-outline-info,
        .btn-outline-warning,
        .btn-outline-secondary {
            border-radius: 0px !important;
        }

        .btn-border-radius {
            border-radius: 5px !important;
        }

        /* ── Keep wide tables inside their own horizontal scroll area at every
              screen size, instead of stretching/clipping the page ── */
        .table-responsive,
        .dataTables_wrapper,
        div.dt-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        /* DataTables sometimes constrains its scroll body; let the wrapper own it */
        .dataTables_wrapper .dataTables_scroll,
        .dt-container .dt-scroll {
            overflow-x: auto;
        }
    </style>

    {{-- ── Global UI overrides: HUD card corner-brackets + slimmer sidebar ── --}}
    <style>
        /* Client HUD card design: square card, thin border, L-brackets on ALL four corners */
        .card,
        .hud-card {
            border-radius: 0 !important;
        }
        /* top stat cards: month-status style — rounded, subtle single-accent fill */
        .card.hud-stat-card:not(.border-0) {
            border-radius: .65rem !important;
            background: rgba(var(--hud-accent-rgb), .07) !important;
            border: 1px solid rgba(var(--hud-accent-rgb), .28) !important;
        }
        /* drop the corner brackets on stat cards for a cleaner, focused look */
        .card.hud-stat-card:after {
            display: none !important;
        }
        .hud-stat-card .hud-sc-label {
            color: var(--hud-accent) !important;
        }
        .card:not(.border-0) {
            border: 1px solid var(--hud-border, rgba(0, 0, 0, .11)) !important;
        }
        /* hide the theme's default inset frame + manual card-arrow (avoid doubling) */
        .card:before,
        .card .card-arrow {
            display: none !important;
        }
        /* draw four corner brackets via the card's ::after layer */
        .card:after {
            content: "" !important;
            position: absolute !important;
            inset: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            border: 0 !important;
            pointer-events: none !important;
            z-index: 20 !important;
            --brk-color: rgba(0, 0, 0, .6);
            --brk-len: 14px;
            --brk-th: 2px;
            background:
                linear-gradient(var(--brk-color), var(--brk-color)) top left / var(--brk-len) var(--brk-th) no-repeat,
                linear-gradient(var(--brk-color), var(--brk-color)) top left / var(--brk-th) var(--brk-len) no-repeat,
                linear-gradient(var(--brk-color), var(--brk-color)) top right / var(--brk-len) var(--brk-th) no-repeat,
                linear-gradient(var(--brk-color), var(--brk-color)) top right / var(--brk-th) var(--brk-len) no-repeat,
                linear-gradient(var(--brk-color), var(--brk-color)) bottom left / var(--brk-len) var(--brk-th) no-repeat,
                linear-gradient(var(--brk-color), var(--brk-color)) bottom left / var(--brk-th) var(--brk-len) no-repeat,
                linear-gradient(var(--brk-color), var(--brk-color)) bottom right / var(--brk-len) var(--brk-th) no-repeat,
                linear-gradient(var(--brk-color), var(--brk-color)) bottom right / var(--brk-th) var(--brk-len) no-repeat !important;
        }

        /* Slimmer sidebar */
        :root {
            --app-sidebar-w: 12.5rem;
        }
        .app-sidebar {
            width: var(--app-sidebar-w) !important;
        }
        /* Only offset content for the sidebar on desktop. On mobile (<768px) the
           sidebar is off-canvas, so the theme's own margin reset must win.
           Logical properties keep this correct in both RTL and LTR:
             - inline-start  = the side the sidebar sits on (offset for sidebar)
             - inline-end    = the opposite side (extra gap to narrow the content) */
        @media (min-width: 768px) {
            .app-content,
            .app-sidebar-toggled .app-content {
                margin-inline-start: var(--app-sidebar-w);
                margin-inline-end: var(--app-sidebar-w);
            }
            .app-footer,
            .app-sidebar-toggled .app-footer {
                margin-inline-start: calc(var(--app-sidebar-w) + 2rem);
                margin-inline-end: calc(var(--app-sidebar-w) + 2rem);
            }
        }
    </style>

    {{-- ── Dark-mode corrections ──────────────────────────────────────────
         Light mode is left untouched. These rules only re-map the hardcoded
         light colors (in the styles above + reused page utilities) that would
         otherwise override the theme's native [data-bs-theme=dark] palette. --}}
    <style>
        /* themed border token used by the HUD cards */
        [data-bs-theme=dark] { --hud-border: var(--bs-border-color); }

        /* cards / panels that hardcode a white fill (no !important so colored
           utility backgrounds like .bg-warning still win) */
        [data-bs-theme=dark] .card,
        [data-bs-theme=dark] .hud-card { background: var(--bs-secondary-bg); }

        /* corner L-brackets: light strokes on dark */
        [data-bs-theme=dark] .card:after { --brk-color: rgba(255, 255, 255, .5); }

        /* top navbar: the hardcoded bright blue (#3675c2) clashes with the dark
           navy page — drop to a deep, muted ocean-blue that blends in, and add a
           thin accent rule so it still reads as a distinct bar */
        [data-bs-theme=dark] .app-header {
            background: #14304f;
            border-bottom: 1px solid rgba(var(--hud-accent-rgb), .4);
        }

        /* HUD typography that hardcodes near-black / translucent-black */
        [data-bs-theme=dark] .hud-stat-value,
        [data-bs-theme=dark] .hud-sc-value,
        [data-bs-theme=dark] .hud-sc-grid-value { color: var(--bs-emphasis-color); }
        [data-bs-theme=dark] .hud-card-label,
        [data-bs-theme=dark] .hud-section-head small,
        [data-bs-theme=dark] .hud-sc-grid-label,
        [data-bs-theme=dark] .hud-sc-footer { color: var(--bs-secondary-color); }
        [data-bs-theme=dark] .hud-section-head .hud-line {
            background: linear-gradient(90deg, rgba(var(--bs-emphasis-color-rgb), .18), transparent);
        }
        [data-bs-theme=dark] .small-text th { color: var(--bs-emphasis-color) !important; }

        /* Bootstrap utility classes that pin light colors on page content */
        [data-bs-theme=dark] .bg-white { background-color: var(--bs-secondary-bg) !important; }
        [data-bs-theme=dark] .bg-light { background-color: var(--bs-tertiary-bg) !important; }
        [data-bs-theme=dark] .text-dark,
        [data-bs-theme=dark] .text-black { color: var(--bs-body-color) !important; }

        /* …but keep dark text legible where it sits on a light-colored badge/box */
        [data-bs-theme=dark] .bg-warning.text-dark,
        [data-bs-theme=dark] .bg-warning .text-dark,
        [data-bs-theme=dark] .bg-info.text-dark,
        [data-bs-theme=dark] .bg-info .text-dark { color: #000 !important; }

        /* native <select> popup: browsers render <option> on the OS default
           (white) unless explicitly themed — match the dark form palette */
        [data-bs-theme=dark] .form-select option,
        [data-bs-theme=dark] select option,
        [data-bs-theme=dark] .form-control option,
        [data-bs-theme=dark] .form-select optgroup,
        [data-bs-theme=dark] select optgroup {
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
        }
    </style>

    {{-- ── Glassy panels: make every card (and the tables inside them) translucent
         so the page background shows through, exactly like the top KPI stat cards.
         The accent-tint fill is the same one used by .hud-stat-card above. ── --}}
    <style>
        /* all cards adopt the translucent fill of the top stat cards */
        .card {
            background: rgba(var(--hud-accent-rgb), .07) !important;
        }
        .card-header {
            background: rgba(var(--hud-accent-rgb), .12) !important;
        }
        /* tables must not paint an opaque fill over the now-translucent card */
        .card .table,
        .card table {
            --bs-table-bg: transparent;
            background-color: transparent !important;
        }
    </style>

    {{-- Make ApexCharts (dashboard, analytics, sales, expenses, …) follow the theme --}}
    <script>
        window.Apex = window.Apex || {};
        window.Apex.theme = Object.assign({}, window.Apex.theme || {}, { mode: '{{ $ownerTheme }}' });
        window.Apex.chart = Object.assign({}, window.Apex.chart || {}, { background: 'transparent' });
        window.Apex.tooltip = Object.assign({}, window.Apex.tooltip || {}, { theme: '{{ $ownerTheme }}' });
    </script>
</head>

<body>
    <!-- BEGIN #app -->
    <div id="app" class="app app-footer-fixed">
        <!-- BEGIN #header -->
        @include('owner.partial.header')
        <!-- END #header -->

        <!-- BEGIN #sidebar -->
        @include('owner.partial.sidebar')
        <!-- END #sidebar -->

        <!-- BEGIN mobile-sidebar-backdrop -->
        <button class="app-sidebar-mobile-backdrop" data-toggle-target=".app"
            data-toggle-class="app-sidebar-mobile-toggled"></button>
        <!-- END mobile-sidebar-backdrop -->

        <!-- BEGIN #content -->
        <div id="content" class="app-content">
            @include('owner.alert.alert')

            @yield('content')

        </div>
        @include('owner.partial.footer')
        <!-- END #content -->

        <!-- BEGIN btn-scroll-top -->
        <a href="#" data-toggle="scroll-to-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>
        <!-- END btn-scroll-top -->
    </div>

    <!-- END #app -->

    <!-- ================== BEGIN core-js ================== -->
    <script src="{{ asset('dashboard/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/app.min.js') }}"></script>
    <!-- ================== END core-js ================== -->

    <!-- DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js') }}"></script>

    <script src="{{ asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}">
    </script>

    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- DataTables -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <!-- ================== BEGIN page-js ================== -->
    <script src="{{ asset('dashboard/assets/js/notify.js') }}"></script>

    <!-- Global DataTables defaults (language) -->
    <script>
        (function() {
            // var dtLang = {};
            // @if (app()->getLocale() == 'ar')
            //     dtLang = { 
            //         url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
            //     };
            // @endif

            function applyDtDefaults() {
                if (window.jQuery && $.fn.dataTable) {
                    $.extend(true, $.fn.dataTable.defaults, {
                        // language: dtLang,
                        language: {
                            @if (app()->getLocale() == 'ar')
                                url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                            @endif
                        },
                    });
                }
            }

            // If jQuery/DataTables already loaded, apply immediately, otherwise wait for DOM
            if (window.jQuery && $.fn.dataTable) {
                applyDtDefaults();
            } else {
                document.addEventListener('DOMContentLoaded', applyDtDefaults);
            }
        })();
    </script>

    @yield('script')
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.getElementsByClassName('success-alert');
                if (alerts.length > 0) {
                    alerts[0].style.display = 'none';
                }
            }, 3000);
        });
    </script>
    @if (session()->has('success'))
        <script>
            $(function() {
                $.notify("{{ session('success') }}", "success");
            });
        </script>
    @endif

    <script>
        /* Auto-wrap any table not already inside a scroll container so it scrolls
           horizontally on small screens instead of overflowing the page. Runs on
           'load' so DataTables (initialised on DOM-ready) are already wrapped. */
        window.addEventListener('load', function() {
            document.querySelectorAll('#content table').forEach(function(table) {
                if (table.closest('.table-responsive, .dataTables_wrapper, .dt-container, .dataTables_scroll')) {
                    return;
                }
                var wrapper = document.createElement('div');
                wrapper.className = 'table-responsive';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            });
        });
    </script>

    <script>
        /* Auto-inject HUD corner brackets into every .card that doesn't have them */
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.card').forEach(function(card) {
                if (card.querySelector('.card-arrow')) { return; }
                var arrow = document.createElement('div');
                arrow.className = 'card-arrow';
                arrow.innerHTML =
                    '<div class="card-arrow-top-left"></div>' +
                    '<div class="card-arrow-top-right"></div>' +
                    '<div class="card-arrow-bottom-left"></div>' +
                    '<div class="card-arrow-bottom-right"></div>';
                card.appendChild(arrow);
            });
        });
    </script>

</body>

</html>

@include('partials.support-floating-button')
