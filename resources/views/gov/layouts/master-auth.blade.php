<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="{{ __('gov.meta.description') }}" />
    <link rel="icon" href="{{ asset('storage/uploads/favicon.ico') }}" type="image/x-icon" />

    <link href="{{ asset('dashboard/assets/css/vendor.min.css') }}" rel="stylesheet">
    @if (app()->getLocale() == 'ar')
        <link href="{{ asset('dashboard/assets/css/app.min-rtl.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    @else
        <link href="{{ asset('dashboard/assets/css/app.min.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    @endif
    @yield('css')
</head>
<body class='pace-top'>
<div id="app" class="app app-full-height app-without-header">
    @yield('content')
</div>
<script src="{{ asset('dashboard/assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/app.min.js') }}"></script>
@yield('script')
</body>
</html>
