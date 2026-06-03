<div class="modal fade" id="catchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('owner.generated.add_catch_record') }}</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>{{ __('owner.sales.date') }}*</label>
                            <input type="date" class="form-control" value="2025-07-28">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.assets.type') }}*</label>
                            <input type="text" class="form-control"
                                placeholder="{{ __('owner.generated.placeholder_salmon_tuna') }}">
                        </div>
                        <div class="col-md-4">
                            <label>{{ __('owner.expenses.print.quantity') }}*</label>
                            <input type="number" class="form-control"
                                placeholder="{{ __('owner.generated.placeholder_fish_count') }}">
                        </div>
                        <div class="col-md-4">
                            <label>{{ __('owner.assets.weight') }}({{ __('owner.generated.lbs') }}) *</label>
                            <input type="number" class="form-control"
                                placeholder="{{ __('owner.generated.placeholder_total_weight') }}">
                        </div>
                        <div class="col-md-4">
                            <label>{{ __('owner.generated.price') }}/{{ __('owner.generated.lbs') }}*</label>
                            <input type="number" class="form-control" placeholder="SAR">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.generated.location') }}*</label>
                            <input type="text" class="form-control"
                                placeholder="{{ __('owner.generated.placeholder_fishing_location') }}">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.catch.filters.boat') }}*</label>
                            <input type="text" class="form-control"
                                placeholder="{{ __('owner.generated.placeholder_sea_hunter') }}">
                        </div>
                        <div class="col-12">
                            <label>{{ __('owner.expenses.show.notes') }}</label>
                            <textarea class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                <button class="btn btn-primary">{{ __('owner.generated.add_record') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('owner.generated.add_expense') }}</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>{{ __('owner.sales.date') }}*</label>
                            <input type="date" class="form-control" value="2025-07-28">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.dalal.payments.amount') }}(SAR) *</label>
                            <input type="number" class="form-control" placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.expenses.sections.categories.table.category') }}*</label>
                            <input type="text" class="form-control"
                                placeholder="{{ __('owner.generated.placeholder_fuel') }}">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.catch.filters.boat') }}</label>
                            <input type="text" class="form-control"
                                placeholder="{{ __('owner.generated.placeholder_general_for_all_boats') }}">
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('owner.expenses.print.description') }}</label>
                            <textarea class="form-control" rows="3"
                                placeholder="{{ __('owner.generated.placeholder_detailed_description') }}"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('owner.generated.upload_receipt') }}</label>
                            <input type="file" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('owner.dalal_invoices.additional_notes') }}</label>
                            <textarea class="form-control"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                <button class="btn btn-warning">{{ __('owner.generated.add_expense') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="crewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('owner.generated.add_crew_member') }}</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <h6 class="fw-bold mb-3">{{ __('owner.dalal.modal.basic_info') }}</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label>{{ __('owner.generated.full_name') }}*</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.crew.table.job_title') }}*</label>
                            <input type="text" class="form-control"
                                placeholder="{{ __('owner.generated.placeholder_captain_role') }}">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.crew.edit.date_appointment') }}*</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>{{ __('owner.generated.hourly_wage') }}*</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>{{ __('owner.generated.participation_share') }}(%)</label>
                            <input type="number" class="form-control">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">{{ __('owner.generated.contact_info') }}</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label>{{ __('owner.generated.phone_number') }}</label>
                            <input type="text" class="form-control" placeholder="+966501234567">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.dalal.modal.form.email') }}</label>
                            <input type="email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.generated.emergency_contact_person') }}</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.generated.emergency_number') }}</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">{{ __('owner.generated.legal_documents') }}</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label>{{ __('owner.crew.edit.residence_number') }}</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.crew.edit.passport_number') }}</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.crew.edit.residence_start_date') }}</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.crew.edit.residence_end_date') }}</label>
                            <input type="date" class="form-control">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">{{ __('owner.dalal.modal.banking') }}</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>{{ __('owner.dalal.modal.form.bank_name') }}</label>
                            <input type="text" class="form-control" placeholder="Al Rajhi Bank">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.dalal.modal.form.bank_account') }}</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label>IBAN</label>
                            <input type="text" class="form-control" placeholder="SA1234567890123456789012">
                        </div>
                        <div class="col-md-4">
                            <label>{{ __('owner.generated.monthly_salary') }}</label>
                            <input type="number" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                <button class="btn btn-success">{{ __('owner.generated.add_member') }}</button>
            </div>
        </div>
    </div>
</div>
