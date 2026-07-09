@extends('owner.layouts.master')

@section('title', __('owner.analysis_reports.account_statements.customer.title'))

@section('content')
    <div class="mb-3">
        <h2 class="mb-1">{{ __('owner.analysis_reports.account_statements.customer.title') }}</h2>
        <p class="text-muted mb-0">{{ __('owner.analysis_reports.account_statements.customer.description') }}</p>
    </div>

    @include('owner.report.partials.statement-filter', [
        'action' => route('owner.reports.customer-statement'),
        'printAction' => 'owner.reports.customer-statement.print',
        'entityParam' => 'customer_id',
        'entityLabel' => __('owner.analysis_reports.account_statements.customer.entity_label'),
        'placeholder' => __('owner.analysis_reports.account_statements.customer.placeholder'),
        'selectedId' => $customerId,
        'from' => $from,
        'to' => $to,
        'groups' => [['label' => null, 'items' => $customers]],
    ])

    @if (!$customer)
        <div class="alert alert-info border-0 shadow-sm">
            <i class="bi bi-info-circle me-1"></i>{{ __('owner.analysis_reports.account_statements.choose_prompt') }}
        </div>
    @else
        <div class="row g-3 mb-3">
            @foreach ([
                ['label' => __('owner.customers.show.cards.orders'), 'value' => number_format($statistics['total_orders']), 'color' => 'primary'],
                ['label' => __('owner.customers.show.cards.purchases'), 'value' => number_format($statistics['total_purchases'], 2), 'color' => 'secondary', 'money' => true],
                ['label' => __('owner.customers.show.cards.paid'), 'value' => number_format($statistics['total_paid'], 2), 'color' => 'success', 'money' => true],
                ['label' => __('owner.customers.show.cards.remaining'), 'value' => number_format($statistics['total_remaining'], 2), 'color' => $statistics['total_remaining'] > 0 ? 'danger' : 'success', 'money' => true],
            ] as $card)
                <div class="col-6 col-lg-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-1">{{ $card['label'] }}</div>
                            <div class="h4 mb-0 text-{{ $card['color'] }}">{{ $card['value'] }}@if (!empty($card['money'])) <x-riyal-icon size="sm" />@endif</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 fw-bold">{{ $customer->name }}</div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('owner.customers.show.table.invoice_number') }}</th>
                            <th>{{ __('owner.customers.show.table.date') }}</th>
                            <th>{{ __('owner.customers.show.table.payment_method') }}</th>
                            <th>{{ __('owner.customers.show.table.payment_status') }}</th>
                            <th class="text-end">{{ __('owner.customers.show.table.total_price') }}</th>
                            <th class="text-end">{{ __('owner.customers.show.table.paid') }}</th>
                            <th class="text-end">{{ __('owner.customers.show.table.remaining') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->sales as $sale)
                            @php $paid = $sale->total_price - $sale->remaining_total; @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sale->number }}</td>
                                <td>{{ optional($sale->sale_datetime)->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ optional($sale->paymentMethod)->name ?: '—' }}</td>
                                <td>{{ \App\Models\Sale::paymentStatusText($sale->payment_status) }}</td>
                                <td class="text-end">{{ number_format($sale->total_price, 2) }} <x-riyal-icon size="sm" /></td>
                                <td class="text-end">{{ number_format($paid, 2) }} <x-riyal-icon size="sm" /></td>
                                <td class="text-end {{ $sale->remaining_total > 0 ? 'text-danger' : '' }}">{{ number_format($sale->remaining_total, 2) }} <x-riyal-icon size="sm" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">{{ __('owner.analysis_reports.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                    @if ($customer->sales->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="5">{{ __('owner.analysis_reports.totals') }}</td>
                                <td class="text-end">{{ number_format($statistics['total_purchases'], 2) }} <x-riyal-icon size="sm" /></td>
                                <td class="text-end">{{ number_format($statistics['total_paid'], 2) }} <x-riyal-icon size="sm" /></td>
                                <td class="text-end">{{ number_format($statistics['total_remaining'], 2) }} <x-riyal-icon size="sm" /></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @endif
@endsection
