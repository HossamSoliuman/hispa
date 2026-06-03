<!-- BEGIN #modalCreate -->
<div class="modal fade" id="modalCreate">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('owner.customers.modal.create_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('owner.customers.store') }}" id="createForm" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="row">

                        <div class="col-4 ">

                            <div class="form-group ">
                                <label for="name"
                                    class="form-label">{{ __('owner.customers.modal.labels.name') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control  "
                                    required placeholder="{{ __('owner.customers.modal.labels.name') }}">


                                @error('name')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-4 ">
                            <div class="form-group ">
                                <label for="phone"
                                    class="form-label">{{ __('owner.customers.modal.labels.phone') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control  "
                                    required placeholder="{{ __('owner.customers.modal.labels.phone') }}">


                                @error('phone')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-4 ">
                            <div class="form-group ">
                                <label for="email"
                                    class="form-label">{{ __('owner.customers.modal.labels.email') }}<span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control  "
                                    required placeholder="{{ __('owner.customers.modal.labels.email') }}">
                                @error('email')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- {{ __('owner.generated.item_49735e') }} -->
                        <div class="col-4 ">
                            <div class="form-group ">
                                <label for="type"
                                    class="form-label">{{ __('owner.customers.modal.labels.type') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="type" value="{{ old('type') }}" class="form-control"
                                    placeholder="{{ __('owner.customers.modal.labels.type') }}">
                                @error('type')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4 ">
                            <div class="form-group ">
                                <label for="notes"
                                    class="form-label">{{ __('owner.customers.modal.labels.notes') }}</label>
                                <textarea class="form-control  " name="notes" placeholder="{{ __('owner.customers.modal.labels.notes') }}">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-check form-switch " style="margin-top: 35px">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label"
                                    for="status">{{ __('owner.customers.modal.labels.status') }}</label>
                                @error('status')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default"
                        data-bs-dismiss="modal">{{ __('owner.customers.modal.buttons.close') }}</button>
                    <button type="submit"
                        class="btn btn-outline-theme">{{ __('owner.customers.modal.buttons.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END #modalCreate-->
<div class="modal fade" id="modelEdit">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('owner.customers.modal.edit_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('owner.customers.update', 'update') }}" id="editForm" method="post"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <div class="row">

                        <div class="col-4 ">

                            <div class="form-group ">
                                <input type="hidden" id="id" name="id">
                                <label for="name" class="form-label">{{ __('owner.assets.name') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    class="form-control  " required placeholder="{{ __('owner.assets.name') }}">


                                @error('name')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-4 ">
                            <div class="form-group ">
                                <label for="phone"
                                    class="form-label">{{ __('owner.dalal.modal.form.phone') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                    class="form-control  " required
                                    placeholder="{{ __('owner.dalal.modal.form.phone') }}">


                                @error('phone')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-4 ">
                            <div class="form-group ">
                                <label for="email"
                                    class="form-label">{{ __('owner.customers.modal.labels.email') }}<span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="form-control  " required
                                    placeholder="{{ __('owner.customers.modal.labels.email') }}">


                                @error('email')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- {{ __('owner.generated.item_49735e') }} -->
                        <div class="col-4 ">
                            <div class="form-group ">
                                <label for="type"
                                    class="form-label">{{ __('owner.customers.modal.labels.type') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" id="type" name="type" value="{{ old('type') }}"
                                    class="form-control" placeholder="{{ __('owner.customers.modal.labels.type') }}">
                                @error('type')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4 ">
                            <div class="form-group ">
                                <label for="notes"
                                    class="form-label">{{ __('owner.expenses.show.notes') }}</label>
                                <textarea class="form-control  " id="notes" name="notes" placeholder="{{ __('owner.expenses.show.notes') }}">{{ old('notes') }}</textarea>



                                @error('notes')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                                <div class="col-xl-6">
                                    <div class="form-check form-switch " style="margin-top: 35px">
                                        <input type="checkbox" name="status" id="status"
                                            class="form-check-input" value="{{ old('status', 1) }}" checked>
                                        <label class="form-check-label"
                                            for="status">{{ __('owner.customers.modal.labels.status') }}</label>
                                        @error('status')
                                            <span class="text-danger error">{{ $message }}</span>
                                        @enderror

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default"
                        data-bs-dismiss="modal">{{ __('owner.customers.modal.buttons.close') }}</button>
                    <button type="submit"
                        class="btn btn-outline-theme">{{ __('owner.customers.modal.buttons.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="addCustomerModalLabel">
                        {{ __('owner.customers.modal.create_title') }}</h5>
                    <small class="text-muted">{{ __('owner.generated.fill_data_by_customer_type') }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('owner.generated.btn_close_modal') }}"></button>
            </div>

            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <!-- {{ __('owner.generated.item_49735e') }} -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.customers.modal.labels.type') }}</label>
                            <select class="form-select" id="customerType">
                                <option value="dalal">{{ __('owner.dalal.performance.broker_unit') }}</option>
                                <option value="regular">{{ __('owner.generated.general_customer') }}</option>
                            </select>
                        </div>

                        <!-- {{ __('owner.generated.item_023966') }}: {{ __('owner.generated.item_2e8b17') }} -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.assets.name') }}</label>
                            <input type="text" class="form-control"
                                placeholder="{{ __('owner.generated.placeholder_customer_name') }}">
                        </div>
                    </div>

                    <!-- {{ __('owner.generated.item_81e9e3') }} -->
                    <div id="dalalFields" class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal.modal.form.phone') }}*</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal.modal.form.email') }}*</label>
                            <input type="email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.crew.edit.id_number') }}*</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.commercial_registration_no') }}*</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal.modal.form.tax_number') }}*</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.region_name') }}*</label>
                            <select class="form-select">
                                <option>{{ __('owner.generated.eastern_region') }}</option>
                                <option>{{ __('owner.generated.western_region') }}</option>
                                <option>{{ __('owner.generated.central_region') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.governorate_name') }}*</label>
                            <select class="form-select">
                                <option>{{ __('owner.generated.dammam') }}</option>
                                <option>{{ __('owner.generated.jeddah') }}</option>
                                <option>{{ __('owner.generated.riyadh') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- {{ __('owner.generated.item_68da47') }} -->
                    <div id="regularFields" class="row g-3 mt-2 d-none">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal.modal.form.phone') }}</label>
                            <input type="text" class="form-control" placeholder="05XXXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal.modal.form.email') }}</label>
                            <input type="email" class="form-control" placeholder="example@email.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.region_name') }}</label>
                            <select class="form-select">
                                <option>{{ __('owner.generated.eastern_region') }}</option>
                                <option>{{ __('owner.generated.western_region') }}</option>
                                <option>{{ __('owner.generated.central_region') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.governorate_name') }}</label>
                            <select class="form-select">
                                <option>{{ __('owner.generated.dammam') }}</option>
                                <option>{{ __('owner.generated.jeddah') }}</option>
                                <option>{{ __('owner.generated.riyadh') }}</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                <button class="btn btn-primary">{{ __('owner.generated.save_customer') }}</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="createSaleModal" tabindex="-1" aria-labelledby="createSaleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="createSaleModalLabel">{{ __('owner.generated.new_sale') }}</h5>
                    <small class="text-muted">{{ __('owner.generated.register_new_sale') }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('owner.generated.btn_close_modal') }}"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.sales.customer') }}</label>
                            <select class="form-select">
                                <option selected disabled>{{ __('owner.generated.select_customer') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal_invoices.table.sale_date') }}</label>
                            <input type="date" class="form-control" value="2025-07-27">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.assets.fish') }}</label>
                            <input type="text" class="form-control" placeholder="Hamour, Kan'ad, ...">
                        </div>
                        <div class="col-md-6">
                            <label
                                class="form-label">{{ __('owner.expenses.print.quantity') }}({{ __('owner.generated.pieces') }})</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label
                                class="form-label">{{ __('owner.assets.weight') }}({{ __('owner.sales_report.kg') }})</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.reports.price_per_kg') }}</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.delivery_date') }}</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.sales.payment_status') }}</label>
                            <select class="form-select">
                                <option selected>{{ __('owner.status.pending') }}</option>
                                <option>{{ __('owner.status.paid') }}</option>
                                <option>{{ __('owner.generated.partial') }}</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label
                                class="form-label">{{ __('owner.expenses.sections.categories.table.total_amount') }}</label>
                            <input type="text" class="form-control" placeholder="$0" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ __('owner.expenses.show.notes') }}</label>
                            <textarea class="form-control" rows="2"
                                placeholder="{{ __('owner.generated.placeholder_additional_notes') }}"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                <button class="btn btn-primary">{{ __('owner.generated.confirm_sale') }}</button>
            </div>
        </div>
    </div>
</div>
