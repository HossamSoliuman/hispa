@extends('owner.layouts.master')

@section('title', __('owner.menu.payrolls_percentage'))
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

        /* Ensure small SAR icon everywhere (cards, tables, buttons) */
        .unit svg,
        .currency-symbol svg {
            width: 14px !important;
            height: 14px !important;
            vertical-align: middle;
            display: inline-block;
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
    <div class="container-fluid mt-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-2">{{ __('owner.menu.payrolls_percentage') }}</h2>
            </div>
            <div>
                <a href="{{ route('owner.percentageCreate') }}" class="btn btn-outline-theme btn-equal">
                    <i class="fa fa-plus-circle btn-success fa-fw me-1"></i>{{ __('owner.payrolls.add_new') }}
                </a>
            </div>
        </div>


        <div class="row mb-4">
            @include('owner.components.stat-card', [
                'title' => __('owner.payrolls.cards.total_payrolls'),
                'value' => '<span id="total_payrolls">0</span>',
                'icon' => 'bi bi-people-fill',
                'gradient' => 'linear-gradient(135deg, #2c3e50, #34495e)',
                'colClass' => 'col-md-3 col-sm-6 mb-3',
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.payrolls.cards.paid_amount'),
                'value' =>
                    '<span id="paid_amount">0</span> <span class="unit">' .
                    view('components.riyal-icon', ['size' => 'sm'])->render() .
                    '</span>',
                'icon' => 'bi bi-cash-stack',
                'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
                'colClass' => 'col-md-3 col-sm-6 mb-3',
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.payrolls.cards.open'),
                'value' => '<span id="pending_approval">0</span>',
                'icon' => 'bi bi-hourglass-split',
                'gradient' => 'linear-gradient(135deg, #d35400, #e67e22)',
                'colClass' => 'col-md-3 col-sm-6 mb-3',
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.payrolls.cards.pending'),
                'value' =>
                    '<span id="avg_per_crew">0</span> <span class="unit">' .
                    view('components.riyal-icon', ['size' => 'sm'])->render() .
                    '</span>',
                'icon' => 'bi bi-person-lines-fill',
                'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
                'colClass' => 'col-md-3 col-sm-6 mb-3',
            ])
        </div>


        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📋 {{ __('owner.generated.payroll_records') }}</h5>
                {{--            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i> {{ __('owner.dalal_stock_report.print') }}</button> --}}
            </div>
            {{-- <div class="card-body p-0">
            <div class="table-responsive"> --}}
            <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text"
                style="width:100%">
                <thead>

                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.generated.the_year') }}</th>
                        <th>{{ __('owner.generated.month') }}</th>
                        <th>{{ __('owner.generated.payment_status_lbl') }}</th>
                        <th>{{ __('owner.generated.payment_date') }}</th>
                        <th>{{ __('owner.payrolls.table.status') }}</th>
                        <th>{{ __('owner.payrolls.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            {{-- </div>

        </div> --}}
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


            // small inline riyal svg copied from component to use in table cells
            const riyalSvg = `{!! view('components.riyal-icon', ['size' => 'sm'])->render() !!}`;

            function currencyHtml(val) {
                if (val === null || val === undefined || val === '') return '-';
                // if value already contains non-numeric chars (formatted), try returning as-is
                var cleaned = String(val).replace(/[,\s\u00A0]/g, '');
                var num = parseFloat(cleaned);
                if (isNaN(num)) {
                    return String(val) + ' <span class="unit">' + riyalSvg + '</span>';
                }
                var formatted = num.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                return formatted + ' <span class="unit">' + riyalSvg + '</span>';
            }

            // Initialize the DataTable
            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,

                language: {
                    url: "{{ asset('dashboard/assets/js/ar.json') }}?v={{ time() }}"
                },

                ajax: {
                    url: "{{ route('owner.getPayrollsData') }}",
                    data: function(d) {
                        d.type = 'percentage';
                    },
                    dataSrc: function(json) {
                        // update stat cards (raw number only)
                        $('#total_payrolls').text(json.total_payrolls);
                        $('#paidPayrolls').text(json.paid_payrolls +
                            '{{ __('owner.generated.item_04abd3') }}');

                        $('#paid_amount').text(json.paid_amount);

                        $('#pending_approval').text(json.pending_approval);

                        $('#avg_per_crew').text(json.avg_per_crew);

                        // return table rows
                        return json.data;
                    },
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'year',
                        name: 'year'
                    },
                    {
                        data: 'month',
                        name: 'month'
                    },
                    {
                        data: 'is_paid',
                        name: 'is_paid'
                    },
                    {
                        data: 'paid_at',
                        name: 'paid_at'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'id',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!data) return '-';
                            var editUrl = '{{ url('owner/payrolls') }}/' + data + '/edit';
                            var printUrl = '{{ url('owner/payrolls') }}/' + data + '/print';
                            var editTitle = {!! json_encode(__('owner.actions.edit')) !!};
                            var printTitle = {!! json_encode(__('owner.actions.print')) !!};
                            var deleteTitle = {!! json_encode(__('owner.actions.delete')) !!};
                            var html = '';
                            html += '<a href="' + editUrl +
                                '" class="btn btn-outline-primary btn-sm me-1" title="' +
                                editTitle + '"><i class="bi bi-pencil"></i></a>';
                            html += '<a href="' + printUrl +
                                '" target="_blank" class="btn btn-outline-info btn-sm me-1" title="' +
                                printTitle + '"><i class="bi bi-printer"></i></a>';
                            html += '<button type="button" onclick="deleteRecord(' + data +
                                ')" class="btn btn-outline-danger btn-sm me-1" title="' +
                                deleteTitle + '"><i class="bi bi-trash"></i></button>';
                            return html;
                        }
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
                title: '{{ __('owner.generated.item_2d62e7') }}',
                text: "{{ __('owner.generated.item_5fb9f9') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('owner.generated.item_7f4bb5') }}',
                cancelButtonText: '{{ __('owner.generated.item_be4b2a') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('owner/payrolls') }}/" + recordId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('{{ __('owner.generated.item_2b6970') }}', response.message,
                                'success');
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message ||
                                '{{ __('owner.generated.item_201cac') }}';
                            Swal.fire('{{ __('owner.generated.item_dc5b8b') }}', message, 'error');
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


@endsection
