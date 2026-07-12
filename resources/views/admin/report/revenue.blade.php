@extends('admin.layouts.master')
@section('title')
    {{ __('admin.report.revenue.title') }}
@endsection
@section('css')
    <style>
        #revenueTable th, #revenueTable td { text-align: center !important; vertical-align: middle; font-size: 12px; }
        #revenueTable th { font-weight: bold; }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.report.revenue.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <button type="button" onclick="printReport()" class="btn btn-outline-theme btn-equal">
                <i class="bi bi-printer me-1"></i> {{ __('admin.report.revenue.print') }}
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @include('owner.components.stat-card', [
            'title' => __('admin.report.revenue.kpi.total_invoices'),
            'value' => new \Illuminate\Support\HtmlString('<span>' . number_format($totalInvoices) . '</span>'),
            'icon' => 'bi bi-receipt',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.revenue.kpi.paid_revenue'),
            'value' => new \Illuminate\Support\HtmlString('<span>' . number_format($paidRevenue, 2) . '</span> ' . view('components.riyal-icon')->render()),
            'icon' => 'bi bi-cash-coin',
            'gradient' => 'linear-gradient(135deg, #198754, #157347)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.revenue.kpi.pending_amount'),
            'value' => new \Illuminate\Support\HtmlString('<span>' . number_format($pendingAmount, 2) . '</span> ' . view('components.riyal-icon')->render()),
            'icon' => 'bi bi-hourglass-split',
            'gradient' => 'linear-gradient(135deg, #fd7e14, #ea5d0a)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.revenue.kpi.active_subscriptions'),
            'value' => new \Illuminate\Support\HtmlString('<span>' . number_format($activeSubscriptions) . '</span>'),
            'icon' => 'bi bi-people',
            'gradient' => 'linear-gradient(135deg, #0dcaf0, #0aa2c0)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.revenue-report') }}" class="row mb-3">
                <div class="col-md-3">
                    <label for="start_date">{{ __('admin.report.revenue.from_date') }}</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('admin.report.revenue.to_date') }}</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="payment_status">{{ __('admin.report.revenue.status') }}</label>
                    <select name="payment_status" id="payment_status" class="form-control">
                        <option value="">{{ __('admin.report.revenue.all') }}</option>
                        <option value="paid" @selected(request('payment_status') === 'paid')>{{ __('admin.report.revenue.paid') }}</option>
                        <option value="pending" @selected(request('payment_status') === 'pending')>{{ __('admin.report.revenue.pending') }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">{{ __('admin.report.revenue.filter') }}</button>
                    <a href="{{ route('admin.revenue-report') }}" class="btn btn-secondary btn-sm">{{ __('admin.report.revenue.reset') }}</a>
                </div>
            </form>
            <div class="table-responsive">
                <table id="revenueTable" class="table table-sm table-bordered table-hover text-center" style="width:100%">
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
                                <td>{{ number_format((float) $invoice->amount, 2) }} {!! view('components.riyal-icon')->render() !!}</td>
                                <td>{{ number_format((float) $invoice->vat_amount, 2) }} {!! view('components.riyal-icon')->render() !!}</td>
                                <td>{{ number_format((float) $invoice->total_amount, 2) }} {!! view('components.riyal-icon')->render() !!}</td>
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
