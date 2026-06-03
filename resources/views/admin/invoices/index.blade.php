@extends('admin.layouts.master')
@section('title')
    {{ __('admin.menu.invoices') }}
@endsection
@section('css')
    <style>
        .small-text th, .small-text td {
            font-size: 12px;
            text-align: center !important;
            vertical-align: middle;
        }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.menu.invoices') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.invoices.create') }}" class="btn btn-outline-theme btn-equal">
                <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.invoices.add') }}
            </a>
            <a href="{{ route('admin.invoices.tax-report') }}" class="btn btn-outline-info btn-equal">
                <i class="bi bi-file-earmark-text"></i> {{ __('admin.invoices.tax_report') }}
            </a>
            <a href="{{ route('admin.invoices.export', request()->query()) }}" class="btn btn-outline-success btn-equal">
                <i class="bi bi-file-earmark-excel"></i> {{ __('admin.invoices.export_invoices') }}
            </a>
        </div>
    </div>

    <div class="row mb-3">
        @include('owner.components.stat-card', [
            'title' => __('admin.invoices.total'),
            'value' => $totalInvoices,
            'icon' => 'bi bi-receipt',
            'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.invoices.paid'),
            'value' => $paidInvoices,
            'icon' => 'bi bi-check-circle',
            'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.invoices.pending'),
            'value' => $pendingInvoices,
            'icon' => 'bi bi-hourglass-split',
            'gradient' => 'linear-gradient(135deg, #f39c12, #f1c40f)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.invoices.total_revenue'),
            'value' => new \Illuminate\Support\HtmlString(
                number_format($totalRevenue, 0) .
                    ' ' .
                    view('components.riyal-icon', [
                        'size' => 'sm',
                        'style' => 'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.25rem;',
                    ])->render()),
            'icon' => 'bi bi-cash-coin',
            'gradient' => 'linear-gradient(135deg, #8e44ad, #9b59b6)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
    </div>

    @php
        $invoicePaymentStatusOptions = [
            ['value' => '', 'label' => __('admin.invoices.all_statuses')],
            ['value' => 'paid', 'label' => __('admin.invoices.paid')],
            ['value' => 'pending', 'label' => __('admin.invoices.pending')],
            ['value' => 'cancelled', 'label' => __('admin.invoices.cancelled')],
        ];
        $invoicePaymentMethodOptions = [
            ['value' => '', 'label' => __('admin.invoices.all_methods')],
            ['value' => 'mada', 'label' => __('admin.invoices.mada')],
            ['value' => 'visa', 'label' => __('admin.invoices.visa')],
            ['value' => 'bank_transfer', 'label' => __('admin.invoices.bank_transfer')],
        ];
    @endphp
    <x-admin.components.datatable-filters
        formId="invoicesFilters"
        formAction="{{ route('admin.invoices.index') }}"
        formMethod="get"
        :showSearchButton="true"
        :showResetButton="true"
        :filters="[
            ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.invoices.search'), 'value' => request('search')],
            ['type' => 'select-static', 'id' => 'payment_status', 'name' => 'payment_status', 'label' => __('admin.invoices.payment_status'), 'options' => $invoicePaymentStatusOptions, 'selected' => request('payment_status')],
            ['type' => 'select-static', 'id' => 'payment_method', 'name' => 'payment_method', 'label' => __('admin.invoices.payment_method'), 'options' => $invoicePaymentMethodOptions, 'selected' => request('payment_method')],
        ]"
        :showArrow="false"
    />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('admin.invoices.invoice_number') }}</th>
                            <th>{{ __('admin.invoices.user') }}</th>
                            <th>{{ __('admin.invoices.amount') }}</th>
                            <th>{{ __('admin.invoices.vat') }}</th>
                            <th>{{ __('admin.invoices.discount') }}</th>
                            <th>{{ __('admin.invoices.total') }}</th>
                            <th>{{ __('admin.invoices.payment_method') }}</th>
                            <th>{{ __('admin.invoices.payment_status') }}</th>
                            <th>{{ __('admin.invoices.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->user->name ?? '--' }}</td>
                            <td>{{ number_format($invoice->amount, 2) }} {{ __('admin.units.sar') }}</td>
                            <td>{{ number_format($invoice->vat_amount, 2) }} {{ __('admin.units.sar') }}</td>
                            <td>
                                @if(($invoice->discount_amount ?? 0) > 0)
                                    <span class="text-success">-{{ number_format($invoice->discount_amount, 2) }}</span>
                                    @if($invoice->coupon)
                                        <br><code class="small">{{ $invoice->coupon->code }}</code>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ number_format($invoice->total_amount, 2) }} {{ __('admin.units.sar') }}</td>
                            <td>{{ __('admin.invoices.payment_methods.' . $invoice->payment_method) }}</td>
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
                                <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">{{ __('admin.invoices.no_invoices') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
@endsection
