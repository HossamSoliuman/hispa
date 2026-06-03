@extends('admin.layouts.master')
@section('title')
    {{ __('admin.report.stock.title') }}
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
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.report.stock.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <button type="button" onclick="printReport()" class="btn btn-outline-theme btn-equal">
                <i class="bi bi-printer me-1"></i> {{ __('admin.report.stock.print') }}
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @include('owner.components.stat-card', [
            'title' => __('admin.report.stock.total_fish_count'),
            'value' => new \Illuminate\Support\HtmlString('<span id="totalFishCount">0</span>'),
            'icon' => 'bi bi-fish',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.stock.total_weight'),
            'value' => new \Illuminate\Support\HtmlString('<span id="totalWeight">0</span> ' . __('admin.units.kg')),
            'icon' => 'bi bi-box-seam',
            'gradient' => 'linear-gradient(135deg, #fd7e14, #ea5d0a)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.stock.added_by'),
            'value' => new \Illuminate\Support\HtmlString('<span id="totalRecords">0</span>'),
            'icon' => 'bi bi-list-ul',
            'gradient' => 'linear-gradient(135deg, #198754, #157347)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.stock.diff_kg'),
            'value' => new \Illuminate\Support\HtmlString('<span id="totalDiff">0</span>'),
            'icon' => 'bi bi-arrow-left-right',
            'gradient' => 'linear-gradient(135deg, #0dcaf0, #0aa2c0)',
            'colClass' => 'col-md-3 col-sm-6',
        ])
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="start_date">{{ __('admin.report.stock.from_date') }}</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('admin.report.stock.to_date') }}</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="fish_type_filter">{{ __('admin.report.stock.fish_type') }}</label>
                    <select id="fish_type_filter" class="form-control">
                        <option value="">{{ __('admin.report.stock.all') }}</option>
                        @foreach($fish as $f)
                            <option value="{{ $f->id }}">{{ $f->name ?? $f->scientific_name ?? $f->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button id="filterBtn" class="btn btn-primary btn-sm">{{ __('admin.report.stock.filter') }}</button>
                    <button id="resetBtn" class="btn btn-secondary btn-sm">{{ __('admin.report.stock.reset') }}</button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ __('admin.table.id') }}</th>
                            <th>{{ __('admin.report.stock.fish_name') }}</th>
                            <th>{{ __('admin.report.stock.added_qty') }}</th>
                            <th>{{ __('admin.report.stock.corrected_qty') }}</th>
                            <th>{{ __('admin.report.stock.total_kg') }}</th>
                            <th>{{ __('admin.report.stock.diff_kg') }}</th>
                            <th>{{ __('admin.report.stock.added_by') }}</th>
                            <th>{{ __('admin.report.stock.corrected_by') }}</th>
                            <th>{{ __('admin.report.stock.created_at') }}</th>
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
            var url = '{{ route("admin.stock-report.print") }}?';
            if ($('#start_date').val()) url += 'start_date=' + $('#start_date').val() + '&';
            if ($('#end_date').val()) url += 'end_date=' + $('#end_date').val() + '&';
            if ($('#fish_type_filter').val()) url += 'fish_type=' + $('#fish_type_filter').val() + '&';
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
                    url: "{{ route('admin.getStockDataReport') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.fish_type = $('#fish_type_filter').val();
                        return d;
                    },
                    dataSrc: function(json) {
                        if (json.total_fish_count !== undefined) $('#totalFishCount').text(json.total_fish_count);
                        if (json.totalWeight !== undefined) $('#totalWeight').text(json.totalWeight);
                        if (json.recordsTotal !== undefined) $('#totalRecords').text(json.recordsTotal);
                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'name', name: 'name' },
                    { data: 'weight_captain', name: 'weight_captain' },
                    { data: 'weight_counter', name: 'weight_counter' },
                    { data: 'total_weight', name: 'total_weight' },
                    { data: 'weight_difference', name: 'weight_difference' },
                    { data: 'added_by', name: 'added_by' },
                    { data: 'correct_by', name: 'correct_by' },
                    { data: 'date', name: 'date' }
                ],
                responsive: true
            });
            $('#filterBtn').on('click', function() { table.ajax.reload(); });
            $('#resetBtn').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#fish_type_filter').val('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
