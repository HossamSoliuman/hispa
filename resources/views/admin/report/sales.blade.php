@extends('admin.layouts.master')
@section('title')
    {{ __('admin.report.sales.title') }}
@endsection
@section('css')
    <style>
        #datatableDefault th,
        #datatableDefault td {
            text-align: center !important;
            vertical-align: middle;
        }

        .small-text th,
        .small-text td {
            font-size: 12px;
            text-align: center !important;
            vertical-align: middle;
            font-weight: bold;
        }
    </style>
@endsection
@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.reports-hub') }}">{{ __('admin.report.sales.report') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.report.sales.title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.report.sales.title') }}</h1>
        </div>
        <div class="ms-auto">
            <button type="button" onclick="printReport()" class="btn btn-outline-theme btn-equal">
                <i class="bi bi-printer me-1"></i> {{ __('admin.report.sales.print') }}
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @include('owner.components.stat-card', [
            'title' => __('admin.report.sales.kpi.total_count'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_total_sales">0</span>'),
            'icon' => 'bi bi-receipt',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-6 col-lg-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.sales.kpi.total_revenue'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_total_revenue">0</span> ' . view('components.riyal-icon', ['size' => 'sm'])->render()),
            'icon' => 'bi bi-currency-exchange',
            'gradient' => 'linear-gradient(135deg, #198754, #157347)',
            'colClass' => 'col-md-6 col-lg-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.sales.kpi.total_weight'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_total_weight">0</span>'),
            'icon' => 'bi bi-box-seam',
            'gradient' => 'linear-gradient(135deg, #fd7e14, #ea5d0a)',
            'colClass' => 'col-md-6 col-lg-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.sales.kpi.net_owner'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_net_owner">0</span> ' . view('components.riyal-icon', ['size' => 'sm'])->render()),
            'icon' => 'bi bi-person-badge',
            'gradient' => 'linear-gradient(135deg, #0dcaf0, #0aa2c0)',
            'colClass' => 'col-md-6 col-lg-3',
        ])
    </div>

    @php
        $monthStart = \Illuminate\Support\Carbon::now()->startOfMonth()->format('Y-m-d');
        $monthEnd = \Illuminate\Support\Carbon::now()->endOfMonth()->format('Y-m-d');
    @endphp

    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="start_date">{{ __('admin.report.sales.from_date') }}</label>
                    <input type="date" id="start_date" class="form-control" value="{{ $monthStart }}" data-default="{{ $monthStart }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('admin.report.sales.to_date') }}</label>
                    <input type="date" id="end_date" class="form-control" value="{{ $monthEnd }}" data-default="{{ $monthEnd }}">
                </div>
                <div class="col-md-3">
                    <label for="status_filter">{{ __('admin.report.sales.status') }}</label>
                    <select id="status_filter" class="form-control">
                        <option value="">{{ __('admin.report.sales.all') }}</option>
                        <option value="1">{{ __('admin.report.sales.in_progress') }}</option>
                        <option value="2">{{ __('admin.report.sales.completed') }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button id="filterBtn" class="btn btn-primary btn-sm">{{ __('admin.report.sales.filter') }}</button>
                    <button id="resetBtn" class="btn btn-secondary btn-sm">{{ __('admin.report.sales.reset') }}</button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ __('admin.table.id') }}</th>
                            <th>{{ __('admin.report.sales.invoice_number') }}</th>
                            <th>{{ __('admin.report.sales.status_th') }}</th>
                            <th>{{ __('admin.report.sales.customer') }}</th>
                            <th>{{ __('admin.report.sales.payment_method') }}</th>
                            <th>{{ __('admin.report.sales.total_weight') }}</th>
                            <th>{{ __('admin.report.sales.total_amount') }}</th>
                            <th>{{ __('admin.report.sales.issued_at') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function printReport() {
            var url = '{{ route('admin.sales-report.print') }}?';
            if ($('#start_date').val()) url += 'start_date=' + $('#start_date').val() + '&';
            if ($('#end_date').val()) url += 'end_date=' + $('#end_date').val() + '&';
            if ($('#status_filter').val()) url += 'status=' + $('#status_filter').val() + '&';
            window.open(url, '_blank');
        }

        $(function() {
            var appLocale = '{{ app()->getLocale() }}';
            var languageOptions = appLocale === 'ar' ? { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" } : {};

            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }

            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,
                dom: "<'row mb-3'<'col-md-4'l><'col-md-4'f><'col-md-4 text-md-end'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",
                language: languageOptions,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, '{{ __('admin.report.sales.all_label') }}']
                ],
                pageLength: 10,
                ajax: {
                    url: "{{ route('admin.getSalesDataReport') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status_filter').val();
                        return d;
                    },
                    dataSrc: function(json) {
                        if (json.total_sales !== undefined) $('#summary_total_sales').text(json.total_sales);
                        if (json.total_revenue !== undefined) $('#summary_total_revenue').text(parseFloat(json.total_revenue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        if (json.total_weight !== undefined) $('#summary_total_weight').text(json.total_weight);
                        if (json.net_owner_amount !== undefined) $('#summary_net_owner').text(parseFloat(json.net_owner_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'number', name: 'number' },
                    { data: 'status', name: 'status' },
                    { data: 'customer', name: 'customer' },
                    { data: 'payment_method', name: 'payment_method' },
                    { data: 'total_weight', name: 'total_weight' },
                    { data: 'total_price', name: 'total_price' },
                    { data: 'date', name: 'date' }
                ],
                buttons: [],
                responsive: false,
                scrollX: true
            });

            $('#filterBtn').on('click', function() { table.ajax.reload(); });
            $('#resetBtn').on('click', function() {
                $('#start_date').val($('#start_date').data('default'));
                $('#end_date').val($('#end_date').data('default'));
                $('#status_filter').val('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
