<div class="row g-4 mt-4">

    <!-- Best Performing Species -->
    <div class="col-md-4">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">🐟 {{ __('owner.generated.best_performing_species') }}</h6>
                <h4 class="fw-bold mb-1 text-primary" id="bestFishName"></h4>
                <p class="mb-2">{{ __('owner.generated.highest_revenue') }}<span class="fw-bold text-success" id="bestFishRevenue">0</span></p>
                <div class="d-flex justify-content-between">
                    <small class="text-muted" id="bestFishCatchCount">0</small>
                    <small class="text-muted" id="bestFishWeight">0</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Productive Location -->
    <div class="col-md-4">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">📍 {{ __('owner.generated.most_productive_location') }}</h6>
                <h4 class="fw-bold mb-1 text-primary" id="topPortName"></h4>
                <p class="mb-2">{{ __('owner.generated.highest_catch_count') }}<span class="fw-bold text-success" id="topPortTrips">0</span></p>
                <small class="text-muted">{{ __('owner.generated.fishing_trips') }}</small>
            </div>
        </div>
    </div>

    <!-- Fleet Performance -->
    <div class="col-md-4">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">🚢 {{ __('owner.generated.boats_performance') }}</h6>
                <h4 class="fw-bold mb-1 text-primary" id="boatsPerformance"></h4>
                <p class="mb-2">{{ __('owner.generated.best_boat') }}<span id="bestBoatName"></span> (<span class="fw-bold text-info" id="bestBoatTrips"></span> {{ __('owner.generated.successful_trips') }})</p>

                <small class="text-muted">{{ __('owner.generated.boats_activity') }}</small>
            </div>
        </div>
    </div>

</div>

<!-- Key Insights -->
<div class="row g-4 mt-4">

    <!-- Performance Highlights -->
    <div class="col-md-6">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-4">📈 {{ __('owner.generated.key_performance_indicators') }}</h5>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted"><i class="bi bi-graph-up me-2 text-success"></i>{{ __('owner.generated.revenue_growth') }}</span>
                    <span class="fw-bold text-success" id="totalRevenue"></span>
                </div>

{{--                <div class="d-flex justify-content-between align-items-center mb-3">--}}
{{--                    <span class="text-muted"><i class="bi bi-speedometer2 me-2 text-primary"></i>{{ __('owner.generated.fishing_efficiency') }}</span>--}}
{{--                    <span class="fw-bold text-primary">{{ __('owner.generated.weight_385_lbs') }}/ {{ __('owner.boats.trip') }}</span>--}}
{{--                </div>--}}

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted"><i class="bi bi-cash-coin me-2 text-warning"></i>{{ __('owner.generated.avg_revenue_per_trip') }}</span>
                    <span class="fw-bold text-warning" id="revenuePerTrip"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Metrics -->
    <div class="col-md-6">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-4">⚙️ {{ __('owner.generated.operational_indicators') }}</h5>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted"><i class="bi bi-water me-2 text-info"></i>{{ __('owner.generated.boats_activity') }}</span>
                    <span class="fw-bold text-success" id="activeBoats">0</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted"><i class="bi bi-geo-alt me-2 text-danger"></i>{{ __('owner.generated.fishing_areas') }}</span>
                    <span class="fw-bold text-primary" id="activePorts">0</span>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted"><i class="bi bi-grid-3x3-gap me-2 text-secondary"></i>{{ __('owner.generated.species_diversity') }}</span>
                    <span class="fw-bold text-info" id="distinctFishTypes"></span>
                </div>
            </div>
        </div>
    </div>

</div>
