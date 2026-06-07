<div class="modal fade" id="crewViewModal" tabindex="-1" aria-labelledby="crewViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crewViewModalLabel">
                    <i class="bi bi-person-badge me-1"></i>{{ __('owner.crew.details_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('owner.assets.name') }}</small>
                        <span class="fw-semibold" data-field="name">--</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('owner.customers.modal.labels.email') }}</small>
                        <span class="fw-semibold" data-field="email">--</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('owner.crew.edit.phone') }}</small>
                        <span class="fw-semibold" data-field="phone">--</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('owner.employee.table.job_title') }}</small>
                        <span class="fw-semibold" data-field="job_title">--</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('owner.employee.table.nationality') }}</small>
                        <span class="fw-semibold" data-field="nationality">--</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('owner.crew.edit.id_number') }}</small>
                        <span class="fw-semibold" data-field="id_number">--</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('owner.payrolls.show.salary_type') }}</small>
                        <span class="fw-semibold" data-field="salary_type">--</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('owner.crew.edit.salary_amount') }}</small>
                        <span class="fw-semibold" data-field="salary_amount">--</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('owner.generated.fishing_license') }}</small>
                        <span class="fw-semibold" data-field="fishing_license_number">--</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('owner.generated.fishing_license_expiry_date') }}</small>
                        <span class="fw-semibold" data-field="fishing_license_expiry">--</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">{{ __('owner.generated.region_name') }}</small>
                        <span class="fw-semibold" data-field="region">--</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">{{ __('owner.generated.governorate_name') }}</small>
                        <span class="fw-semibold" data-field="governorate">--</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">{{ __('owner.crew.table.port') }}</small>
                        <span class="fw-semibold" data-field="port">--</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">{{ __('owner.dalal.modal.form.bank_name') }}</small>
                        <span class="fw-semibold" data-field="bank_name">--</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">{{ __('owner.dalal.modal.form.bank_account') }}</small>
                        <span class="fw-semibold" data-field="account_number">--</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">IBAN</small>
                        <span class="fw-semibold" data-field="iban">--</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('owner.customers.modal.labels.status') }}</small>
                        <span class="fw-semibold" data-field="status">--</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('owner.actions.close') }}</button>
            </div>
        </div>
    </div>
</div>
