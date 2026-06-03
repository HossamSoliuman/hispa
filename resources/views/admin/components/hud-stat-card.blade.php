@php
    $title    = $title    ?? '';
    $value    = $value    ?? '';
    $icon     = $icon     ?? 'bi bi-graph-up';
    $colClass = $colClass ?? 'col-6 col-md-3';
@endphp

<div class="{{ $colClass }}">
    <div class="hud-card h-100">
        <div class="hud-card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="hud-card-label">{{ $title }}</div>
                <div class="hud-icon-box">
                    <i class="{{ $icon }}"></i>
                </div>
            </div>
            <div class="hud-stat-value">{!! $value !!}</div>
        </div>
        <div class="hud-card-arrow">
            <div class="hud-arrow-tl"></div>
            <div class="hud-arrow-tr"></div>
            <div class="hud-arrow-bl"></div>
            <div class="hud-arrow-br"></div>
        </div>
    </div>
</div>
