@extends('admin.layouts.master')

@section('title')
    {{ __('admin.stocks_admin.detail.title') }} - {{ $fish ? $fish->name : $id }}
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
                <li class="breadcrumb-item"><a href="{{ route('admin.stocks.index') }}">{{ __('admin.stocks_admin.breadcrumb') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.stocks_admin.detail.title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.stocks_admin.detail.title') }} @if($fish)<small class="text-muted">– {{ $fish->name }}</small>@endif</h1>
            <p class="text-muted mb-0 small">{{ __('admin.stocks_admin.detail.subtitle') }}</p>
        </div>
        <div class="ms-auto">
            <a href="{{ route('admin.stocks.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.stocks_admin.back_to_list') }}
            </a>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #2980b9, #3498db);">
                <div class="card-body d-flex justify-content-between align-items-center text-white p-3">
                    <div>
                        <h6 class="mb-1 opacity-90">{{ __('admin.stocks_admin.detail.total_records') }}</h6>
                        <h4 class="mb-0" id="detail_total_items">0</h4>
                    </div>
                    <i class="bi bi-list-ul fs-3 opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <div class="card-body d-flex justify-content-between align-items-center text-white p-3">
                    <div>
                        <h6 class="mb-1 opacity-90">{{ __('admin.stocks_admin.detail.total_weight') }}</h6>
                        <h4 class="mb-0"><span id="detail_total_weight_value">0</span> {{ __('admin.stocks_admin.unit_kg') }}</h4>
                    </div>
                    <i class="bi bi-speedometer2 fs-3 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('admin.stocks_admin.detail.title') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="stockDetailTable" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('admin.stocks_admin.table.id') }}</th>
                            <th>{{ __('admin.stocks_admin.table.fish_name') }}</th>
                            <th>{{ __('admin.stocks_admin.detail.captain_name') }}</th>
                            <th>{{ __('admin.stocks_admin.detail.weight_captain') }}</th>
                            <th>{{ __('admin.stocks_admin.detail.counter_name') }}</th>
                            <th>{{ __('admin.stocks_admin.detail.weight_counter') }}</th>
                            <th>{{ __('admin.stocks_admin.detail.weight') }}</th>
                            <th>{{ __('admin.stocks_admin.detail.unit') }}</th>
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
            const noData = @json(__('admin.stocks_admin.no_data'));
            const languageOptions = (appLocale === 'ar')
                ? { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" }
                : {};

            if ($.fn.DataTable.isDataTable('#stockDetailTable')) {
                $('#stockDetailTable').DataTable().destroy();
            }

            $('#stockDetailTable').DataTable({
                processing: true,
                serverSide: false,
                language: Object.assign({}, languageOptions, { emptyTable: noData }),
                ajax: {
                    url: "{{ route('admin.stocks.detail', ['id' => $id]) }}",
                    dataSrc: function (json) {
                        $('#detail_total_items').text(json.total_items ?? 0);
                        const weight = parseFloat(json.total_weight) || 0;
                        $('#detail_total_weight_value').text(weight.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        return json.data ?? [];
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
                    { data: 'name' },
                    { data: 'captain_name' },
                    { data: 'weight_captain' },
                    { data: 'counter_name' },
                    { data: 'weight_counter' },
                    { data: 'weight' },
                    { data: 'unit', width: '80px' }
                ],
                order: [[0, 'asc']],
                responsive: true,
                pageLength: 25
            });
        });
    </script>
@endsection
