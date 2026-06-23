<x-report-layout
    :title="__('owner.customers.statement.title') . ' — ' . $customer->name"
    :title-en="'Customer Statement'"
    :document-number="'#' . str_pad($customer->id, 8, '0', STR_PAD_LEFT)"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? null">


    <x-report-header
        :document-number="'#' . str_pad($customer->id, 8, '0', STR_PAD_LEFT)"
        :title="__('owner.customers.statement.title')"
        :title-en="'Customer Statement'"
        :settings="$settings" />

    {{-- Customer key facts --}}
    <table class="info-bar">
        <tr>
            <td>
                <span class="ib-label">{{ __('owner.customers.statement.customer_info') }}</span>
                <span class="ib-value">{{ $customer->name ?: '—' }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('owner.customers.show.phone') }}</span>
                <span class="ib-value">{{ $customer->phone ?: '—' }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('owner.customers.show.email') }}</span>
                <span class="ib-value">{{ $customer->email ?: '—' }}</span>
            </td>
            @if($customer->type)
                <td>
                    <span class="ib-label">{{ __('owner.customers.show.type') }}</span>
                    <span class="ib-value">{{ $customer->type }}</span>
                </td>
            @endif
            <td>
                <span class="ib-label">{{ __('owner.customers.show.registered_at') }}</span>
                <span class="ib-value">{{ optional($customer->created_at)->format('Y-m-d') ?? '—' }}</span>
            </td>
        </tr>
    </table>

    {{-- KPIs --}}
    <x-report-stats :items="[
        ['label' => __('owner.customers.show.cards.orders'), 'value' => number_format($statistics['total_orders'])],
        ['label' => __('owner.customers.show.cards.purchases'), 'value' => number_format($statistics['total_purchases'], 2)],
        ['label' => __('owner.customers.show.cards.paid'), 'value' => number_format($statistics['total_paid'], 2)],
        ['label' => __('owner.customers.show.cards.remaining'), 'value' => number_format($statistics['total_remaining'], 2), 'color' => $statistics['total_remaining'] > 0 ? '#dc2626' : '#16a34a'],
    ]" />

    {{-- Invoices listing --}}
    <div class="section-title">{{ __('owner.customers.show.invoices_title') }}</div>
    <table class="report-table block">
        <thead>
            <tr>
                <th style="width:6%;">#</th>
                <th style="width:18%;">{{ __('owner.customers.show.table.invoice_number') }}</th>
                <th style="width:13%;">{{ __('owner.customers.show.table.date') }}</th>
                <th style="width:15%;">{{ __('owner.customers.show.table.payment_method') }}</th>
                <th style="width:14%;">{{ __('owner.customers.show.table.payment_status') }}</th>
                <th class="col-num" style="width:11%;">{{ __('owner.customers.show.table.total_price') }}</th>
                <th class="col-num" style="width:11%;">{{ __('owner.customers.show.table.paid') }}</th>
                <th class="col-num" style="width:12%;">{{ __('owner.customers.show.table.remaining') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customer->sales as $sale)
                @php $paid = $sale->total_price - $sale->remaining_total; @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $sale->number }}</td>
                    <td>{{ optional($sale->sale_datetime)->format('Y-m-d') ?? '—' }}</td>
                    <td>{{ optional($sale->paymentMethod)->name ?: '—' }}</td>
                    <td>{{ \App\Models\Sale::paymentStatusText($sale->payment_status) }}</td>
                    <td class="col-num">{{ number_format($sale->total_price, 2) }} <x-riyal-icon /></td>
                    <td class="col-num">{{ number_format($paid, 2) }} <x-riyal-icon /></td>
                    <td class="col-num">{{ number_format($sale->remaining_total, 2) }} <x-riyal-icon /></td>
                </tr>
            @empty
                <tr><td colspan="8" style="color:#95a5a6;">{{ __('owner.customers.show.no_invoices') }}</td></tr>
            @endforelse
        </tbody>
        @if($customer->sales->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="5" class="col-text">{{ __('owner.sales.total') }}</td>
                    <td class="col-num">{{ number_format($statistics['total_purchases'], 2) }} <x-riyal-icon /></td>
                    <td class="col-num">{{ number_format($statistics['total_paid'], 2) }} <x-riyal-icon /></td>
                    <td class="col-num">{{ number_format($statistics['total_remaining'], 2) }} <x-riyal-icon /></td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- Footer --}}
    <table class="report-footer">
        <tr>
            <td class="rf-text">
                {{ $settings['title'] ?? $settings['company_name'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}
            </td>
        </tr>
    </table>

</x-report-layout>
