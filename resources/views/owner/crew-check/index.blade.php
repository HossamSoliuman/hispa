@extends('owner.layouts.master')

@section('title', '{{ __('owner.generated.crew_check') }}')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">✔️ {{ __('owner.generated.crew_check') }}</h4>
            <p class="text-muted mb-0">{{ __('owner.generated.monitor_crew_status_compliance') }}</p>
        </div>
        <div>
            <button class="btn btn-outline-secondary me-2"><i class="bi bi-printer"></i> {{ __('owner.generated.print_report') }}</button>
            <button class="btn btn-outline-success"><i class="bi bi-download"></i> {{ __('owner.generated.export') }}</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col">
            <div class="card bg-dark shadow-sm border-0 text-white">
                <div class="card-body text-center">
                    <h6 class="text-white"><i class="bi bi-people-fill me-2"></i>{{ __('owner.payrolls.show.crew_total') }}</h6>
                    <h4 class="fw-bold text-white">2</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success shadow-sm border-0 text-white">
                <div class="card-body text-center">
                    <h6 class="text-white"><i class="bi bi-check-circle-fill me-2"></i>{{ __('owner.generated.available') }}</h6>
                    <h4 class="fw-bold text-white">1</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-primary shadow-sm border-0 text-white">
                <div class="card-body text-center">
                    <h6 class="text-white"><i class="bi bi-airplane-fill me-2"></i>{{ __('owner.generated.on_trip') }}</h6>
                    <h4 class="fw-bold text-white">1</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info shadow-sm border-0 text-white">
                <div class="card-body text-center">
                    <h6 class="text-white"><i class="bi bi-check2-square me-2"></i>{{ __('owner.generated.ready') }}</h6>
                    <h4 class="fw-bold text-white">0</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-danger shadow-sm border-0 text-white">
                <div class="card-body text-center">
                    <h6 class="text-white"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('owner.generated.issues') }}</h6>
                    <h4 class="fw-bold text-white">2</h4>
                </div>
            </div>
        </div>
    </div>


    <h5 class="fw-bold text-dark mb-2 mt-4">🔍 {{ __('owner.generated.filter_and_search') }}</h5>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">{{ __('owner.generated.search_crew_members') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('owner.generated.placeholder_name_role_phone') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark">{{ __('owner.assets.status') }}</label>
                    <select class="form-select">
                        <option selected>{{ __('owner.expenses.filters.all_statuses') }}</option>
                        <option>{{ __('owner.generated.available') }}</option>
                        <option>{{ __('owner.generated.on_trip') }}</option>
                        <option>{{ __('owner.generated.ready') }}</option>
                        <option>{{ __('owner.generated.issues') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark">{{ __('owner.generated.compliance') }}</label>
                    <select class="form-select">
                        <option selected>{{ __('owner.generated.good') }}</option>
                        <option>{{ __('owner.generated.warning') }}</option>
                        <option>{{ __('owner.generated.critical') }}</option>
                    </select>
                </div>

            </div>
        </div>
    </div>


    <div class="container-fluid mt-4">

        <ul class="nav nav-tabs mb-4" id="crewTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">{{ __('owner.boats.show.overview') }}</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="status-tab" data-bs-toggle="tab" data-bs-target="#status" type="button" role="tab">{{ __('owner.generated.status_dashboard') }}</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="compliance-tab" data-bs-toggle="tab" data-bs-target="#compliance" type="button" role="tab">{{ __('owner.generated.compliance') }}</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">{{ __('owner.generated.details') }}</button>
            </li>
        </ul>

        <div class="tab-content" id="crewTabsContent">

            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row">
                    {{-- البطاقة الأولى --}}
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1">{{ __('owner.generated.ahmed_alrashed') }}</h5>
                                        <p class="text-muted mb-0 small">{{ __('owner.generated.captain') }}</p>
                                        <span class="badge bg-success-subtle text-success small">{{ __('owner.generated.available') }}</span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">{{ __('owner.generated.last_login') }}</small>
                                        <div class="fw-medium">{{ __('owner.generated.ago_8h_42m') }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">{{ __('owner.generated.current_vessel') }}</small>
                                        <div class="fw-medium">{{ __('owner.generated.sea_1') }}</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">{{ __('owner.generated.compliance') }}</small>
                                        <small class="fw-medium">0%</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-danger me-2">●</span>
                                    <span class="text-danger fw-medium small">{{ __('owner.generated.not_ready') }}</span>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal" data-name="{{ __('owner.generated.ahmed_alrashed') }}">
                                        <i class="fas fa-user-check me-2"></i>
                                        {{ __('owner.generated.verification') }}</button>
                                    <button class="btn btn-outline-secondary">
                                        <i class="fas fa-info-circle"></i>
                                        {{ __('owner.generated.details') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- البطاقة الثانية --}}
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1">{{ __('owner.generated.fatima_alzahra') }}</h5>
                                        <p class="text-muted mb-0 small">{{ __('owner.generated.first_assistant') }}</p>
                                        <span class="badge bg-primary-subtle text-primary small">{{ __('owner.generated.on_trip') }}</span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">{{ __('owner.generated.last_login') }}</small>
                                        <div class="fw-medium">{{ __('owner.generated.ago_10h_42m') }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">{{ __('owner.generated.current_vessel') }}</small>
                                        <div class="fw-medium">{{ __('owner.generated.sea_2') }}</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">{{ __('owner.generated.compliance') }}</small>
                                        <small class="fw-medium">0%</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-danger me-2">●</span>
                                    <span class="text-danger fw-medium small">{{ __('owner.generated.not_ready') }}</span>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal" data-name="{{ __('owner.generated.fatima_alzahra') }}">
                                        <i class="fas fa-user-check me-2"></i>
                                        {{ __('owner.generated.verification') }}</button>
                                    <button class="btn btn-outline-secondary">
                                        <i class="fas fa-info-circle"></i>
                                        {{ __('owner.generated.details') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="status" role="tabpanel">
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-0">📋 {{ __('owner.generated.crew_status_board') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0 text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('owner.menu.crew') }}</th>
                                        <th>{{ __('owner.employee.table.nationality') }}</th>
                                        <th>{{ __('owner.crew.table.job_title') }}</th>
                                        <th>{{ __('owner.assets.status') }}</th>
                                        <th>{{ __('owner.catch.filters.boat') }}</th>
                                        <th>{{ __('owner.generated.last_login') }}</th>
                                        <th>{{ __('owner.generated.readiness') }}</th>
                                        <th>{{ __('owner.generated.communication') }}</th>
                                        <th>{{ __('owner.generated.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ __('owner.generated.ahmed_alrashed') }}</td>
                                        <td>{{ __('owner.generated.saudi') }}</td>
                                        <td>{{ __('owner.generated.captain') }}</td>
                                        <td><span class="badge bg-success">{{ __('owner.generated.available') }}</span></td>
                                        <td>{{ __('owner.generated.sea_1') }}</td>
                                        <td>{{ __('owner.generated.since_9h_36m') }}</td>
                                        <td><span class="text-danger">{{ __('owner.generated.not_ready') }}</span></td>
                                        <td><a href="#" class="text-decoration-none"><i class="bi bi-envelope me-1"></i>{{ __('owner.actions.send') }}</a></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal" data-name="{{ __('owner.generated.ahmed_alrashed') }}">
                                                <i class="fas fa-user-check me-1"></i> {{ __('owner.generated.verification') }}</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('owner.generated.fatima_alzahra') }}</td>
                                        <td>{{ __('owner.generated.saudi_female') }}</td>
                                        <td>{{ __('owner.generated.first_assistant') }}</td>
                                        <td><span class="badge bg-primary">{{ __('owner.generated.on_trip') }}</span></td>
                                        <td>{{ __('owner.generated.sea_2') }}</td>
                                        <td>{{ __('owner.generated.since_11h_36m') }}</td>
                                        <td><span class="text-danger">{{ __('owner.generated.not_ready') }}</span></td>
                                        <td><a href="#" class="text-decoration-none"><i class="bi bi-envelope me-1"></i>{{ __('owner.actions.send') }}</a></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#checkInModal" data-name="{{ __('owner.generated.fatima_alzahra') }}">
                                                <i class="fas fa-user-check me-1"></i> {{ __('owner.generated.verification') }}</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="compliance" role="tabpanel">
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-0">📑 {{ __('owner.generated.docs_compliance_overview') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0 text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('owner.menu.crew') }}</th>
                                        <th>{{ __('owner.generated.residence_permit') }}</th>
                                        <th>{{ __('owner.generated.passport_1') }}</th>
                                        <th>{{ __('owner.generated.medical_check') }}</th>
                                        <th>{{ __('owner.generated.license') }}</th>
                                        <th>{{ __('owner.generated.general_status') }}</th>
                                        <th>{{ __('owner.assets.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ __('owner.generated.ahmed_alrashed') }}</td>
                                        <td><span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>{{ __('owner.generated.missing') }}</span></td>
                                        <td><span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>{{ __('owner.generated.missing') }}</span></td>
                                        <td><span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>{{ __('owner.generated.valid') }}</span></td>
                                        <td><span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>{{ __('owner.generated.valid') }}</span></td>
                                        <td><span class="badge bg-danger">{{ __('owner.generated.critical') }}</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil me-1"></i>{{ __('owner.profit_loss.update') }}</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('owner.generated.fatima_alzahra') }}</td>
                                        <td><span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>{{ __('owner.generated.missing') }}</span></td>
                                        <td><span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>{{ __('owner.generated.missing') }}</span></td>
                                        <td><span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>{{ __('owner.generated.valid') }}</span></td>
                                        <td><span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>{{ __('owner.generated.valid') }}</span></td>
                                        <td><span class="badge bg-danger">{{ __('owner.generated.critical') }}</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil me-1"></i>{{ __('owner.profit_loss.update') }}</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="details" role="tabpanel">
                <div class="row g-4 mt-3">

                    <!-- Ahmed Al-Rashid -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-1 h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-2">🧑‍✈️ {{ __('owner.generated.ahmed_alrashed') }}</h5>
                                <p class="text-muted">{{ __('owner.generated.captain') }}</p>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <h6 class="text-muted">📞 {{ __('owner.crew.edit.phone') }}</h6>
                                        <p class="mb-0">+966501234567</p>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted">📧 {{ __('owner.dalal.modal.form.email') }}</h6>
                                        <p class="mb-0">ahmed.rashid@fishhouse.sa</p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <h6 class="text-muted">🌍 {{ __('owner.employee.table.nationality') }}</h6>
                                        <p class="mb-0">{{ __('owner.generated.saudi') }}</p>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted">📅 {{ __('owner.generated.employment_date') }}</h6>
                                        <p class="mb-0">2023-01-15</p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <h6 class="text-muted">💰 {{ __('owner.generated.base_wage') }}</h6>
                                        <p class="mb-0">{{ __('owner.generated.amount_25_sar') }}/{{ __('owner.generated.hour') }}</p>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted">📊 {{ __('owner.generated.participation_share') }}</h6>
                                        <p class="mb-0">25%</p>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="fw-bold mb-2">📟 {{ __('owner.generated.emergency_contact') }}</h6>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="mb-0">{{ __('owner.generated.name') }}<span class="text-muted">{{ __('owner.generated.not_available') }}</span></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-0">{{ __('owner.generated.phone') }}<span class="text-muted">{{ __('owner.generated.not_available') }}</span></p>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="fw-bold mb-2">🏦 {{ __('owner.generated.banking_info') }}</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-0">{{ __('owner.generated.bank') }}<span class="text-muted">{{ __('owner.generated.not_available') }}</span></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-0">{{ __('owner.generated.account') }}<span class="text-muted">{{ __('owner.generated.not_available') }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fatima Al-Zahra -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-1 h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-2">👩‍✈️ {{ __('owner.generated.fatima_alzahra') }}</h5>
                                <p class="text-muted">{{ __('owner.generated.first_assistant') }}</p>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <h6 class="text-muted">📞 {{ __('owner.crew.edit.phone') }}</h6>
                                        <p class="mb-0">+966507654321</p>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted">📧 {{ __('owner.dalal.modal.form.email') }}</h6>
                                        <p class="mb-0">fatima.zahra@fishhouse.sa</p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <h6 class="text-muted">🌍 {{ __('owner.employee.table.nationality') }}</h6>
                                        <p class="mb-0">{{ __('owner.generated.saudi_female') }}</p>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted">📅 {{ __('owner.generated.employment_date') }}</h6>
                                        <p class="mb-0">2023-03-20</p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <h6 class="text-muted">💰 {{ __('owner.generated.base_wage') }}</h6>
                                        <p class="mb-0">{{ __('owner.generated.amount_20_sar') }}/{{ __('owner.generated.hour') }}</p>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted">📊 {{ __('owner.generated.participation_share') }}</h6>
                                        <p class="mb-0">20%</p>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="fw-bold mb-2">📟 {{ __('owner.generated.emergency_contact') }}</h6>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="mb-0">{{ __('owner.generated.name') }}<span class="text-muted">{{ __('owner.generated.not_available') }}</span></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-0">{{ __('owner.generated.phone') }}<span class="text-muted">{{ __('owner.generated.not_available') }}</span></p>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="fw-bold mb-2">🏦 {{ __('owner.generated.banking_info') }}</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-0">{{ __('owner.generated.bank') }}<span class="text-muted">{{ __('owner.generated.not_available') }}</span></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-0">{{ __('owner.generated.account') }}<span class="text-muted">{{ __('owner.generated.not_available') }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

<div class="modal fade" id="checkInModal" tabindex="-1" aria-labelledby="checkInModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white " id="checkInModalLabel">{{ __('owner.generated.update_crew_status') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('owner.generated.btn_close_white') }}"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">{{ __('owner.assets.name') }}</label>
                        <input type="text" id="crewName" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('owner.assets.status') }}</label>
                        <select class="form-select">
                            <option>{{ __('owner.generated.available') }}</option>
                            <option>{{ __('owner.generated.on_trip') }}</option>
                            <option>{{ __('owner.generated.ready') }}</option>
                            <option>{{ __('owner.generated.issues') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('owner.catch.filters.boat') }}({{ __('owner.generated.if_on_trip') }})</label>
                        <select class="form-select">
                            <option>{{ __('owner.generated.sea_1') }}</option>
                            <option>{{ __('owner.generated.sea_2') }}</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('owner.boats.close') }}</button>
                <button type="button" class="btn btn-primary">{{ __('owner.generated.confirm_verification') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    const checkInModal = document.getElementById('checkInModal');
    checkInModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const name = button.getAttribute('data-name');
        document.getElementById('crewName').value = name;
    });
</script>
@endsection
