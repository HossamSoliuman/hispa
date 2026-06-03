@props([
    'title' => '',
    'subtitle' => null,
    'icon' => null,
    'breadcrumbs' => [],
    'actions' => [],
    'stats' => [],
    'showBackButton' => false,
    'backRoute' => null,
    'containerClass' => 'mb-4',
])

{{--
/**
 * Owner Dashboard Page Header Component
 *
 * A comprehensive page header with title, breadcrumbs, action buttons, and optional stats
 *
 * @usage
 * <x-owner.page-header
 *     title="إدارة القوارب"
 *     subtitle="عرض وإدارة جميع القوارب"
 *     icon="bi bi-ship"
 *     :breadcrumbs="[
 *         ['label' => 'الرئيسية', 'url' => route('owner.dashboard')],
 *         ['label' => 'القوارب', 'url' => null]
 *     ]"
 *     :actions="[
 *         ['label' => 'إضافة قارب', 'url' => route('owner.boats.create'), 'icon' => 'bi-plus', 'class' => 'btn-primary'],
 *         ['label' => 'طباعة', 'url' => route('owner.boats.print'), 'icon' => 'bi-printer', 'class' => 'btn-success', 'target' => '_blank']
 *     ]"
 *     :stats="[
 *         ['label' => 'إجمالي القوارب', 'value' => '25', 'icon' => 'bi-ship', 'color' => 'primary'],
 *         ['label' => 'نشطة', 'value' => '20', 'icon' => 'bi-check-circle', 'color' => 'success']
 *     ]"
 * />
 */
--}}

<div class="{{ $containerClass }}">
    {{-- Breadcrumbs --}}
    @if(count($breadcrumbs) > 0)
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            @foreach($breadcrumbs as $breadcrumb)
                @if($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $breadcrumb['label'] ?? '' }}
                    </li>
                @else
                    <li class="breadcrumb-item">
                        @if(isset($breadcrumb['url']) && $breadcrumb['url'])
                            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] ?? '' }}</a>
                        @else
                            {{ $breadcrumb['label'] ?? '' }}
                        @endif
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
    @endif

    {{-- Main Header Row --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        {{-- Title Section --}}
        <div class="d-flex align-items-center gap-3">
            @if($showBackButton)
                <a href="{{ $backRoute ?? 'javascript:history.back()' }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-right-circle me-1"></i>
                    {{ __('owner.actions.back') ?? 'رجوع' }}
                </a>
            @endif

            <div>
                <div class="d-flex align-items-center gap-2">
                    @if($icon)
                        <i class="{{ $icon }} fs-4 text-primary"></i>
                    @endif
                    <h1 class="h3 fw-bold text-dark mb-0">{{ $title }}</h1>
                </div>
                @if($subtitle)
                    <p class="text-muted mb-0 mt-1 small">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        @if(count($actions) > 0)
        <div class="d-flex flex-wrap align-items-center gap-2">
            @foreach($actions as $action)
                @php
                    $actionLabel = $action['label'] ?? '';
                    $actionUrl = $action['url'] ?? '#';
                    $actionIcon = $action['icon'] ?? null;
                    $actionClass = $action['class'] ?? 'btn-outline-primary';
                    $actionSize = $action['size'] ?? 'btn-sm';
                    $actionTarget = $action['target'] ?? null;
                    $actionModal = $action['modal'] ?? null;
                    $actionClick = $action['onclick'] ?? null;
                    $actionId = $action['id'] ?? null;
                    $actionDisabled = $action['disabled'] ?? false;
                    $actionBadge = $action['badge'] ?? null;
                @endphp

                @if($actionModal)
                    <button
                        type="button"
                        class="btn {{ $actionClass }} {{ $actionSize }} d-flex align-items-center gap-1"
                        data-bs-toggle="modal"
                        data-bs-target="{{ $actionModal }}"
                        @if($actionId) id="{{ $actionId }}" @endif
                        @if($actionDisabled) disabled @endif
                    >
                        @if($actionIcon)
                            <i class="{{ $actionIcon }}"></i>
                        @endif
                        {{ $actionLabel }}
                        @if($actionBadge)
                            <span class="badge bg-white text-dark ms-1">{{ $actionBadge }}</span>
                        @endif
                    </button>
                @else
                    <a
                        href="{{ $actionUrl }}"
                        class="btn {{ $actionClass }} {{ $actionSize }} d-flex align-items-center gap-1"
                        @if($actionTarget) target="{{ $actionTarget }}" @endif
                        @if($actionClick) onclick="{{ $actionClick }}" @endif
                        @if($actionId) id="{{ $actionId }}" @endif
                        @if($actionDisabled) disabled @endif
                    >
                        @if($actionIcon)
                            <i class="{{ $actionIcon }}"></i>
                        @endif
                        {{ $actionLabel }}
                        @if($actionBadge)
                            <span class="badge bg-white text-dark ms-1">{{ $actionBadge }}</span>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>
        @endif
    </div>

    {{-- Quick Stats Row (Optional) --}}
    @if(count($stats) > 0)
    <div class="row g-2 mt-3">
        @foreach($stats as $stat)
            @php
                $statLabel = $stat['label'] ?? '';
                $statValue = $stat['value'] ?? '0';
                $statIcon = $stat['icon'] ?? 'bi-info-circle';
                $statColor = $stat['color'] ?? 'primary';
                $statUrl = $stat['url'] ?? null;
            @endphp
            <div class="col-6 col-md-3 col-lg-2">
                @if($statUrl)
                    <a href="{{ $statUrl }}" class="text-decoration-none">
                @endif
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body p-2 text-center">
                        <i class="{{ $statIcon }} text-{{ $statColor }} fs-5 mb-1"></i>
                        <div class="fw-bold fs-5 text-dark">{{ $statValue }}</div>
                        <small class="text-muted">{{ $statLabel }}</small>
                    </div>
                </div>
                @if($statUrl)
                    </a>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    {{-- Custom Content Slot --}}
    @if(isset($slot) && $slot->isNotEmpty())
        <div class="mt-3">
            {{ $slot }}
        </div>
    @endif
</div>

<style>
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
</style>
