<div class="tab-pane fade show active" id="customers" role="tabpanel">

    {{-- <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                @include('owner.partials._card_arrow')
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-printer me-2"></i>{{ __('owner.customers.reports.title') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-sm-12">
                            <div class="report-card" onclick="printCustomersReport()">
                                <div class="report-icon bg-primary">
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                                <h6 class="mt-3 mb-1">{{ __('owner.customers.reports.customers') }}</h6>
                                <small class="text-muted">{{ __('owner.customers.reports.customers_desc') }}</small>
                            </div>
                        </div>                       
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="card shadow-sm border-0">

        {{-- <div class=" p-0">
            <div class="table-responsive"> --}}
        <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text"
            style="width:100%">
            <thead>

                <tr>
                    <th>{{ __('owner.customers.table.index') }}</th>
                    <th>{{ __('owner.customers.table.name') }}</th>
                    <th>{{ __('owner.customers.table.email') }}</th>
                    <th>{{ __('owner.customers.table.phone') }}</th>
                    <th>{{ __('owner.customers.table.type') }}</th>
                    <th>{{ __('owner.customers.table.order_count') }}</th>
                    <th>{{ __('owner.customers.table.total_sales') }}</th>
                    <th>{{ __('owner.customers.table.total_remaining') }}</th>
                    <th>{{ __('owner.customers.table.last_order') }}</th>
                    <th>{{ __('owner.customers.table.status') }}</th>
                    <th>{{ __('owner.customers.table.actions') }}</th>
                </tr>
            </thead>
            <tbody>



            </tbody>
        </table>
        {{-- 
            </div>
        </div> --}}
    </div>
</div>
