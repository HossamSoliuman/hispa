<div class="d-flex align-items-center mb-3">
    <div>
        <h4 class="mb-2">{{ __('owner.generated.company_info') }}</h4>
    </div>

    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">

    </div>
</div>

<div class="card-body">
    <form action="{{ route('owner.settings.company') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label
                    class="form-label">{{ __('owner.generated.company_name') }}({{ __('owner.generated.in_english') }})</label>
                <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror"
                    value="{{ old('name_en', $company->name_en) }}">
                @error('name_en')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <label
                    class="form-label">{{ __('owner.generated.company_name') }}({{ __('owner.generated.in_arabic') }})</label>
                <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
                    value="{{ old('name_ar', $company->name_ar) }}">
                @error('name_ar')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.generated.commercial_registration_no') }}</label>
                <input type="text" name="cr_number" class="form-control @error('cr_number') is-invalid @enderror"
                    value="{{ old('cr_number', $company->cr_number) }}">
                @error('cr_number')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.generated.agri_record_no') }}</label>
                <input type="text" name="record_number"
                    class="form-control @error('record_number') is-invalid @enderror"
                    value="{{ old('record_number', $company->record_number) }}">
                @error('record_number')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.dalal.modal.form.email') }}</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $company->email) }}">
                @error('email')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.generated.phone_number') }}</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone', $company->phone) }}">
                @error('phone')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.generated.address') }}</label>
                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                    value="{{ old('address', $company->address) }}">
                @error('address')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.generated.website') }}</label>
                <input type="text" name="website" class="form-control @error('website') is-invalid @enderror"
                    value="{{ old('website', $company->website) }}">
                @error('website')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('owner.generated.company_logo') }}</label>
                <input type="file" name="logo" accept="image/*" class="form-control @error('logo') is-invalid @enderror">
                @error('logo')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
                <small
                    class="text-muted">{{ __('owner.generated.recommended_size') }}x{{ __('owner.generated.pixels_200') }}-
                    PNG {{ __('owner.generated.or') }}JPG</small>
                <div class="mt-2">
                    <span class="d-block text-muted mb-1">
                        {{ $company->logo ? __('owner.generated.current_logo') : __('owner.generated.default_logo') }}
                    </span>
                    <img src="{{ $company->logo_url }}" alt="{{ __('owner.generated.company_logo') }}"
                        style="max-height: 70px; width: auto; object-fit: contain;">
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>
                {{ __('owner.generated.save_info') }}</button>
        </div>
    </form>
</div>
