@extends('owner.layouts.master')

@section('title', '{{ __('owner.generated.docs') }}')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">📄 {{ __('owner.generated.documents_management') }}</h4>
            <p class="text-muted mb-0">{{ __('owner.generated.manage_crew_docs_compliance') }}</p>
        </div>
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
            <i class="bi bi-upload me-1"></i> {{ __('owner.generated.upload_document') }}</button>
    </div>

    <!-- Cards  -->
    <div class="row row-cols-1 row-cols-md-5 g-3 mb-4 text-white">
        <div class="col">
            <div class="card bg-dark shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-folder2-open me-2"></i>{{ __('owner.generated.total_documents') }}</h6>
                    <h4 class="fw-bold text-white">3</h4>
                    <small class="text-white">{{ __('owner.generated.for_all_crew') }}</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-check-circle-fill me-2"></i>{{ __('owner.generated.valid_doc') }}</h6>
                    <h4 class="fw-bold text-white">2</h4>
                    <small class="text-white">{{ __('owner.generated.currently_valid') }}</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-warning shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('owner.generated.expiring_soon') }}</h6>
                    <h4 class="fw-bold text-white">1</h4>
                    <small class="text-white">{{ __('owner.generated.within_30_days') }}</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-danger shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-x-circle-fill me-2"></i>{{ __('owner.generated.expired') }}</h6>
                    <h4 class="fw-bold text-white">0</h4>
                    <small class="text-white">{{ __('owner.generated.needs_renewal') }}</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="mb-1 text-white"><i class="bi bi-shield-check me-2"></i>{{ __('owner.generated.compliant_crew') }}</h6>
                    <h4 class="fw-bold text-white">1</h4>
                    <small class="text-white">{{ __('owner.generated.of_2_total') }}</small>
                </div>
            </div>
        </div>
    </div>


    <ul class="nav nav-tabs mb-4" id="documentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-documents-tab" data-bs-toggle="tab" data-bs-target="#all-documents" type="button" role="tab">
                📁 {{ __('owner.generated.all_documents') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="crew-compliance-tab" data-bs-toggle="tab" data-bs-target="#crew-compliance" type="button" role="tab">
                ✅ {{ __('owner.generated.crew_compliance') }}</button>
        </li>
    </ul>

    <div class="tab-content" id="documentTabsContent">
        <div class="tab-pane fade show active" id="all-documents" role="tabpanel" aria-labelledby="all-documents-tab">
            <!-- Filters -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h6 class="mb-3 text-muted fw-bold"><i class="bi bi-funnel me-2"></i>{{ __('owner.generated.filters') }}</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="{{ __('owner.generated.placeholder_search_docs') }}">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option selected>{{ __('owner.generated.all_statuses') }}</option>
                                <option>{{ __('owner.generated.valid_doc') }}</option>
                                <option>{{ __('owner.generated.expiring_soon') }}</option>
                                <option>{{ __('owner.generated.expired') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option selected>{{ __('owner.generated.all_types') }}</option>
                                <option>{{ __('owner.generated.residence_permit') }}</option>
                                <option>{{ __('owner.generated.passport_1') }}</option>
                                <option>{{ __('owner.generated.license') }}</option>
                                <option>{{ __('owner.generated.medical_insurance') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle"></i> {{ __('owner.dalal.filters.clear') }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Table -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold text-muted mb-3">📂 {{ __('owner.generated.docs') }}(3)</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('owner.generated.document') }}</th>
                                    <th>{{ __('owner.generated.crew_name') }}</th>
                                    <th>{{ __('owner.assets.type') }}</th>
                                    <th>{{ __('owner.generated.document_number') }}</th>
                                    <th>{{ __('owner.customers.sales_table.date') }}</th>
                                    <th>{{ __('owner.generated.expiry_date') }}</th>
                                    <th>{{ __('owner.assets.status') }}</th>
                                    <th>{{ __('owner.assets.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>IQAMA - John Martinez</td>
                                    <td>
                                        {{ __('owner.generated.ahmed_alrashed') }}<br><small class="text-muted">{{ __('owner.generated.captain') }}</small>
                                    </td>
                                    <td>{{ __('owner.generated.residence_permit') }}</td>
                                    <td>2345678901</td>
                                    <td>2022-01-15</td>
                                    <td>2025-01-15</td>
                                    <td><span class="badge bg-success">{{ __('owner.generated.valid_doc') }}</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                        <a href="#" class="btn btn-sm btn-outline-success"><i class="bi bi-pencil"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Passport - John Martinez</td>
                                    <td>
                                        {{ __('owner.generated.ahmed_alrashed') }}<br><small class="text-muted">{{ __('owner.generated.captain') }}</small>
                                    </td>
                                    <td>{{ __('owner.generated.passport_1') }}</td>
                                    <td>A12345678</td>
                                    <td>2020-01-15</td>
                                    <td>2030-01-15</td>
                                    <td><span class="badge bg-success">{{ __('owner.generated.valid_doc') }}</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                        <a href="#" class="btn btn-sm btn-outline-success"><i class="bi bi-pencil"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>IQAMA - Mike Thompson</td>
                                    <td>
                                        {{ __('owner.generated.fatima_alzahra') }}<br><small class="text-muted">{{ __('owner.generated.first_assistant') }}</small>
                                    </td>
                                    <td>{{ __('owner.generated.residence_permit') }}</td>
                                    <td>3456789012</td>
                                    <td>2022-03-01</td>
                                    <td>2025-02-01</td>
                                    <td><span class="badge bg-warning text-dark">{{ __('owner.generated.expiring_soon') }}</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                        <a href="#" class="btn btn-sm btn-outline-success"><i class="bi bi-pencil"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="crew-compliance" role="tabpanel" aria-labelledby="crew-compliance-tab">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="fw-bold mb-0"><i class="bi bi-shield-check text-success me-2"></i> {{ __('owner.generated.compliance_overview') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0 text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('owner.generated.crew_member') }}</th>
                                    <th>{{ __('owner.generated.job_role') }}</th>
                                    <th>{{ __('owner.generated.docs') }}</th>
                                    <th>{{ __('owner.generated.residence_status') }}(IQAMA)</th>
                                    <th>{{ __('owner.generated.compliance_status') }}</th>
                                    <th>{{ __('owner.generated.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ __('owner.generated.ahmed_alrashed') }}</td>
                                    <td>{{ __('owner.generated.captain') }}</td>
                                    <td>
                                        <span class="badge bg-secondary mb-1">iqama</span><br>
                                        <span class="badge bg-secondary">passport</span><br>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ __('owner.generated.valid_doc') }}</span>
                                        <small class="text-muted">2025-01-15</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success">{{ __('owner.generated.compliant') }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                            <i class="bi bi-plus-circle"></i> {{ __('owner.generated.add_document') }}</button>
                                    </td>
                                </tr>

                                <tr>
                                    <td>{{ __('owner.generated.fatima_alzahra') }}</td>
                                    <td>{{ __('owner.generated.first_assistant') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">iqama</span><br>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">{{ __('owner.generated.expiring_soon') }}</span>
                                        <small class="text-muted">2025-02-01</small>

                                    </td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger">{{ __('owner.generated.non_compliant') }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                            <i class="bi bi-plus-circle"></i> {{ __('owner.generated.add_document') }}</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal: Upload Document -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="uploadDocumentModalLabel">
                    📄 {{ __('owner.generated.upload_new_document') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('owner.generated.btn_close_white') }}"></button>
            </div>

            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.crew_member_name') }}<span class="text-danger">*</span></label>
                            <select class="form-select">
                                <option selected disabled>{{ __('owner.generated.select_crew_member') }}</option>
                                <option>{{ __('owner.generated.ahmed_alrashed') }}</option>
                                <option>{{ __('owner.generated.fatima_alzahra') }}</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.document_type') }}<span class="text-danger">*</span></label>
                            <select class="form-select">
                                <option selected disabled>{{ __('owner.generated.select_type') }}</option>
                                <option>{{ __('owner.generated.iqama') }}(IQAMA)</option>
                                <option>{{ __('owner.generated.passport_doc') }}(Passport)</option>
                                <option>{{ __('owner.generated.medical_license') }}</option>
                                <option>{{ __('owner.generated.work_permit') }}</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.document_name') }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="{{ __('owner.generated.placeholder_iqama_john') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.document_number') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('owner.generated.placeholder_doc_number') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.customers.sales_table.date') }}</label>
                            <input type="date" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.expiry_date') }}</label>
                            <input type="date" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('owner.generated.upload_file') }}</label>
                            <input type="file" class="form-control">
                            <small class="text-muted d-block mt-1">{{ __('owner.generated.supported_types') }}PDF{{ __('owner.generated.item_c0cb') }}JPG{{ __('owner.generated.item_c0cb') }}PNG — {{ __('owner.generated.max_size_10') }}MB</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('owner.expenses.show.notes') }}</label>
                            <textarea class="form-control" rows="3" placeholder="{{ __('owner.generated.placeholder_more_notes') }}"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('owner.generated.upload_the_document') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
@endsection
