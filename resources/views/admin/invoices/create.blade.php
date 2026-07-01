@extends('admin.layouts.master')
@section('title')
    {{ __('admin.invoices.create.title') }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.invoices.create.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.invoices.page_header') }}
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
            <form action="{{ route('admin.invoices.store') }}" method="post">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="subscription_id" class="form-label">{{ __('admin.invoices.subscription') }} <span class="text-danger">*</span></label>
                        <select name="subscription_id" id="subscription_id" class="form-select" required>
                            <option value="">{{ __('admin.actions.choose') }}</option>
                            @foreach($subscriptions as $s)
                                <option value="{{ $s->id }}" {{ old('subscription_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->user->name ?? '--' }} — {{ $s->package->name ?? '--' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="amount" class="form-label">{{ __('admin.invoices.amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="vat_rate" class="form-label">{{ __('admin.invoices.vat_rate') }} (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="vat_rate" id="vat_rate" class="form-control" value="{{ old('vat_rate', 15) }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="payment_method" class="form-label">{{ __('admin.invoices.payment_method') }} <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="mada" {{ old('payment_method') == 'mada' ? 'selected' : '' }}>{{ __('admin.invoices.mada') }}</option>
                            <option value="visa" {{ old('payment_method') == 'visa' ? 'selected' : '' }}>{{ __('admin.invoices.visa') }}</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>{{ __('admin.invoices.bank_transfer') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="payment_status" class="form-label">{{ __('admin.invoices.payment_status') }} <span class="text-danger">*</span></label>
                        <select name="payment_status" id="payment_status" class="form-select" required>
                            <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>{{ __('admin.invoices.pending') }}</option>
                            <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>{{ __('admin.invoices.paid') }}</option>
                            <option value="cancelled" {{ old('payment_status') == 'cancelled' ? 'selected' : '' }}>{{ __('admin.invoices.cancelled') }}</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="bank_transfer_receipt" class="form-label">{{ __('admin.invoices.bank_transfer_receipt') }}</label>
                        <input type="text" name="bank_transfer_receipt" id="bank_transfer_receipt" class="form-control" value="{{ old('bank_transfer_receipt') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="payment_notes" class="form-label">{{ __('admin.invoices.payment_notes') }}</label>
                        <input type="text" name="payment_notes" id="payment_notes" class="form-control" value="{{ old('payment_notes') }}">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('admin.invoices.add') }}</button>
                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">{{ __('admin.actions.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
