@extends('admin.layouts.master')
@section('title')
    {{ __('admin.subscriptions.show.title') }} - {{ $subscription->user->name ?? '#' . $subscription->id }}
@endsection
@section('css')
    <style>
        .small-text th, .small-text td { font-size: 12px; text-align: center !important; vertical-align: middle; }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.subscriptions.show.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="btn btn-outline-primary btn-equal">
                <i class="bi bi-pencil"></i> {{ __('admin.actions.edit') }}
            </a>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.subscriptions.all') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('admin.subscriptions.subscription_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscriptions.user') }}:</strong> {{ $subscription->user->name ?? '--' }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscriptions.package') }}:</strong> {{ $subscription->package->name ?? '--' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscriptions.start_date') }}:</strong>
                            {{ $subscription->start_date ? $subscription->start_date->format('Y-m-d') : '--' }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscriptions.end_date') }}:</strong>
                            {{ $subscription->end_date ? $subscription->end_date->format('Y-m-d') : '--' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscriptions.status') }}:</strong>
                            @if($subscription->is_suspended)
                                <span class="badge bg-secondary">{{ __('admin.subscriptions.suspended') }}</span>
                            @elseif($subscription->status == 'active' && $subscription->end_date >= now()->toDateString())
                                <span class="badge bg-success">{{ __('admin.subscriptions.active') }}</span>
                            @elseif($subscription->status == 'expired' || $subscription->end_date < now()->toDateString())
                                <span class="badge bg-danger">{{ __('admin.subscriptions.expired') }}</span>
                            @elseif($subscription->status == 'trial')
                                <span class="badge bg-warning">{{ __('admin.subscriptions.trial') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $subscription->status }}</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('admin.subscriptions.renewal_count') }}:</strong> {{ $subscription->renewal_count ?? 0 }}
                        </div>
                    </div>
                    @if($subscription->is_suspended && $subscription->suspension_reason)
                        <div class="row">
                            <div class="col-12">
                                <strong>{{ __('admin.subscriptions.suspension_reason') }}:</strong> {{ $subscription->suspension_reason }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('admin.subscriptions.invoices') }}</h5>
                    <a href="{{ route('admin.invoices.create') }}?subscription_id={{ $subscription->id }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus"></i> {{ __('admin.invoices.add') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover small-text">
                            <thead>
                                <tr>
                                    <th>{{ __('admin.invoices.invoice_number') }}</th>
                                    <th>{{ __('admin.invoices.amount') }}</th>
                                    <th>{{ __('admin.invoices.total_amount') }}</th>
                                    <th>{{ __('admin.invoices.payment_status') }}</th>
                                    <th>{{ __('admin.subscriptions.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscription->invoices ?? [] as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ number_format($invoice->amount, 2) }}</td>
                                        <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                        <td>
                                            @if($invoice->payment_status == 'paid')
                                                <span class="badge bg-success">{{ __('admin.invoices.paid') }}</span>
                                            @elseif($invoice->payment_status == 'pending')
                                                <span class="badge bg-warning">{{ __('admin.invoices.pending') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('admin.invoices.cancelled') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('admin.invoices.no_invoices') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('admin.subscriptions.subscription_history') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover small-text">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('admin.subscriptions.package') }}</th>
                                    <th>{{ __('admin.subscriptions.start_date') }}</th>
                                    <th>{{ __('admin.subscriptions.end_date') }}</th>
                                    <th>{{ __('admin.subscriptions.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscriptionHistory as $hist)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $hist->package->name ?? '--' }}</td>
                                        <td>{{ $hist->start_date ? $hist->start_date->format('Y-m-d') : '--' }}</td>
                                        <td>{{ $hist->end_date ? $hist->end_date->format('Y-m-d') : '--' }}</td>
                                        <td>{{ $hist->status }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('admin.subscriptions.no_history') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('admin.subscriptions.actions') }}</h5>
                </div>
                <div class="card-body">
                    @if($subscription->is_suspended)
                        <form action="{{ route('admin.subscriptions.unsuspend', $subscription->id) }}" method="post" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-play-circle"></i> {{ __('admin.subscriptions.unsuspend') }}
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.subscriptions.suspend', $subscription->id) }}" method="post" class="mb-2" id="formSuspend">
                            @csrf
                            <div class="mb-2">
                                <label for="suspension_reason" class="form-label">{{ __('admin.subscriptions.suspension_reason') }}</label>
                                <textarea name="suspension_reason" id="suspension_reason" class="form-control" rows="2" maxlength="500"></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-pause-circle"></i> {{ __('admin.subscriptions.suspend') }} ({{ __('admin.subscriptions.freeze') }})
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.subscriptions.renew', $subscription->id) }}" method="post" class="mb-2">
                        @csrf
                        <div class="mb-2">
                            <label for="duration_type" class="form-label">{{ __('admin.subscriptions.duration_type') }}</label>
                            <select name="duration_type" id="duration_type" class="form-select">
                                <option value="">{{ __('admin.subscriptions.use_package_duration') }}</option>
                                <option value="monthly">monthly</option>
                                <option value="quarterly">quarterly</option>
                                <option value="yearly">yearly</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-arrow-repeat"></i> {{ __('admin.subscriptions.renew') }}
                        </button>
                    </form>

                    <hr>
                    <button type="button" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#modalTrial">
                        <i class="bi bi-gift"></i> {{ __('admin.subscriptions.grant_free_trial') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTrial" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.subscriptions.grant-trial', $subscription->id) }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('admin.subscriptions.grant_free_trial') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="trial_days" class="form-label">{{ __('admin.subscriptions.trial_days') }}</label>
                        <input type="number" name="trial_days" id="trial_days" class="form-control" min="1" max="30" value="7" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.actions.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('admin.subscriptions.grant') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
