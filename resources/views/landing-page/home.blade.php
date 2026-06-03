@extends('landing-page.index')

@section('content')

<!-- BEGIN #home -->
<div id="home" class="py-5 position-relative bg-body bg-opacity-50" data-bs-theme="light">
    <!-- BEGIN container -->
    <div class="container-xl p-3 p-lg-5 mb-0">
        <!-- BEGIN div-hero-content -->
        <div class="div-hero-content z-3 position-relative">
            <!-- BEGIN row -->
            <div class="row">
                <!-- BEGIN col-8 -->
                <div class="col-lg-6">
                    <!-- BEGIN hero-title-desc -->
                    <h1 class="display-6 fw-bold mb-4 mt-4" style="font-size: 1.9rem;">
                        {{__('landing-page.home.title')}}
                    </h1>
                    <div class="fs-18px text-body text-opacity-75 mb-4">
                        {{__('landing-page.home.description')}}
                        <span class="d-xl-inline d-none"><br></span>
                        {{__('landing-page.home.description_2')}}
                    </div>
                    <!-- END hero-title-desc -->


                    <!-- <div class="mb-2">
                        <a href="{{ route('frontend.login') }}" class="btn btn-outline-theme fw-semibold text-uppercase  text-nowrap"> {{__('landing-page.home.cta')}} <i class="fa fa-arrow-right ms-2 opacity-5"></i></a>
                    </div> -->


                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="https://play.google.com/store/apps/details?id=com.os.hawat" target="_blank">
                            <img src="{{ asset('dashboard/assets/img/landing/google-play.svg') }}"
                                alt="Google Play"
                                style="height:50px; width:auto; object-fit:contain;">
                        </a>
                        <a href="https://apps.apple.com/gb/app/hawat-حسبة/id6751249892?uo=2" target="_blank">
                            <img src="{{ asset('dashboard/assets/img/landing/app-store.svg') }}"
                                alt="App Store"
                                style="height:50px; width:auto; object-fit:contain;">
                        </a>
                    </div>


                    <!-- BEGIN row -->
                    <!-- <div class="row text-body mt-4 mb-4"> -->
                    <!-- BEGIN col-4 -->
                    <!-- <div class="col-6 mb-3 mb-lg-0">
                                <div class="d-flex align-items-center">
                                    <div class="h1 text-body text-opacity-25 me-3"><iconify-icon icon="bi:download"></iconify-icon></div>
                                    <div>
                                        <div class="fw-500 mb-0 h3">+1.8k</div>
                                        <div class="fw-500 text-body text-opacity-75">{{__('landing-page.home.stats.operations_text')}}</div>
                                    </div>
                                </div>
                            </div> -->
                    <!-- END col-4 -->
                    <!-- BEGIN col-4 -->
                    <!-- <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="h1 text-body text-opacity-25 me-3"><iconify-icon icon="bi:globe2"></iconify-icon></div>
                                    <div>
                                        <div class="fw-500 mb-0 h3">2025</div>
                                        <div class="fw-500 text-body text-opacity-75">{{__('landing-page.home.stats.launch_year_text')}}</div>
                                    </div>
                                </div>
                            </div> -->
                    <!-- END col-4 -->
                    <!-- </div> -->
                    <!-- END row -->
                </div>
                <!-- END col-8 -->
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
<!-- END #home -->


<!-- BEGIN #about -->
@include('landing-page.partial.about')

<!-- END #about -->

<!-- BEGIN divider -->
<div class="container-xxl px-3 px-lg-5">
    <hr class="opacity-4 m-0" />
</div>
<!-- END divider -->
<!-- BEGIN #features -->
@include('landing-page.partial.feature')
<!-- END #features -->

<!-- BEGIN divider -->
<div class="container-xxl px-3 px-lg-5">
    <hr class="opacity-4 m-0" />
</div>
<!-- END divider -->


<!-- BEGIN #achievements -->
@include('landing-page.partial.achievements')
<!-- END #achievements -->

<!-- BEGIN divider -->
<div class="container-xxl px-3 px-lg-5">
    <hr class="opacity-4 m-0" />
</div>
<!-- END divider -->

<!-- BEGIN #mobiles -->
@include('landing-page.partial.mobiles')
<!-- END #mobiles -->

<!-- BEGIN divider -->
<div class="container-xxl px-3 px-lg-5">
    <hr class="opacity-4 m-0" />
</div>
<!-- END divider -->

<!-- BEGIN #contact -->
@include('landing-page.partial.contact')
<!-- END #contact -->


@endsection
