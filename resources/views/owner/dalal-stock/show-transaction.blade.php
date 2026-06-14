@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.item_21553c') }}
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


    <div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-labelledby="saleDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saleDetailsModalLabel">{{ __('owner.generated.sales_details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('owner.dalal_invoices.fish_name') }}</th>
                            <th>{{ __('owner.assets.weight') }}({{ __('owner.sales_report.kg') }})</th>
                            <th>{{ __('owner.expenses.print.quantity') }}</th>
                            <th>{{ __('owner.generated.price_per_kilo') }}</th>
                            <th>{{ __('owner.dalal_invoices.total') }}</th>
                            <th>{{ __('owner.generated.remaining') }}</th>
                        </tr>
                        </thead>
                        <tbody id="saleDetailsBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- {{ __('owner.generated.item_84b1e0') }} -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-primary">
                <div class="card-header fw-bold bg-primary text-white text-center">
                    {{ __('owner.generated.sales_statistics') }}</div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6 col-md-4 col-lg-2">
                            <strong>{{ __('owner.dalal_invoices.table.dalal_name') }}</strong>
                            <span class="badge bg-info fs-6 mt-1" id="dalal_name">0</span>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <strong>{{ __('owner.generated.items_count') }}</strong>
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
                <div class="table-responsive">
                    <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                        <thead>

                        <tr>
                            <th>#</th>
                            <th>{{ __('owner.dalal_invoices.table.sale_date') }}</th>
                            <th>{{ __('owner.generated.operation_number') }}</th>
                            <th>{{ __('owner.generated.total_sales') }}</th>
                            <th>{{ __('owner.generated.broker_commission') }}</th>
                            <th>{{ __('owner.generated.labor_commission') }}</th>
                            <th>{{ __('owner.generated.total_broker') }}</th>
                            <th>{{ __('owner.generated.total_fisherman') }}</th>
                            <th>{{ __('owner.generated.items_count') }}</th>
                            <th>{{ __('owner.generated.available_quantity') }}({{ __('owner.sales_report.kg') }})</th>
                            <th>{{ __('owner.dalal.table.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>



                        </tbody>
                    </table>
                </div>            </div>
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
                    url: "{{ route('owner.getDalalTransactionData',$id) }}",
                    data: function (d) {
                    },
                    dataSrc: function (json) {
                        setTimeout(() => {
                            $('#dalal_name').text(json.dalal_name ?? '-');
                            $('#totalDalals').text(json.total_fish_count ?? 0);
                            $('#totalRevenue').text((json.total_sales_amount ?? 0) + '{{ __('owner.generated.item_93fe61') }}');
                            $('#totalWeight').text((json.total_stock_weight ?? 0) + '{{ __('owner.generated.item_5a01cb') }}');
                            $('#totalRemaining').text((json.total_remaining_weight ?? 0) + '{{ __('owner.generated.item_5a01cb') }}');
                            $('#totalSold').text((json.total_sold_weight ?? 0) + '{{ __('owner.generated.item_5a01cb') }}');

                        }, 300);
                        return json.data;

                    }
                },

                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'date', name: 'date' },
                    { data: 'number', name: 'number' },
                    { data: 'total_sales_amount', name: 'total_sales_amount' },
                    { data: 'commission_rate', name: 'commission_rate' },
                    { data: 'labor_rate', name: 'labor_rate' },
                    { data: 'total_dalal_commission', name: 'total_dalal_commission' },
                    { data: 'total_owner_amount', name: 'total_owner_amount' },
                    { data: 'fish_count', name: 'fish_count' },
                    { data: 'remaining_stock_weight', name: 'remaining_stock_weight' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }

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
<script>
    $(document).on('click', '.showSaleDetailsBtn', function() {
        let saleId = $(this).data('id');

        $.ajax({
            url: '/owner/sale-details/' + saleId,
            type: 'GET',
            success: function(res) {
                let tbody = '';
                res.forEach((item, index) => {
                    tbody += `<tr>
                    <td>${index + 1}</td>
                    <td>${item.fish_name}</td>
                    <td>${item.weight}</td>
                    <td>${item.quantity}</td>
                    <td>${item.price_per_kilo}</td>
                    <td>${item.total_price}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary showRemainingBtn" data-stock-id="${item.dalal_stock_detail_id}">+</button>
                        <span class="remainingText" id="remaining-${item.dalal_stock_detail_id}"></span>
                    </td>
                </tr>`;
                });
                $('#saleDetailsBody').html(tbody);
                $('#saleDetailsModal').modal('show');
            }
        });
    });
    $(document).on('click', '.showRemainingBtn', function() {
        let stockDetailId = $(this).data('stock-id');

        $.ajax({
            url: '/owner/remaining-stock/' + stockDetailId,
            type: 'GET',
            success: function(res) {
                $('#remaining-' + stockDetailId).text(res.remaining_weight + '{{ __('owner.generated.item_5a01cb') }}');
            }
        });
    });


</script>
@endsection
