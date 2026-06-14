@extends('owner.layouts.master')
@section('title')
    {{ __('owner.reports.trip_report') }}
@endsection
@section('css')

    <link href="{{asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}"
          rel="stylesheet">
    <link href="{{asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css')}}"
          rel="stylesheet">
    <link href="{{asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}"
          rel="stylesheet">
    <link href="{{asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css')}}" rel="stylesheet">
    <style>
        #datatableDefault th,
        #datatableDefault td {
            text-align: center !important;
            vertical-align: middle;
        }

        /* Smaller font for compact tables */
        .small-text th,
        .small-text td {
            font-size: 12px;
            text-align: center !important;
            vertical-align: middle;
            font-weight: bold;
        }

        label.error {
            color: red;
            font-weight: bold;
            margin-top: 5px;
            display: block;
        }
    </style>
@endsection
@section('content')

    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">{{ __('owner.menu.reports') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.reports.trip_report') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.reports.trip_report') }}</h1>
        </div>
        <div class="ms-auto">
            <button onclick="printReport()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i> {{ __('owner.actions.print') }}
            </button>
        </div>

    </div>

    <div class="tab-content py-4">
        <div class="row g-3 mb-4">
            @include('owner.components.stat-card', [
                'title' => __('owner.reports.total_trips'),
                'value' => '<span id="totalTripCount" class="num">0</span>',
                'icon' => 'fas fa-ship',
                'gradient' => 'linear-gradient(135deg,#0d6efd,#0b5ed7)',
                'colClass' => 'col-6 col-md-3 col-lg-3'
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.reports.total_catch'),
                'value' => '<span id="totalFishCount" class="num">0</span>',
                'icon' => 'fas fa-fish',
                'gradient' => 'linear-gradient(135deg,#10b981,#059669)',
                'colClass' => 'col-6 col-md-3 col-lg-3'
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.stock_report.total_weight'),
                'value' => '<span id="totalWeight" class="num">0</span> <span class="unit">{{ __("owner.stock_report.kg") }}</span>',
                'icon' => 'bi bi-box-seam',
                'gradient' => 'linear-gradient(135deg,#f59e0b,#d97706)',
                'colClass' => 'col-6 col-md-3 col-lg-3'
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.stock_report.total_records'),
                'value' => '<span id="totalRecords" class="num">0</span>',
                'icon' => 'fas fa-list',
                'gradient' => 'linear-gradient(135deg,#6c757d,#495057)',
                'colClass' => 'col-6 col-md-3 col-lg-3'
            ])
        </div>


        <br>
        <div class="tab-pane fade show active" id="allTab">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="start_date">{{ __('owner.reports.from_date') }}</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('owner.reports.to_date') }}</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="status_filter">{{ __('owner.trips.status') }}</label>
                    <select id="status_filter" class="form-control">
                        <option value="">{{ __('owner.stock_report.all') }}</option>
                        <option value="1">{{ __('owner.statusLabels.1') }}</option>
                        <option value="2">{{ __('owner.statusLabels.2') }}</option>
                        <option value="3">{{ __('owner.statusLabels.3') }}</option>
                        <option value="4">{{ __('owner.statusLabels.4') }}</option>
                        <option value="5">{{ __('owner.statusLabels.5') }}</option>
                        <option value="6">{{ __('owner.statusLabels.6') }}</option>
                        <option value="7">{{ __('owner.statusLabels.7') }}</option>
                        <option value="8">{{ __('owner.statusLabels.8') }}</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button id="filterBtn" class="btn btn-primary btn-sm" style="min-width: 60px;">{{ __('owner.sales_report.filter') }}</button>
                    <button id="resetBtn" class="btn btn-secondary btn-sm" style="min-width: 60px;">{{ __('owner.sales_report.reset') }}</button>
                </div>

            </div>
            <br>
            <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text"
                       style="width:100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.trips.show.name') }}</th>
                        <th>{{ __('owner.trips.trip_number') }}</th>
                        <th>{{ __('owner.trips.show.license_number') }}</th>
                        <th>{{ __('owner.trips.status') }}</th>
                        <th>{{ __('owner.trips.show.owner') }}</th>
                        <th>{{ __('owner.trips.show.captain') }}</th>
                        <th>{{ __('owner.trips.show.counter') }}</th>
                        <th>{{ __('owner.trips.show.total_items') }}</th>
                        <th>{{ __('owner.trips.show.total_weight') }}</th>
                        <th>{{ __('owner.trips.show.port') }}</th>
                        <th>{{ __('owner.trips.show.date_depart_return') }}</th>
                        <th>{{ __('owner.trips.show.time_depart_return') }}</th>
                        <th>{{ __('owner.trips.days_count') }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
@section('script')

    <script src="{{asset('dashboard/assets/plugins/@highlightjs/cdn-assets/highlight.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/js/demo/highlightjs.demo.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
    <script
        src="{{asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
    <script
        src="{{asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script
        src="{{asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/js/demo/table-plugins.demo.js')}}"></script>
    <script src="{{asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js')}}"></script>
    <script src="{{asset('dashboard/assets/js/jquery.validate.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- Buttons Bootstrap (optional if using Bootstrap styles) -->


    <script type="text/javascript">
        function printReport() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            const status = $('#status_filter').val();

            const params = new URLSearchParams();
            if (startDate) params.append('from_date', startDate);
            if (endDate) params.append('to_date', endDate);
            if (status) params.append('status', status);

            const query = params.toString();
            const url = query
                ? `{{ route('owner.reports.print.all_trips') }}?${query}`
                : `{{ route('owner.reports.print.all_trips') }}`;

            window.open(url, '_blank');
        }

        $('#resetBtn').on('click', function () {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#status_filter').val('');
            $('#datatableDefault').DataTable().ajax.reload();
        });

        $(function () {
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }

            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,
                dom:
                    "<'row mb-3' " +
                    "<'col-md-4'l>" +                   // length selector
                    "<'col-md-4'f>" +                   // search box
                    "<'col-md-4 text-md-end'B>" +      // export buttons aligned right
                    ">" +
                    "<'row'<'col-sm-12'tr>>" +             // table
                    "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>", // table info & pagination

                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                },
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, '{{ __('owner.generated.item_6d08f1') }}']
                ],
                pageLength: 10,
                ajax: {
                    url: "{{ route('owner.getTripDataReport') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status_filter').val(); // add status filter

                    },

                    dataSrc: function (json) {
                        $('#totalTripCount').text(json.trip_count || 0);
                        $('#totalFishCount').text(json.total_fish_count || 0);

                        // weight formatting: show tons when >= 1000 kg
                        function formatWeightRaw(val) {
                            var kgLabel = "{{ __('owner.stock_report.kg') }}";
                            var tonLabel = "{{ __('owner.stock_report.ton') }}";
                            var n = parseFloat(val) || 0;
                            if (n >= 1000) {
                                return {value: (n / 1000).toFixed(2), unit: tonLabel};
                            }
                            return {value: n.toFixed(2), unit: kgLabel};
                        }

                        var w = formatWeightRaw(json.totalWeight);
                        $('#totalWeight').text(w.value);
                        $('#totalWeight').next('.unit').text(w.unit);

                        // total records (fallback to data length)
                        $('#totalRecords').text(json.total_records ?? (json.data ? json.data.length : 0));
                        return json.data;
                    }

                },
                columns: [


                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'number', name: 'number'},
                    {data: 'license_number', name: 'license_number'},
                    {data: 'status', name: 'status'},
                    {data: 'owner', name: 'owner'},
                    {data: 'captain', name: 'captain'},
                    {data: 'counter', name: 'counter'},
                    {data: 'item_count', name: 'item_count'},
                    {data: 'item_weight', name: 'item_weight'},
                    {data: 'port', name: 'port'},
                    {data: 'date', name: 'date'},
                    {data: 'time', name: 'time'},
                    {data: 'date_count', name: 'date_count'},

                ],
                buttons: [
                    // {
                    //     extend: 'excelHtml5',
                    //     text: 'Excel',
                    //     className: 'btn btn-outline-success btn-sm me-1'
                    // },
                    // {
                    //     extend: 'print',
                    //     text: 'Print',
                    //     className: 'btn btn-outline-primary btn-sm'
                    // }
                ],
                responsive: false, scrollX: true
            });

            $('#filterBtn').on('click', function () {
                table.ajax.reload();
            });
        });

    </script>

@endsection
