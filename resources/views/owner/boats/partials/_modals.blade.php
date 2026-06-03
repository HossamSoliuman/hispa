<!-- Modal: Boat Details -->
<div class="modal fade" id="boatDetailsModal" tabindex="-1" aria-labelledby="boatDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title text-white fw-bold" id="boatDetailsModalLabel">
                    <i class="bi bi-info-circle-fill me-2"></i>{{ __('owner.boats.boat_details_modal') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('owner.boats.close') }}"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                <div class="row g-3">
                    <!-- Basic Information Section -->
                    <div class="col-12">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-info-circle me-2"></i>{{ __('owner.boats.name') }}
                        </h6>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.name_ara') }}</label>
                                <div class="fw-semibold">{{ $boat->name_ar }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.name_eng') }}</label>
                                <div class="fw-semibold">{{ $boat->name_en }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.class') }}</label>
                                <div class="fw-semibold">{{ $boat->boat_type->name ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.boat_number') }}</label>
                                <div class="fw-semibold">{{ $boat->number }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.status') }}</label>
                                <div>
                                    <span class="badge bg-{{ $boat->status ? 'success' : 'danger' }}">
                                        {{ $boat->status ? __('owner.status.active') : __('owner.status.inactive') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Physical Specifications -->
                    <div class="col-12 mt-3">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-rulers me-2"></i>{{ __('owner.boats.type') }}
                        </h6>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.length') }}</label>
                                <div class="fw-semibold">{{ $boat->length }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.width') }}</label>
                                <div class="fw-semibold">{{ $boat->width }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.color') }}</label>
                                <div class="fw-semibold">{{ $boat->color }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- License Information -->
                    <div class="col-12 mt-3">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-file-earmark-text me-2"></i>{{ __('owner.boats.license_region') }}
                        </h6>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.license_region') }}</label>
                                <div class="fw-semibold">{{ $boat->licenseRegion->name ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.license_date') }}</label>
                                <div class="fw-semibold">{{ $boat->license_date }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.license_date_expire') }}</label>
                                <div class="fw-semibold">{{ $boat->license_date_expire }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Body & Engine -->
                    <div class="col-12 mt-3">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-gear me-2"></i>{{ __('owner.boats.engine_type') }}
                        </h6>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.body_number') }}</label>
                                <div class="fw-semibold">{{ $boat->body_number }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.body_type') }}</label>
                                <div class="fw-semibold">{{ $boat->body_type }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.callsign_number') }}</label>
                                <div class="fw-semibold">{{ $boat->callsign_number }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.serial_number') }}</label>
                                <div class="fw-semibold">{{ $boat->serial_number }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.engine_type') }}</label>
                                <div class="fw-semibold">{{ $boat->engine_type }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.engine_power') }}</label>
                                <div class="fw-semibold">{{ $boat->engine_power }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Location & Capacity -->
                    <div class="col-12 mt-3">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-geo-alt me-2"></i>{{ __('owner.boats.region') }}
                        </h6>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.crew_count') }}</label>
                                <div class="fw-semibold">{{ $boat->crew_number }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.cargo_capacity') }}</label>
                                <div class="fw-semibold">{{ $boat->payload }} {{ __('owner.units.ton') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.region') }}</label>
                                <div class="fw-semibold">{{ $boat->region->name ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.governorate') }}</label>
                                <div class="fw-semibold">{{ $boat->governorate->name ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">{{ __('owner.boats.port') }}</label>
                                <div class="fw-semibold">{{ $boat->port->name ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <label class="text-muted small mb-1">
                                    <i class="bi bi-calendar-plus me-1"></i>{{ __('owner.dashboard.dates.registration_date') ?? __('owner.generated.item_a25ef8') }}
                                </label>
                                <div class="fw-semibold">{{ $boat->created_at->format('Y-m-d') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>{{ __('owner.boats.close') }}
                </button>
            </div>
        </div>
    </div>
</div>
