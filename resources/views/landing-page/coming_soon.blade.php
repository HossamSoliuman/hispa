<!DOCTYPE html>
<html lang="ar" dir="rtl" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>{{ $settings['title'] ?? 'حسبة' }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="حسبة - الحل الأمثل لإدارة مشاريعك ومتابعة أعصيّاد بسهولة" />
        <meta name="author" content="حسبة" />
        <meta name="keywords" content="حسبة, حسبة, إدارة مشاريع, تطبيق ويب" />
        <link rel="icon" href="{{ asset('storage/uploads/favicon.ico') }}" type="image/x-icon" />
    </head>


    <!-- ================== BEGIN core-css ================== -->
    <link href="{{asset('dashboard/assets/css/vendor.min.css')}}" rel="stylesheet">
    <link href="{{asset('dashboard/assets/css/app.min-rtl.css')}}" rel="stylesheet">
    <!-- ================== END core-css ================== -->

    <!-- ================== BEGIN page-css ================== -->
    <link href="{{asset('dashboard/assets/plugins/lity/dist/lity.min.css')}}" rel="stylesheet">
    <link href="{{asset('dashboard/assets/plugins/kbw-countdown/dist/css/jquery.countdown.css')}}" rel="stylesheet">

    <!-- ================== END page-css ================== -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

</head>
<body class='pace-top'>
<!-- BEGIN #app -->
<div id="app" class="app app-full-height app-without-header p-0">
    <!-- BEGIN coming-soon -->
    <div class="coming-soon">
        <div class="flex-1">
            <div class="coming-soon-timer">
                <div id="timer"></div>
            </div>
            <!-- BEGIN coming-soon-content -->
            <div class="coming-soon-content d-flex flex-column">
                <div class="flex-1 mb-3">
                    <h2 class="mb-3">We're coming soon!</h2>
                    <p class="mb-4">We are working very hard on the new version of our site.<br> It will bring a lot of new features. Stay tuned!</p>

                </div>
                <div class="text-center small text-inverse text-opacity-50" dir="rtl">
                    جميع الحقوق محفوظة لدى {{ $settings['title'] }} &copy; {{ date('Y') }}
                </div>


            </div>
            <!-- END coming-soon-content -->
        </div>
    </div>
    <!-- END coming-soon -->


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
<script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
<script src="{{asset('dashboard/assets/plugins/lity/dist/lity.min.js')}}"></script>
<!-- ================== BEGIN page-js ================== -->
<script src="{{asset('dashboard/assets/plugins/kbw-countdown/dist/js/jquery.plugin.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/kbw-countdown/dist/js/jquery.countdown.js')}}"></script>
<script src="{{asset('dashboard/assets/js/demo/page-coming-soon.demo.js')}}"></script>
<!-- ================== END page-js ================== -->

</body>
</html>

