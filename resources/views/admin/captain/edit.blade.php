@extends('admin.layouts.master')
@section('title')
    {{ __('admin.captains.edit.page_header') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/tag-it/css/jquery.tagit.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/summernote/dist/summernote-lite.css') }}" rel="stylesheet">
    <style> label.error { color: red; font-weight: bold; margin-top: 5px; display: block; } </style>
@endsection
@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.captain.index') }}">{{ __('admin.captains.breadcrumb') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.captains.edit.page_header') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.captains.edit.page_header') }}</h1>
        </div>
    </div>
    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">
                <form action="{{ route('admin.captain.update', $data->id) }}" id="editForm" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="owner_id" class="form-label">{{ __('admin.captains.form.owner') }}<span class="text-danger">*</span></label>
                            <select name="owner_id" class="form-control" required id="owner_id">
                                <option value="">{{ __('admin.captains.placeholders.choose') }}</option>
                                @foreach ($owners as $owner)
                                    <option value="{{ $owner->id }}" {{ old('owner_id', $data->owner_id) == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                                @endforeach
                            </select>
                            @error('owner_id')<span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="boat_id" class="form-label">{{ __('admin.captains.form.boat') }}<span class="text-danger">*</span></label>
                            <select name="boat_id" class="form-control" required id="boat_id">
                                <option value="">{{ __('admin.captains.placeholders.choose') }}</option>
                                @foreach ($boats as $boat)
                                    <option value="{{ $boat->id }}" {{ old('boat_id', $data->boat_id) == $boat->id ? 'selected' : '' }}>{{ $boat->name }}</option>
                                @endforeach
                            </select>
                            @error('boat_id')<span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('admin.captains.form.name') }}<span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $data->name) }}" class="form-control" required>
                            @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('admin.captains.form.email') }}<span class="text-danger">*</span></label>
                            <input type="text" name="email" value="{{ old('email', $data->email) }}" class="form-control" required>
                            @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('admin.captains.form.logo') }}</label>
                            <input type="file" name="logo" class="form-control">
                            @if($data->logo)
                                <br><img src="{{ asset($data->logo) }}" alt="logo" style="max-width: 120px; max-height: 80px;"><br>
                                <small class="text-muted">{{ __('admin.captains.edit.current_image') }}</small>
                            @endif
                            @error('logo')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('admin.captains.form.phone') }}<span class="text-danger">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $data->phone) }}" class="form-control" required>
                            @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="region_id" class="form-label">{{ __('admin.captains.form.region') }}<span class="text-danger">*</span></label>
                                <select name="region_id" class="form-control" required id="region_id">
                                    <option value="">{{ __('admin.captains.placeholders.choose') }}</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id', $data->region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                                @error('region_id')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="governorate_id" class="form-label">{{ __('admin.captains.form.governorate') }}<span class="text-danger">*</span></label>
                                <select name="governorate_id" class="form-control" required id="governorate_id">
                                    <option value="">{{ __('admin.captains.placeholders.choose') }}</option>
                                    @if($data->governorate_id)
                                        <option value="{{ $data->governorate_id }}" selected>{{ $data->governorate?->name }}</option>
                                    @endif
                                </select>
                                @error('governorate_id')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch" style="margin-top: 35px">
                                <input type="checkbox" name="status" class="form-check-input" value="1" {{ old('status', $data->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">{{ __('admin.captains.form.status') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('admin.captain.index') }}" class="btn btn-secondary">{{ __('admin.captains.buttons.back') }}</a>
                            <button type="submit" class="btn btn-success">{{ __('admin.captains.buttons.save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('dashboard/assets/js/jquery.validate.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>
    <script>
        $("#editForm").validate();
        $('#region_id').on('change', function() {
            var regionId = $(this).val();
            $('#governorate_id').empty().append('<option value="">{{ __('admin.captains.placeholders.choose') }}</option>');
            if (regionId) {
                var govUrl = "{{ route('admin.getGovernorates', ['region_id' => '__ID__']) }}".replace('__ID__', regionId);
                $.get(govUrl, function(data) {
                    $.each(data, function(i, item) {
                        $('#governorate_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                });
            }
        });
    </script>
@endsection
