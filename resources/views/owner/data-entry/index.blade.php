@extends('owner.layouts.master')

@section('title', '{{ __('owner.generated.data_entry') }}')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>{{ __('owner.generated.data_entry') }}</h4>
        <span class="text-muted">{{ __('owner.generated.fast_entry_forms') }}</span>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <button class="btn btn-link text-dark fw-bold fs-5 mt-2" data-bs-toggle="modal" data-bs-target="#catchModal">
                    <i class="bi bi-basket-fill me-2"></i>{{ __('owner.generated.add_catch_record') }}</button>
            </div>
        </div>
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <button class="btn btn-link text-dark fw-bold fs-5 mt-2" data-bs-toggle="modal" data-bs-target="#expenseModal">
                    <i class="bi bi-receipt-cutoff me-2"></i>{{ __('owner.generated.add_expense') }}</button>
            </div>
        </div>
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <button class="btn btn-link text-dark fw-bold fs-5 mt-2" data-bs-toggle="modal" data-bs-target="#crewModal">
                    <i class="bi bi-person-plus-fill me-2"></i>{{ __('owner.generated.add_crew_member') }}</button>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-3">
    <div class="col">
        <div class="card bg-primary text-white text-center shadow-sm">
            <div class="card-body">
                <h6 class="text-white">
                    <i class="bi bi-basket-fill me-2"></i>{{ __('owner.generated.latest_catch_records') }}</h6>
                <h4 class="fw-bold text-white">2</h4>
                <small class="text-white">{{ __('owner.generated.total_revenue_7920') }}</small>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card bg-warning text-white text-center shadow-sm">
            <div class="card-body">
                <h6 class="text-white">
                    <i class="bi bi-receipt-cutoff me-2"></i>
                    {{ __('owner.dashboard.total_expenses') }}</h6>
                <h4 class="fw-bold text-white">2</h4>
                <small class="text-white">{{ __('owner.generated.total_value_2350000') }}</small>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card bg-success text-white text-center shadow-sm">
            <div class="card-body">
                    <h6 class="text-white">
                    <i class="bi bi-people-fill me-2"></i>
                    {{ __('owner.generated.active_crew_members') }}</h6>
                <h4 class="fw-bold text-white">2</h4>
                <small class="text-white">{{ __('owner.generated.number_of_crew') }}</small>
            </div>
        </div>
    </div>
</div>

</div>


@include('owner.data-entry._modals')

@endsection

@section('script')
@endsection
