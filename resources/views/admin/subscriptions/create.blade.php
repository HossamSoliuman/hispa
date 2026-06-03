@extends('admin.layouts.master')
@section('title')
    {{ __('admin.subscriptions.create.title') }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.subscriptions.create.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.subscriptions.all') }}
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.subscriptions.store') }}" method="post">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="user_id" class="form-label">{{ __('admin.subscriptions.user') }} <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">{{ __('admin.actions.choose') }}</option>
                            @foreach($fishermen as $f)
                                <option value="{{ $f->id }}" {{ old('user_id') == $f->id ? 'selected' : '' }}>{{ $f->name }} ({{ $f->phone ?? $f->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="package_id" class="form-label">{{ __('admin.subscriptions.package') }} <span class="text-danger">*</span></label>
                        <select name="package_id" id="package_id" class="form-select" required>
                            <option value="">{{ __('admin.actions.choose') }}</option>
                            @foreach($packages as $p)
                                <option value="{{ $p->id }}" {{ old('package_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->duration_type }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">{{ __('admin.subscriptions.start_date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="duration_type" class="form-label">{{ __('admin.subscriptions.duration_type') }}</label>
                        <select name="duration_type" id="duration_type" class="form-select">
                            <option value="">{{ __('admin.subscriptions.use_package_duration') }}</option>
                            <option value="monthly" {{ old('duration_type') == 'monthly' ? 'selected' : '' }}>monthly</option>
                            <option value="quarterly" {{ old('duration_type') == 'quarterly' ? 'selected' : '' }}>quarterly</option>
                            <option value="yearly" {{ old('duration_type') == 'yearly' ? 'selected' : '' }}>yearly</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="hidden" name="is_trial" value="0">
                            <input type="checkbox" name="is_trial" id="is_trial" value="1" class="form-check-input" {{ old('is_trial') ? 'checked' : '' }}>
                            <label for="is_trial" class="form-check-label">{{ __('admin.subscriptions.subscription_type') }} ({{ __('admin.subscriptions.trial') }})</label>
                        </div>
                    </div>
                    <div class="col-md-6" id="wrap_trial_days" style="{{ old('is_trial') ? '' : 'display:none;' }}">
                        <label for="trial_days" class="form-label">{{ __('admin.subscriptions.trial_days') }}</label>
                        <input type="number" name="trial_days" id="trial_days" class="form-control" min="1" max="30" value="{{ old('trial_days', 7) }}">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('admin.subscriptions.add') }}</button>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">{{ __('admin.actions.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
    <script>
        document.getElementById('is_trial').addEventListener('change', function() {
            document.getElementById('wrap_trial_days').style.display = this.checked ? 'block' : 'none';
        });
    </script>
    @endpush
@endsection
