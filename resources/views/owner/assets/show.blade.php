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
                <li class="breadcrumb-item"><a href="{{ url('owner/assets') }}">{{__('owner.asset.title')}}</a></li>
                <li class="breadcrumb-item active">{{__('owner.asset.edit.title')}}</li>
            </ul>
            <h1 class="page-header mb-0">{{__('owner.boats.edit.title')}}</h1>
        </div>

    </div>
    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">
                <form action="{{ route('owner.assets.update', $asset->id) }}" method="post" id="createForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Row 1 --}}
                    <div class="row mb-3">
                        <div class="col-xl-3">
                            <div class="form-group">
                                <label for="name_ar" class="form-label">{{ __('owner.asset.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" value="{{ old('name_ar', $asset->name_ar) }}" class="form-control" required placeholder="{{ __('owner.asset.name') }}">
                                @error('name_ar') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="form-group">
                                <label for="name_en" class="form-label">{{ __('owner.asset.name_en') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name_en" value="{{ old('name_en', $asset->name_en) }}" class="form-control" required placeholder="{{ __('owner.asset.name_en') }}">
                                @error('name_en') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="status" class="form-label">{{ __('owner.asset.status') }} <span class="text-danger">*</span></label>
                            <select name="status" required class="form-control">
                                <option value="1" {{ (old('status') || $asset->status) == 1 ? 'selected' : '' }}>{{ __('owner.asset.status_active') }}</option>
                                <option value="0" {{ (old('status') || $asset->status) == 0 ? 'selected' : '' }}>{{ __('owner.asset.status_inactive') }}</option>
                            </select>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
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
                       }
                   });
               }
           });
       </script>
@endsection
