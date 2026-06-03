@extends('owner.layouts.master')

@section('title', __('owner.payrolls.edit.title'))

@section('css')
    <style>
        label.error {
            color: #d9534f;
            font-weight: bold;
        }

        a {
            cursor: pointer !important;
        }
    </style>
@endsection

@section('content')

    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ route('owner.payrolls.index') }}">{{ __('owner.payrolls.manage_title') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.payrolls.edit.title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.payrolls.edit.page_header') }}</h1>
        </div>
    </div>

    <div class="card form-section">
        <div class="card-body">

            <form method="POST" action="{{ route('owner.payrolls.update', $payroll->id ?? 0) }}">
                @csrf
                @if (isset($payroll))
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <input type="hidden" name="id" value="{{ $payroll->id }}">
                    <div class="col">
                        <label>{{ __('owner.generated.the_year') }}</label>
                        <input type="number" name="year" value="{{ old('year', $payroll->year ?? $year) }}"
                            class="form-control" readonly>
                    </div>
                    <div class="col">
                        <label>{{ __('owner.generated.month') }}</label>
                        <input type="number" name="month" value="{{ old('month', $payroll->month ?? $month) }}"
                            class="form-control" readonly>
                    </div>
                    <div class="col">
                        <label>{{ __('owner.assets.status') }}</label>
                        <select name="status" class="form-control form-select"
                            @if (isset($payroll) && $payroll->is_paid && $payroll->status == 'approved') disabled @endif>
                            <option value="draft" {{ isset($payroll) && $payroll->status == 'draft' ? 'selected' : '' }}>
                                {{ __('owner.generated.draft') }}</option>
                            <option value="approved"
                                {{ isset($payroll) && $payroll->status == 'approved' ? 'selected' : '' }}>
                                {{ __('owner.generated.approved') }}</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label>{{ __('owner.sales.payment_status') }}</label>
                        <select name="is_paid" id="is_paid" class="form-control form-select"
                            @if (isset($payroll) && $payroll->status == 'approved' && $payroll->is_paid) disabled @endif>
                            <option value="0" {{ isset($payroll) && !$payroll->is_paid ? 'selected' : '' }}>
                                {{ __('owner.sales.unpaid') }}</option>
                            <option value="1" {{ isset($payroll) && $payroll->is_paid ? 'selected' : '' }}>
                                {{ __('owner.status.paid') }}</option>
                        </select>
                    </div>
                    <div class="col">
                        <label>{{ __('owner.generated.payment_date') }}</label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control"
                            value="{{ old('payment_date', $payroll->payment_date ?? '') }}"
                            @if (isset($payroll) && $payroll->is_paid && $payroll->status == 'approved') readonly @endif>
                    </div>
                </div>


                <div class="mb-3 mt-4 border-top py-3">
                    <input type="text" id="employeeSearch" class="form-control"
                        placeholder="{{ __('owner.generated.search_employee_name') }}">
                </div>

                @php
                    $looop = 0;
                @endphp

                <ul class="nav nav-tabs mb-3">
                    @if ($payroll->type == 'salary')
                        <li class="nav-item">
                            <a class="nav-link active"
                                data-tab="fixed">{{ __('owner.generated.fixed_salary_employees') }}</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link active"
                                data-tab="percentage">{{ __('owner.generated.commission_employees') }}</a>
                        </li>
                    @endif
                </ul>
                @if ($payroll->type == 'salary')
                    <div id="fixed" class="tab-content">
                        <div class="row mb-3 mt-3  px-2">
                            <h5>{{ __('owner.generated.fixed_salary_employees') }}</h5>
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>{{ __('owner.payrolls.show.employee') }}</th>
                                        <th>{{ __('owner.generated.basic_salary') }}</th>
                                        <th>{{ __('owner.generated.increase') }}</th>
                                        <th>{{ __('owner.generated.deduction') }}</th>
                                        <th>{{ __('owner.expenses.show.notes') }}</th>
                                        <th>{{ __('owner.generated.net') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payroll->details ?? [] as $i => $d)
                                        @if ($d->user->salary_type == 'salary')
                                            <tr>
                                                <td>
                                                    {{ $d->user->name }}
                                                    <input type="hidden" name="details[{{ $looop }}][id]"
                                                        value="{{ $d->id }}">
                                                    <input type="hidden" name="details[{{ $looop }}][user_id]"
                                                        value="{{ $d->user_id }}">
                                                </td>
                                                <input type="hidden" class="form-control base"
                                                    value="{{ $d->base_salary }}" readonly>
                                                <td>{{ $d->base_salary }}</td>
                                                <td><input type="number" name="details[{{ $looop }}][increase]"
                                                        class="form-control increase" value="{{ $d->increase }}"></td>
                                                <td><input type="number" name="details[{{ $looop }}][deduction]"
                                                        class="form-control deduction" value="{{ $d->deduction }}"></td>
                                                <td><input type="text" name="details[{{ $looop }}][note]"
                                                        class="form-control" value="{{ $d->note }}"></td>
                                                <td>
                                                    {{-- <input type="text" class="form-control net_salary" value="{{ $d->final_salary }}" readonly> --}}
                                                    <span class="text-black fw-bold net_salary">
                                                        {{ $d->final_salary }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @php
                                                $looop++;
                                            @endphp
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div id="percentage" class="tab-content">
                        <div class="row mb-3 mt-3 px-2">
                            <h5>{{ __('owner.generated.commission_employees') }}</h5>
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>{{ __('owner.payrolls.show.employee') }}</th>
                                        <th>{{ __('owner.catch.filters.boat') }}</th>
                                        <th>{{ __('owner.generated.total_fishermen_profits') }}</th>
                                        <th>{{ __('owner.generated.fishermen_count') }}</th>
                                        <th>{{ __('owner.generated.fisherman_percentage') }}</th>
                                        <th>{{ __('owner.generated.increase') }}</th>
                                        <th>{{ __('owner.generated.deduction') }}</th>
                                        <th>{{ __('owner.expenses.show.notes') }}</th>
                                        <th>{{ __('owner.generated.net') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payroll->details ?? [] as $i => $d)
                                        @if ($d->user->salary_type == 'percentage')
                                            <tr>
                                                <td>
                                                    {{ $d->user->name }}
                                                    <input type="hidden" name="details[{{ $looop }}][id]"
                                                        value="{{ $d->id }}">
                                                    <input type="hidden" name="details[{{ $looop }}][user_id]"
                                                        value="{{ $d->user_id }}">
                                                </td>
                                                <td>{{ $d->user->boat->name ?? '' }}</td>
                                                <input type="hidden" class="form-control base"
                                                    value="{{ $d->sales_amount }}" readonly>
                                                <td>{{ (float) $d->captins_amount }}</td>
                                                <td>{{ (int) $d->captins_count }}</td>
                                                <td>{{ round((float) $d->captins_amount / (int) $d->captins_count, 2) }}
                                                </td>
                                                {{-- <td>{{ $d->percentage }}</td>                                   --}}
                                                <td><input type="number" name="details[{{ $looop }}][increase]"
                                                        class="form-control increase" value="{{ $d->increase }}"></td>
                                                <td><input type="number" name="details[{{ $looop }}][deduction]"
                                                        class="form-control deduction" value="{{ $d->deduction }}"></td>
                                                <td><input type="text" name="details[{{ $looop }}][note]"
                                                        class="form-control" value="{{ $d->note }}"></td>
                                                <td>
                                                    {{-- <input type="text" class="form-control net_salary" value="{{ $d->final_salary }}" readonly> --}}
                                                    <span class="text-black fw-bold net_salary">
                                                        {{ $d->final_salary }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @php
                                                $looop++;
                                            @endphp
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif







                <button class="btn btn-success" @if (isset($payroll) && $payroll->is_paid && $payroll->status == 'approved') disabled @endif>
                    {{ __('owner.customers.modal.buttons.save') }}</button>

                <button class="btn btn-danger" disabled>
                    {{ __('owner.generated.pay') }}</button>

                <button class="btn btn-info" disabled>
                    {{ __('owner.generated.print') }}</button>


            </form>


        </div>
        <div class="card-arrow">
            <div class="card-arrow-top-left"></div>
            <div class="card-arrow-top-right"></div>
            <div class="card-arrow-bottom-left"></div>
            <div class="card-arrow-bottom-right"></div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#editPayrollForm').on('submit', function(e) {
                // Form validation handled server-side
            });
        });

        document.querySelectorAll('tbody tr').forEach(row => {
            const base = parseFloat(row.querySelector('.base').value) || 0;
            const increaseInput = row.querySelector('.increase');
            const deductionInput = row.querySelector('.deduction');
            const netSpan = row.querySelector('.net_salary');

            const updateNet = () => {
                const increase = parseFloat(increaseInput.value) || 0;
                const deduction = parseFloat(deductionInput.value) || 0;
                netSpan.textContent = base + increase - deduction;
            };

            increaseInput.addEventListener('input', updateNet);
            deductionInput.addEventListener('input', updateNet);
        });
        document.addEventListener('DOMContentLoaded', function() {
            const isPaidSelect = document.getElementById('is_paid');
            const paymentDate = document.getElementById('payment_date');

            const togglePaymentDate = () => {
                if (isPaidSelect.value == '1') {
                    paymentDate.removeAttribute('readonly');
                    if (!paymentDate.value) {
                        const today = new Date().toISOString().split('T')[0];
                        paymentDate.value = today;
                    }
                } else {
                    paymentDate.value = '';
                    paymentDate.setAttribute('readonly', true);
                }
            };

            isPaidSelect.addEventListener('change', togglePaymentDate);
            togglePaymentDate(); // initialize on load
        });


        const searchInput = document.getElementById('employeeSearch');

        searchInput.addEventListener('input', function() {
            const value = this.value.toLowerCase();

            document.querySelectorAll('tbody tr').forEach(row => {
                const nameCell = row.querySelector('td');
                if (!nameCell) return;

                const name = nameCell.textContent.toLowerCase();
                row.style.display = name.includes(value) ? '' : 'none';
            });
        });

        document.querySelectorAll('[data-tab]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.nav-link').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.add('d-none'));

                btn.classList.add('active');
                document.getElementById(btn.dataset.tab).classList.remove('d-none');
            });
        });
    </script>
@endsection
