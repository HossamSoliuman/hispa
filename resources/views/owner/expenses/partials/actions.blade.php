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
