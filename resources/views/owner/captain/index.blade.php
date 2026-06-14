@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.item_164ec2') }}
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
            <h2 class=" mb-2">{{ __('owner.captain.page_header') }}</h2>
        </div>


{{--        @if (auth()->user()->captainCount() < 4)--}}
        <div class="ms-auto">
        <a href="{{route('owner.captain.create')}}"  class="btn btn-outline-theme"><i
            class="fa fa-plus-circle btn-success fa-fw me-1"></i>{{ __('owner.captain.add_new') }}</a>
        </div>

        <!-- {{ __('owner.generated.item_84b1e0') }} -->

{{--            @endif--}}
    </div>

    <div class="row">
        {{-- Use shared stat-card component for consistent look (4 cards) --}}
        @include('owner.components.stat-card', [
            'title' => __('owner.captain.cards.total'),
            'value' => '<span id="captain_count">0</span>',
            'icon' => 'bi bi-people-fill',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.captain.cards.active'),
            'value' => '<span id="captain_active">0</span>',
            'icon' => 'bi bi-person-check-fill',
            'gradient' => 'linear-gradient(135deg, #20c997, #198754)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.captain.cards.inactive'),
            'value' => '<span id="captain_disable">0</span>',
            'icon' => 'bi bi-person-x-fill',
            'gradient' => 'linear-gradient(135deg, #dc3545, #c82333)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.captain.cards.active_percent'),
            'value' => '<span id="captain_active_percent">0%</span>',
            'icon' => 'bi bi-percent',
            'gradient' => 'linear-gradient(135deg, #ffc107, #fd7e14)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])
    </div>

    <!-- delete -->
    <div class="modal fade" id="deleteModel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('owner.captain.modal.delete_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('owner.captain.delete.close') }}"></button>
                </div>
                <form action="{{ route('owner.captain.destroy','test') }}" method="post">

                    {{method_field('delete')}}
                    {{csrf_field()}}
                    <div class="modal-body">
                        <p class="text-center">
                            <h6 class="text-danger">{{ __('owner.captain.modal.delete_confirm') }}</h6>
                        </p>

                        <input type="hidden" name="id" id="id" value="">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('owner.captain.delete.close') }}</button>
                        <button type="submit" class="btn btn-danger ">{{ __('owner.captain.delete.confirm') }}</button>
                    </div>
                </form>
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
                        <th>{{ __('owner.captain.table.index') }}</th>
                        <th>{{ __('owner.captain.table.name') }}</th>
                        <th>{{ __('owner.captain.table.email') }}</th>
                        <th>{{ __('owner.captain.table.phone') }}</th>
                        <th>{{ __('owner.captain.table.id_number') }}</th>
                        <th>{{ __('owner.captain.table.boat_name') }}</th>
                        <th>{{ __('owner.captain.table.crew_count') }}</th>
                        <th>{{ __('owner.captain.table.region') }}</th>
                        <th>{{ __('owner.captain.table.governorate') }}</th>
                        <th>{{ __('owner.captain.table.status') }}</th>
                        <th>{{ __('owner.captain.table.actions') }}</th>

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
                    url: "{{ route('owner.getCaptainData') }}",
                    data: function(d) {},
                    dataSrc: function (json) {
                        // تحديث الكروت
                        $('#captain_count').text(json.captain_count);
                        $('#captain_active').text(json.captain_active);
                        $('#captain_disable').text(json.captain_disable);
                        // حساب نسبة النشطين
                        var pct = 0;
                        if (Number(json.captain_count) > 0) {
                            pct = Math.round((Number(json.captain_active) / Number(json.captain_count)) * 100);
                        }
                        $('#captain_active_percent').text(pct + '%');

                        // إرجاع البيانات للجدول
                        return json.data;
                    },
                },

                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    {data: 'id_number', name: 'id_number'},
                    {data: 'boat_name', name: 'boat_name'},
                    {data: 'crew_count', name: 'crew_count'},
                    {data: 'region', name: 'region'},
                    {data: 'governorate', name: 'governorate'},
                    {data: 'status', name: 'status'},

                    {data: 'action', name: 'action', orderable: true, searchable: false},
                ],
                responsive: false, scrollX: true,

                buttons: [
                    // 'copy', 'excel', 'pdf'
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
                        url: "{{ url('owner/captain') }}/" + recordId,
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
