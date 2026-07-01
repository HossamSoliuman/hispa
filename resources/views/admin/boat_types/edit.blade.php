@extends('admin.layouts.master')
@section('title')
    {{ display_string(__('admin.boat_types.edit'), 'تعديل نوع القارب') }} - {{ $data->name }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ display_string(__('admin.boat_types.edit'), 'تعديل نوع القارب') }}: {{ $data->name }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.boat_types.show', $data->id) }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ display_string(__('admin.boat_types.show_title'), 'تفاصيل نوع القارب') }}
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.boat_types.update', $data->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name_ar" class="form-label">{{ display_string(__('admin.boat_types.name_ar'), 'الاسم بالعربية') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" id="name_ar" class="form-control" value="{{ old('name_ar', $data->name_ar) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="name_en" class="form-label">{{ display_string(__('admin.boat_types.name_en'), 'الاسم بالإنجليزية') }}</label>
                        <input type="text" name="name_en" id="name_en" class="form-control" value="{{ old('name_en', $data->name_en) }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status" class="form-label">{{ display_string(__('admin.boat_types.status'), 'الحالة') }}</label>
                        <select name="status" id="status" class="form-select">
                            <option value="1" {{ old('status', $data->status) == 1 ? 'selected' : '' }}>{{ __('admin.status.active') }}</option>
                            <option value="0" {{ old('status', $data->status) == 0 ? 'selected' : '' }}>{{ __('admin.status.inactive') }}</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('admin.actions.save') }}</button>
                    <a href="{{ route('admin.boat_types.show', $data->id) }}" class="btn btn-secondary">{{ __('admin.actions.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
