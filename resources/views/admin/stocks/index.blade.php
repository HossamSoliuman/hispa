@extends('admin.layouts.master')

@section('title')
    {{ __('admin.stocks_admin.page_title') }}
@endsection

@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <style>
        .small-text th, .small-text td {
            font-size: 12px;
            text-align: center !important;
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.menu.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.stocks_admin.breadcrumb') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.stocks_admin.page_header') }}</h1>
            <p class="text-muted mb-0 small">{{ __('admin.stocks_admin.subtitle') }}</p>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="row mb-4 g-3">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #2980b9, #3498db);">
                <div class="card-body d-flex justify-content-between align-items-center text-white p-3">
                    <div>
                        <h6 class="mb-1 opacity-90">{{ __('admin.stocks_admin.stats.total_fish_types') }}</h6>
                        <h4 class="mb-0" id="stock_total_items">0</h4>
                    </div>
                    <i class="bi bi-box-seam fs-3 opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <div class="card-body d-flex justify-content-between align-items-center text-white p-3">
                    <div>
                        <h6 class="mb-1 opacity-90">{{ __('admin.stocks_admin.stats.total_weight') }}</h6>
                        <h4 class="mb-0" id="stock_total_weight"><span id="stock_total_weight_value">0</span> {{ __('admin.stocks_admin.unit_kg') }}</h4>
                    </div>
                    <i class="bi bi-speedometer2 fs-3 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <x-admin.components.datatable-filters
        formId="stocksFilters"
        :filters="[
            ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.stocks_admin.table.fish_name'), 'value' => request('search')],
        ]"
        :showArrow="false"
    />

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('admin.stocks_admin.page_header') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="stocksTable" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('admin.stocks_admin.table.id') }}</th>
                            <th>{{ __('admin.stocks_admin.table.fish_name') }}</th>
                            <th>{{ __('admin.stocks_admin.table.total_weight') }}</th>
                            <th>{{ __('admin.stocks_admin.table.unit') }}</th>
                            <th>{{ __('admin.stocks_admin.table.actions') }}</th>
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
            const unitKg = @json(__('admin.stocks_admin.unit_kg'));
            const noData = @json(__('admin.stocks_admin.no_data'));
            const languageOptions = (appLocale === 'ar')
                ? { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" }
                : {};

            if ($.fn.DataTable.isDataTable('#stocksTable')) {
                $('#stocksTable').DataTable().destroy();
            }

            $('#stocksTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.getStockData') }}",
                    dataSrc: function (json) {
                        $('#stock_total_items').text(json.total_items ?? 0);
                        const weight = parseFloat(json.total_weight) || 0;
                        $('#stock_total_weight_value').text(weight.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        return json.data ?? [];
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '60px' },
                    { data: 'name', name: 'name' },
                    { data: 'total_weight', name: 'total_weight' },
                    { data: 'unit', name: 'unit', width: '80px' },
                    { data: 'details', name: 'details', orderable: false, searchable: false, width: '100px' }
                ],
                order: [[1, 'asc']],
                responsive: true,
                pageLength: 25,
                language: Object.assign({}, languageOptions, { emptyTable: noData })
            });

            $('#stocksFilters').on('change keyup', 'select, input', function () {
                $('#stocksTable').DataTable().ajax.reload();
            });
        });
    </script>
@endsection
