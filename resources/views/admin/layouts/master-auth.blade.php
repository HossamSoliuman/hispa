<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="{{ __('admin.generated.meta_desc_hasbah') }}" />
    <meta name="author" content="{{ __('admin.generated.meta_author') }}" />
    <meta name="keywords" content="{{ __('admin.generated.meta_keywords') }}" />
    <link rel="icon" href="{{ asset('storage/uploads/favicon.ico') }}" type="image/x-icon" />

    <!-- ================== BEGIN core-css ================== -->
    <link href="{{asset('dashboard/assets/css/vendor.min.css')}}" rel="stylesheet">
    @if (app()->getLocale() == 'ar')
        <link href="{{asset('dashboard/assets/css/app.min-rtl.css')}}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    @else
        <link href="{{asset('dashboard/assets/css/app.min.css')}}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    @endif

    <!-- ================== END core-css ================== -->
@yield('css')
</head>
<body class='pace-top'>
<!-- BEGIN #app -->
<div id="app" class="app app-full-height app-without-header">
    @yield('content')
</div>
<!-- END #app -->
<!-- ================== BEGIN core-js ================== -->
<script src="{{asset('dashboard/assets/js/vendor.min.js')}}"></script>
<script src="{{asset('dashboard/assets/js/app.min.js')}}"></script>
<!-- ================== END core-js ================== -->
@yield('script')
</body>
</html>
