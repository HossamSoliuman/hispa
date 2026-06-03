@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.edit_employee') }}
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
                <li class="breadcrumb-item"><a href="#">{{ __('owner.employee.page_header') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.generated.edit_employee') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.generated.edit_employee') }}</h1>
        </div>

    </div>
    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">
                <form action="{{ route('owner.employee.update', $data->id) }}" id="editForm" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        {{-- الاسم --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.assets.name') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $data->name) }}" class="form-control"
                                required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.customers.modal.labels.email') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="email" value="{{ old('email', $data->email) }}"
                                class="form-control" required>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- الشعار --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.crew.edit.image') }}</label>
                            <input type="file" name="logo" class="form-control">
                            @if ($data->logo)
                                <br>
                                <img src="{{ asset($data->logo) }}" alt="logo"
                                    style="max-width: 120px; max-height: 80px;">
                                <br>
                                <small class="text-muted">{{ __('owner.crew.edit.current_image') }}</small>
                            @endif
                            @error('logo')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        @php
                            $options = [
                                __('owner.generated.saudi'),
                                __('owner.generated.egyptian'),
                                __('owner.generated.sudanese'),
                                __('owner.generated.yemeni'),
                                __('owner.generated.bangladeshi'),
                                __('owner.generated.indian'),
                                __('owner.generated.pakistani'),
                                __('owner.generated.filipino'),
                                __('owner.generated.ethiopian'),
                                __('owner.generated.nepali'),
                                __('owner.assets.other'),
                            ];
                        @endphp
                        {{-- الجنسية --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.employee.table.nationality') }}<span
                                    class="text-danger">*</span></label>
                            <select name="nationality" id="nationality" class="form-control" required>
                                <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                @foreach ($options as $nat)
                                    <option value="{{ $nat }}"
                                        {{ old('nationality', $data->nationality) == $nat ? 'selected' : '' }}>
                                        {{ $nat }}</option>
                                @endforeach
                            </select>
                            @error('nationality')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- سعودي فقط --}}
                        <div class="col-md-4 saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.id_number') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="id_number" value="{{ old('id_number', $data->id_number) }}"
                                class="form-control">
                            @error('id_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- غير سعودي --}}
                        <div class="col-md-4 non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.residence_number') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="residence_number"
                                value="{{ old('residence_number', $data->residence_number) }}" class="form-control">
                            @error('residence_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.passport_number') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="passport_number"
                                value="{{ old('passport_number', $data->passport_number) }}" class="form-control">
                            @error('passport_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.residence_start_date') }}<span
                                    class="text-danger">*</span></label>
                            <input type="date" name="residence_start_date"
                                value="{{ old('residence_start_date', $data->residence_start_date) }}"
                                class="form-control">
                            @error('residence_start_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.residence_end_date') }}<span
                                    class="text-danger">*</span></label>
                            <input type="date" name="residence_end_date"
                                value="{{ old('residence_end_date', $data->residence_end_date) }}" class="form-control">
                            @error('residence_end_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- الوظيفة --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.employee.table.job_title') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="job_title" value="{{ old('job_title', $data->job_title) }}"
                                class="form-control" required>
                            @error('job_title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- الهاتف --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.crew.edit.phone') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $data->phone) }}"
                                class="form-control" required>
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- قيمة الراتب --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.crew.edit.salary_amount') }}<span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="salary_amount"
                                value="{{ old('salary_amount', $data->salary_amount) }}" class="form-control" required>
                        </div>
                        {{-- البنك --}}
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.dalal.modal.form.bank_name') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $data->bank_name) }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.dalal.modal.form.bank_account') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="account_number"
                                value="{{ old('account_number', $data->account_number) }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IBAN<span class="text-danger">*</span></label>
                            <input type="text" name="IBAN" value="{{ old('IBAN', $data->IBAN) }}"
                                class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-check form-switch" style="margin-top: 35px">
                                <input type="checkbox" name="status" class="form-check-input" value="1"
                                    {{ old('status', $data->status) ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="status">{{ __('owner.customers.modal.labels.status') }}</label>
                                @error('status')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('owner.employee.index') }}"
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
        $("#editForm").validate();
    </script>
    <script>
        // This script toggles Saudi and Non-Saudi fields based on nationality selection
        document.addEventListener('DOMContentLoaded', function() {
            const nationalitySelect = document.getElementById('nationality');

            const toggleFields = () => {
                const saudiFields = document.querySelectorAll('.saudi-fields');
                const nonSaudiFields = document.querySelectorAll('.non-saudi-fields');

                if (nationalitySelect.value === '{{ __('owner.generated.saudi') }}') {
                    // Show Saudi
                    saudiFields.forEach(f => {
                        f.style.display = 'block';
                        f.querySelectorAll('input, select').forEach(i => {
                            if (i.name === 'id_attachment' && i.dataset.hasAttachment ===
                                'true') {
                                i.required = false;
                            } else {
                                i.required = true;
                            }
                        });
                    });
                    // Hide Non-Saudi
                    nonSaudiFields.forEach(f => {
                        f.style.display = 'none';
                        f.querySelectorAll('input, select').forEach(i => i.required = false);
                    });
                } else if (nationalitySelect.value) {
                    // Hide Saudi
                    saudiFields.forEach(f => {
                        f.style.display = 'none';
                        f.querySelectorAll('input, select').forEach(i => i.required = false);
                    });
                    // Show Non-Saudi
                    nonSaudiFields.forEach(f => {
                        f.style.display = 'block';
                        f.querySelectorAll('input, select').forEach(i => {
                            if (i.name === 'attachment' && i.dataset.hasAttachment === 'true') {
                                i.required = false;
                            } else {
                                i.required = true;
                            }
                        });
                    });
                } else {
                    // Hide both
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
@endsection
