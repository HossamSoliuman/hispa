<div class="btn-group" role="group">
    <a href="{{ route('owner.dalal-invoices.show', $invoice->id) }}"
       class="btn btn-sm btn-info"
       title="{{ __('owner.actions.show') }}">
        <i class="bi bi-eye"></i>
    </a>

    @if($invoice->payment_status == 0)
    <button type="button"
            class="btn btn-sm btn-success accept-invoice"
            data-id="{{ $invoice->id }}"
            title="{{ __('owner.dalal_invoices.actions.accept') }}">
        <i class="bi bi-check-circle"></i>
    </button>

    <button type="button"
            class="btn btn-sm btn-danger reject-invoice"
            data-id="{{ $invoice->id }}"
            title="{{ __('owner.dalal_invoices.actions.reject') }}">
        <i class="bi bi-x-circle"></i>
    </button>
    @endif
</div>

@once
@push('scripts')
<script>
    // Accept invoice
    $(document).on('click', '.accept-invoice', function() {
        const invoiceId = $(this).data('id');

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
                            timer: 2000
                        });
                        $('#invoicesTable').DataTable().ajax.reload();
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
    $(document).on('click', '.reject-invoice', function() {
        const invoiceId = $(this).data('id');

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
                            timer: 2000
                        });
                        $('#invoicesTable').DataTable().ajax.reload();
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
</script>
@endpush
@endonce
