@props([
    'title' => '',
    'value' => '',
    'icon' => 'bi-graph-up',
    'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
    'bgClass' => null,
    'badge' => null,
    'badgeText' => null,
    'badgeClass' => 'bg-white bg-opacity-15',
    'stats' => [],
    'footer' => null,
    'footerIcon' => 'bi-info-circle',
    'url' => null,
    'colClass' => 'col-6 col-md-4 col-lg-3',
    'trend' => null, // 'up', 'down', or null
    'trendValue' => null,
    'onClick' => null,
])

{{--
/**
 * Owner Dashboard Enhanced Stat Card Component
 *
 * Beautiful, animated statistics cards with multiple display options
 *
 * @usage
 * <x-owner.stat-card
 *     title="إجمالي القوارب"
 *     value="125"
 *     icon="bi-ship"
 *     gradient="linear-gradient(135deg, #2980b9, #3498db)"
 *     :url="route('owner.boats.index')"
 *     trend="up"
 *     trendValue="+12%"
 *     :badge="'25'"
 *     badgeText="جديد"
 *     :stats="[
 *         ['label' => 'نشط', 'value' => '100'],
 *         ['label' => 'متوقف', 'value' => '25']
 *     ]"
 *     footer="آخر تحديث: اليوم"
 * />
 */
--}}

@php
use Illuminate\Support\Str;
$bgStyle = '';
if ($bgClass) {
    // Use Bootstrap background class
} else {
    // Use gradient or custom background
    $bgStyle = "background: {$gradient};";
}

$wrapperTag = $url ? 'a' : 'div';
$wrapperAttrs = $url ? 'href="' . $url . '" class="text-decoration-none"' : '';
@endphp

<div class="{{ $colClass }}">
    <{!! $wrapperTag !!} {!! $wrapperAttrs !!}>
        <div class="card h-100 border-0 shadow-sm stat-card-animated {{ $bgClass }}"
             style="{{ $bgStyle }} transition: all 0.3s ease;"
             @if($onClick) onclick="{{ $onClick }}" @endif>

            <div class="card-body p-3 d-flex flex-column position-relative">
                {{-- Main Content Row --}}
                <div class="d-flex justify-content-between align-items-start mb-2">
                    {{-- Title and Value --}}
                    <div class="flex-grow-1 pe-2">
                        <h6 class="mb-1 text-white opacity-90 fw-semibold stat-title">{{ $title }}</h6>
                        <h2 class="fw-bold text-white mb-0 stat-value d-flex align-items-baseline gap-2">
                            {!! $value !!}
                            @if($trend)
                                <span class="stat-trend trend-{{ $trend }}">
                                    <i class="bi bi-arrow-{{ $trend === 'up' ? 'up' : 'down' }}"></i>
                                    @if($trendValue)
                                        <span class="ms-1">{{ $trendValue }}</span>
                                    @endif
                                </span>
                            @endif
                        </h2>
                    </div>

                    {{-- Icon Circle --}}
                    <div class="icon-circle bg-white bg-opacity-15 rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center"
                         style="width: 42px; height: 42px;">
                        <i class="{{ $icon }} stat-icon text-white"></i>
                    </div>
                </div>

                {{-- Badge Row --}}
                @if($badge && $badgeText)
                <div class="{{ $badgeClass }} px-2 py-1 rounded-3 mb-2 d-inline-flex align-items-center stat-badge"
                     style="backdrop-filter: blur(4px); width: fit-content;">
                    <i class="bi bi-star-fill me-1 stat-badge-icon"></i>
                    <span class="text-white fw-semibold stat-badge-text">
                        {!! $badge !!} {!! $badgeText !!}
                    </span>
                </div>
                @endif

                {{-- Stats Grid (ensure $stats is array to avoid conflict with controller $stats) --}}
                @if(is_array($stats) && count($stats) > 0)
                <div class="row text-white g-1 {{ $footer ? 'mb-2' : 'mt-auto' }} stat-grid">
                    @foreach($stats as $index => $stat)
                    <div class="col-{{ 12 / count($stats) }} text-center {{ $index < count($stats) - 1 ? 'border-end border-white border-opacity-25' : '' }}">
                        <div class="opacity-90 stat-grid-label">{{ $stat['label'] ?? '' }}</div>
                        <div class="fw-semibold stat-grid-value">{!! $stat['value'] ?? '0' !!}</div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Footer Info --}}
                @if($footer)
                <div class="bg-white bg-opacity-12 px-2 py-1 rounded-3 mt-auto stat-footer"
                     style="backdrop-filter: blur(4px);">
                    <small class="text-white fw-semibold d-flex align-items-center stat-footer-text">
                        <i class="{{ $footerIcon }} me-1"></i>{!! $footer !!}
                    </small>
                </div>
                @endif

                {{-- Hover Effect Overlay --}}
                <div class="stat-hover-overlay"></div>
            </div>
        </div>
    </{!! $wrapperTag !!}>
</div>

<style>
    /* Base Card Styling */
    .stat-card-animated {
        cursor: pointer;
        overflow: hidden;
        position: relative;
    }

    .stat-card-animated:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
    }

    /* Icon Animation */
    .stat-card-animated .icon-circle {
        transition: all 0.3s ease;
    }

    .stat-card-animated:hover .icon-circle {
        transform: scale(1.1) rotate(5deg);
        background: rgba(255, 255, 255, 0.3) !important;
    }

    /* Typography */
    .stat-card-animated .stat-title {
        font-size: 0.75rem;
        line-height: 1.2;
    }

    .stat-card-animated .stat-value {
        font-size: 1.5rem;
        line-height: 1.1;
    }

    .stat-card-animated .stat-icon {
        font-size: 1.1rem;
    }

    /* Trend Indicator */
    .stat-trend {
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
        background: rgba(255, 255, 255, 0.2);
    }

    .stat-trend.trend-up {
        color: #2ecc71;
    }

    .stat-trend.trend-down {
        color: #e74c3c;
    }

    /* Badge */
    .stat-badge {
        animation: pulse 2s ease-in-out infinite;
    }

    .stat-badge-icon {
        font-size: 0.625rem;
    }

    .stat-badge-text {
        font-size: 0.625rem;
    }

    /* Stats Grid */
    .stat-grid-label {
        font-size: 0.625rem;
        margin-bottom: 0.25rem;
    }

    .stat-grid-value {
        font-size: 0.75rem;
    }

    /* Footer */
    .stat-footer-text {
        font-size: 0.625rem;
    }

    /* Hover Overlay Effect */
    .stat-hover-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.05), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .stat-card-animated:hover .stat-hover-overlay {
        opacity: 1;
    }

    /* Pulse Animation */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
    }

    /* Responsive Adjustments */
    @media (max-width: 576px) {
        .stat-card-animated .icon-circle {
            width: 36px !important;
            height: 36px !important;
        }

        .stat-card-animated .stat-icon {
            font-size: 0.95rem;
        }

        .stat-card-animated .stat-value {
            font-size: 1.25rem;
        }

        .stat-card-animated .stat-title {
            font-size: 0.7rem;
        }
    }

    /* Link Styling */
    a .stat-card-animated {
        transition: all 0.3s ease;
    }

    a:hover .stat-card-animated {
        transform: translateY(-4px);
    }
</style>
