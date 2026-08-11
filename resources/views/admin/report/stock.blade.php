@extends('admin.layouts.master')
@section('title')
    {{ __('admin.report.stock.title') }}
@endsection
@section('content')
    <div class="mb-4 d-flex align-items-start">
        <div>
            <h2 class="mb-1">{{ __('admin.report.stock.title') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.report.stock.subtitle') }}</p>
        </div>
        <div class="ms-auto">
            <button type="button" onclick="printReport()" class="btn btn-outline-theme">
                <i class="bi bi-printer me-1"></i> {{ __('admin.report.print') }}
            </button>
        </div>
    </div>

    @php
        $totalFishCountValue = new \Illuminate\Support\HtmlString('<span id="totalFishCount">0</span>');
        $totalWeightValue = new \Illuminate\Support\HtmlString('<span id="totalWeight">0</span>');
        $totalRecordsValue = new \Illuminate\Support\HtmlString('<span id="totalRecords">0</span>');
        $totalDifferenceValue = new \Illuminate\Support\HtmlString('<span id="totalDiff">0</span>');
        $monthStart = \Illuminate\Support\Carbon::now()->startOfMonth()->format('Y-m-d');
        $monthEnd = \Illuminate\Support\Carbon::now()->endOfMonth()->format('Y-m-d');
    @endphp

    <div class="row g-3 mb-4">
        <x-stat-card
            :title="__('admin.report.stock.total_fish_count')"
            :value="$totalFishCountValue"
            icon="bi bi-fish"
            gradient="linear-gradient(135deg, #0d6efd, #0b5ed7)"
            col-class="col-md-6 col-lg-3"
        />
        <x-stat-card
            :title="__('admin.report.stock.total_weight')"
            :value="$totalWeightValue"
            icon="bi bi-box-seam"
            gradient="linear-gradient(135deg, #fd7e14, #ea5d0a)"
            col-class="col-md-6 col-lg-3"
        />
        <x-stat-card
            :title="__('admin.report.stock.added_by')"
            :value="$totalRecordsValue"
            icon="bi bi-list-ul"
            gradient="linear-gradient(135deg, #198754, #157347)"
            col-class="col-md-6 col-lg-3"
        />
        <x-stat-card
            :title="__('admin.report.stock.difference')"
            :value="$totalDifferenceValue"
            icon="bi bi-arrow-left-right"
            gradient="linear-gradient(135deg, #0dcaf0, #0aa2c0)"
            col-class="col-md-6 col-lg-3"
        />
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">{{ __('admin.report.stock.from_date') }}</label>
                    <input type="date" id="start_date" class="form-control" value="{{ $monthStart }}" data-default="{{ $monthStart }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">{{ __('admin.report.stock.to_date') }}</label>
                    <input type="date" id="end_date" class="form-control" value="{{ $monthEnd }}" data-default="{{ $monthEnd }}">
                </div>
                <div class="col-md-3">
                    <label for="fish_type_filter" class="form-label">{{ __('admin.report.stock.fish_type') }}</label>
                    <select id="fish_type_filter" class="form-select">
                        <option value="">{{ __('admin.report.stock.all') }}</option>
                        @foreach($fish as $f)
                            <option value="{{ $f->id }}">{{ $f->name ?? $f->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button id="filterBtn" type="button" class="btn btn-primary">{{ __('admin.report.stock.filter') }}</button>
                    <button id="resetBtn" type="button" class="btn btn-outline-secondary">{{ __('admin.report.stock.reset') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ __('admin.table.id') }}</th>
                            <th>{{ __('admin.report.stock.fish_name') }}</th>
                            <th>{{ __('admin.report.stock.added_qty') }}</th>
                            <th>{{ __('admin.report.stock.corrected_qty') }}</th>
                            <th>{{ __('admin.report.stock.total_weight') }}</th>
                            <th>{{ __('admin.report.stock.difference') }}</th>
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
    <script type="text/javascript">
        function printReport() {
            var url = '{{ route('admin.stock-report.print') }}?';
            if ($('#start_date').val()) url += 'start_date=' + $('#start_date').val() + '&';
            if ($('#end_date').val()) url += 'end_date=' + $('#end_date').val() + '&';
            if ($('#fish_type_filter').val()) url += 'fish_type=' + $('#fish_type_filter').val() + '&';
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
                    [10, 25, 50, 100, '{{ __('admin.report.stock.all_label') }}']
                ],
                pageLength: 10,
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
                buttons: [],
                responsive: false,
                scrollX: true
            });
            $('#filterBtn').on('click', function() { table.ajax.reload(); });
            $('#resetBtn').on('click', function() {
                $('#start_date').val($('#start_date').data('default'));
                $('#end_date').val($('#end_date').data('default'));
                $('#fish_type_filter').val('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
