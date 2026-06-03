@extends('admin.layouts.master')

@section('title')
    {{ __('admin.menu.owners') }}
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
            font-size: 13px;
            text-align: center !important;
            vertical-align: middle;
        }
        #datatableDefault thead th {
            font-weight: 600;
            white-space: nowrap;
        }
        #datatableDefault tbody td {
            font-weight: 500;
        }
        #datatableDefault .badge {
            font-size: 0.75rem;
        }
    </style>
@endsection

@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.menu.owners') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.owner.create') }}" class="btn btn-primary btn-equal">
                <i class="fa fa-plus-circle me-1"></i> {{ __('admin.owner.create_title') }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        @include('owner.components.stat-card', [
            'title' => __('admin.owner.total_owners'),
            'value' => new \Illuminate\Support\HtmlString('<div id="owner_total">0</div>'),
            'icon' => 'bi bi-people-fill',
            'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.owner.active_owners'),
            'value' => new \Illuminate\Support\HtmlString('<div id="owner_active">0</div>'),
            'icon' => 'bi bi-check-circle-fill',
            'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.owner.inactive_owners'),
            'value' => new \Illuminate\Support\HtmlString('<div id="owner_inactive">0</div>'),
            'icon' => 'bi bi-x-circle-fill',
            'gradient' => 'linear-gradient(135deg, #c0392b, #e74c3c)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.owner.payroll_fixed'),
            'value' => new \Illuminate\Support\HtmlString('<div id="owner_fixed">0</div>'),
            'icon' => 'bi bi-cash-coin',
            'gradient' => 'linear-gradient(135deg, #0984e3, #74b9ff)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('admin.owner.payroll_percentage'),
            'value' => new \Illuminate\Support\HtmlString('<div id="owner_percentage">0</div>'),
            'icon' => 'bi bi-percent',
            'gradient' => 'linear-gradient(135deg, #6c5ce7, #a29bfe)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
        'title' => 'الصيادين الأفراد',
        'value' => new \Illuminate\Support\HtmlString('<div id="owner_fisherman">0</div>'),
        'icon' => 'bi bi-person-badge-fill',
        'gradient' => 'linear-gradient(135deg, #16a085, #1abc9c)',
        'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
        'title' => 'المؤسسات / الشركات',
        'value' => new \Illuminate\Support\HtmlString('<div id="owner_company">0</div>'),
        'icon' => 'bi bi-building',
        'gradient' => 'linear-gradient(135deg, #8e44ad, #9b59b6)',
        'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])
    </div>

    @php
        $payrollFilterOptions = [
            ['value' => '', 'label' => __('admin.owner.filter_all')],
            ['value' => 'fixed', 'label' => __('admin.owner.payroll_fixed')],
            ['value' => 'percentage', 'label' => __('admin.owner.payroll_percentage')],
        ];

        $ownerTypeFilterOptions = [
            ['value' => '', 'label' => __('admin.owner.filter_all') ?? __('admin.filters.all')],
            ['value' => 'fisherman', 'label' => 'صيّاد (فرد)'],
            ['value' => 'company', 'label' => 'مؤسسة / شركة'],
        ];
    @endphp
    <x-admin.components.datatable-filters
        formId="ownerFilters"
        :filters="[
            ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.owner.name'), 'value' => request('search')],
            ['type' => 'select-static', 'id' => 'payroll_type', 'name' => 'payroll_type', 'label' => __('admin.owner.payroll_type'), 'options' => $payrollFilterOptions, 'selected' => request('payroll_type', 'all')],
            ['type' => 'select-static', 'id' => 'owner_type', 'name' => 'owner_type', 'label' => 'نوع المالك', 'options' => $ownerTypeFilterOptions, 'selected' => request('owner_type')],
        ]"
        :showArrow="false"
    />

    <div class="tab-content py-4">
        <div class="tab-pane fade show active">
            <div id="datatable" class="mb-5">
                <div class="table-responsive">
                    <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('admin.table.id') }}</th>
                                <th>{{ __('admin.owner.name') }}</th>
                                <th>{{ __('admin.owner.phone') }}</th>
                                <th>{{ __('admin.owner.email') }}</th>
                                <th>{{ __('admin.owner.region') }}</th>
                                <th>{{ __('admin.owner.governorate') }}</th>
                                <th>{{ __('admin.owner.port') }}</th>
                                <th>{{ __('admin.owner.boats_count') }}</th>
                                <th>نوع المالك</th>
                                <th>{{ __('admin.owner.payroll_type') }}</th>
                                <th>{{ __('admin.owner.subscription_status') }}</th>
                                <th>{{ __('admin.owner.registered_at') }}</th>
                                <th>{{ __('admin.owner.status') }}</th>
                                <th class="text-center">{{ __('admin.actions.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
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
                    url: "{{ route('admin.getOwnerData') }}",
                    data: function(d) {
                        var form = document.getElementById('ownerFilters');
                        if (form) {
                            var fd = new FormData(form);
                            fd.forEach(function(value, key) { d[key] = value; });
                        }
                        if (!d.payroll_type) d.payroll_type = 'all';
                    },
                    dataSrc: function(json) {
                        $('#owner_total').text(json.total_count ?? 0);
                        $('#owner_active').text(json.active_count ?? 0);
                        $('#owner_inactive').text(json.inactive_count ?? 0);
                        $('#owner_fixed').text(json.fixed_count ?? 0);
                        $('#owner_percentage').text(json.percentage_count ?? 0);
                        $('#owner_fisherman').text(json.fisherman_count ?? 0);
                        $('#owner_company').text(json.company_count ?? 0);
                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '40px' },
                    { data: 'name', name: 'name' },
                    { data: 'phone', name: 'phone' },
                    { data: 'email', name: 'email' },
                    { data: 'region', name: 'region' },
                    { data: 'governorate', name: 'governorate' },
                    { data: 'port', name: 'port' },
                    { data: 'boats_count', name: 'boats_count', orderable: false, searchable: false, width: '90px' },
                    { data: 'owner_type', name: 'owner_type', orderable: false, searchable: false, width: '110px' },
                    { data: 'payroll_type', name: 'payroll_type', orderable: false, searchable: false, width: '110px' },
                    { data: 'subscription_status', name: 'subscription_status', orderable: false, searchable: false, width: '100px' },
                    { data: 'registered_at', name: 'created_at' },
                    { data: 'status', name: 'status', orderable: false, searchable: false, width: '90px' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, width: '240px' },
                ],
                order: [[11, 'desc']],
                responsive: true,
                pageLength: 25,
            });

            $('#ownerFilters').on('change keyup', 'select, input', function() {
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
                    url: "{{ url('admin/owner') }}/" + recordId,
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

