@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.create_new_captain') }}
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
                <li class="breadcrumb-item"><a href="#">{{ __('owner.captain.breadcrumb_manage') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.generated.create_new_captain') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.generated.create_new_captain') }}</h1>
        </div>

    </div>
    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">
                <form action="{{ route('owner.captain.store') }}" id="createForm" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group ">
                                <label for="boat_id" class="form-label">{{ __('owner.sales.boat') }}<span
                                        class="text-danger">*</span></label>

                                <select name="boat_id" class="form-control  " required id="boat_id">
                                    <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                    @foreach ($boats as $boat)
                                        <option value="{{ $boat->id }}"
                                            {{ old('boat_id') == $boat->id ? 'selected' : '' }}>
                                            {{ $boat->name }}
                                    @endforeach
                                </select>


                                @error('boat_id')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>

                        </div>
                        {{-- الاسم --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.assets.name') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.customers.modal.labels.email') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="email" value="{{ old('email') }}" class="form-control" required>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- الشعار --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.crew.edit.image') }}</label>
                            <input type="file" name="logo" class="form-control">
                            @error('logo')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>



                        {{-- الجنسية --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.employee.table.nationality') }}<span
                                    class="text-danger">*</span></label>
                            <select name="nationality" id="nationality" class="form-control" required>
                                <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                <option value="{{ __('owner.generated.saudi') }}"
                                    {{ old('nationality') == __('owner.generated.saudi') ? 'selected' : '' }}>
                                    {{ __('owner.generated.saudi') }}</option>
                                <option value="{{ __('owner.generated.egyptian') }}">{{ __('owner.generated.egyptian') }}
                                </option>
                                <option value="{{ __('owner.generated.sudanese') }}">{{ __('owner.generated.sudanese') }}
                                </option>
                                <option value="{{ __('owner.generated.yemeni') }}">{{ __('owner.generated.yemeni') }}
                                </option>
                                <option value="{{ __('owner.generated.bangladeshi') }}">
                                    {{ __('owner.generated.bangladeshi') }}</option>
                                <option value="{{ __('owner.generated.indian') }}">{{ __('owner.generated.indian') }}
                                </option>
                                <option value="{{ __('owner.generated.pakistani') }}">
                                    {{ __('owner.generated.pakistani') }}</option>
                                <option value="{{ __('owner.generated.filipino') }}">{{ __('owner.generated.filipino') }}
                                </option>
                                <option value="{{ __('owner.generated.ethiopian') }}">
                                    {{ __('owner.generated.ethiopian') }}</option>
                                <option value="{{ __('owner.generated.nepali') }}">{{ __('owner.generated.nepali') }}
                                </option>
                                <option value="{{ __('owner.assets.other') }}">{{ __('owner.assets.other') }}</option>
                            </select>
                            @error('nationality')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- سعودي فقط --}}
                        <div class="col-md-4 saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.id_number') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="id_number" value="{{ old('id_number') }}" class="form-control">
                            @error('id_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.id_attachment') }}</label>
                            <input type="file" name="id_attachment" class="form-control">
                            @error('id_attachment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- غير سعودي --}}
                        <div class="col-md-4 non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.residence_number') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="residence_number" value="{{ old('residence_number') }}"
                                class="form-control">
                            @error('residence_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.passport_number') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="passport_number" value="{{ old('passport_number') }}"
                                class="form-control">
                            @error('passport_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 non-saudi-fields">
                            <label
                                class="form-label">{{ __('owner.generated.passport_attachment') }}/{{ __('owner.generated.residence_permit') }}<span
                                    class="text-danger">*</span></label>
                            <input type="file" name="attachment" class="form-control">
                            @error('attachment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.residence_start_date') }}<span
                                    class="text-danger">*</span></label>
                            <input type="date" name="residence_start_date" value="{{ old('residence_start_date') }}"
                                class="form-control">
                            @error('residence_start_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.residence_end_date') }}<span
                                    class="text-danger">*</span></label>
                            <input type="date" name="residence_end_date" value="{{ old('residence_end_date') }}"
                                class="form-control">
                            @error('residence_end_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- الوظيفة --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.employee.table.job_title') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="job_title" value="{{ old('job_title') }}" class="form-control"
                                required>
                            @error('job_title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- الهاتف --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.crew.edit.phone') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control"
                                required>
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- نوع الراتب --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.payrolls.show.salary_type') }}<span
                                    class="text-danger">*</span></label>
                            <select name="salary_type" required class="form-control">
                                <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                <option value="salary">{{ __('owner.crew.edit.salary_option_salary') }}</option>
                                <option value="percentage">{{ __('owner.payrolls.show.percentage') }}%</option>
                            </select>
                        </div>

                        {{-- قيمة الراتب --}}
                        <div class="col-md-4" id="salary_value">
                            {{-- <label class="form-label">{{ __('owner.crew.edit.salary_amount') }}<span class="text-danger">*</span></label> --}}
                            <label class="form-label"
                                id="salaryAmountLabel">{{ __('owner.crew.edit.salary_amount') }}<span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.01" required name="salary_amount"
                                value="{{ old('salary_amount') }}" class="form-control">
                        </div>

                        {{-- نسبة خاصة من حصة الطاقم (اختياري، تظهر مع النسبة فقط) --}}
                        <div class="col-md-4 custom-share-field"
                            style="display: {{ old('salary_type') === 'percentage' ? 'block' : 'none' }};">
                            <label class="form-label">{{ __('owner.custom_share.label') }}</label>
                            <input type="number" step="0.01" min="0" max="100" name="custom_share_percent"
                                value="{{ old('custom_share_percent') }}" class="form-control"
                                placeholder="{{ __('owner.custom_share.placeholder') }}">
                            <small class="text-muted d-block">{{ __('owner.custom_share.hint') }}</small>
                            @error('custom_share_percent')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.generated.fishing_license') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="fishing_license_number"
                                value="{{ old('fishing_license_number') }}" class="form-control" required
                                placeholder="{{ __('owner.generated.placeholder_fishing_license_no') }}">
                            @error('fishing_license_number')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.generated.fishing_license_expiry_date') }}<span
                                    class="text-danger">*</span></label>
                            <input type="date" name="fishing_license_expiry"
                                value="{{ old('fishing_license_expiry') }}" class="form-control" required>
                            @error('fishing_license_expiry')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.generated.driving_license') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="driving_license_number"
                                value="{{ old('driving_license_number') }}" class="form-control" required
                                placeholder="{{ __('owner.generated.placeholder_driving_license') }}">
                            @error('driving_license_number')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.generated.license_expiry') }}<span
                                    class="text-danger">*</span></label>
                            <input type="date" name="driving_license_expiry"
                                value="{{ old('driving_license_expiry') }}" class="form-control" required>
                            @error('driving_license_expiry')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- البنك --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.dalal.modal.form.bank_name') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="bank_name" required value="{{ old('bank_name') }}"
                                class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.dalal.modal.form.bank_account') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="account_number" required value="{{ old('account_number') }}"
                                class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">IBAN<span class="text-danger">*</span></label>
                            <input type="text" name="IBAN" required value="{{ old('IBAN') }}"
                                class="form-control">
                        </div>
                        <div class="col-md-4">
                            <div class="form-group ">
                                <label for="region_id" class="form-label">{{ __('owner.generated.region_name') }}<span
                                        class="text-danger">*</span></label>

                                <select name="region_id" class="form-control  " required id="region_id">
                                    <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}"
                                            {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                    @endforeach
                                </select>


                                @error('region_id')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="form-group ">
                                <label for="governorate_id"
                                    class="form-label">{{ __('owner.generated.governorate_name') }}<span
                                        class="text-danger">*</span></label>

                                <select name="governorate_id" class="form-control" required id="governorate_id">
                                    <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                    @if (old('region_id') && old('governorate_id'))
                                        @php
                                            $oldGovernorates = \App\Models\Governorate::where(
                                                'region_id',
                                                old('region_id'),
                                            )->get();
                                        @endphp
                                        @foreach ($oldGovernorates as $gov)
                                            <option value="{{ $gov->id }}"
                                                {{ old('governorate_id') == $gov->id ? 'selected' : '' }}>
                                                {{ $gov->name }}
                                            </option>
                                        @endforeach
                                    @endif

                                </select>
                                @error('governorate_id')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="form-group ">
                                <label for="password" class="form-label">{{ __('owner.crew.edit.password') }}<span
                                        class="text-danger">*</span></label>

                                <input type="password" name="password" value="{{ old('password') }}"
                                    class="form-control  " required placeholder="{{ __('owner.crew.edit.password') }}">


                                @error('password')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>

                        </div>
                        <div class="col-xl-4">
                            <div class="form-group ">
                                <label for="password" class="form-label">
                                    {{ __('owner.crew.edit.password_confirmation') }}<span
                                        class="text-danger">*</span></label>

                                <input type="password" name="password_confirmation"
                                    value="{{ old('password_confirmation') }}" class="form-control  " required
                                    placeholder="{{ __('owner.crew.edit.password_confirmation') }}">


                                @error('password_confirmation')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-check form-switch " style="margin-top: 35px">
                                <input type="checkbox" name="status" class="form-check-input" value="1"
                                    {{ old('status', 1) ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="status">{{ __('owner.customers.modal.labels.status') }}</label>
                                @error('status')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('owner.crew.index') }}"
                                class="btn btn-secondary">{{ __('owner.crew.edit.back') }}</a>
                            <button type="submit"
                                class="btn btn-success">{{ __('owner.customers.modal.buttons.save') }}</button>
                        </div>
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
        $(document).ready(function() {
            let oldRegionId = '{{ old('region_id') }}';
            let oldGovernorateId = '{{ old('governorate_id') }}';
            let oldCityId = '{{ old('city_id') }}';
            let oldPortId = '{{ old('port_id') }}';

            // تحميل المحافظات عند اختيار المنطقة
            $('#region_id').on('change', function() {
                let regionId = $(this).val();
                $('#governorate_id').empty().append(
                    '<option value="">{{ __('owner.dalal.performance.loading') }}</option>');
                $('#city_id').empty().append(
                    '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');

                if (regionId) {
                    $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace(
                        'REGION_ID', regionId), function(data) {
                        $('#governorate_id').empty().append(
                            '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>'
                        );
                        $.each(data, function(i, item) {
                            $('#governorate_id').append('<option value="' + item.id + '">' +
                                item.name + '</option>');
                        });
                    });
                }
            });

            // تحميل المدن عند اختيار المحافظة
            $('#governorate_id').on('change', function() {
                let govId = $(this).val();
                $('#port_id').empty().append(
                    '<option value="">{{ __('owner.dalal.performance.loading') }}</option>');

                if (govId) {
                    $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID',
                            govId),
                        function(data) {
                            $('#port_id').empty().append(
                                '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>'
                            );
                            $.each(data, function(i, item) {
                                $('#port_id').append('<option value="' + item.id + '">' + item
                                    .name + '</option>');
                            });
                        });
                }
            });

            // عند تحميل الصفحة إذا في old value للمنطقة والمحافظة والمدينة
            if (oldRegionId && !$('#governorate_id option:selected').val()) {
                $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID',
                    oldRegionId), function(governorates) {
                    $('#governorate_id').empty().append(
                        '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');
                    $.each(governorates, function(i, item) {
                        let selected = (item.id == oldGovernorateId) ? 'selected' : '';
                        $('#governorate_id').append('<option value="' + item.id + '" ' + selected +
                            '>' + item.name + '</option>');
                    });

                    if (oldGovernorateId) {
                        $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID',
                            oldGovernorateId), function(ports) {
                            $('#port_id').empty().append(
                                '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>'
                            );
                            $.each(ports, function(i, item) {
                                let selected = (item.id == oldPortId) ? 'selected' : '';
                                $('#port_id').append('<option value="' + item.id + '" ' +
                                    selected + '>' + item.name + '</option>');
                            });
                        });
                    }
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nationalitySelect = document.getElementById('nationality');

            const toggleFields = () => {
                const saudiFields = document.querySelectorAll('.saudi-fields');
                const nonSaudiFields = document.querySelectorAll('.non-saudi-fields');

                if (nationalitySelect.value === '{{ __('owner.generated.saudi') }}') {
                    saudiFields.forEach(f => {
                        f.style.display = 'block';
                        f.querySelectorAll('input, select').forEach(i => i.required = true);
                    });
                    nonSaudiFields.forEach(f => {
                        f.style.display = 'none';
                        f.querySelectorAll('input, select').forEach(i => i.required = false);
                    });
                } else if (nationalitySelect.value) {
                    saudiFields.forEach(f => {
                        f.style.display = 'none';
                        f.querySelectorAll('input, select').forEach(i => i.required = false);
                    });
                    nonSaudiFields.forEach(f => {
                        f.style.display = 'block';
                        f.querySelectorAll('input, select').forEach(i => i.required = true);
                    });
                } else {
                    saudiFields.forEach(f => {
                        f.style.display = 'none';
                        f.querySelectorAll('input, select').forEach(i => i.required = false);
                    });
                    nonSaudiFields.forEach(f => {
                        f.style.display = 'none';
                        f.querySelectorAll('input, select').forEach(i => i.required = false);
                    });
                }
            };

            nationalitySelect.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>


    <script>
        (function() {
            const salaryTypeSelect = document.querySelector('select[name="salary_type"]');
            const salaryValue = document.getElementById('salary_value');
            const salaryAmount = document.querySelector('input[name="salary_amount"]');
            const label = document.getElementById('salaryAmountLabel');
            const customShareFields = document.querySelectorAll('.custom-share-field');

            const applySalaryType = () => {
                const isPercentage = salaryTypeSelect.value === 'percentage';

                if (isPercentage) {
                    label.innerHTML = '{{ __('owner.generated.item_87c8b2') }} <span class="text-danger">*</span>';
                    salaryValue.style.display = 'none';
                    salaryAmount.required = false;
                } else {
                    label.innerHTML = '{{ __('owner.generated.item_98b1f2') }} <span class="text-danger">*</span>';
                    salaryValue.style.display = 'block';
                    salaryAmount.required = true;
                }

                customShareFields.forEach(f => {
                    f.style.display = isPercentage ? 'block' : 'none';
                });
            };

            salaryTypeSelect.addEventListener('change', applySalaryType);
            applySalaryType();
        })();
    </script>
@endsection
