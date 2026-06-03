@extends('admin.layouts.master')
@section('title')
    {{ __('admin.profile.title') }}
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
                <li class="breadcrumb-item"><a href="#">{{ __('admin.profile.title') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.profile.title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.profile.edit_profile') }}</h1>
        </div>

    </div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Profile Card -->
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-primary text-white text-center rounded-top-4">
                        <h4 class="mb-0 text-white">{{ __('admin.profile.title') }}</h4>
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
                                <p><strong>{{ __('admin.profile.email') }}:</strong> {{ auth()->user()->email }}</p>
                                <p><strong>{{ __('admin.profile.phone') }}:</strong> {{ auth()->user()->phone ?? '---' }}</p>
                                <p><strong>{{ __('admin.profile.status') }}:</strong>
                                    @if(auth()->user()->status)
                                        <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Edit Button -->
                        <button class="btn btn-outline-primary mt-4" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            {{ __('admin.profile.edit_profile') }}
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
                <form action="{{ route('admin.profile.update',$admin->id) }}" id="createForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title text-white" id="editProfileModalLabel">{{ __('admin.profile.edit_profile') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>{{ __('admin.profile.name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                            </div>

                            <div class="col-md-6">
                                <label>{{ __('admin.profile.phone') }}<span class="text-danger">*</span></label>
                                <input type="text" required name="phone" class="form-control" value="{{ auth()->user()->phone }}">
                            </div>
                            <div class="col-md-6">
                                <label>{{ __('admin.profile.logo') }}</label>
                                <input type="file" name="logo" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>{{ __('admin.profile.password') }}</label>
                                <input type="password" name="password" class="form-control" placeholder="{{ __('admin.profile.placeholder_password') }}">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-success px-5">{{ __('admin.profile.save') }}</button>
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
            let oldPortId = '{{ old('port_id') }}';

            $('#region_id').on('change', function() {
            let regionId = $(this).val();
            $('#governorate_id').html('<option value="">تحميل...</option>');
            $('#port_id').html('<option value="">اختر</option>');

            if (regionId) {
                $.get("{{ route('admin.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID', regionId), function(data) {
                    $('#governorate_id').html('<option value="">اختر</option>');
                    $.each(data, function(i, item) {
                        let selected = (item.id == oldGovernorateId) ? 'selected' : '';
                        $('#governorate_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                    });
                    oldGovernorateId = null;
                }).fail(function() {
                    $('#governorate_id').html('<option value="">اختر</option>');
                });
            }
        });

        $('#governorate_id').on('change', function() {
            let govId = $(this).val();
            $('#port_id').html('<option value="">تحميل...</option>');

            if (govId) {
                $.get("{{ route('admin.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', govId), function(data) {
                    $('#port_id').html('<option value="">اختر</option>');
                    $.each(data, function(i, item) {
                        let selected = (item.id == oldPortId) ? 'selected' : '';
                        $('#port_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                    });
                    oldPortId = null;
                }).fail(function() {
                    $('#port_id').html('<option value="">اختر</option>');
                });
            }
        });

        if (oldRegionId) {
            $('#region_id').val(oldRegionId).trigger('change');
        }

         
        });
    </script>
@endsection
