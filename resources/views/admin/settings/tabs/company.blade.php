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
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.company_logo') }}</label>
                    <input type="file" class="form-control" name="logo" accept="image/*">
                    <small class="text-muted">{{ __('admin.settings.recommended_size') }}</small>
                    @error('logo')<span class="text-danger d-block">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> {{ __('admin.actions.save') }}</button>
            </div>
        </form>
    </div>
</div>
