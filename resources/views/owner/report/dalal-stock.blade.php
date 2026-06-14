@extends('owner.layouts.master')
@section('title')
    {{ __('owner.dalal_stock_report.title') }}
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
                <li class="breadcrumb-item"><a href="#">{{ __('owner.dalal_stock_report.breadcrumb_reports') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.dalal_stock_report.title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.dalal_stock_report.title') }}</h1>
        </div>
        <div class="ms-auto">
            <button id="printBtn" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>{{ __('owner.dalal_stock_report.print') }}
            </button>
        </div>
        {{-- Inline, lightweight print handler placed near the button so it is available even if later scripts fail to parse --}}
        <script>
            (function () {
                // Build a safe base URL server-side and expose a small print helper.
                var baseUrl = {!! json_encode(route('owner.dalal-stock-report.print')) !!};
                function doPrint() {
                    var startDate = (document.getElementById('start_date') || {}).value || '';
                    var endDate = (document.getElementById('end_date') || {}).value || '';
                    var dalalId = (document.getElementById('dalal_id_filter') || {}).value || '';

                    var params = [];
                    if (startDate) params.push('start_date=' + encodeURIComponent(startDate));
                    if (endDate) params.push('end_date=' + encodeURIComponent(endDate));
                    if (dalalId) params.push('dalal_id_filter=' + encodeURIComponent(dalalId));

                    var separator = baseUrl.indexOf('?') !== -1 ? '&' : '?';
                    var url = params.length ? baseUrl + separator + params.join('&') : baseUrl;
                    // open in a new tab/window (use _blank to avoid popup blockers in many environments)
                    window.open(url, '_blank');
                }

                // Expose globally for backwards compatibility
                window.printReport = doPrint;

                // Attach to the button if present
                document.addEventListener('DOMContentLoaded', function () {
                    var btn = document.getElementById('printBtn');
                    if (btn) btn.addEventListener('click', doPrint);
                });
            })();
        </script>
    </div>

    <!-- Summary Cards (use reusable component, 4-per-row) -->
    <div class="row g-3 mb-4">
        @include('owner.components.stat-card', [
            'title' => __('owner.dalal_stock_report.total_fish_types'),
            'value' => '<span id="totalFishCount">0</span>',
            'icon' => 'fas fa-fish',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-6 col-md-3 col-lg-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.dalal_stock_report.total_weight'),
            'value' => '<span id="totalWeight">0</span> ' . __('owner.dalal_stock_report.kg'),
            'icon' => 'bi bi-box-seam',
            'gradient' => 'linear-gradient(135deg, #ffc107, #ffb020)',
            'colClass' => 'col-6 col-md-3 col-lg-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.dalal_stock_report.total_dalals'),
            'value' => '<span id="totalDalalCount">0</span>',
            'icon' => 'bi bi-person-badge-fill',
            'gradient' => 'linear-gradient(135deg, #198754, #0f8a4a)',
            'colClass' => 'col-6 col-md-3 col-lg-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.dalal_stock_report.total_records'),
            'value' => '<span id="totalRecords">0</span>',
            'icon' => 'bi bi-list-check',
            'gradient' => 'linear-gradient(135deg, #6c757d, #495057)',
            'colClass' => 'col-6 col-md-3 col-lg-3'
        ])
    </div>

    <div class="tab-content py-4">

        <div class="tab-pane fade show active" id="allTab">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="start_date">{{ __('owner.dalal_stock_report.from_date') }}:</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('owner.dalal_stock_report.to_date') }}:</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="dalal_id_filter">{{ __('owner.dalal_stock_report.dalal') }}:</label>
                    <select id="dalal_id_filter" class="form-control">
                        <option value="">{{ __('owner.dalal_stock_report.all_dalals') }}</option>
                        @foreach($dalals as $d)
                            <option value="{{$d->id}}">{{$d->name}}</option>

                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button id="filterBtn" class="btn btn-primary btn-sm" style="min-width: 60px;">{{ __('owner.dalal_stock_report.filter') }}</button>
                    <button id="resetBtn" class="btn btn-secondary btn-sm" style="min-width: 60px;">{{ __('owner.dalal_stock_report.reset') }}</button>
                </div>



            </div>

            <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text"
                       style="width:100%">
                    <thead>
                    <tr>
                        <th>{{ __('owner.dalal.table.index') }}</th>
                        <th>{{ __('owner.dalal_stock_report.dalal_name') }}</th>
                        <th>{{ __('owner.dalal_stock_report.fish_name') }}</th>
                        <th>{{ __('owner.dalal_stock_report.total_weight') }} ({{ __('owner.units.kg') }})</th>
                        <th>{{ __('owner.dalal_stock_report.date') }}</th>
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
        // Expose a global printReport function and use JSON-encoded base URL to avoid Blade quoting issues
        window.printReport = function() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            const dalalId = $('#dalal_id_filter').val();

            // Use JSON encoding so the URL is a safe JS string
            const baseUrl = @json(route('owner.dalal-stock-report.print'));
            let separator = baseUrl.includes('?') ? '&' : '?';
            let params = [];
            if (startDate) params.push(`start_date=${encodeURIComponent(startDate)}`);
            if (endDate) params.push(`end_date=${encodeURIComponent(endDate)}`);
            if (dalalId) params.push(`dalal_id_filter=${encodeURIComponent(dalalId)}`);

            const url = params.length ? `${baseUrl}${separator}${params.join('&')}` : baseUrl;
            // open in a new tab/window
            window.open(url, '_blank');
        };

        $('#resetBtn').on('click', function () {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#dalal_id_filter').val('');
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
                    [10, 25, 50, 100, '{{ __('owner.generated.item_6d08f1') }}']
                ],
                pageLength: 10,
                ajax: {
                    url: "{{ route('owner.getDalalStockDataReport') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.dalal_id_filter = $('#dalal_id_filter').val(); // أضف هذا السطر

                    },
                    dataSrc: function (json) {
                        $('#totalFishCount').text(json.total_fish_count); // تحديث العنصر في HTML
                        $('#totalWeight').text(json.totalWeight); // تحديث العنصر في HTML
                        $('#totalDalalCount').text(json.total_dalal_count); // تحديث العنصر في HTML
                        $('#totalRecords').text(json.total_records ?? json.data.length || 0);
                        return json.data;
                    }

                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'dalal_name', name: 'dalal_name'},
                    {data: 'fish_name', name: 'fish_name'},
                    {data: 'total_weight', name: 'total_weight'},
                    {data: 'date', name: 'date'},
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
