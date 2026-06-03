@extends('admin.layouts.master')

@section('title')
    {{ __('admin.menu.boats') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css') }}" rel="stylesheet">
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
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.menu.boats') }}</h2>
        </div>

     
    </div>

    @php
        $boatStatusOptions = [
            ['value' => '', 'label' => __('admin.filters.all')],
            ['value' => '1', 'label' => __('admin.status.active')],
            ['value' => '0', 'label' => __('admin.status.inactive')],
        ];
    @endphp
    <x-admin.components.datatable-filters
        formId="boatsFilters"
        :filters="[
            ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.boats.name') . ' / ' . __('admin.boats.owner'), 'value' => request('search')],
            ['type' => 'select-static', 'id' => 'status', 'name' => 'status', 'label' => __('admin.boats.status'), 'options' => $boatStatusOptions, 'selected' => request('status')],
            ['type' => 'select', 'id' => 'boat_type_id', 'name' => 'boat_type_id', 'label' => __('admin.boats.type'), 'options' => $boat_types ?? [], 'optionValue' => 'id', 'optionLabel' => 'name', 'selected' => request('boat_type_id')],
        ]"
        :showArrow="false"
    />

    <!-- Statistics Cards -->
    <div class="row">
        @include('owner.components.stat-card', [
            'title' => __('admin.boats.active_boats'),
            'value' => new \Illuminate\Support\HtmlString('<div id="boat_active">0</div>'),
            'icon' => 'bi bi-speedometer2',
            'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.boats.total_boats'),
            'value' => new \Illuminate\Support\HtmlString('<div id="boats">0</div>'),
            'icon' => 'fas fa-ship',
            'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.boats.inactive_boats'),
            'value' => new \Illuminate\Support\HtmlString('<div id="boat_inactive">0</div>'),
            'icon' => 'bi bi-x-circle',
            'gradient' => 'linear-gradient(135deg, #c0392b, #e74c3c)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])
    </div>

    <!-- DataTable -->
    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <div id="datatable" class="mb-5">
                <table id="datatableDefault"
                    class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ __('admin.table.id') }}</th>
                            <th>{{ __('admin.boats.name') }}</th>
                            <th>{{ __('admin.boats.category') }}</th>
                            <th>{{ __('admin.boats.type') }}</th>
                            <th>{{ __('admin.boats.owner') }}</th>
                            <th>{{ __('admin.boats.captain') }}</th>
                            <th>{{ __('admin.boats.status') }}</th>
                            <th>{{ __('admin.boats.actions') }}</th>
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
@endsection
@section('script')
    <script src="{{ asset('dashboard/assets/plugins/@highlightjs/cdn-assets/highlight.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/highlightjs.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}">
    </script>
    <script src="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/table-plugins.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/jquery.validate.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let appLocale = '{{ app()->getLocale() }}';
        let languageOptions = {};
        if (appLocale === 'ar') {
            languageOptions = {
                url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
            };
        }

        let swalOptions = {
            title: '{{ __('admin.swal.confirm_title') }}',
            text: '{{ __('admin.swal.confirm_text') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __('admin.swal.confirm_yes') }}',
            cancelButtonText: '{{ __('admin.swal.cancel') }}'
        };
    </script>

    <script type="text/javascript">
        $(function() {
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }

            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,
                language: languageOptions,
                ajax: {
                    url: "{{ route('admin.getBoatData') }}",
                    data: function(d) {
                        var form = document.getElementById('boatsFilters');
                        if (form) {
                            var fd = new FormData(form);
                            fd.forEach(function(value, key) { d[key] = value; });
                        }
                    },
                    dataSrc: function(json) {
                        $('#boat_active').text(json.boat_active_count || 0);
                        $('#boats').text(json.boat_count || 0);
                        $('#boat_inactive').text((json.boat_count || 0) - (json.boat_active_count || 0));
                        return json.data;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'owner',
                        name: 'owner'
                    },
                    {
                        data: 'captain',
                        name: 'captain'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                responsive: true,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });

            $('#boatsFilters').on('change keyup', 'select, input', function() {
                $('#datatableDefault').DataTable().ajax.reload();
            });
        });
    </script>

    <script>
        function deleteRecord(recordId) {
            Swal.fire({
                title: swalOptions.title,
                text: swalOptions.text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: swalOptions.confirmButtonText,
                cancelButtonText: swalOptions.cancelButtonText
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/boats') }}/" + recordId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('{{ __('admin.swal.deleted') }}', response.message || '{{ __('admin.swal.success') }}', 'success');
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message || '{{ __('admin.swal.error_occurred') }}';
                            Swal.fire('{{ __('admin.swal.error') }}', message, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
