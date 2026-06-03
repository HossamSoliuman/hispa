@props([
    'tabs' => [],
    'activeTab' => null,
    'variant' => 'tabs', // tabs, pills, underline
    'alignment' => 'start', // start, center, end, fill, justified
    'showCard' => true,
    'showArrow' => true,
    'cardClass' => '',
    'tabsClass' => '',
    'contentClass' => '',
])

{{--
/**
 * Owner Dashboard Tabs Wrapper Component
 *
 * Flexible tabbed navigation with multiple styles
 *
 * @usage
 * <x-owner.tabs-wrapper
 *     :tabs="[
 *         [
 *             'id' => 'boats',
 *             'label' => 'القوارب',
 *             'icon' => 'bi-ship',
 *             'badge' => '15',
 *             'url' => route('owner.boats.index'),
 *             'active' => true
 *         ],
 *         [
 *             'id' => 'maintenance',
 *             'label' => 'الصيانة',
 *             'icon' => 'bi-tools',
 *             'badge' => '3',
 *             'url' => null
 *         ]
 *     ]"
 *     variant="pills"
 *     alignment="center"
 * >
 *     <x-slot:boats>
 *         <!-- Boats tab content -->
 *     </x-slot:boats>
 *
 *     <x-slot:maintenance>
 *         <!-- Maintenance tab content -->
 *     </x-slot:maintenance>
 * </x-owner.tabs-wrapper>
 */
--}}

@php
$navClass = match($variant) {
    'pills' => 'nav-pills',
    'underline' => 'nav-underline',
    default => 'nav-tabs',
};

$alignmentClass = match($alignment) {
    'center' => 'justify-content-center',
    'end' => 'justify-content-end',
    'fill' => 'nav-fill',
    'justified' => 'nav-justified',
    default => '',
};

// Determine active tab
$activeTabId = $activeTab;
if (!$activeTabId) {
    foreach ($tabs as $tab) {
        if ($tab['active'] ?? false) {
            $activeTabId = $tab['id'];
            break;
        }
    }
}
if (!$activeTabId && count($tabs) > 0) {
    $activeTabId = $tabs[0]['id'];
}

$uniqueId = 'tabs_' . uniqid();
@endphp

@if($showCard)
<x-card-wrapper
    :showArrow="$showArrow"
    :cardClass="$cardClass"
    bodyClass="p-0">

    {{-- Tab Navigation --}}
    <div class="tabs-header border-bottom">
        <ul class="nav {{ $navClass }} {{ $alignmentClass }} {{ $tabsClass }} px-3 pt-3" id="{{ $uniqueId }}" role="tablist">
            @foreach($tabs as $index => $tab)
                @php
                $tabId = $tab['id'] ?? 'tab_' . $index;
                $isActive = $tabId === $activeTabId;
                $tabUrl = $tab['url'] ?? null;
                $tabLabel = $tab['label'] ?? '';
                $tabIcon = $tab['icon'] ?? null;
                $tabBadge = $tab['badge'] ?? null;
                $tabDisabled = $tab['disabled'] ?? false;
                @endphp

                <li class="nav-item" role="presentation">
                    @if($tabUrl)
                        <a class="nav-link {{ $isActive ? 'active' : '' }} {{ $tabDisabled ? 'disabled' : '' }}"
                           href="{{ $tabUrl }}"
                           @if($tabDisabled) aria-disabled="true" @endif>
                            @if($tabIcon)
                            <i class="{{ $tabIcon }} me-1"></i>
                            @endif
                            <span>{{ $tabLabel }}</span>
                            @if($tabBadge)
                            <span class="badge bg-primary bg-opacity-15 text-primary ms-2">{{ $tabBadge }}</span>
                            @endif
                        </a>
                    @else
                        <button class="nav-link {{ $isActive ? 'active' : '' }} {{ $tabDisabled ? 'disabled' : '' }}"
                                id="{{ $tabId }}-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#{{ $tabId }}-pane"
                                type="button"
                                role="tab"
                                aria-controls="{{ $tabId }}-pane"
                                aria-selected="{{ $isActive ? 'true' : 'false' }}"
                                @if($tabDisabled) disabled @endif>
                            @if($tabIcon)
                            <i class="{{ $tabIcon }} me-1"></i>
                            @endif
                            <span>{{ $tabLabel }}</span>
                            @if($tabBadge)
                            <span class="badge bg-primary bg-opacity-15 text-primary ms-2">{{ $tabBadge }}</span>
                            @endif
                        </button>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Tab Content --}}
    <div class="tab-content {{ $contentClass }} p-3" id="{{ $uniqueId }}-content">
        @foreach($tabs as $index => $tab)
            @php
            $tabId = $tab['id'] ?? 'tab_' . $index;
            $isActive = $tabId === $activeTabId;
            $tabUrl = $tab['url'] ?? null;
            @endphp

            @if(!$tabUrl)
            <div class="tab-pane fade {{ $isActive ? 'show active' : '' }}"
                 id="{{ $tabId }}-pane"
                 role="tabpanel"
                 aria-labelledby="{{ $tabId }}-tab"
                 tabindex="0">
                @isset(${$tabId})
                    {{ ${$tabId} }}
                @endisset
            </div>
            @endif
        @endforeach

        {{-- Default slot content --}}
        @if(!isset($tabs) || count($tabs) === 0)
            {{ $slot }}
        @endif
    </div>
