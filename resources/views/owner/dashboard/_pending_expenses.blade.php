{{-- Pending (unpaid) expenses for the authenticated owner. Rendered as its own
     compact card in the "أهم 5" section so it doesn't stretch the alerts panel. --}}
<div class="card shadow-sm h-100 border-0 pending-expenses-card">
    <div class="card-body d-flex flex-column">
        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
            <div class="min-w-0">
                <span class="pe-title fw-bold d-block">
                    <i class="bi bi-hourglass-split me-1 text-warning"></i>
                    {{ __('owner.dashboard.pending_expenses.title') }}
                </span>
                @if (($pendingExpensesSummary['count'] ?? 0) > 0)
                    <span class="pe-total text-muted d-block">
                        {{ __('owner.dashboard.pending_expenses.total') }}:
                        <span class="fw-semibold text-warning-emphasis">
                            {{ number_format($pendingExpensesSummary['amount'], 2) }}
                            {!! view('components.riyal-icon', [
                                'size' => 'sm',
                                'style' => 'width:.62rem;height:auto;display:inline-block;vertical-align:middle;',
                            ])->render() !!}
                        </span>
                    </span>
                @endif
            </div>
            <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis flex-shrink-0">
                {{ __('owner.dashboard.pending_expenses.count_badge', ['count' => $pendingExpensesSummary['count'] ?? 0]) }}
            </span>
        </div>

        <div class="pending-expenses-list pe-1">
            @forelse ($pendingExpenses as $expense)
                <a href="{{ route('owner.expenses.show', $expense) }}"
                    class="pending-expense-row d-flex align-items-center justify-content-between gap-2 py-1 text-decoration-none">
                    <span class="min-w-0">
                        <span class="pe-row-title d-block fw-semibold text-body text-truncate">
                            {{ $expense->number ?: $expense->category?->name ?: __('owner.dashboard.pending_expenses.uncategorized') }}
                        </span>
                        <span class="pe-row-meta d-block text-muted text-truncate">
                            {{ $expense->date }}
                            <span aria-hidden="true">·</span>
                            {{ $expense->category?->name ?: __('owner.dashboard.pending_expenses.uncategorized') }}
                        </span>
                    </span>
                    <span class="pending-expense-amount flex-shrink-0 fw-bold">
                        {{ number_format((float) $expense->final_price, 2) }}
                        {!! view('components.riyal-icon', [
                            'size' => 'sm',
                            'style' => 'width:.56rem;height:auto;display:inline-block;vertical-align:middle;',
                        ])->render() !!}
                    </span>
                </a>
            @empty
                <div class="text-center text-muted py-3">
                    <i class="bi bi-check2-circle text-success fs-5 d-block mb-1"></i>
                    <span class="pe-row-title">{{ __('owner.dashboard.pending_expenses.empty') }}</span>
                </div>
            @endforelse
        </div>

        @if (($pendingExpensesSummary['count'] ?? 0) > 0)
            <a href="{{ route('owner.expenses.index', ['status' => 'pending']) }}"
                class="pending-expenses-link pe-total text-decoration-none d-flex align-items-center justify-content-end gap-1 pt-2 mt-auto">
                {{ __('owner.dashboard.pending_expenses.view_all') }}
                <i class="bi bi-arrow-left-short rtl-flip"></i>
            </a>
        @endif
    </div>
</div>

<style>
    .pending-expenses-card .card-body {
        min-height: 0;
    }

    .pending-expenses-card .pe-title {
        font-size: .8rem;
        line-height: 1.25;
    }

    .pending-expenses-card .pe-total {
        font-size: .68rem;
    }

    .pending-expenses-card .pe-row-title {
        font-size: .72rem;
        line-height: 1.25;
    }

    .pending-expenses-card .pe-row-meta {
        font-size: .64rem;
        line-height: 1.2;
    }

    .pending-expenses-card .pending-expenses-list {
        flex: 1 1 auto;
        min-height: 0;
        max-height: 190px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    .pending-expenses-card .pending-expense-row+.pending-expense-row {
        border-top: 1px solid var(--bs-border-color-translucent);
    }

    .pending-expenses-card .pending-expense-row {
        transition: background-color .15s ease, padding-inline .15s ease;
    }

    .pending-expenses-card .pending-expense-row:hover,
    .pending-expenses-card .pending-expense-row:focus-visible {
        background: var(--bs-warning-bg-subtle);
        padding-inline: .35rem;
    }

    .pending-expenses-card .pending-expense-amount {
        color: var(--bs-warning-text-emphasis);
        font-size: .7rem;
        white-space: nowrap;
    }

    .pending-expenses-card .min-w-0 {
        min-width: 0;
    }

    [dir="ltr"] .pending-expenses-card .rtl-flip {
        transform: scaleX(-1);
    }
</style>
