<x-report-layout
    :title="__('admin.report.revenue.print_title')"
    :document-number="$documentNumber"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? null"
    orientation="landscape">

    <x-report-header
        :document-number="$documentNumber"
        :settings="$settings"
        :title="__('admin.report.revenue.print_title')"
        :title-en="__('admin.report.revenue.print_title_en')"
    />

    <p class="report-subtitle">
        {{ __('admin.report.revenue.period', ['from' => $from, 'to' => $to]) }}
    </p>

    <table class="info-bar">
        <tr>
            <td>
                <span class="ib-label">{{ __('admin.report.revenue.date_from') }}</span>
                <span class="ib-value">{{ $from }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('admin.report.revenue.date_to') }}</span>
                <span class="ib-value">{{ $to }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('admin.report.revenue.status_th') }}</span>
                <span class="ib-value">
                    {{ match ($status) {
                        'paid' => __('admin.report.revenue.paid'),
                        'pending' => __('admin.report.revenue.pending'),
                        null => __('admin.report.revenue.all_statuses'),
                        default => $status,
                    } }}
                </span>
            </td>
        </tr>
    </table>

    <x-report-stats :items="[
        ['label' => __('admin.report.revenue.kpi.total_invoices'), 'value' => number_format($totalInvoices)],
        ['label' => __('admin.report.revenue.kpi.paid_revenue'), 'value' => $paidRevenue, 'money' => true],
        ['label' => __('admin.report.revenue.kpi.pending_amount'), 'value' => $pendingAmount, 'money' => true],
        ['label' => __('admin.report.revenue.kpi.active_subscriptions'), 'value' => number_format($activeSubscriptions)],
    ]" />

    @if ($invoices->isEmpty())
        <div class="alert alert-warning">
            <strong>{{ __('admin.report.revenue.no_data') }}</strong>
            <p class="mb-0 text-muted">{{ __('admin.report.revenue.adjust_filters_hint') }}</p>
        </div>
    @else
        <table class="report-table block">
            <thead>
                <tr>
                    <th class="col-num" style="width: 4%;">#</th>
                    <th class="col-text" style="width: 13%;">{{ __('admin.report.revenue.invoice_number') }}</th>
                    <th class="col-text" style="width: 14%;">{{ __('admin.report.revenue.owner') }}</th>
                    <th class="col-text" style="width: 12%;">{{ __('admin.report.revenue.plan') }}</th>
                    <th class="col-num" style="width: 10%;">{{ __('admin.report.revenue.amount') }}</th>
                    <th class="col-num" style="width: 9%;">{{ __('admin.report.revenue.vat') }}</th>
                    <th class="col-num" style="width: 10%;">{{ __('admin.report.revenue.total') }}</th>
                    <th class="col-text" style="width: 8%;">{{ __('admin.report.revenue.status_th') }}</th>
                    <th class="col-num" style="width: 10%;">{{ __('admin.report.revenue.created_at') }}</th>
                    <th class="col-num" style="width: 10%;">{{ __('admin.report.revenue.paid_at') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $index => $invoice)
                    <tr>
                        <td class="col-num">{{ $index + 1 }}</td>
                        <td class="col-text">{{ $invoice->invoice_number }}</td>
                        <td class="col-text">{{ optional($invoice->user)->name ?? '---' }}</td>
                        <td class="col-text">{{ optional(optional($invoice->subscription)->package)->name ?? '---' }}</td>
                        <td class="col-num"><x-report-money :amount="$invoice->amount" /></td>
                        <td class="col-num"><x-report-money :amount="$invoice->vat_amount" /></td>
                        <td class="col-num"><x-report-money :amount="$invoice->total_amount" /></td>
                        <td class="col-text">{{ $invoice->isPaid() ? __('admin.report.revenue.paid') : __('admin.report.revenue.pending') }}</td>
                        <td class="col-num">{{ $invoice->created_at ? $invoice->created_at->format('Y-m-d') : '---' }}</td>
                        <td class="col-num">{{ $invoice->paid_at ? $invoice->paid_at->format('Y-m-d') : '---' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="net-row">
                    <td class="col-text" colspan="4">{{ __('admin.report.revenue.total') }}</td>
                    <td class="col-num"><x-report-money :amount="$invoices->sum('amount')" /></td>
                    <td class="col-num"><x-report-money :amount="$invoices->sum('vat_amount')" /></td>
                    <td class="col-num"><x-report-money :amount="$invoices->sum('total_amount')" /></td>
                    <td class="col-text"></td>
                    <td class="col-num"></td>
                    <td class="col-num"></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <table class="report-footer">
        <tr>
            <td class="rf-text">
                {{ $settings['company_name'] ?? $settings['title'] ?? '' }} &mdash;
                {{ __('admin.report.revenue.all_rights_reserved') }} &copy; {{ date('Y') }}
            </td>
        </tr>
    </table>
</x-report-layout>