</x-card-wrapper>
@else
{{-- Without Card --}}
<div class="tabs-wrapper">
    {{-- Tab Navigation --}}
    <ul class="nav {{ $navClass }} {{ $alignmentClass }} {{ $tabsClass }} mb-3" id="{{ $uniqueId }}" role="tablist">
        @foreach($tabs as $index => $tab)
            @php
            $tabId = $tab['id'] ?? 'tab_' . $index;
            $isActive = $tabId === $activeTabId;
            $tabUrl = $tab['url'] ?? null;
            $tabLabel = $tab['label'] ?? '';
            $tabIcon = $tab['icon'] ?? null;
            $tabBadge = $tab['badge'] ?? null;
            $tabDisabled = $tab['disabled'] ?? false;
            @endphp

            <li class="nav-item" role="presentation">
                @if($tabUrl)
                    <a class="nav-link {{ $isActive ? 'active' : '' }} {{ $tabDisabled ? 'disabled' : '' }}"
                       href="{{ $tabUrl }}"
                       @if($tabDisabled) aria-disabled="true" @endif>
                        @if($tabIcon)
                        <i class="{{ $tabIcon }} me-1"></i>
                        @endif
                        <span>{{ $tabLabel }}</span>
                        @if($tabBadge)
                        <span class="badge bg-primary bg-opacity-15 text-primary ms-2">{{ $tabBadge }}</span>
                        @endif
                    </a>
                @else
                    <button class="nav-link {{ $isActive ? 'active' : '' }} {{ $tabDisabled ? 'disabled' : '' }}"
                            id="{{ $tabId }}-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#{{ $tabId }}-pane"
                            type="button"
                            role="tab"
                            aria-controls="{{ $tabId }}-pane"
                            aria-selected="{{ $isActive ? 'true' : 'false' }}"
                            @if($tabDisabled) disabled @endif>
                        @if($tabIcon)
                        <i class="{{ $tabIcon }} me-1"></i>
                        @endif
                        <span>{{ $tabLabel }}</span>
                        @if($tabBadge)
                        <span class="badge bg-primary bg-opacity-15 text-primary ms-2">{{ $tabBadge }}</span>
                        @endif
                    </button>
                @endif
            </li>
        @endforeach
    </ul>

    {{-- Tab Content --}}
    <div class="tab-content {{ $contentClass }}" id="{{ $uniqueId }}-content">
        @foreach($tabs as $index => $tab)
            @php
            $tabId = $tab['id'] ?? 'tab_' . $index;
            $isActive = $tabId === $activeTabId;
            $tabUrl = $tab['url'] ?? null;
            @endphp

            @if(!$tabUrl)
            <div class="tab-pane fade {{ $isActive ? 'show active' : '' }}"
                 id="{{ $tabId }}-pane"
                 role="tabpanel"
                 aria-labelledby="{{ $tabId }}-tab"
                 tabindex="0">
                @isset(${$tabId})
                    {{ ${$tabId} }}
                @endisset
            </div>
            @endif
        @endforeach

        {{-- Default slot content --}}
        @if(!isset($tabs) || count($tabs) === 0)
            {{ $slot }}
        @endif
    </div>
