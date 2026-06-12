@if ($d->is_paid)
    <span class="badge bg-success">{{ __('owner.status.paid') }}</span>
    <div class="small text-muted">{{ optional($d->paid_at)->format('Y-m-d') }}</div>
@else
    <button type="button" class="btn btn-success btn-sm pay-btn" data-detail-id="{{ $d->id }}">
        <i class="bi bi-cash-coin me-1"></i>{{ __('owner.generated.pay') }}
    </button>
@endif
