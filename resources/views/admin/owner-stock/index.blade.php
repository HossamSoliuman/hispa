@extends('admin.layouts.master')

@section('title')
    {{ __('admin.owner_stocks.page_title') }}
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
                <li class="breadcrumb-item active">{{ __('admin.owner_stocks.breadcrumb_owner') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.owner_stocks.page_header') }}</h1>
            <p class="text-muted mb-0 small">{{ __('admin.owner_stock_fish_quantity.subtitle') }}</p>
        </div>
    </div>

    @php
        $ownerStockOwnerOptions = [['value' => '', 'label' => __('admin.owner_stock_fish_quantity.all_owners')]];
        foreach ($owners ?? [] as $o) {
            $ownerStockOwnerOptions[] = ['value' => (string) $o->id, 'label' => $o->name];
        }
    @endphp
    <x-admin.components.datatable-filters
        formId="ownerStockFilters"
        formAction="{{ route('admin.owner-stock.index') }}"
        formMethod="get"
        :showSearchButton="true"
        :showResetButton="true"
        :filters="[
            ['type' => 'date', 'id' => 'from', 'name' => 'from', 'label' => __('admin.owner_stock_fish_quantity.from_date'), 'value' => $from ?? request('from')],
            ['type' => 'date', 'id' => 'to', 'name' => 'to', 'label' => __('admin.owner_stock_fish_quantity.to_date'), 'value' => $to ?? request('to')],
            ['type' => 'select-static', 'id' => 'owner_id', 'name' => 'owner_id', 'label' => __('admin.owner_stock_details.owner_name'), 'options' => $ownerStockOwnerOptions, 'selected' => request('owner_id')],
        ]"
        :showArrow="false"
    />

    {{-- Statistics --}}
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #2980b9, #3498db);">
                <div class="card-body d-flex justify-content-between align-items-center text-white p-3">
                    <div>
                        <h6 class="mb-1 opacity-90">{{ __('admin.owner_stocks.statistics.owners_count') }}</h6>
                        <h4 class="mb-0" id="owner_stock_total_owners">0</h4>
                    </div>
                    <i class="bi bi-people-fill fs-3 opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <div class="card-body d-flex justify-content-between align-items-center text-white p-3">
                    <div>
                        <h6 class="mb-1 opacity-90">{{ __('admin.owner_stock_fish_quantity.total_quantity') }}</h6>
                        <h4 class="mb-0" id="owner_stock_total_quantity">0</h4>
                    </div>
                    <i class="bi bi-box-seam fs-3 opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
                <div class="card-body d-flex justify-content-between align-items-center text-white p-3">
                    <div>
                        <h6 class="mb-1 opacity-90">{{ __('admin.owner_stock_fish_quantity.total_value') }}</h6>
                        <h4 class="mb-0" id="owner_stock_total_value">0</h4>
                    </div>
                    <i class="bi bi-currency-exchange fs-3 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('admin.owner_stocks.table.owner_name') }} / {{ __('admin.owner_stocks.table.total') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="ownerStockTable" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('admin.owner_stocks.table.id') }}</th>
                            <th>{{ __('admin.owner_stocks.table.owner_name') }}</th>
                            <th>{{ __('admin.owner_stock_fish_quantity.quantity') }}</th>
                            <th>{{ __('admin.owner_stock_fish_quantity.total_value_col') }}</th>
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

            const params = new URLSearchParams(window.location.search);
            const from = params.get('from') || "{{ $from }}";
            const to = params.get('to') || "{{ $to }}";
            const ownerId = params.get('owner_id') || '';

            if ($.fn.DataTable.isDataTable('#ownerStockTable')) {
                $('#ownerStockTable').DataTable().destroy();
            }

            $('#ownerStockTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.getOwnerStockData') }}",
                    data: function () { return { from: from, to: to, owner_id: ownerId }; },
                    dataSrc: function (json) {
                        $('#owner_stock_total_owners').text(json.total_owners ?? 0);
                        const qty = parseFloat(json.total_quantity) || 0;
                        $('#owner_stock_total_quantity').text(qty.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        const val = parseFloat(json.total_value) || 0;
                        $('#owner_stock_total_value').text(val.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        return json.data ?? [];
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, width: '60px' },
                    { data: 'owner_name' },
                    { data: 'total_quantity' },
                    { data: 'total_value' },
                    { data: 'details', orderable: false, searchable: false, width: '100px' }
                ],
                order: [[1, 'asc']],
                responsive: true,
                pageLength: 25,
                language: Object.assign({}, languageOptions, { emptyTable: noData })
            });
        });
    </script>
@endsection
