<!-- Trips Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-map text-primary fs-2"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1 small">{{ __('owner.boats.total_trips') }}</h6>
                        <h3 class="mb-0 fw-bold text-primary" id="trip_count">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-hourglass-split text-warning fs-2"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1 small">{{ __('owner.statusLabels.2') ?? __('owner.generated.item_59fdab') }}</h6>
                        <h3 class="mb-0 fw-bold text-warning" id="trip_waiting_status">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle text-success fs-2"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1 small">{{ __('owner.statusLabels.8') ?? __('owner.generated.item_02ee8f') }}</h6>
                        <h3 class="mb-0 fw-bold text-success" id="trip_completed_status">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-cash-stack text-danger fs-2"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1 small">{{ __('owner.boats.trip_revenues') }}</h6>
                        <h3 class="mb-0 fw-bold text-danger" id="sales_amount">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Trips Data Table -->
<div class="tab-content">
    <div class="tab-pane fade show active" id="allTab">
        <div id="datatable" class="mb-5">
            <div class="table-responsive">
                <table id="datatableDefault" class="table table-hover align-middle text-center" style="width:100%">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">#</th>
                            <th class="border-0">{{ __('owner.boats.trip_number') }}</th>
                            <th class="border-0">{{ __('owner.boats.captain') }}</th>
                            <th class="border-0">{{ __('owner.boats.departure_date') }}</th>
                            <th class="border-0">{{ __('owner.boats.return_date') }}</th>
                            <th class="border-0">{{ __('owner.boats.trip_status') }}</th>
                            <th class="border-0">{{ __('owner.boats.trip_revenues') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
