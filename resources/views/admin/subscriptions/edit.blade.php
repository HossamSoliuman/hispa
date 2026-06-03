@extends('admin.layouts.master')
@section('title')
    {{ __('admin.subscriptions.edit.title') }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.subscriptions.edit.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="btn btn-outline-primary btn-equal">
                <i class="bi bi-eye"></i> {{ __('admin.actions.view') }}
            </a>
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
            <form action="{{ route('admin.subscriptions.update', $subscription->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('admin.subscriptions.user') }}</label>
                        <p class="form-control-plaintext">{{ $subscription->user->name ?? '--' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label for="package_id" class="form-label">{{ __('admin.subscriptions.package') }} <span class="text-danger">*</span></label>
                        <select name="package_id" id="package_id" class="form-select" required>
                            @foreach($packages as $p)
                                <option value="{{ $p->id }}" {{ old('package_id', $subscription->package_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">{{ __('admin.subscriptions.start_date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $subscription->start_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">{{ __('admin.subscriptions.end_date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $subscription->end_date?->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status" class="form-label">{{ __('admin.subscriptions.status') }} <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="active" {{ old('status', $subscription->status) == 'active' ? 'selected' : '' }}>{{ __('admin.subscriptions.active') }}</option>
                            <option value="expired" {{ old('status', $subscription->status) == 'expired' ? 'selected' : '' }}>{{ __('admin.subscriptions.expired') }}</option>
                            <option value="trial" {{ old('status', $subscription->status) == 'trial' ? 'selected' : '' }}>{{ __('admin.subscriptions.trial') }}</option>
                            <option value="suspended" {{ old('status', $subscription->status) == 'suspended' ? 'selected' : '' }}>{{ __('admin.subscriptions.suspended') }}</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('admin.actions.save') }}</button>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">{{ __('admin.actions.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
