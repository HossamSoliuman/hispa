<x-report-layout :settings="$settings ?? []">
    <x-report-header
        :settings="$settings"
        :title="__('admin.report.revenue.print_title')"
    />

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
</x-report-layout>
