@extends('owner.layouts.master')

@section('title')
    {{ __('owner.generated.item_46ba3e') }}
@endsection

@section('css')
    <style>
        .expense-fields {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .maintenance-item {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .maintenance-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .maintenance-checkbox:checked~* .maintenance-item,
        .maintenance-item:has(.maintenance-checkbox:checked) {
            border-color: #28a745;
            background-color: rgba(40, 167, 69, 0.05);
        }

        .equipment-row {
            background-color: var(--bs-tertiary-bg);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
        }

        .section-header {
            background: linear-gradient(135deg, #0d6efd 0%, #375aeb 50%, #004085 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px 8px 0 0;
            margin-bottom: 0;
        }

        .form-section {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .form-section .card-body {
            padding: 25px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex align-items-center mb-4">
            <div class="me-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('owner.expenses.index') }}">{{ __('owner.generated.expenses_management') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('owner.expenses.add_new') }}</li>
                    </ol>
                </nav>
                <h1 class="page-header mb-0">{{ __('owner.expenses.add_new') }}</h1>
                <p class="text-muted">{{ __('owner.generated.add_new_expense_desc') }}</p>
            </div>
            <div>
                <a href="{{ route('owner.expenses.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-right"></i> {{ __('owner.generated.back_to_menu') }}</a>
            </div>
        </div>

        <form id="expenseForm" enctype="multipart/form-data">
            @csrf
            <div class="form-section">
                <h5 class="section-header">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ __('owner.dalal.modal.basic_info') }}
                </h5>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.sales.date') }}<span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.expenses.table.vendor') }}</label>
                            <select class="form-select" name="vendor_id">
                                <option value="">{{ __('owner.generated.select_vendor') }}</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.catch.filters.boat') }}</label>
                            <select class="form-select" name="boat_id">
                                <option value="general">{{ __('owner.general') }}</option>
                                @foreach ($boats as $boat)
                                    <option value="{{ $boat->id }}">{{ $boat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.generated.expense_type') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="expenseType" name="expense_type" required>
                                <option selected disabled>{{ __('owner.generated.select_type') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->type }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="generalFields" class="form-section expense-fields" style="display:none;">
                <h5 class="section-header">
                    <i class="bi bi-receipt me-2"></i>
                    {{ __('owner.generated.general_expense_details') }}
                </h5>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.expenses.sections.categories.table.category') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="category_id">
                                <option disabled selected>{{ __('owner.generated.select_category') }}</option>
                                @foreach ($categories_general as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.expenses.print.description') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="description"
                                placeholder="{{ __('owner.generated.placeholder_description') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.generated.label_amount') }} {!! '<span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}
                                <span class="text-danger">*</span></label>
                            <input type="number" class="form-control total_price_input" name="total_price"
                                placeholder="0.00" step="0.01">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.generated.discount_type') }}</label>
                            <select class="form-select discount_type_select" name="discount_type">
                                <option value="none">{{ __('owner.generated.no_discount') }}</option>
                                <option value="percentage">{{ __('owner.payrolls.show.percentage') }}%</option>
                                <option value="fixed">{{ __('owner.generated.fixed_amount') }}</option>
                            </select>
                        </div>

                        <div class="col-md-3 discount_value_div" style="display:none;">
                            <label class="form-label">{{ __('owner.expenses.show.totals.discount_value') }}</label>
                            <input type="number" class="form-control discount_value_input" name="discount_value"
                                value="0" step="0.01">
                        </div>

                        <div class="col-md-3 final_price_div" style="display:none;">
                            <label class="form-label">{{ __('owner.generated.price_after_discount') }}</label>
                            <input type="number" class="form-control final_price_input" name="final_price" readonly
                                step="0.01">
                        </div>
                    </div>
                </div>
            </div>

            <div id="governmentFields" class="form-section expense-fields" style="display:none;">
                <h5 class="section-header">
                    <i class="bi bi-receipt me-2"></i>
                    {{ __('owner.generated.gov_expense_details') }}
                </h5>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.expenses.sections.categories.table.category') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="category_id">
                                <option disabled selected>{{ __('owner.generated.select_category') }}</option>
                                @foreach ($categories_government as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.expenses.print.description') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="description"
                                placeholder="{{ __('owner.generated.placeholder_description') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.generated.label_amount') }} {!! '<span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}
                                <span class="text-danger">*</span></label>
                            <input type="number" class="form-control total_price_input_gov" name="total_price"
                                placeholder="0.00" step="0.01">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.generated.discount_type') }}</label>
                            <select class="form-select discount_type_select_gov" name="discount_type">
                                <option value="none">{{ __('owner.generated.no_discount') }}</option>
                                <option value="percentage">{{ __('owner.payrolls.show.percentage') }}%</option>
                                <option value="fixed">{{ __('owner.generated.fixed_amount') }}</option>
                            </select>
                        </div>

                        <div class="col-md-3 discount_value_div_gov" style="display:none;">
                            <label class="form-label">{{ __('owner.expenses.show.totals.discount_value') }}</label>
                            <input type="number" class="form-control discount_value_input_gov" name="discount_value"
                                value="0" step="0.01">
                        </div>

                        <div class="col-md-3 final_price_div_gov" style="display:none;">
                            <label class="form-label">{{ __('owner.generated.price_after_discount') }}</label>
                            <input type="number" class="form-control final_price_input_gov" name="final_price" readonly
                                step="0.01">
                        </div>
                    </div>
                </div>
            </div>

            <div id="operatingFields" class="form-section expense-fields" style="display:none;">
                <h5 class="section-header">
                    <i class="bi bi-tools me-2"></i>
                    {{ __('owner.generated.operational_expense_details') }}
                </h5>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.expenses.sections.categories.table.category') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="category_id">
                                <option disabled selected>{{ __('owner.generated.select_category') }}</option>
                                @foreach ($categories_operating as $category)
                                    <option value="{{ $category->id }}" data-type="{{ $category->type }}">
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="equipmentWrapper" style="display:none;">
                            <div class="text-end mb-4">
                                <button type="button" class="btn btn-outline-primary" id="addEquipment">
                                    <i class="bi bi-plus-circle"></i>
                                    {{ __('owner.generated.add_other_equipment') }}</button>
                            </div>
                            <div class="equipment-row">
                                <div class="row g-3">
                                    {{-- <div class="col-md-4">
                                        <label class="form-label">{{ __('owner.generated.equipment') }}<span
                                                class="text-danger">*</span></label>
                                        <select class="form-select equipment_name" name="fishing_equipment_id[]">
                                            <option selected disabled>{{ __('owner.generated.select_equipment') }}</option>
                                            @foreach ($fishingEquipments as $equipment)
                                                <option value="{{ $equipment->id }}"
                                                    data-unit-price="{{ $equipment->unit_price }}">{{ $equipment->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div> --}}

                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('owner.expenses.print.quantity') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control quantity" name="quantity[]"
                                            value="1" min="1">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('owner.generated.label_unit_price') }}
                                            {!! '<span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control unit_price" name="unit_price[]"
                                            value="0" step="0.01">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('owner.assets.total_price') }}</label>
                                        <input type="number" class="form-control total_price_equipment"
                                            name="total_price_equipment[]" readonly step="0.01">
                                    </div>

                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm removeEquipment">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="notes_div_equipment">
                                                        <div class="col-12">
                                                            <label class="form-label">{{ __('owner.dalal_invoices.additional_notes') }}</label>
                                                            <textarea class="form-control" rows="2" placeholder="{{ __('owner.generated.placeholder_notes') }}"></textarea>
                                                        </div>
                                                    </div> -->
                        </div>

                        <div id="simpleOperatingWrapper" style="display:none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('owner.expenses.print.description') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="description_operating"
                                        placeholder="{{ __('owner.generated.placeholder_operating_description') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('owner.generated.label_amount') }}
                                        {!! '<span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}
                                        <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="total_price_operating"
                                        placeholder="0.00" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 border-top pt-3">
                            <div class="col-md-3">
                                <label class="form-label">{{ __('owner.generated.grand_total') }}</label>
                                <input type="number" class="form-control fw-bold" id="operatingGrandTotal" readonly
                                    step="0.01">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ __('owner.generated.discount_type') }}</label>
                                <select class="form-select discount_type_select_operating" name="discount_type_operating">
                                    <option value="none">{{ __('owner.generated.no_discount') }}</option>
                                    <option value="percentage">{{ __('owner.payrolls.show.percentage') }}%</option>
                                    <option value="fixed">{{ __('owner.generated.fixed_amount') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3 discount_value_div_operating" style="display:none;">
                                <label class="form-label">{{ __('owner.expenses.show.totals.discount_value') }}</label>
                                <input type="number" class="form-control discount_value_input_operating"
                                    name="discount_value_operating" value="0" step="0.01">
                            </div>

                            <div class="col-md-3 final_price_div_operating" style="display:none;">
                                <label class="form-label">{{ __('owner.generated.price_after_discount') }}</label>
                                <input type="number" class="form-control final_price_input_operating fw-bold"
                                    name="final_price_operating" readonly step="0.01">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="maintenanceFields" class="form-section expense-fields" style="display:none;">
                <h5 class="section-header">
                    <i class="bi bi-gear me-2"></i>
                    {{ __('owner.expenses.print.maintenance_details') }}
                </h5>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('owner.generated.select_unpaid_scheduled_maintenance') }}
                    </div>

                    <div id="maintenanceWrapper">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">{{ __('owner.dashboard.loading') }}</span>
                            </div>
                            <p class="mt-2">{{ __('owner.generated.loading_available_maintenance') }}</p>
                        </div>
                    </div>

                    <div class="row g-3 border-top pt-3" id="maintenanceTotalSection" style="display:none;">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.generated.grand_total') }}</label>
                            <input type="number" class="form-control fw-bold" id="maintenanceGrandTotal" readonly
                                step="0.01">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.generated.discount_type') }}</label>
                            <select class="form-select discount_type_select_maintenance" name="discount_type_maintenance">
                                <option value="none">{{ __('owner.generated.no_discount') }}</option>
                                <option value="percentage">{{ __('owner.payrolls.show.percentage') }}%</option>
                                <option value="fixed">{{ __('owner.generated.fixed_amount') }}</option>
                            </select>
                        </div>

                        <div class="col-md-3 discount_value_div_maintenance" style="display:none;">
                            <label class="form-label">{{ __('owner.expenses.show.totals.discount_value') }}</label>
                            <input type="number" class="form-control discount_value_input_maintenance"
                                name="discount_value_maintenance" value="0" step="0.01">
                        </div>

                        <div class="col-md-3 final_price_div_maintenance" style="display:none;">
                            <label class="form-label">{{ __('owner.generated.price_after_discount') }}</label>
                            <input type="number" class="form-control final_price_input_maintenance fw-bold"
                                name="final_price_maintenance" readonly step="0.01">
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('owner.dalal_invoices.additional_notes') }}</label>
                            <textarea class="form-control" rows="3" placeholder="{{ __('owner.generated.placeholder_notes') }}"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <div class="form-section">
                <h5 class="section-header">
                    <i class="bi bi-credit-card me-2"></i>
                    {{ __('owner.generated.payment_attachment_info') }}
                </h5>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.sales.payment_method_id') }}</label>
                            <select class="form-select" name="payment_method_id">
                                <option value="">{{ __('owner.generated.select_method') }}</option>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.assets.status') }}</label>
                            <select class="form-select" name="status">
                                <option value="pending">{{ __('owner.pending') }}</option>
                                <option value="paid">{{ __('owner.paid') }}</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.generated.attachments') }}</label>
                            <input type="file" class="form-control" name="attachment"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>

                    </div>
                </div>
            </div>

            <div class="text-end mb-4">
                <button type="reset" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-x-circle"></i> {{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                <button type="submit" class="btn btn-success" id="saveExpenseBtn">
                    <i class="bi bi-check-circle"></i> {{ __('owner.generated.save_expense') }}</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        window.routes = {
            expensesIndex: "{{ route('owner.expenses.index') }}",
            expensesData: "{{ route('owner.expenses.data') }}",
            expensesStore: "{{ route('owner.expenses.store') }}",
            expensesUpdate: "{{ route('owner.expenses.update', ':id') }}",
            expensesDestroy: "{{ route('owner.expenses.destroy', ':id') }}",
            availableMaintenanceData: "{{ route('owner.api.available-maintenance.data') }}",
            boatsData: "{{ route('owner.api.boats.data') }}",
            fishingEquipmentsData: "{{ route('owner.api.fishing-equipments.data') }}",

        };
    </script>
    <script src="{{ asset('dashboard/assets/js/owner/expenses/create.js') }}"></script>
@endsection
