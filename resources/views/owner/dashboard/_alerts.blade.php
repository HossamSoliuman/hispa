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

        <div id="ownerAlertsList" class="scroll-lg pe-1 flex-grow-1">
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
            <div id="ownerAlertsMore" class="text-center pt-2 border-top mt-2">
                <small class="text-muted">
                    {{ __('owner.alerts.showing', ['shown' => $maxVisible, 'total' => $alertSummary['total']]) }}
                </small>
            </div>
        @endif
    </div>
</div>

<style>
    .owner-alerts-card .alert-bar {
        flex: 0 0 4px;
        align-self: stretch;
        border-radius: 2px;
    }

    .owner-alerts-card .alert-row+.alert-row {
        border-top: 1px solid var(--bs-border-color-translucent);
    }

    .owner-alerts-card .min-w-0 {
        min-width: 0;
    }
</style>