</div>
@endif

<style>
    /* Tabs Header */
    .tabs-header {
        background: linear-gradient(to bottom, #ffffff, #f8f9fa);
    }

    /* Nav Tabs Styling */
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 500;
        padding: 0.75rem 1.25rem;
        transition: all 0.2s ease;
        position: relative;
    }

    .nav-tabs .nav-link:hover:not(.disabled) {
        color: #0d6efd;
        background-color: transparent;
        border-bottom-color: rgba(13, 110, 253, 0.3);
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-bottom-color: #0d6efd;
    }

    /* Nav Pills Styling */
    .nav-pills .nav-link {
        border-radius: 0.5rem;
        color: #6c757d;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    .nav-pills .nav-link:hover:not(.disabled) {
        color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3);
    }

    /* Nav Underline Styling */
    .nav-underline .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #6c757d;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    .nav-underline .nav-link:hover:not(.disabled) {
        color: #0d6efd;
        border-bottom-color: rgba(13, 110, 253, 0.3);
    }

    .nav-underline .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
    }

    /* Badge Styling */
    .nav-link .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        font-weight: 600;
    }

    .nav-pills .nav-link.active .badge {
        background-color: rgba(255, 255, 255, 0.3) !important;
        color: white !important;
    }

    /* Disabled State */
    .nav-link.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Tab Content */
    .tab-content {
        min-height: 200px;
    }

    .tab-pane {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Icon Styling */
    .nav-link i {
        font-size: 0.9rem;
        transition: transform 0.2s ease;
    }

    .nav-link:hover i {
        transform: scale(1.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .nav-tabs .nav-link,
        .nav-pills .nav-link,
        .nav-underline .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .nav-link .badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
        }

        /* Stack tabs vertically on mobile */
        .nav-tabs.nav-stacked-mobile,
        .nav-pills.nav-stacked-mobile {
            flex-direction: column;
        }

        .nav-stacked-mobile .nav-item {
            width: 100%;
        }

        .nav-stacked-mobile .nav-link {
            text-align: right;
        }
    }

    @media (max-width: 576px) {
        .tabs-header .nav {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            padding-top: 0.75rem !important;
        }

        .tab-content {
            padding: 0.75rem !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Store active tab in localStorage
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(tabEl) {
            tabEl.addEventListener('shown.bs.tab', function(e) {
                const tabId = e.target.getAttribute('data-bs-target');
                const navId = e.target.closest('.nav').id;
                if (navId) {
                    localStorage.setItem('activeTab_' + navId, tabId);
                }
            });
        });

        // Restore active tab from localStorage
        document.querySelectorAll('.nav[id]').forEach(function(navEl) {
            const navId = navEl.id;
            const savedTab = localStorage.getItem('activeTab_' + navId);
            if (savedTab) {
                const tabTrigger = document.querySelector('[data-bs-target="' + savedTab + '"]');
                if (tabTrigger && !tabTrigger.classList.contains('active')) {
                    const tab = new bootstrap.Tab(tabTrigger);
                    tab.show();
                }
            }
        });
    });
</script>
