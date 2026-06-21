@extends('owner.layouts.master')

@section('title')
    {{ __('owner.customers.show.title') }}
@endsection

@section('css')
    <style>
        .info-list dt {
            font-size: 12px;
            color: var(--bs-secondary-color);
            font-weight: 600;
        }

        .info-list dd {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: .75rem;
        }

        .invoice-items-cell {
            background: var(--bs-tertiary-bg);
        }

        .riyal-cell {
            display: inline-flex;
            align-items: baseline;
            gap: .2rem;
        }

        .riyal-cell svg {
            width: 13px;
            height: 13px;
            fill: currentColor;
        }
    </style>
@endsection

@section('content')
    @php
        $statusBadge = $customer->status == 1 ? 'success' : 'danger';
        $statusText = $customer->status == 1 ? __('owner.status.active') : __('owner.status.inactive');
    @endphp

    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold mb-1">{{ __('owner.customers.show.title') }}</h2>
            <p class="text-muted mb-0">
                <i class="bi bi-person-circle me-1"></i>{{ $customer->name }}
                <span class="badge bg-{{ $statusBadge }} ms-2">{{ $statusText }}</span>
            </p>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <div class="d-flex flex-wrap justify-content-md-end justify-content-start align-items-center gap-2">
                <a href="{{ route('owner.customers.statement.print', $customer->id) }}" target="_blank"
                    class="btn btn-outline-info btn-border-radius">
                    <i class="bi bi-printer me-1"></i> {{ __('owner.customers.show.print_statement') }}
                </a>
                <a href="{{ route('owner.customers.index') }}" class="btn btn-outline-default">
                    <i class="bi bi-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-1"></i>
                    {{ __('owner.customers.show.back') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Customer info --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent fw-bold">
            <i class="bi bi-info-circle me-1"></i>{{ __('owner.customers.show.info_title') }}
        </div>
        <div class="card-body">
            <div class="row info-list">
                <div class="col-md-3 col-sm-6">
                    <dt>{{ __('owner.customers.show.name') }}</dt>
                    <dd>{{ $customer->name ?: '—' }}</dd>
                </div>
                <div class="col-md-3 col-sm-6">
                    <dt>{{ __('owner.customers.show.phone') }}</dt>
                    <dd>{{ $customer->phone ?: '—' }}</dd>
                </div>
                <div class="col-md-3 col-sm-6">
                    <dt>{{ __('owner.customers.show.email') }}</dt>
                    <dd>{{ $customer->email ?: '—' }}</dd>
                </div>
                <div class="col-md-3 col-sm-6">
                    <dt>{{ __('owner.customers.show.type') }}</dt>
                    <dd>{{ $customer->type ?: '—' }}</dd>
                </div>
                <div class="col-md-3 col-sm-6">
                    <dt>{{ __('owner.customers.show.registered_at') }}</dt>
                    <dd>{{ optional($customer->created_at)->format('Y-m-d') ?? '—' }}</dd>
                </div>
                <div class="col-md-9 col-sm-6">
                    <dt>{{ __('owner.customers.show.notes') }}</dt>
                    <dd>{{ $customer->notes ?: '—' }}</dd>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="row mb-4">
        @include('owner.components.stat-card', [
            'title' => __('owner.customers.show.cards.orders'),
            'value' => '<span>' . number_format($statistics['total_orders']) . '</span>',
            'icon' => 'bi bi-receipt',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.customers.show.cards.purchases'),
            'value' =>
                '<span>' . number_format($statistics['total_purchases'], 2) . '</span> <span class="unit">' .
                view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>',
            'icon' => 'bi bi-cash-stack',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.customers.show.cards.paid'),
            'value' =>
                '<span>' . number_format($statistics['total_paid'], 2) . '</span> <span class="unit">' .
                view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>',
            'icon' => 'bi bi-check2-circle',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.customers.show.cards.remaining'),
            'value' =>
                '<span>' . number_format($statistics['total_remaining'], 2) . '</span> <span class="unit">' .
                view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>',
            'icon' => 'bi bi-exclamation-circle',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
    </div>

    {{-- Invoices --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent fw-bold">
            <i class="bi bi-card-list me-1"></i>{{ __('owner.customers.show.invoices_title') }}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover align-middle text-center mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('owner.customers.show.table.index') }}</th>
                            <th>{{ __('owner.customers.show.table.invoice_number') }}</th>
                            <th>{{ __('owner.customers.show.table.date') }}</th>
                            <th>{{ __('owner.customers.show.table.payment_method') }}</th>
                            <th>{{ __('owner.customers.show.table.status') }}</th>
                            <th>{{ __('owner.customers.show.table.payment_status') }}</th>
                            <th>{{ __('owner.customers.show.table.total_weight') }}</th>
                            <th>{{ __('owner.customers.show.table.total_price') }}</th>
                            <th>{{ __('owner.customers.show.table.paid') }}</th>
                            <th>{{ __('owner.customers.show.table.remaining') }}</th>
                            <th>{{ __('owner.customers.show.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->sales as $sale)
                            @php
                                $totalWeight = $sale->details->sum('weight');
                                $paid = $sale->total_price - $sale->remaining_total;
                                $statusClass = match ($sale->status) {
                                    1 => 'warning',
                                    2 => 'success',
                                    default => 'secondary',
                                };
                                $paymentClass = match ($sale->payment_status) {
                                    'paid' => 'success',
                                    'partially_paid' => 'warning',
                                    'unpaid' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sale->number }}</td>
                                <td>{{ optional($sale->sale_datetime)->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ optional($sale->paymentMethod)->name ?: '—' }}</td>
                                <td><span class="badge bg-{{ $statusClass }}">{{ \App\Models\Sale::statusText($sale->status) }}</span></td>
                                <td><span class="badge bg-{{ $paymentClass }}">{{ \App\Models\Sale::paymentStatusText($sale->payment_status) }}</span></td>
                                <td>{{ number_format($totalWeight, 2) }}</td>
                                <td><span class="riyal-cell">{{ number_format($sale->total_price, 2) }} <x-riyal-icon size="sm" /></span></td>
                                <td><span class="riyal-cell">{{ number_format($paid, 2) }} <x-riyal-icon size="sm" /></span></td>
                                <td>
                                    <span class="riyal-cell {{ $sale->remaining_total > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                        {{ number_format($sale->remaining_total, 2) }} <x-riyal-icon size="sm" />
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-info"
                                        data-bs-toggle="collapse" data-bs-target="#inv-details-{{ $sale->id }}"
                                        title="{{ __('owner.customers.show.view_details') }}">
                                        <i class="bi bi-list-ul"></i>
                                    </button>
                                    <a href="{{ route('owner.sales.print', $sale->id) }}" target="_blank"
                                        class="btn btn-sm btn-outline-secondary"
                                        title="{{ __('owner.customers.show.print_invoice') }}">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="11" class="p-0 border-0">
                                    <div class="collapse" id="inv-details-{{ $sale->id }}">
                                        <div class="invoice-items-cell p-3 text-start">
                                            <h6 class="fw-bold mb-2">
                                                <i class="bi bi-fish me-1"></i>{{ __('owner.customers.show.items.title') }}
                                            </h6>
                                            <table class="table table-sm table-bordered mb-0 bg-body">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>{{ __('owner.customers.show.items.fish') }}</th>
                                                        <th>{{ __('owner.customers.show.items.weight') }}</th>
                                                        <th>{{ __('owner.customers.show.items.unit') }}</th>
                                                        <th>{{ __('owner.customers.show.items.price_per_kilo') }}</th>
                                                        <th>{{ __('owner.customers.show.items.total') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($sale->details as $detail)
                                                        <tr class="text-center">
                                                            <td>{{ $detail->fish?->name ?? ($detail->fish_name ?: '—') }}</td>
                                                            <td>{{ number_format($detail->weight, 2) }}</td>
                                                            <td>{{ $detail->unit?->name ?: __('owner.units.kg') }}</td>
                                                            <td><span class="riyal-cell">{{ number_format($detail->price_per_kilo, 2) }} <x-riyal-icon size="sm" /></span></td>
                                                            <td><span class="riyal-cell">{{ number_format($detail->total_price, 2) }} <x-riyal-icon size="sm" /></span></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-muted py-4">{{ __('owner.customers.show.no_invoices') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
