@extends('owner.layouts.master')

@section('title')
{{ __('owner.generated.item_239a4f') }}-{{ __('owner.generated.item_1488ff') }}
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

        <h3 class="mb-4">{{ __('owner.generated.sales_and_payments_broker_details') }}<span class="text-primary">{{ $dalal->name }}</span></h3>

        {{-- كروت الإحصائيات --}}
        <div class="row row-cols-1 row-cols-md-4 g-3 mb-4 text-white">
            <div class="col">
                <div class="card bg-primary text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-white"><i class="bi bi-basket3-fill me-1"></i>{{ __('owner.generated.sales_operations_count') }}</h6>
                        <h4 id="totalSalesCount" class="fw-bold text-white">0</h4>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-success text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-white"><i class="bi bi-currency-dollar me-1"></i>{{ __('owner.generated.total_due_amounts') }}</h6>
                        <h4 id="totalOwnerAmount" class="fw-bold text-white">{{ __('owner.generated.amount_000_sar') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-warning text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-white"><i class="bi bi-cash-stack me-1"></i>{{ __('owner.generated.total_paid') }}</h6>
                        <h4 id="totalPaid" class="fw-bold text-white">{{ __('owner.generated.amount_000_sar') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-danger text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-white"><i class="bi bi-exclamation-circle me-1"></i>{{ __('owner.dalal_stock_boat.cards.total_remaining') }}</h6>
                        <h4 id="totalRemaining" class="fw-bold text-white">{{ __('owner.generated.amount_000_sar') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <!-- {{ __('owner.generated.item_a13db5') }} -->
        <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="addPaymentForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addPaymentLabel">{{ __('owner.generated.add_payment_settlement') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="sale_id" id="sale_id">
                            <div class="mb-3">
                                <label class="form-label">{{ __('owner.dalal.payments.amount') }}<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('owner.generated.payment_date') }}<span class="text-danger">*</span></label>
                                <input type="date" name="paid_at" value="{{old('paid_at',date('Y-m-d'))}}" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('owner.sales.payment_method_id') }}<span class="text-danger">*</span></label>
                                <select name="payment_method_id" required class="form-control">
                                    <option value="">{{ __('owner.generated.choose_payment_method') }}</option>
                                    @foreach($payment_methods as $payment_method)
                                        <option value="{{$payment_method->id}}">{{$payment_method->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- {{ __('owner.generated.item_4f56b0') }} -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('owner.expenses.show.notes') }}</label>
                                <textarea name="{{ __('owner.generated.note_placeholder_additional') }}"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">{{ __('owner.customers.modal.buttons.save') }}</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('owner.boats.close') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- {{ __('owner.generated.item_6486b8') }} -->
        <div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editPaymentForm">
                        @csrf
                        <input type="hidden" name="payment_id" id="edit_payment_id" />
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPaymentLabel">{{ __('owner.generated.edit_payment') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_amount" class="form-label">{{ __('owner.dalal.payments.amount') }}<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_paid_at" class="form-label">{{ __('owner.generated.payment_date') }}<span class="text-danger">*</span></label>
                                <input type="date" name="paid_at" id="edit_paid_at" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_payment_method_id" class="form-label">{{ __('owner.sales.payment_method_id') }}<span class="text-danger">*</span></label>
                                <select name="payment_method_id" id="edit_payment_method_id" class="form-control" required>
                                    <option value="">{{ __('owner.generated.choose_payment_method') }}</option>
                                    @foreach($payment_methods as $payment_method)
                                        <option value="{{ $payment_method->id }}">{{ $payment_method->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_note" class="form-label">{{ __('owner.expenses.show.notes') }}</label>
                                <textarea name="note" id="edit_note" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">{{ __('owner.generated.save_changes') }}</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('owner.boats.close') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- {{ __('owner.generated.item_ac6680') }} -->
        <div class="modal fade" id="showPaymentsModal" tabindex="-1" aria-labelledby="showPaymentsLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showPaymentsLabel">{{ __('owner.generated.payments') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="paymentsList">
                        <!-- {{ __('owner.generated.item_266f93') }} -->
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="vendorTabsContent">
            <div class="tab-pane fade show active" id="vendors" role="tabpanel" aria-labelledby="vendors-tab">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                                <thead>

                                <tr>
                                    <th>#</th>
                                    <th>{{ __('owner.dalal_invoices.table.sale_date') }}</th>
                                    <th>{{ __('owner.catch.filters.boat') }}</th>
                                    <th>{{ __('owner.generated.total_sales') }}</th>
                                    <th>{{ __('owner.generated.broker_commission') }}</th>
                                    <th>{{ __('owner.generated.labor_commission') }}</th>
                                    <th>{{ __('owner.generated.total_broker') }}</th>
                                    <th>{{ __('owner.generated.total_fisherman') }}</th>
                                    <th>{{ __('owner.generated.amount_paid') }}</th>
                                    <th>{{ __('owner.generated.remaining') }}</th>
                                    <th>{{ __('owner.sales.payment_status') }}</th>
                                    <th>{{ __('owner.generated.settlements') }}</th>
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


        </div>


        <a href="{{ route('owner.dalal.index') }}" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> {{ __('owner.crew.edit.back') }}</a>

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

    <script>
        $("#addPaymentForm").validate();
        $("#editPaymentForm").validate();

    </script>

    <script type="text/javascript">


        $(function () {
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }

            $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                },
                ajax: {
                    url: "{{ route('owner.getDalalPaymentData',$dalal->id) }}",
                    data: function(d) {

                    },
                    dataSrc: function (json) {
                        $('#totalSalesCount').text(json.total_sales_count);
                        $('#totalOwnerAmount').text(json.total_owner_amount + '{{ __('owner.generated.item_93fe61') }}');
                        $('#totalPaid').text(json.total_paid + '{{ __('owner.generated.item_93fe61') }}');
                        $('#totalRemaining').text(json.total_remaining + '{{ __('owner.generated.item_93fe61') }}');
                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'sale_date', name: 'sale_date' },
                    { data: 'boat', name: 'boat' },
                    { data: 'total_price', name: 'total_price' },
                    { data: 'commission_rate', name: 'commission_rate' },
                    { data: 'labor_rate', name: 'labor_rate' },
                    { data: 'dalal_total', name: 'dalal_total' },
                    { data: 'owner_total', name: 'owner_total' },
                    { data: 'paid_amount', name: 'paid_amount' },
                    { data: 'remaining', name: 'remaining' },
                    { data: 'payment_status', name: 'payment_status', orderable: false, searchable: false },
                    { data: 'payments', name: 'payments', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                responsive: false, scrollX: true
            });
        });


    </script>
<script>
    $(document).on('click', '.addPayment', function () {
        var saleId = $(this).data('id');
        $('#sale_id').val(saleId);
        $('#addPaymentModal').modal('show');
    });
    $('#addPaymentForm').submit(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var maxAmount = parseFloat($('#addPaymentForm input[name="amount"]').attr('max'));
        var enteredAmount = parseFloat($('#addPaymentForm input[name="amount"]').val());

        if (enteredAmount > maxAmount) {
            alert("{{ __('owner.generated.item_1b9838') }}" + maxAmount);
            return;
        }
        if (isNaN(enteredAmount)) {
            return;
        }

        $.ajax({
            url: "{{ route('owner.dalal-payment.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function (res) {
                $('#addPaymentModal').modal('hide');
                alert('{{ __('owner.generated.item_6706c5') }}');
                $('#datatableDefault').DataTable().ajax.reload(null, false); // يعيد تحميل بدون الرجوع للصفحة الأولى

            },
            error: function (err) {
                alert(err.responseJSON?.message || '{{ __('owner.generated.item_9332f1') }}');
            }
        });
    });

    // عرض المدفوعات
    $(document).on('click', '.showPayments', function () {
        var saleId = $(this).data('id');
        $.ajax({
            url: "{{ route('owner.getPayments') }}",
            method: "GET",
            data: { sale_id: saleId },
            success: function (res) {
                $('#paymentsList').html(res);
                $('#showPaymentsModal').modal('show');
            }
        });
    });
</script>

    <script>
        $(document).ready(function() {

            let dalalPaymentUrlEdit = @json(route('owner.dalal-payment.edit', ':id'));


            $(document).on('click', '.edit-payment-btn', function() {

                let paymentId = $(this).data('payment-id');
                let url_edit = dalalPaymentUrlEdit.replace(':id', paymentId);

                $.ajax({
                    url:url_edit,
                    method: 'GET',
                    success: function(res) {
                        $('#edit_payment_id').val(res.id);
                        $('#edit_amount').val(res.amount);
                        $('#edit_paid_at').val(res.paid_at ? res.paid_at.split(' ')[0] : '');

                        $('#edit_payment_method_id').val(res.payment_method_id);
                        $('#edit_note').val(res.note);

                        var editModal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
                        $('#showPaymentsModal').modal('hide');

                        editModal.show();
                    },
                    error: function() {
                        alert('{{ __('owner.generated.item_1d0fcf') }}');
                    }
                });
            });
            let dalalPaymentUrlUpdate = @json(route('owner.dalal-payment.update', ':id'));

            $('#editPaymentForm').submit(function(e) {
                e.preventDefault();

                var paymentId = $('#edit_payment_id').val();
                let url_update = dalalPaymentUrlUpdate.replace(':id', paymentId);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: url_update,  // راوت PUT لتحديث الدفع
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {

                        alert('{{ __('owner.generated.item_1c41bd') }}');
                        $('#editPaymentModal').modal('hide');
                        $('#datatableDefault').DataTable().ajax.reload(null, false); // يعيد تحميل بدون الرجوع للصفحة الأولى

                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // رسائل الخطأ في Laravel ترجع في xhr.responseJSON.errors
                            var errors = xhr.responseJSON.errors;
                            if (errors && errors.amount) {
                                alert(errors.amount[0]); // عرض رسالة خطأ حقل المبلغ
                            } else if (xhr.responseJSON.message) {
                                alert(xhr.responseJSON.message);
                            } else {
                                alert('{{ __('owner.generated.item_6f2c1f') }}');
                            }
                        } else {
                            alert('{{ __('owner.generated.item_6f2c1f') }}');
                        }
                    }

                });
            });

        });
</script>
@endsection
