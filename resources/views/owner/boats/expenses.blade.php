@extends('owner.layouts.master')

@section('title', '{{ __('owner.generated.item_0646af') }} - {{ __('owner.generated.boat_abu_salem') }}')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">🧾 {{ __('owner.payrolls.manage_title') }}- {{ __('owner.generated.boat_abu_salem') }}</h4>
            <p class="text-muted mb-0">{{ __('owner.generated.monthly_summary_fishermen_payroll') }}</p>
        </div>
        <div>
            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#payrollModal">
                <i class="bi bi-plus-circle me-1"></i> {{ __('owner.generated.add_new_period') }}</a>
        </div>
    </div>

    {{-- البطاقات الإحصائية --}}
    <div class="row g-3 mb-4 text-white">
        <div class="col-md-3 col-sm-6">
            <div class="card bg-dark shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-white"><i class="bi bi-people-fill me-2"></i>{{ __('owner.generated.fishermen_count') }}</h6>
                    <h4 class="text-white fw-bold">4</h4>
                    <small class="text-white">{{ __('owner.generated.in_this_boat') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-success shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-white"><i class="bi bi-cash-stack me-2"></i>{{ __('owner.generated.total_paid') }}</h6>
                    <h4 class="text-white fw-bold">SAR 24,000</h4>
                    <small class="text-white">{{ __('owner.generated.during_this_year') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-warning shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-white"><i class="bi bi-hourglass-split me-2"></i>{{ __('owner.generated.partially_paid') }}</h6>
                    <h4 class="text-white fw-bold">SAR 3,000</h4>
                    <small class="text-white">{{ __('owner.generated.waiting_completion') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-info shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-white"><i class="bi bi-person-lines-fill me-2"></i>{{ __('owner.generated.average_per_fisherman') }}</h6>
                    <h4 class="text-white fw-bold">SAR 6,000</h4>
                    <small class="text-white">{{ __('owner.generated.per_period') }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول الملخص الشهري --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📋 {{ __('owner.generated.payroll_summary_by_month') }}</h5>
            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i> {{ __('owner.dalal_stock_report.print') }}</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('owner.payrolls.show.period') }}</th>
                            <th>{{ __('owner.generated.trips_count') }}</th>
                            <th>{{ __('owner.payrolls.table.total_revenues') }}</th>
                            <th>{{ __('owner.generated.crew_share_percentage') }}</th>
                            <th>{{ __('owner.generated.fishermen_count') }}</th>
                            <th>{{ __('owner.generated.total_share') }}</th>
                            <th>{{ __('owner.generated.paid_amount') }}</th>
                            <th>{{ __('owner.assets.status') }}</th>
                            <th>{{ __('owner.generated.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ __('owner.generated.july_2025') }}</td>
                            <td>12</td>
                            <td>SAR 40,000</td>
                            <td>60%</td>
                            <td>4</td>
                            <td>SAR 24,000</td>
                            <td>SAR 21,000</td>
                            <td><span class="badge bg-warning text-dark">{{ __('owner.generated.partially_paid') }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#salaryModalJuly">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="#" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('owner.generated.june_2025') }}</td>
                            <td>10</td>
                            <td>SAR 30,000</td>
                            <td>50%</td>
                            <td>4</td>
                            <td>SAR 15,000</td>
                            <td>SAR 15,000</td>
                            <td><span class="badge bg-success">{{ __('owner.status.paid') }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#salaryModalJuly">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="#" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal: {{ __('owner.generated.item_ba41f6') }} -->
<div class="modal fade" id="payrollModal" tabindex="-1" aria-labelledby="payrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="payrollModalLabel">
                    <i class="bi bi-cash-stack me-2"></i>{{ __('owner.generated.create_new_payroll') }}- {{ __('owner.generated.boat_abu_salem') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="" method="POST">
                @csrf
                <div class="modal-body">

                    {{-- الفترة --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.start_date') }}</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.end_date') }}</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>

                    {{-- بيانات عامة --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.generated.trips_count') }}</label>
                            <input type="number" name="trips_count" class="form-control" required min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.reports.total_revenue') }}({{ __('owner.generated.in_sar') }})</label>
                            <input type="number" name="revenue" class="form-control" required min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.generated.crew_distribution_percentage') }}(%)</label>
                            <input type="number" name="crew_share" class="form-control" required min="0" max="100" value="60">
                        </div>
                    </div>

                    {{-- الصيادين --}}
                    <div class="border rounded p-3 mb-3">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-person-fill me-2 text-secondary"></i>
                            {{ __('owner.generated.ahmed_alrashed') }}- {{ __('owner.generated.captain') }}<small class="text-muted">• SAR 25/{{ __('owner.generated.hour') }}• {{ __('owner.generated.percentage_25') }}%</small>
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.generated.working_hours') }}</label>
                                <input type="number" name="fishermen[0][hours]" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.generated.bonuses') }}</label>
                                <input type="number" name="fishermen[0][bonus]" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.generated.deductions') }}</label>
                                <input type="number" name="fishermen[0][deduction]" class="form-control" value="0" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="border rounded p-3 mb-3">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-person-fill me-2 text-secondary"></i>
                            {{ __('owner.generated.fatima_alzahra') }}- {{ __('owner.generated.first_assistant') }}<small class="text-muted">• SAR 20/{{ __('owner.generated.hour') }}• {{ __('owner.generated.percentage_20') }}%</small>
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.generated.working_hours') }}</label>
                                <input type="number" name="fishermen[1][hours]" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.generated.bonuses') }}</label>
                                <input type="number" name="fishermen[1][bonus]" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.generated.deductions') }}</label>
                                <input type="number" name="fishermen[1][deduction]" class="form-control" value="0" min="0">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save2 me-1"></i> {{ __('owner.generated.save_payroll') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal: {{ __('owner.generated.item_a8ad71') }} -->
<div class="modal fade" id="salaryModalJuly" tabindex="-1" aria-labelledby="salaryModalJulyLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salaryModalJulyLabel">{{ __('owner.payrolls.show.details_title') }}- {{ __('owner.generated.july_2025') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('owner.generated.btn_close_modal') }}"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('owner.generated.fisherman_name') }}</th>
                            <th>{{ __('owner.generated.fisherman_percentage') }}</th>
                            <th>{{ __('owner.generated.due_amount') }}</th>
                            <th>{{ __('owner.generated.paid_amount') }}</th>
                            <th>{{ __('owner.generated.remaining') }}</th>
                            <th>{{ __('owner.assets.status') }}</th>
                            <th>{{ __('owner.generated.payment_batch') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ __('owner.generated.mohammed') }}</td>
                            <td>25%</td>
                            <td>SAR 6,000</td>
                            <td>SAR 6,000</td>
                            <td>SAR 0</td>
                            <td><span class="badge bg-success">{{ __('owner.status.paid') }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#paymentModalMohamed">
                                    <i class="bi bi-cash-coin"></i> {{ __('owner.generated.payment_batch') }}</button>
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('owner.generated.ahmed') }}</td>
                            <td>25%</td>
                            <td>SAR 6,000</td>
                            <td>SAR 5,000</td>
                            <td>SAR 1,000</td>
                            <td><span class="badge bg-warning text-dark">{{ __('owner.generated.partially_paid') }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#paymentModalMohamed">
                                    <i class="bi bi-cash-coin"></i> {{ __('owner.generated.payment_batch') }}</button>
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('owner.generated.salem') }}</td>
                            <td>25%</td>
                            <td>SAR 6,000</td>
                            <td>SAR 5,000</td>
                            <td>SAR 1,000</td>
                            <td><span class="badge bg-warning text-dark">{{ __('owner.generated.partially_paid') }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#paymentModalMohamed">
                                    <i class="bi bi-cash-coin"></i> {{ __('owner.generated.payment_batch') }}</button>
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('owner.generated.ali') }}</td>
                            <td>25%</td>
                            <td>SAR 6,000</td>
                            <td>SAR 5,000</td>
                            <td>SAR 1,000</td>
                            <td><span class="badge bg-warning text-dark">{{ __('owner.generated.partially_paid') }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#paymentModalMohamed">
                                    <i class="bi bi-cash-coin"></i> {{ __('owner.generated.payment_batch') }}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('owner.boats.close') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal {{ __('owner.generated.item_3bcbdf') }} -->
<div class="modal fade" id="paymentModalMohamed" tabindex="-1" aria-labelledby="paymentModalMohamedLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form action="#" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalMohamedLabel">{{ __('owner.generated.add_payment') }}- {{ __('owner.generated.mohammed') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('owner.generated.btn_close_modal') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amountMohamed" class="form-label">{{ __('owner.dalal.payments.amount') }}</label>
                        <input type="number" name="amount" id="amountMohamed" class="form-control" placeholder="{{ __('owner.generated.input_amount_mohammed') }}" required>
                    </div>
                    <input type="hidden" name="fisherman_id" value="1">
                    <input type="hidden" name="period" value="2025-07">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('owner.customers.modal.buttons.save') }}</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
