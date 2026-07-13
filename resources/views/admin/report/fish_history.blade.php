@extends('admin.layouts.master')
@section('title')
    {{ __('admin.report.fish_history.title') }}
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
                <li class="breadcrumb-item"><a href="{{ route('admin.reports-hub') }}">{{ __('admin.report.fish_history.report') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.report.fish_history.title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.report.fish_history.title') }}</h1>
        </div>
        <div class="ms-auto">
            <button type="button" onclick="printReport()" class="btn btn-outline-theme btn-equal">
                <i class="bi bi-printer me-1"></i> {{ __('admin.report.fish_history.print') }}
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @include('owner.components.stat-card', [
            'title' => __('admin.report.fish_history.total_history_count'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_total_records">0</span>'),
            'icon' => 'bi bi-clock-history',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-6 col-lg-4',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.fish_history.total_fish_count'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_total_fish">0</span>'),
            'icon' => 'bi bi-basket',
            'gradient' => 'linear-gradient(135deg, #6f42c1, #59339d)',
            'colClass' => 'col-md-6 col-lg-4',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.fish_history.total_remaining_weight'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_total_weight">0</span> ' . __('admin.units.kg')),
            'icon' => 'bi bi-box-seam',
            'gradient' => 'linear-gradient(135deg, #198754, #157347)',
            'colClass' => 'col-md-6 col-lg-4',
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
                    <label for="start_date">{{ __('admin.report.fish_history.from_date') }}</label>
                    <input type="date" id="start_date" class="form-control" value="{{ $monthStart }}" data-default="{{ $monthStart }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('admin.report.fish_history.to_date') }}</label>
                    <input type="date" id="end_date" class="form-control" value="{{ $monthEnd }}" data-default="{{ $monthEnd }}">
                </div>
                <div class="col-md-3">
                    <label for="fish_filter">{{ __('admin.report.fish_history.fish_type') }}</label>
                    <select id="fish_filter" class="form-control">
                        <option value="">{{ __('admin.report.fish_history.all') }}</option>
                        @foreach ($fish as $item)
                            <option value="{{ $item->id }}">{{ app()->getLocale() == 'ar' ? $item->name_ar : $item->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button id="filterBtn" class="btn btn-primary btn-sm">{{ __('admin.report.fish_history.filter') }}</button>
                    <button id="resetBtn" class="btn btn-secondary btn-sm">{{ __('admin.report.fish_history.reset') }}</button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ __('admin.report.fish_history.id') }}</th>
                            <th>{{ __('admin.report.fish_history.date') }}</th>
                            <th>{{ __('admin.report.fish_history.fish_name') }}</th>
                            <th>{{ __('admin.report.fish_history.operation') }}</th>
                            <th>{{ __('admin.report.fish_history.changed_weight') }}</th>
                            <th>{{ __('admin.report.fish_history.remaining_weight') }}</th>
                            <th>{{ __('admin.report.fish_history.user') }}</th>
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
            var url = '{{ route('admin.fish-history-report.print') }}?';
            if ($('#start_date').val()) url += 'start_date=' + $('#start_date').val() + '&';
            if ($('#end_date').val()) url += 'end_date=' + $('#end_date').val() + '&';
            if ($('#fish_filter').val()) url += 'fish_id=' + $('#fish_filter').val() + '&';
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
                    [10, 25, 50, 100, '{{ __('admin.report.fish_history.all_label') }}']
                ],
                pageLength: 10,
                ajax: {
                    url: "{{ route('admin.getFishHistoryDataReport') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.fish_id = $('#fish_filter').val();
                        return d;
                    },
                    dataSrc: function(json) {
                        if (json.fish_history_count !== undefined) $('#summary_total_records').text(json.fish_history_count);
                        if (json.total_fish_count !== undefined) $('#summary_total_fish').text(json.total_fish_count);
                        if (json.totalWeight !== undefined) $('#summary_total_weight').text(parseFloat(json.totalWeight).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'fish_name', name: 'fish_name' },
                    { data: 'operation_type', name: 'operation_type' },
                    { data: 'changed_weight', name: 'changed_weight', render: function(data) {
                        return parseFloat(data || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    } },
                    { data: 'remaining_weight', name: 'remaining_weight', render: function(data) {
                        return parseFloat(data || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    } },
                    { data: 'user_name', name: 'user_name' }
                ],
                buttons: [],
                responsive: false,
                scrollX: true
            });
            $('#filterBtn').on('click', function() { table.ajax.reload(); });
            $('#resetBtn').on('click', function() {
                $('#start_date').val($('#start_date').data('default'));
                $('#end_date').val($('#end_date').data('default'));
                $('#fish_filter').val('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
