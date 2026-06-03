<div class="btn-group" role="group">
     <a href="{{ route('owner.expenses.show', $expense->id) }}"
         class="btn btn-sm btn-outline-info mx-1"
         title="{{ __('owner.actions.show') }}">
        <i class="bi bi-eye"></i>
    </a>

    @if($expense->status !== 'paid')
     <a href="{{ route('owner.expenses.edit', $expense->id) }}"
         class="btn btn-sm btn-outline-primary mx-1"
         title="{{ __('owner.actions.edit') }}">
        <i class="bi bi-pencil"></i>
    </a>
    @endif

    @if($expense->status === 'pending')
    <button type="button"
            class="btn btn-sm btn-outline-success mx-1"
            onclick="changeExpenseStatus({{ $expense->id }}, 'paid')"
            title="{{ __('owner.expenses.actions.confirm_payment') }}">
        <i class="bi bi-check-circle"></i>
    </button>
    @else
    <button type="button"
            class="btn btn-sm btn-outline-warning mx-1"
            onclick="changeExpenseStatus({{ $expense->id }}, 'pending')"
            title="{{ __('owner.expenses.actions.undo_payment') }}">
        <i class="bi bi-clock"></i>
    </button>
    @endif

    <button type="button"
            class="btn btn-sm btn-outline-secondary mx-1"
            onclick="printExpense({{ $expense->id }})"
            title="{{ __('owner.actions.print') }}">
        <i class="bi bi-printer"></i>
    </button>

    <button type="button"
            class="btn btn-sm btn-outline-danger mx-1"
            onclick="deleteExpense({{ $expense->id }})"
            title="{{ __('owner.actions.delete') }}">
        <i class="bi bi-trash"></i>
    </button>
</div>

<script>
function changeExpenseStatus(expenseId, newStatus) {
    const statusText = newStatus === 'paid' ? '{{ __('owner.paid') }}' : '{{ __('owner.pending') }}';
    const confirmText = newStatus === 'paid' ? '{{ __('owner.expenses.actions.confirm_payment') }}' : '{{ __('owner.expenses.actions.undo_payment') }}';

    Swal.fire({
        title: '{{ __('owner.expenses.actions.change_status_title') }}',
        text: `{{ __('owner.expenses.actions.change_status_text') }}`.replace(':action', confirmText),
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: newStatus === 'paid' ? '#28a745' : '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('owner.expenses.actions.confirm_button') }}',
        cancelButtonText: '{{ __('owner.actions.cancel') }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ route('owner.expenses.status', ['expense' => 'EXPENSE_ID']) }}`.replace('EXPENSE_ID', expenseId),
                method: 'PATCH',
                data: {
                    status: newStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('{{ __('owner.expenses.actions.done_title') }}', `{{ __('owner.expenses.actions.status_changed') }}`.replace(':status', statusText), 'success');
                        // إعادة تحميل الجدول إذا كان موجوداً
                        if (typeof table !== 'undefined') {
                            table.ajax.reload();
                        } else {
                            location.reload();
                        }
                    }
                },
                error: function(xhr) {
                    Swal.fire('{{ __('owner.swal.error') }}', '{{ __('owner.expenses.actions.update_error') }}', 'error');
                }
            });
        }
    });
}

function printExpense(expenseId) {
    window.open(`{{ route('owner.expenses.print', ['expense' => 'EXPENSE_ID']) }}`.replace('EXPENSE_ID', expenseId), '_blank');
}

function deleteExpense(expenseId) {
    Swal.fire({
        title: '{{ __('owner.swal.confirm_title') }}',
        text: '{{ __('owner.expenses.actions.delete_confirm_text') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('owner.swal.confirm_yes') }}',
        cancelButtonText: '{{ __('owner.actions.cancel') }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ route('owner.expenses.destroy', ['expense' => 'EXPENSE_ID']) }}`.replace('EXPENSE_ID', expenseId),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('{{ __('owner.swal.deleted') }}', response.message, 'success');
                        if (typeof table !== 'undefined') {
                            table.ajax.reload();
                        } else {
                            location.reload();
                        }
                    }
                },
                error: function(xhr) {
                    let message = '{{ __('owner.expenses.actions.delete_error') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire('{{ __('owner.generated.item_8ba2ea') }}', message, 'error');
                }
            });
        }
    });
}
</script>
