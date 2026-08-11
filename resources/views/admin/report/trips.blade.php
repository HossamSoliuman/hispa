@extends('admin.layouts.master')
@section('title')
    {{ __('admin.report.trip.title') }}
@endsection
@section('content')
    <div class="mb-4 d-flex align-items-start">
        <div>
            <h2 class="mb-1">{{ __('admin.report.trip.title') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.report.trip.subtitle') }}</p>
        </div>
        <div class="ms-auto">
            <button type="button" onclick="printReport()" class="btn btn-outline-theme">
                <i class="bi bi-printer me-1"></i> {{ __('admin.report.print') }}
            </button>
        </div>
    </div>

    @php
        $totalTripsValue = new \Illuminate\Support\HtmlString('<span id="summary_total_trips">0</span>');
        $totalWeightValue = new \Illuminate\Support\HtmlString('<span id="summary_total_weight">0</span>');
        $monthStart = \Illuminate\Support\Carbon::now()->startOfMonth()->format('Y-m-d');
        $monthEnd = \Illuminate\Support\Carbon::now()->endOfMonth()->format('Y-m-d');
    @endphp

    <div class="row g-3 mb-4">
        <x-stat-card
            :title="__('admin.report.trip.total_trips_count')"
            :value="$totalTripsValue"
            icon="bi bi-compass"
            gradient="linear-gradient(135deg, #0d6efd, #0b5ed7)"
            col-class="col-md-6"
        />
        <x-stat-card
            :title="__('admin.report.trip.total_weight')"
            :value="$totalWeightValue"
            icon="bi bi-box-seam"
            gradient="linear-gradient(135deg, #fd7e14, #ea5d0a)"
            col-class="col-md-6"
        />
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">{{ __('admin.report.trip.from_date') }}</label>
                    <input type="date" id="start_date" class="form-control" value="{{ $monthStart }}" data-default="{{ $monthStart }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">{{ __('admin.report.trip.to_date') }}</label>
                    <input type="date" id="end_date" class="form-control" value="{{ $monthEnd }}" data-default="{{ $monthEnd }}">
                </div>
                <div class="col-md-2">
                    <label for="status_filter" class="form-label">{{ __('admin.report.trip.status') }}</label>
                    <select id="status_filter" class="form-select">
                        <option value="">{{ __('admin.report.trip.all') }}</option>
                        @foreach (\App\Enums\TripStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="boat_filter" class="form-label">{{ __('admin.report.trip.boat') }}</label>
                    <select id="boat_filter" class="form-select">
                        <option value="">{{ __('admin.report.trip.all') }}</option>
                        @foreach ($boats as $boat)
                            <option value="{{ $boat->id }}">{{ app()->getLocale() == 'ar' ? $boat->name_ar : $boat->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button id="filterBtn" type="button" class="btn btn-primary">{{ __('admin.report.trip.filter') }}</button>
                    <button id="resetBtn" type="button" class="btn btn-outline-secondary">{{ __('admin.report.trip.reset') }}</button>
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
                            <th>{{ __('admin.report.trip.id') }}</th>
                            <th>{{ __('admin.report.trip.trip_number') }}</th>
                            <th>{{ __('admin.report.trip.owner') }}</th>
                            <th>{{ __('admin.report.trip.captain') }}</th>
                            <th>{{ __('admin.report.trip.port') }}</th>
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
                    [10, 25, 50, 100, '{{ __('admin.report.trip.all_label') }}']
                ],
                pageLength: 10,
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
                        if (json.totalWeight !== undefined) $('#summary_total_weight').text(json.totalWeight);
                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'number', name: 'number' },
                    { data: 'owner', name: 'owner' },
                    { data: 'captain', name: 'captain' },
                    { data: 'port', name: 'port' },
                    { data: 'item_weight', name: 'item_weight' },
                    { data: 'status', name: 'status' },
                    { data: 'date', name: 'date' },
                    { data: 'date_count', name: 'date_count' }
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
                $('#boat_filter').val('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
