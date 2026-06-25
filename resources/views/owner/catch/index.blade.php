@extends('owner.layouts.master')

@section('title', __('owner.catch.page_title'))
@section('css')

    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css') }}" rel="stylesheet">
    <style>
        .stat-card-hover .stat-value .unit svg {
            width: 14px !important;
            height: 14px !important;
        }

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

    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-2">{{ __('owner.catch.manage_title') }}</h2>
        </div>
        {{-- <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
        <a href="{{route('owner.catch.create')}}" class="btn btn-black btn-sm">
            <i class="bi bi-plus"></i> {{__('owner.catch.add_catch')}}
        </a>
    </div> --}}
    </div>

    <div class="row mb-4">
        @include('owner.components.stat-card', [
            'title' => __('owner.catch.cards.total_catch'),
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

                <div class="col-md-2">
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
                <div class="col-md-2">
                    <label class="form-label">{{ __('owner.catch.filters.boat') }}</label>
                    <select class="form-select" id="boat_id">
                        <option value="">{{ __('owner.catch.filters.all_boats') }}</option>
                        @foreach ($boats as $boat)
                            <option value="{{ $boat->id }}">{{ $boat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('owner.catch.filters.from_date') }}</label>
                    <input type="date" class="form-control" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('owner.catch.filters.to_date') }}</label>
                    <input type="date" class="form-control" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('owner.catch.filters.has_catch') }}</label>
                    <select id="has_catch" class="form-control">
                        <option value="">{{ __('owner.catch.all') }}</option>
                        <option value="1">{{ __('owner.catch.has_Catch') }}</option>
                        <option value="0">{{ __('owner.catch.no_Catch') }}</option>
                    </select>
                </div>

            </div>
            <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3">
                <button type="button" id="searchBtn" class="btn btn-success btn-sm"><i class="bi bi-search"></i>
                    {{ __('owner.catch.filters.search') }}</button>
                <button type="button" id="clearBtn" class="btn btn-light btn-sm"><i class="bi bi-x-circle"></i>
                    {{ __('owner.catch.filters.clear') }}</button>
                <a href="#" id="printReportBtn" target="_blank" class="btn btn-dark btn-sm"><i class="bi bi-printer"></i>
                    {{ __('owner.catch.filters.print_report') }}</a>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mt-4 d-flex" id="catchTabs" role="tablist">
        <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link w-100 active" id="records-tab" data-bs-toggle="tab" data-bs-target="#records"
                type="button" role="tab">
                {{ __('owner.catch.tabs.records') }}
            </button>
        </li>
        <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link w-100" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics"
                type="button" role="tab">
                {{ __('owner.catch.tabs.analytics') }}
            </button>
        </li>
        <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link w-100" id="species-tab" data-bs-toggle="tab" data-bs-target="#species" type="button"
                role="tab">
                {{ __('owner.catch.tabs.species') }}
            </button>
        </li>
        <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link w-100" id="trends-tab" data-bs-toggle="tab" data-bs-target="#trends" type="button"
                role="tab">
                {{ __('owner.catch.tabs.trends') }}
            </button>
        </li>
    </ul>


    <!-- Tabs Content -->
    <div class="tab-content pt-3" id="catchTabsContent">
        <div class="tab-pane fade show active" id="records" role="tabpanel">
            @include('owner.catch.partials.records')
        </div>

        <!-- Tab: Analytics -->
        <div class="tab-pane fade" id="analytics" role="tabpanel">
            <div class="row g-4 mt-3">

                <!-- Revenue by Species -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">{{ __('owner.catch.charts.revenue_by_species') }}</h6>
                            <div style="height: 280px;">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Weight by Species -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">{{ __('owner.catch.charts.weight_by_species') }}</h6>
                            <div style="height: 280px;">
                                <canvas id="weightChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Performance -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">{{ __('owner.catch.charts.monthly_performance') }}</h6>
                            <div style="height: 380px;">
                                <canvas id="monthlyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <div class="tab-pane fade" id="species" role="tabpanel">
            @include('owner.catch.partials.species')
        </div>

        <div class="tab-pane fade" id="trends" role="tabpanel">
            @include('owner.catch.partials.trends')
        </div>
    </div>

    <!-- Modal: Add Catch -->
    <div class="modal fade" id="catchModal" tabindex="-1" aria-labelledby="catchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="catchModalLabel">{{ __('owner.generated.add_new_catch') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('owner.generated.btn_close_modal') }}"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.sales.date') }}</label>
                                <input type="date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.assets.type') }}</label>
                                <input type="text" class="form-control" placeholder="Hamour">
                            </div>
                            <div class="col-md-4">
                                <label
                                    class="form-label">{{ __('owner.expenses.print.quantity') }}({{ __('owner.catch.cards.total_fish') }})</label>
                                <input type="number" class="form-control" placeholder="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.assets.weight') }}(lbs)</label>
                                <input type="number" class="form-control" placeholder="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.generated.price_per_lb') }}</label>
                                <input type="number" class="form-control" placeholder="SAR">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.generated.location') }}</label>
                                <input type="text" class="form-control" name="location"
                                    placeholder="{{ __('owner.generated.placeholder_coordinates') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.catch.filters.boat') }}</label>
                                <select class="form-select">
                                    <option selected disabled>{{ __('owner.payrolls.create.choose_boat') }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('owner.expenses.show.notes') }}</label>
                                <textarea class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('owner.generated.save_catch') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


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
                    url: "{{ route('owner.getCatchData') }}",
                    data: function(d) {
                        d.fish_id = $('#fish_id').val();
                        d.boat_id = $('#boat_id').val();
                        d.has_catch = $('#has_catch').val();
                        d.from_date = $('input[type=date]').eq(0).val(); // أول input تاريخ
                        d.to_date = $('input[type=date]').eq(1).val(); // ثاني input تاريخ

                    },
                    dataSrc: function(json) {
                        // تحديث الكروت من الإحصائيات
                        let s = json.summary;
                        $('#totalTrips').text(s.total_trips);
                        $('#totalFish').text(s.total_fish_types);
                        // Update numeric values only; unit markup is provided by the stat-card markup
                        $('#totalRevenue').text(s.total_revenue.toLocaleString());
                        $('#avgRevenuePerTrip').text(s.avg_revenue_per_trip.toFixed(2));
                        $('#totalWeight').text(s.total_weight_kg.toFixed(2));
                        $('#avgWeightPerTrip').text(s.avg_weight_per_trip_kg.toFixed(2));
                        $('#avgPricePerKg').text(s.avg_price_per_kg.toFixed(2));

                        return json.data; // مهم لإرجاع بيانات الجدول
                    }
                },



                columns: [{
                        data: 'trip',
                        name: 'trip'
                    },
                    {
                        data: 'boat',
                        name: 'boat',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_weight',
                        name: 'weight_kg'
                    },
                    {
                        data: 'total_amount',
                        name: 'price_per_kg'
                    },
                    {
                        data: 'start_date',
                        name: 'date'
                    },
                    {
                        data: 'end_date',
                        name: 'date'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                responsive: false, scrollX: true,

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
            $('.btn-success').on('click', function() {
                table.ajax.reload();
            });

            // زر مسح الكل يعيد تعيين الفلاتر ويحدث الجدول
            $('.btn-light').on('click', function() {
                $('#fish_id').val('');
                $('#boat_id').val('');
                $('input[type=date]').val('');
                table.ajax.reload();
            });

            // طباعة تقرير بالمصيد المعروض حسب الفلاتر المطبقة
            $('#printReportBtn').on('click', function(e) {
                e.preventDefault();
                const params = new URLSearchParams();
                const fishId = $('#fish_id').val();
                const boatId = $('#boat_id').val();
                const hasCatch = $('#has_catch').val();
                const fromDate = $('input[type=date]').eq(0).val();
                const toDate = $('input[type=date]').eq(1).val();
                if (fishId) params.append('fish_id', fishId);
                if (boatId) params.append('boat_id', boatId);
                if (hasCatch) params.append('has_catch', hasCatch);
                if (fromDate) params.append('from_date', fromDate);
                if (toDate) params.append('to_date', toDate);
                const query = params.toString();
                const url = "{{ route('owner.printCatchesReport') }}" + (query ? ('?' + query) : '');
                window.open(url, '_blank');
            });
        });
    </script>
    <script type="text/javascript">
        $(function() {
            // Check if the DataTable is already initialized and destroy it
            if ($.fn.DataTable.isDataTable('#dataFishPerformance')) {
                $('#dataFishPerformance').DataTable().destroy();
            }


            // Initialize the DataTable
            var table = $('#dataFishPerformance').DataTable({
                processing: true,
                serverSide: true,

                language: {
                    url: "{{ asset('dashboard/assets/js/ar.json') }}?v={{ time() }}"

                },

                ajax: {
                    url: "{{ route('owner.getFishStats') }}",
                    data: function(d) {
                        d.fish_id = $('#fish_id').val();
                        d.boat_id = $('#boat_id').val();
                        d.from_date = $('input[type=date]').eq(0).val(); // أول input تاريخ
                        d.to_date = $('input[type=date]').eq(1).val(); // ثاني input تاريخ

                    },

                },



                columns: [{
                        data: 'date',
                        title: '{{ __('owner.generated.item_8456f2') }}'
                    }, // <-- هذا هو سبب الخطأ
                    {
                        data: 'type',
                        title: '{{ __('owner.generated.item_a70b01') }}'
                    },
                    {
                        data: 'catch_count',
                        title: '{{ __('owner.generated.catch_count') }}'
                    },
                    {
                        data: 'total_weight_kg',
                        title: '{{ __('owner.generated.item_8d0fa7') }}'
                    },
                    {
                        data: 'avg_price_per_kg',
                        title: '{{ __('owner.generated.item_3817c5') }}'
                    },
                    {
                        data: 'total_revenue',
                        title: '{{ __('owner.generated.item_33a72b') }}'
                    },
                    {
                        data: 'performance',
                        title: '{{ __('owner.generated.item_74b996') }}'
                    }
                ],
                responsive: false, scrollX: true,

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
            $('.btn-success').on('click', function() {
                table.ajax.reload();
            });

            // زر مسح الكل يعيد تعيين الفلاتر ويحدث الجدول
            $('.btn-light').on('click', function() {
                $('#fish_id').val('');
                $('#boat_id').val('');
                $('input[type=date]').val('');
                table.ajax.reload();
            });
        });
    </script>
    <script>
        const catchChartLocale = '{{ app()->getLocale() === 'ar' ? 'ar-SA' : 'en-US' }}';

        function formatChartMonth(yearMonth) {
            const [year, month] = yearMonth.split('-');
            return new Intl.DateTimeFormat(catchChartLocale, { year: 'numeric', month: 'long' })
                .format(new Date(year, month - 1, 1));
        }

        // Revenue by Species - Doughnut
        fetch("{{ route('owner.getRevenueBySpecies') }}")
            .then(response => response.json())
            .then(data => {
                new Chart(document.getElementById('revenueChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data.map(item => item.fish_name),
                        datasets: [{
                            data: data.map(item => item.total_revenue),
                            backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#dc3545', '#6f42c1'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            });

        // Weight by Species - Bar
        fetch("{{ route('owner.getWeightBySpecies') }}")
            .then(response => response.json())
            .then(data => {
                new Chart(document.getElementById('weightChart'), {
                    type: 'bar',
                    data: {
                        labels: data.map(item => item.fish_name),
                        datasets: [{
                            label: '{{ __('owner.generated.item_7d3c47') }}',
                            data: data.map(item => item.total_weight_lb),
                            backgroundColor: '#ffc107'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            });

        // Monthly Performance - Mixed Bar/Line
        fetch("{{ route('owner.getMonthlyPerformance') }}")
            .then(response => response.json())
            .then(data => {
                new Chart(document.getElementById('monthlyChart'), {
                    type: 'bar',
                    data: {
                        labels: data.map(item => formatChartMonth(item.month)),
                        datasets: [{
                                label: '{{ __('owner.generated.catch_count') }}',
                                data: data.map(item => item.catch_count),
                                backgroundColor: '#0dcaf0',
                                yAxisID: 'y',
                            },
                            {
                                label: '{{ __('owner.generated.item_f1296c') }}',
                                data: data.map(item => item.total_revenue),
                                backgroundColor: '#198754',
                                yAxisID: 'y1',
                            },
                            {
                                label: '{{ __('owner.generated.item_7d3c47') }}',
                                data: data.map(item => item.total_weight_lb),
                                type: 'line',
                                borderColor: '#dc3545',
                                backgroundColor: '#dc3545',
                                tension: 0.4,
                                yAxisID: 'y2',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        stacked: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                position: 'left',
                                title: { display: true, text: '{{ __('owner.generated.catch_count') }}' }
                            },
                            y1: {
                                type: 'linear',
                                position: 'right',
                                grid: { drawOnChartArea: false },
                                title: { display: true, text: '{{ __('owner.generated.item_cd39bd') }}' }
                            },
                            y2: {
                                type: 'linear',
                                position: 'right',
                                grid: { drawOnChartArea: false },
                                title: { display: true, text: '{{ __('owner.generated.item_67e19f') }}' }
                            }
                        }
                    }
                });
            });
    </script>

    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('owner.getStatsSummary') }}", // عدل حسب اسم الراوت اللي رجعنا فيه البيانات
                method: "GET",
                success: function(data) {
                    // أفضل نوع أداءً
                    $('#bestFishName').text(data.best_fish.name);
                    $('#bestFishRevenue').text(data.best_fish.revenue);
                    $('#bestFishCatchCount').text(data.best_fish.catch_count);
                    $('#bestFishWeight').text(data.best_fish.weight_kg);
                    $('#bestFishPerformance').html(data.best_fish.performance);

                    // الموقع الأكثر إنتاجًا
                    $('#topPortName').text(data.top_port.name);
                    $('#topPortTrips').text(data.top_port.trip_count);

                    // أداء القوارب
                    let boatsHtml = '';
                    data.boats_performance.boats.forEach(function(boat) {
                        boatsHtml += `<li>${boat.name}: ${boat.trips} </li>`;
                    });
                    $('#boatsPerformance').html(boatsHtml);
                    $('#bestBoatName').text(data.boats_performance.best_boat.name);
                    $('#bestBoatTrips').text(data.boats_performance.best_boat.trips);

                    // مؤشرات الأداء
                    $('#totalTrips').text(data.performance_indicators.total_trips);
                    $('#activeBoats').text(data.performance_indicators.active_boats);
                    $('#activePorts').text(data.performance_indicators.active_ports);
                    $('#distinctFishTypes').text(data.performance_indicators.distinct_fish_types);
                    $('#totalRevenue').text(data.performance_indicators.total_revenue);
                    $('#totalWeightKg').text(data.performance_indicators.total_weight_kg);
                    $('#revenuePerTrip').text(data.performance_indicators.revenue_per_trip);
                    $('#weightPerTrip').text(data.performance_indicators.weight_per_trip_kg);
                    $('#avgPricePerKg').text(data.performance_indicators.avg_price_per_kg);
                },
                error: function(xhr) {
                    alert('{{ __('owner.generated.error_fetching_data') }}');
                }
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
                        url: "{{ route('owner.catch.destroy', 'RECORD_ID') }}".replace('RECORD_ID', recordId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('{{ __('owner.swal.deleted') }}', response.message, 'success');
                            $('#datatableDefault').DataTable().ajax.reload(null, false);
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
