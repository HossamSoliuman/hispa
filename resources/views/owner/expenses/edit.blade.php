@extends('owner.layouts.master')

@section('title', '{{ __('owner.generated.edit_expense') }}')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('owner.expenses.index') }}">{{ __('owner.generated.expenses_management') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('owner.generated.edit_expense') }}</li>
                </ol>
            </nav>
        </div>
        <div class="badge bg-primary fs-6">{{ $expense->category->name ?? '{{ __('owner.generated.undefined') }}' }}</div>
    </div>

    <!-- {{ __('owner.generated.item_c356fe') }} -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-gradient-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">
                    <i class="fas fa-receipt me-2"></i>
                    {{ __('owner.generated.item_b6a5be') }}: {{ $expense->number }}
                </h5>
                <span class="badge bg-light text-dark">{{ $expense->category->parent->name }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">{{ __('owner.catch.filters.boat') }}</h6>
                    <p class="mb-0 fw-bold">{{ $expense->boat->name ?? __('owner.general') }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">{{ __('owner.sales.date') }}</h6>
                    <p class="mb-0">{{ $expense->date }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">{{ __('owner.expenses.table.vendor') }}</h6>
                    <p class="mb-0">{{ $expense->vendor->name ?? '{{ __('owner.generated.undefined') }}' }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">{{ __('owner.assets.status') }}</h6>
                    <span class="badge bg-{{ $expense->status === 'paid' ? 'success' : 'warning' }}">
                        {{ $expense->status === 'paid' ? __('owner.status.paid') : __('owner.status.pending') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <form id="expenseEditForm" method="POST" action="{{ route('owner.expenses.update', $expense->id) }}">
        @csrf
        @method('PUT')

        <!-- {{ __('owner.generated.item_312b56') }} -->
        @if($expense->category->parent->type === 'general' || $expense->category->parent->type === 'government' || ($expense->category->parent->type == 'operating' && $expense->category->type != 'operating-equipments'))
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0 text-white">
                    <i class="fas fa-coins me-2"></i>
                    {{ __('owner.generated.edit_expenses') }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('owner.generated.label_total_amount') }} {!! '<span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</label>
                        <input type="number" name="total_price" class="form-control" step="0.01"
                            value="{{ $expense->total_price }}" required>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.generated.discount_type') }}</label>
                                <select name="discount_type" class="form-select discount-type-select">
                                    <option value="none" {{ !$expense->discount_type ? 'selected' : '' }}>{{ __('owner.generated.no_discount') }}</option>
                                    <option value="percentage" {{ $expense->discount_type === 'percentage' ? 'selected' : '' }}>{{ __('owner.generated.percentage') }}</option>
                                    <option value="fixed" {{ $expense->discount_type === 'fixed' ? 'selected' : '' }}>{{ __('owner.generated.fixed_sum') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 discount-value-section" style="{{ !$expense->discount_type ? 'display: none;' : '' }}">
                                <label class="form-label discount-label">
                                    @if($expense->discount_type === 'percentage')
                                        {{ __('owner.generated.item_af5864') }}
                                    @else
                                        {!! __('owner.generated.item_641834') . '<span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}
                                    @endif
                                </label>
                                <input type="number" name="discount_value" class="form-control discount-value-input"
                                    step="0.01" value="{{ $expense->discount_value ?? 0 }}">
                            </div>
                            <div class="col-md-4 final-price-section" style="{{ !$expense->discount_type ? 'display: none;' : '' }}">
                                <label class="form-label">{{ __('owner.generated.label_final_amount') }} {!! '<span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</label>
                                <input type="number" name="final_price" class="form-control final-price-input"
                                    value="{{ $expense->final_price }}" readonly>
                            </div>
                        </div>
                    </div>
                    <!-- {{ __('owner.generated.item_4a0cba') }} -->
                    <div class="row mt-3">

                    </div>
                </div>
            </div>
            @endif

            <!-- {{ __('owner.generated.equipment') }} -->
            @if($expense->category->parent->type == 'operating' && $expense->category->type == 'operating-equipments')
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-white">
                            <i class="fas fa-tools me-2"></i>
                            {{ __('owner.generated.edit_fishing_gear') }}</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div id="equipmentContainer">
                        @foreach($expense->details as $detail)
                        @if($detail->expenseable_type === 'App\\Models\\ExpenseFishingEquipment')
                        <div class="equipment-row border rounded p-3 mb-3" style="background-color: var(--bs-tertiary-bg);">
                            <div class="row align-items-center">
                                {{-- <div class="col-md-4">
                                    <label class="form-label">{{ __('owner.expenses.print.equipment_name') }}</label>
                                    <!-- <select name="fishing_equipment_id[]" class="form-select" required>
                                        @foreach($fishingEquipments as $equipment)
                                        <option value="{{ $equipment->id }}"
                                            {{ $detail->expenseable->fishing_equipment_id == $equipment->id ? 'selected' : '' }}>
                                            {{ $equipment->name }}
                                        </option>
                                        @endforeach
                                    </select> -->
                                    <!-- <input type="text" name="fishing_equipment_id[]" class="form-control" value="{{ $detail->expenseable->fishing_equipment_id }}" readonly> -->

                                    
                                    <input type="hidden" name="fishing_equipment_id[]" value="{{ $detail->expenseable->fishing_equipment_id }}">

                                    
                                    <input type="text" class="form-control"
                                        value="{{ $detail->expenseable->fishingEquipment->name ?? '' }}" readonly>

                                </div> --}}
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('owner.expenses.print.quantity') }}</label>
                                    <input type="number" name="quantity[]" class="form-control quantity-input"
                                        value="{{ $detail->expenseable->quantity }}" min="1" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('owner.expenses.print.unit_price') }}</label>
                                    <input type="number" name="unit_price[]" class="form-control price-input"
                                        step="0.01" value="{{ $detail->expenseable->unit_price }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('owner.dalal_invoices.total') }}</label>
                                    <input type="number" class="form-control total-price-display"
                                        value="{{ $detail->expenseable->total_price }}" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm remove-equipment-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>

                    <div class="row mt-3 border-top pt-3">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>{{ __('owner.generated.grand_total_1') }}<span id="equipmentGrandTotal" class="text-primary">{!! number_format($expense->total_price, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</span></h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.generated.discount_type') }}</label>
                            <select name="discount_type" class="form-select discount-type-select">
                                <option value="none" {{ !$expense->discount_type ? 'selected' : '' }}>{{ __('owner.generated.no_discount') }}</option>
                                <option value="percentage" {{ $expense->discount_type === 'percentage' ? 'selected' : '' }}>{{ __('owner.generated.percentage') }}</option>
                                <option value="fixed" {{ $expense->discount_type === 'fixed' ? 'selected' : '' }}>{{ __('owner.generated.fixed_sum') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 discount-value-section" style="{{ !$expense->discount_type ? 'display: none;' : '' }}">
                            <label class="form-label discount-label">
                                @if($expense->discount_type === 'percentage')
                                    {{ __('owner.generated.item_af5864') }}
                                @else
                                    {!! __('owner.generated.item_641834') . '<span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}
                                @endif
                            </label>
                            <input type="number" name="discount_value" class="form-control discount-value-input"
                                step="0.01" value="{{ $expense->discount_value ?? 0 }}">
                        </div>
                        <div class="col-md-4 final-price-section" style="{{ !$expense->discount_type ? 'display: none;' : '' }}">
                            <label class="form-label">{{ __('owner.generated.label_final_amount') }} {!! '<span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</label>
                            <input type="number" name="final_price" class="form-control final-price-input"
                                value="{{ $expense->final_price }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($expense->category->type === 'maintenance')
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-wrench me-2"></i>
                        {{ __('owner.generated.edit_maintenance') }}</h6>
                </div>
                <div class="card-body">
                    @foreach($expense->details as $detail)
                    @if($detail->expenseable_type === 'App\\Models\\Maintenance')
                    <div class="maintenance-item border rounded p-3 mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="mb-1">{{ $detail->expenseable->description }}</h6>
                                <small class="text-muted">{{ __('owner.generated.item_f4353c') }}: {{ $detail->expenseable->date }}</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('owner.generated.label_suggested_value') }} {!! '<span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</label>
                                <input type="number" name="estimated_cost" class="form-control maintenance-cost-input"
                                    step="0.01" value="{{ $detail->expenseable->estimated_cost }}" required>
                                <input type="hidden" name="maintenance_id" value="{{ $detail->expenseable->id }}">
                            </div>

                        </div>
                    </div>
                    @endif
                    @endforeach

                    <div class="row mt-3 border-top pt-3">
                        <div class="col-md-12">
                            <h6>{{ __('owner.generated.total_maintenance_cost') }}<span id="maintenanceGrandTotal" class="text-primary">{!! number_format($expense->total_price, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</span></h6>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.generated.discount_type') }}</label>
                            <select name="discount_type" class="form-select discount-type-select">
                                <option value="none" {{ !$expense->discount_type ? 'selected' : '' }}>{{ __('owner.generated.no_discount') }}</option>
                                <option value="percentage" {{ $expense->discount_type === 'percentage' ? 'selected' : '' }}>{{ __('owner.generated.percentage') }}</option>
                                <option value="fixed" {{ $expense->discount_type === 'fixed' ? 'selected' : '' }}>{{ __('owner.generated.fixed_sum') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 discount-value-section" style="{{ !$expense->discount_type ? 'display: none;' : '' }}">
                            <label class="form-label discount-label">
                                @if($expense->discount_type === 'percentage')
                                    {{ __('owner.generated.item_af5864') }}
                                @else
                                    {!! __('owner.generated.item_641834') . '<span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}
                                @endif
                            </label>
                            <input type="number" name="discount_value" class="form-control discount-value-input"
                                step="0.01" value="{{ $expense->discount_value ?? 0 }}">
                        </div>
                        <div class="col-md-4 final-price-section" style="{{ !$expense->discount_type ? 'display: none;' : '' }}">
                            <label class="form-label">{{ __('owner.generated.label_final_amount') }} {!! '<span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</label>
                            <input type="number" name="final_price" class="form-control final-price-input"
                                value="{{ $expense->final_price }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0 text-white">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('owner.generated.additional_info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.expenses.table.vendor') }}</label>
                            <select name="vendor_id" class="form-select">
                                <option value="">{{ __('owner.generated.select_vendor') }}</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ $expense->vendor_id == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.sales.payment_method_id') }}</label>
                            <select name="payment_method_id" class="form-select">
                                @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" {{ $expense->payment_method_id == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.sales.payment_status') }}</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $expense->status === 'pending' ? 'selected' : '' }}>{{ __('owner.status.pending') }}</option>
                                <option value="paid" {{ $expense->status === 'paid' ? 'selected' : '' }}>{{ __('owner.status.paid') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.expense_date') }}</label>
                            <input type="date" name="date" class="form-control" value="{{ $expense->date }}" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">{{ __('owner.generated.attachments') }}</label>
                        <input type="file" class="form-control" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        @if($expense->attachment)
                        <small class="text-muted">{{ __('owner.generated.current_attachments') }}<a href="{{ $expense->attachment_url }}" target="_blank"> {{ __('owner.expenses.show.view_attachment') }}</a></small>
                        @endif
                    </div>
                    <div class="mt-3">
                        <label class="form-label">{{ __('owner.expenses.show.notes') }}</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('owner.generated.placeholder_add_notes') }}">{{ $expense->notes }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('owner.expenses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right me-2"></i>
                            {{ __('owner.generated.back_to_menu') }}</a>
                        <div>
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <i class="fas fa-save me-2"></i>
                                {{ __('owner.generated.save_changes') }}</button>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</div>

<template id="equipmentRowTemplate">
    <div class="equipment-row border rounded p-3 mb-3 bg-light">
        <div class="row align-items-center">
            <div class="col-md-4">
                <label class="form-label">{{ __('owner.expenses.print.equipment_name') }}</label>
                <select name="fishing_equipment_id[]" class="form-select" required>
                    <option value="">{{ __('owner.generated.select_gear') }}</option>
                    @foreach($fishingEquipments as $equipment)
                    <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('owner.expenses.print.quantity') }}</label>
                <input type="number" name="quantity[]" class="form-control quantity-input" value="1" min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('owner.expenses.print.unit_price') }}</label>
                <input type="number" name="unit_price[]" class="form-control price-input" step="0.01" value="0" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('owner.dalal_invoices.total') }}</label>
                <input type="number" class="form-control total-price-display" value="0" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label d-block">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm remove-equipment-row">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>
@endsection

@section('script')
<script>
    window.routes = {
        expenseUpdate: '{{ route("owner.expenses.update", $expense->id) }}',
        expensesIndex: '{{ route("owner.expenses.index") }}'
    };
    window.expenseData = @json($expense);
</script>
<script src="{{ asset('dashboard/assets/js/owner/expenses/edit.js') }}"></script>
@endsection
