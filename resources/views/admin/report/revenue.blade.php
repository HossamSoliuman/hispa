@extends('admin.layouts.master')
@section('title')
    {{ __('admin.report.revenue.title') }}
@endsection
@section('content')
    <div class="mb-4 d-flex align-items-start">
        <div>
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.report.revenue.title') }}</h2>
            <p class="mb-0 text-muted">{{ __('admin.report.revenue.subtitle') }}</p>
        </div>
        <div class="ms-auto">
            <button type="button" onclick="printReport()" class="btn btn-outline-theme">
                <i class="bi bi-printer me-1"></i> {{ __('admin.report.revenue.print') }}
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <x-stat-card
            :title="__('admin.report.revenue.kpi.total_invoices')"
            icon="bi bi-receipt"
            colClass="col-md-6 col-lg-3"
        >
            <x-slot:value><span>{{ number_format($totalInvoices) }}</span></x-slot:value>
        </x-stat-card>
        <x-stat-card
            :title="__('admin.report.revenue.kpi.paid_revenue')"
            icon="bi bi-cash-coin"
            colClass="col-md-6 col-lg-3"
        >
            <x-slot:value><span>{{ number_format($paidRevenue, 2) }}</span> <x-riyal-icon size="sm" /></x-slot:value>
        </x-stat-card>
        <x-stat-card
            :title="__('admin.report.revenue.kpi.pending_amount')"
            icon="bi bi-hourglass-split"
            colClass="col-md-6 col-lg-3"
        >
            <x-slot:value><span>{{ number_format($pendingAmount, 2) }}</span> <x-riyal-icon size="sm" /></x-slot:value>
        </x-stat-card>
        <x-stat-card
            :title="__('admin.report.revenue.kpi.active_subscriptions')"
            icon="bi bi-people"
            colClass="col-md-6 col-lg-3"
        >
            <x-slot:value><span>{{ number_format($activeSubscriptions) }}</span></x-slot:value>
        </x-stat-card>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.revenue-report') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="start_date" class="form-label">{{ __('admin.report.revenue.from_date') }}</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $from }}">
                </div>
                <div class="col-12 col-md-3">
                    <label for="end_date" class="form-label">{{ __('admin.report.revenue.to_date') }}</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $to }}">
                </div>
                <div class="col-12 col-md-3">
                    <label for="payment_status" class="form-label">{{ __('admin.report.revenue.status') }}</label>
                    <select name="payment_status" id="payment_status" class="form-select">
                        <option value="">{{ __('admin.report.revenue.all') }}</option>
                        <option value="paid" @selected(request('payment_status') === 'paid')>{{ __('admin.report.revenue.paid') }}</option>
                        <option value="pending" @selected(request('payment_status') === 'pending')>{{ __('admin.report.revenue.pending') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">{{ __('admin.report.revenue.filter') }}</button>
                    <a href="{{ route('admin.revenue-report') }}" class="btn btn-outline-secondary">{{ __('admin.report.revenue.reset') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="revenueTable" class="table table-bordered table-hover align-middle text-center mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('admin.report.revenue.id') }}</th>
                            <th>{{ __('admin.report.revenue.invoice_number') }}</th>
                            <th>{{ __('admin.report.revenue.owner') }}</th>
                            <th>{{ __('admin.report.revenue.plan') }}</th>
                            <th>{{ __('admin.report.revenue.amount') }}</th>
                            <th>{{ __('admin.report.revenue.vat') }}</th>
                            <th>{{ __('admin.report.revenue.total') }}</th>
                            <th>{{ __('admin.report.revenue.status_th') }}</th>
                            <th>{{ __('admin.report.revenue.created_at') }}</th>
                            <th>{{ __('admin.report.revenue.paid_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $index => $invoice)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ optional($invoice->user)->name ?? '---' }}</td>
                                <td>{{ optional(optional($invoice->subscription)->package)->name ?? '---' }}</td>
                                <td>{{ number_format((float) $invoice->amount, 2) }} <x-riyal-icon size="sm" /></td>
                                <td>{{ number_format((float) $invoice->vat_amount, 2) }} <x-riyal-icon size="sm" /></td>
                                <td>{{ number_format((float) $invoice->total_amount, 2) }} <x-riyal-icon size="sm" /></td>
                                <td>
                                    @if($invoice->isPaid())
                                        <span class="badge bg-success">{{ __('admin.report.revenue.paid') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ __('admin.report.revenue.pending') }}</span>
                                    @endif
                                </td>
                                <td>{{ $invoice->created_at ? $invoice->created_at->format('Y-m-d') : '---' }}</td>
                                <td>{{ $invoice->paid_at ? $invoice->paid_at->format('Y-m-d') : '---' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">{{ __('admin.report.revenue.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function printReport() {
            var url = '{{ route("admin.revenue-report.print") }}?';
            if ($('#start_date').val()) url += 'start_date=' + $('#start_date').val() + '&';
            if ($('#end_date').val()) url += 'end_date=' + $('#end_date').val() + '&';
            if ($('#payment_status').val()) url += 'payment_status=' + $('#payment_status').val() + '&';
            window.open(url, '_blank');
        }
    </script>
@endsection
