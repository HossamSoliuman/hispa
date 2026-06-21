@extends('owner.layouts.master')
@section('title')
    {{ __('owner.sales.title') }}
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
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class=" fw-bold text-dark mb-2">{{ __('owner.sales.title') }}</h2>
        </div>

        <div class="col-md-6 col-sm-12 text-md-end text-sm-start d-flex justify-content-md-end gap-2">
            <a href="{{ route('owner.sales.create') }}" class="btn btn-outline-theme btn-equal">
                <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('owner.sales.add_new') }}
            </a>
            <a href="{{ route('owner.reports.print.sales') }}" target="_blank"
                class="btn btn-outline-info btn-border-radius">
                <i class="bi bi-printer me-1"></i> {{ __('owner.customers.reports.sales') }}
            </a>
        </div>

    </div>

    <div class="row mb-4">
        @include('owner.components.stat-card', [
            'title' => __('owner.sales.total_sales'),
            'value' => '<span id="totalTrips">0</span>',
            'icon' => 'bi bi-basket2-fill',
            'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.catch.cards.total_revenue'),
            'value' =>
                '<span id="totalRevenue">0</span> <span class="unit">' .
                view('components.riyal-icon', ['size' => 'sm'])->render() .
                '</span>',
            'icon' => 'bi bi-currency-dollar',
            'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.catch.cards.total_weight'),
            'value' => '<span id="totalWeight">0</span> <span class="unit">' . __('owner.units.kg') . '</span>',
            'icon' => 'bi bi-bar-chart-line',
            'gradient' => 'linear-gradient(135deg, #16a085, #1abc9c)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.catch.cards.avg_price_per_kg'),
            'value' =>
                '<span id="avgPricePerKg">0</span> <span class="unit">' .
                view('components.riyal-icon', ['size' => 'sm'])->render() .
                '</span>',
            'icon' => 'bi bi-graph-up-arrow',
            'gradient' => 'linear-gradient(135deg, #f39c12, #f1c40f)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header">
            <h5 class="card-title">{{ __('owner.catch.filters.title') }}</h5>
        </div>
        <div class="card-body">
            <div class="row align-items-end gy-2">

                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.catch.filters.fish_type') }}</label>
                    <select class="form-select" id="fish_id">
                        <option value="">{{ __('owner.catch.filters.all_types') }}</option>
                        @foreach ($fish as $f)
                            <option value="{{ $f->id }}">
                                {{ $f->scientific_name }}
                            </option>
                        @endforeach

                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.catch.filters.from_date') }}</label>
                    <input type="date" id="from_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.catch.filters.to_date') }}</label>
                    <input type="date" id="to_date" class="form-control">
                </div>

            </div>
            <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3">
                <button type="button" id="searchBtn" class="btn btn-success btn-sm"><i class="bi bi-search"></i>
                    {{ __('owner.catch.filters.search') }}</button>
                <button type="button" id="clearBtn" class="btn btn-light btn-sm"><i class="bi bi-x-circle"></i>
                    {{ __('owner.catch.filters.clear') }}</button>
                <a href="#" id="printReportBtn" target="_blank" class="btn btn-dark btn-sm"><i class="bi bi-printer"></i>
                    {{ __('owner.sales.print_report') }}</a>
            </div>
        </div>
    </div>


    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <!-- BEGIN #datatable -->
            <!-- BEGIN #datatable -->
            <div id="datatable" class="mb-5">
                {{-- <div class="card"> --}}
                {{-- <div class="card-body"> --}}
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('owner.sales.number') }}</th>
                            <th>{{ __('owner.sales.customer') }}</th>
                            <th>{{ __('owner.sales.total_weight') }}</th>
                            <th>{{ __('owner.sales.total_price') }}</th>
                            <th>{{ __('owner.sales.status') }}</th>
                            <th>{{ __('owner.sales.payment_status') }}</th>
                            <th>{{ __('owner.sales.date') }}</th>
                            <th>{{ __('owner.sales.payment_method') }}</th>
                            <th>{{ __('owner.sales.actions') }}</th>
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


    <script type="text/javascript">
        $(function() {
            let appLocale = '{{ app()->getLocale() }}';
            let languageOptions = {};
            if (appLocale === 'ar') {
                languageOptions = {
                    url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                };
            }
            // Check if the DataTable is already initialized and destroy it
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }



            // Initialize the DataTable
            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,

                language: languageOptions,

                ajax: {
                    url: "{{ route('owner.getSalesData') }}",
                    data: function(d) {
                        d.status = '{{ request('status') }}'; // تمرير الحالة الحالية من الرابط
                        d.fish_id = $('#fish_id').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    },
                    dataSrc: function(json) {
                        // ✅ عرض القيم في أي مكان خارج الجدول
                        $('#boat_active').text(json.boat_active_count);
                        $('#boats').text(json.boat_count);
                        // $('#trip_completed_status').text(json.trip_completed_status);
                        // $('#sales_amount').text(json.sales_amount + '{{ __('owner.generated.item_93fe61') }}');
                        let s = json.summary;
                        $('#totalTrips').text(s.total_trips);
                        $('#totalFish').text(s.total_fish_types);
                        // Update numeric values only; unit markup is provided by the stat-card markup
                        $('#totalRevenue').text(s.total_revenue.toLocaleString());
                        $('#avgRevenuePerTrip').text(s.avg_revenue_per_trip.toFixed(2));
                        $('#totalWeight').text(s.total_weight_kg.toFixed(2));
                        $('#avgWeightPerTrip').text(s.avg_weight_per_trip_kg.toFixed(2));
                        $('#avgPricePerKg').text(s.avg_price_per_kg.toFixed(2));


                        return json.data;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'number',
                        name: 'number'
                    },
                    {
                        data: 'customer',
                        name: 'customer'
                    },
                    {
                        data: 'total_weight',
                        name: 'total_weight'
                    },
                    {
                        data: 'total_price',
                        name: 'total_price'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method'
                    },
                    {
                        data: 'actions',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    },
                ],
                responsive: false, scrollX: true,

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
            $('#searchBtn').on('click', function() {
                table.draw();
            });

            $('#clearBtn').on('click', function() {
                $('#fish_id').val('');
                $('#from_date').val('');
                $('#to_date').val('');
                table.draw();
            });

            // طباعة المبيعات المعروضة حسب الفلاتر المطبقة
            $('#printReportBtn').on('click', function(e) {
                e.preventDefault();
                const params = new URLSearchParams();
                const fishId = $('#fish_id').val();
                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();
                if (fishId) params.append('fish_id', fishId);
                if (fromDate) params.append('from_date', fromDate);
                if (toDate) params.append('to_date', toDate);
                const query = params.toString();
                const url = "{{ route('owner.sales.report.print') }}" + (query ? ('?' + query) : '');
                window.open(url, '_blank');
            });
        });
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
                cancelButtonText: '{{ __('owner.swal.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('owner/sales') }}/" + recordId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('{{ __('owner.swal.deleted') }}', response.message, 'success');
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message ||
                                '{{ __('owner.swal.unexpected_error') }}';
                            Swal.fire('{{ __('owner.swal.error') }}', message, 'error');
                        }

                    });
                }
            });
        }
    </script>
@endsection
