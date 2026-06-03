<div id="achievements" class="py-2 position-relative">
    <div class="container-xl p-3 p-lg-5 z-2 position-relative">
        <div class="text-center mb-5">
            <h1 class="mb-3 fw-bold">
                {{ __('landing-page.achievements.title') }}
            </h1>
            <p class="fs-16px text-body text-opacity-50 mb-5">
                {{ __('landing-page.achievements.description') }}
            </p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="card border-0 h-100 py-4">
                    <div class="card-body d-flex flex-column align-items-center justify-content-between">
                        <div>
                            <img src="{{ asset('dashboard/assets/img/landing/achievements/fishing-boat.png') }}" width="55" class="mb-3">
                            <h6 class="fw-bold text-dark mb-1">{{ __('landing-page.achievements.trips') }}</h6>
                            <p class="text-secondary small mb-3">{{ __('landing-page.achievements.trips_desc') }}</p>
                        </div>
                        <h2 class="fw-bold text-dark mb-0">439+</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 h-100 py-4">
                    <div class="card-body d-flex flex-column align-items-center justify-content-between">
                        <div>
                            <img src="{{ asset('dashboard/assets/img/landing/achievements/seafood.png') }}" width="55" class="mb-3">
                            <h6 class="fw-bold text-dark mb-1">{{ __('landing-page.achievements.fish') }}</h6>
                            <p class="text-secondary small mb-3">{{ __('landing-page.achievements.fish_desc') }}</p>
                        </div>
                        <h2 class="fw-bold text-dark mb-0">129+</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 h-100 py-4">
                    <div class="card-body d-flex flex-column align-items-center justify-content-between">
                        <div>
                            <img src="{{ asset('dashboard/assets/img/landing/achievements/deal.png') }}" width="55" class="mb-3">
                            <h6 class="fw-bold text-dark mb-1">{{ __('landing-page.achievements.sales') }}</h6>
                            <p class="text-secondary small mb-3">{{ __('landing-page.achievements.sales_desc') }}</p>
                        </div>
                        <h2 class="fw-bold text-dark mb-0">241+</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 h-100 py-4">
                    <div class="card-body d-flex flex-column align-items-center justify-content-between">
                        <div>
                            <img src="{{ asset('dashboard/assets/img/landing/achievements/fisherman.png') }}" width="55" class="mb-3">
                            <h6 class="fw-bold text-dark mb-1">{{ __('landing-page.achievements.users') }}</h6>
                            <p class="text-secondary small mb-3">{{ __('landing-page.achievements.users_desc') }}</p>
                        </div>
                        <h2 class="fw-bold text-dark mb-0">149+</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('frontend.register') }}" class="btn btn-primary px-4 py-2 rounded-pill">
                {{ __('landing-page.achievements.register_now') }}
            </a>
        </div>
    </div>
</div>
