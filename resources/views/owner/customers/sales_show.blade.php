@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.item_8f6d39') }}
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

        @media print {
            body * {
                visibility: hidden;
            }

            #invoice-area, #invoice-area * {
                visibility: visible;
            }

            #invoice-area {
                position: absolute;
                top: 0;
                right: 0;
                left: 0;
                margin: 0 auto;
                width: 100%;
            }

            button, .card-arrow {
                display: none !important;
            }
        }
    </style>


@endsection
@section('content')

    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">{{ __('owner.generated.invoice') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.generated.invoice') }}</li>
            </ul>
            <h1 class="page-header mb-0"> {{ __('owner.generated.item_8f6d39') }}-{{$data->number}}</h1>
        </div>


    </div>






    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('owner.generated.item_1e02b2') }}: {{ $data->number }}</h4>
                    <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> {{ __('owner.generated.print_invoice') }}</button>
                </div>

                <div class="card-body" id="invoice-area">
                    <h5 class="mb-3">🧾 {{ __('owner.dalal_invoices.invoice_info') }}</h5>
                    <ul class="list-group mb-4">
                        <li class="list-group-item"><strong>{{ __('owner.dalal.filters.status') }}</strong>
                            <span class="badge bg-{{ $data->status == 1 ? 'warning' : ($data->status == 2 ? 'success' : 'secondary') }}">
                            {{ \App\Models\Sale::statusText($data->status) }}
                        </span>
                        </li>
                        <li class="list-group-item"><strong>{{ __('owner.generated.customer') }}</strong> {{ $data->customer_name ?? optional($data->customer)->name }}</li>
                        <li class="list-group-item"><strong>{{ __('owner.generated.seller') }}</strong>
                            @php
                                $sellerName = optional($data->seller)->name;
                                $roleName = $data->seller_type == 'dalal' ? __('owner.generated.item_3b125b') : ($data->seller_type == 'owner' ? __('owner.generated.item_83f142') : __('owner.generated.item_6b5e6d'));
                                $badge = $data->seller_type == 'dalal' ? 'info' : ($data->seller_type == 'owner' ? 'primary' : 'secondary');
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $sellerName }} - {{ $roleName }}</span>
                        </li>
                        <li class="list-group-item"><strong>{{ __('owner.generated.payment_method') }}</strong>
                            @php
                                $methodColor = match(optional($data->paymentMethod)->id) {
                                    1 => 'success', // نقدي
                                    2 => 'warning', // بنكي
                                    3 => 'info',    // شيك مثلاً
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $methodColor }}">{{ optional($data->paymentMethod)->name }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __('owner.generated.date') }}</strong>
                            <strong>{{ \Carbon\Carbon::parse($data->sale_datetime)->format('Y-m-d h:i A') }}</strong>
                        </li>
                    </ul>

                    <h5 class="mb-3">🐟 {{ __('owner.generated.items_details') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                            <tr>
                                <th>{{ __('owner.generated.fish') }}</th>
                                <th>{{ __('owner.assets.weight') }}({{ __('owner.sales_report.kg') }})</th>
                                <th>{{ __('owner.generated.price') }}/{{ __('owner.sales_report.kg') }}</th>
                                <th>{{ __('owner.assets.total_price') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data->details as $detail)
                                <tr>
                                    <td>{{ $detail->fish_name }}</td>
                                    <td>{{ $detail->weight }}</td>
                                    <td>{{ number_format($detail->price_per_kilo, 2) }}</td>
                                    <td>{{ number_format($detail->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4">💰 <strong>{{ __('owner.expenses.show.invoice_summary') }}</strong></h5>
                    <ul class="list-group fs-5">
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>{{ __('owner.generated.total') }}</strong>
                            <strong>{{ number_format($data->total_price{{ __('owner.generated.total_2') }}) }} {{ __('owner.units.sar') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>{{ __('owner.generated.item_f109e1') }} ({{ $data->commission_rate }}%):</strong>
                            <strong>{{ number_format($data->commission_amount{{ __('owner.generated.total_2') }}) }} {{ __('owner.units.sar') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>{{ __('owner.generated.item_a1db64') }} ({{ $data->labor_rate }}%):</strong>
                            <strong>{{ number_format($data->labor_amount{{ __('owner.generated.total_2') }}) }} {{ __('owner.units.sar') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>{{ __('owner.generated.fisherman_net') }}</strong>
                            <strong>{{ number_format($data->net_owner_amount{{ __('owner.generated.total_2') }}) }} {{ __('owner.units.sar') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>{{ __('owner.generated.remaining_1') }}</strong>
                            <strong>{{ number_format($data->remaining_total{{ __('owner.generated.total_2') }}) }} {{ __('owner.units.sar') }}</strong>
                        </li>
                    </ul>

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

@endsection
@section('script')

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
                    url: "{{ route('admin.getShowDetailStockData',$id) }}",
                    data: function (d) {

                    },
                    dataSrc: function (json) {
                        console.log(json.total_items); // ✅ يعرض البيانات في الكونسول
                        $('#item_name').text(json.data[0]?.name || '---');
                        $('#totalItems').text(json.total_items);
                        $('#totalWeight').text(json.total_weight + '{{ __('owner.generated.item_79b120') }}');
                        return json.data;
                    }
                },

                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'name', name: 'name' },
                    { data: 'captain_name', name: 'captain_name' },
                    { data: 'weight_captain', name: 'weight_captain' },
                    { data: 'counter_name', name: 'counter_name' },
                    { data: 'weight_counter', name: 'weight_counter' },
                    { data: 'weight', name: 'weight' },
                    { data: 'unit', name: 'unit' },

                ],

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
            $('#from_date, #to_date').change(function () {
                table.draw();
            });
        });
    </script>

    <script>
        $("#createForm").validate();
        $("#editForm").validate();

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
                confirmButtonText: '{{ __('owner.generated.item_7f4bb5') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/fish') }}/" + recordId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire('{{ __('owner.generated.item_2b6970') }}', response.message, 'success');
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function (xhr) {
                            Swal.fire('{{ __('owner.generated.item_dc5b8b') }}', '{{ __('owner.generated.item_843b15') }}', 'error');
                        }
                    });
                }
            });
        }

        // Model Edit
        $('#modelEdit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var scientific_name = button.data('scientific_name')
            var english_name = button.data('english_name')
            var local_name_primary = button.data('local_name_primary')
            var local_name_secondary = button.data('local_name_secondary')
            var status = button.data('status')

            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #scientific_name').val(scientific_name);
            modal.find('.modal-body #english_name').val(english_name);
            modal.find('.modal-body #local_name_primary').val(local_name_primary);
            modal.find('.modal-body #local_name_secondary').val(local_name_secondary);
            modal.find('.modal-body #status').prop('checked', status == 1);

        });

    </script>

@endsection
