@extends('admin.layouts.master')

@section('title')
    {{ __('admin.owner.create_title') }}
@endsection

@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.owner.create_title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
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

    <form action="{{ route('admin.owner.store') }}" method="post">
        @csrf

        {{-- بيانات الصياد --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.owner.profile_tab') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">{{ __('admin.owner.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="owner_type" class="form-label">نوع المالك <span class="text-danger">*</span></label>
                        <select name="owner_type" id="owner_type" class="form-select" required>
                            <option value="fisherman" {{ old('owner_type', 'fisherman') === 'fisherman' ? 'selected' : '' }}>صيّاد (فرد)</option>
                            <option value="company" {{ old('owner_type') === 'company' ? 'selected' : '' }}>مؤسسة / شركة</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">{{ __('admin.owner.phone') }}</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">{{ __('admin.owner.email') }}</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">{{ __('admin.owner.password') }} <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">{{ __('admin.owner.password_confirmation') }} <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">{{ __('admin.owner.status') }} <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>{{ __('admin.status.active') }}</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>{{ __('admin.status.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="region_id" class="form-label">{{ __('admin.owner.region') }}</label>
                        <select name="region_id" id="region_id" class="form-select">
                            <option value="">{{ __('admin.actions.choose') }}</option>
                            @foreach($regions as $r)
                                <option value="{{ $r->id }}" {{ old('region_id') == $r->id ? 'selected' : '' }}>{{ $r->name ?? $r->name_en ?? $r->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="governorate_id" class="form-label">{{ __('admin.owner.governorate') }}</label>
                        <select name="governorate_id" id="governorate_id" class="form-select">
                            <option value="">{{ __('admin.actions.choose') }}</option>
                            @foreach($governorates as $g)
                                <option value="{{ $g->id }}" {{ old('governorate_id') == $g->id ? 'selected' : '' }}>{{ $g->name ?? $g->name_en ?? $g->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="port_id" class="form-label">{{ __('admin.owner.port') }}</label>
                        <select name="port_id" id="port_id" class="form-select">
                            <option value="">{{ __('admin.actions.choose') }}</option>
                            @foreach($ports as $p)
                                <option value="{{ $p->id }}" {{ old('port_id') == $p->id ? 'selected' : '' }}>{{ $p->name ?? $p->name_en ?? $p->id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- الاشتراك والدفع --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.owner.subscription_and_payment') }}</h5>
            </div>
            <div class="card-body">
                <div class="form-check mb-3">
                    <input type="hidden" name="add_subscription" value="0">
                    <input type="checkbox" name="add_subscription" id="add_subscription" value="1" class="form-check-input" {{ old('add_subscription', true) ? 'checked' : '' }}>
                    <label for="add_subscription" class="form-check-label">{{ __('admin.owner.add_subscription_now') }}</label>
                </div>

                <div id="subscription_fields">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="package_id" class="form-label">{{ __('admin.subscriptions.package') }} <span class="text-danger">*</span></label>
                            <select name="package_id" id="package_id" class="form-select">
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                @foreach($packages as $p)
                                    <option value="{{ $p->id }}" data-price="{{ $p->price }}" {{ old('package_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} — {{ number_format($p->price ?? 0, 2) }} ({{ $p->duration_type ?? 'monthly' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">{{ __('admin.subscriptions.start_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', now()->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="coupon_code" class="form-label">{{ __('admin.coupons.coupon_code') }}</label>
                        <div class="input-group">
                            <input type="text" name="coupon_code" id="coupon_code" class="form-control text-uppercase" value="{{ old('coupon_code') }}" placeholder="{{ __('admin.coupons.code') }}" maxlength="64">
                        </div>
                        <small class="text-muted">{{ __('admin.coupons.apply_coupon') }}</small>
                    </div>
                    <div class="form-check mb-3">
                        <input type="hidden" name="pay_cash" value="0">
                        <input type="checkbox" name="pay_cash" id="pay_cash" value="1" class="form-check-input" {{ old('pay_cash', true) ? 'checked' : '' }}>
                        <label for="pay_cash" class="form-check-label">{{ __('admin.owner.pay_cash_to_admin') }}</label>
                    </div>
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">{{ __('admin.owner.payment_notes') }}</label>
                        <input type="text" name="payment_notes" id="payment_notes" class="form-control" value="{{ old('payment_notes') }}" placeholder="{{ __('admin.owner.payment_cash_to_admin') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ __('admin.owner.save_and_register') }}</button>
            <a href="{{ route('admin.owner.index') }}" class="btn btn-secondary">{{ __('admin.actions.cancel') }}</a>
        </div>
    </form>
@endsection

@section('script')
<script>
    document.getElementById('add_subscription').addEventListener('change', function() {
        var el = document.getElementById('subscription_fields');
        el.style.display = this.checked ? 'block' : 'none';
        document.querySelector('[name="package_id"]').required = this.checked;
        document.querySelector('[name="start_date"]').required = this.checked;
    });
    document.querySelector('[name="package_id"]').required = document.getElementById('add_subscription').checked;
    document.querySelector('[name="start_date"]').required = document.getElementById('add_subscription').checked;
</script>
@endsection
