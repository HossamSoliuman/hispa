@extends('admin.layouts.master')
@section('title')
    {{ __('admin.invoices.tax_report') }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.invoices.tax_report') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.invoices.page_header') }}
            </a>
        </div>
    </div>

    <div class="row mb-3">
        @include('owner.components.stat-card', [
            'title' => __('admin.invoices.amount'),
            'value' => new \Illuminate\Support\HtmlString(number_format($totalAmount, 2) . ' ' . view('components.riyal-icon')->render()),
            'icon' => 'bi bi-cash-coin',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.invoices.vat'),
            'value' => new \Illuminate\Support\HtmlString(number_format($totalVAT, 2) . ' ' . view('components.riyal-icon')->render()),
            'icon' => 'bi bi-percent',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.invoices.total_revenue'),
            'value' => new \Illuminate\Support\HtmlString(number_format($totalRevenue, 2) . ' ' . view('components.riyal-icon')->render()),
            'icon' => 'bi bi-graph-up',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.invoices.tax-report') }}" method="get" class="row g-2 mb-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">{{ __('admin.report.sales.from_date') }}</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">{{ __('admin.report.sales.to_date') }}</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">{{ __('admin.report.sales.filter') }}</button>
                    <a href="{{ route('admin.invoices.tax-report') }}" class="btn btn-secondary">{{ __('admin.report.sales.reset') }}</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('admin.invoices.invoice_number') }}</th>
                            <th>{{ __('admin.invoices.user') }}</th>
                            <th>{{ __('admin.invoices.subscription') }}</th>
                            <th>{{ __('admin.invoices.amount') }}</th>
                            <th>{{ __('admin.invoices.vat') }}</th>
                            <th>{{ __('admin.invoices.total') }}</th>
                            <th>{{ __('admin.invoices.payment_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->user->name ?? '--' }}</td>
                                <td>{{ $invoice->subscription->package->name ?? '--' }}</td>
                                <td>{{ number_format($invoice->amount, 2) }}</td>
                                <td>{{ number_format($invoice->vat_amount, 2) }}</td>
                                <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                <td>{{ $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at)->format('Y-m-d') : '--' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ __('admin.invoices.no_invoices') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-active fw-bold">
                            <td colspan="4" class="text-end">{{ __('admin.invoices.total') }}</td>
                            <td>{{ number_format($totalAmount, 2) }}</td>
                            <td>{{ number_format($totalVAT, 2) }}</td>
                            <td>{{ number_format($totalRevenue, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
