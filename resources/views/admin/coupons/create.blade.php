@extends('admin.layouts.master')
@section('title')
    {{ __('admin.coupons.create.title') }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.coupons.create.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.actions.cancel') }}
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
            <form action="{{ route('admin.coupons.store') }}" method="post">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">{{ __('admin.coupons.name') }}</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" maxlength="255" placeholder="{{ __('admin.coupons.name_placeholder') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="code" class="form-label">{{ __('admin.coupons.code') }} <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control text-uppercase" value="{{ old('code') }}" required maxlength="64" placeholder="SUMMER20">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="type" class="form-label">{{ __('admin.coupons.type') }} <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="percentage" {{ old('type', 'percentage') == 'percentage' ? 'selected' : '' }}>{{ __('admin.coupons.percentage') }}</option>
                            <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>{{ __('admin.coupons.fixed') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="value" class="form-label">{{ __('admin.coupons.value') }} <span class="text-danger">*</span></label>
                        <input type="number" name="value" id="value" class="form-control" value="{{ old('value', 0) }}" min="0" step="0.01" required>
                        <small class="text-muted" id="value_hint">{{ __('admin.coupons.percentage') }}: 1-100</small>
                    </div>
                </div>
                <div class="row mb-3" id="discount_preview_section">
                    <div class="col-12">
                        <div class="card border-primary bg-light">
                            <div class="card-body py-3">
                                <h6 class="card-title text-primary mb-2">{{ __('admin.coupons.discount_preview') }}</h6>
                                <p class="text-muted small mb-2">{{ __('admin.coupons.preview_hint') }}</p>
                                <div class="row align-items-end g-2">
                                    <div class="col-auto">
                                        <label for="preview_amount" class="form-label small mb-0">{{ __('admin.coupons.sample_amount') }} ({{ __('admin.units.sar') }})</label>
                                        <input type="number" id="preview_amount" class="form-control form-control-sm" value="1000" min="0" step="1" style="width: 120px;">
                                    </div>
                                    <div class="col-auto">
                                        <div class="mb-0">
                                            <span class="text-muted small">{{ __('admin.coupons.discount_amount') }}:</span>
                                            <strong id="preview_discount" class="text-danger">0.00</strong> {{ __('admin.units.sar') }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <span class="text-muted small">{{ __('admin.coupons.amount_after_discount') }}:</span>
                                        <strong id="preview_final" class="text-success">0.00</strong> {{ __('admin.units.sar') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usage_limit" class="form-label">{{ __('admin.coupons.usage_limit') }}</label>
                        <input type="number" name="usage_limit" id="usage_limit" class="form-control" value="{{ old('usage_limit') }}" min="1" placeholder="{{ __('admin.coupons.unlimited') }}">
                        <small class="text-muted">{{ __('admin.coupons.unlimited') }}: {{ __('admin.filters.leave_empty') }}</small>
                    </div>
                    <div class="col-md-6">
                        <label for="valid_from" class="form-label">{{ __('admin.coupons.valid_from') }}</label>
                        <input type="datetime-local" name="valid_from" id="valid_from" class="form-control" value="{{ old('valid_from') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="valid_until" class="form-label">{{ __('admin.coupons.valid_until') }}</label>
                        <input type="datetime-local" name="valid_until" id="valid_until" class="form-control" value="{{ old('valid_until') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">{{ __('admin.coupons.package_ids') }}</label>
                        <p class="text-muted small mb-2">{{ __('admin.coupons.all_packages') }} ({{ __('admin.filters.leave_empty') ?? 'اتركه فارغاً' }}) / {{ __('admin.coupons.specific_packages') }}</p>
                        <div class="border rounded p-3 bg-light">
                            @forelse($packages as $pkg)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="package_ids[]" id="pkg_{{ $pkg->id }}" value="{{ $pkg->id }}" {{ in_array($pkg->id, old('package_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pkg_{{ $pkg->id }}">{{ $pkg->name }} ({{ number_format($pkg->effective_price, 2) }} {{ __('admin.units.sar') }})</label>
                                </div>
                            @empty
                                <span class="text-muted">{{ __('admin.subscription_packages.no_packages') ?? 'لا توجد باقات' }}</span>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="description_ar" class="form-label">{{ __('admin.coupons.description_ar') }}</label>
                        <textarea name="description_ar" id="description_ar" class="form-control" rows="2" maxlength="500">{{ old('description_ar') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="description_en" class="form-label">{{ __('admin.coupons.description_en') }}</label>
                        <textarea name="description_en" id="description_en" class="form-control" rows="2" maxlength="500">{{ old('description_en') }}</textarea>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">{{ __('admin.coupons.is_active') }}</label>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('admin.coupons.add') }}</button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">{{ __('admin.actions.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
    <script>
        (function() {
            var typeEl = document.getElementById('type');
            var valueEl = document.getElementById('value');
            var previewAmountEl = document.getElementById('preview_amount');
            var previewDiscountEl = document.getElementById('preview_discount');
            var previewFinalEl = document.getElementById('preview_final');
            var sar = @json(__('admin.units.sar'));

            function updateHint() {
                var hint = document.getElementById('value_hint');
                if (hint) hint.textContent = typeEl.value === 'percentage' ? '{{ __("admin.coupons.percentage") }}: 1-100' : '{{ __("admin.coupons.fixed") }}';
            }

            function updatePreview() {
                var amount = parseFloat(previewAmountEl.value) || 0;
                var type = typeEl.value;
                var value = parseFloat(valueEl.value) || 0;
                var discount = 0;
                if (type === 'percentage') {
                    value = Math.min(100, Math.max(0, value));
                    discount = amount * (value / 100);
                } else {
                    discount = Math.min(amount, Math.max(0, value));
                }
                var finalAmount = Math.max(0, amount - discount);
                previewDiscountEl.textContent = discount.toFixed(2);
                previewFinalEl.textContent = finalAmount.toFixed(2);
            }

            if (typeEl) typeEl.addEventListener('change', function() { updateHint(); updatePreview(); });
            if (valueEl) valueEl.addEventListener('input', updatePreview);
            if (previewAmountEl) previewAmountEl.addEventListener('input', updatePreview);
            updateHint();
            updatePreview();
        })();
    </script>
    @endpush
@endsection
