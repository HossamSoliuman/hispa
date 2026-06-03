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

    @yield('content')

    <!-- BEGIN #pricing -->
{{--    <div id="pricing" class="py-5 text-body text-opacity-75">--}}
{{--        <div class="container-xxl p-3 p-lg-5">--}}
{{--            <h1 class="mb-3 text-center">Our Pricing Plans</h1>--}}
{{--            <p class="fs-16px text-body text-opacity-50 text-center mb-0">Choose the perfect plan that suits your needs. <br>Our pricing is designed to be flexible and affordable, providing value for businesses of all sizes. <br>Explore our plans to find the best fit for your requirements.</p>--}}

{{--            <div class="row g-3 py-3 gx-lg-5 pt-lg-5">--}}
{{--                <div class="col-xl-3 col-md-4 col-sm-6 py-xl-5">--}}
{{--                    <div class="card h-100">--}}
{{--                        <div class="card-body p-4 d-flex flex-column">--}}
{{--                            <div class="d-flex align-items-center">--}}
{{--                                <div class="flex-1">--}}
{{--                                    <div class="h6 font-monospace">Starter Plan</div>--}}
{{--                                    <div class="h1 fw-semibold mb-0">$5 <small class="h6 fw-semibold text-body text-opacity-50">/month*</small></div>--}}
{{--                                </div>--}}
{{--                                <div>--}}
{{--                                    <iconify-icon icon="solar:usb-bold-duotone" class="display-6 text-body text-opacity-50"></iconify-icon>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <hr class="my-20px">--}}
{{--                            <div class="mb-5 text-body text-opacity-75 flex-1">--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Storage:</span> <b class="text-body">10 GB</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Bandwidth:</span> <b class="text-body">100 GB</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Domain Names:</span> <b class="text-body">1</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">SSL Certificate:</span> <b class="text-body"> Shared</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Email Accounts:</span> <b class="text-body"> 5</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">24/7 Support:</span> <b class="text-body"> Yes</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Backup:</span> <b class="text-body"> Daily</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Uptime Guarantee:</span> <b class="text-body"> 99.9%</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">FTP Access:</span> <b class="text-body"> Yes</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Control Panel:</span> <b class="text-body"> cPanel</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Free Domain:</span> <b class="text-body"> No</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Firewall:</span> <b class="text-body"> No</b></div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="mx-n2">--}}
{{--                                <a href="#" class="btn btn-outline-default btn-lg w-100 font-monospace">Get Started <i class="fa fa-arrow-right"></i></a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="card-arrow">--}}
{{--                            <div class="card-arrow-top-left"></div>--}}
{{--                            <div class="card-arrow-top-right"></div>--}}
{{--                            <div class="card-arrow-bottom-left"></div>--}}
{{--                            <div class="card-arrow-bottom-right"></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-xl-3 col-md-6 py-3 py-xl-5">--}}
{{--                    <div class="card h-100">--}}
{{--                        <div class="card-body p-4 d-flex flex-column">--}}
{{--                            <div class="d-flex align-items-center">--}}
{{--                                <div class="flex-1">--}}
{{--                                    <div class="h6 font-monospace">Booster Plan</div>--}}
{{--                                    <div class="h1 fw-semibold mb-0">$10 <small class="h6 fw-semibold text-body text-opacity-50">/month*</small></div>--}}
{{--                                </div>--}}
{{--                                <div>--}}
{{--                                    <iconify-icon icon="solar:map-arrow-up-bold-duotone" class="display-6 text-body text-opacity-50"></iconify-icon>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <hr class="my-20px">--}}
{{--                            <div class="mb-5 text-body text-opacity-75 flex-1">--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Storage:</span> <b class="text-body">20 GB</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Bandwidth:</span> <b class="text-body">200 GB</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Domain Names:</span> <b class="text-body">2</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">SSL Certificate:</span> <b class="text-body"> Free</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Email Accounts:</span> <b class="text-body"> 10</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">24/7 Support:</span> <b class="text-body"> Yes</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Backup:</span> <b class="text-body"> Daily</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Uptime Guarantee:</span> <b class="text-body"> 99.9%</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">FTP Access:</span> <b class="text-body"> Yes</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Control Panel:</span> <b class="text-body"> cPanel</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Free Domain:</span> <b class="text-body"> No</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Firewall:</span> <b class="text-body"> No</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-times fa-lg text-body text-opacity-25"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">45-Day Money-Back Guarantee</span></div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="mx-n2">--}}
{{--                                <a href="#" class="btn btn-outline-default btn-lg w-100 font-monospace">Get Started <i class="fa fa-arrow-right"></i></a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="card-arrow">--}}
{{--                            <div class="card-arrow-top-left"></div>--}}
{{--                            <div class="card-arrow-top-right"></div>--}}
{{--                            <div class="card-arrow-bottom-left"></div>--}}
{{--                            <div class="card-arrow-bottom-right"></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-xl-3 col-md-6 py-3 py-xl-0">--}}
{{--                    <div class="card border-theme h-100">--}}
{{--                        <div class="card-body p-30px h-100 d-flex flex-column">--}}
{{--                            <div class="d-flex align-items-center">--}}
{{--                                <div class="flex-1">--}}
{{--                                    <div class="h6 font-monospace text-theme">Premium Plan</div>--}}
{{--                                    <div class="display-6 fw-bold mb-0 text-theme">$15 <small class="h6 text-body text-opacity-50">/month*</small></div>--}}
{{--                                </div>--}}
{{--                                <div>--}}
{{--                                    <iconify-icon icon="solar:cup-first-bold-duotone" class="display-5 text-theme"></iconify-icon>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <hr class="my-20px">--}}
{{--                            <div class="mb-5 text-body flex-1">--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">Storage:</span> <b class="text-body">50 GB</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">Bandwidth:</span> <b class="text-body">500 GB</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">Domain Names:</span> <b class="text-body">Unlimited</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">SSL Certificate:</span> <b class="text-body">Free</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">Email Accounts:</span> <b class="text-body">Unlimited</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">24/7 Support:</span> <b class="text-body">Yes</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">Backup:</span> <b class="text-body">Daily</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">Uptime Guarantee:</span> <b class="text-body">99.9%</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">FTP Access:</span> <b class="text-body">Yes</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">Control Panel:</span> <b class="text-body">cPanel</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">Free Domain:</span> <b class="text-body">No</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">Firewall:</span> <b class="text-body">Yes</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">E-commerce Support</span></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace text-body text-opacity-50 small">45-Day Money-Back Guarantee</span></div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <a href="#" class="btn btn-theme btn-lg w-100 text-black font-monospace">Get Started <i class="fa fa-arrow-right"></i></a>--}}
{{--                        </div>--}}
{{--                        <div class="card-arrow">--}}
{{--                            <div class="card-arrow-top-left"></div>--}}
{{--                            <div class="card-arrow-top-right"></div>--}}
{{--                            <div class="card-arrow-bottom-left"></div>--}}
{{--                            <div class="card-arrow-bottom-right"></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-xl-3 col-md-6 py-3 py-xl-5">--}}
{{--                    <div class="card h-100">--}}
{{--                        <div class="card-body p-30px d-flex flex-column">--}}
{{--                            <div class="d-flex align-items-center">--}}
{{--                                <div class="flex-1">--}}
{{--                                    <div class="h6 font-monospace">Business Plan</div>--}}
{{--                                    <div class="display-6 fw-bold mb-0">$99<small class="h6 text-body text-opacity-50">/month*</small></div>--}}
{{--                                </div>--}}
{{--                                <div>--}}
{{--                                    <iconify-icon icon="solar:buildings-bold-duotone" class="display-6 text-white text-opacity-50"></iconify-icon>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <hr class="my-20px">--}}
{{--                            <div class="mb-5 text-body text-opacity-75 flex-1">--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Storage:</span> <b class="text-body">1 TB</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Bandwidth:</span> <b class="text-body">20 TB</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Domain Names:</span> <b class="text-body">Unlimited</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">SSL Certificate:</span> <b class="text-body">Free</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Email Accounts:</span> <b class="text-body">Unlimited</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check fa-lg text-theme"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">24/7 Support:</span> <b class="text-body">Yes</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Backup:</span> <b class="text-body"> Daily</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Uptime Guarantee:</span> <b class="text-body">99.9%</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">FTP Access:</span> <b class="text-body">Yes</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Control Panel:</span> <b class="text-body">cPanel</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Free Domain:</span> <b class="text-body">Yes</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">Firewall:</span> <b class="text-body">Yes</b></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">E-commerce Support</span></div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center mb-1">--}}
{{--                                    <i class="fa fa-check text-theme fa-lg"></i>--}}
{{--                                    <div class="flex-1 ps-3"><span class="font-monospace small">45-Day Money-Back Guarantee</span></div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="mx-n2">--}}
{{--                                <a href="#" class="btn btn-outline-default btn-lg w-100 font-monospace">Get Started <i class="fa fa-arrow-right"></i></a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="card-arrow">--}}
{{--                            <div class="card-arrow-top-left"></div>--}}
{{--                            <div class="card-arrow-top-right"></div>--}}
{{--                            <div class="card-arrow-bottom-left"></div>--}}
{{--                            <div class="card-arrow-bottom-right"></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <!-- END #pricing -->


    <!-- BEGIN #blog -->
{{--    <div id="blog" class="py-5 bg-component">--}}
{{--        <div class="container-xxl p-3 p-lg-5">--}}
{{--            <div class="text-center mb-5">--}}
{{--                <h1 class="mb-3 text-center">Our Latest Insights</h1>--}}
{{--                <p class="fs-16px text-body text-opacity-50 text-center mb-0">--}}
{{--                    Dive into our blog for the latest trends, tips, and updates <br>--}}
{{--                    on web development, design, and industry best practices. Stay informed and inspired <br>--}}
{{--                    with expert insights and valuable resources.--}}
{{--                </p>--}}
{{--            </div>--}}
{{--            <div class="row g-3 g-xl-4 mb-5">--}}
{{--                <div class="col-xl-3 col-lg-4 col-sm-6">--}}
{{--                    <div class="card d-flex flex-column h-100 mb-5 mb-lg-0">--}}
{{--                        <div class="card-body">--}}
{{--                            <img src="assets/img/landing/blog-1.jpg" alt="" class="object-fit-cover h-200px w-100 d-block">--}}
{{--                        </div>--}}
{{--                        <div class="flex-1 px-3 pb-0">--}}
{{--                            <div class="mb-2">--}}
{{--                                <span class="bg-theme bg-opacity-15 text-theme px-2 py-1 rounded small fw-bold">Web Design</span>--}}
{{--                            </div>--}}

{{--                            <h5>Mastering Responsive Design: A Guide for Beginners</h5>--}}
{{--                            <p>Explore the fundamentals of responsive web design and learn essential tips to create websites that look great on any device.</p>--}}
{{--                        </div>--}}
{{--                        <div class="p-3 pt-0 text-body text-opacity-50">July 15, 2025</div>--}}
{{--                        <div class="card-arrow">--}}
{{--                            <div class="card-arrow-top-left"></div>--}}
{{--                            <div class="card-arrow-top-right"></div>--}}
{{--                            <div class="card-arrow-bottom-left"></div>--}}
{{--                            <div class="card-arrow-bottom-right"></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-xl-3 col-lg-4 col-sm-6">--}}
{{--                    <div class="card d-flex flex-column h-100 mb-5 mb-lg-0">--}}
{{--                        <div class="card-body">--}}
{{--                            <img src="{{asset('dashboard/assets/img/landing/blog-2.jpg')}}" alt="" class="object-fit-cover h-200px w-100 d-block">--}}
{{--                        </div>--}}
{{--                        <div class="flex-1 p-3 pb-0">--}}
{{--                            <div class="mb-2">--}}
{{--                                <span class="bg-theme bg-opacity-15 text-theme px-2 py-1 rounded small fw-bold">UXUI Design</span>--}}
{{--                            </div>--}}
{{--                            <h5>The Future of UI/UX Trends in 2025</h5>--}}
{{--                            <p>Discover the latest trends shaping user interface and experience design in the digital landscape this year.</p>--}}
{{--                        </div>--}}
{{--                        <div class="p-3 pt-0 text-body text-opacity-50">July 11, 2025</div>--}}
{{--                        <div class="card-arrow">--}}
{{--                            <div class="card-arrow-top-left"></div>--}}
{{--                            <div class="card-arrow-top-right"></div>--}}
{{--                            <div class="card-arrow-bottom-left"></div>--}}
{{--                            <div class="card-arrow-bottom-right"></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-xl-3 col-lg-4 col-sm-6">--}}
{{--                    <div class="card d-flex flex-column h-100 mb-5 mb-lg-0">--}}
{{--                        <div class="card-body">--}}
{{--                            <img src="assets/img/landing/blog-3.jpg" alt="" class="object-fit-cover h-200px w-100 d-block">--}}
{{--                        </div>--}}
{{--                        <div class="flex-1 p-3 pb-0">--}}
{{--                            <div class="mb-2">--}}
{{--                                <span class="bg-theme bg-opacity-15 text-theme px-2 py-1 rounded small fw-bold">Search Engine</span>--}}
{{--                            </div>--}}
{{--                            <h5>Effective SEO Strategies for 2025</h5>--}}
{{--                            <p>Dive into actionable SEO strategies and tips to boost your website’s visibility and drive organic traffic.</p>--}}
{{--                        </div>--}}
{{--                        <div class="p-3 pt-0 text-body text-opacity-50">June 29, 2025</div>--}}
{{--                        <div class="card-arrow">--}}
{{--                            <div class="card-arrow-top-left"></div>--}}
{{--                            <div class="card-arrow-top-right"></div>--}}
{{--                            <div class="card-arrow-bottom-left"></div>--}}
{{--                            <div class="card-arrow-bottom-right"></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-xl-3 col-lg-4 col-sm-6">--}}
{{--                    <div class="card d-flex flex-column h-100 mb-5 mb-lg-0">--}}
{{--                        <div class="card-body">--}}
{{--                            <img src="assets/img/landing/blog-4.jpg" alt="" class="object-fit-cover h-200px w-100 d-block">--}}
{{--                        </div>--}}
{{--                        <div class="flex-1 p-3 pb-0">--}}
{{--                            <div class="mb-2">--}}
{{--                                <span class="bg-theme bg-opacity-15 text-theme px-2 py-1 rounded small fw-bold">Cyber Security</span>--}}
{{--                            </div>--}}
{{--                            <h5>Security Essentials: Protecting Your Website from Cyber Threats</h5>--}}
{{--                            <p>Essential security measures and best practices to safeguard your website and user data from cyber threats.</p>--}}
{{--                        </div>--}}
{{--                        <div class="p-3 pt-0 text-body text-opacity-50">June 27, 2025</div>--}}
{{--                        <div class="card-arrow">--}}
{{--                            <div class="card-arrow-top-left"></div>--}}
{{--                            <div class="card-arrow-top-right"></div>--}}
{{--                            <div class="card-arrow-bottom-left"></div>--}}
{{--                            <div class="card-arrow-bottom-right"></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="text-center">--}}
{{--                <a href="#" class="text-decoration-none text-body text-opacity-50 h6">See More Company Stories <i class="fa fa-arrow-right ms-3"></i></a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <!-- END #blog -->





    <!-- BEGIN #footer -->
    @include('landing-page.partial.footer')
    <!-- END #footer -->

    <!-- Support Floating Button -->
    @include('partials.support-floating-button')

</div>
<!-- END #app -->

<!-- ================== BEGIN core-js ================== -->
<script src="{{asset('dashboard/assets/js/vendor.min.js')}}"></script>
<script src="{{asset('dashboard/assets/js/app.min.js')}}"></script>
<!-- ================== END core-js ================== -->

<!-- ================== BEGIN page-js ================== -->
<script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
<script src="{{asset('dashboard/assets/plugins/lity/dist/lity.min.js')}}"></script>
<!-- ================== END page-js ================== -->
<script>
    $(document).ready(function () {
        $('form[name="form_contact_us"]').on('submit', function (e) {
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
                success: function (response) {
                    alert('تم إرسال الرسالة بنجاح');
                    $('form[name="form_contact_us"]')[0].reset();
                },
                error: function (xhr) {
                    alert('حدث خطأ أثناء الإرسال');
                }
            });
        });
    });
</script>


</body>
</html>
