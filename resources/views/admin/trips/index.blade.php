@extends('admin.layouts.master')
@section('title')
    {{ __('admin.menu.trips') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet">
    <style>
        #datatableDefault th, #datatableDefault td {
            text-align: center !important;
            vertical-align: middle;
        }
        .small-text th, .small-text td {
            font-size: 12px;
            text-align: center !important;
            vertical-align: middle;
            font-weight: bold;
        }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.menu.trips') }}</h2>
        </div>
    
    </div>

    <div class="row">
        @include('owner.components.stat-card', [
            'title' => __('admin.trips.total'),
            'value' => new \Illuminate\Support\HtmlString('<div id="trip_count">0</div>'),
            'icon' => 'fas fa-ship',
            'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.trips.completed'),
            'value' => new \Illuminate\Support\HtmlString('<div id="trip_completed">0</div>'),
            'icon' => 'bi bi-check-circle',
            'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.trips.active'),
            'value' => new \Illuminate\Support\HtmlString('<div id="trip_active">0</div>'),
            'icon' => 'bi bi-arrow-repeat',
            'gradient' => 'linear-gradient(135deg, #f39c12, #f1c40f)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.trips.cancelled'),
            'value' => new \Illuminate\Support\HtmlString('<div id="trip_cancelled">0</div>'),
            'icon' => 'bi bi-x-circle',
            'gradient' => 'linear-gradient(135deg, #c0392b, #e74c3c)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
    </div>

    @php
        $tripStatusOptions = [['value' => '', 'label' => __('admin.filters.all')]];
        $tripStatuses = __('admin.trip_statuses');
        if (is_array($tripStatuses)) {
            foreach ($tripStatuses as $id => $label) {
                $tripStatusOptions[] = ['value' => (string) $id, 'label' => is_string($label) ? $label : (string) $id];
            }
        }
    @endphp
    <x-admin.components.datatable-filters
        formId="tripsFilters"
        :filters="[
            ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.trips.number') . ' / ' . __('admin.trips.owner'), 'value' => request('search')],
            ['type' => 'select-static', 'id' => 'status', 'name' => 'status', 'label' => __('admin.trips.status'), 'options' => $tripStatusOptions, 'selected' => request('status')],
            ['type' => 'daterange', 'id' => 'date_range', 'nameFrom' => 'from_date', 'nameTo' => 'to_date', 'label' => __('admin.trips.date'), 'valueFrom' => request('from_date'), 'valueTo' => request('to_date')],
        ]"
        :showArrow="false"
    />

    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <div id="datatable" class="mb-5">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ __('admin.table.id') }}</th>
                            <th>{{ __('admin.trips.number') }}</th>
                            <th>{{ __('admin.trips.owner') }}</th>
                            <th>{{ __('admin.trips.captain') }}</th>
                            <th>{{ __('admin.trips.counter') }}</th>
                            <th>{{ __('admin.trips.port') }}</th>
                            <th>{{ __('admin.trips.date') }}</th>
                            <th>{{ __('admin.trips.status') }}</th>
                            <th>{{ __('admin.trips.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        $(function() {
            let appLocale = '{{ app()->getLocale() }}';
            let languageOptions = {};
            if (appLocale === 'ar') {
                languageOptions = { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" };
            }
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }

            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,
                language: languageOptions,
                ajax: {
                    url: "{{ route('admin.getTripData') }}",
                    data: function(d) {
                        var form = document.getElementById('tripsFilters');
                        if (form) {
                            var fd = new FormData(form);
                            fd.forEach(function(value, key) { d[key] = value; });
                        }
                    },
                    dataSrc: function(json) {
                        $('#trip_count').text(json.trip_count || 0);
                        $('#trip_completed').text(json.trip_completed || 0);
                        $('#trip_active').text(json.trip_active || 0);
                        $('#trip_cancelled').text(json.trip_cancelled || 0);
                        return json.data;
                    }
                },
                columns: [{
                    data: 'DT_RowIndex', name: 'DT_RowIndex'
                }, {
                    data: 'number', name: 'number'
                }, {
                    data: 'owner', name: 'owner'
                }, {
                    data: 'captain', name: 'captain'
                }, {
                    data: 'counter', name: 'counter'
                }, {
                    data: 'port', name: 'port'
                }, {
                    data: 'date', name: 'date'
                }, {
                    data: 'status', name: 'status'
                }, {
                    data: 'action', name: 'action', orderable: false, searchable: false
                }],
                responsive: true,
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            });

            $('#tripsFilters').on('change keyup', 'select, input', function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
