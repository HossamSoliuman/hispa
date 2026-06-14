@extends('owner.layouts.master')
@section('title')
    {{ __('owner.dalal_stock_boat.page_title') }}
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
        .stat-card-hover .stat-value .unit svg {
            width: 14px !important;
            height: 14px !important;
        }
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
    <!-- BEGIN #modalCreate -->
    <div class="modal fade" id="modalCreate">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('owner.generated.add_new_fish') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{route('admin.fish.store')}}" id="createForm" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="row">

                            <div class="col-4 ">

                                <div class="form-group ">
                                    <label for="scientific_name" class="form-label">{{ __('owner.fish.scientific_name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="scientific_name" value="{{old('scientific_name')}}"
                                           class="form-control  " required
                                           placeholder="{{ __('owner.fish.scientific_name') }}">


                                    @error('scientific_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                            <div class="col-4 ">
                                <div class="form-group ">
                                    <label for="english_name" class="form-label">{{ __('owner.generated.name_in_english') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="english_name" value="{{old('english_name')}}"
                                           class="form-control  " required
                                           placeholder="{{ __('owner.generated.name_in_english') }}">


                                    @error('english_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                            <div class="col-4 ">
                                <div class="form-group ">
                                    <label for="local_name_primary" class="form-label">{{ __('owner.generated.local_name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="local_name_primary" value="{{old('local_name_primary')}}"
                                           class="form-control  " required
                                           placeholder="{{ __('owner.generated.name_local_name') }}">


                                    @error('local_name_primary') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-4 ">
                                <div class="form-group ">
                                    <label for="local_name_secondary" class="form-label">{{ __('owner.generated.secondary_local_name') }}</label>
                                    <input type="text" name="local_name_secondary"
                                           value="{{old('local_name_secondary')}}"
                                           class="form-control  "
                                           placeholder="{{ __('owner.generated.name_secondary_local_name') }}">


                                    @error('local_name_secondary') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                            <div class="col-6 ">
                                <div class="form-check form-switch " style="margin-top: 35px">
                                    <input type="checkbox" name="status" checked class="form-check-input" value="1">
                                    <label class="form-check-label" for="status">{{ __('owner.customers.modal.labels.status') }}</label>
                                    @error('status') <span class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('owner.customers.modal.buttons.close') }}</button>
                        <button type="submit" class="btn btn-outline-theme">{{ __('owner.customers.modal.buttons.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END #modalCreate-->
    <div class="modal fade" id="modelEdit">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('owner.generated.edit_fish_data') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{route('admin.fish.update','update')}}" id="editForm"
                      method="post"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">

                        <div class="row">

                            <div class="col-4 ">
                                <input type="hidden" name="id" id="id">
                                <div class="form-group ">
                                    <label for="scientific_name" class="form-label">{{ __('owner.fish.scientific_name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="scientific_name" id="scientific_name"
                                           value="{{old('scientific_name')}}"
                                           class="form-control  " required
                                           placeholder="{{ __('owner.fish.scientific_name') }}">


                                    @error('scientific_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                            <div class="col-4 ">
                                <div class="form-group ">
                                    <label for="english_name" class="form-label">{{ __('owner.generated.name_in_english') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="english_name" id="english_name"
                                           value="{{old('english_name')}}"
                                           class="form-control  " required
                                           placeholder="{{ __('owner.generated.name_in_english') }}">


                                    @error('english_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                            <div class="col-4 ">
                                <div class="form-group ">
                                    <label for="scientific_name" class="form-label">{{ __('owner.generated.local_name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="scientific_name" id="scientific_name"
                                           value="{{old('scientific_name')}}"
                                           class="form-control  " required
                                           placeholder="{{ __('owner.generated.name_local_name') }}">


                                    @error('scientific_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-4 ">
                                <div class="form-group ">
                                    <label for="local_name_secondary" class="form-label">{{ __('owner.generated.secondary_local_name') }}</label>
                                    <input type="text" name="local_name_secondary" id="local_name_secondary"
                                           value="{{old('local_name_secondary')}}"
                                           class="form-control  "
                                           placeholder="{{ __('owner.generated.name_secondary_local_name') }}">


                                    @error('local_name_secondary') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                            <div class="col-6 ">
                                <div class="form-check form-switch " style="margin-top: 35px">
                                    <input type="checkbox" name="status" id="status" checked class="form-check-input"
                                           value="1">
                                    <label class="form-check-label" for="status">{{ __('owner.customers.modal.labels.status') }}</label>
                                    @error('status') <span class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('owner.customers.modal.buttons.close') }}</button>
                        <button type="submit" class="btn btn-outline-theme">{{ __('owner.customers.modal.buttons.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- delete -->
    <div class="modal fade" id="deleteModel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('owner.generated.delete_contact') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="post">

                    {{method_field('delete')}}
                    {{csrf_field()}}
                    <div class="modal-body">
                        <p class="text-center">
                        <h6 style="color:red"> {{ __('owner.generated.confirm_delete_operation') }}</h6>
                        </p>

                        <input type="hidden" name="id" id="id" value="">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('owner.customers.modal.buttons.close') }}</button>
                        <button type="submit" class="btn btn-danger ">{{ __('owner.customers.modal.buttons.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        @include('owner.components.stat-card', [
            'title' => __('owner.dalal_stock_boat.cards.total_boats'),
            'value' => '<span id="totalBoats">0</span>',
            'icon' => 'bi bi-ship',
            'gradient' => 'linear-gradient(135deg, #17a2b8, #20c997)',
            'colClass' => 'col-md-2 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.dalal_stock_boat.cards.total_dalals'),
            'value' => '<span id="totalDalals">0</span>',
            'icon' => 'bi bi-people-fill',
            'gradient' => 'linear-gradient(135deg, #6610f2, #7952b3)',
            'colClass' => 'col-md-2 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.dalal_stock_boat.cards.total_revenue'),
            'value' => '<span id="totalRevenue">0</span> <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>',
            'icon' => 'bi bi-currency-exchange',
            'gradient' => 'linear-gradient(135deg, #28a745, #20c997)',
            'colClass' => 'col-md-2 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.dalal_stock_boat.cards.total_weight'),
            'value' => '<span id="totalWeight">0</span> <span class="unit">' . __('owner.units.kg') . '</span>',
            'icon' => 'bi bi-minecart-loaded',
            'gradient' => 'linear-gradient(135deg, #28a745, #20c997)',
            'colClass' => 'col-md-2 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.dalal_stock_boat.cards.total_remaining'),
            'value' => '<span id="totalRemaining">0</span> <span class="unit">' . __('owner.units.kg') . '</span>',
            'icon' => 'bi bi-boxes',
            'gradient' => 'linear-gradient(135deg, #ffc107, #fd7e14)',
            'colClass' => 'col-md-2 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.dalal_stock_boat.cards.total_sold'),
            'value' => '<span id="totalSold">0</span> <span class="unit">' . __('owner.units.kg') . '</span>',
            'icon' => 'bi bi-bag-check-fill',
            'gradient' => 'linear-gradient(135deg, #dc3545, #c82333)',
            'colClass' => 'col-md-2 col-sm-6 mb-3'
        ])
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
                        <th>{{ __('owner.dalal_stock_boat.table.boat_name') }}</th>
                        <th>{{ __('owner.dalal_stock_boat.table.total_weight') }}</th>
                        <th>{{ __('owner.dalal_stock_boat.table.remaining_weight') }}</th>
                        <th>{{ __('owner.dalal_stock_boat.table.sold_weight') }}</th>
                        <th>{{ __('owner.dalal_stock_boat.table.total_revenue') }}</th>
                        <th>{{ __('owner.dalal_stock_boat.table.details') }}</th>


                    </tr>
                    </thead>
                    <tbody>


                    </tbody>
                </table>
            </div>
            @include('owner.partials._card_arrow')

        </div>
    </div>
    <!-- END #datatable -->


    {{--            </div>--}}
    {{--        </div>--}}
    <!-- END #datatable -->

    @include('owner.partials._card_arrow')
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
                    url: "{{ route('owner.getDalalStockBoatData') }}",
                    data: function (d) {
                        // يمكنك إضافة فلاتر أو بارامترات هنا إن أردت
                    },
                    dataSrc: function (json) {
                        setTimeout(() => {
                            $('#totalBoats').text(json.total_boats);
                            $('#totalDalals').text(json.total_dalals);
                            $('#totalWeight').text(json.total_weight);
                            $('#totalSold').text(json.total_sold);
                            $('#totalRemaining').text(json.total_remaining);
                            $('#totalRevenue').text(json.total_revenue);
                        }, 300);
                        return json.data;
                    }
                },

                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'boat_name', name: 'boat_name' },
                    { data: 'total_weight', name: 'total_weight' },
                    { data: 'remaining_weight', name: 'remaining_weight' },
                    {data: 'sold_weight', name: 'sold_weight'},
                    { data: 'total_revenue', name: 'total_revenue' },
                    { data: 'details', name: 'details', orderable: false, searchable: false }

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
