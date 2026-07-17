@php $govTheme = request()->cookie('gov_theme') === 'light' ? 'light' : 'dark'; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" data-bs-theme="{{ $govTheme }}">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="{{ __('gov.meta.description') }}" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />

    <link href="{{ asset('dashboard/assets/css/vendor.min.css') }}" rel="stylesheet">
    @if (app()->getLocale() == 'ar')
        <link href="{{ asset('dashboard/assets/css/app.min-rtl.css') }}" rel="stylesheet">
    @else
        <link href="{{ asset('dashboard/assets/css/app.min.css') }}" rel="stylesheet">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @yield('css')
</head>
<body class='pace-top'>
<div id="app" class="app app-full-height app-without-header">
    @yield('content')
</div>
<script src="{{ asset('dashboard/assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/app.min.js') }}"></script>
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
