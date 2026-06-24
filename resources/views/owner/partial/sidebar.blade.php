<div id="sidebar" class="app-sidebar">
    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
        <div class="menu">
            <div class="menu-header"> </div>
            <div class="menu-item  {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                <a href="{{ route('owner.dashboard') }}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-speedometer2"></i></span>
                    <span class="menu-text">{{ __('owner.menu.dashboard') }}</span>
                </a>
            </div>
            <div
                class="menu-item {{ request()->routeIs('owner.trips.index') || request()->routeIs('owner.trips.create') || request()->routeIs('owner.trips.show') || request()->routeIs('owner.trips.edit') ? 'active' : '' }}">
                <a href="{{ route('owner.trips.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-map"></i></span>
                    <span class="menu-text">{{ __('owner.menu.trips') }}</span>
                </a>
            </div>

            <div
                class="menu-item has-sub {{ request()->routeIs('owner.catch.index') ||
                request()->routeIs('owner.catch.create') ||
                request()->routeIs('owner.catch.edit') ||
                request()->routeIs('owner.catch.show') ||
                request()->routeIs('owner.sales.index') ||
                request()->routeIs('owner.sales.create') ||
                request()->routeIs('owner.sales.edit') ||
                request()->routeIs('owner.sales.show')
                    ? 'active'
                    : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-journal-richtext"></i></span>
                    <span class="menu-text">{{ __('owner.generated.catch_management') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">
                    <div
                        class="menu-item {{ request()->routeIs('owner.catch.index') || request()->routeIs('owner.catch.create') || request()->routeIs('owner.catch.edit') || request()->routeIs('owner.catch.show') ? 'active' : '' }}">
                        <a href="{{ route('owner.catch.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.catches') }}</span>
                        </a>
                    </div>
                    <div
                        class="menu-item {{ request()->routeIs('owner.sales.index') || request()->routeIs('owner.sales.create') || request()->routeIs('owner.sales.edit') || request()->routeIs('owner.sales.show') ? 'active' : '' }}">
                        <a href="{{ route('owner.sales.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.sales') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div
                class="menu-item has-sub {{ request()->routeIs('owner.expenses.index') ||
                request()->routeIs('owner.expenses.create') ||
                request()->routeIs('owner.expenses.edit') ||
                request()->routeIs('owner.expenses.show') ||
                request()->routeIs('owner.payrolls.edit') ||
                request()->routeIs('owner.assets.index') ||
                request()->routeIs('owner.assets.create') ||
                request()->routeIs('owner.assets.edit') ||
                request()->routeIs('owner.assets.show') ||
                request()->routeIs('owner.percentage') ||
                request()->routeIs('owner.percentageCreate')
                    ? 'active'
                    : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-cash-coin"></i></span>
                    <span class="menu-text">{{ __('owner.menu.financial_management') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">

                    <div class="menu-item {{ request()->routeIs('owner.expenses.index') ? 'active' : '' }}">
                        <a href="{{ route('owner.expenses.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.expenses') }}</span>
                        </a>
                    </div>

                    <div
                        class="menu-item {{ request()->routeIs('owner.percentage') || request()->routeIs('owner.percentageCreate') || request()->routeIs('owner.payrolls.edit') ? 'active' : '' }}">
                        <a href="{{ route('owner.percentage') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.payrolls_percentage') }}</span>
                        </a>
                    </div>

                    <div
                        class="menu-item {{ request()->routeIs('owner.assets.index') || request()->routeIs('owner.assets.create') || request()->routeIs('owner.payrolls.edit') ? 'active' : '' }}">
                        <a href="{{ route('owner.assets.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.assets') }}</span>
                        </a>
                    </div>

                </div>
            </div>

            <div
                class="menu-item has-sub {{ request()->routeIs('owner.crew.index') ||
                request()->routeIs('owner.crew.create') ||
                request()->routeIs('owner.crew.edit') ||
                request()->routeIs('owner.crew.show') ||
                request()->routeIs('owner.employee.index') ||
                request()->routeIs('owner.employee.create') ||
                request()->routeIs('owner.employee.edit') ||
                request()->routeIs('owner.employee.show') ||
                request()->routeIs('owner.captain.index') ||
                request()->routeIs('owner.captain.create') ||
                request()->routeIs('owner.captain.edit') ||
                request()->routeIs('owner.captain.show') ||
                request()->routeIs('owner.customers.index') ||
                request()->routeIs('owner.customers.create') ||
                request()->routeIs('owner.customers.edit') ||
                request()->routeIs('owner.vendors.index') ||
                request()->routeIs('owner.vendors.create') ||
                request()->routeIs('owner.vendors.edit')
                    ? 'active'
                    : '' }}">
                <a href="#" class="menu-link ">
                    <span class="menu-icon"><i class="bi bi-people"></i></span>
                    <span class="menu-text">{{ __('owner.menu.hr') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div
                    class="menu-submenu {{ request()->routeIs('owner.captain.index') || request()->routeIs('owner.captain.create') || request()->routeIs('owner.captain.edit') ? 'active' : '' }}">
                    <div class="menu-item {{ request()->routeIs('owner.captain.index') ? 'active' : '' }}">
                        <a href="{{ route('owner.captain.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.captains') }}</span>
                        </a>
                    </div>
                    <div
                        class="menu-item {{ request()->routeIs('owner.crew.index') || request()->routeIs('owner.crew.create') || request()->routeIs('owner.crew.edit') || request()->routeIs('owner.crew.show') ? 'active' : '' }} ">
                        <a href="{{ route('owner.crew.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.crew') }}</span>
                        </a>
                    </div>
                    <div
                        class="menu-item {{ request()->routeIs('owner.employee.index') || request()->routeIs('owner.employee.create') || request()->routeIs('owner.employee.edit') || request()->routeIs('owner.employee.show') ? 'active' : '' }} ">
                        <a href="{{ route('owner.employee.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.employees') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('owner.customers.index') ? 'active' : '' }}">
                        <a href="{{ route('owner.customers.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.customers') }}</span>
                        </a>
                    </div>

                    <div class="menu-item {{ request()->routeIs('owner.vendors.index') ? 'active' : '' }}">
                        <a href="{{ route('owner.vendors.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.vendors') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div
                class="menu-item {{ request()->routeIs('owner.boats.index') || request()->routeIs('owner.boats.create') || request()->routeIs('owner.boats.show') || request()->routeIs('owner.boats.edit') ? 'active' : '' }}">
                <a href="{{ route('owner.boats.index') }}" class="menu-link">
                    <span class="menu-icon">
                        <svg xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                            <path
                                d="M19.875,21a1.174,1.174,0,0,1-.9-.466A9.338,9.338,0,0,0,22,13.5V12.438l-2-.7V7.5A3.5,3.5,0,0,0,16.5,4H15V2a2,2,0,0,0-2-2H11A2,2,0,0,0,9,2V4H7.5A3.5,3.5,0,0,0,4,7.5v4.233l-2,.705V13.5a9.34,9.34,0,0,0,3.02,7.029A1.145,1.145,0,0,1,4.125,21,1.173,1.173,0,0,1,3,20H0a4.171,4.171,0,0,0,4.125,4,4.147,4.147,0,0,0,2.63-.969,4.079,4.079,0,0,0,5.261.015,4.076,4.076,0,0,0,5.259-.015A4.084,4.084,0,0,0,24,20H21A1.158,1.158,0,0,1,19.875,21ZM7,7.5A.5.5,0,0,1,7.5,7h9a.5.5,0,0,1,.5.5v3.174L12,8.909,7,10.674ZM9.375,21A1.173,1.173,0,0,1,8.25,20l-.012-.828-.691-.443a6.147,6.147,0,0,1-2.475-4.193L10.5,12.62V20A1.158,1.158,0,0,1,9.375,21Zm5.25,0A1.173,1.173,0,0,1,13.5,20V12.62l5.428,1.916a6.161,6.161,0,0,1-2.472,4.192l-.706.434V20A1.158,1.158,0,0,1,14.625,21Z">
                            </path>
                        </svg>
                    </span>
                    <span class="menu-text">{{ __('owner.menu.boats') }}</span>
                </a>
            </div>

            <div
                class="menu-item has-sub {{ request()->routeIs('owner.profit.loss') ||
                request()->routeIs('owner.month-closing.*') ||
                request()->routeIs('owner.reports.*') ||
                request()->routeIs('owner.fishQuntity') ||
                request()->routeIs('owner.sales.index') ||
                request()->routeIs('owner.customers.index')
                    ? 'active'
                    : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-bar-chart"></i></span>
                    <span class="menu-text">{{ __('owner.menu.reports') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">

                    <div class="menu-item {{ request()->routeIs('owner.reports.hub') ? 'active' : '' }} ">
                        <a href="{{ route('owner.reports.hub') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.analysis_reports.hub_title') }}</span>
                        </a>
                    </div>

                    <!-- {{ __('owner.generated.item_57b5f1') }} -->
                    <div class="menu-item {{ request()->routeIs('owner.profit.loss') ? 'active' : '' }} ">
                        <a href="{{ route('owner.profit.loss') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.profit_loss') }}</span>
                        </a>
                    </div>

                    <div class="menu-item {{ request()->routeIs('owner.month-closing.*') ? 'active' : '' }} ">
                        <a href="{{ route('owner.month-closing.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.month_closing') }}</span>
                        </a>
                    </div>

                    <div class="menu-item {{ request()->routeIs('owner.fishQuntity') ? 'active' : '' }} ">
                        <a href="{{ route('owner.fishQuntity') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.menu.fish_stock') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('owner.customers.index') ? 'active' : '' }} ">
                        <a href="{{ route('owner.customers.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.customers.reports.customers') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('owner.sales.index') ? 'active' : '' }} ">
                        <a href="{{ route('owner.sales.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('owner.customers.reports.sales') }}</span>
                        </a>
                    </div>



                </div>
            </div>



            {{-- <div class="menu-item has-sub {{ request()->routeIs('owner.governorates.index') || request()->routeIs('owner.regions.index') || request()->routeIs('owner.cities.index') || request()->routeIs('owner.ports.index') ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-pin-map"></i></span>
                    <span class="menu-text">{{ __('owner.generated.locations') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">
                    <div class="menu-item {{ request()->routeIs('owner.regions.index') ? 'active' : '' }}">
                        <a href="{{route('owner.regions.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('owner.generated.regions') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('owner.governorates.index') ? 'active' : '' }}">
                        <a href="{{route('owner.governorates.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('owner.generated.governorates') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('owner.ports.index') ? 'active' : '' }}">
                        <a href="{{route('owner.ports.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('owner.generated.ports') }}</span>
                        </a>
                    </div>
                </div>
            </div> --}}

            <div class="menu-item {{ request()->routeIs('owner.settings.index') ? 'active' : '' }}">
                <a href="{{ route('owner.settings.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-gear-fill"></i></span>
                    <span class="menu-text">{{ __('owner.settings.title') }}</span>
                </a>
            </div>

            {{-- <div class="menu-item {{ request()->routeIs('owner.notifications') ? 'active' : '' }}">
                <a href="{{route('owner.notifications')}}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-bell-fill"></i></span>
                    <span class="menu-text">{{ __('owner.menu.notifications') }}</span>
                </a>
            </div>


            <div class="menu-item">
                <form method="POST" action="{{ route('owner.logout') }}">
                    @csrf
                    <button type="submit" class="menu-link bg-transparent border-0">
                        <span class="menu-icon"><i class="bi bi-box-arrow-right"></i></span>
                        <span class="menu-text">{{ __('owner.menu.logout') }}</span>
                    </button>
                </form>
            </div> --}}
        </div>
    </div>
</div>
