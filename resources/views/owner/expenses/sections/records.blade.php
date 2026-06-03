<div class="card mt-4 shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">{{ __('owner.expenses.sections.records.title') }}</h5>
            <small class="text-muted">{{ __('owner.expenses.sections.records.subtitle') }}</small>
        </div>
    </div>
    {{-- <div class="card-body p-0"> --}}
    {{-- <div class="table-responsive"> --}}
    <table id="expensesTable" class="table table-hover table-bordered mb-0 text-center align-middle table-sm small"
        style="width:100%">
        <thead class="">
            <tr>
                <th>{{ __('owner.expenses.table.index') }}</th>
                <th>{{ __('owner.expenses.table.expense_number') }}</th>
                <th>{{ __('owner.expenses.table.date') }}</th>
                <th>{{ __('owner.expenses.table.type') }}</th>
                <th>{{ __('owner.expenses.table.category') }}</th>
                <th>{{ __('owner.expenses.table.boat') }}</th>
                <th>{{ __('owner.expenses.table.vendor') }}</th>
                <th>{{ __('owner.expenses.table.total_amount') }}</th>
                <th>{{ __('owner.expenses.table.final_amount') }}</th>
                <th>{{ __('owner.expenses.table.status') }}</th>
                <th>{{ __('owner.expenses.table.actions') }}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    {{-- </div> --}}
    {{-- </div> --}}
</div>
