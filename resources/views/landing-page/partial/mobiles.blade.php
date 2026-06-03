<section class="px-3 px-lg-5">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-md-8 d-flex justify-content-center align-items-center">
                <div class="w-100 text-center">
                    <img src="{{ asset('dashboard/assets/img/landing/hawat-phones.png') }}"
                        alt="تطبيق حسبة على الهواتف"
                        class="img-fluid"
                        style="
                            max-width: 600px;
                            width: 100%;
                            height: auto;
                            object-fit: contain;
                            display: inline-block;
                        ">
                </div>
            </div>

            <div class="col-md-4 d-flex flex-column align-items-start text-start gap-3 px-2">

                <img src="{{ asset('dashboard/assets/img/logo/logo.png') }}"
                    alt="حسبة"
                    class="img-fluid"
                    style="width: 120px; height: 120px; border-radius: 20px; box-shadow: 4px 8px 10px rgba(0,0,0,0.2);">

                <div>
                    <h2 class="fw-bold mb-1">{{ __('landing-page.app_name') }}</h2>
                    <p class="text-muted mb-0">{{ __('landing-page.app_description') }}</p>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-3">
                    <a href="https://play.google.com/store/apps/details?id=com.os.hawat" target="_blank">
                        <img src="{{ asset('dashboard/assets/img/landing/google-play.svg') }}"
                            alt="Google Play"
                            style="height:40px; width:auto;">
                    </a>
                    <a href="https://apps.apple.com/gb/app/hawat-حسبة/id6751249892?uo=2" target="_blank">
                        <img src="{{ asset('dashboard/assets/img/landing/app-store.svg') }}"
                            alt="App Store"
                            style="height:40px; width:auto;">
                    </a>
                </div>

            </div>

        </div>
    </div>
</section>
