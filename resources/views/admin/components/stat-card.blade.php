@props([
    'title'      => '',
    'value'      => '',
    'icon'       => 'bi bi-graph-up',
    'gradient'   => null,
    'bgClass'    => null,
    'badge'      => null,
    'badgeText'  => null,
    'badgeClass' => null,
    'stats'      => [],
    'footer'     => null,
    'footerIcon' => 'bi-info-circle',
    'url'        => null,
    'colClass'   => 'col-6 col-md-4 col-lg-3',
    'trend'      => null,
    'trendValue' => null,
    'onClick'    => null,
])

@php
    $tag   = $url ? 'a' : 'div';
    $attrs = $url ? 'href="' . $url . '" class="text-decoration-none d-block h-100"' : 'class="h-100"';
@endphp

<div class="{{ $colClass }}">
    <{!! $tag !!} {!! $attrs !!}>
        <div class="card hud-stat-card h-100" @if($onClick) onclick="{{ $onClick }}" style="cursor:pointer;" @endif>
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
                        <small><i class="{{ $footerIcon }} me-1"></i>{!! $footer !!}</small>
                    </div>
                @endif

            </div>
        </div>
    </{!! $tag !!}>
</div>
