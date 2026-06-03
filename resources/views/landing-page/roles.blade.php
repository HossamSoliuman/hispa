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
    <link href="{{ asset('dashboard/assets/css/vendor.min.css') }}" rel="stylesheet">
    @if (app()->getLocale() == 'ar')
        <link href="{{ asset('dashboard/assets/css/app.min-rtl.css') }}" rel="stylesheet">
    @else
        <link href="{{ asset('dashboard/assets/css/app.min.css') }}" rel="stylesheet">
    @endif
    <!-- ================== END core-css ================== -->

    <!-- ================== BEGIN page-css ================== -->
    <link href="{{ asset('dashboard/assets/plugins/lity/dist/lity.min.css') }}" rel="stylesheet">
    <!-- ================== END page-css ================== -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

</head>

<body>
    <!-- BEGIN #app -->
    <div id="app" class="app">

        <section id="roles"
            class="overflow-hidden d-flex flex-column justify-content-center align-items-center text-center py-4">

            <div class="container-xl text-center">
                <h2 class="fw-bold mb-2 text-dark">{{ __('landing-page.roles.title') }}</h2>
                <p class="text-body text-opacity-50 mt-2 w-75 mx-auto fs-5">
                    {{ __('landing-page.roles.description') }}
                </p>

                <div class="mx-auto d-flex justify-content-center align-items-center"
                    style="width: 160px; height: 100px;">
                    <img src="{{ asset('dashboard/assets/img/logo/logo.png') }}" alt="حسبة" class="img-fluid w-100"
                        style="height: auto; object-fit: contain;">
                </div>

                <div class="row justify-content-center g-4">
                    {{-- الصياد --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100 border border-primary  rounded-4 shadow-sm p-4">
                            <div class="card-body d-flex flex-column align-items-center">
                                <img src="{{ asset('dashboard/assets/img/landing/roles/big-wave.png') }}"
                                    alt="الصياد" width="55" class="mb-3">
                                <h5 class="fw-bold text-dark mb-2">{{ __('landing-page.roles.fisherman') }}</h5>
                                <p class="text-muted small mb-4 lh-base">{{ __('landing-page.roles.fisherman_desc') }}
                                </p>
                                <a href="{{ route('frontend.login') }}"
                                    class="text-primary fw-semibold small text-decoration-underline position-absolute bottom-0 m-3">{{ __('landing-page.roles.login') }}</a>
                            </div>
                        </div>
                    </div>

                    {{-- العداد --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100 border border-primary  rounded-4 shadow-sm p-4">
                            <div class="card-body d-flex flex-column align-items-center">
                                <img src="{{ asset('dashboard/assets/img/landing/roles/rudder.png') }}" alt="العداد"
                                    width="55" class="mb-3">
                                <h5 class="fw-bold text-dark mb-2">{{ __('landing-page.roles.counter') }}</h5>
                                <p class="text-muted small mb-4 lh-base">{{ __('landing-page.roles.counter_desc') }}
                                </p>
                                <a href="{{ route('frontend.login') }}"
                                    class="text-primary fw-semibold small text-decoration-underline position-absolute bottom-0 m-3">{{ __('landing-page.roles.login') }}</a>
                            </div>
                        </div>
                    </div>

                    {{-- الدلال --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100 border border-primary  rounded-4 shadow-sm p-4">
                            <div class="card-body d-flex flex-column align-items-center">
                                <img src="{{ asset('dashboard/assets/img/landing/roles/brokerage.png') }}"
                                    alt="الدلال" width="55" class="mb-3">
                                <h5 class="fw-bold text-dark mb-2">{{ __('landing-page.roles.broker') }}</h5>
                                <p class="text-muted small mb-4 lh-base">{{ __('landing-page.roles.broker_desc') }}</p>
                                <a href="{{ route('frontend.login') }}"
                                    class="text-primary fw-semibold small text-decoration-underline position-absolute bottom-0 m-3">{{ __('landing-page.roles.login') }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('frontend.register') }}"
                        class="btn btn-primary">{{ __('landing-page.roles.register_new') }}</a>
                </div>
            </div>
        </section>

    </div>
    <!-- END #app -->

    <!-- Support Floating Button -->
    @include('partials.support-floating-button')

    <!-- ================== BEGIN core-js ================== -->
    <script src="{{ asset('dashboard/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/app.min.js') }}"></script>
    <!-- ================== END core-js ================== -->

    <!-- ================== BEGIN page-js ================== -->
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <script src="{{ asset('dashboard/assets/plugins/lity/dist/lity.min.js') }}"></script>
    <!-- ================== END page-js ================== -->

</body>

</html>
