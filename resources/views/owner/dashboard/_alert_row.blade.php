@php
    $color = $alert->severity->color();
    $rowTag = $alert->url ? 'a' : 'div';
@endphp
<{{ $rowTag }} @if ($alert->url) href="{{ $alert->url }}" @endif
    class="alert-row d-flex gap-2 align-items-start py-2 text-decoration-none">
    <span class="alert-bar bg-{{ $color }}"></span>
    <span class="text-{{ $color }} flex-shrink-0 pt-1"><i class="bi {{ $alert->type->icon() }}"></i></span>
    <span class="flex-grow-1 min-w-0">
        <span class="d-block fw-semibold small text-body">{{ $alert->title }}</span>
        <span class="d-block text-muted lh-sm" style="font-size:.75rem;">{{ $alert->message }}</span>
        @if ($alert->dueAt)
            <span class="d-block text-{{ $color }}" style="font-size:.7rem;">
                <i class="bi bi-clock me-1"></i>{{ $alert->dueAt->diffForHumans() }}
            </span>
        @endif
    </span>
</{{ $rowTag }}>
