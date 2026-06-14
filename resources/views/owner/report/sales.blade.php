@extends('owner.layouts.master')
@section('title')
    {{ __('owner.sales_report.title') }}
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
                <li class="breadcrumb-item"><a href="#">{{ __('owner.sales_report.breadcrumb_reports') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.sales_report.title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.sales_report.title') }}</h1>
        </div>
        <div class="ms-auto">
            <button onclick="printReport()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>{{ __('owner.sales_report.print') }}
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        @include('owner.components.stat-card', [
            'title' => __('owner.sales_report.total_sales'),
            'value' => '<span id="summary_total_sales" class="num">0</span>',
            'icon' => 'fas fa-file-invoice',
            'gradient' => 'linear-gradient(135deg,#0d6efd,#0b5ed7)',
            'colClass' => 'col-md-6 col-lg-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.sales_report.total_revenue'),
            'value' => '<span id="summary_total_revenue" class="num">0</span> <span class="unit">'.app('view')->make('components.riyal-icon', ['size' => 'sm'])->render().'</span>',
            'icon' => 'fas fa-coins',
            'gradient' => 'linear-gradient(135deg,#10b981,#059669)',
            'colClass' => 'col-md-6 col-lg-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.sales_report.total_weight'),
            'value' => '<span id="summary_total_weight" class="num">0</span> <span class="unit">'.__('owner.sales_report.kg').'</span>',
            'icon' => 'fas fa-weight',
            'gradient' => 'linear-gradient(135deg,#f59e0b,#d97706)',
            'colClass' => 'col-md-6 col-lg-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.sales_report.net_owner_amount'),
            'value' => '<span id="summary_net_owner" class="num">0</span> <span class="unit">'.app('view')->make('components.riyal-icon', ['size' => 'sm'])->render().'</span>',
            'icon' => 'fas fa-hand-holding-usd',
            'gradient' => 'linear-gradient(135deg,#06b6d4,#0891b2)',
            'colClass' => 'col-md-6 col-lg-3',
        ])
    </div>

    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="start_date">{{ __('owner.sales_report.from_date') }}:</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('owner.sales_report.to_date') }}:</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="status_filter">{{ __('owner.sales_report.status') }}:</label>
                    <select id="status_filter" class="form-control">
                        <option value="">{{ __('owner.sales_report.all_status') }}</option>
                        <option value="1">{{ __('owner.sales_report.status_ongoing') }}</option>
                        <option value="2">{{ __('owner.sales_report.status_completed') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button id="filterBtn" class="btn btn-primary btn-sm" style="min-width: 60px;">{{ __('owner.sales_report.filter') }}</button>
                    <button id="resetBtn" class="btn btn-secondary btn-sm" style="min-width: 60px;">{{ __('owner.sales_report.reset') }}</button>
                </div>

            </div>
            <br>
            <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                    <tr>
                        <th>{{ __('owner.sales_report.index') }}</th>
                        <th>{{ __('owner.sales_report.invoice_number') }}</th>
                        <th>{{ __('owner.sales_report.status') }}</th>
                        {{-- <th>{{ __('owner.sales_report.seller') }}</th> --}}
                        <th>{{ __('owner.sales_report.customer') }}</th>
                        <th>{{ __('owner.sales_report.payment_method') }}</th>
                        <th>{{ __('owner.sales_report.total_weight') }} ({{ __('owner.sales_report.kg') }})</th>
                        <th>{{ __('owner.sales_report.commission') }}</th>
                        <th>{{ __('owner.sales_report.labor') }}</th>
                        <th>{{ __('owner.sales_report.total_price') }}</th>
                        <th>{{ __('owner.sales_report.net_owner') }}</th>
                        <th>{{ __('owner.sales_report.remaining_total') }}</th>
                        <th>{{ __('owner.sales_report.date') }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="card-arrow">
        <div class="card-arrow-top-left"></div>
        <div class="card-arrow-top-right"></div>
        <div class="card-arrow-bottom-left"></div>
        <div class="card-arrow-bottom-right"></div>
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

    <!-- Buttons Bootstrap ({{ __('owner.generated.item_df378a') }} Bootstrap style) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>



    <script type="text/javascript">
        function printReport() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            const status = $('#status_filter').val();

            let url = '{{ route("owner.sales-report.print") }}?';
            if (startDate) url += `start_date=${startDate}&`;
            if (endDate) url += `end_date=${endDate}&`;
            if (status) url += `status=${status}&`;

            window.open(url, '_blank');
        }

        $('#resetBtn').on('click', function () {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#status_filter').val('');
            $('#datatableDefault').DataTable().ajax.reload();
        });

        $(function () {
            // Check if the DataTable is already initialized and destroy it
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }


            // Initialize the DataTable
            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,
                dom:
                    "<'row mb-3' " +
                    "<'col-md-4'l>" +                   // {{ __('owner.generated.page_length') }}"<'col-md-4'f>" +                   // {{ __('owner.dalal_invoices.filters.search') }}"<'col-md-4 text-md-end'B>" +      // {{ __('owner.generated.export_buttons') }}">" +
                    "<'row'<'col-sm-12'tr>>" +             // {{ __('owner.generated.table') }}"<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>", // معلومات الجدول والترقيم

                language: {
                    url: "{{asset('dashboard/assets/js/ar.json')}}?v={{ time() }}"

                },
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, '{{ __('owner.generated.item_6d08f1') }}']
                ],
                pageLength: 10, // الافتراضي
                ajax: {
                    url: "{{ route('owner.getSalesDataReport') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status_filter').val();
                        return d;
                    },
                    dataSrc: function (json) {
                        // Update summary cards if extra data is provided
                        if (json.total_sales !== undefined) {
                            $('#summary_total_sales').text(json.total_sales);
                        }
                        if (json.total_revenue !== undefined) {
                            $('#summary_total_revenue').text(parseFloat(json.total_revenue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        }
                        if (json.total_weight !== undefined) {
                            $('#summary_total_weight').text(parseFloat(json.total_weight).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        }
                        if (json.net_owner_amount !== undefined) {
                            $('#summary_net_owner').text(parseFloat(json.net_owner_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        }
                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'number', name: 'number' },
                    { data: 'status', name: 'status' },
                    // { data: 'seller', name: 'seller' },
                    { data: 'customer', name: 'customer' },
                    { data: 'payment_method', name: 'payment_method' },
                    { data: 'total_weight', name: 'total_weight' },
                    { data: 'commission_rate', name: 'commission_rate' },
                    { data: 'labor_rate', name: 'labor_rate' },
                    { data: 'total_price', name: 'total_price' },
                    { data: 'net_owner_amount', name: 'net_owner_amount' },
                    { data: 'remaining_total', name: 'remaining_total' },
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
