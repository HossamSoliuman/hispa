@extends('admin.layouts.master')
@section('title')
    {{ __('admin.customers.title') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css') }}" rel="stylesheet">
    <style>
        #datatableDefault th, #datatableDefault td { text-align: center !important; vertical-align: middle; }
        .small-text th, .small-text td { font-size: 12px; text-align: center !important; vertical-align: middle; font-weight: bold; }
        label.error { color: red; font-weight: bold; margin-top: 5px; display: block; }
        .stat-card { min-height: 150px; height: 100%; border-radius: 12px; }
        .stat-icon { font-size: 2rem; margin-bottom: 5px; }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-2">{{ __('admin.customers.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <button class="btn btn-outline-theme btn-equal" data-bs-toggle="modal" data-bs-target="#modalCreate">
                <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.customers.add_button') }}
            </button>
        </div>
    </div>

    @include('admin.customers._models')

    <div class="row mb-4">
        @include('owner.components.stat-card', [
            'title' => __('admin.customers.cards.total'),
            'value' => '<span id="customerCount">0</span>',
            'icon' => 'bi bi-people',
            'gradient' => 'linear-gradient(135deg, #f59f00, #f97316)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.customers.cards.active'),
            'value' => '<span id="customerCountActive">0</span>',
            'icon' => 'bi bi-person-check',
            'gradient' => 'linear-gradient(135deg, #16a34a, #059669)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.customers.cards.total_sales'),
            'value' => '<span id="totalSales">0</span> <span class="unit">' . __('admin.units.sar') . '</span>',
            'icon' => 'bi bi-cash-stack',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.customers.cards.total_orders'),
            'value' => '<span id="totalOrders">0</span>',
            'icon' => 'bi bi-receipt',
            'gradient' => 'linear-gradient(135deg, #0f172a, #1f2937)',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])
    </div>

    @php
        $customerStatusOptions = [
            ['value' => '', 'label' => __('admin.filters.all')],
            ['value' => '1', 'label' => __('admin.customers.cards.active') ?? __('admin.status.active')],
            ['value' => '0', 'label' => __('admin.status.inactive')],
        ];
    @endphp
    <x-admin.components.datatable-filters
        formId="customerFilters"
        :filters="[
            ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.customers.table.name'), 'value' => request('search')],
            ['type' => 'select-static', 'id' => 'status', 'name' => 'status', 'label' => __('admin.customers.table.status'), 'options' => $customerStatusOptions, 'selected' => request('status')],
        ]"
        :showArrow="false"
    />

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead>
                    <tr>
                        <th>{{ __('admin.table.id') }}</th>
                        <th>{{ __('admin.customers.table.name') }}</th>
                        <th>{{ __('admin.customers.table.email') }}</th>
                        <th>{{ __('admin.customers.table.phone') }}</th>
                        <th>{{ __('admin.customers.table.type') }}</th>
                        <th>{{ __('admin.customers.table.order_count') }}</th>
                        <th>{{ __('admin.customers.table.total_sales') }}</th>
                        <th>{{ __('admin.customers.table.last_order') }}</th>
                        <th>{{ __('admin.customers.table.status') }}</th>
                        <th>{{ __('admin.customers.table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/table-plugins.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/jquery.validate.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>
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
                    url: "{{ route('admin.getCustomerData') }}",
                    data: function (d) {
                        var form = document.getElementById('customerFilters');
                        if (form) {
                            var fd = new FormData(form);
                            fd.forEach(function (value, key) { d[key] = value; });
                        }
                    },
                    dataSrc: function (json) {
                        $('#customerCount').text(json.customer_count);
                        $('#customerCountActive').text(json.customer_count_active);
                        $('#totalSales').text(json.total_sales);
                        $('#totalOrders').text(json.total_orders);
                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'type', name: 'type' },
                    { data: 'order_count', name: 'order_count' },
                    { data: 'total_sales', name: 'total_sales' },
                    { data: 'last_order', name: 'last_order' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: true, searchable: false },
                ],
                responsive: true,
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            });

            $('#customerFilters').on('change keyup', 'select, input', function () {
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

        $('#modelEdit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('.modal-body #id').val(button.data('id'));
            modal.find('.modal-body #name').val(button.data('name'));
            modal.find('.modal-body #email').val(button.data('email'));
            modal.find('.modal-body #phone').val(button.data('phone'));
            modal.find('.modal-body #notes').val(button.data('notes'));
            modal.find('.modal-body #status').prop('checked', button.data('status') == 1);
            modal.find('.modal-body #type').val(button.data('type'));
        });
    </script>
@endsection
