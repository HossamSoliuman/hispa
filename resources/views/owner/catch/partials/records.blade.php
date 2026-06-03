<div class="card mt-4 shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">{{ __('owner.generated.catch_logs') }}</h5>
            <small class="text-muted">{{ __('owner.generated.track_all_catches') }}</small>
        </div>
        <div class="d-flex align-items-center">
            <span class="me-2 text-muted">{{ __('owner.generated.sort_by') }}</span>
            <select class="form-select form-select-sm w-auto">
                <option value="date">{{ __('owner.sales.date') }}</option>
                <option value="weight">{{ __('owner.assets.weight') }}</option>
                <option value="quantity">{{ __('owner.expenses.print.quantity') }}</option>
                <option value="revenue">{{ __('owner.payrolls.table.total_revenues') }}</option>
                <option value="species">{{ __('owner.catch.tabs.species') }}</option>
            </select>
        </div>

    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                    <tr>
                        <th>{{ __('owner.generated.trip') }}</th>
                        <th>{{ __('owner.catch.filters.boat') }}</th>
                        <th>{{ __('owner.assets.weight') }}({{ __('owner.generated.kg') }})</th>
                        <th>{{ __('owner.generated.price') }}</th>
                        <th>{{ __('owner.generated.trip_start') }}</th>
                        <th>{{ __('owner.generated.trip_end') }}</th>
                        <th>{{ __('owner.assets.actions') }}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
