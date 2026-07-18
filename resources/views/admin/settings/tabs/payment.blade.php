<div class="d-flex align-items-center mb-3">
    <h4 class="mb-2">{{ __('admin.settings.payment_info') }}</h4>
</div>

<div class="card border-0">
    <div class="card-body">
        <p class="text-muted small mb-4">{{ __('admin.settings.payment_desc') }}</p>

        <form action="{{ route('admin.settings.payment') }}" method="POST">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.bank_name') }}</label>
                    <input type="text" class="form-control" name="bank_name" value="{{ optional($data->where('key', 'bank_name')->first())->value ?? '' }}">
                    @error('bank_name')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.settings.bank_account_name') }}</label>
                    <input type="text" class="form-control" name="bank_account_name" value="{{ optional($data->where('key', 'bank_account_name')->first())->value ?? '' }}">
                    @error('bank_account_name')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label">{{ __('admin.settings.bank_account_number') }}</label>
                    <input type="text" class="form-control" name="bank_account_number" dir="ltr" value="{{ optional($data->where('key', 'bank_account_number')->first())->value ?? '' }}">
                    @error('bank_account_number')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label">{{ __('admin.settings.payment_instructions') }}</label>
                    <textarea class="form-control" name="payment_instructions" rows="3">{{ optional($data->where('key', 'payment_instructions')->first())->value ?? '' }}</textarea>
                    @error('payment_instructions')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> {{ __('admin.actions.save') }}</button>
            </div>
        </form>
    </div>
</div>
