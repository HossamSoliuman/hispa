@extends('owner.layout.master')

@section('title', __('owner.dalal_invoices.invoice_details'))

@section('style')
    <style>
        .invoice-detail-card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 1.1rem;
            color: #212529;
        }
        .invoice-badge {
            font-size: 1rem;
            padding: 8px 16px;
        }
        .invoice-item-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('owner.dashboard') }}">{{ __('owner.menu.dashboard') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('owner.dalal-invoices.index') }}">{{ __('owner.dalal_invoices.title') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ __('owner.dalal_invoices.invoice_details') }}</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">
                <i class="bi bi-receipt me-2"></i>{{ __('owner.dalal_invoices.invoice_details') }} #{{ $sale->number }}
            </h4>
            <div>
                @if($sale->payment_status == 1)
                    <span class="badge bg-success invoice-badge">
                        <i class="bi bi-check-circle me-1"></i>{{ __('owner.status.paid') }}
                    </span>
                @else
                    <span class="badge bg-warning text-dark invoice-badge">
                        <i class="bi bi-hourglass-split me-1"></i>{{ __('owner.status.pending') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Invoice Details -->
            <div class="col-lg-8">
                <!-- Main Invoice Info -->
                <div class="card invoice-detail-card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>{{ __('owner.dalal_invoices.invoice_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="detail-label">{{ __('owner.dalal_invoices.table.invoice_number') }}</div>
                                <div class="detail-value">{{ $sale->number }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="detail-label">{{ __('owner.dalal_invoices.table.dalal_name') }}</div>
                                <div class="detail-value">{{ $sale->seller->name ?? __('owner.unknown') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="detail-label">{{ __('owner.dalal_invoices.table.boat_name') }}</div>
                                <div class="detail-value">{{ $sale->trip->boat->name ?? __('owner.trips.no_boat') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="detail-label">{{ __('owner.dalal_invoices.table.customer') }}</div>
                                <div class="detail-value">{{ $sale->customer_name ?? ($sale->customer->name ?? '-') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="detail-label">{{ __('owner.dalal_invoices.table.sale_date') }}</div>
                                <div class="detail-value">{{ $sale->sale_datetime ? \Carbon\Carbon::parse($sale->sale_datetime)->format('Y-m-d') : '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="detail-label">{{ __('owner.dalal_invoices.table.sent_date') }}</div>
                                <div class="detail-value">{{ $sale->invoice_sent_at ? \Carbon\Carbon::parse($sale->invoice_sent_at)->format('Y-m-d H:i') : '-' }}</div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="detail-label">{{ __('owner.dalal_invoices.payment_method') }}</div>
                                <div class="detail-value">{{ $sale->paymentMethod->name ?? $sale->payment_method ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="card invoice-detail-card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-basket me-2"></i>{{ __('owner.dalal_invoices.invoice_items') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table invoice-item-table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('owner.dalal_invoices.fish_name') }}</th>
                                        <th>{{ __('owner.dalal_invoices.weight') }}</th>
                                        <th>{{ __('owner.dalal_invoices.price_per_kg') }}</th>
                                        <th>{{ __('owner.dalal_invoices.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sale->details as $index => $detail)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $detail->fish->name ?? __('owner.unknown') }}</td>
                                            <td>{{ number_format($detail->weight ?? 0, 2) }} {{ __('owner.units.kg') }}</td>
                                            <td>{{ number_format($detail->price ?? 0, 2) }} {{ __('owner.units.sar') }}</td>
                                            <td>{{ number_format(($detail->weight ?? 0) * ($detail->price ?? 0), 2) }} {{ __('owner.units.sar') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">{{ __('owner.no_data') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                @if($sale->invoice_sent_note || $sale->notes)
                <div class="card invoice-detail-card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>{{ __('owner.dalal_invoices.notes') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($sale->invoice_sent_note)
                            <div class="mb-3">
                                <strong>{{ __('owner.dalal_invoices.dalal_note') }}:</strong>
                                <p class="mb-0 mt-2">{{ $sale->invoice_sent_note }}</p>
                            </div>
                        @endif
                        @if($sale->notes)
                            <div>
                                <strong>{{ __('owner.dalal_invoices.additional_notes') }}:</strong>
                                <p class="mb-0 mt-2" style="white-space: pre-line;">{{ $sale->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column: Financial Summary & Actions -->
            <div class="col-lg-4">
                <!-- Financial Summary -->
                <div class="card invoice-detail-card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>{{ __('owner.dalal_invoices.financial_summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                            <span class="fw-semibold">{{ __('owner.dalal_invoices.subtotal') }}:</span>
                            <span class="fw-bold">{{ number_format($sale->total_price ?? 0, 2) }} {{ __('owner.units.sar') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                            <span class="fw-semibold">{{ __('owner.dalal_invoices.commission') }} ({{ $sale->commission_rate ?? 0 }}%):</span>
                            <span class="text-danger">- {{ number_format($sale->commission_amount ?? 0, 2) }} {{ __('owner.units.sar') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                            <span class="fw-semibold">{{ __('owner.dalal_invoices.labor') }} ({{ $sale->labor_rate ?? 0 }}%):</span>
                            <span class="text-danger">- {{ number_format($sale->labor_amount ?? 0, 2) }} {{ __('owner.units.sar') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-0 pt-2">
                            <span class="fw-bold fs-5">{{ __('owner.dalal_invoices.net_owner_amount') }}:</span>
                            <span class="fw-bold fs-5 text-success">{{ number_format($sale->net_owner_amount ?? 0, 2) }} {{ __('owner.units.sar') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if($sale->payment_status == 0)
                <div class="card invoice-detail-card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>{{ __('owner.dalal_invoices.actions.title') }}</h5>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-success w-100 mb-2" id="acceptInvoiceBtn">
                            <i class="bi bi-check-circle me-2"></i>{{ __('owner.dalal_invoices.actions.accept') }}
                        </button>
                        <button type="button" class="btn btn-danger w-100" id="rejectInvoiceBtn">
                            <i class="bi bi-x-circle me-2"></i>{{ __('owner.dalal_invoices.actions.reject') }}
                        </button>
                    </div>
                </div>
                @endif

                <!-- Back Button -->
                <a href="{{ route('owner.dalal-invoices.index') }}" class="btn btn-secondary w-100">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('owner.actions.back') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            const invoiceId = {{ $sale->id }};

            // Accept invoice
            $('#acceptInvoiceBtn').on('click', function() {
                Swal.fire({
                    title: '{{ __("owner.dalal_invoices.actions.confirm_accept") }}',
                    text: '{{ __("owner.dalal_invoices.actions.accept_note") }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '{{ __("owner.dalal_invoices.actions.yes_accept") }}',
                    cancelButtonText: '{{ __("owner.actions.cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/{{ app()->getLocale() }}/owner/dalal-invoices/${invoiceId}/accept`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __("owner.dalal_invoices.success_title") }}',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = '{{ route("owner.dalal-invoices.index") }}';
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("owner.swal.error") }}',
                                    text: xhr.responseJSON?.message || '{{ __("owner.swal.unexpected_error") }}'
                                });
                            }
                        });
                    }
                });
            });

            // Reject invoice
            $('#rejectInvoiceBtn').on('click', function() {
                Swal.fire({
                    title: '{{ __("owner.dalal_invoices.actions.confirm_reject") }}',
                    html: `
                        <textarea id="rejection-reason" class="form-control" rows="3"
                                  placeholder="{{ __('owner.dalal_invoices.actions.rejection_reason') }}"></textarea>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '{{ __("owner.dalal_invoices.actions.yes_reject") }}',
                    cancelButtonText: '{{ __("owner.actions.cancel") }}',
                    preConfirm: () => {
                        const reason = document.getElementById('rejection-reason').value;
                        if (!reason) {
                            Swal.showValidationMessage('{{ __("owner.dalal_invoices.actions.reason_required") }}');
                        }
                        return { reason: reason };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/{{ app()->getLocale() }}/owner/dalal-invoices/${invoiceId}/reject`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                reason: result.value.reason
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __("owner.dalal_invoices.success_title") }}',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = '{{ route("owner.dalal-invoices.index") }}';
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("owner.swal.error") }}',
                                    text: xhr.responseJSON?.message || '{{ __("owner.swal.unexpected_error") }}'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
