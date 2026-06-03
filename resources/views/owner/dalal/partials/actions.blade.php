<div class="d-flex gap-1">
    <a href="{{ route('owner.dalal.show', $id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('owner.actions.show') }}">
        <i class="bi bi-eye"></i>
        <span class="d-none d-md-inline"> {{ __('owner.actions.show') }}</span>
    </a>
    <a href="{{ route('owner.reports.print.dalal', $id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="{{ __('owner.dalal.report') }}">
        <i class="bi bi-printer"></i>
        <span class="d-none d-md-inline"> {{ __('owner.dalal.report') }}</span>
    </a>
</div>
