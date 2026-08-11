@extends('admin.layouts.master')
@section('title')
    {{ __('admin.report.sales.title') }}
@endsection
@section('content')
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">{{ __('admin.report.sales.title') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.report.sales.subtitle') }}</p>
        </div>
        <button type="button" onclick="printReport()" class="btn btn-outline-theme ms-auto">
            <i class="bi bi-printer me-1"></i> {{ __('admin.report.sales.print') }}
        </button>
    </div>

    <div class="row g-3 mb-4">
        <x-stat-card
            :title="__('admin.report.sales.kpi.total_count')"
            icon="bi bi-receipt"
            gradient="linear-gradient(135deg, #0d6efd, #0b5ed7)"
            col-class="col-md-6 col-lg-3"
        >
            <x-slot:value><span id="summary_total_sales">0</span></x-slot:value>
        </x-stat-card>
        <x-stat-card
            :title="__('admin.report.sales.kpi.total_revenue')"
            icon="bi bi-currency-exchange"
            gradient="linear-gradient(135deg, #198754, #157347)"
            col-class="col-md-6 col-lg-3"
        >
            <x-slot:value><span id="summary_total_revenue">0</span> <x-riyal-icon size="sm" /></x-slot:value>
        </x-stat-card>
        <x-stat-card
            :title="__('admin.report.sales.kpi.total_weight')"
            icon="bi bi-box-seam"
            gradient="linear-gradient(135deg, #fd7e14, #ea5d0a)"
            col-class="col-md-6 col-lg-3"
        >
            <x-slot:value><span id="summary_total_weight">0</span></x-slot:value>
        </x-stat-card>
        <x-stat-card
            :title="__('admin.report.sales.kpi.net_owner')"
            icon="bi bi-person-badge"
            gradient="linear-gradient(135deg, #0dcaf0, #0aa2c0)"
            col-class="col-md-6 col-lg-3"
        >
            <x-slot:value><span id="summary_net_owner">0</span> <x-riyal-icon size="sm" /></x-slot:value>
        </x-stat-card>
    </div>

    @php
        $monthStart = \Illuminate\Support\Carbon::now()->startOfMonth()->format('Y-m-d');
        $monthEnd = \Illuminate\Support\Carbon::now()->endOfMonth()->format('Y-m-d');
    @endphp

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">{{ __('admin.report.sales.from_date') }}</label>
                    <input type="date" id="start_date" class="form-control" value="{{ $monthStart }}" data-default="{{ $monthStart }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">{{ __('admin.report.sales.to_date') }}</label>
                    <input type="date" id="end_date" class="form-control" value="{{ $monthEnd }}" data-default="{{ $monthEnd }}">
                </div>
                <div class="col-md-3">
                    <label for="status_filter" class="form-label">{{ __('admin.report.sales.status') }}</label>
                    <select id="status_filter" class="form-control">
                        <option value="">{{ __('admin.report.sales.all') }}</option>
                        <option value="1">{{ __('admin.report.sales.in_progress') }}</option>
                        <option value="2">{{ __('admin.report.sales.completed') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-wrap gap-2">
                        <button id="filterBtn" class="btn btn-primary">{{ __('admin.report.sales.filter') }}</button>
                        <button id="resetBtn" class="btn btn-outline-secondary">{{ __('admin.report.sales.reset') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover align-middle text-center mb-0" style="width:100%">
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
