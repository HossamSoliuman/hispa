@extends('owner.layouts.master')
@section('title')
    {{__('owner.sales.title')}} - {{__('owner.sales.add_sales')}}
@endsection
@section('css')
    <link href="{{asset('dashboard/assets/plugins/tag-it/css/jquery.tagit.css')}}" rel="stylesheet">
    <link href="{{asset('dashboard/assets/plugins/summernote/dist/summernote-lite.css')}}" rel="stylesheet">

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
                <li class="breadcrumb-item"><a href="{{ url('owner/sales') }}">{{__('owner.sales.title')}}</a></li>
                <li class="breadcrumb-item active">{{__('owner.sales.add_sales')}}</li>
            </ul>
            <h1 class="page-header mb-0">{{__('owner.sales.add_sales')}}</h1>
        </div>
    </div>
    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">
                <form action="{{ route('owner.sales.store') }}" method="post" id="createForm" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="customer_id" class="form-label">{{ __('owner.catch.customer_id') }}<span class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_id" class="form-control" required>
                                    <option value="">{{ __('owner.actions.choose') }}</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-xl-4">
                            <div class="form-group">
                                <label for="sale_datetime" class="form-label">{{ __('owner.sales.datetime') }}<span class="text-danger">*</span></label>
                                <input type="datetime-local" name="sale_datetime" value="{{ old('sale_datetime', now()->format('Y-m-d\TH:i')) }}" class="form-control" required>
                                @error('sale_datetime') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-xl-4">
                            <div class="form-group">
                                <label for="trip_id" class="form-label">{{ __('owner.trips.name') }} <span class="text-danger">*</span></label>
                                <select name="trip_id" id="trip_id" class="form-select">
                                    <option value="">{{ __('owner.actions.choose') }}</option>
                                    @foreach($trips as $trip)
                                        <option @if(request()->has('trip_id') && request()->get('trip_id') == $trip->id) selected="selected"  @endif value="{{ $trip->id }}">
                                            {{ $trip->name }}
                                        </option>
                                    @endforeach
                                </select>
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
                            <div id="fish-wrapper"></div>
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
                            <label class="form-label">{{ __('owner.sales.payment_method_id') }}<span class="text-danger">*</span></label>
                            <select class="form-select" name="payment_method_id">
                                <option value="">{{ __('owner.actions.choose') }}</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('owner.catch.payment_status') }}<span class="text-danger">*</span></label>
                                <select name="payment_status" class="form-select">
                                    <option value="">{{ __('owner.actions.choose') }}</option>
                                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>{{ __('owner.catch.unpaid') }}</option>
                                    <option value="partially_paid" {{ request('payment_status') == 'partially_paid' ? 'selected' : '' }}>{{ __('owner.catch.partially_paid') }}</option>
                                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>{{ __('owner.catch.paid') }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4 d-none" id="paidAmountWrapper">
                            <label class="form-label">{{ __('owner.generated.amount_paid') }}</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">{{__('owner.actions.save')}}</button>
                        <a href="{{ route('owner.boats.index') }}" class="btn btn-secondary">{{__('owner.actions.cancel')}}</a>
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
    <script src="{{asset('dashboard/assets/plugins/jquery-migrate/dist/jquery-migrate.min.js')}}"></script>

    <script src="{{asset('dashboard/assets/plugins/@highlightjs/cdn-assets/highlight.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/js/demo/highlightjs.demo.js')}}"></script>
    <script src="{{asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js')}}"></script>
    <script src="{{asset('dashboard/assets/js/jquery.validate.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>
    <script src="{{asset('dashboard/assets/plugins/summernote/dist/summernote-lite.min.js')}}"></script>


    <script>
        $("#createForm").validate();
    </script>

<script>
    const catchDetailsUrl = "{{ route('owner.catchDetails', ':id') }}";

    function buildFishRow(detail) {
        const price = detail.price_per_kg > 0 ? detail.price_per_kg : '';
        const unitId = detail.unit_id || '';
        const unitName = (detail.unit && detail.unit.name) ? detail.unit.name : '';
        return `
            <div class="row mb-2 fish-row align-items-center">
                <div class="col-md-3">
                    <input type="hidden" name="fish_id[]" value="${detail.fish_id}">
                    <input type="hidden" name="unit_id[]" value="${unitId}">
                    <input type="text" class="form-control" value="${detail.fish.name}" disabled>
                </div>

                <div class="col-md-2">
                    <input type="number" step="0.01" min="0" max="${detail.quantity}"
                           name="weight[]" class="form-control weight"
                           value="${detail.quantity}"
                           placeholder="≤ ${detail.quantity}">
                </div>

                <div class="col-md-2">
                    <input type="text" class="form-control" value="${unitName}" disabled>
                </div>

                <div class="col-md-2">
                    <input type="number" step="0.01" min="0"
                           name="price_per_kilo[]" class="form-control price-per-kg"
                           value="${price}"
                           placeholder="{{ __('owner.sales.price_per_unit') }}">
                </div>

                <div class="col-md-3">
                    <span class="fw-bold total-price-text">0.00</span>
                </div>
            </div>
        `;
    }

    function loadCatchDetails(tripId) {
        const wrapper = document.getElementById('fish-wrapper');
        wrapper.innerHTML = '';
        if (!tripId) {
            calculateGrandTotal();
            return;
        }

        fetch(catchDetailsUrl.replace(':id', tripId))
            .then(res => res.json())
            .then(data => {
                if (!data || data.length === 0) {
                    wrapper.innerHTML = '<p class="text-muted">{{ __('owner.generated.no_items') }}</p>';
                    calculateGrandTotal();
                    return;
                }

                data.forEach(detail => wrapper.insertAdjacentHTML('beforeend', buildFishRow(detail)));
                calculateGrandTotal();
            })
            .catch(() => {
                wrapper.innerHTML = '<p class="text-danger">{{ __('owner.generated.error_fetch') }}</p>';
            });
    }

    document.getElementById('trip_id').addEventListener('change', function () {
        loadCatchDetails(this.value);
    });

    document.addEventListener('DOMContentLoaded', function () {
        const initialTrip = document.getElementById('trip_id').value;
        if (initialTrip) {
            loadCatchDetails(initialTrip);
        }
    });
</script>

    <script>
        $(document).ready(function() {
            let baseUrl = "{{ LaravelLocalization::localizeUrl('/') }}";
            let oldRegionId = '{{ old('region_id') }}';
            let oldGovernorateId = '{{ old('governorate_id') }}';
            let oldPortId = '{{ old('port_id') }}';

            // تحميل المحافظات عند اختيار المنطقة
            $('#region_id').on('change', function() {
                let regionId = $(this).val();
                $('#governorate_id').empty().append('<option value="">{{__('owner.loading')}}</option>');
                $('#port_id').empty().append('<option value="">{{__('owner.actions.choose')}}</option>');

                if (regionId) {
                    $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID', regionId), function(data) {
                        $('#governorate_id').empty().append('<option value="">{{__('owner.actions.choose')}}</option>');
                        $.each(data, function(i, item) {
                            $('#governorate_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                    });
                }
            });

            // تحميل المدن عند اختيار المحافظة
            $('#governorate_id').on('change', function() {
                let govId = $(this).val();
                $('#port_id').empty().append('<option value="">{{__('owner.loading')}}</option>');

                if (govId) {
                    $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', govId), function(data) {
                        $('#port_id').empty().append('<option value="">{{__('owner.actions.choose')}}</option>');
                        $.each(data, function(i, item) {
                            $('#port_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                    });
                }
            });

            $('#trip_id').change(function() {
                let tripId = $(this).val();

                if (!tripId) {
                    $('[name="boat_id"], [name="boat_name"]').val('');
                    return;
                }

                let url = `${baseUrl}/owner/getBoatInfoByTrip/${tripId}`;
                $.get(url, function(data) {
                    $('[name="boat_id"]').val(data.boat_id);
                    $('[name="boat_name"]').val(data.boat_name);
                }).fail(function() {
                    console.error('Failed to load boat info');
                });
            });

            // عند تحميل الصفحة إذا في old value للمنطقة والمحافظة والمدينة
            if (oldRegionId && !$('#governorate_id option:selected').val()) {
                $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID', oldRegionId), function(governorates) {
                    $('#governorate_id').empty().append('<option value="">{{__('owner.actions.choose')}}</option>');
                    $.each(governorates, function(i, item) {
                        let selected = (item.id == oldGovernorateId) ? 'selected' : '';
                        $('#governorate_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                    });

                    if (oldGovernorateId) {
                        $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', oldGovernorateId), function(ports) {
                            $('#port_id').empty().append('<option value="">{{__('owner.actions.choose')}}</option>');
                            $.each(ports, function(i, item) {
                                let selected = (item.id == oldPortId) ? 'selected' : '';
                                $('#port_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                            });
                        });
                    }
                });
            }
        });
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

    // Event delegation
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('weight') || e.target.classList.contains('price-per-kg')) {
            calculateGrandTotal();
        }
    });
</script>

<script>
    document.querySelector('[name="payment_status"]').addEventListener('change', function () {
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
