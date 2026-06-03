@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.item_e9c893') }}
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
                <li class="breadcrumb-item"><a href="#">{{ __('owner.menu.profile') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.menu.profile') }}</li>
            </ul>
            <h1 class="page-header mb-0"> {{ __('owner.generated.edit_profile') }}</h1>
        </div>

    </div>

    <div class="row">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center bg-primary text-white p-5 h-80">
                    <div class="mb-4">
                        @if (auth()->user()->logo)
                            <img src="{{ asset(auth()->user()->logo) }}" class="rounded-circle shadow" width="120"
                                height="120" alt="Logo">
                        @else
                            <img src="{{ asset('default-avatar.png') }}" class="rounded-circle shadow" width="120"
                                height="120" alt="Default Logo">
                        @endif
                    </div>

                    <h5 class="mb-1 text-white">{{ auth()->user()->name }}</h5>
                    <p class="mb-2 text-white">{{ auth()->user()->role }}</p>
                    <p>
                        @if (auth()->user()->status)
                            <span class="badge bg-success p-2"><i class="fa fa-clock"></i>
                                {{ __('owner.assets.active') }}</span>
                        @else
                            <span class="badge bg-danger p-2">{{ __('owner.fish.status_inactive') }}</span>
                        @endif
                    </p>
                </div>
                <div class="text-center h-20">
                    <a href="{{ route('owner.profile.edit', auth()->user()->id) }}" class="btn btn-primary rounded-4 m-4">
                        <i class="fa fa-edit mx-2"></i>{{ __('owner.generated.edit_profile') }}</a>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-5 text-start h-100">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-2">
                                <i
                                    class="bi bi-envelope  d-inline-block bg-primary-100 p-2 rounded-3 p-4 fs-4 text-center line-height-100 text-primary"></i>
                            </div>
                            <div class="col-md-10">
                                <p class="my-2">{{ __('owner.generated.email_address') }}</p>
                                <p class="m-0"><strong>{{ auth()->user()->email }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mt-3">
                                <i
                                    class="bi bi-telephone d-inline-block  bg-success-100 p-2 rounded-3 p-4 fs-4 text-center line-height-100 text-success"></i>
                            </div>
                            <div class="col-md-10 mt-3">
                                <p class="my-2">{{ __('owner.generated.phone_number_1') }}</p>
                                <p class="m-0"><strong>{{ auth()->user()->phone ?? '-----' }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mt-3">
                                <i
                                    class="bi bi-pin-fill d-inline-block  bg-warning-100 p-2 rounded-3 p-4 fs-4 text-center line-height-100 text-warning"></i>
                            </div>
                            <div class="col-md-10 mt-3">
                                <p class="my-2">{{ __('owner.generated.region') }}</p>
                                <p class="m-0"><strong>{{ auth()->user()->region?->name ?? '-----' }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mt-3">
                                <i
                                    class="bi bi-pin-map d-inline-block  bg-default-100 p-2 rounded-3 p-4 fs-4 text-center line-height-100 text-default"></i>
                            </div>
                            <div class="col-md-10 mt-3">
                                <p class="my-2">{{ __('owner.generated.governorate') }}</p>
                                <p class="m-0"><strong>{{ auth()->user()->governorate?->name ?? '-----' }}</strong></p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

        </div>
    </div>


    <!-- Modal: Edit Profile -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4">
                <form action="{{ route('owner.profile.update', auth()->user()->id) }}" id="createForm" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title text-white" id="editProfileModalLabel">
                            {{ __('owner.generated.edit_profile') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>{{ __('owner.assets.name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ auth()->user()->name }}" required>
                            </div>

                            <div class="col-md-6">
                                <label>{{ __('owner.generated.phone_number') }}<span class="text-danger">*</span></label>
                                <input type="text" required name="phone" class="form-control"
                                    value="{{ auth()->user()->phone }}">
                            </div>
                            <div class="col-md-6">
                                <label>{{ __('owner.generated.logo') }}({{ __('owner.generated.new_image') }})</label>
                                <input type="file" name="logo" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>{{ __('owner.generated.new_password') }}</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="{{ __('owner.generated.placeholder_password') }}">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="submit"
                            class="btn btn-success px-5">{{ __('owner.generated.save_changes') }}</button>
                    </div>
                </form>
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
                    $.get('/get-governorates/' + regionId, function(data) {
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
                $('#city_id').empty().append(
                    '<option value="">{{ __('owner.dalal.performance.loading') }}</option>');

                if (govId) {
                    $.get('/get-cities/' + govId, function(data) {
                        $('#city_id').empty().append(
                            '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>'
                        );
                        $.each(data, function(i, item) {
                            $('#city_id').append('<option value="' + item.id + '">' + item
                                .name + '</option>');
                        });
                    });
                }
            });
            // تحميل المنافذ عند اختيار المدينة
            $('#city_id').on('change', function() {
                let cityId = $(this).val();
                $('#port_id').empty().append(
                    '<option value="">{{ __('owner.dalal.performance.loading') }}</option>');

                if (cityId) {
                    $.get('/get-ports/' + cityId, function(data) {
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
                $.get('/get-governorates/' + oldRegionId, function(governorates) {
                    $('#governorate_id').empty().append(
                        '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');
                    $.each(governorates, function(i, item) {
                        let selected = (item.id == oldGovernorateId) ? 'selected' : '';
                        $('#governorate_id').append('<option value="' + item.id + '" ' + selected +
                            '>' + item.name + '</option>');
                    });

                    if (oldGovernorateId) {
                        $.get('/get-cities/' + oldGovernorateId, function(cities) {
                            $('#city_id').empty().append(
                                '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>'
                            );
                            $.each(cities, function(i, item) {
                                let selected = (item.id == oldCityId) ? 'selected' : '';
                                $('#city_id').append('<option value="' + item.id + '" ' +
                                    selected + '>' + item.name + '</option>');
                            });

                            // ✅ تحميل المنافذ من المدينة المختارة
                            if (oldCityId) {
                                $.get('/get-ports/' + oldCityId, function(ports) {
                                    $('#port_id').empty().append(
                                        '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>'
                                    );
                                    $.each(ports, function(i, item) {
                                        let selected = (item.id == oldPortId) ?
                                            'selected' : '';
                                        $('#port_id').append('<option value="' +
                                            item.id + '" ' + selected + '>' +
                                            item.name + '</option>');
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
