@props([
    'title'    => '',
    'value'    => '',
    'icon'     => 'bi bi-graph-up',
    'gradient' => null,
    'badge'    => null,
    'badgeText' => null,
    'stats'    => [],
    'footer'   => null,
    'colClass' => 'col-6 col-md-4 col-lg-3',
    'url'      => null,
    'trend'    => null,
    'trendValue' => null,
])

@php
    $tag   = $url ? 'a' : 'div';
    $attrs = $url ? 'href="' . $url . '" class="text-decoration-none d-block h-100"' : 'class="h-100"';
@endphp

<div class="{{ $colClass }}">
    <{!! $tag !!} {!! $attrs !!}>
        <div class="card hud-stat-card h-100">
            <div class="card-body p-3 d-flex flex-column">

                {{-- header row --}}
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1 pe-2">
                        <div class="hud-sc-label">{{ $title }}</div>
                        <div class="hud-sc-value d-flex align-items-baseline gap-2">
                            {!! $value !!}
                            @if($trend)
                                <span class="hud-sc-trend hud-sc-trend-{{ $trend }}">
                                    <i class="bi bi-arrow-{{ $trend === 'up' ? 'up' : 'down' }}"></i>
                                    @if($trendValue)<span class="ms-1">{{ $trendValue }}</span>@endif
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="hud-icon-box">
                        <i class="{{ $icon }}"></i>
                    </div>
                </div>

                {{-- optional badge --}}
                @if($badge && $badgeText)
                    <div class="hud-sc-badge mb-2 d-inline-flex align-items-center">
                        <i class="bi bi-arrow-up-right me-1"></i>
                        <span>{!! $badge !!} {!! $badgeText !!}</span>
                    </div>
                @endif

                {{-- optional mini-stats grid --}}
                @if(is_array($stats) && count($stats) > 0)
                    <div class="row text-center g-1 mt-auto">
                        @foreach($stats as $i => $stat)
                            <div class="col-{{ 12 / count($stats) }} {{ $i < count($stats) - 1 ? 'border-end' : '' }}">
                                <div class="hud-sc-grid-label">{{ $stat['label'] ?? '' }}</div>
                                <div class="hud-sc-grid-value">{!! $stat['value'] ?? '0' !!}</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- optional footer --}}
                @if($footer)
                    <div class="hud-sc-footer mt-auto pt-2 border-top">
                        <small><i class="bi bi-info-circle me-1"></i>{!! $footer !!}</small>
                    </div>
                @endif

            </div>
        </div>
    </{!! $tag !!}>
</div>

<style>
    .hud-stat-card {
        border-radius: 0 !important;
    }
    .hud-sc-label {
        font-size: 12px;
        font-weight: 600;
        color: rgba(0,0,0,.5);
        line-height: 1.25;
        margin-bottom: .35rem;
    }
    .hud-sc-value {
        font-size: 1.45rem;
        font-weight: 800;
        color: #1a1a2e;
        line-height: 1.1;
    }
    .hud-sc-trend {
        font-size: .72rem;
        display: inline-flex;
        align-items: center;
        padding: .1rem .35rem;
        border: 1px solid currentColor;
        opacity: .75;
    }
    .hud-sc-trend-up   { color: #198754; }
    .hud-sc-trend-down { color: #dc3545; }
    .hud-sc-badge {
        font-size: 11px;
        font-weight: 600;
        color: var(--hud-accent, #3675c2);
        border: 1px solid rgba(54,117,194,.3);
        padding: .15rem .45rem;
    }
    .hud-sc-grid-label { font-size: .65rem; color: rgba(0,0,0,.45); margin-bottom: .15rem; }
    .hud-sc-grid-value { font-size: .8rem; font-weight: 700; color: #1a1a2e; }
    .hud-sc-footer     { font-size: 11px; color: rgba(0,0,0,.45); }
</style>
