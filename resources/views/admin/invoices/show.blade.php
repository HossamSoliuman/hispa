@extends('admin.layouts.master')
@section('title')
    {{ __('admin.invoices.show.title') }} #{{ $invoice->invoice_number }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.invoices.invoice_details') }} #{{ $invoice->invoice_number }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.invoices.page_header') }}
            </a>
            <a href="{{ route('admin.invoices.print', $invoice->id) }}" target="_blank" class="btn btn-outline-dark btn-equal">
                <i class="bi bi-printer"></i> {{ __('admin.invoices.print') }}
            </a>
            <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-outline-success btn-equal">
                <i class="bi bi-pencil"></i> {{ __('admin.invoices.edit.title') }}
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin.invoices.invoice_details') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered align-middle mb-0">
                        <tbody>
                            <tr>
                                <th style="width:35%">{{ __('admin.invoices.invoice_number') }}</th>
                                <td>{{ $invoice->invoice_number }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.invoices.user') }}</th>
                                <td>{{ $invoice->user->name ?? '--' }} <span class="text-muted small">{{ $invoice->user->phone ?? '' }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.invoices.send_to_email') }}</th>
                                <td>
                                    @if($invoice->user?->email)
                                        <a href="mailto:{{ $invoice->user->email }}">{{ $invoice->user->email }}</a>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.invoices.subscription') }}</th>
                                <td>{{ $invoice->subscription->package->name ?? '--' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.invoices.amount') }}</th>
                                <td>{{ number_format($invoice->amount, 2) }} <x-riyal-icon /></td>
                            </tr>
                            @if(($invoice->discount_amount ?? 0) > 0)
                                <tr>
                                    <th>{{ __('admin.invoices.discount') }}</th>
                                    <td class="text-success">-{{ number_format($invoice->discount_amount, 2) }} <x-riyal-icon /></td>
                                </tr>
                            @endif
                            <tr class="table-active">
                                <th>{{ __('admin.invoices.total') }}</th>
                                <td class="fw-bold">{{ number_format($invoice->total_amount, 2) }} <x-riyal-icon /></td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.invoices.payment_method') }}</th>
                                <td>{{ __('admin.invoices.payment_methods.' . $invoice->payment_method) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.invoices.payment_status') }}</th>
                                <td>
                                    @if($invoice->payment_status == 'paid')
                                        <span class="badge bg-success">{{ __('admin.invoices.paid') }}</span>
                                    @elseif($invoice->payment_status == 'pending')
                                        <span class="badge bg-warning">{{ __('admin.invoices.pending') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('admin.invoices.cancelled') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($invoice->paid_at)
                                <tr>
                                    <th>{{ __('admin.invoices.payment_date') }}</th>
                                    <td>{{ \Carbon\Carbon::parse($invoice->paid_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endif
                            @if($invoice->payment_confirmed_at)
                                <tr>
                                    <th>{{ __('admin.invoices.confirmed_at') }}</th>
                                    <td>{{ \Carbon\Carbon::parse($invoice->payment_confirmed_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('admin.invoices.confirmed_by') }}</th>
                                    <td>{{ $invoice->confirmedBy->name ?? '--' }}</td>
                                </tr>
                            @endif
                            @if($invoice->bank_transfer_receipt)
                                <tr>
                                    <th>{{ __('admin.invoices.bank_transfer_receipt') }}</th>
                                    <td>{{ $invoice->bank_transfer_receipt }}</td>
                                </tr>
                            @endif
                            @if($invoice->payment_notes)
                                <tr>
                                    <th>{{ __('admin.invoices.payment_notes') }}</th>
                                    <td>{{ $invoice->payment_notes }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>{{ __('admin.invoices.created_at') }}</th>
                                <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($invoice->payment_status === 'pending')
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('admin.invoices.confirm_payment') }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">{{ __('admin.invoices.confirm_payment_message') }}</p>
                        <form action="{{ route('admin.invoices.confirm-payment', $invoice->id) }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label for="payment_notes" class="form-label">{{ __('admin.invoices.payment_notes') }}</label>
                                <textarea name="payment_notes" id="payment_notes" rows="3" class="form-control">{{ $invoice->payment_notes }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle me-1"></i> {{ __('admin.invoices.confirm') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
