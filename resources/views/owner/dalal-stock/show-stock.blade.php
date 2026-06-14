@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.item_a17aa1') }}
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

        .card-arrow > div {
            width: 15px;
            height: 15px;
            position: absolute;
            background: #007bff; /* {{ __('owner.generated.item_693202') }} */
            z-index: 1;
        }
        .card-arrow-top-left {
            top: 0;
            left: 0;
            clip-path: polygon(0 0, 100% 0, 0 100%);
        }
        .card-arrow-top-right {
            top: 0;
            right: 0;
            clip-path: polygon(100% 0, 100% 100%, 0 0);
        }
        .card-arrow-bottom-left {
            bottom: 0;
            left: 0;
            clip-path: polygon(0 0, 100% 100%, 0 100%);
        }
        .card-arrow-bottom-right {
            bottom: 0;
            right: 0;
            clip-path: polygon(100% 0, 100% 100%, 0 100%);
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
                <li class="breadcrumb-item"><a href="#">{{ __('owner.dalal_stock_boat.page_title') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.dalal_stock_boat.page_title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.dalal_stock_boat.manage_title') }}</h1>
        </div>


    </div>




    <!-- {{ __('owner.generated.item_84b1e0') }} -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-primary">
                <div class="card-header fw-bold bg-primary text-white text-center">
                    {{ __('owner.dalal_stock_boat.statistics_title') }}</div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6 col-md-4 col-lg-2">
                            <strong>{{ __('owner.sales.trip') }}</strong>
                            <span class="badge bg-info fs-6 mt-1" id="boatname">0</span>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <strong>{{ __('owner.dalal_stock_boat.cards.total_dalals') }}</strong>
                            <span class="badge bg-info fs-6 mt-1" id="totalDalals">0</span>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <strong>{{ __('owner.reports.total_revenue') }}</strong>
                            <span class="badge bg-success fs-6 mt-1" id="totalRevenue">{{ __('owner.generated.amount_0_sar') }}</span>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <strong>{{ __('owner.dalal_stock_boat.cards.total_weight') }}</strong>
                            <span class="badge bg-success fs-6 mt-1" id="totalWeight">{{ __('owner.generated.weight_0_kg') }}</span>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <strong>{{ __('owner.dalal_stock_boat.cards.total_remaining') }}</strong>
                            <span class="badge bg-warning fs-6 mt-1" id="totalRemaining">{{ __('owner.generated.weight_0_kg') }}</span>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <strong>{{ __('owner.dalal_stock_boat.cards.total_sold') }}</strong>
                            <span class="badge bg-danger fs-6 mt-1" id="totalSold">{{ __('owner.generated.weight_0_kg') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>






    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <!-- BEGIN #datatable -->
            <!-- BEGIN #datatable -->
            <div id="datatable" class="mb-5">
                {{--                    <div class="card">--}}
                {{--                        <div class="card-body">--}}
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.dalal_invoices.table.dalal_name') }}</th>
                        <th>{{ __('owner.generated.grand_total') }}({{ __('owner.sales_report.kg') }})</th>
                        <th>{{ __('owner.generated.remaining_total') }}({{ __('owner.sales_report.kg') }})</th>
                        <th>{{ __('owner.generated.sold_total') }}({{ __('owner.sales_report.kg') }})</th>
                        <th>{{ __('owner.reports.total_revenue') }}({{ __('owner.units.sar') }})</th>
                        <th>{{ __('owner.dalal_stock_boat.table.details') }}</th>


                    </tr>
                    </thead>
                    <tbody>


                    </tbody>
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
    <!-- END #datatable -->


    {{--            </div>--}}
    {{--        </div>--}}
    <!-- END #datatable -->

    <div class="card-arrow">
        <div class="card-arrow-top-left"></div>
        <div class="card-arrow-top-right"></div>
        <div class="card-arrow-bottom-left"></div>
        <div class="card-arrow-bottom-right"></div>
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
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script type="text/javascript">

        $(function () {
            // Check if the DataTable is already initialized and destroy it
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }


            // Initialize the DataTable
            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,

                language: {
                    url: "{{asset('dashboard/assets/js/ar.json')}}?v={{ time() }}"

                },

                ajax: {
                    url: "{{ route('owner.getTripDalalData',$id) }}",
                    data: function (d) {
                        // يمكنك إضافة فلاتر أو بارامترات هنا إن أردت
                    },
                    dataSrc: function (json) {
                        setTimeout(() => {
                            $('#boatname').text(json.boatname);
                            $('#totalDalals').text(json.total_dalals);
                            $('#totalRevenue').text(json.total_revenue + '{{ __('owner.generated.item_93fe61') }}');
                            $('#totalWeight').text(json.total_weight + '{{ __('owner.generated.item_5a01cb') }}');
                            $('#totalRemaining').text(json.total_remaining + '{{ __('owner.generated.item_5a01cb') }}');
                            $('#totalSold').text(json.total_sold + '{{ __('owner.generated.item_5a01cb') }}');
                        }, 300);
                        return json.data;
                    }
                },

                columns: [
                    {data:'DT_RowIndex', name:'DT_RowIndex'},
                    {data:'dalal_name', name:'dalal_name'},
                    {data:'total_weight', name:'total_weight'},
                    {data:'remaining_weight', name:'remaining_weight'},
                    {data:'sold_weight', name:'sold_weight'},
                    {data:'total_revenue', name:'total_revenue'},
                    {data:'details', name:'details', orderable:false, searchable:false}

                ],
                responsive: false, scrollX: true,

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
            $('#from_date, #to_date').change(function () {
                table.draw();
            });
        });
    </script>

    <script>
        $("#createForm").validate();
        $("#editForm").validate();

    </script>
    <script>
        function deleteRecord(recordId) {
            Swal.fire({
                title: '{{ __('owner.generated.item_2d62e7') }}',
                text: "{{ __('owner.generated.item_5fb9f9') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('owner.generated.item_7f4bb5') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/fish') }}/" + recordId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire('{{ __('owner.generated.item_2b6970') }}', response.message, 'success');
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function (xhr) {
                            Swal.fire('{{ __('owner.generated.item_dc5b8b') }}', '{{ __('owner.generated.item_843b15') }}', 'error');
                        }
                    });
                }
            });
        }

        // Model Edit
        $('#modelEdit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var scientific_name = button.data('scientific_name')
            var english_name = button.data('english_name')
            var scientific_name = button.data('scientific_name')
            var local_name_secondary = button.data('local_name_secondary')
            var status = button.data('status')

            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #scientific_name').val(scientific_name);
            modal.find('.modal-body #english_name').val(english_name);
            modal.find('.modal-body #scientific_name').val(scientific_name);
            modal.find('.modal-body #local_name_secondary').val(local_name_secondary);
            modal.find('.modal-body #status').prop('checked', status == 1);

        });

    </script>

@endsection
