<div id="sidebar" class="app-sidebar">
    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
        <div class="menu">
            <div class="menu-header"> </div>
            <div class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-speedometer2"></i></span>
                    <span class="menu-text">{{ __('admin.menu.dashboard') }}</span>
                </a>
            </div>

            <div class="menu-item has-sub {{ request()->routeIs('admin.subscription-packages.*') || request()->routeIs('admin.subscriptions.*') || request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-box-seam"></i></span>
                    <span class="menu-text">{{ __('admin.menu.subscription_packages') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">
                    <div class="menu-item {{ request()->routeIs('admin.subscription-packages.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.subscription-packages.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.packages') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.subscriptions.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.subscriptions') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.coupons.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.coupons') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="menu-item {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                <a href="{{ route('admin.invoices.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-receipt"></i></span>
                    <span class="menu-text">{{ __('admin.menu.invoices') }}</span>
                </a>
            </div>

            <div class="menu-item {{ request()->routeIs('admin.trips.*') ? 'active' : '' }}">
                <a href="{{ route('admin.trips.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-map"></i></span>
                    <span class="menu-text">{{ __('admin.menu.trips') }}</span>
                </a>
            </div>

            <div class="menu-item {{ request()->routeIs('admin.boats.*') ? 'active' : '' }}">
                <a href="{{ route('admin.boats.index') }}" class="menu-link">
                    <span class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                            <path d="M19.875,21a1.174,1.174,0,0,1-.9-.466A9.338,9.338,0,0,0,22,13.5V12.438l-2-.7V7.5A3.5,3.5,0,0,0,16.5,4H15V2a2,2,0,0,0-2-2H11A2,2,0,0,0,9,2V4H7.5A3.5,3.5,0,0,0,4,7.5v4.233l-2,.705V13.5a9.34,9.34,0,0,0,3.02,7.029A1.145,1.145,0,0,1,4.125,21,1.173,1.173,0,0,1,3,20H0a4.171,4.171,0,0,0,4.125,4,4.147,4.147,0,0,0,2.63-.969,4.079,4.079,0,0,0,5.261.015,4.076,4.076,0,0,0,5.259-.015A4.084,4.084,0,0,0,24,20H21A1.158,1.158,0,0,1,19.875,21Z"/>
                        </svg>
                    </span>
                    <span class="menu-text">{{ __('admin.menu.boats') }}</span>
                </a>
            </div>

            <div class="menu-item {{ request()->routeIs('admin.boat_types.*') ? 'active' : '' }}">
                <a href="{{ route('admin.boat_types.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-tags"></i></span>
                    <span class="menu-text">{{ __('admin.menu.boat_types') }}</span>
                </a>
            </div>

            <div class="menu-item {{ request()->routeIs('admin.owner.*') ? 'active' : '' }}">
                <a href="{{ route('admin.owner.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-people"></i></span>
                    <span class="menu-text">{{ __('admin.menu.owners') }}</span>
                </a>
            </div>

            <div class="menu-item has-sub {{ request()->routeIs('admin.counter.*') || request()->routeIs('admin.captain.*') || request()->routeIs('admin.crew.*') ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-person-badge"></i></span>
                    <span class="menu-text">{{ __('admin.menu.hr_management') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">
                    <div class="menu-item {{ request()->routeIs('admin.captain.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.captain.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.captains') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('admin.crew.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.crew.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.crews') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="menu-item has-sub {{ request()->routeIs('admin.fish.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.pages.*') ? 'active' : '' }}">
             
                <div class="menu-submenu">
                    <div class="menu-item {{ request()->routeIs('admin.fish.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.fish.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.fish_types') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.categories.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.categories') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pages.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.pages') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="menu-item has-sub {{ request()->routeIs('admin.stocks.*') || request()->routeIs('admin.owner-stock.*') || request()->routeIs('admin.dalal-stock.*') ? 'active' : '' }}">
                <div class="menu-item {{ request()->routeIs('admin.owner-stock.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.owner-stock.index') }}" class="menu-link">
                        <span class="menu-icon"><i class="bi bi-people-fill"></i></span>
                        <span class="menu-text">{{ __('admin.menu.owner_stocks') }}</span>
                    </a>
                </div>
            </div>


            <div class="menu-item has-sub {{ request()->routeIs('admin.sales-report') || request()->routeIs('admin.stock-report') ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-graph-up"></i></span>
                    <span class="menu-text">{{ __('admin.menu.reports') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">
                    <div class="menu-item {{ request()->routeIs('admin.sales-report*') ? 'active' : '' }}">
                        <a href="{{ route('admin.sales-report') }}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.sales_report') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('admin.stock-report*') ? 'active' : '' }}">
                        <a href="{{ route('admin.stock-report') }}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.catch_report') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="menu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <a href="{{ route('admin.settings.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-gear"></i></span>
                    <span class="menu-text">{{ __('admin.menu.settings') }}</span>
                </a>
            </div>

            <div class="menu-item {{ request()->routeIs('admin.user_request.*') ? 'active' : '' }}">
                <a href="{{ route('admin.user_request.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-envelope-paper"></i></span>
                    <span class="menu-text">{{ __('admin.menu.user_requests') }}</span>
                </a>
            </div>

            <div class="menu-item {{ request()->routeIs('admin.notifications') ? 'active' : '' }}">
                <a href="{{ route('admin.notifications') }}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-bell-fill"></i></span>
                    <span class="menu-text">{{ __('admin.menu.notifications') }}</span>
                </a>
            </div>

            <div class="menu-item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <a href="{{ route('admin.profile.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-person-circle"></i></span>
                    <span class="menu-text">{{ __('admin.menu.profile') }}</span>
                </a>
            </div>
        </div>
    </div>
</div>
