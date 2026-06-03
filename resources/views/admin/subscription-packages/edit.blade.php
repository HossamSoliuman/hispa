@extends('admin.layouts.master')
@section('title')
    {{ __('admin.subscription_packages.edit.title') }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.subscription_packages.edit.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.subscription-packages.show', $subscriptionPackage->id) }}" class="btn btn-outline-primary btn-equal">
                <i class="bi bi-eye"></i> {{ __('admin.actions.view') }}
            </a>
            <a href="{{ route('admin.subscription-packages.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.actions.cancel') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.subscription-packages.update', $subscriptionPackage->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name_ar" class="form-label">{{ __('admin.subscription_packages.name_ar') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" id="name_ar" class="form-control" value="{{ old('name_ar', $subscriptionPackage->name_ar) }}" required maxlength="255">
                    </div>
                    <div class="col-md-6">
                        <label for="name_en" class="form-label">{{ __('admin.subscription_packages.name_en') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" id="name_en" class="form-control" value="{{ old('name_en', $subscriptionPackage->name_en) }}" required maxlength="255">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="boats_count" class="form-label">{{ __('admin.subscription_packages.boats_count') }} <span class="text-danger">*</span></label>
                        <input type="number" name="boats_count" id="boats_count" class="form-control" value="{{ old('boats_count', $subscriptionPackage->boats_count) }}" min="1" required>
                    </div>
                    <div class="col-md-6">
                        <label for="original_price" class="form-label">{{ __('admin.subscription_packages.original_price') }} <span class="text-danger">*</span></label>
                        <input type="number" name="original_price" id="original_price" class="form-control" value="{{ old('original_price', $subscriptionPackage->original_price) }}" min="0" step="0.01" required placeholder="{{ __('admin.subscription_packages.original_price_placeholder') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="price" class="form-label">{{ __('admin.subscription_packages.offer_price') }}</label>
                        <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $subscriptionPackage->price) }}" min="0" step="0.01" placeholder="{{ __('admin.subscription_packages.offer_price_placeholder') }}">
                        <small class="text-muted">{{ __('admin.subscription_packages.offer_price_hint') }}</small>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="duration_type" class="form-label">{{ __('admin.subscription_packages.duration_type') }} <span class="text-danger">*</span></label>
                        <select name="duration_type" id="duration_type" class="form-select" required>
                            <option value="monthly" {{ old('duration_type', $subscriptionPackage->duration_type) == 'monthly' ? 'selected' : '' }}>{{ __('admin.subscription_packages.duration_types.monthly') }}</option>
                            <option value="quarterly" {{ old('duration_type', $subscriptionPackage->duration_type) == 'quarterly' ? 'selected' : '' }}>{{ __('admin.subscription_packages.duration_types.quarterly') }}</option>
                            <option value="yearly" {{ old('duration_type', $subscriptionPackage->duration_type) == 'yearly' ? 'selected' : '' }}>{{ __('admin.subscription_packages.duration_types.yearly') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="sort_order" class="form-label">{{ __('admin.subscription_packages.sort_order') }}</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $subscriptionPackage->sort_order) }}" min="0">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('admin.subscription_packages.feature_ar') }}</label>
                        <div id="features_ar_wrapper" class="package-features-wrapper">
                            @php
                                $featuresAr = old('feature_ar', $subscriptionPackage->feature_ar ?? []);
                                $featuresAr = is_array($featuresAr) ? $featuresAr : [''];
                                if (empty($featuresAr)) {
                                    $featuresAr = [''];
                                }
                            @endphp
                            @foreach($featuresAr as $index => $feature)
                                <div class="input-group mb-2 feature-row-ar align-items-center">
                                    <input type="text" name="feature_ar[]" class="form-control" value="{{ $feature }}" placeholder="{{ __('admin.subscription_packages.feature_placeholder') }}">
                                    <button type="button" class="btn btn-feature-remove btn-remove-feature-ar" title="{{ __('admin.actions.delete') }}" @if($loop->first) style="display:none" @endif>
                                        <i class="bi bi-dash-lg"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('admin.subscription_packages.feature_en') }}</label>
                        <div id="features_en_wrapper" class="package-features-wrapper">
                            @php
                                $featuresEn = old('feature_en', $subscriptionPackage->feature_en ?? []);
                                $featuresEn = is_array($featuresEn) ? $featuresEn : [''];
                                if (empty($featuresEn)) {
                                    $featuresEn = [''];
                                }
                            @endphp
                            @foreach($featuresEn as $index => $feature)
                                <div class="input-group mb-2 feature-row-en align-items-center">
                                    <input type="text" name="feature_en[]" class="form-control" value="{{ $feature }}" placeholder="{{ __('admin.subscription_packages.feature_placeholder_en') }}">
                                    <button type="button" class="btn btn-feature-remove btn-remove-feature-en" title="{{ __('admin.actions.delete') }}" @if($loop->first) style="display:none" @endif>
                                        <i class="bi bi-dash-lg"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-feature-add" id="btn_add_feature">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('admin.subscription_packages.add_feature') }}
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check mb-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" {{ old('is_active', $subscriptionPackage->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">{{ __('admin.subscription_packages.active') }}</label>
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" class="form-check-input" {{ old('is_featured', $subscriptionPackage->is_featured) ? 'checked' : '' }}>
                        <label for="is_featured" class="form-check-label">{{ __('admin.subscription_packages.is_featured') }}</label>
                        <small class="text-muted d-block">{{ __('admin.subscription_packages.is_featured_hint') }}</small>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('admin.actions.save') }}</button>
                    <a href="{{ route('admin.subscription-packages.index') }}" class="btn btn-secondary">{{ __('admin.actions.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .btn-feature-add {
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 0.9rem;
            font-weight: 500;
            border: 1px dashed var(--bs-success);
            color: var(--bs-success);
            background: rgba(25, 135, 84, 0.06);
            border-radius: 0.375rem;
            transition: all 0.2s;
        }
        .btn-feature-add:hover {
            background: rgba(25, 135, 84, 0.12);
            border-color: var(--bs-success);
            color: var(--bs-success);
        }
        /* صف الوصف: محاذاة الحقل مع زر الحذف */
        .package-features-wrapper .input-group {
            display: flex;
            align-items: stretch;
            gap: 0;
        }
        .package-features-wrapper .input-group .form-control {
            flex: 1 1 auto;
            min-width: 0;
            border-radius: 0.375rem 0 0 0.375rem;
            border-right: none;
        }
        .package-features-wrapper .input-group .btn-feature-remove {
            width: 2.25rem;
            min-width: 2.25rem;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--bs-danger);
            border-left: none;
            border-radius: 0 0.375rem 0.375rem 0;
            color: #fff;
            background: var(--bs-danger);
            font-size: 1rem;
            transition: opacity 0.2s, background 0.2s;
        }
        .package-features-wrapper .input-group .btn-feature-remove:hover {
            color: #fff;
            background: #b02a37;
            border-color: #b02a37;
        }
        .package-features-wrapper .input-group .form-control:focus {
            border-right: none;
            box-shadow: none;
        }
        .package-features-wrapper .input-group .form-control:focus + .btn-feature-remove {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.2);
        }
    </style>
