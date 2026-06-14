@extends('owner.layouts.master')
@section('title')
    {{ __('owner.sales.title') }} - {{ __('owner.sales.edit_sales') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/tag-it/css/jquery.tagit.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/summernote/dist/summernote-lite.css') }}" rel="stylesheet">

    <style>
        label.error {
            color: red;
            font-weight: bold;
            margin-top: 5px;
            display: block;
        }
    </style>
@endsection
@section('content')

    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('owner/sales') }}">{{ __('owner.sales.title') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.sales.edit_sales') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.sales.edit_sales') }}</h1>
        </div>
    </div>
    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">
                <form action="{{ route('owner.sales.update', $sale->id) }}" method="post" id="createForm"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="trip_id" value="{{ $sale->trip_id }}">

                    <div class="row mb-3">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="customer_id" class="form-label">{{ __('owner.catch.customer_id') }}<span
                                        class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_id" class="form-control" required>
                                    <option value="">{{ __('owner.actions.choose') }}</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ old('customer_id', $sale->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-xl-4">
                            <div class="form-group">
                                <label for="sale_datetime" class="form-label">{{ __('owner.sales.datetime') }}<span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" name="sale_datetime"
                                    value="{{ old('sale_datetime', \Illuminate\Support\Carbon::parse($sale->sale_datetime)->format('Y-m-d\TH:i')) }}"
                                    class="form-control" required>
                                @error('sale_datetime')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <h4 class="mt-5">{{ __('owner.sales.sales_details') }}</h4>
                    <div class="row mb-2 fw-bold">
                        <div class="col-md-3">{{ __('owner.sales.fish') }}</div>
                        <div class="col-md-2">{{ __('owner.sales.weight') }}</div>
                        <div class="col-md-2">{{ __('owner.sales.unit') }}</div>
                        <div class="col-md-2">{{ __('owner.sales.price_per_unit') }}</div>
                        <div class="col-md-3">{{ __('owner.sales.total_price') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-xl-12">
                            <div id="fish-wrapper">
                                @foreach ($rows as $row)
                                    <div class="row mb-2 fish-row align-items-center">
                                        <div class="col-md-3">
                                            <input type="hidden" name="fish_id[]" value="{{ $row['fish_id'] }}">
                                            <input type="hidden" name="unit_id[]" value="{{ $row['unit_id'] }}">
                                            <input type="text" class="form-control" value="{{ $row['fish_name'] }}" disabled>
                                        </div>

                                        <div class="col-md-2">
                                            <input type="number" step="0.01" min="0" max="{{ $row['available'] }}"
                                                name="weight[]" class="form-control weight"
                                                value="{{ $row['weight'] }}" placeholder="≤ {{ $row['available'] }}">
                                        </div>

                                        <div class="col-md-2">
                                            <input type="text" class="form-control" value="{{ $row['unit_name'] }}" disabled>
                                        </div>

                                        <div class="col-md-2">
                                            <input type="number" step="0.01" min="0" name="price_per_kilo[]"
                                                class="form-control price-per-kg" value="{{ $row['price'] }}"
                                                placeholder="{{ __('owner.sales.price_per_unit') }}">
                                        </div>

                                        <div class="col-md-3">
                                            <span class="fw-bold total-price-text">0.00</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3 offset-md-9 text-start">
                            <label class="form-label fw-bold">{{ __('owner.sales.total_price') }}</label>
                            <h4 class="fw-bold text-success mb-0">
                                <span id="grand_total_text">0.00</span>
                            </h4>
                        </div>
                    </div>

                    <div class="row mb-3 mt-5">

                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.sales.payment_method_id') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="payment_method_id">
                                <option value="">{{ __('owner.actions.choose') }}</option>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->id }}"
                                        {{ old('payment_method_id', $sale->payment_method_id) == $method->id ? 'selected' : '' }}>
                                        {{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('owner.catch.payment_status') }}<span
                                        class="text-danger">*</span></label>
                                <select name="payment_status" class="form-select">
                                    <option value="">{{ __('owner.actions.choose') }}</option>
                                    <option value="unpaid"
                                        {{ old('payment_status', $sale->payment_status) == 'unpaid' ? 'selected' : '' }}>
                                        {{ __('owner.catch.unpaid') }}</option>
                                    <option value="partially_paid"
                                        {{ old('payment_status', $sale->payment_status) == 'partially_paid' ? 'selected' : '' }}>
                                        {{ __('owner.catch.partially_paid') }}</option>
                                    <option value="paid"
                                        {{ old('payment_status', $sale->payment_status) == 'paid' ? 'selected' : '' }}>
                                        {{ __('owner.catch.paid') }}</option>
                                </select>
                            </div>
                        </div>

                        @php($paidAmount = $sale->payment_status === 'partially_paid' ? round($sale->total_price - $sale->remaining_total, 2) : '')
                        <div class="col-md-4 {{ old('payment_status', $sale->payment_status) === 'partially_paid' ? '' : 'd-none' }}"
                            id="paidAmountWrapper">
                            <label class="form-label">{{ __('owner.generated.amount_paid') }}</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control"
                                value="{{ old('paid_amount', $paidAmount) }}">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">{{ __('owner.actions.save') }}</button>
                        <a href="{{ route('owner.sales.index') }}"
                            class="btn btn-secondary">{{ __('owner.actions.cancel') }}</a>
                    </div>
                </form>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>

        </div>
    </div>

@endsection
@section('script')
    <script src="{{ asset('dashboard/assets/plugins/jquery-migrate/dist/jquery-migrate.min.js') }}"></script>

    <script src="{{ asset('dashboard/assets/plugins/@highlightjs/cdn-assets/highlight.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/highlightjs.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/jquery.validate.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>
    <script src="{{ asset('dashboard/assets/plugins/summernote/dist/summernote-lite.min.js') }}"></script>

    <script>
        $("#createForm").validate();
    </script>

    <script>
        function calculateGrandTotal() {
            let sum = 0;

            document.querySelectorAll('.fish-row').forEach(row => {
                const weight = parseFloat(row.querySelector('.weight')?.value) || 0;
                const price = parseFloat(row.querySelector('.price-per-kg')?.value) || 0;
                const total = weight * price;

                const cell = row.querySelector('.total-price-text');
                if (cell) {
                    cell.innerText = total.toFixed(2);
                }
                sum += total;
            });

            document.getElementById('grand_total_text').innerText = sum.toFixed(2);
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('weight') || e.target.classList.contains('price-per-kg')) {
                calculateGrandTotal();
            }
        });

        document.addEventListener('DOMContentLoaded', calculateGrandTotal);
    </script>

    <script>
        document.querySelector('[name="payment_status"]').addEventListener('change', function() {
            const wrapper = document.getElementById('paidAmountWrapper');
            if (this.value === 'partially_paid') {
                wrapper.classList.remove('d-none');
            } else {
                wrapper.classList.add('d-none');
                wrapper.querySelector('input').value = '';
            }
        });
    </script>
@endsection
