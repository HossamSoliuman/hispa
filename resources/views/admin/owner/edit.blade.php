@extends('admin.layouts.master')

@section('title')
    {{ __('admin.owner.edit_title') }} - {{ $owner->name }}
@endsection

@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.owner.edit_title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.owner.show', $owner->id) }}" class="btn btn-outline-info btn-equal">
                <i class="bi bi-eye"></i> {{ __('admin.actions.view') }}
            </a>
            <a href="{{ route('admin.owner.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.menu.owners') }}
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

    <form action="{{ route('admin.owner.update', $owner->id) }}" method="post">
        @csrf
        @method('PUT')

        {{-- Profile --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.owner.profile_tab') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">{{ __('admin.owner.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $owner->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="owner_type" class="form-label">نوع المالك <span class="text-danger">*</span></label>
                        <select name="owner_type" id="owner_type" class="form-select" required>
                            <option value="fisherman" {{ old('owner_type', $owner->owner_type ?? 'fisherman') === 'fisherman' ? 'selected' : '' }}>صيّاد (فرد)</option>
                            <option value="company" {{ old('owner_type', $owner->owner_type ?? 'fisherman') === 'company' ? 'selected' : '' }}>مؤسسة / شركة</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">{{ __('admin.owner.phone') }}</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $owner->phone) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">{{ __('admin.owner.email') }}</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $owner->email) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">{{ __('admin.owner.status') }} <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="1" {{ old('status', $owner->status) == 1 ? 'selected' : '' }}>{{ __('admin.status.active') }}</option>
                            <option value="0" {{ old('status', $owner->status) == 0 ? 'selected' : '' }}>{{ __('admin.status.inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Current subscription (optional edit) --}}
        @php
            $currentSub = $owner->activeSubscription ?? $owner->subscriptions->first();
        @endphp
        @if($currentSub && $packages->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('admin.owner.current_subscription') }}</h5>
                    <a href="{{ route('admin.subscriptions.edit', $currentSub->id) }}" class="btn btn-sm btn-outline-primary">
                        {{ __('admin.owner.change_subscription') }}
                    </a>
                </div>
                <div class="card-body">
                    <input type="hidden" name="subscription_id" value="{{ $currentSub->id }}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="package_id" class="form-label">{{ __('admin.subscriptions.package') }} <span class="text-danger">*</span></label>
                            <select name="package_id" id="package_id" class="form-select" required>
                                @foreach($packages as $p)
                                    <option value="{{ $p->id }}" data-duration="{{ $p->duration_type ?? 'monthly' }}" {{ old('package_id', $currentSub->package_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="subscription_status" class="form-label">{{ __('admin.subscriptions.status') }} <span class="text-danger">*</span></label>
                            <select name="subscription_status" id="subscription_status" class="form-select" required>
                                <option value="active" {{ old('subscription_status', $currentSub->status) == 'active' ? 'selected' : '' }}>{{ __('admin.subscriptions.active') }}</option>
                                <option value="expired" {{ old('subscription_status', $currentSub->status) == 'expired' ? 'selected' : '' }}>{{ __('admin.subscriptions.expired') }}</option>
                                <option value="trial" {{ old('subscription_status', $currentSub->status) == 'trial' ? 'selected' : '' }}>{{ __('admin.subscriptions.trial') }}</option>
                                <option value="suspended" {{ old('subscription_status', $currentSub->status) == 'suspended' ? 'selected' : '' }}>{{ __('admin.subscriptions.suspended') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">{{ __('admin.subscriptions.start_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $currentSub->start_date?->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">{{ __('admin.subscriptions.end_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $currentSub->end_date?->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($owner->subscriptions->isEmpty() && $packages->isNotEmpty())
            <div class="card mb-4">
                <div class="card-body">
                    <p class="text-muted mb-2">{{ __('admin.owner.no_subscriptions') }}</p>
                    <a href="{{ route('admin.subscriptions.create') }}?user_id={{ $owner->id }}" class="btn btn-primary">{{ __('admin.subscriptions.create_new') }}</a>
                </div>
            </div>
        @endif

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ __('admin.actions.save') }}</button>
            <a href="{{ route('admin.owner.show', $owner->id) }}" class="btn btn-secondary">{{ __('admin.actions.cancel') }}</a>
        </div>
    </form>
@endsection

@section('script')
<script>
    (function() {
        var startEl = document.getElementById('start_date');
        var endEl = document.getElementById('end_date');
        var packageEl = document.getElementById('package_id');
        if (!startEl || !endEl || !packageEl) return;
        function addDuration(date, duration) {
            var d = new Date(date);
            if (duration === 'yearly') d.setFullYear(d.getFullYear() + 1);
            else if (duration === 'quarterly') d.setMonth(d.getMonth() + 3);
            else d.setMonth(d.getMonth() + 1);
            return d.toISOString().slice(0, 10);
        }
        function updateEndDate() {
            var start = startEl.value;
            var opt = packageEl.options[packageEl.selectedIndex];
            var duration = opt ? (opt.getAttribute('data-duration') || 'monthly') : 'monthly';
            if (start) endEl.value = addDuration(start, duration);
        }
        startEl.addEventListener('change', updateEndDate);
        packageEl.addEventListener('change', updateEndDate);
    })();
</script>
@endsection
