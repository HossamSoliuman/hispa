{{-- Auto-derived owner warnings (overdue trips, expiring licenses, due
     inspections/maintenance...). Computed by App\Service\Owner\OwnerAlertService
     and refreshed in the background via route('owner.alerts.data'). --}}
@php
    $maxVisible = (int) config('alerts.max_visible', 6);
    $visibleAlerts = $alerts->take($maxVisible);
    $badgeClass = ($alertSummary['critical'] ?? 0) > 0
        ? 'bg-danger'
        : (($alertSummary['warning'] ?? 0) > 0 ? 'bg-warning text-dark' : 'bg-secondary');
@endphp

<div class="card shadow-sm h-100 border-0 owner-alerts-card">
    <div class="card-body d-flex flex-column">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bi bi-bell-fill me-2 text-warning"></i>{{ __('owner.alerts.title') }}
            </h5>
            <span id="ownerAlertsBadge"
                class="badge rounded-pill {{ $badgeClass }} {{ ($alertSummary['total'] ?? 0) === 0 ? 'd-none' : '' }}">
                {{ __('owner.alerts.count_badge', ['count' => $alertSummary['total'] ?? 0]) }}
            </span>
        </div>

        <div id="ownerAlertsList" class="owner-alerts-list pe-1">
            @forelse ($visibleAlerts as $alert)
                @include('owner.dashboard._alert_row', ['alert' => $alert])
            @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-check-circle-fill text-success fs-2 d-block mb-2"></i>
                    <span class="small">{{ __('owner.alerts.all_clear') }}</span>
                </div>
            @endforelse
        </div>

        @if (($alertSummary['total'] ?? 0) > $maxVisible)
            <div id="ownerAlertsMore" class="text-center pt-1">
                <small class="text-muted">
                    {{ __('owner.alerts.showing', ['shown' => $maxVisible, 'total' => $alertSummary['total']]) }}
                </small>
            </div>
        @endif

        <div class="pending-expenses-pane pt-2 mt-2 border-top">
            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                <div>
                    <h6 class="mb-1 fw-bold">
                        <i class="bi bi-hourglass-split me-1 text-warning"></i>
                        {{ __('owner.dashboard.pending_expenses.title') }}
                    </h6>
                    @if (($pendingExpensesSummary['count'] ?? 0) > 0)
                        <small class="text-muted">
                            {{ __('owner.dashboard.pending_expenses.total') }}:
                            <span class="fw-semibold text-warning-emphasis">
                                {{ number_format($pendingExpensesSummary['amount'], 2) }}
                                {!! view('components.riyal-icon', [
                                    'size' => 'sm',
                                    'style' => 'width:.68rem;height:auto;display:inline-block;vertical-align:middle;',
                                ])->render() !!}
                            </span>
                        </small>
                    @endif
                </div>
                <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis flex-shrink-0">
                    {{ __('owner.dashboard.pending_expenses.count_badge', ['count' => $pendingExpensesSummary['count'] ?? 0]) }}
                </span>
            </div>

            <div class="pending-expenses-list pe-1">
                @forelse ($pendingExpenses as $expense)
                    <a href="{{ route('owner.expenses.show', $expense) }}"
                        class="pending-expense-row d-flex align-items-center justify-content-between gap-2 py-2 text-decoration-none">
                        <span class="min-w-0">
                            <span class="d-block fw-semibold small text-body text-truncate">
                                {{ $expense->number ?: $expense->category?->name ?: __('owner.dashboard.pending_expenses.uncategorized') }}
                            </span>
                            <span class="d-block text-muted text-truncate" style="font-size:.72rem;">
                                {{ $expense->date }}
                                <span aria-hidden="true">·</span>
                                {{ $expense->category?->name ?: __('owner.dashboard.pending_expenses.uncategorized') }}
                            </span>
                        </span>
                        <span class="pending-expense-amount flex-shrink-0 fw-bold">
                            {{ number_format((float) $expense->final_price, 2) }}
                            {!! view('components.riyal-icon', [
                                'size' => 'sm',
                                'style' => 'width:.62rem;height:auto;display:inline-block;vertical-align:middle;',
                            ])->render() !!}
                        </span>
                    </a>
                @empty
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-check2-circle text-success fs-4 d-block mb-1"></i>
                        <span class="small">{{ __('owner.dashboard.pending_expenses.empty') }}</span>
                    </div>
                @endforelse
            </div>

            @if (($pendingExpensesSummary['count'] ?? 0) > 0)
                <a href="{{ route('owner.expenses.index', ['status' => 'pending']) }}"
                    class="pending-expenses-link small text-decoration-none d-flex align-items-center justify-content-end gap-1 pt-2">
                    {{ __('owner.dashboard.pending_expenses.view_all') }}
                    <i class="bi bi-arrow-left-short rtl-flip"></i>
                </a>
            @endif
        </div>
    </div>
</div>

<style>
    .owner-alerts-card {
        min-height: 0;
    }

    .owner-alerts-card .card-body {
        min-height: 0;
    }

    .owner-alerts-card .owner-alerts-list,
    .owner-alerts-card .pending-expenses-list {
        min-height: 0;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    .owner-alerts-card .owner-alerts-list {
        flex: 1 1 45%;
    }

    .owner-alerts-card .pending-expenses-pane {
        display: flex;
        flex: 1 1 55%;
        min-height: 0;
        flex-direction: column;
    }

    .owner-alerts-card .pending-expenses-list {
        flex: 1 1 auto;
    }

    .owner-alerts-card .alert-bar {
        flex: 0 0 4px;
        align-self: stretch;
        border-radius: 2px;
    }

    .owner-alerts-card .alert-row+.alert-row {
        border-top: 1px solid var(--bs-border-color-translucent);
    }

    .owner-alerts-card .pending-expense-row+.pending-expense-row {
        border-top: 1px solid var(--bs-border-color-translucent);
    }

    .owner-alerts-card .pending-expense-row {
        transition: background-color .15s ease, padding-inline .15s ease;
    }

    .owner-alerts-card .pending-expense-row:hover,
    .owner-alerts-card .pending-expense-row:focus-visible {
        background: var(--bs-warning-bg-subtle);
        padding-inline: .35rem;
    }

    .owner-alerts-card .pending-expense-amount {
        color: var(--bs-warning-text-emphasis);
        font-size: .78rem;
        white-space: nowrap;
    }

    .owner-alerts-card .min-w-0 {
        min-width: 0;
    }

    [dir="ltr"] .owner-alerts-card .rtl-flip {
        transform: scaleX(-1);
    }
</style>
