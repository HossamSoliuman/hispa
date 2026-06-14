@extends('owner.layouts.master')

@section('title', __('owner.dalal_invoices.title'))

@section('style')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet">

    <style>
        .stat-card {
            min-height: 150px;
            height: 100%;
            border-radius: 12px;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .badge {
            padding: 6px 12px;
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">
                <i class="bi bi-receipt me-2"></i>{{ __('owner.dalal_invoices.title') }}
            </h4>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            @include('owner.components.stat-card', [
                'title' => __('owner.dalal_invoices.total_invoices'),
                'value' => '<span id="totalInvoices">' . ($totalInvoices ?? 0) . '</span>',
                'icon' => 'bi bi-receipt-cutoff',
                'gradient' => 'linear-gradient(135deg, #f59f00, #f97316)',
                'colClass' => 'col-md-3 col-sm-6 mb-3'
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.dalal_invoices.total_amount'),
                'value' => '<span id="totalAmount">' . number_format($totalAmount ?? 0, 2) . '</span> <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>',
                'icon' => 'bi bi-cash-stack',
                'gradient' => 'linear-gradient(135deg, #16a34a, #059669)',
                'colClass' => 'col-md-3 col-sm-6 mb-3'
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.dalal_invoices.pending_amount'),
                'value' => '<span id="pendingAmount">' . number_format($pendingAmount ?? 0, 2) . '</span> <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>',
                'icon' => 'bi bi-hourglass-split',
                'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
                'colClass' => 'col-md-3 col-sm-6 mb-3'
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.dalal_invoices.recent_invoices'),
                'value' => '<span id="recentInvoices">' . ($recentInvoices ?? 0) . '</span>',
                'icon' => 'bi bi-clock-history',
                'gradient' => 'linear-gradient(135deg, #0f172a, #1f2937)',
                'colClass' => 'col-md-3 col-sm-6 mb-3'
            ])
        </div>

        <!-- Filters -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('owner.dalal_invoices.filters.payment_status') }}</label>
                        <select class="form-select" id="payment_status_filter">
                            <option value="">{{ __('owner.dalal_invoices.filters.all') }}</option>
                            <option value="0">{{ __('owner.status.pending') }}</option>
                            <option value="1">{{ __('owner.status.paid') }}</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('owner.dalal_invoices.filters.from_date') }}</label>
                        <input type="date" class="form-control" id="from_date">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('owner.dalal_invoices.filters.to_date') }}</label>
                        <input type="date" class="form-control" id="to_date">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary me-2" id="searchBtn">
                            <i class="bi bi-search me-1"></i>{{ __('owner.dalal_invoices.filters.search') }}
                        </button>
                        <button type="button" class="btn btn-secondary" id="clearBtn">
                            <i class="bi bi-x-circle me-1"></i>{{ __('owner.dalal_invoices.filters.clear') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-table me-2"></i>{{ __('owner.dalal_invoices.table.title') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="invoicesTable" class="table table-bordered table-hover text-center" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('owner.dalal_invoices.table.index') }}</th>
                                <th>{{ __('owner.dalal_invoices.table.invoice_number') }}</th>
                                <th>{{ __('owner.dalal_invoices.table.dalal_name') }}</th>
                                <th>{{ __('owner.dalal_invoices.table.boat_name') }}</th>
                                <th>{{ __('owner.dalal_invoices.table.customer') }}</th>
                                <th>{{ __('owner.dalal_invoices.table.total_price') }}</th>
                                <th>{{ __('owner.dalal_invoices.table.commission') }}</th>
                                <th>{{ __('owner.dalal_invoices.table.labor') }}</th>
                                <th>{{ __('owner.dalal_invoices.table.net_owner') }}</th>
                                <th>{{ __('owner.dalal_invoices.table.sale_date') }}</th>
                                <th>{{ __('owner.dalal_invoices.table.sent_date') }}</th>
                                <th>{{ __('owner.dalal_invoices.table.payment_status') }}</th>
                                <th>{{ __('owner.dalal_invoices.table.actions') }}</th>
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
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#invoicesTable').DataTable({
                processing: true,
                serverSide: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                },
                ajax: {
                    url: "{{ route('owner.getDalalInvoiceData') }}",
                    data: function(d) {
                        d.payment_status = $('#payment_status_filter').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'invoice_number', name: 'number' },
                    { data: 'dalal_name', name: 'seller.name' },
                    { data: 'boat_name', name: 'trip.boat.name' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'total_price', name: 'total_price' },
                    { data: 'commission', name: 'commission_amount' },
                    { data: 'labor', name: 'labor_amount' },
                    { data: 'net_owner_amount', name: 'net_owner_amount' },
                    { data: 'sale_date', name: 'sale_datetime' },
                    { data: 'sent_date', name: 'invoice_sent_at' },
                    { data: 'payment_status', name: 'payment_status', orderable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[10, 'desc']], // Sort by sent_date descending
                responsive: false, scrollX: true
            });

            // Search button
            $('#searchBtn').on('click', function() {
                table.draw();
            });

            // Clear filters button
            $('#clearBtn').on('click', function() {
                $('#payment_status_filter').val('');
                $('#from_date').val('');
                $('#to_date').val('');
                table.draw();
            });

            // Filter on enter key
            $('#from_date, #to_date').on('change', function() {
                table.draw();
            });

            $('#payment_status_filter').on('change', function() {
                table.draw();
            });
        });
    </script>
@endsection
