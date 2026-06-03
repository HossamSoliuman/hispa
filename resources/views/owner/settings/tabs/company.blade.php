<div class="d-flex align-items-center mb-3">
    <div>
        <h4 class="mb-2">{{ __('owner.generated.company_info') }}</h4>
    </div>

    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">

    </div>
</div>

<div class="card-body">
    <form>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label
                    class="form-label">{{ __('owner.generated.company_name') }}({{ __('owner.generated.in_english') }})</label>
                <input type="text" class="form-control" value="Fish House Trading Est.">
            </div>
            <div class="col-md-6">
                <label
                    class="form-label">{{ __('owner.generated.company_name') }}({{ __('owner.generated.in_arabic') }})</label>
                <input type="text" class="form-control" value="{{ __('owner.generated.company_name_value') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.generated.commercial_registration_no') }}</label>
                <input type="text" class="form-control" value="4603007827">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.generated.agri_record_no') }}</label>
                <input type="text" class="form-control" value="">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.dalal.modal.form.tax_number') }}(VAT)</label>
                <input type="text" class="form-control" value="301054997700003">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.dalal.modal.form.email') }}</label>
                <input type="email" class="form-control" value="yaquobi@ymail.com">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.generated.phone_number') }}</label>
                <input type="text" class="form-control" value="0595233393">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.generated.address') }}</label>
                <input type="text" class="form-control" value="Al Qunfudah, Makkah, Saudi Arabia, 28822">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.generated.website') }}</label>
                <input type="text" class="form-control" value="www.fishhouse.sa">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.generated.company_logo') }}</label>
                <input type="file" class="form-control">
                <small
                    class="text-muted">{{ __('owner.generated.recommended_size') }}x{{ __('owner.generated.pixels_200') }}-
                    PNG {{ __('owner.generated.or') }}JPG</small>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>
                {{ __('owner.generated.save_info') }}</button>
        </div>
    </form>
</div>
