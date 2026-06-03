@props([
    'title' => '',
    'icon' => 'bi bi-bar-chart',
    'description' => null,
])

<div class="stat-section mb-4">
    <h5 class="stat-section-title">
        <i class="{{ $icon }} me-2"></i>{{ $title }}
        @if($description)
            <small class="text-muted ms-2" style="font-size: 0.9rem; font-weight: normal;">{{ $description }}</small>
        @endif
    </h5>
    <div class="row">