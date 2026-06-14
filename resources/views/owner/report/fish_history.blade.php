@extends('owner.layouts.master')
@section('title')
    Item Movement Reports
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

        /* Smaller font size for compact table */
        .small-text th,
        .small-text td {
            font-size: 12px; /* or 13px, adjust as needed */
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
                <li class="breadcrumb-item active"> {{ __('owner.fish_history_report.title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.fish_history_report.page_header') }}</h1>
        </div>
        <div class="ms-auto">
            <button onclick="printReport()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>{{ __('owner.fish_history_report.print') }}
            </button>
        </div>

    </div>

    <div class="tab-content py-4">
        <div class="row g-3"> <!-- g-3 {{ __('owner.generated.item_26ceee') }} -->
                    <!-- Fish count stat cards -->
            <div class="tab-content py-4">
                <div class="row g-3 mb-4">
                    @include('owner.components.stat-card', [
                        'title' => __('owner.stock_report.total_fish_types'),
                        'value' => '<span id="totalFishTypes" class="num">0</span>',
                        'icon' => 'fas fa-fish',
                        'gradient' => 'linear-gradient(135deg,#0d6efd,#0b5ed7)',
                        'colClass' => 'col-6 col-md-3 col-lg-3'
                    ])

                    @include('owner.components.stat-card', [
                        'title' => __('owner.stock_report.total_weight'),
                        'value' => '<span id="totalWeight" class="num">0</span> <span class="unit">{{ __("owner.stock_report.kg") }}</span>',
                        'icon' => 'bi bi-weight',
                        'gradient' => 'linear-gradient(135deg,#10b981,#059669)',
                        'colClass' => 'col-6 col-md-3 col-lg-3'
                    ])

                    @include('owner.components.stat-card', [
                        'title' => __('owner.stock_report.total_records'),
                        'value' => '<span id="totalRecords" class="num">0</span>',
                        'icon' => 'fas fa-list',
                        'gradient' => 'linear-gradient(135deg,#f59e0b,#d97706)',
                        'colClass' => 'col-6 col-md-3 col-lg-3'
                    ])

                    @include('owner.components.stat-card', [
                        'title' => __('owner.reports.total_catch'),
                        'value' => '<span id="totalCatch" class="num">0</span>',
                        'icon' => 'fas fa-water',
                        'gradient' => 'linear-gradient(135deg,#6c757d,#495057)',
                        'colClass' => 'col-6 col-md-3 col-lg-3'
                    ])
                </div>
        <br>
        <div class="tab-pane fade show active" id="allTab">
                <div class="row mb-3">
                <div class="col-md-3">
                    <label for="start_date">{{ __('owner.fish_history_report.from_date') }}:</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('owner.fish_history_report.to_date') }}:</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="fish_type_filter">{{ __('owner.fish_history_report.fish_type') }}:</label>
                    <select id="fish_type_filter" class="form-control">
                        <option value="">{{ __('owner.fish_history_report.all') }}</option>
                        @foreach($fish as $f)
                            <option value="{{$f->id}}">{{$f->name}}</option>

                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button id="filterBtn" class="btn btn-primary btn-sm" style="min-width: 60px;">{{ __('owner.fish_history_report.filter_button') }}</button>
                    <button id="resetBtn" class="btn btn-secondary btn-sm" style="min-width: 60px;">{{ __('owner.fish_history_report.reset_button') }}</button>
                </div>



            </div>
            <br>
            <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text"
                       style="width:100%">
                    <thead>
                    <tr>
                        <th>{{ __('owner.fish_history_report.table.index') }}</th>
                        <th>{{ __('owner.fish_history_report.table.date') }}</th>
                        <th>{{ __('owner.fish_history_report.table.item') }}</th>
                        <th>{{ __('owner.fish_history_report.table.operation') }}</th>
                        <th>{{ __('owner.fish_history_report.table.weight') }}</th>
                        <th>{{ __('owner.fish_history_report.table.remaining_balance') }}</th>
                        <th>{{ __('owner.fish_history_report.table.user') }}</th>
                        <th>{{ __('owner.fish_history_report.table.notes') }}</th>
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

    <!-- Buttons Bootstrap ({{ __('owner.generated.item_df378a') }} Bootstrap style) -->



    <script type="text/javascript">
        // Open the print view with current filters
        function printReport() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            const fishId = $('#fish_type_filter').val();

            let url = '{{ route('owner.fish-history-report.print') }}?';
            if (startDate) url += `start_date=${startDate}&`;
            if (endDate) url += `end_date=${endDate}&`;
            if (fishId) url += `fish_id=${fishId}&`;

            window.open(url, '_blank');
        }

        // Reset filters and reload table
        $('#resetBtn').on('click', function () {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#fish_type_filter').val('');
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
                    [10, 25, 50, 100, "{{ __('owner.fish_history_report.all') }}"]
                ],
                pageLength: 10,
                ajax: {
                    url: "{{ route('owner.getFishStockHistoryReport') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.fish_id = $('#fish_type_filter').val(); // pass selected fish id

                    },

                    dataSrc: function (json) {
                        // historic count -> show in totalFishTypes card (fallbacks covered)
                        $('#totalFishTypes').text(json.fish_history_count ?? json.total_fish_types ?? json.total_fish_count ?? 0);
                        // total catch / amount
                        $('#totalCatch').text(json.total_catch ?? json.totalCatch ?? 0);

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

                        // Backwards compat: if other scripts expect these IDs, set them when present
                        if ($('#totalFishCount').length) {
                            $('#totalFishCount').text(json.total_fish_count ?? 0);
                        }

                        return json.data;
                    }

                },
                columns: [


                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    { data: 'created_at', name: 'created_at' },
                    { data: 'fish_name' },
                    { data: 'operation_type', orderable: false, searchable: false },
                    { data: 'changed_weight', orderable: false, searchable: false },
                    { data: 'remaining_weight', orderable: false, searchable: false },
                    { data: 'user_name' },
                    { data: 'notes', name: 'notes' },

                ],
                buttons: [
                    // Optional export buttons (enable if needed)
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
