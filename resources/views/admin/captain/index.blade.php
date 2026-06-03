@extends('admin.layouts.master')
@section('title')
    {{ __('admin.captains.title') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css') }}" rel="stylesheet">
    <style>
        #datatableDefault th,
        #datatableDefault td { text-align: center !important; vertical-align: middle; }
        .small-text th, .small-text td { font-size: 12px; text-align: center !important; vertical-align: middle; font-weight: bold; }
        label.error { color: red; font-weight: bold; margin-top: 5px; display: block; }
        .stat-card { min-height: 150px; height: 100%; border-radius: 12px; }
        .stat-icon { font-size: 2rem; margin-bottom: 5px; }
    </style>
@endsection
@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-2">{{ __('admin.captains.page_header') }}</h2>
        </div>
    </div>

    <div class="row">
        @include('owner.components.stat-card', [
            'title' => __('admin.captains.cards.total'),
            'value' => '<span id="captain_count">0</span>',
            'icon' => 'bi bi-people-fill',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.captains.cards.active'),
            'value' => '<span id="captain_active">0</span>',
            'icon' => 'bi bi-person-check-fill',
            'gradient' => 'linear-gradient(135deg, #20c997, #198754)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.captains.cards.inactive'),
            'value' => '<span id="captain_disable">0</span>',
            'icon' => 'bi bi-person-x-fill',
            'gradient' => 'linear-gradient(135deg, #dc3545, #c82333)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.captains.cards.active_percent'),
            'value' => '<span id="captain_active_percent">0%</span>',
            'icon' => 'bi bi-percent',
            'gradient' => 'linear-gradient(135deg, #ffc107, #fd7e14)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])
    </div>

    @php
        $captainStatusOptions = [
            ['value' => '', 'label' => __('admin.filters.all')],
            ['value' => '1', 'label' => __('admin.captains.status.active') ?? __('admin.status.active')],
            ['value' => '0', 'label' => __('admin.captains.status.inactive') ?? __('admin.status.inactive')],
        ];
    @endphp
    <x-admin.components.datatable-filters
        formId="captainFilters"
        :filters="[
            ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.captains.table.name'), 'value' => request('search')],
            ['type' => 'select-static', 'id' => 'status', 'name' => 'status', 'label' => __('admin.captains.table.status'), 'options' => $captainStatusOptions, 'selected' => request('status')],
        ]"
        :showArrow="false"
    />

    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <div id="datatable" class="mb-5">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                    <tr>
                        <th>{{ __('admin.table.id') }}</th>
                        <th>{{ __('admin.captains.table.name') }}</th>
                        <th>{{ __('admin.captains.table.boat_name') }}</th>
                        <th>{{ __('admin.captains.table.owner') }}</th>
                        <th>{{ __('admin.captains.table.region') }}</th>
                        <th>{{ __('admin.captains.table.governorate') }}</th>
                        <th>{{ __('admin.captains.table.port') }}</th>
                        <th>{{ __('admin.captains.table.status') }}</th>
                        <th>{{ __('admin.captains.table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
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

    <div class="card-arrow">
        <div class="card-arrow-top-left"></div>
        <div class="card-arrow-top-right"></div>
        <div class="card-arrow-bottom-left"></div>
        <div class="card-arrow-bottom-right"></div>
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
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/table-plugins.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        $(function () {
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }

            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,
                language: {@if(app()->getLocale() == 'ar') url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" @endif},
                ajax: {
                    url: "{{ route('admin.getCaptainData') }}",
                    data: function(d) {
                        var form = document.getElementById('captainFilters');
                        if (form) {
                            var fd = new FormData(form);
                            fd.forEach(function(value, key) { d[key] = value; });
                        }
                    },
                    dataSrc: function (json) {
                        $('#captain_count').text(json.captain_count);
                        $('#captain_active').text(json.captain_active);
                        $('#captain_disable').text(json.captain_disable);
                        var pct = 0;
                        if (Number(json.captain_count) > 0) {
                            pct = Math.round((Number(json.captain_active) / Number(json.captain_count)) * 100);
                        }
                        $('#captain_active_percent').text(pct + '%');
                        return json.data;
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'name', name: 'name' },
                    { data: 'boat_name', name: 'boat_name' },
                    { data: 'owner', name: 'owner' },
                    { data: 'region', name: 'region' },
                    { data: 'governorate', name: 'governorate' },
                    { data: 'port', name: 'port' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: true, searchable: false },
                ],
                responsive: true,
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            });

            $('#captainFilters').on('change keyup', 'select, input', function() {
                $('#datatableDefault').DataTable().ajax.reload();
            });
        });
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
                cancelButtonText: '{{ __('admin.swal.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl + "/" + recordId,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            Swal.fire('{{ __('admin.swal.deleted') }}', response.message, 'success');
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function (xhr) {
                            var message = xhr.responseJSON?.message || '{{ __('admin.swal.unexpected_error') }}';
                            Swal.fire('{{ __('admin.swal.error') }}', message, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
