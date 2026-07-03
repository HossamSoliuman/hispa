@extends('admin.layouts.master')
@section('title')
    {{ __('admin.subscription_packages.create.title') ?? __('admin.subscription_packages.add_new') }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.subscription_packages.create.title') ?? __('admin.subscription_packages.add_new') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.subscription-packages.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.actions.cancel') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.subscription-packages.store') }}" method="post">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name_ar" class="form-label">{{ __('admin.subscription_packages.name_ar') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" id="name_ar" class="form-control" value="{{ old('name_ar') }}" required maxlength="255">
                    </div>
                    <div class="col-md-6">
                        <label for="name_en" class="form-label">{{ __('admin.subscription_packages.name_en') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" id="name_en" class="form-control" value="{{ old('name_en') }}" required maxlength="255">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="boats_count" class="form-label">{{ __('admin.subscription_packages.boats_count') }} <span class="text-danger">*</span></label>
                        <input type="number" name="boats_count" id="boats_count" class="form-control" value="{{ old('boats_count', 1) }}" min="1" required>
                        <small class="text-muted">{{ __('admin.subscription_packages.boats_count_hint') }}</small>
                    </div>
                    <div class="col-md-6">
                        <label for="original_price" class="form-label">{{ __('admin.subscription_packages.original_price') }} <span class="text-danger">*</span></label>
                        <input type="number" name="original_price" id="original_price" class="form-control" value="{{ old('original_price') }}" min="0" step="0.01" required placeholder="{{ __('admin.subscription_packages.original_price_placeholder') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="price" class="form-label">{{ __('admin.subscription_packages.offer_price') }}</label>
                        <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" min="0" step="0.01" placeholder="{{ __('admin.subscription_packages.offer_price_placeholder') }}">
                        <small class="text-muted">{{ __('admin.subscription_packages.offer_price_hint') }}</small>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="duration_type" class="form-label">{{ __('admin.subscription_packages.duration_type') }} <span class="text-danger">*</span></label>
                        <select name="duration_type" id="duration_type" class="form-select" required>
                            <option value="monthly" {{ old('duration_type') == 'monthly' ? 'selected' : '' }}>{{ __('admin.subscription_packages.duration_types.monthly') }}</option>
                            <option value="quarterly" {{ old('duration_type') == 'quarterly' ? 'selected' : '' }}>{{ __('admin.subscription_packages.duration_types.quarterly') }}</option>
                            <option value="yearly" {{ old('duration_type', 'monthly') == 'yearly' ? 'selected' : '' }}>{{ __('admin.subscription_packages.duration_types.yearly') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="sort_order" class="form-label">{{ __('admin.subscription_packages.sort_order') }}</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order') }}" min="0">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check mb-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">{{ __('admin.subscription_packages.active') }}</label>
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" class="form-check-input" {{ old('is_featured') ? 'checked' : '' }}>
                        <label for="is_featured" class="form-check-label">{{ __('admin.subscription_packages.is_featured') }}</label>
                        <small class="text-muted d-block">{{ __('admin.subscription_packages.is_featured_hint') }}</small>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('admin.subscription_packages.add') }}</button>
                    <a href="{{ route('admin.subscription-packages.index') }}" class="btn btn-secondary">{{ __('admin.actions.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
