@props([
    'title' => '',
    'value' => '',
    'icon' => 'bi-graph-up',
    'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
    'badge' => null,
    'badgeText' => null,
    'stats' => [],
    'footer' => null,
    // Allow caller to control bootstrap column classes. Example: 'col-6 col-md-4 col-lg-3' for 4-per-row on large screens.
    'colClass' => 'col-6 col-md-4 col-lg-3',
])

<div class="{{ $colClass }}">
    <div class="card h-100 border-0 shadow-sm stat-card-hover" style="background: {{ $gradient }}; transition: all 0.18s ease;">
    <!-- increased padding for readability -->
    <div class="card-body p-3 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="flex-grow-1 pe-1">
                    <h6 class="mb-1 text-white opacity-90 fw-semibold small-title">{{ $title }}</h6>
                    <h2 class="fw-bold text-white mb-0 stat-value">{!! $value !!}</h2>
                </div>
                <div class="icon-circle bg-white bg-opacity-15 rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="{{ $icon }} stat-icon text-white"></i>
                </div>
            </div>

            @if($badge && $badgeText)
            <div class="bg-white bg-opacity-12 px-1 py-1 rounded-3 mb-1 d-inline-flex align-items-center" style="backdrop-filter: blur(4px); width: fit-content;">
                <i class="bi bi-arrow-up-right-circle me-1 xsmall"></i>
                <span class="text-white fw-semibold xsmall">
                    {!! $badge !!} {!! $badgeText !!}
                </span>
            </div>
            @endif

            @if(count($stats) > 0)
            <div class="row text-white g-1 {{ $footer ? 'mb-1' : 'mt-auto' }} small-stats">
                @foreach($stats as $index => $stat)
                <div class="col-{{ 12 / count($stats) }} text-center {{ $index < count($stats) - 1 ? 'border-end border-white border-opacity-10' : '' }}">
                    <div class="opacity-90 xsmall mb-1">{!! $stat['label'] !!}</div>
                    <div class="fw-semibold xsmall">{!! $stat['value'] !!}</div>
                </div>
                @endforeach
            </div>
            @endif

            @if($footer)
            <div class="bg-white bg-opacity-12 px-1 py-1 rounded-3 mt-auto" style="backdrop-filter: blur(4px);">
                <small class="text-white fw-semibold d-flex align-items-center xsmall">
                    <i class="bi bi-info-circle me-1 xsmall"></i>{!! $footer !!}
                </small>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .stat-card-hover {
        cursor: pointer;
    }
    .stat-card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.08) !important;
    }
    .stat-card-hover .icon-circle {
        transition: all 0.3s ease;
    }
    .stat-card-hover:hover .icon-circle {
            transform: scale(1.06) rotate(4deg);
            background: rgba(255, 255, 255, 0.28) !important;
    }

    /* Compact typography + responsive tweaks */
    /* Increased typography for better readability */
    .stat-card-hover .stat-icon { font-size: 1.4rem; }
    .stat-card-hover .stat-value { font-size: 1.5rem; line-height: 1.05; display: inline-flex; align-items: baseline; gap: .3rem; }
    .stat-card-hover .stat-value .num { font-weight: 800; }
    .stat-card-hover .stat-value .unit { font-size: .95rem; opacity: .98; }
    .stat-card-hover .small-title { font-size: 1.05rem; }
    .stat-card-hover .xsmall { font-size: .78rem; }

    @media (max-width: 576px) {
        .stat-card-hover .icon-circle { width: 44px !important; height: 44px !important; }
        .stat-card-hover .stat-icon { font-size: 1.2rem; }
        .stat-card-hover .stat-value { font-size: 1.25rem; }
        .stat-card-hover .small-title { font-size: .95rem; }
    }

    @media (min-width: 1200px) {
        .stat-card-hover .stat-value { font-size: 1.8rem; }
        .stat-card-hover .stat-value .unit { font-size: 1.05rem; }
    }
</style>
