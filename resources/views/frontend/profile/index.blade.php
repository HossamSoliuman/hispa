@extends('frontend.layouts.master')
@section('title')
    الملف الشخصي
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
                <li class="breadcrumb-item"><a href="#">الملف الشخصي</a></li>
                <li class="breadcrumb-item active">الملف الشخصي</li>
            </ul>
            <h1 class="page-header mb-0"> تعديل الملف الشخصي</h1>
        </div>

    </div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Profile Card -->
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-primary text-white text-center rounded-top-4">
                        <h4 class="mb-0 text-white">الملف الشخصي</h4>
                    </div>
                    <div class="card-body text-center">

                        <div class="mb-4">
                            @if(auth()->user()->logo)
                                <img src="{{ asset(auth()->user()->logo) }}" class="rounded-circle shadow" width="120" height="120" alt="Logo">
                            @else
                                <img src="{{ asset('default-avatar.png') }}" class="rounded-circle shadow" width="120" height="120" alt="Default Logo">
                            @endif
                        </div>

                        <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                        <p class="text-muted mb-2">{{ auth()->user()->role }}</p>

                        <div class="row justify-content-center text-start mt-4">
                            <div class="col-md-6">
                                <p><strong>البريد الإلكتروني:</strong> {{ auth()->user()->email }}</p>
                                <p><strong>رقم الهاتف:</strong> {{ auth()->user()->phone ?? '---' }}</p>
                                <p><strong>الحالة:</strong>
                                    @if(auth()->user()->status)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Edit Button -->
                        <button class="btn btn-outline-primary mt-4" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            تعديل الملف الشخصي
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Profile -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4">
                <form action="{{ route('frontend.profile.update',$user->id) }}" id="createForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title text-white" id="editProfileModalLabel">تعديل الملف الشخصي</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>الاسم<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                            </div>

                            <div class="col-md-6">
                                <label>رقم الهاتف<span class="text-danger">*</span></label>
                                <input type="text" required name="phone" class="form-control" value="{{ auth()->user()->phone }}">
                            </div>
                            <div class="col-md-6">
                                <label>شعار (صورة جديدة)</label>
                                <input type="file" name="logo" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>كلمة المرور الجديدة</label>
                                <input type="password" name="password" class="form-control" placeholder="اتركه فارغًا إذا لا تريد تغييره">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-success px-5">حفظ التعديلات</button>
                    </div>
                </form>
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
            let oldCityId = '{{ old('city_id') }}';
            let oldPortId = '{{ old('port_id') }}';

            // تحميل المحافظات عند اختيار المنطقة
            $('#region_id').on('change', function () {
                let regionId = $(this).val();
                $('#governorate_id').empty().append('<option value="">تحميل...</option>');
                $('#city_id').empty().append('<option value="">اختر</option>');

                if (regionId) {
                    $.get('/get-governorates/' + regionId, function (data) {
                        $('#governorate_id').empty().append('<option value="">اختر</option>');
                        $.each(data, function (i, item) {
                            $('#governorate_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                    });
                }
            });

            // تحميل المدن عند اختيار المحافظة
            $('#governorate_id').on('change', function () {
                let govId = $(this).val();
                $('#city_id').empty().append('<option value="">تحميل...</option>');

                if (govId) {
                    $.get('/get-cities/' + govId, function (data) {
                        $('#city_id').empty().append('<option value="">اختر</option>');
                        $.each(data, function (i, item) {
                            $('#city_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                    });
                }
            });
// تحميل المنافذ عند اختيار المدينة
            $('#city_id').on('change', function () {
                let cityId = $(this).val();
                $('#port_id').empty().append('<option value="">تحميل...</option>');

                if (cityId) {
                    $.get('/get-ports/' + cityId, function (data) {
                        $('#port_id').empty().append('<option value="">اختر</option>');
                        $.each(data, function (i, item) {
                            $('#port_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                    });
                }
            });

            // عند تحميل الصفحة إذا في old value للمنطقة والمحافظة والمدينة
            if (oldRegionId && !$('#governorate_id option:selected').val()) {
                $.get('/get-governorates/' + oldRegionId, function (governorates) {
                    $('#governorate_id').empty().append('<option value="">اختر</option>');
                    $.each(governorates, function (i, item) {
                        let selected = (item.id == oldGovernorateId) ? 'selected' : '';
                        $('#governorate_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                    });

                    if (oldGovernorateId) {
                        $.get('/get-cities/' + oldGovernorateId, function (cities) {
                            $('#city_id').empty().append('<option value="">اختر</option>');
                            $.each(cities, function (i, item) {
                                let selected = (item.id == oldCityId) ? 'selected' : '';
                                $('#city_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                            });

                            // ✅ تحميل المنافذ من المدينة المختارة
                            if (oldCityId) {
                                $.get('/get-ports/' + oldCityId, function (ports) {
                                    $('#port_id').empty().append('<option value="">اختر</option>');
                                    $.each(ports, function (i, item) {
                                        let selected = (item.id == oldPortId) ? 'selected' : '';
                                        $('#port_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                                    });
                                });
                            }
                        });
                    }
                });
            }
        });
    </script>





@endsection
