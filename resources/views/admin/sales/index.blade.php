@extends('admin.layouts.master')

@section('title')
    {{ __('admin.menu.sales') }}
@endsection

@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <style>
        .small-text th, .small-text td { font-size: 12px; text-align: center !important; vertical-align: middle; }
    </style>
@endsection

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.menu.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.menu.sales') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.menu.sales') }}</h1>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #2980b9, #3498db);">
                <div class="card-body d-flex justify-content-between align-items-center text-white p-3">
                    <div>
                        <h6 class="mb-1 opacity-90">{{ __('admin.sales.total_items') ?? __('admin.table.id') }}</h6>
                        <h4 class="mb-0" id="sales_total_items">0</h4>
                    </div>
                    <i class="bi bi-receipt fs-3 opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <div class="card-body d-flex justify-content-between align-items-center text-white p-3">
                    <div>
                        <h6 class="mb-1 opacity-90">{{ __('admin.stocks_admin.stats.total_weight') }}</h6>
                        <h4 class="mb-0" id="sales_total_weight">0</h4>
                    </div>
                    <i class="bi bi-speedometer2 fs-3 opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
                <div class="card-body d-flex justify-content-between align-items-center text-white p-3">
                    <div>
                        <h6 class="mb-1 opacity-90">{{ __('admin.sales.total_amount') ?? __('admin.owner_stock_fish_quantity.total_value') }}</h6>
                        <h4 class="mb-0" id="sales_total_amount">0</h4>
                    </div>
                    <i class="bi bi-currency-exchange fs-3 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    @php
        $salesTypeOptions = [
            ['value' => '', 'label' => __('admin.filters.all')],
            ['value' => 'owner', 'label' => __('admin.menu.owner_sales')],
            ['value' => 'dalal', 'label' => __('admin.menu.dalal_sales')],
        ];
    @endphp
    <x-admin.components.datatable-filters
        formId="salesFilters"
        :filters="[
            ['type' => 'select-static', 'id' => 'type', 'name' => 'type', 'label' => __('admin.sales.seller_type') ?? __('admin.menu.sales'), 'options' => $salesTypeOptions, 'selected' => request('type')],
            ['type' => 'daterange', 'id' => 'date_range', 'nameFrom' => 'from_date', 'nameTo' => 'to_date', 'label' => __('admin.sales.date') ?? __('admin.trips.date'), 'valueFrom' => request('from_date'), 'valueTo' => request('to_date')],
        ]"
        :showArrow="false"
    />

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('admin.menu.sales') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="salesTable" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('admin.sales.number') ?? __('admin.invoices.invoice_number') }}</th>
                            <th>{{ __('admin.sales.seller') ?? __('admin.owner_stock_details.owner_name') }}</th>
                            <th>{{ __('admin.sales.customer') ?? __('admin.customers.table.name') }}</th>
                            <th>{{ __('admin.sales.payment_method') }}</th>
                            <th>{{ __('admin.sales.total_weight') ?? __('admin.stocks_admin.table.total_weight') }}</th>
                            <th>{{ __('admin.sales.total_price') ?? __('admin.owner_stock_fish_quantity.total_price') }}</th>
                            <th>{{ __('admin.sales.net_owner_amount') ?? __('admin.invoices.amount') }}</th>
                            <th>{{ __('admin.sales.date') ?? __('admin.owner_stock_details.table.date') }}</th>
                            <th>{{ __('admin.sales.status') }}</th>
                            <th>{{ __('admin.owner_stocks.table.details') }}</th>
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
    <script>
        $(function () {
            const appLocale = '{{ app()->getLocale() }}';
            const noData = @json(__('admin.owner_stocks.statistics.no_data'));
            const languageOptions = (appLocale === 'ar') ? { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" } : {};

            if ($.fn.DataTable.isDataTable('#salesTable')) {
                $('#salesTable').DataTable().destroy();
            }

            $('#salesTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.getSalesData') }}",
                    data: function (d) {
                        var form = document.getElementById('salesFilters');
                        if (form) {
                            var fd = new FormData(form);
                            fd.forEach(function (value, key) { d[key] = value; });
                        }
                    },
                    dataSrc: function (json) {
                        $('#sales_total_items').text(json.total_items ?? 0);
                        $('#sales_total_weight').text((parseFloat(json.total_weight) || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) + ' {{ __("admin.stocks_admin.unit_kg") }}');
                        $('#sales_total_amount').text((parseFloat(json.total_amount) || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }));
                        return json.data ?? [];
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
                    { data: 'number' },
                    { data: 'seller' },
                    { data: 'customer' },
                    { data: 'payment_method' },
                    { data: 'total_weight', render: function (d) { return (parseFloat(d) || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) + ' {{ __("admin.stocks_admin.unit_kg") }}'; } },
                    { data: 'total_price' },
                    { data: 'net_owner_amount' },
                    { data: 'date' },
                    { data: 'status' },
                    { data: 'details', orderable: false, searchable: false, width: '80px' }
                ],
                order: [[8, 'desc']],
                responsive: true,
                pageLength: 25,
                language: Object.assign({}, languageOptions, { emptyTable: noData })
            });

            $('#salesFilters').on('change keyup', 'select, input', function () {
                $('#salesTable').DataTable().ajax.reload();
            });
        });
    </script>
@endsection
