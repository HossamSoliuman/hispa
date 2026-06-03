@extends('admin.layouts.master')
@section('title')
    {{ __('admin.crew.title') }}
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
            <h2 class="mb-2">{{ __('admin.crew.page_header') }}</h2>
        </div>
    </div>


    <div class="row">

        @include('owner.components.stat-card', [
            'title' => __('admin.crew.cards.total'),
            'value' => '<span id="crew_count">0</span>',
            'icon' => 'bi bi-people-fill',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.crew.cards.active'),
            'value' => '<span id="crew_active">0</span>',
            'icon' => 'bi bi-person-check-fill',
            'gradient' => 'linear-gradient(135deg, #20c997, #198754)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.crew.cards.inactive'),
            'value' => '<span id="crew_disable">0</span>',
            'icon' => 'bi bi-person-x-fill',
            'gradient' => 'linear-gradient(135deg, #dc3545, #c82333)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.crew.cards.active_percent'),
            'value' => '<span id="crew_active_percent">0%</span>',
            'icon' => 'bi bi-percent',
            'gradient' => 'linear-gradient(135deg, #ffc107, #fd7e14)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

    </div>

    @php
        $crewStatusOptions = [
            ['value' => '', 'label' => __('admin.filters.all')],
            ['value' => '1', 'label' => __('admin.crew.cards.active') ?? __('admin.status.active')],
            ['value' => '0', 'label' => __('admin.crew.cards.inactive') ?? __('admin.status.inactive')],
        ];
    @endphp
    <x-admin.components.datatable-filters
        formId="crewFilters"
        :filters="[
            ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.crew.table.name'), 'value' => request('search')],
            ['type' => 'select-static', 'id' => 'status', 'name' => 'status', 'label' => __('admin.crew.table.status'), 'options' => $crewStatusOptions, 'selected' => request('status')],
        ]"
        :showArrow="false"
    />

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
                        <th>{{ __('admin.table.id') }}</th>
                        <th>{{ __('admin.crew.table.name') }}</th>
                        <th>{{ __('admin.crew.table.email') }}</th>
                        <th>{{ __('admin.crew.table.phone') }}</th>
                        <th>{{ __('admin.crew.table.nationality') }}</th>
                        <th>{{ __('admin.crew.table.id_number') }}</th>
                        <th>{{ __('admin.crew.table.job_title') }}</th>
                        <th>{{ __('admin.crew.table.boat') }}</th>
                        <th>{{ __('admin.crew.table.region') }}</th>
                        <th>{{ __('admin.crew.table.governorate') }}</th>
                        <th>{{ __('admin.crew.table.port') }}</th>
                        <th>{{ __('admin.crew.table.status') }}</th>
                        <th>{{ __('admin.crew.table.actions') }}</th>

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
                    url: "{{ route('admin.getCrewData') }}",
                    data: function(d) {
                        var form = document.getElementById('crewFilters');
                        if (form) {
                            var fd = new FormData(form);
                            fd.forEach(function(value, key) { d[key] = value; });
                        }
                    },
                    dataSrc: function (json) {
                        // تحديث الكروت
                        $('#crew_count').text(json.crew_count);
                        $('#crew_active').text(json.crew_active);
                        $('#crew_disable').text(json.crew_disable);
                        // حساب نسبة النشطين
                        var pct = 0;
                        if (Number(json.crew_count) > 0) {
                            pct = Math.round((Number(json.crew_active) / Number(json.crew_count)) * 100);
                        }
                        $('#crew_active_percent').text(pct + '%');

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
                    {data: 'boat', name: 'boat'},
                    {data: 'region', name: 'region'},
                    {data: 'governorate', name: 'governorate'},
                    {data: 'port', name: 'port'},
                    {data: 'status', name: 'status'},

                    {data: 'action', name: 'action', orderable: true, searchable: false},
                ],
                responsive:true,

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });

            $('#crewFilters').on('change keyup', 'select, input', function() {
                $('#datatableDefault').DataTable().ajax.reload();
            });
        });
    </script>

    <script>
        $("#createForm").validate();

    </script>
    <script>
        function deleteRecord(recordId, deleteUrl) {
            Swal.fire({
                title: '{{ __('admin.swal.confirm_title') }}',
                text: "{{ __('admin.swal.confirm_text') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('admin.swal.confirm_yes') }}',
                cancelButtonText:'{{ __('admin.swal.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl + "/" + recordId,
                            type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire('{{ __('admin.swal.deleted') }}', response.message, 'success');
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function (xhr) {
                            let message = xhr.responseJSON?.message || '{{ __('admin.swal.unexpected_error') }}';
                            Swal.fire('{{ __('admin.swal.error') }}', message, 'error');
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
