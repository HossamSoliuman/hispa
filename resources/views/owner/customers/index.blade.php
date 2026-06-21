@extends('owner.layouts.master')

@section('title')
    {{ __('owner.generated.item_a6f78f') }}
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

        .stat-card {
            min-height: 150px;
            height: 100%;
            border-radius: 12px;
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 5px;
        }

        /* Ensure embedded SVG currency icon displays correctly inside stat cards */
        .stat-card-hover .stat-value .unit {
            display: inline-flex;
            align-items: baseline;
            gap: .25rem;
        }

        .stat-card-hover .stat-value .unit svg {
            width: 16px;
            height: 16px;
            vertical-align: middle;
            fill: currentColor;
        }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-2">{{ __('owner.customers.title') }}</h2>
            {{-- <p class="text-muted mb-0">{{ __('owner.customers.subtitle') }}</p> --}}
        </div>

        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <div class="d-flex flex-wrap justify-content-md-end justify-content-start align-items-center gap-2">
                <button class="btn btn-outline-theme btn-equal" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('owner.customers.add_button') }}
                </button>
                <a href="{{ route('owner.reports.print.customers') }}" target="_blank"
                    class="btn btn-outline-info btn-border-radius">
                    <i class="bi bi-printer me-1"></i> {{ __('owner.customers.reports.customers') }}
                </a>
            </div>
        </div>
    </div>
    @include('owner.customers._models')
    <div class="row mb-4">
        @include('owner.components.stat-card', [
            'title' => __('owner.customers.cards.total'),
            'value' => '<span id="customerCount">0</span>',
            'icon' => 'bi bi-people',
            'gradient' => 'linear-gradient(135deg, #f59f00, #f97316)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.customers.cards.active'),
            'value' => '<span id="customerCountActive">0</span>',
            'icon' => 'bi bi-person-check',
            'gradient' => 'linear-gradient(135deg, #16a34a, #059669)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.customers.cards.total_sales'),
            'value' =>
                '<span id="totalSales">0</span> <span class="unit">' .
                view('components.riyal-icon', ['size' => 'sm'])->render() .
                '</span>',
            'icon' => 'bi bi-cash-stack',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.customers.cards.total_orders'),
            'value' => '<span id="totalOrders">0</span>',
            'icon' => 'bi bi-receipt',
            'gradient' => 'linear-gradient(135deg, #0f172a, #1f2937)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
    </div>

    <ul class="nav nav-tabs mb-3" id="customerTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="customers-tab" data-bs-toggle="tab" data-bs-target="#customers"
                type="button" role="tab">{{ __('owner.customers.tabs.customers') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button"
                role="tab">{{ __('owner.customers.tabs.sales') }}</button>
        </li>
    </ul>


    <div class="tab-content" id="customerTabsContent">

        @include('owner.customers.customers')

        <div class="tab-pane fade" id="sales" role="tabpanel">
            @include('owner.customers.sales')
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
                    @if (app()->getLocale() == 'ar')
                        url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                    @endif
                },

                ajax: {
                    url: "{{ route('owner.getCustomerData') }}",
                    data: function(d) {
                        // d.from_date = $('#from_date').val();
                        // d.to_date = $('#to_date').val();
                    },
                    dataSrc: function(json) {
                        // تحديث عناصر الإحصائيات في الصفحة
                        $('#customerCount').text(json.customer_count);
                        $('#customerCountActive').text(json.customer_count_active);
                        // update numeric value only; SVG currency icon is rendered server-side inside the .unit span
                        $('#totalSales').text(json.total_sales);
                        $('#totalOrders').text(json.total_orders);

                        // إرجاع بيانات الصفوف للعرض في الجدول
                        return json.data;
                    }
                },



                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'order_count',
                        name: 'order_count'
                    },
                    {
                        data: 'total_sales',
                        name: 'total_sales'
                    },
                    {
                        data: 'total_remaining',
                        name: 'total_remaining'
                    },
                    {
                        data: 'last_order',
                        name: 'last_order'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: false
                    },
                ],
                responsive: false, scrollX: true,

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
            $('#from_date, #to_date').change(function() {
                table.draw();
            });
        });
    </script>
    <script type="text/javascript">
        $(function() {
            // Check if the DataTable is already initialized and destroy it
            if ($.fn.DataTable.isDataTable('#datatableSales')) {
                $('#datatableSales').DataTable().destroy();
            }


            // Initialize the DataTable
            var table = $('#datatableSales').DataTable({
                processing: true,
                serverSide: true,
                language: {
                    @if (app()->getLocale() == 'ar')
                        url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                    @endif
                },

                ajax: {
                    url: "{{ route('owner.getSalesData') }}",
                    data: function(d) {
                        // d.from_date = $('#from_date').val();
                        // d.to_date = $('#to_date').val();
                    },

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
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'customer',
                        name: 'customer'
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method'
                    },
                    {
                        data: 'total_weight',
                        name: 'total_weight'
                    },
                    // { data: 'commission_rate', name: 'commission_rate' },
                    // { data: 'labor_rate', name: 'labor_rate' },
                    {
                        data: 'total_price',
                        name: 'total_price'
                    },
                    // { data: 'net_owner_amount', name: 'net_owner_amount' },
                    // { data: 'remaining_total', name: 'remaining_total' },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                responsive: false, scrollX: true,

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
            $('#from_date, #to_date').change(function() {
                table.draw();
            });
        });
    </script>

    <script>
        $("#createForm").validate();
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
                        url: "{{ url('owner/customers') }}/" + recordId,
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

        // Model Edit
        $('#modelEdit').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var name = button.data('name')
            var email = button.data('email')
            var phone = button.data('phone')
            var notes = button.data('notes')

            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #name').val(name);
            modal.find('.modal-body #email').val(email);
            modal.find('.modal-body #phone').val(phone);
            modal.find('.modal-body #notes').val(notes);

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('customerType');
            const dalalFields = document.getElementById('dalalFields');
            const regularFields = document.getElementById('regularFields');

            function toggleCustomerFields() {
                if (typeSelect.value === 'dalal') {
                    dalalFields.classList.remove('d-none');
                    regularFields.classList.add('d-none');
                } else {
                    dalalFields.classList.add('d-none');
                    regularFields.classList.remove('d-none');
                }
            }

            typeSelect.addEventListener('change', toggleCustomerFields);
            toggleCustomerFields();
        });
    </script>
    <script>
        // Model Edit
        $('#modelEdit').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var name = button.data('name')
            var email = button.data('email')
            var phone = button.data('phone')
            var status = button.data('status')
            var notes = button.data('notes')
            var type = button.data('type')

            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #name').val(name);
            modal.find('.modal-body #email').val(email);
            modal.find('.modal-body #status').prop('checked', status == 1);
            modal.find('.modal-body #phone').val(phone);
            modal.find('.modal-body #notes').val(notes);
            modal.find('.modal-body #type').val(type);
        });
    </script>

    <script>
        function printCustomersReport() {
            window.open('{{ route('owner.reports.print.customers') }}', '_blank');
        }

        function printSalesReport() {
            window.open('{{ route('owner.reports.print.sales') }}', '_blank');
        }
    </script>

    <style>
        .report-card {
            padding: 2rem 1rem;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            height: 100%;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--bs-primary);
        }

        .report-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin: 0 auto;
        }
    </style>
    </script>
@endsection
