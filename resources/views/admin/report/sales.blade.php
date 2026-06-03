@extends('admin.layouts.master')
@section('title')
    {{ __('admin.report.sales.title') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet">
    <style>
        #datatableDefault th, #datatableDefault td { text-align: center !important; vertical-align: middle; }
        .small-text th, .small-text td { font-size: 12px; text-align: center !important; vertical-align: middle; font-weight: bold; }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.report.sales.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
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
            'colClass' => 'col-md-3 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.sales.kpi.total_revenue'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_total_revenue">0</span> ' . __('admin.units.sar')),
            'icon' => 'bi bi-currency-exchange',
            'gradient' => 'linear-gradient(135deg, #198754, #157347)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.sales.kpi.total_weight'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_total_weight">0</span> ' . __('admin.units.kg')),
            'icon' => 'bi bi-box-seam',
            'gradient' => 'linear-gradient(135deg, #fd7e14, #ea5d0a)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.sales.kpi.net_owner'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_net_owner">0</span> ' . __('admin.units.sar')),
            'icon' => 'bi bi-person-badge',
            'gradient' => 'linear-gradient(135deg, #0dcaf0, #0aa2c0)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="start_date">{{ __('admin.report.sales.from_date') }}</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('admin.report.sales.to_date') }}</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="status_filter">{{ __('admin.report.sales.status') }}</label>
                    <select id="status_filter" class="form-control">
                        <option value="">{{ __('admin.report.sales.all') }}</option>
                        <option value="1">{{ __('admin.report.sales.in_progress') ?? __('admin.status.active') }}</option>
                        <option value="2">{{ __('admin.report.sales.completed') ?? __('admin.trips.completed') }}</option>
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
                            <th>{{ __('admin.report.sales.commission_value_broker') }}</th>
                            <th>{{ __('admin.report.sales.labor_commission_value_broker') }}</th>
                            <th>{{ __('admin.report.sales.total_amount') }}</th>
                            <th>{{ __('admin.report.sales.net_owner') }}</th>
                            <th>{{ __('admin.report.sales.remaining_broker') }}</th>
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
    <script src="{{ asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script type="text/javascript">
        function printReport() {
            var url = '{{ route("admin.sales-report.print") }}?';
            if ($('#start_date').val()) url += 'start_date=' + $('#start_date').val() + '&';
            if ($('#end_date').val()) url += 'end_date=' + $('#end_date').val() + '&';
            if ($('#status_filter').val()) url += 'status=' + $('#status_filter').val() + '&';
            window.open(url, '_blank');
        }
        $(function() {
            var appLocale = '{{ app()->getLocale() }}';
            var languageOptions = appLocale === 'ar' ? { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" } : {};
            if ($.fn.DataTable.isDataTable('#datatableDefault')) $('#datatableDefault').DataTable().destroy();
            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,
                language: languageOptions,
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
                        if (json.total_weight !== undefined) $('#summary_total_weight').text(parseFloat(json.total_weight).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
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
                    { data: 'commission_rate', name: 'commission_rate' },
                    { data: 'labor_rate', name: 'labor_rate' },
                    { data: 'total_price', name: 'total_price' },
                    { data: 'net_owner_amount', name: 'net_owner_amount' },
                    { data: 'remaining_total', name: 'remaining_total' },
                    { data: 'date', name: 'date' }
                ],
                responsive: true
            });
            $('#filterBtn').on('click', function() { table.ajax.reload(); });
            $('#resetBtn').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#status_filter').val('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