@endsection

@section('script')
    <script>
        (function() {
            var placeholders = {
                ar: @json(is_string(__('admin.subscription_packages.feature_placeholder')) ? __('admin.subscription_packages.feature_placeholder') : 'أدخل الميزة أو الوصف'),
                en: @json(is_string(__('admin.subscription_packages.feature_placeholder_en')) ? __('admin.subscription_packages.feature_placeholder_en') : 'Enter feature or description')
            };
            var removeLabel = @json(is_string(__('admin.actions.delete')) ? __('admin.actions.delete') : 'حذف');

            function addOneRow(wrapperSelector, rowClass, inputName, placeholder) {
                var wrapper = document.querySelector(wrapperSelector);
                if (!wrapper) return null;
                var ph = (typeof placeholder === 'string') ? placeholder : (placeholder && placeholder[0]) || '';
                var div = document.createElement('div');
                div.className = 'input-group mb-2 ' + rowClass + ' align-items-center';
                div.innerHTML = '<input type="text" name="' + inputName + '" class="form-control" placeholder="' + (ph.replace(/"/g, '&quot;')) + '">' +
                    '<button type="button" class="btn btn-feature-remove btn-remove" title="' + (String(removeLabel).replace(/"/g, '&quot;')) + '"><i class="bi bi-dash-lg"></i></button>';
                wrapper.appendChild(div);
                return div;
            }

            function syncRemoveButtons() {
                var arRows = document.querySelectorAll('#features_ar_wrapper .feature-row-ar');
                var enRows = document.querySelectorAll('#features_en_wrapper .feature-row-en');
                [arRows, enRows].forEach(function(rows) {
                    for (var i = 0; i < rows.length; i++) {
                        var btn = rows[i].querySelector('.btn-remove');
                        if (btn) btn.style.display = rows.length > 1 ? '' : 'none';
                    }
                });
            }

            function addFeatureRowBoth() {
                addOneRow('#features_ar_wrapper', 'feature-row-ar', 'feature_ar[]', placeholders.ar);
                addOneRow('#features_en_wrapper', 'feature-row-en', 'feature_en[]', placeholders.en);
                syncRemoveButtons();
            }

            function removeRowByIndex(index) {
                var arRows = document.querySelectorAll('#features_ar_wrapper .feature-row-ar');
                var enRows = document.querySelectorAll('#features_en_wrapper .feature-row-en');
                if (arRows.length <= 1 || enRows.length <= 1) return;
                if (index >= 0 && index < arRows.length) arRows[index].remove();
                if (index >= 0 && index < enRows.length) enRows[index].remove();
                syncRemoveButtons();
            }

            function init() {
                var btnAdd = document.getElementById('btn_add_feature');
                if (btnAdd) btnAdd.addEventListener('click', addFeatureRowBoth);

                var wrapAr = document.getElementById('features_ar_wrapper');
                var wrapEn = document.getElementById('features_en_wrapper');
                if (wrapAr) {
                    wrapAr.addEventListener('click', function (e) {
                        var btn = e.target.closest('.btn-remove-feature-ar') || e.target.closest('.btn-remove');
                        if (!btn) return;
                        var row = btn.closest('.feature-row-ar');
                        if (!row) return;
                        var rows = wrapAr.querySelectorAll('.feature-row-ar');
                        if (rows.length > 1) {
                            var index = Array.prototype.indexOf.call(rows, row);
                            removeRowByIndex(index);
                        }
                    });
                }
                if (wrapEn) {
                    wrapEn.addEventListener('click', function (e) {
                        var btn = e.target.closest('.btn-remove-feature-en') || e.target.closest('.btn-remove');
                        if (!btn) return;
                        var row = btn.closest('.feature-row-en');
                        if (!row) return;
                        var rows = wrapEn.querySelectorAll('.feature-row-en');
                        if (rows.length > 1) {
                            var index = Array.prototype.indexOf.call(rows, row);
                            removeRowByIndex(index);
                        }
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
@endsection
