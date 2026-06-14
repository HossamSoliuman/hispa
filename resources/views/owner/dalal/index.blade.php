@extends('owner.layouts.master')

@section('title')
{{ __('owner.dalal.title') }}
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

        /* {{ __('owner.generated.item_ed06b0') }} */
        .small-text th,
        .small-text td {
            font-size: 12px; /* {{ __('owner.generated.or') }} 13px {{ __('owner.generated.item_4cc9e8') }} */
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
<div class="container-fluid py-4">


    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-truck me-2"></i>{{ __('owner.dalal.title') }}</h4>
{{--        <div>--}}
{{--            <button class="btn btn-dark me-2"><i class="bi bi-upload me-1"></i> {{ __('owner.generated.export') }}CSV</button>--}}
{{--            <button class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i> {{ __('owner.generated.print_report') }}</button>--}}
{{--        </div>--}}
    </div>



    <div class="row mb-4">
        @include('owner.components.stat-card', [
            'title' => __('owner.dalal.cards.total'),
            'value' => '<span id="totalDalals">' . ($totalDalals ?? 0) . '</span>',
            'icon' => 'bi bi-people-fill',
            'gradient' => 'linear-gradient(135deg, #2980b9, #3498db);',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.dalal.cards.amount_due'),
            'value' => '<span id="amount_due">' . number_format($amount_due ?? 0, 2) . '</span> <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>',
            'icon' => 'bi bi-exclamation-circle',
            'gradient' => 'linear-gradient(135deg, #c0392b, #e74c3c);',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.dalal.cards.paid'),
            'value' => '<span id="paidAmount">' . number_format($paid_amount ?? 0, 2) . '</span> <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>',
            'icon' => 'bi bi-cash-coin',
            'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71);',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.dalal.cards.unpaid'),
            'value' => '<span id="unpaidAmount">' . number_format($unpaid_amount ?? 0, 2) . '</span> <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>',
            'icon' => 'bi bi-wallet2',
            'gradient' => 'linear-gradient(135deg, #8e44ad, #9b59b6);',
            'colClass' => 'col-md-3 col-sm-6 mb-3'
        ])
    </div>

    <style>
        /* Ensure riyal SVG is small and aligned in stat-cards */
        .stat-card-hover .stat-value .unit svg { width: 14px !important; height: 14px !important; vertical-align: middle; margin-left: 4px; }
        .stat-card-hover .stat-value { display: flex; align-items: center; gap: 6px; }
    </style>




{{--        <div class="col">--}}
{{--            <div class="card bg-warning text-center shadow-sm border-0">--}}
{{--                <div class="card-body">--}}
{{--                    <h6 class="text-white"><i class="bi bi-wallet2 me-1"></i>{{ __('owner.generated.partially_paid_amounts') }}</h6>--}}
{{--                    <h4 class="fw-bold text-white" id="partialPaidAmount">0</h4>--}}
{{--                    <small class="text-white">{{ __('owner.generated.partially_settled') }}</small>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--  </div>--}}

    <!-- Filters & Add Button -->
{{--    <div class="d-flex justify-content-between align-items-center mb-3">--}}
{{--        <h5 class="fw-bold mb-0"><i class="bi bi-database-fill-gear me-2"></i>{{ __('owner.generated.brokers_database') }}</h5>--}}
{{--        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addVendorModal">--}}
{{--            <i class="bi bi-plus-circle me-1"></i>إضافة دلال جديد--}}
{{--        </button>--}}
{{--    </div>--}}

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        @include('owner.partials._card_arrow')
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-2">
                    <label class="form-label">{{ __('owner.dalal.filters.status') }}</label>
                    <select class="form-select" id="payment_status_filter">
                        <option value="">{{ __('owner.dalal.filters.options.all') }}</option>
                        <option value="paid">{{ __('owner.dalal.filters.options.paid') }}</option>
                        <option value="partial">{{ __('owner.dalal.filters.options.partial') }}</option>
                        <option value="unpaid">{{ __('owner.dalal.filters.options.unpaid') }}</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('owner.dalal.filters.from_date') }}</label>
                    <input type="date" class="form-control" id="start_date_filter">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('owner.dalal.filters.to_date') }}</label>
                    <input type="date" class="form-control" id="end_date_filter">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-success w-100" id="searchBtn">{{ __('owner.dalal.filters.search') }}</button>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-light w-100" id="clearFiltersBtn">{{ __('owner.dalal.filters.clear') }}</button>
                </div>

            </div>
        </div>
    </div>


    <ul class="nav nav-tabs mb-4" id="vendorTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="vendors-tab" data-bs-toggle="tab" data-bs-target="#vendors" type="button" role="tab" aria-controls="vendors" aria-selected="true">
                <i class="bi bi-building me-1"></i> {{ __('owner.dalal.tabs.brokers') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab" aria-controls="analytics" aria-selected="false">
                <i class="bi bi-graph-up-arrow me-1"></i> {{ __('owner.dalal.tabs.analytics') }}
            </button>
        </li>
{{--        <li class="nav-item" role="presentation">--}}
{{--            <button class="nav-link" id="terms-tab" data-bs-toggle="tab" data-bs-target="#terms" type="button" role="tab" aria-controls="terms" aria-selected="false">--}}
{{--                <i class="bi bi-clock-history me-1"></i> شروط الدفع--}}
{{--            </button>--}}
{{--        </li>--}}
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" type="button" role="tab" aria-controls="performance" aria-selected="false">
                <i class="bi bi-bar-chart-line-fill me-1"></i> {{ __('owner.dalal.tabs.performance') }}
            </button>
        </li>
    </ul>

    <div class="tab-content" id="vendorTabsContent">
        <div class="tab-pane fade show active" id="vendors" role="tabpanel" aria-labelledby="vendors-tab">
            <div class="card shadow-sm border-0">
                @include('owner.partials._card_arrow')
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                            <thead>

                            <tr>
                                <th>{{ __('owner.dalal.table.index') }}</th>
                                <th>{{ __('owner.dalal.table.name') }}</th>
                                <th>{{ __('owner.dalal.table.contact') }}</th>
{{--                                <th>{{ __('owner.generated.items_count') }}</th>--}}
{{--                                <th>{{ __('owner.generated.total_stock') }}</th>--}}
{{--                                <th>{{ __('owner.generated.remaining_items_count') }}</th>--}}
{{--                                <th>{{ __('owner.generated.remaining_stock') }}</th>--}}
                                <th>{{ __('owner.dalal.table.sales_count') }}</th>
                                <th>{{ __('owner.dalal.table.total_sales') }}</th>
                                <th>{{ __('owner.dalal.table.dalal_commission_rate') }}</th>
                                <th>{{ __('owner.dalal.table.labor_commission_rate') }}</th>
                                <th>{{ __('owner.dalal.table.total_dalal_commission') }}</th>
                                <th>{{ __('owner.dalal.table.total_due') }}</th>
                                <th>{{ __('owner.dalal.table.total_paid') }}</th>
                                <th>{{ __('owner.dalal.table.date') }}</th>
                                <th>{{ __('owner.dalal.table.payment_status') }}</th>
                                <th>{{ __('owner.dalal.table.actions') }}</th>

                            </tr>
                            </thead>
                            <tbody>



                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="analytics" role="tabpanel" aria-labelledby="analytics-tab">
            <div class="row g-3 mb-4">
                    <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        @include('owner.partials._card_arrow')
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill me-2"></i>{{ __('owner.dalal.analytics.by_sales') }}</h6>
                            <canvas id="DalalByChart" height="200"></canvas>
                        </div>
                    </div>
                </div>



                    <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        @include('owner.partials._card_arrow')
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill me-2"></i>{{ __('owner.dalal.analytics.top_brokers_by_amount') }}</h6>
                            <canvas id="topDalalChart" height="280" width="600"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="terms" role="tabpanel" aria-labelledby="terms-tab">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>{{ __('owner.dalal.terms.title') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('owner.dalal.terms.table.term') }}</th>
                                    <th>{{ __('owner.dalal.terms.table.brokers_count') }}</th>
                                    <th>{{ __('owner.dalal.terms.table.percentage') }}</th>
                                    <th>{{ __('owner.dalal.terms.table.avg_credit_limit') }}</th>
                                    <th>{{ __('owner.dalal.terms.table.total_due_balance') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Net 30 Days</td>
                                    <td>1</td>
                                    <td>100.0%</td>
                                    <td>{{ __('owner.generated.amount_500000_sar') }}</td>
                                    <td>{{ __('owner.generated.amount_45000_sar') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="performance" class="tab-pane fade" role="tabpanel" aria-labelledby="performance-tab">
        <div class="row g-3 mb-4">

            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="bi bi-award-fill me-2"></i>{{ __('owner.dalal.performance.top_due') }}</h6>
                            <h5 class="fw-bold text-primary" id="topDalalName">{{ __('owner.loading') }}</h5>
                            <p class="mb-1"><strong>{{ __('owner.dalal.performance.due_label') }}</strong> <span id="topDalalDue">0</span> {{ __('owner.units.sar') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="bi bi-lightning-fill me-2"></i>{{ __('owner.dalal.performance.most_active') }}</h6>
                            <h5 class="fw-bold text-success" id="mostActiveDalalName">{{ __('owner.loading') }}</h5>
                            <p class="mb-0"><strong>{{ __('owner.dalal.performance.operations_label') }}:</strong> <span id="mostActiveDalalCount">0</span></p>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-line-fill me-2"></i>{{ __('owner.dalal.performance.insights') }}</h6>
                        <div class="row g-3 text-center">
                            <div class="col-md-3">
                                <div class="border rounded py-3">
                                        <h6 class="fw-bold mb-1">{{ __('owner.dalal.performance.total_active') }}</h6>
                                        <p class="mb-0" id="totalActiveDalals">{{ __('owner.dalal.performance.active_summary', ['active' => 0, 'total' => 0]) }}</p>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="border rounded py-3">
                                    <h6 class="fw-bold mb-1">{{ __('owner.dalal.performance.avg_sale') }}</h6>
                                    <p class="mb-0"><span id="avgSaleAmount">0</span> {{ __('owner.units.sar') }} {{ __('owner.dalal.performance.per_sale') }}</p>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="border rounded py-3">
                                    <h6 class="fw-bold mb-1">{{ __('owner.dalal.performance.growth') }}</h6>
                                    <p class="mb-0"><span id="newDalalsThisMonth">0</span> {{ __('owner.dalal.performance.new_brokers_month') }}</p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-3 text-center">
                            <div class="col-md-6">
                                <div class="border rounded py-3">
                                    <h6 class="fw-bold mb-1">{{ __('owner.dalal.performance.with_due') }}</h6>
                                    <p class="mb-0"><span id="dalalsWithDueBalance">0</span> {{ __('owner.dalal.performance.broker_unit') }}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Add Broker Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark ">
                <h5 class="modal-title text-white" id="addVendorModalLabel"><i class="bi bi-person-plus-fill me-2"></i>{{ __('owner.dalal.modal.add_title') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('owner.actions.close') }}"></button>
            </div>
            <div class="modal-body">
                <form>
                    <!-- Basic Info -->
                    <h6 class="fw-bold mb-3">{{ __('owner.dalal.modal.basic_info') }}</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal.modal.form.name') }} *</label>
                            <input type="text" class="form-control" placeholder="{{ __('owner.dalal.modal.form.name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal.modal.form.contact_name') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('owner.dalal.modal.form.contact_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal.modal.form.email') }}</label>
                            <input type="email" class="form-control" placeholder="vendor@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal.modal.form.phone') }}</label>
                            <input type="text" class="form-control" placeholder="+966501234567">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal.modal.form.category') }}</label>
                            <select class="form-select">
                                <option selected>{{ __('owner.dalal.modal.form.category') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal.modal.form.tax_number') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('owner.dalal.modal.form.tax_number') }}">
                        </div>
                    </div>

                    <!-- Address -->
                    <h6 class="fw-bold mb-3">{{ __('owner.dalal.modal.address') }}</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.dalal.modal.form.address') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('owner.dalal.modal.form.address') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.dalal.modal.form.city') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('owner.dalal.modal.form.city') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.dalal.modal.form.country') }}</label>
                            <input type="text" class="form-control" value="{{ __('owner.dalal.modal.form.country') }}">
                        </div>
                    </div>

                    <!-- Payment Terms -->
                    <h6 class="fw-bold mb-3">{{ __('owner.dalal.modal.payment_terms') }}</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.dalal.modal.form.term') }}</label>
                            <input type="text" class="form-control" placeholder="Net 30 Days">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.dalal.modal.form.preferred_payment') }}</label>
                            <select class="form-select">
                                <option selected>{{ __('owner.dalal.modal.form.preferred_payment') }}</option>
                                <option>Bank Transfer</option>
                                <option>Check</option>
                                <option>Cash</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.dalal.modal.form.credit_limit') }}</label>
                            <input type="number" step="0.01" class="form-control" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Banking -->
                    <h6 class="fw-bold mb-3">{{ __('owner.dalal.modal.banking') }}</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.dalal.modal.form.bank_name') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('owner.dalal.modal.form.bank_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.dalal.modal.form.bank_account') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('owner.dalal.modal.form.bank_account') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.dalal.modal.form.iban') }}</label>
                            <input type="text" class="form-control" placeholder="SA1234567890123456789012">
                        </div>
                    </div>
                    <!-- Notes -->
                    <div class="mb-4">
                        <label class="form-label">{{ __('owner.dalal.modal.notes') }}</label>
                        <textarea class="form-control" rows="3" placeholder="{{ __('owner.dalal.modal.notes') }}"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('owner.dalal.modal.buttons.cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('owner.dalal.modal.buttons.add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


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

                language: {
                    url: "{{asset('dashboard/assets/js/ar.json')}}?v={{ time() }}"

                },

                ajax: {
                    url: "{{ route('owner.getDalalData') }}",
                    data: function(d) {
                        d.payment_status = $('#payment_status_filter').val();
                        d.from_date = $('#start_date_filter').val();
                        d.to_date = $('#end_date_filter').val();

                    },
                    dataSrc: function (json) {
                        // تحديث العناصر الإحصائية في الصفحة (فقط تحديث الأرقام، لا نغير DOM للوحدة/رمز العملة)
                        $('#totalDalals').text(json.total_dalals);
                        $('#activeDalals').text(json.active_dalals);

                        // Helper: robustly parse numeric value (strip commas/currency) and format
                        function fmtMoney(val) {
                            var str = (val === null || val === undefined) ? '' : String(val);
                            // remove everything except digits, dot, minus
                            var cleaned = str.replace(/[^0-9.\-]+/g, '');
                            var n = parseFloat(cleaned);
                            if (isNaN(n)) n = 0;
                            return n.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }

                        $('#amount_due').text(fmtMoney(json.amount_due));
                        $('#paidAmount').text(fmtMoney(json.paid_amount));
                        $('#unpaidAmount').text(fmtMoney(json.unpaid_amount));

                        // ترجع فقط بيانات الجدول
                        return json.data;
                    }
                },



                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', defaultContent: '' },
                    { data: 'dalal_name', name: 'dalal_name', defaultContent: '-' },
                    { data: 'contact', name: 'contact', defaultContent: '-' },
                    // { data: 'fish_count', name: 'fish_count', defaultContent: '0' },
                    // { data: 'total_stock_weight', name: 'total_stock_weight', defaultContent: '0.00' },
                    // { data: 'remaining_fish_count', name: 'remaining_fish_count', defaultContent: '0' },
                    // { data: 'remaining_stock_weight', name: 'remaining_stock_weight', defaultContent: '0.00' },
                    { data: 'total_sales', name: 'total_sales', defaultContent: '0' },
                    { data: 'total_sales_amount', name: 'total_sales_amount', defaultContent: '0.00' },
                    { data: 'commission_rate', name: 'dalal_commission_rate', defaultContent: '0.00%' },
                    { data: 'labor_rate', name: 'labor_commission_rate', defaultContent: '0.00%' },
                    { data: 'total_dalal_commission', name: 'total_dalal_commission', defaultContent: '0.00' },
                    { data: 'total_owner_amount', name: 'total_owner_amount', defaultContent: '0.00' },
                    { data: 'total_paid_amount', name: 'total_paid_amount', defaultContent: '0.00' },
                    { data: 'date', name: 'date', defaultContent: '0.00' },
                    { data: 'payment_status', name: 'payment_status', orderable: false, searchable: false, defaultContent: '' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, defaultContent: '' },
                ],
                responsive: false, scrollX: true,

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#clearFiltersBtn').on('click', function() {
                $('#payment_status_filter').val('');
                $('#start_date_filter').val('');
                $('#end_date_filter').val('');
                table.ajax.reload();
            });
        });
    </script>
<script>
    let vendorsChartsInitialized = false;

    function initVendorsCharts() {
        if (vendorsChartsInitialized) return;
        vendorsChartsInitialized = true;

        $.get("{{ route('owner.top-dalals-chart') }}", function (res) {
            new Chart(document.getElementById('DalalByChart'), {
                type: 'pie',
                data: {
                    labels: res.labels,
                    datasets: [{
                        data: res.salesAmounts,
                        backgroundColor: [
                            '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1'
                        ],
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false
                }
            });
        });



        $.get("{{ route('owner.top-dalals-bar-chart') }}", function(res) {
            new Chart(document.getElementById('topDalalChart'), {
                type: 'bar',
                data: {
                    labels: res.labels,
                    datasets: [{
                        label: '{{ __('owner.generated.item_0e58d1') }}',
                        data: res.data,
                        backgroundColor: ['#dc3545', '#198754']
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

        });
    }

    document.getElementById('analytics-tab').addEventListener('shown.bs.tab', function() {
        initVendorsCharts();
    });

    function fetchPerformanceStats() {
        $.ajax({
            url: "{{ route('owner.dalal-performance.stats') }}",
            method: 'GET',
            success: function(data) {
                $('#topDalalName').text(data.topDalalName);
                $('#topDalalDue').text(Number(data.topDalalDue).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#mostActiveDalalName').text(data.mostActiveDalalName);
                $('#mostActiveDalalCount').text(data.mostActiveDalalCount);
                $('#totalActiveDalals').text(data.totalActiveDalals + '{{ __('owner.generated.item_99fb92') }}' + data.totalActiveDalals);
                $('#avgSaleAmount').text(Number(data.avgSaleAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#newDalalsThisMonth').text(data.newDalalsThisMonth);
                $('#dalalsWithDueBalance').text(data.dalalsWithDueBalance);

            },
            error: function() {
                alert('{{ __('owner.swal.unexpected_error') }}');
            }
        });
    }

    // استدعاء المعلومة عند تحميل الصفحة أو عند فتح التبويب
    fetchPerformanceStats();

    // لو عندك تبويب وتريد تحمل البيانات عند فتحه فقط
    $('#performance-tab').on('shown.bs.tab', function () {
        fetchPerformanceStats();
    });
</script>
@endsection
