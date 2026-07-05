@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.view_crew') }}
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
    @php
        $sarIcon = view('components.riyal-icon', [
            'size' => 'sm',
            'style' => 'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-inline-start:.2rem;',
        ])->render();
    @endphp

    {{-- Page header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <ul class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('owner.crew.index') }}">{{ __('owner.generated.crew_management') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.generated.view_crew') }}</li>
            </ul>
            <h1 class="h3 fw-bold mb-0">
                <i class="bi bi-person-workspace text-primary me-2"></i>{{ $user->name }}
            </h1>
        </div>
    </div>

    {{-- Profile hero --}}
    @include('owner.partials._person_profile', [
        'user' => $user,
        'editRoute' => route('owner.crew.edit', $user->id),
        'statementRoute' => route('owner.crew.payroll-statement', $user->id),
    ])

    {{-- KPI cards --}}
    <div class="row g-3 mb-3">
        @include('owner.components.stat-card', [
            'title' => __('owner.generated.trips_count'),
            'value' => '<span id="trips_count">0</span>',
            'icon' => 'bi bi-basket3',
            'colClass' => 'col-6 col-lg-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('owner.generated.current_boat_name'),
            'value' => '<span id="boat_name">—</span>',
            'icon' => 'bi bi-water',
            'colClass' => 'col-6 col-lg-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('owner.payrolls.statement.total_unpaid'),
            'value' => number_format($stats->unpaid_dues ?? 0, 2) . ' ' . $sarIcon,
            'icon' => 'bi bi-cash-stack',
            'colClass' => 'col-6 col-lg-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('owner.crew_advances.total'),
            'value' => number_format($stats->total_advances ?? 0, 2) . ' ' . $sarIcon,
            'icon' => 'bi bi-wallet2',
            'colClass' => 'col-6 col-lg-3',
        ])
    </div>

    {{-- Advances (السلف) --}}
    @include('owner.crew-advances._profile', ['user' => $user])
    @include('owner.crew-advances._modal', ['people' => collect([$user]), 'selectedUserId' => $user->id])

    {{-- Trip log --}}
    <div class="card border-0 shadow-sm">
        @include('owner.partials._card_arrow')
        <div class="card-header bg-transparent border-bottom d-flex align-items-center py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-clipboard-data text-primary me-2"></i>{{ __('owner.generated.crew_trip_log') }}
            </h5>
        </div>
        <div class="card-body p-0">
            <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text mb-0"
                style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.sales.trip') }}</th>
                        <th>{{ __('owner.sales.boat') }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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



    <script type="text/javascript">
        $(function() {
            // Check if the DataTable is already initialized and destroy it
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }


            // Initialize the DataTable
            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,

                language: {
                    url: "{{ asset('dashboard/assets/js/ar.json') }}?v={{ time() }}"

                },

                ajax: {
                    url: "{{ route('owner.showCrewData', $user->id) }}",
                    data: function(d) {
                        // d.from_date = $('#from_date').val();
                        // d.to_date = $('#to_date').val();
                    },
                    dataSrc: function(json) {
                        $('#boat_name').text(json.boat_name);
                        $('#trips_count').text(json.trips_count);

                        return json.data;
                    },


                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'boat_name',
                        name: 'trip.name'
                    },
                    {
                        data: 'trip_name',
                        name: 'fish_name'
                    },

                ],

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                responsive: false, scrollX: true

            });
            $('#from_date, #to_date').change(function() {
                table.draw();
            });
        });
    </script>

    <script>
        $("#createForm").validate();
    </script>
@endsection
