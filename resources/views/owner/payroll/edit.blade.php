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

    @php
        $totalDetails = $payroll->details->count();
        $paidDetails = $payroll->details->where('is_paid', true)->count();
    @endphp

    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ route('owner.percentage') }}">{{ __('owner.payrolls.manage_title') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.payrolls.edit.title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.payrolls.edit.page_header') }}</h1>
        </div>
        <div class="ms-auto">
            <span class="badge {{ $totalDetails > 0 && $paidDetails === $totalDetails ? 'bg-success' : 'bg-warning' }} fs-6">
                {{ __('owner.status.paid') }}: {{ $paidDetails }} / {{ $totalDetails }}
            </span>
        </div>
    </div>

    @isset($financials)
        <div class="row g-3 mb-4">
            @php
                $summaryCards = [
                    ['owner.payrolls.summary.sales', $financials['net_sales'] ?? 0, 'success'],
                    ['owner.payrolls.summary.revenue', $financials['net_owner_revenue'] ?? 0, 'info'],
                    ['owner.payrolls.summary.depreciation', $financials['depreciation'] ?? 0, 'secondary'],
                    ['owner.payrolls.summary.owner_share', $financials['owner_share'] ?? 0, 'primary'],
                    ['owner.payrolls.summary.crew_share', $financials['crew_share'] ?? 0, 'warning'],
                ];
            @endphp
            @foreach ($summaryCards as [$label, $value, $color])
                <div class="col-md">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="small text-muted mb-1">{{ __($label) }}</div>
                            <div class="h5 fw-bold text-{{ $color }} mb-0">
                                {{ number_format((float) $value, 2) }} <x-riyal-icon size="sm" />
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endisset

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


                <div class="mb-3 mt-4 border-top py-3">
                    <input type="text" id="employeeSearch" class="form-control"
                        placeholder="{{ __('owner.generated.search_employee_name') }}">
                </div>

                @php
                    $looop = 0;
                    $crewRows = ($payroll->details ?? collect())->filter(fn ($d) => $d->user && $d->user->salary_type === 'percentage' && $d->user->role !== 'captain');
                    $captainRows = ($payroll->details ?? collect())->filter(fn ($d) => $d->user && $d->user->role === 'captain' && $d->user->salary_type === 'percentage');
                @endphp

                <div class="row mb-3 mt-3 px-2">
                    <h5>{{ __('owner.payrolls.crew_payroll') }}</h5>
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>{{ __('owner.payrolls.show.employee') }}</th>
                                <th>{{ __('owner.catch.filters.boat') }}</th>
                                <th>{{ __('owner.payrolls.special_percent_col') }}</th>
                                <th>{{ __('owner.generated.increase') }}</th>
                                <th>{{ __('owner.expenses.show.notes') }}</th>
                                <th>{{ __('owner.payrolls.advances_col') }}</th>
                                <th>{{ __('owner.generated.net') }}</th>
                                <th>{{ __('owner.payrolls.payment_col') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($crewRows as $d)
                                <tr data-detail-id="{{ $d->id }}">
                                    <td>
                                        {{ $d->user->name }}
                                        <input type="hidden" name="details[{{ $looop }}][id]" value="{{ $d->id }}">
                                        <input type="hidden" name="details[{{ $looop }}][user_id]" value="{{ $d->user_id }}">
                                        <input type="hidden" class="base" value="{{ (float) $d->base_salary }}">
                                    </td>
                                    <td>{{ $d->user->boat->name ?? '' }}</td>
                                    <td>{{ $d->custom_share_percent > 0 ? number_format((float) $d->custom_share_percent, 2) . '%' : '-' }}</td>
                                    <td><input type="number" name="details[{{ $looop }}][increase]" class="form-control increase" value="{{ $d->increase }}" @readonly($d->is_paid)></td>
                                    <td><input type="text" name="details[{{ $looop }}][note]" class="form-control note" value="{{ $d->note }}" @readonly($d->is_paid)></td>
                                    <td><input type="number" class="form-control advances text-danger" value="{{ (float) $d->advances }}" readonly></td>
                                    <td><span class="text-black fw-bold net_salary">{{ $d->final_salary }}</span></td>
                                    <td class="payment-cell">
                                        @include('owner.payroll.partials.pay-cell', ['d' => $d])
                                    </td>
                                </tr>
                                @php $looop++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($captainRows->isNotEmpty())
                    <div class="row mb-3 mt-3 px-2">
                        <h5>{{ __('owner.captain.title') }}</h5>
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>{{ __('owner.payrolls.show.employee') }}</th>
                                    <th>{{ __('owner.catch.filters.boat') }}</th>
                                    <th>{{ __('owner.payrolls.special_percent_col') }}</th>
                                    <th>{{ __('owner.generated.increase') }}</th>
                                    <th>{{ __('owner.expenses.show.notes') }}</th>
                                    <th>{{ __('owner.payrolls.advances_col') }}</th>
                                    <th>{{ __('owner.generated.net') }}</th>
                                    <th>{{ __('owner.payrolls.payment_col') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($captainRows as $d)
                                    <tr data-detail-id="{{ $d->id }}">
                                        <td>
                                            {{ $d->user->name }}
                                            <input type="hidden" name="details[{{ $looop }}][id]" value="{{ $d->id }}">
                                            <input type="hidden" name="details[{{ $looop }}][user_id]" value="{{ $d->user_id }}">
                                            <input type="hidden" class="base" value="{{ (float) $d->base_salary }}">
                                        </td>
                                        <td>{{ $d->user->boat->name ?? '' }}</td>
                                        <td>{{ $d->custom_share_percent > 0 ? number_format((float) $d->custom_share_percent, 2) . '%' : '-' }}</td>
                                        <td><input type="number" name="details[{{ $looop }}][increase]" class="form-control increase" value="{{ $d->increase }}" @readonly($d->is_paid)></td>
                                        <td><input type="text" name="details[{{ $looop }}][note]" class="form-control note" value="{{ $d->note }}" @readonly($d->is_paid)></td>
                                        <td><input type="number" class="form-control advances text-danger" value="{{ (float) $d->advances }}" readonly></td>
                                        <td><span class="text-black fw-bold net_salary">{{ $d->final_salary }}</span></td>
                                        <td class="payment-cell">
                                            @include('owner.payroll.partials.pay-cell', ['d' => $d])
                                        </td>
                                    </tr>
                                    @php $looop++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <button class="btn btn-success" @if (isset($payroll) && $payroll->is_paid && $payroll->status == 'approved') disabled @endif>
                    {{ __('owner.customers.modal.buttons.save') }}</button>

                <a href="{{ route('owner.payrolls.print', $payroll->id) }}" target="_blank" class="btn btn-info">
                    {{ __('owner.generated.print') }}</a>


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
        const PAYROLL = {
            payUrl: "{{ route('owner.payrolls.payDetail', ['detail' => '__ID__']) }}",
            csrf: "{{ csrf_token() }}",
            txt: {
                confirmTitle: @json(__('owner.payrolls.pay_confirm_title')),
                confirmText: @json(__('owner.payrolls.pay_confirm_text')),
                confirmYes: @json(__('owner.payrolls.pay_confirm_yes')),
                cancel: @json(__('owner.swal.cancel')),
                paid: @json(__('owner.status.paid')),
                paidOn: @json(__('owner.payrolls.paid_on')),
                error: @json(__('owner.payrolls.pay_error')),
            },
        };

        // Live net = base (+ increase - advances) for editable rows.
        document.querySelectorAll('tbody tr').forEach(row => {
            const baseInput = row.querySelector('.base');
            const increaseInput = row.querySelector('.increase');
            const advancesInput = row.querySelector('.advances');
            const netSpan = row.querySelector('.net_salary');
            if (!baseInput || !increaseInput || !netSpan) return;

            const base = parseFloat(baseInput.value) || 0;
            const advances = parseFloat(advancesInput?.value) || 0;
            const updateNet = () => {
                const increase = parseFloat(increaseInput.value) || 0;
                netSpan.textContent = (base + increase - advances).toFixed(2);
            };

            increaseInput.addEventListener('input', updateNet);
        });

        // Per-person pay.
        $(document).on('click', '.pay-btn', function() {
            const btn = $(this);
            const row = btn.closest('tr');
            const detailId = btn.data('detail-id');

            Swal.fire({
                title: PAYROLL.txt.confirmTitle,
                text: PAYROLL.txt.confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: PAYROLL.txt.confirmYes,
                cancelButtonText: PAYROLL.txt.cancel,
            }).then((result) => {
                if (!result.isConfirmed) return;

                btn.prop('disabled', true);
                $.ajax({
                    url: PAYROLL.payUrl.replace('__ID__', detailId),
                    type: 'POST',
                    data: {
                        _token: PAYROLL.csrf,
                        increase: row.find('.increase').val() || 0,
                        note: row.find('.note').val() || '',
                    },
                    success: function(res) {
                        row.find('.increase, .note').prop('readonly', true);
                        row.find('.net_salary').text(res.final_salary);
                        btn.closest('.payment-cell').html(
                            '<span class="badge bg-success">' + PAYROLL.txt.paid + '</span>' +
                            '<div class="small text-muted">' + res.paid_at + '</div>'
                        );
                        if (window.toastr) {
                            toastr.success(res.message);
                        }
                        updatePaidCounter();
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false);
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) || PAYROLL.txt.error;
                        if (window.toastr) {
                            toastr.error(msg);
                        } else {
                            alert(msg);
                        }
                    },
                });
            });
        });

        function updatePaidCounter() {
            const total = document.querySelectorAll('tbody tr[data-detail-id]').length;
            const paid = document.querySelectorAll('tbody tr[data-detail-id] .payment-cell .badge').length;
            const badge = document.querySelector('.page-header').closest('.d-flex').querySelector('.badge');
            if (badge) {
                badge.textContent = PAYROLL.txt.paid + ': ' + paid + ' / ' + total;
                badge.classList.toggle('bg-success', total > 0 && paid === total);
                badge.classList.toggle('bg-warning', !(total > 0 && paid === total));
            }
        }

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
    </script>
@endsection
