<!DOCTYPE html dir="rtl" >
<html lang="ar" dir="rtl" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <title>منصة الحسبة | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="حسبة - الحل الأمثل لإدارة مشاريعك ومتابعة أعصيّاد بسهولة" />
    <meta name="author" content="حسبة" />
    <meta name="keywords" content="حسبة, حسبة, إدارة مشاريع, تطبيق ويب" />
    <link rel="icon" href="{{ asset('storage/uploads/favicon.ico') }}" type="image/x-icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ================== BEGIN core-css ================== -->
    <link href="{{asset('dashboard/assets/css/vendor.min.css')}}" rel="stylesheet">
    <link href="{{asset('dashboard/assets/css/app.min-rtl.css')}}" rel="stylesheet">
    <!-- ================== END core-css ================== -->

    <!-- ================== BEGIN page-css ================== -->
    <link href="{{asset('dashboard/assets/plugins/jvectormap-next/jquery-jvectormap.css')}}" rel="stylesheet">
    <!-- ================== END page-css ================== -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        .invalid-feedback,.text-danger{
            color: red;
        }
    </style>
    <style>
        .error {
            border: 1px solid red;
        }
        .error-message {
            color: red;
            font-size: 13px;
            margin-top: 5px;
        }
    </style>


    <!-- ================== END core-css ================== -->
    @yield('css')
</head>
<body>
<!-- BEGIN #app -->
<div id="app" class="app app-footer-fixed">
    <!-- BEGIN #header -->
    @include('frontend.partial.header')
    <!-- END #header -->

    <!-- BEGIN #sidebar -->
    @include('frontend.partial.sidebar')
    <!-- END #sidebar -->

    <!-- BEGIN mobile-sidebar-backdrop -->
    <button class="app-sidebar-mobile-backdrop" data-toggle-target=".app" data-toggle-class="app-sidebar-mobile-toggled"></button>
    <!-- END mobile-sidebar-backdrop -->

    <!-- BEGIN #content -->
    <div id="content" class="app-content">
        @include('frontend.alert.alert')

        @yield('content')

    </div>
    @include('frontend.partial.footer')
    <!-- END #content -->

    <!-- BEGIN btn-scroll-top -->
    <a href="#" data-toggle="scroll-to-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>
    <!-- END btn-scroll-top -->
</div>

<!-- END #app -->

<!-- ================== BEGIN core-js ================== -->
<script src="{{asset('dashboard/assets/js/vendor.min.js')}}"></script>
<script src="{{asset('dashboard/assets/js/app.min.js')}}"></script>
<!-- ================== END core-js ================== -->

<!-- ================== BEGIN page-js ================== -->
{{--<script src="{{asset('dashboard/assets/plugins/jvectormap-next/jquery-jvectormap.min.js')}}"></script>--}}
{{--<script src="{{asset('dashboard/assets/plugins/jvectormap-content/world-mill.js')}}"></script>--}}
{{--<script src="{{asset('dashboard/assets/plugins/apexcharts/dist/apexcharts.min.js')}}"></script>--}}
{{--<script src="{{asset('dashboard/assets/js/demo/dashboard.demo.js')}}"></script>--}}
<script src="{{asset('dashboard/assets/js/notify.js')}}"></script>

<!-- ================== END page-js ================== -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@include('partials.support-floating-button')

@yield('script')
<script>
    window.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
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

</body>
</html>
