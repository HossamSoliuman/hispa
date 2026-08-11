<div class="d-flex align-items-center mb-3">
    <h4 class="mb-2">{{ __('admin.settings.company_info') }}</h4>
</div>

<div class="card border-0">
    <div class="card-body">
        <form action="{{ route('admin.settings.company') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.company_name_en') }}</label>
                    <input type="text" class="form-control" name="title_en" value="{{ optional($data->where('key', 'title_en')->first())->value ?? '' }}">
                    @error('title_en')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.company_name_ar') }}</label>
                    <input type="text" class="form-control" name="title" value="{{ optional($data->where('key', 'title')->first())->value ?? optional($data->where('key', 'site_name')->first())->value ?? '' }}">
                    @error('title')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.commercial_registration_no') }}</label>
                    <input type="text" class="form-control" name="commercial_registration_no" value="{{ optional($data->where('key', 'commercial_registration_no')->first())->value ?? '' }}">
                    @error('commercial_registration_no')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.agri_record_no') }}</label>
                    <input type="text" class="form-control" name="agri_record_no" value="{{ optional($data->where('key', 'agri_record_no')->first())->value ?? '' }}">
                    @error('agri_record_no')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.email') }}</label>
                    <input type="email" class="form-control" name="email" value="{{ optional($data->where('key', 'email')->first())->value ?? '' }}">
                    @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.phone') }}</label>
                    <input type="text" class="form-control" name="phone" value="{{ optional($data->where('key', 'phone')->first())->value ?? '' }}">
                    @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.address') }}</label>
                    <input type="text" class="form-control" name="address" value="{{ optional($data->where('key', 'address')->first())->value ?? '' }}">
                    @error('address')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.website') }}</label>
                    <input type="text" class="form-control" name="domain" value="{{ optional($data->where('key', 'domain')->first())->value ?? '' }}">
                    @error('domain')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            @php
                $storedBranding = fn (string $key) => optional($data->where('key', $key)->first())->getRawOriginal('value');
                $brandingFields = [
                    [
                        'name' => 'logo',
                        'label' => __('admin.settings.company_logo_light'),
                        'hint' => __('admin.settings.company_logo_light_hint'),
                        'preview' => $platformLogoUrl,
                        'surface' => 'light',
                        'accept' => 'image/png,image/webp,image/jpeg',
                        'max' => '2 MB',
                    ],
                    [
                        'name' => 'logo_dark',
                        'label' => __('admin.settings.company_logo_dark'),
                        'hint' => __('admin.settings.company_logo_dark_hint'),
                        'preview' => $platformLogoOnDarkUrl,
                        'surface' => 'dark',
                        'accept' => 'image/png,image/webp,image/jpeg',
                        'max' => '2 MB',
                    ],
                    [
                        'name' => 'favicon',
                        'label' => __('admin.settings.favicon'),
                        'hint' => __('admin.settings.favicon_hint'),
                        'preview' => $platformFaviconUrl,
                        'surface' => 'icon',
                        'accept' => 'image/png,image/webp',
                        'max' => '1 MB',
                    ],
                ];
            @endphp

            <div class="d-flex align-items-center gap-2 mt-4 mb-1">
                <i class="bi bi-palette text-primary"></i>
                <h5 class="mb-0">{{ __('admin.settings.branding_assets') }}</h5>
            </div>
            <p class="text-muted small mb-3">{{ __('admin.settings.branding_assets_desc') }}</p>

            @if (! filled($storedBranding('logo_dark')) && filled($storedBranding('logo')))
                <div class="alert alert-warning d-flex align-items-start gap-2 py-2">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <span class="small">{{ __('admin.settings.dark_logo_fallback_notice') }}</span>
                </div>
            @endif

            <div class="row g-3 mb-3">
                @foreach ($brandingFields as $field)
                    @php $isUploaded = filled($storedBranding($field['name'])); @endphp
                    <div class="col-md-4">
                        <div class="border h-100 p-3 branding-field">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label mb-0 fw-semibold" for="branding-{{ $field['name'] }}">{{ $field['label'] }}</label>
                                <span class="badge bg-{{ $isUploaded ? 'success' : 'secondary' }}">
                                    {{ $isUploaded ? __('admin.settings.uploaded') : __('admin.settings.using_default') }}
                                </span>
                            </div>

                            <div class="branding-preview branding-preview-{{ $field['surface'] }} mb-2">
                                <img src="{{ $field['preview'] }}" alt="{{ $field['label'] }}"
                                     data-branding-preview="{{ $field['name'] }}">
                            </div>

                            <input type="file" class="form-control form-control-sm" id="branding-{{ $field['name'] }}"
                                   name="{{ $field['name'] }}" accept="{{ $field['accept'] }}"
                                   data-branding-input="{{ $field['name'] }}">
                            <small class="text-muted d-block mt-1">{{ $field['hint'] }}</small>
                            <small class="text-muted d-block">{{ __('admin.settings.branding_max_size', ['size' => $field['max']]) }}</small>

                            @if ($isUploaded)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1"
                                           name="remove_{{ $field['name'] }}" id="remove-{{ $field['name'] }}">
                                    <label class="form-check-label small" for="remove-{{ $field['name'] }}">
                                        {{ __('admin.settings.restore_default') }}
                                    </label>
                                </div>
                            @endif

                            @error($field['name'])<span class="text-danger d-block small mt-1">{{ $message }}</span>@enderror
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> {{ __('admin.actions.save') }}</button>
            </div>
        </form>
    </div>
</div>
