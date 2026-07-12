@extends('admin.layouts.master')
@section('title')
    {{ __('admin.report.trip.title') }}
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
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.report.trip.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <button type="button" onclick="printReport()" class="btn btn-outline-theme btn-equal">
                <i class="bi bi-printer me-1"></i> {{ __('admin.report.trip.print') }}
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @include('owner.components.stat-card', [
            'title' => __('admin.report.trip.total_trips_count'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_total_trips">0</span>'),
            'icon' => 'bi bi-compass',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-4 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.trip.total_fish_count'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_total_fish">0</span>'),
            'icon' => 'bi bi-basket',
            'gradient' => 'linear-gradient(135deg, #6f42c1, #59339d)',
            'colClass' => 'col-md-4 col-sm-6',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.report.trip.total_weight'),
            'value' => new \Illuminate\Support\HtmlString('<span id="summary_total_weight">0</span> ' . __('admin.units.kg')),
            'icon' => 'bi bi-box-seam',
            'gradient' => 'linear-gradient(135deg, #fd7e14, #ea5d0a)',
            'colClass' => 'col-md-4 col-sm-6',
        ])
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="start_date">{{ __('admin.report.trip.from_date') }}</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('admin.report.trip.to_date') }}</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="status_filter">{{ __('admin.report.trip.status') }}</label>
                    <select id="status_filter" class="form-control">
                        <option value="">{{ __('admin.report.trip.all') }}</option>
                        @foreach (\App\Enums\TripStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="boat_filter">{{ __('admin.report.trip.owner') }}</label>
                    <select id="boat_filter" class="form-control">
                        <option value="">{{ __('admin.report.trip.all') }}</option>
                        @foreach ($boats as $boat)
                            <option value="{{ $boat->id }}">{{ app()->getLocale() == 'ar' ? $boat->name_ar : $boat->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button id="filterBtn" class="btn btn-primary btn-sm">{{ __('admin.report.trip.filter') }}</button>
                    <button id="resetBtn" class="btn btn-secondary btn-sm">{{ __('admin.report.trip.reset') }}</button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ __('admin.report.trip.id') }}</th>
                            <th>{{ __('admin.report.trip.trip_number') }}</th>
                            <th>{{ __('admin.report.trip.owner') }}</th>
                            <th>{{ __('admin.report.trip.captain') }}</th>
                            <th>{{ __('admin.report.trip.port') }}</th>
                            <th>{{ __('admin.report.trip.items_count') }}</th>
                            <th>{{ __('admin.report.trip.total_kg') }}</th>
                            <th>{{ __('admin.report.trip.status_th') }}</th>
                            <th>{{ __('admin.report.trip.start_end_date') }}</th>
                            <th>{{ __('admin.report.trip.days_count') }}</th>
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
            var url = '{{ route('admin.trip-report.print') }}?';
            if ($('#start_date').val()) url += 'start_date=' + $('#start_date').val() + '&';
            if ($('#end_date').val()) url += 'end_date=' + $('#end_date').val() + '&';
            if ($('#status_filter').val()) url += 'status=' + $('#status_filter').val() + '&';
            if ($('#boat_filter').val()) url += 'boat_id=' + $('#boat_filter').val() + '&';
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
                    url: "{{ route('admin.getTripDataReport') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status_filter').val();
                        d.boat_id = $('#boat_filter').val();
                        return d;
                    },
                    dataSrc: function(json) {
                        if (json.trip_count !== undefined) $('#summary_total_trips').text(json.trip_count);
                        if (json.total_fish_count !== undefined) $('#summary_total_fish').text(json.total_fish_count);
                        if (json.totalWeight !== undefined) $('#summary_total_weight').text(parseFloat(json.totalWeight).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'number', name: 'number' },
                    { data: 'owner', name: 'owner' },
                    { data: 'captain', name: 'captain' },
                    { data: 'port', name: 'port' },
                    { data: 'item_count', name: 'item_count' },
                    { data: 'item_weight', name: 'item_weight', render: function(data) {
                        return parseFloat(data || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    } },
                    { data: 'status', name: 'status' },
                    { data: 'date', name: 'date' },
                    { data: 'date_count', name: 'date_count' }
                ],
                responsive: true
            });
            $('#filterBtn').on('click', function() { table.ajax.reload(); });
            $('#resetBtn').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#status_filter').val('');
                $('#boat_filter').val('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
