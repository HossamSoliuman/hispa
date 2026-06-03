@extends('admin.layouts.master')

@section('title')
    {{ __('admin.menu.counters') }}
@endsection

@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet">
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
    </style>
@endsection

@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.menu.counters') }}</h2>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        @include('owner.components.stat-card', [
            'title' => __('admin.counter.total_counters') ?? __('admin.menu.counters'),
            'value' => new \Illuminate\Support\HtmlString('<div id="counter_total">0</div>'),
            'icon' => 'bi bi-people-fill',
            'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.counter.active_counters') ?? __('admin.status.active'),
            'value' => new \Illuminate\Support\HtmlString('<div id="counter_active">0</div>'),
            'icon' => 'bi bi-check-circle-fill',
            'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.counter.inactive_counters') ?? __('admin.status.inactive'),
            'value' => new \Illuminate\Support\HtmlString('<div id="counter_inactive">0</div>'),
            'icon' => 'bi bi-x-circle-fill',
            'gradient' => 'linear-gradient(135deg, #c0392b, #e74c3c)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])
    </div>

    @php
        $counterStatusOptions = [
            ['value' => '', 'label' => __('admin.filters.all')],
            ['value' => '1', 'label' => __('admin.status.active')],
            ['value' => '0', 'label' => __('admin.status.inactive')],
        ];
    @endphp
    <x-admin.components.datatable-filters
        formId="counterFilters"
        :filters="[
            ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.counter.name'), 'value' => request('search')],
            ['type' => 'select-static', 'id' => 'status', 'name' => 'status', 'label' => __('admin.counter.status') ?? __('admin.boat_types.status'), 'options' => $counterStatusOptions, 'selected' => request('status')],
        ]"
        :showArrow="false"
    />

    <div class="tab-content py-4">
        <div class="tab-pane fade show active">
            <div id="datatable" class="mb-5">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ __('admin.table.id') }}</th>
                            <th>{{ __('admin.counter.name') }}</th>
                            <th>{{ __('admin.counter.region') }}</th>
                            <th>{{ __('admin.counter.governorate') }}</th>
                            <th>{{ __('admin.status.status') }}</th>
                            <th>{{ __('admin.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const appLocale = '{{ app()->getLocale() }}';
        const languageOptions = (appLocale === 'ar')
            ? { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" }
            : {};

        const swalOptions = {
            title: '{{ __('admin.swal.confirm_title') }}',
            text: '{{ __('admin.swal.confirm_text') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __('admin.swal.confirm_yes') }}',
            cancelButtonText: '{{ __('admin.swal.cancel') }}'
        };

        $(function () {
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }

            $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,
                language: languageOptions,
                ajax: {
                    url: "{{ route('admin.getCounterData') }}",
                    data: function(d) {
                        var form = document.getElementById('counterFilters');
                        if (form) {
                            var fd = new FormData(form);
                            fd.forEach(function(value, key) { d[key] = value; });
                        }
                    },
                    dataSrc: function(json) {
                        $('#counter_total').text(json.total_count || 0);
                        $('#counter_active').text(json.active_count || 0);
                        $('#counter_inactive').text(json.inactive_count || 0);
                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'name', name: 'name' },
                    { data: 'region', name: 'region' },
                    { data: 'governorate', name: 'governorate' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                responsive: true,
            });

            $('#counterFilters').on('change keyup', 'select, input', function() {
                $('#datatableDefault').DataTable().ajax.reload();
            });
        });

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
                if (!result.isConfirmed) return;

                $.ajax({
                    url: "{{ url('admin/counter') }}/" + recordId,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function () {
                        $('#datatableDefault').DataTable().ajax.reload();
                    },
                    error: function () {
                        $('#datatableDefault').DataTable().ajax.reload();
                    }
                });
            });
        }
    </script>
@endsection

