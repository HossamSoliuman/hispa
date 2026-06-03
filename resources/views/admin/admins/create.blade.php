@extends('admin.layouts.master')
@section('title')
    {{ __('admin.admins.add_new_title') }}
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
                <li class="breadcrumb-item"><a href="#">{{ __('admin.admins.title') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.admins.add_new_title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.admins.add_new_title') }}</h1>
        </div>

    </div>
    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">
                <form action="{{ route('admin.admins.store') }}" id="createForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row gy-4">
                        {{-- الاسم --}}
                        <div class="col-md-3">
                            <label for="name" class="form-label fw-bold">{{ __('admin.admins.name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- البريد الإلكتروني --}}
                        <div class="col-md-3">
                            <label for="email" class="form-label fw-bold">{{ __('admin.admins.email') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- رقم الهاتف --}}
                        <div class="col-md-3">
                            <label for="phone" class="form-label fw-bold">{{ __('admin.admins.phone') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- الحالة --}}
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                                <label class="form-check-label fw-bold" for="status">{{ __('admin.admins.activate') }}</label>
                            </div>
                        </div>

                        {{-- كلمة المرور --}}
                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ __('admin.admins.password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required>
                            @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- تأكيد كلمة المرور --}}
                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ __('admin.admins.confirm_password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                            @error('password_confirmation') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- صلاحيات المشرف --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('admin.admins.permissions') }} <span class="text-danger">*</span></label>
                            <select name="roles_name[]" class="form-control" multiple required>
                                @foreach($roles as $key => $role)
                                    <option value="{{ $key }}">{{ $role }}</option>
                                @endforeach
                            </select>
                            @error('roles_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">{{ __('admin.actions.back') }}</a>
                        <button type="submit" class="btn btn-success px-4">{{ __('admin.actions.save') }}</button>
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

@endsection
