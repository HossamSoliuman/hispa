@extends('owner.layouts.master')
@section('title')
    {{ __('owner.stock_report.title') }}
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

        /* {{ __('owner.generated.item_ed06b0') }} */
        .small-text th,
        .small-text td {
            font-size: 12px; /* {{ __('owner.generated.or') }} 13px {{ __('owner.generated.item_4cc9e8') }} */
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
                <li class="breadcrumb-item"><a href="#">{{ __('owner.stock_report.breadcrumb_reports') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.stock_report.title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.stock_report.title') }}</h1>
        </div>
        <div class="ms-auto">
            <button onclick="printReport()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>{{ __('owner.stock_report.print') }}
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        @include('owner.components.stat-card', [
            'title' => __('owner.stock_report.total_fish_types'),
            'value' => '<span id="totalFishCount" class="num">0</span>',
            'icon' => 'fas fa-fish',
            'gradient' => 'linear-gradient(135deg,#0d6efd,#0b5ed7)',
            'colClass' => 'col-md-6 col-lg-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.stock_report.total_weight'),
            // only the numeric span here; JS will inject the unit to avoid duplicates
            'value' => '<span id="totalWeight" class="num">0</span>',
            'icon' => 'bi bi-box-seam',
            'gradient' => 'linear-gradient(135deg,#f59e0b,#d97706)',
            'colClass' => 'col-md-6 col-lg-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.stock_report.total_records'),
            'value' => '<span id="totalRecords" class="num">0</span>',
            'icon' => 'fas fa-list',
            'gradient' => 'linear-gradient(135deg,#10b981,#059669)',
            'colClass' => 'col-md-6 col-lg-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.stock_report.total_difference'),
            // only the numeric span here; JS will inject the unit to avoid duplicates
            'value' => '<span id="totalDiff" class="num">0</span>',
            'icon' => 'fas fa-balance-scale',
            'gradient' => 'linear-gradient(135deg,#06b6d4,#0891b2)',
            'colClass' => 'col-md-6 col-lg-3',
        ])
    </div>

    <div class="tab-content py-4">


        <div class="tab-pane fade show active" id="allTab">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="start_date">{{ __('owner.stock_report.from_date') }}:</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('owner.stock_report.to_date') }}:</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="fish_type_filter">{{ __('owner.stock_report.fish_type') }}:</label>
                    <select id="fish_type_filter" class="form-control">
                        <option value="">{{ __('owner.stock_report.all_fish') }}</option>
                        @foreach($fish as $f)
                            <option value="{{$f->id}}">{{$f->name}}</option>

                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button id="filterBtn" class="btn btn-primary btn-sm" style="min-width: 60px;">{{ __('owner.stock_report.filter') }}</button>
                    <button id="resetBtn" class="btn btn-secondary btn-sm" style="min-width: 60px;">{{ __('owner.stock_report.reset') }}</button>
                </div>





            </div>

            <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.stock_report.fish_name') }}</th>
                        <th>{{ __('owner.stock_report.captain_weight') }}</th>
                        <th>{{ __('owner.stock_report.counter_weight') }}</th>
                        <th>{{ __('owner.stock_report.total_weight') }} ({{ __('owner.stock_report.kg') }})</th>
                        <th>{{ __('owner.stock_report.difference') }} ({{ __('owner.stock_report.kg') }})</th>
                        <th>{{ __('owner.stock_report.added_by') }}</th>
                        <th>{{ __('owner.stock_report.corrected_by') }}</th>
                        <th>{{ __('owner.stock_report.date') }}</th>
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
        function printReport() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            const fishType = $('#fish_type_filter').val();

            let url = '{{ route("owner.stock-report.print") }}?';
            if (startDate) url += `start_date=${startDate}&`;
            if (endDate) url += `end_date=${endDate}&`;
            if (fishType) url += `fish_type=${fishType}&`;

            window.open(url, '_blank');
        }

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
                        "<'col-md-4'l>" +                   // {{ __('owner.generated.page_length') }}"<'col-md-4'f>" +                   // {{ __('owner.dalal_invoices.filters.search') }}"<'col-md-4 text-md-end'B>" +      // {{ __('owner.generated.export_buttons') }}">" +
                    "<'row'<'col-sm-12'tr>>" +             // {{ __('owner.generated.table') }}"<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>", // معلومات الجدول والترقيم

                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                },
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, '{{ __('owner.stock_report.all') }}']
                ],
                pageLength: 10,
                ajax: {
                    url: "{{ route('owner.getStockDataReport') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status_filter').val();
                        d.fish_type = $('#fish_type_filter').val(); // أضف هذا السطر

                    },
                    dataSrc: function (json) {
                        if (json.total_fish_count !== undefined) {
                            $('#totalFishCount').text(json.total_fish_count);
                        }
                        if (json.totalWeight !== undefined) {
                            const w = parseFloat(json.totalWeight) || 0;
                            if (w >= 1000) {
                                const t = (w / 1000);
                                $('#totalWeight').html(t.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' <span class="unit">{{ __('owner.stock_report.ton') }}</span>');
                            } else {
                                $('#totalWeight').html(w.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' <span class="unit">{{ __('owner.stock_report.kg') }}</span>');
                            }
                        }
                        if (json.total_records !== undefined) {
                            $('#totalRecords').text(json.total_records);
                        }
                        if (json.total_difference !== undefined) {
                            const d = parseFloat(json.total_difference) || 0;
                            if (Math.abs(d) >= 1000) {
                                const dt = (d / 1000);
                                $('#totalDiff').html(dt.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' <span class="unit">{{ __('owner.stock_report.ton') }}</span>');
                            } else {
                                $('#totalDiff').html(d.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' <span class="unit">{{ __('owner.stock_report.kg') }}</span>');
                            }
                        }
                        return json.data;
                    }

                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'name', name: 'name' },
                    { data: 'weight_captain', name: 'total_weight' },
                    { data: 'weight_counter', name: 'total_weight' },
                    { data: 'total_weight', name: 'total_weight' },
                    { data: 'weight_difference', name: 'weight_difference' },

                    { data: 'added_by', name: 'added_by' },
                    { data: 'correct_by', name: 'correct_by' },
                    { data: 'date', name: 'date' },
                ],
                buttons: [
                    // {
                    //     extend: 'excelHtml5',
                    //     text: 'Excel',
                    //     className: 'btn btn-outline-success btn-sm me-1'
                    // },
                    // {
                    //     extend: 'print',
                    //     text: '{{ __('owner.generated.item_88c5d1') }}',
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
