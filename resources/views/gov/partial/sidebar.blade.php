@php
    $govMenu = [
        ['route' => 'gov.dashboard',          'icon' => 'bi-house-door',           'label' => 'dashboard'],
        ['route' => 'gov.analytics',          'icon' => 'bi-graph-up-arrow',       'label' => 'analytics'],
        ['route' => 'gov.production',         'icon' => 'bi-compass',              'label' => 'production'],
        ['route' => 'gov.seasons',            'icon' => 'bi-calendar3',            'label' => 'seasons'],
        ['route' => 'gov.fish_types',         'icon' => 'bi-collection',           'label' => 'fish_types'],
        ['route' => 'gov.workforce',          'icon' => 'bi-people',               'label' => 'workforce'],
        ['route' => 'gov.fishing_tools',      'icon' => 'bi-tools',                'label' => 'fishing_tools'],
        ['route' => 'gov.fishing_equipment',  'icon' => 'bi-gear-wide-connected',  'label' => 'fishing_equipment'],
        ['route' => 'gov.reports',            'icon' => 'bi-file-earmark-text',    'label' => 'reports'],
        ['route' => 'gov.violations',         'icon' => 'bi-exclamation-triangle', 'label' => 'violations'],
        ['route' => 'gov.ports',              'icon' => 'bi-geo-alt',              'label' => 'ports'],
        ['route' => 'gov.employees',          'icon' => 'bi-person-badge',         'label' => 'employees'],
        ['route' => 'gov.roles',              'icon' => 'bi-shield-lock',          'label' => 'roles'],
        ['route' => 'gov.profile',            'icon' => 'bi-person-circle',        'label' => 'profile'],
    ];
@endphp

<div id="sidebar" class="app-sidebar">
    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
        <div class="menu">
            <div class="menu-header">{{ __('gov.auth.badge') }}</div>

            @foreach ($govMenu as $item)
                <div class="menu-item {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                    <a href="{{ route($item['route']) }}" class="menu-link"
                        @if (request()->routeIs($item['route'])) aria-current="page" @endif>
                        <span class="menu-icon"><i class="bi {{ $item['icon'] }}"></i></span>
                        <span class="menu-text">{{ __('gov.menu.'.$item['label']) }}</span>
                    </a>
                </div>
            @endforeach

            <div class="gov-menu-separator" aria-hidden="true"></div>

            <div class="menu-item">
                <a href="{{ route('gov.logout') }}" class="menu-link"
                    onclick="event.preventDefault(); document.getElementById('gov-sidebar-logout').submit();">
                    <span class="menu-icon"><i class="bi bi-box-arrow-right"></i></span>
                    <span class="menu-text">{{ __('gov.menu.logout') }}</span>
                </a>
                <form id="gov-sidebar-logout" action="{{ route('gov.logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </div>
    </div>
</div>
