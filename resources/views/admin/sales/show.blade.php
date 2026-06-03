@extends('admin.layouts.master')

@section('title')
    {{ __('admin.menu.sales') }} - {{ __('admin.actions.show') }}
@endsection

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.menu.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}">{{ __('admin.menu.sales') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.actions.show') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.menu.sales') }} - {{ $sale->number }}</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>{{ __('admin.sales.number') ?? __('admin.invoices.invoice_number') }}:</strong> {{ $sale->number }}</p>
                    <p class="mb-1"><strong>{{ __('admin.sales.date') ?? __('admin.owner_stock_details.table.date') }}:</strong> {{ $sale->sale_datetime ? $sale->sale_datetime->format('Y-m-d H:i') : '---' }}</p>
                    <p class="mb-1"><strong>{{ __('admin.sales.customer') ?? __('admin.customers.table.name') }}:</strong> {{ $sale->customer_name ?? optional($sale->customer)->name }}</p>
                    <p class="mb-1"><strong>{{ __('admin.sales.payment_method') }}:</strong> {{ optional($sale->paymentMethod)->name }}</p>
                    <p class="mb-1"><strong>{{ __('admin.sales.status') }}:</strong> {{ \App\Models\Sale::statusText($sale->status) }}</p>
                    <p class="mb-1"><strong>{{ __('admin.sales.payment_status') ?? __('admin.invoices.payment_status') }}:</strong> {{ \App\Models\Sale::paymentStatusText($sale->payment_status) }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>{{ __('admin.sales.total_price') ?? __('admin.owner_stock_fish_quantity.total_price') }}:</strong> {{ number_format($sale->total_price, 2) }}</p>
                    <p class="mb-1"><strong>{{ __('admin.sales.net_owner_amount') ?? __('admin.invoices.amount') }}:</strong> {{ number_format($sale->net_owner_amount, 2) }}</p>
                    <p class="mb-1"><strong>{{ __('admin.sales.remaining_total') ?? __('admin.invoices.pending') }}:</strong> {{ number_format($sale->remaining_total, 2) }}</p>
                </div>
            </div>

            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('admin.owner_stocks.table.fish_name') }}</th>
                        <th>{{ __('admin.stocks_admin.table.total_weight') }}</th>
                        <th>{{ __('admin.owner_stock_fish_quantity.total_price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->details as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ optional($detail->fish)->scientific_name ?? '---' }}</td>
                            <td>{{ number_format($detail->weight ?? 0, 2) }} {{ __('admin.stocks_admin.unit_kg') }}</td>
                            <td>{{ number_format($detail->total_price ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">{{ __('admin.catch.total') ?? __('admin.owner_stock_fish_quantity.total_price') }}</th>
                        <th>{{ number_format($sale->details->sum('weight'), 2) }} {{ __('admin.stocks_admin.unit_kg') }}</th>
                        <th>{{ number_format($sale->details->sum('total_price'), 2) }}</th>
                    </tr>
                </tfoot>
            </table>

            <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">{{ __('admin.stocks_admin.back_to_list') }}</a>
        </div>
    </div>
@endsection
