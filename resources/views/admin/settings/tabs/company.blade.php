<div class="d-flex align-items-center mb-3">
    <h4 class="mb-2">{{ __('admin.settings.company_info') }}</h4>
</div>

<div class="card border-0">
    <div class="card-body">
        <form>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.company_name_en') }}</label>
                    <input type="text" class="form-control" name="title_en" value="{{ optional($data->where('key', 'title_en')->first())->value ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.company_name_ar') }}</label>
                    <input type="text" class="form-control" name="title" value="{{ optional($data->where('key', 'title')->first())->value ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.commercial_registration_no') }}</label>
                    <input type="text" class="form-control" value="">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.agri_record_no') }}</label>
                    <input type="text" class="form-control" value="">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.tax_number') }}</label>
                    <input type="text" class="form-control" value="">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.email') }}</label>
                    <input type="email" class="form-control" name="email" value="{{ optional($data->where('key', 'email')->first())->value ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.phone') }}</label>
                    <input type="text" class="form-control" name="phone" value="{{ optional($data->where('key', 'phone')->first())->value ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.address') }}</label>
                    <input type="text" class="form-control" name="address" value="{{ optional($data->where('key', 'address')->first())->value ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.website') }}</label>
                    <input type="text" class="form-control" name="domain" value="{{ optional($data->where('key', 'domain')->first())->value ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.company_logo') }}</label>
                    <input type="file" class="form-control">
                    <small class="text-muted">{{ __('admin.settings.recommended_size') }}</small>
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> {{ __('admin.actions.save') }}</button>
            </div>
        </form>
    </div>
</div>
