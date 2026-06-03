@extends('owner.layouts.master')
@section('title')
{{__('owner.boats.edit.title')}}
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
                <li class="breadcrumb-item"><a href="#">{{__('owner.boats.title')}}</a></li>
                <li class="breadcrumb-item active">{{__('owner.boats.edit.title')}}</li>
            </ul>
            <h1 class="page-header mb-0">{{__('owner.boats.edit.title')}}</h1>
        </div>

    </div>
    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">

                <form action="{{ route('owner.boats.update', $data->id) }}" method="post" id="createForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Row 1 --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                        <label for="name_ar" class="form-label">{{__('owner.boats.name_ara')}} <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" class="form-control" required value="{{ old('name_ar', $data->name_ar) }}">
                            @error('name_ar') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="name_en" class="form-label">{{__('owner.boats.name_eng')}} <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" class="form-control" required value="{{ old('name_en', $data->name_en) }}">
                            @error('name_en') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="boat_type_id" class="form-label">{{__('owner.boats.class')}} <span class="text-danger">*</span></label>
                            <select name="boat_type_id" required class="form-control">
                                <option value="">{{__('owner.actions.choose')}}</option>
                                @foreach($boat_types as $boat_type)
                                    <option value="{{$boat_type->id}}" {{ $boat_type->id == $data->boat_type_id ? "selected":""}}>{{$boat_type->name}}</option>

                                @endforeach
                            </select>
                            @error('boat_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="number" class="form-label">{{__('owner.boats.boat_number')}} <span class="text-danger">*</span></label>
                            <input type="text" name="number" class="form-control" required value="{{ old('number', $data->number) }}">
                            @error('number') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    {{-- Row 2 --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">{{__('owner.boats.status')}} <span class="text-danger">*</span></label>
                            <select name="status" required class="form-control">
                                <option value="1" {{ old('status', $data->status) == 1 ? 'selected' : '' }}>{{__('owner.status.active')}}</option>
                                <option value="0" {{ old('status', $data->status) == 0 ? 'selected' : '' }}>{{__('owner.status.inactive')}}</option>
                            </select>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="length" class="form-label">{{__('owner.boats.length')}} <span class="text-danger">*</span></label>
                            <input type="number" name="length" class="form-control" required value="{{ old('length', $data->length) }}">
                            @error('length') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="width" class="form-label">{{__('owner.boats.width')}} <span class="text-danger">*</span></label>
                            <input type="number" name="width" class="form-control" required value="{{ old('width', $data->width) }}">
                            @error('width') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="color" class="form-label">{{__('owner.boats.color')}} <span class="text-danger">*</span></label>
                            <input type="text" name="color" class="form-control" required value="{{ old('color', $data->color) }}">
                            @error('color') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    {{-- Row 3 --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="type" class="form-label">{{__('owner.boats.type')}} <span class="text-danger">*</span></label>
                            <input type="text" name="type" class="form-control" required value="{{ old('type', $data->type) }}">
                            @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="license_region_id" class="form-label">{{__('owner.boats.license_region')}} <span class="text-danger">*</span></label>
                            <select name="license_region_id" required class="form-control">
                                <option value="">{{__('owner.actions.choose')}}</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ old('license_region_id', $data->license_region_id) == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('license_region_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="license_date" class="form-label">{{__('owner.boats.license_date')}} <span class="text-danger">*</span></label>
                            <input type="date" name="license_date" required class="form-control" value="{{ old('license_date', $data->license_date) }}">
                            @error('license_date') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="license_date_expire" class="form-label">{{__('owner.boats.license_date_expire')}} <span class="text-danger">*</span></label>
                            <input type="date" name="license_date_expire" required class="form-control" value="{{ old('license_date_expire', $data->license_date_expire) }}">
                            @error('license_date_expire') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Row 4 --}}
                    <div class="row mb-3">
                    
                        <div class="col-md-3">
                            <label for="body_number" class="form-label">{{__('owner.boats.body_number')}} <span class="text-danger">*</span></label>
                            <input type="text" name="body_number" required class="form-control" value="{{ old('body_number', $data->body_number) }}">
                            @error('body_number') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="body_type" class="form-label">{{__('owner.boats.body_type')}} <span class="text-danger">*</span></label>
                            <input type="text" name="body_type" required class="form-control" value="{{ old('body_type', $data->body_type) }}">
                            @error('body_type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="callsign_number" class="form-label">{{__('owner.boats.callsign_number')}} <span class="text-danger">*</span></label>
                            <input type="text" name="callsign_number" required class="form-control" value="{{ old('callsign_number', $data->callsign_number) }}">
                            @error('callsign_number') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    {{-- Row 5 --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="serial_number" class="form-label">{{__('owner.boats.serial_number')}} <span class="text-danger">*</span></label>
                            <input type="text" name="serial_number" required class="form-control" value="{{ old('serial_number', $data->serial_number) }}">
                            @error('serial_number') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="engine_type" class="form-label">{{__('owner.boats.engine_type')}} <span class="text-danger">*</span></label>
                            <input type="text" required name="engine_type" class="form-control" value="{{ old('engine_type', $data->engine_type) }}">
                            @error('engine_type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="engine_power" class="form-label">{{__('owner.boats.engine_power')}} <span class="text-danger">*</span></label>
                            <input type="text" required name="engine_power" class="form-control" value="{{ old('engine_power', $data->engine_power) }}">
                            @error('engine_power') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    {{-- Row 6 --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="crew_number" class="form-label">{{__('owner.boats.crew_count')}} <span class="text-danger">*</span></label>
                            <input type="number" required name="crew_number" class="form-control" value="{{ old('crew_number', $data->crew_number) }}">
                            @error('crew_number') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="payload" class="form-label">{{__('owner.boats.cargo_capacity')}} <span class="text-danger">*</span></label>
                            <input type="number" required step="0.01" name="payload" class="form-control" value="{{ old('payload', $data->payload) }}">
                            @error('payload') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="region_id" class="form-label">{{__('owner.boats.region')}} <span class="text-danger">*</span></label>
                            <select name="region_id" id="region_id" required class="form-control">
                                <option value="">{{__('owner.actions.choose')}} </option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}"
                                        {{ (old('region_id') ?? ($data->region_id ?? '')) == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('region_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="governorate_id" class="form-label">{{__('owner.boats.governorate')}} <span class="text-danger">*</span></label>
                            <select name="governorate_id" id="governorate_id" required class="form-control">
                                <option value="">{{__('owner.actions.choose')}} </option>
                                @php
                                    $selectedRegionId = old('region_id') ?? $data->region_id ?? null;
                                    $selectedGovernorateId = old('governorate_id') ?? $data->governorate_id ?? null;
                                    $governorates = $selectedRegionId ? \App\Models\Governorate::where('region_id', $selectedRegionId)->get() : collect();
                                @endphp

                                @foreach($governorates as $gov)
                                    <option
                                        value="{{ $gov->id }}" {{ $selectedGovernorateId == $gov->id ? 'selected' : '' }}>
                                        {{ $gov->name }}
                                    </option>
                                @endforeach                            </select>
                            @error('governorate_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="port_id" class="form-label">{{__('owner.boats.port')}} <span class="text-danger">*</span></label>
                            <select name="port_id" id="port_id" required class="form-control">
                                <option value="">{{__('owner.actions.choose')}} </option>
                                @php
                                    $selectedGovernorateId = old('governorate_id') ?? $data->governorate_id ?? null;
                                    $selectedPortId = old('port_id') ?? $data->port_id ?? null;
                                    $ports = $selectedGovernorateId ? \App\Models\Port::where('governorate_id', $selectedGovernorateId)->get() : collect();
                                @endphp

                                @foreach($ports as $port)
                                    <option
                                        value="{{ $port->id }}" {{ $selectedPortId == $port->id ? 'selected' : '' }}>
                                        {{ $port->name }}
                                    </option>
                                @endforeach                            </select>
                            @error('port_id') <span class="text-danger">{{ $message }}</span> @enderror
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
           $(document).ready(function () {
               let oldRegionId = '{{ old('region_id') }}';
               let oldGovernorateId = '{{ old('governorate_id') }}';
               {{--let oldCityId = '{{ old('city_id') }}';--}}
               let oldPortId = '{{ old('port_id') }}';

               // تحميل المحافظات عند اختيار المنطقة
               $('#region_id').on('change', function () {
                   let regionId = $(this).val();
                   $('#governorate_id').empty().append('<option value="">{{__('owner.loading')}} </option>');
                   $('#port_id').empty().append('<option value="">{{__('owner.loading')}} </option>');

                   if (regionId) {
                       $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID', regionId), function (data) {
                           $('#governorate_id').empty().append('<option value="">{{__('owner.actions.choose')}} </option>');
                           $.each(data, function (i, item) {
                               $('#governorate_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                           });
                       });
                   }
               });

               // تحميل المدن عند اختيار المحافظة
               $('#governorate_id').on('change', function () {
                   let govId = $(this).val();
                   $('#port_id').empty().append('<option value="">{{__('owner.loading')}} </option>');

                   if (govId) {
                       $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', govId), function (data) {
                           $('#port_id').empty().append('<option value="">{{__('owner.actions.choose')}} </option>');
                           $.each(data, function (i, item) {
                               $('#port_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                           });
                       });
                   }
               });

               // تحميل المنافذ عند اختيار المدينة
               // $('#city_id').on('change', function () {
               //     let cityId = $(this).val();
               //     $('#port_id').empty().append('<option value="">{{ __('owner.dalal.performance.loading') }}</option>');
               //
               //     if (cityId) {
               //         $.get('/owner/get-ports/' + cityId, function (data) {
               //             $('#port_id').empty().append('<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');
               //             $.each(data, function (i, item) {
               //                 $('#port_id').append('<option value="' + item.id + '">' + item.name + '</option>');
               //             });
               //         });
               //     }
               // });

               // عند تحميل الصفحة إذا في old value للمنطقة والمحافظة والمدينة
               if (oldRegionId && !$('#governorate_id option:selected').val()) {
                   $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID', oldRegionId), function (governorates) {
                       $('#governorate_id').empty().append('<option value="">{{__('owner.actions.choose')}} </option>');
                       $.each(governorates, function (i, item) {
                           let selected = (item.id == oldGovernorateId) ? 'selected' : '';
                           $('#governorate_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                       });

                       if (oldGovernorateId) {

                           $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', oldGovernorateId), function (ports) {
                               $('#port_id').empty().append('<option value="">{{__('owner.actions.choose')}} </option>');
                               $.each(ports, function (i, item) {
                                   let selected = (item.id == oldPortId) ? 'selected' : '';
                                   $('#port_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                               });
                           });

                           // $.get('/owner/get-cities/' + oldGovernorateId, function (cities) {
                           //     $('#city_id').empty().append('<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');
                           //     $.each(cities, function (i, item) {
                           //         let selected = (item.id == oldCityId) ? 'selected' : '';
                           //         $('#city_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                           //     });
                           //
                           //     // ✅ تحميل المنافذ من المدينة المختارة
                           //     if (oldCityId) {
                           //         $.get('/owner/get-ports/' + oldCityId, function (ports) {
                           //             $('#port_id').empty().append('<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');
                           //             $.each(ports, function (i, item) {
                           //                 let selected = (item.id == oldPortId) ? 'selected' : '';
                           //                 $('#port_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                           //             });
                           //         });
                           //     }
                           // });
                       }
                   });
               }
           });
       </script>






@endsection
