@extends('admin.layouts.master')
@section('title')
    {{ __('admin.invoices.edit.title') }} #{{ $invoice->invoice_number }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.invoices.edit.title') }} #{{ $invoice->invoice_number }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.invoices.show.title') }}
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
            <form action="{{ route('admin.invoices.update', $invoice->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">{{ __('admin.invoices.subscription') }}</label>
                        <input type="text" class="form-control" value="{{ $invoice->subscription->user->name ?? '--' }} — {{ $invoice->subscription->package->name ?? '--' }}" disabled>
                        <small class="text-muted">{{ __('admin.invoices.cannot_change_subscription') }}</small>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="amount" class="form-label">{{ __('admin.invoices.amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control" value="{{ old('amount', $invoice->amount) }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="payment_method" class="form-label">{{ __('admin.invoices.payment_method') }} <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            @foreach(['mada' => __('admin.invoices.mada'), 'visa' => __('admin.invoices.visa'), 'bank_transfer' => __('admin.invoices.bank_transfer')] as $val => $label)
                                <option value="{{ $val }}" {{ old('payment_method', $invoice->payment_method) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="payment_status" class="form-label">{{ __('admin.invoices.payment_status') }} <span class="text-danger">*</span></label>
                        <select name="payment_status" id="payment_status" class="form-select" required>
                            @foreach(['pending' => __('admin.invoices.pending'), 'paid' => __('admin.invoices.paid'), 'cancelled' => __('admin.invoices.cancelled')] as $val => $label)
                                <option value="{{ $val }}" {{ old('payment_status', $invoice->payment_status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="bank_transfer_receipt" class="form-label">{{ __('admin.invoices.bank_transfer_receipt') }}</label>
                        <input type="text" name="bank_transfer_receipt" id="bank_transfer_receipt" class="form-control" value="{{ old('bank_transfer_receipt', $invoice->bank_transfer_receipt) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="payment_notes" class="form-label">{{ __('admin.invoices.payment_notes') }}</label>
                        <input type="text" name="payment_notes" id="payment_notes" class="form-control" value="{{ old('payment_notes', $invoice->payment_notes) }}">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('admin.actions.save') }}</button>
                    <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-secondary">{{ __('admin.actions.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
