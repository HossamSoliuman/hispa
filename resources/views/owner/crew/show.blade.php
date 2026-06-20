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

        /* {{ __('owner.generated.item_ed06b0') }} */
        .small-text th,
        .small-text td {
            font-size: 12px;
            /* {{ __('owner.generated.or') }} 13px {{ __('owner.generated.item_4cc9e8') }} */
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

        .profile-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        [data-bs-theme=dark] .profile-card { background-color: var(--bs-secondary-bg); box-shadow: none; }

        .profile-logo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #0d6efd;
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
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"> {{ __('owner.generated.view_crew') }}</a></li>
                <li class="breadcrumb-item active"> {{ __('owner.generated.view_crew') }}</li>
            </ul>
            <h1 class="page-header mb-0"> {{ __('owner.generated.view_crew') }}- {{ $user->name }}</h1>
        </div>


    </div>


    {{-- Profile Section --}}
    <div class="row mb-3">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center bg-primary text-white p-5 h-80">
                    <div class="mb-4">
                        @if ($user->logo)
                            <img src="{{ asset($user->logo) }}" class="rounded-circle shadow" width="120" height="120"
                                alt="Logo">
                        @else
                            <img src="{{ asset('default-avatar.png') }}" class="rounded-circle shadow" width="120"
                                height="120" alt="Default Logo">
                        @endif
                    </div>

                    <h5 class="mb-1 text-white">{{ $user->name }}</h5>
                    <p class="mb-2 text-white">{{ $user->role }}</p>
                    <p>
                        @if (auth()->user()->status)
                            <span class="badge bg-success p-2"><i class="fa fa-clock"></i>
                                {{ __('owner.assets.active') }}</span>
                        @else
                            <span class="badge bg-danger p-2">{{ __('owner.fish.status_inactive') }}</span>
                        @endif
                    </p>
                </div>
                <div class="text-center h-20">
                    <a href="{{ route('owner.crew.edit', $user->id) }}" class="btn btn-primary rounded-4 m-4">
                        <i class="bi bi-pencil"></i> {{ __('owner.actions.edit') }}</a>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-5 text-start h-100">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-2">
                                <i
                                    class="bi bi-envelope  d-inline-block bg-primary-100 p-2 rounded-3 p-4 fs-4 text-center line-height-100 text-primary"></i>
                            </div>
                            <div class="col-md-10">
                                <p class="my-2">{{ __('owner.generated.email_address') }}</p>
                                <p class="m-0"><strong>{{ $user->email }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mt-3">
                                <i
                                    class="bi bi-telephone d-inline-block  bg-success-100 p-2 rounded-3 p-4 fs-4 text-center line-height-100 text-success"></i>
                            </div>
                            <div class="col-md-10 mt-3">
                                <p class="my-2">{{ __('owner.generated.phone_number_1') }}</p>
                                <p class="m-0"><strong>{{ $user->phone ?? '-----' }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mt-3">
                                <i
                                    class="bi bi-pin-fill d-inline-block  bg-warning-100 p-2 rounded-3 p-4 fs-4 text-center line-height-100 text-warning"></i>
                            </div>
                            <div class="col-md-10 mt-3">
                                <p class="my-2">{{ __('owner.generated.region') }}</p>
                                <p class="m-0"><strong>{{ $user->region?->name ?? '-----' }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mt-3">
                                <i
                                    class="bi bi-pin-map d-inline-block  bg-default-100 p-2 rounded-3 p-4 fs-4 text-center line-height-100 text-default"></i>
                            </div>
                            <div class="col-md-10 mt-3">
                                <p class="my-2">{{ __('owner.generated.governorate') }}</p>
                                <p class="m-0"><strong>{{ $user->governorate?->name ?? '-----' }}</strong>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

        </div>
    </div>


    {{-- Statistics Cards --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="stat-card bg-primary text-white p-4 text-center shadow rounded-0">
                <i class="bi bi-basket3 stat-icon"></i>
                <h5 class="my-2 text-white">{{ __('owner.generated.trips_count') }}</h5>
                <h3 class="text-white" id="trips_count"></h3>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card bg-success text-white p-4 text-center shadow rounded-0">
                <i class="bi bi-box-seam stat-icon"></i>
                <h5 class="my-2 text-white">{{ __('owner.generated.current_boat_name') }}</h5>
                <h3 class="text-white" id="boat_name"></h3>
            </div>
        </div>

    </div>


    {{-- Sales Table --}}
    <div class="">
        <div class="card-header bg-primary text-white p-2 mb-2">
            <h4 class="mb-0 text-white">{{ __('owner.generated.crew_trip_log') }}</h4>
        </div>
        <div class="card-body p-0">
            <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text"
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
