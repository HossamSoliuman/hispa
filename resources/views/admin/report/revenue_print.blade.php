<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings"
        :title="__('admin.report.revenue.print_title')"
    />

    <x-report-info :settings="$settings" :from-date="$from" :to-date="$to">
        <x-slot:additionalInfo>
            <div class="info-row">
                <div class="info-item">
                    <span class="label">{{ __('admin.report.revenue.from_date') }}</span>
                    <span class="value">{{ $from ? \Illuminate\Support\Carbon::parse($from)->format('Y-m-d') : __('admin.report.revenue.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('admin.report.revenue.to_date') }}</span>
                    <span class="value">{{ $to ? \Illuminate\Support\Carbon::parse($to)->format('Y-m-d') : __('admin.report.revenue.all_dates') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('admin.report.revenue.status') }}</span>
                    <span class="value">
                        @if($status === 'paid')
                            {{ __('admin.report.revenue.paid') }}
                        @elseif($status === 'pending')
                            {{ __('admin.report.revenue.pending') }}
                        @else
                            {{ __('admin.report.revenue.all') }}
                        @endif
                    </span>
                </div>
            </div>
        </x-slot:additionalInfo>
    </x-report-info>

    <x-report-stats :items="[
        ['label' => __('admin.report.revenue.kpi.total_invoices'), 'value' => number_format($totalInvoices)],
        ['label' => __('admin.report.revenue.kpi.paid_revenue'), 'value' => $paidRevenue, 'money' => true, 'color' => '#16a085'],
        ['label' => __('admin.report.revenue.kpi.pending_amount'), 'value' => $pendingAmount, 'money' => true, 'color' => '#e67e22'],
        ['label' => __('admin.report.revenue.kpi.active_subscriptions'), 'value' => number_format($activeSubscriptions)],
    ]" />

    <x-report-table :headers="[
        '#',
        __('admin.report.revenue.invoice_number'),
        __('admin.report.revenue.owner'),
        __('admin.report.revenue.plan'),
        __('admin.report.revenue.amount'),
        __('admin.report.revenue.vat'),
        __('admin.report.revenue.total'),
        __('admin.report.revenue.status_th'),
        __('admin.report.revenue.created_at'),
        __('admin.report.revenue.paid_at'),
    ]" :data="$invoices">
        @foreach($invoices as $index => $invoice)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ optional($invoice->user)->name ?? '---' }}</td>
                <td>{{ optional(optional($invoice->subscription)->package)->name ?? '---' }}</td>
                <td><x-report-money :amount="$invoice->amount" /></td>
                <td><x-report-money :amount="$invoice->vat_amount" /></td>
                <td><x-report-money :amount="$invoice->total_amount" /></td>
                <td>{{ $invoice->isPaid() ? __('admin.report.revenue.paid') : __('admin.report.revenue.pending') }}</td>
                <td>{{ $invoice->created_at ? $invoice->created_at->format('Y-m-d') : '---' }}</td>
                <td>{{ $invoice->paid_at ? $invoice->paid_at->format('Y-m-d') : '---' }}</td>
            </tr>
        @endforeach
    </x-report-table>

    <x-report-summary :qr-code="$settings['qr_code'] ?? null">
        <x-report-summary-row
            :label="__('admin.report.revenue.kpi.total_invoices')"
            :value="number_format($totalInvoices)"
        />
        <x-report-summary-row
            :label="__('admin.report.revenue.total_vat')"
            :value="number_format($totalVat, 2)"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('admin.report.revenue.kpi.pending_amount')"
            :value="number_format($pendingAmount, 2)"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('admin.report.revenue.kpi.paid_revenue')"
            :value="number_format($paidRevenue, 2)"
            :showCurrency="true"
            :highlight="true"
        />
    </x-report-summary>
</x-report-layout>
