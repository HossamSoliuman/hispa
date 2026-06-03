<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" data-bs-theme="light">

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
    @if(app()->getLocale() == 'ar')
    <link href="{{asset('dashboard/assets/css/app.min-rtl.css')}}" rel="stylesheet">
    @else
    <link href="{{asset('dashboard/assets/css/app.min.css')}}" rel="stylesheet">
    @endif
    <!-- ================== END core-css ================== -->

    <!-- ================== BEGIN page-css ================== -->
    <link href="{{asset('dashboard/assets/plugins/lity/dist/lity.min.css')}}" rel="stylesheet">
    <!-- ================== END page-css ================== -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

</head>

<body>
    <!-- BEGIN #app -->
    <div id="app" class="app">
        <!-- BEGIN #header -->
        @include('landing-page.partial.header')
        <!-- END #header -->

        <!-- BEGIN #home -->
        <div id="home" class="py-5 position-relative bg-body bg-opacity-50" data-bs-theme="light">
            <!-- BEGIN container -->
            <div class="container-xl p-3 p-lg-5 mb-0">

                <!-- BEGIN div-hero-content -->
                <div class="div-hero-content z-3 position-relative">
                    <!-- BEGIN row -->
                    <div class="row align-items-center">

                        <div class="col-lg-8">
                            <h1>{{$page->title}}</h1>
                            {!! $page->body !!}
                        </div>

                        <div class="col-lg-4"></div>
                    </div>
                    <!-- END row -->
                </div>
                <!-- END div-hero-content -->

                <div class="position-absolute top-0 bottom-0 end-0 w-50 p-5 z-2 overflow-hidden d-lg-flex align-items-center d-none">
                    <img class="w-100 d-block" alt="حسبة Project" src="{{asset('dashboard/assets/img/ai/cover-page.png')}}">
                </div>
            </div>
            <!-- END container -->
            <div class="position-absolute bg-size-cover bg-position-center d-none2 bg-no-repeat top-0 start-0 w-100 h-100" style="background-image: url('{{asset('dashboard/assets/img/landing/cover.jpg')}}');"></div>
            <div class="position-absolute top-0 start-0 d-none2 w-100 h-100 opacity-95" style="background: var(--bs-body-bg-gradient);"></div>
            <div class="position-absolute top-0 start-0 d-none2 w-100 h-100 opacity-95" style="background-image: url('{{asset('dashboard/assets/css/images/pattern-light.png')}}'); background-size: var(--bs-body-bg-image-size);"></div>
        </div>

        <!-- BEGIN #contact -->
        @include('landing-page.partial.contact')
        <!-- END #contact -->
        <!-- BEGIN #footer -->
        @include('landing-page.partial.footer')
        <!-- END #footer -->


    </div>
    <!-- END #app -->

    <!-- ================== BEGIN core-js ================== -->
    <script src="{{asset('dashboard/assets/js/vendor.min.js')}}"></script>
    @if(app()->getLocale() == 'ar')
    <script src="{{asset('dashboard/assets/js/app.min.js')}}"></script>
    @else
    <script src="{{asset('dashboard/assets/js/app.min-rtl.js')}}"></script>
    @endif
    <!-- ================== END core-js ================== -->

    <!-- ================== BEGIN page-js ================== -->
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <script src="{{asset('dashboard/assets/plugins/lity/dist/lity.min.js')}}"></script>
    <!-- ================== END page-js ================== -->
    <script>
        $(document).ready(function() {
            $('form[name="form_contact_us"]').on('submit', function(e) {
                e.preventDefault();

                let formData = {
                    first_name: $(this).find('input[name="first_name"]').val(),
                    last_name: $(this).find('input[name="last_name"]').val(),
                    email: $(this).find('input[type="email"]').val(),
                    phone: $(this).find('input[type="tel"]').val(),
                    subject: $(this).find('input[name="subject"]').val(),
                    message: $(this).find('textarea').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    type: 'POST',
                    url: '{{ route("frontend-contact.store") }}', // غيّر المسار حسب ما يلزمك
                    data: formData,
                    success: function(response) {
                        alert('تم إرسال الرسالة بنجاح');
                        $('form[name="form_contact_us"]')[0].reset();
                    },
                    error: function(xhr) {
                        alert('حدث خطأ أثناء الإرسال');
                    }
                });
            });
        });
    </script>


</body>

</html>
