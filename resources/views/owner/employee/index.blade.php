@extends('owner.layouts.master')
@section('title')
{{ __('owner.generated.item_cbfacb') }}
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
        .stat-card {
            min-height: 150px;
            height: 100%;
            border-radius: 12px;
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 5px;
        }
    </style>
@endsection
@section('content')

    <div class="d-flex align-items-center mb-3">
        <div>           
            <h2 class="mb-2">{{ __('owner.employee.page_header') }}</h2>
        </div>


        <div class="ms-auto">
        <a href="{{route('owner.employee.create')}}"  class="btn btn-outline-theme"><i
            class="fa fa-plus-circle btn-success fa-fw me-1"></i>{{ __('owner.employee.add_new') }}</a>
        </div>

    </div>


    <div class="row">

        @include('owner.components.stat-card', [
            'title' => __('owner.employee.cards.total'),
            'value' => '<span id="employee_count">0</span>',
            'icon' => 'bi bi-people-fill',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.employee.cards.active'),
            'value' => '<span id="employee_active">0</span>',
            'icon' => 'bi bi-person-check-fill',
            'gradient' => 'linear-gradient(135deg, #20c997, #198754)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.employee.cards.inactive'),
            'value' => '<span id="employee_disable">0</span>',
            'icon' => 'bi bi-person-x-fill',
            'gradient' => 'linear-gradient(135deg, #dc3545, #c82333)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.employee.cards.active_percent'),
            'value' => '<span id="employee_active_percent">0%</span>',
            'icon' => 'bi bi-percent',
            'gradient' => 'linear-gradient(135deg, #ffc107, #fd7e14)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

    </div>


    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <!-- BEGIN #datatable -->
            <div id="datatable" class="mb-5">
                {{--                    <div class="card">--}}
                {{--                        <div class="card-body">--}}
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>

                    <tr>
                        <th>{{ __('owner.employee.table.index') }}</th>
                        <th>{{ __('owner.employee.table.name') }}</th>
                        <th>{{ __('owner.employee.table.email') }}</th>
                        <th>{{ __('owner.employee.table.phone') }}</th>
                        <th>{{ __('owner.employee.table.nationality') }}</th>
                        <th>{{ __('owner.employee.table.id_number') }}</th>
                        <th>{{ __('owner.employee.table.job_title') }}</th>
                        <th>{{ __('owner.employee.table.status') }}</th>
                        <th>{{ __('owner.employee.table.actions') }}</th>

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

                language: {@if(app()->getLocale() == 'ar')
                    url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                    @endif
                },

                ajax: {
                    url: "{{ route('owner.getEmployeeData') }}",
                    data: function(d) {
                        // d.from_date = $('#from_date').val();
                        // d.to_date = $('#to_date').val();
                    }, dataSrc: function (json) {
                        // تحديث الكروت
                        $('#employee_count').text(json.employee_count);
                        $('#employee_active').text(json.employee_active);
                        $('#employee_disable').text(json.employee_disable);
                        // حساب نسبة النشطين
                        var pct = 0;
                        if (Number(json.employee_count) > 0) {
                            pct = Math.round((Number(json.employee_active) / Number(json.employee_count)) * 100);
                        }
                        $('#employee_active_percent').text(pct + '%');

                        // إرجاع البيانات للجدول
                        return json.data;
                    },
                },

                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    {data: 'nationality', name: 'nationality'},
                    {data: 'id_number', name: 'id_number'},
                    {data: 'job_title', name: 'job_title'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: true, searchable: false},
                ],
                responsive: false, scrollX: true,

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
            $('#from_date, #to_date').change(function() {
                table.draw();
            });
        });
    </script>

    <script>
        $("#createForm").validate();

    </script>
    <script>
        function deleteRecord(recordId) {
            Swal.fire({
                title: '{{ __('owner.swal.confirm_title') }}',
                text: "{{ __('owner.swal.confirm_text') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('owner.swal.confirm_yes') }}',
                cancelButtonText:'{{ __('owner.swal.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('owner/employee') }}/" + recordId,
                            type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire('{{ __('owner.swal.deleted') }}', response.message, 'success');
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function (xhr) {
                            let message = xhr.responseJSON?.message || '{{ __('owner.swal.unexpected_error') }}';
                            Swal.fire('{{ __('owner.swal.error') }}', message, 'error');
                        }

                    });
                }
            });
        }

        // Model Edit
        $('#modelEdit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var name = button.data('name')
            var email = button.data('email')
            var phone = button.data('phone')
            var notes = button.data('notes')

            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #name').val(name);
            modal.find('.modal-body #email').val(email);
            modal.find('.modal-body #phone').val(phone);
            modal.find('.modal-body #notes').val(notes);

        });

    </script>


@endsection
