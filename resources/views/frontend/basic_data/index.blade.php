@extends('frontend.layouts.master')
@section('title')
    البيانات الشخصية
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
                <li class="breadcrumb-item"><a href="#">ادارة البيانات الشخصية</a></li>
                <li class="breadcrumb-item active">البيانات الشخصية </li>
            </ul>
            <h1 class="page-header mb-0">البيانات الشخصية </h1>
        </div>

    </div>
    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">
                <form action="{{route('frontend.basic_data.update',$user->id)}}" id="createForm" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="form-group ">
                                <label for="name" class="form-label">الاسم<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="fields[name]" value="{{old('name',$user->name)}}"
                                       class="form-control  " required
                                       placeholder="الاسم">


                                @error('name') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>

                        </div>
                        <div class="col-xl-4">
                            <div class="form-group ">
                                <label for="phone" class="form-label">رقم الجوال<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="fields[phone]" disabled value="{{old('phone',$user->phone)}}"
                                       class="form-control  " required
                                       placeholder="رقم الجوال">


                                @error('phone') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>

                        </div>

                        <div class="col-xl-4">
                            <div class="form-group ">
                                <label for="email" class="form-label">الايميل<span
                                        class="text-danger">*</span></label>
                                <input type="email" name="fields[email]" disabled value="{{old('email',$user->email)}}"
                                       class="form-control  " required
                                       placeholder="الايميل">


                                @error('email') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>

                        </div>

                    </div>
                    @if($user->role =="owner" ||$user->role =="dalal")
                        <div class="row">
                            <div class="col-xl-4">
                                <div class="form-group ">
                                    <label for="id_number" class="form-label">رقم الهوية<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="fields[id_number]" value="{{old('id_number',$user->id_number)}}"
                                           class="form-control  " required
                                           placeholder="رقم الهوية">


                                    @error('id_number') <span class="text-danger error">{{ $message }}</span>@enderror

                                </div>

                            </div>
                            <div class="col-xl-4">
                                <div class="form-group ">
                                    <label for="record_number" class="form-label">رقم السجل التجاري<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="fields[record_number]" value="{{old('record_number',$user->record_number)}}"
                                           class="form-control  " required
                                           placeholder="رقم السجل التجاري">


                                    @error('record_number') <span class="text-danger error">{{ $message }}</span>@enderror

                                </div>

                            </div>
                            <div class="col-xl-4">
                                <div class="form-group ">
                                    <label for="tax_number" class="form-label">رقم الضريبي<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="fields[tax_number]" value="{{old('tax_number',$user->tax_number)}}"
                                           class="form-control  " required
                                           placeholder="رقم الضريبي">


                                    @error('tax_number') <span class="text-danger error">{{ $message }}</span>@enderror

                                </div>

                            </div>

                        </div>


                    @endif



                    <div class="row">
                        <div class="col-xl-4">
                            <div class="form-group ">
                                <label for="region_id" class="form-label">اسم المنطقة<span
                                        class="text-danger">*</span></label>

                                <select name="fields[region_id]"    class="form-control  " required id="region_id">
                                    <option value="">اختر</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}"
                                            {{ (old('region_id') ?? ($user->region_id ?? '')) == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach

                                </select>


                                @error('region_id') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>

                        </div>
                        <div class="col-xl-4">
                            <div class="form-group ">
                                <label for="governorate_id" class="form-label">اسم المحافظة<span
                                        class="text-danger">*</span></label>

                                <select name="fields[governorate_id]"    class="form-control" required id="governorate_id">
                                    <option value="">اختر</option>
                                    @php
                                        $selectedRegionId = old('region_id') ?? $user->region_id ?? null;
                                        $selectedGovernorateId = old('governorate_id') ?? $user->governorate_id ?? null;
                                        $governorates = $selectedRegionId ? \App\Models\Governorate::where('region_id', $selectedRegionId)->get() : collect();
                                    @endphp

                                    @foreach($governorates as $gov)
                                        <option value="{{ $gov->id }}" {{ $selectedGovernorateId == $gov->id ? 'selected' : '' }}>
                                            {{ $gov->name }}
                                        </option>
                                    @endforeach

                                </select>


                                @error('governorate_id') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>

                        </div>
{{--                        <div class="col-xl-4">--}}
{{--                            <div class="form-group ">--}}
{{--                                <label for="city_id" class="form-label">اسم المدينة<span--}}
{{--                                        class="text-danger">*</span></label>--}}
{{--                                <select  name="fields[city_id]"   class="form-control  " required id="city_id">--}}
{{--                                    <option value="">اختر</option>--}}
{{--                                    @php--}}
{{--                                        $selectedGovernorateId = old('governorate_id') ?? $user->governorate_id ?? null;--}}
{{--                                        $selectedCityId = old('city_id') ?? $user->city_id ?? null;--}}
{{--                                        $cities = $selectedGovernorateId ? \App\Models\City::where('governorate_id', $selectedGovernorateId)->get() : collect();--}}
{{--                                    @endphp--}}

{{--                                    @foreach($cities as $city)--}}
{{--                                        <option value="{{ $city->id }}" {{ $selectedCityId == $city->id ? 'selected' : '' }}>--}}
{{--                                            {{ $city->name }}--}}
{{--                                        </option>--}}
{{--                                    @endforeach--}}

{{--                                </select>--}}


{{--                                @error('city_id') <span class="text-danger error">{{ $message }}</span>@enderror--}}

{{--                            </div>--}}

{{--                        </div>--}}



                    </div>
              <br>
              <br>
                    @if($user->role =="owner" ||$user->role =="dalal")
                    <div class="row">

                        <label for="attachment" class="form-label">المرفق</label>

                        <input type="file" name="attachment" class="form-control mt-3">

                        @if(isset($user->attachment))
                            <a href="{{ $user->attachment }}" target="_blank" class="d-block mt-2 text-primary">عرض المرفق الحالي</a>
                        @endif

                    </div>
                    @endif
                    <br>
                    <br>
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="btn btn-secondary">رجوع</button>
                            <button type="submit" class="btn btn-success">حفظ</button>
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

            // تحميل المحافظات عند اختيار المنطقة
            $('#region_id').on('change', function () {
                let regionId = $(this).val();
                $('#governorate_id').empty().append('<option value="">تحميل...</option>');
                // $('#city_id').empty().append('<option value="">اختر</option>');

                if (regionId) {
                    $.get('/get-governorates/' + regionId, function (data) {
                        $('#governorate_id').empty().append('<option value="">اختر</option>');
                        $.each(data, function (i, item) {
                            $('#governorate_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                    });
                }
            });
            //
            // // تحميل المدن عند اختيار المحافظة
            // $('#governorate_id').on('change', function () {
            //     let govId = $(this).val();
            //     $('#city_id').empty().append('<option value="">تحميل...</option>');
            //
            //     if (govId) {
            //         $.get('/get-cities/' + govId, function (data) {
            //             $('#city_id').empty().append('<option value="">اختر</option>');
            //             $.each(data, function (i, item) {
            //                 $('#city_id').append('<option value="' + item.id + '">' + item.name + '</option>');
            //             });
            //         });
            //     }
            // });

            // عند تحميل الصفحة إذا في old value للمنطقة والمحافظة والمدينة
            if (oldRegionId && !$('#governorate_id option:selected').val()) {
                $.get('/get-governorates/' + oldRegionId, function (data) {
                    $('#governorate_id').empty().append('<option value="">اختر</option>');
                    $.each(data, function (i, item) {
                        let selected = (item.id == oldGovernorateId) ? 'selected' : '';
                        $('#governorate_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                    });

                    // if (oldGovernorateId) {
                    //     $.get('/get-cities/' + oldGovernorateId, function (data) {
                    //         $('#city_id').empty().append('<option value="">اختر</option>');
                    //         $.each(data, function (i, item) {
                    //             let selected = (item.id == oldCityId) ? 'selected' : '';
                    //             $('#city_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                    //         });
                    //     });
                    // }
                });
            }
        });
    </script>





@endsection
