@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.personal_data') }}
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
                <li class="breadcrumb-item"><a href="#">{{ __('owner.generated.manage_personal_data') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.generated.personal_data') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.generated.personal_data') }}</h1>
        </div>

    </div>
    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">
                <form action="{{ route('owner.basic_data.update', $user->id) }}" id="createForm" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="form-group ">
                                <label for="name" class="form-label">{{ __('owner.assets.name') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="fields[name]" value="{{ old('name', $user->name) }}"
                                    class="form-control  " required placeholder="{{ __('owner.assets.name') }}">


                                @error('name')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>

                        </div>
                        <div class="col-xl-4">
                            <div class="form-group ">
                                <label for="phone" class="form-label">{{ __('owner.dalal.modal.form.phone') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="fields[phone]" disabled value="{{ old('phone', $user->phone) }}"
                                    class="form-control  " required placeholder="{{ __('owner.dalal.modal.form.phone') }}">


                                @error('phone')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>

                        </div>

                        <div class="col-xl-4">
                            <div class="form-group ">
                                <label for="email"
                                    class="form-label">{{ __('owner.customers.modal.labels.email') }}<span
                                        class="text-danger">*</span></label>
                                <input type="email" name="fields[email]" disabled value="{{ old('email', $user->email) }}"
                                    class="form-control  " required
                                    placeholder="{{ __('owner.customers.modal.labels.email') }}">


                                @error('email')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>

                        </div>

                    </div>
                    @if ($user->role == 'owner' || $user->role == 'dalal')
                        <div class="row">
                            <div class="col-xl-4">
                                <div class="form-group ">
                                    <label for="id_number" class="form-label">{{ __('owner.crew.edit.id_number') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="fields[id_number]"
                                        value="{{ old('id_number', $user->id_number) }}" class="form-control  " required
                                        placeholder="{{ __('owner.crew.edit.id_number') }}">


                                    @error('id_number')
                                        <span class="text-danger error">{{ $message }}</span>
                                    @enderror

                                </div>

                            </div>
                            <div class="col-xl-4">
                                <div class="form-group ">
                                    <label for="record_number"
                                        class="form-label">{{ __('owner.generated.commercial_registration_no') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="fields[record_number]"
                                        value="{{ old('record_number', $user->record_number) }}" class="form-control  "
                                        required placeholder="{{ __('owner.generated.commercial_registration_no') }}">


                                    @error('record_number')
                                        <span class="text-danger error">{{ $message }}</span>
                                    @enderror

                                </div>

                            </div>
                            <div class="col-xl-4">
                                <div class="form-group ">
                                    <label for="tax_number" class="form-label">{{ __('owner.generated.tax_number') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="fields[tax_number]"
                                        value="{{ old('tax_number', $user->tax_number) }}" class="form-control  " required
                                        placeholder="{{ __('owner.generated.tax_number') }}">


                                    @error('tax_number')
                                        <span class="text-danger error">{{ $message }}</span>
                                    @enderror

                                </div>
                            </div>
                            <div class="col-xl-4">
                                <label class="form-label">{{ __('owner.generated.fishing_license') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="fishing_license_number"
                                    value="{{ old('fishing_license_number') }}" class="form-control"
                                    placeholder="{{ __('owner.generated.placeholder_fishing_license_no') }}">
                                @error('fishing_license_number')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-xl-4">
                                <label class="form-label">{{ __('owner.generated.fishing_license_expiry_date') }}<span
                                        class="text-danger">*</span></label>
                                <input type="date" name="fishing_license_expiry"
                                    value="{{ old('fishing_license_expiry') }}" class="form-control" required>
                                @error('fishing_license_expiry')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-xl-4">
                                <div class="form-group ">
                                    <label for="region_id"
                                        class="form-label">{{ __('owner.generated.region_name') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="fields[region_id]" class="form-control  " required id="region_id">
                                        <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region->id }}"
                                                {{ (old('region_id') ?? ($user->region_id ?? '')) == $region->id ? 'selected' : '' }}>
                                                {{ $region->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('region_id')
                                        <span class="text-danger error">{{ $message }}</span>
                                    @enderror

                                </div>

                            </div>
                            <div class="col-xl-4">
                                <div class="form-group ">
                                    <label for="governorate_id"
                                        class="form-label">{{ __('owner.generated.governorate_name') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="fields[governorate_id]" class="form-control" required
                                        id="governorate_id">
                                        <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                        @php
                                            $selectedRegionId = old('region_id') ?? ($user->region_id ?? null);
                                            $selectedGovernorateId =
                                                old('governorate_id') ?? ($user->governorate_id ?? null);
                                            $governorates = $selectedRegionId
                                                ? \App\Models\Governorate::where('region_id', $selectedRegionId)->get()
                                                : collect();
                                        @endphp

                                        @foreach ($governorates as $gov)
                                            <option value="{{ $gov->id }}"
                                                {{ $selectedGovernorateId == $gov->id ? 'selected' : '' }}>
                                                {{ $gov->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    @error('governorate_id')
                                        <span class="text-danger error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <label for="port_id" class="form-label">{{ __('owner.crew.table.port') }}<span
                                        class="text-danger">*</span></label>
                                <select name="fields[port_id]" id="port_id" required class="form-control">
                                    <option value="">-- {{ __('owner.boats.select_port') }}--</option>
                                    @if (old('port_id') && old('governorate_id'))
                                        @php
                                            $oldPorts = \App\Models\Port::where(
                                                'governorate_id',
                                                old('governorate_id'),
                                            )->get();
                                        @endphp
                                        @foreach ($oldPorts as $port)
                                            <option value="{{ $port->id }}"
                                                {{ old('port_id') == $port->id ? 'selected' : '' }}>
                                                {{ $port->name }}
                                            </option>
                                        @endforeach
                                    @endif

                                </select>
                                @error('port_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endif
                    <br>
                    <br>
                    @if ($user->role == 'owner' || $user->role == 'dalal')
                        <div class="row">

                            <label for="attachment" class="form-label">{{ __('owner.expenses.show.attachment') }}</label>

                            <input type="file" name="attachment" class="form-control mt-3">

                            @if (isset($user->attachment))
                                <a href="{{ $user->attachment }}" target="_blank"
                                    class="d-block mt-2 text-primary">{{ __('owner.expenses.show.view_attachment') }}</a>
                            @endif

                        </div>
                    @endif
                    <br>
                    <br>
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="btn btn-secondary">{{ __('owner.crew.edit.back') }}</button>
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
            let oldPortId = '{{ old('port_id') }}';

            // تحميل المحافظات عند اختيار المنطقة
            $('#region_id').on('change', function() {
                let regionId = $(this).val();
                $('#governorate_id').empty().append(
                    '<option value="">{{ __('owner.dalal.performance.loading') }}</option>');
                $('#port_id').empty().append(
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
                    $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', govId),
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
@endsection
