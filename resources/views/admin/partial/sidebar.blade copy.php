<div id="sidebar" class="app-sidebar">
    <!-- BEGIN scrollbar -->
    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
        <!-- BEGIN menu -->
        <div class="menu">
            @can('read_dashboard')
            <div class="menu-header">{{ __('admin.menu.dashboard') }}</div>
            <div
                class="menu-item  {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{route('admin.dashboard')}}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-speedometer2"></i>
                    </span>
                    <span class="menu-text">{{ __('admin.menu.dashboard') }}</span>
                </a>
            </div>
            @endcan
            @can('read_statistics')
            <div class="menu-item {{ request()->routeIs('admin.dashboard.statistics') ? 'active' : '' }}">
                <a href="{{route('admin.dashboard.statistics')}}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-bar-chart"></i></span>
                    <span class="menu-text">{{ __('admin.menu.statistics') }}</span>
                </a>
            </div>
            @endcan
            {{-- <div class="menu-header">Components</div>--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="widgets.html" class="menu-link">--}}
            {{-- <span class="menu-icon"><i class="bi bi-columns-gap"></i></span>--}}
            {{-- <span class="menu-text">Widgets</span>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item has-sub">--}}
            {{-- <a href="javascript:;" class="menu-link">--}}
            {{-- <div class="menu-icon"><i class="bi bi-stars"></i></div>--}}
            {{-- <div class="menu-text">AI Studio</div>--}}
            {{-- <span class="menu-caret"><b class="caret"></b></span>--}}
            {{-- </a>--}}
            {{-- <div class="menu-submenu">--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="ai_chat.html" class="menu-link">--}}
            {{-- <div class="menu-text">AI Chat</div>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="ai_image_generator.html" class="menu-link">--}}
            {{-- <div class="menu-text">AI Image Generator</div>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- </div>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item has-sub">--}}
            {{-- <a href="javascript:;" class="menu-link">--}}
            {{-- <div class="menu-icon">--}}
            {{-- <i class="bi bi-bag-check"></i>--}}
            {{-- <span class="w-5px h-5px rounded-3 bg-theme position-absolute top-0 end-0 mt-3px me-3px"></span>--}}
            {{-- </div>--}}
            {{-- <div class="menu-text d-flex align-items-center">POS System</div>--}}
            {{-- <span class="menu-caret"><b class="caret"></b></span>--}}
            {{-- </a>--}}
            {{-- <div class="menu-submenu">--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="pos_customer_order.html" target="_blank" class="menu-link">--}}
            {{-- <div class="menu-text">Customer Order</div>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="pos_kitchen_order.html" target="_blank" class="menu-link">--}}
            {{-- <div class="menu-text">Kitchen Order</div>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="pos_counter_checkout.html" target="_blank" class="menu-link">--}}
            {{-- <div class="menu-text">Counter Checkout</div>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="pos_table_booking.html" target="_blank" class="menu-link">--}}
            {{-- <div class="menu-text">Table Booking</div>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="pos_menu_stock.html" target="_blank" class="menu-link">--}}
            {{-- <div class="menu-text">Menu Stock</div>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- </div>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item has-sub">--}}
            {{-- <a href="#" class="menu-link">--}}
            {{-- <span class="menu-icon"><i class="bi bi-controller"></i></span>--}}
            {{-- <span class="menu-text">UI Kits</span>--}}
            {{-- <span class="menu-caret"><b class="caret"></b></span>--}}
            {{-- </a>--}}
            {{-- <div class="menu-submenu">--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="ui_bootstrap.html" class="menu-link">--}}
            {{-- <span class="menu-text">Bootstrap</span>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="ui_buttons.html" class="menu-link">--}}
            {{-- <span class="menu-text">Buttons</span>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="ui_card.html" class="menu-link">--}}
            {{-- <span class="menu-text">Card</span>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="ui_icons.html" class="menu-link">--}}
            {{-- <span class="menu-text">Icons</span>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="ui_modal_notification.html" class="menu-link">--}}
            {{-- <span class="menu-text">Modal & Notification</span>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="ui_typography.html" class="menu-link">--}}
            {{-- <span class="menu-text">Typography</span>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- <div class="menu-item">--}}
            {{-- <a href="ui_tabs_accordions.html" class="menu-link">--}}
            {{-- <span class="menu-text">Tabs & Accordions</span>--}}
            {{-- </a>--}}
            {{-- </div>--}}
            {{-- </div>--}}
            {{-- </div>--}}

            @if(auth()->user()->can('read_stocks') ||auth()->user()->can('read_owner-stock') ||auth()->user()->can('read_dalal-stock'))

            <div
                class="menu-item has-sub {{ request()->routeIs('admin.stocks.index') ||request()->routeIs('admin.stocks.show')  || request()->routeIs('admin.owner-stock.index')   || request()->routeIs('admin.owner-stock.show') || request()->routeIs('admin.dalal-stock.index') || request()->routeIs('admin.dalal-stock.show') ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="fas fa-ship"></i></span>
                    <span class="menu-text">{{ __('admin.menu.catch') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu ">
                    @can('read_stocks')
                    <div
                        class="menu-item {{ request()->routeIs('admin.stocks.index') ||request()->routeIs('admin.stocks.show')  ? 'active' : '' }}">
                        <a href="{{route('admin.stocks.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.general') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_owner-stock')
                    <div
                        class="menu-item {{ request()->routeIs('admin.owner-stock.index')  ||request()->routeIs('admin.owner-stock.show') ? 'active' : '' }}">
                        <a href="{{route('admin.owner-stock.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.fishermen') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_dalal-stock')
                    <div
                        class="menu-item {{ request()->routeIs('admin.dalal-stock.index')  ||request()->routeIs('admin.dalal-stock.show') ? 'active' : '' }}">
                        <a href="{{route('admin.dalal-stock.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.dalals') }}</span>
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
            @endif
            @if(auth()->user()->can('read_trips') ||auth()->user()->can('create_trips'))

            <div class="menu-item has-sub
    {{ request()->routeIs('admin.trips.index') || request()->routeIs('admin.trips.create') || request()->routeIs('admin.trips.edit') || request()->routeIs('admin.trips.show') ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-map"></i></span>
                    <span class="menu-text">{{ __('admin.menu.trips') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">

                    @can('create_trips')
                    {{-- إضافة رحلة جديدة --}}
                    <div class="menu-item {{ request()->routeIs('admin.trips.create') ? 'active' : '' }}">
                        <a href="{{ route('admin.trips.create') }}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.add_new_trip') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_trips')
                    {{-- كل الرحلات --}}
                    <div
                        class="menu-item {{ request('status') == null && request()->routeIs('admin.trips.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.trips.index') }}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.all_trips') }}</span>
                        </a>
                    </div>
                    @php
                    $statusColors = [
                    1 => 'primary', // جديدة
                    2 => 'info', // قيد التنفيذ
                    3 => 'danger', // ملغاة
                    4 => 'secondary', // انتهت من الكابتن
                    5 => 'warning', // جاري العد
                    6 => 'warning', // انتهت من المحاسب
                    7 => 'success', // جاهزة للبيع
                    8 => 'success', // مكتملة
                    ];
                    @endphp

                    @foreach ($tripStatuses as $key => $label)
                    @php
                    $count = $tripCounts[$key] ?? 0;
                    $color = $statusColors[$key] ?? 'secondary';
                    @endphp
                    <div class="menu-item {{ request('status') == $key ? 'active' : '' }}">
                        <a href="{{ route('admin.trips.index', ['status' => $key]) }}" class="menu-link">
                            <span class="menu-text">
                                {{ $label }}
                                <span class="badge bg-{{ $color }} ms-1">{{ $count }}</span>
                            </span>
                        </a>
                    </div>
                    @endforeach
                    @endcan

                </div>
            </div>
            @endif
            @can('read_sales')

            <div
                class="menu-item has-sub {{ request()->routeIs('admin.sales.index') ||request()->routeIs('admin.sales.create') ||request()->routeIs('admin.sales.edit') ? 'active' : '' }}">
                <a href="#" class="menu-link ">
                    <span class="menu-icon"><i class="bi bi-receipt"></i></span>
                    <span class="menu-text">{{ __('admin.menu.sales') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">
                    <div
                        class="menu-item {{ request()->routeIs('admin.sales.index') && !request('type') ? 'active' : '' }}">
                        <a href="{{ route('admin.sales.index') }}"
                            class="menu-link d-flex justify-content-between align-items-center">
                            <span class="menu-text">{{ __('admin.menu.all_sales') }}</span>
                            <span class="badge bg-secondary">{{ $saleCounts['all'] ?? 0 }}</span>
                        </a>
                    </div>

                    <div
                        class="menu-item {{ request()->routeIs('admin.sales.index') && request('type') == 'owner' ? 'active' : '' }}">
                        <a href="{{ route('admin.sales.index', ['type' => 'owner']) }}"
                            class="menu-link d-flex justify-content-between align-items-center">
                            <span class="menu-text">{{ __('admin.menu.owner_sales') }}</span>
                            <span class="badge bg-primary">{{ $saleCounts['owner'] ?? 0 }}</span>
                        </a>
                    </div>

                    <div
                        class="menu-item {{ request()->routeIs('admin.sales.index') && request('type') == 'dalal' ? 'active' : '' }}">
                        <a href="{{ route('admin.sales.index', ['type' => 'dalal']) }}"
                            class="menu-link d-flex justify-content-between align-items-center">
                            <span class="menu-text">{{ __('admin.menu.dalal_sales') }}</span>
                            <span class="badge bg-info">{{ $saleCounts['dalal'] ?? 0 }}</span>
                        </a>
                    </div>
                </div>

            </div>
            @endcan
            @if(auth()->user()->can('read_sales_report') ||auth()->user()->can('read_stock_report') ||auth()->user()->can('read_dalal_stock_report') ||auth()->user()->can('read_trip_report') ||auth()->user()->can('read_fish_stock_history_report') )

            <div
                class="menu-item has-sub {{ request()->routeIs('admin.sales-report') ||request()->routeIs('admin.stock-report')|| request()->routeIs('admin.dalal-stock-report')  || request()->routeIs('admin.trip-report') ||request()->routeIs('admin.fish-history-report')        ? 'active' : '' }}">
                <a href="#" class="menu-link ">
                    <span class="menu-icon"><i class="bi bi-file-earmark-bar-graph-fill"></i></span>
                    <span class="menu-text">{{ __('admin.menu.reports') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">

                    @can('read_sales_report')
                    <div class="menu-item {{ request()->routeIs('admin.sales-report')  ? 'active' : '' }}">
                        <a href="{{ route('admin.sales-report') }}"
                            class="menu-link d-flex justify-content-between align-items-center">
                            <span class="menu-text">{{ __('admin.menu.sales_report') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_stock_report')
                    <div class="menu-item {{ request()->routeIs('admin.stock-report')  ? 'active' : '' }}">
                        <a href="{{ route('admin.stock-report') }}"
                            class="menu-link d-flex justify-content-between align-items-center">
                            <span class="menu-text">{{ __('admin.menu.catch_report') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_dalal_stock_report')
                    <div
                        class="menu-item {{ request()->routeIs('admin.dalal-stock-report')  ? 'active' : '' }}">
                        <a href="{{ route('admin.dalal-stock-report') }}"
                            class="menu-link d-flex justify-content-between align-items-center">
                            <span class="menu-text">{{ __('admin.menu.dalal_stock_report') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_trip_report')
                    <div class="menu-item {{ request()->routeIs('admin.trip-report')  ? 'active' : '' }}">
                        <a href="{{ route('admin.trip-report') }}"
                            class="menu-link d-flex justify-content-between align-items-center">
                            <span class="menu-text">{{ __('admin.menu.trip_report') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_fish_stock_history_report')
                    <div
                        class="menu-item {{ request()->routeIs('admin.fish-history-report')  ? 'active' : '' }}">
                        <a href="{{ route('admin.fish-history-report') }}"
                            class="menu-link d-flex justify-content-between align-items-center">
                            <span class="menu-text">{{ __('admin.menu.fish_movement_report') }}</span>
                        </a>
                    </div>
                    @endcan
                </div>

            </div>
            @endif

            @if(auth()->user()->can('create_owner') ||auth()->user()->can('read_owner'))

            <div
                class="menu-item has-sub {{ request()->routeIs('admin.owner.index') ||request()->routeIs('admin.owner.create') ||request()->routeIs('admin.owner.edit')  ||request()->routeIs('admin.owner.show') ? 'active' : '' }}">
                <a href="#" class="menu-link ">
                    <span class="menu-icon"><i class="bi bi-people"></i></span>
                    <span class="menu-text">{{ __('admin.menu.owners_management') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu ">
                    @can('create_owner')
                    <div class="menu-item {{ request()->routeIs('admin.owner.create') ? 'active' : '' }}">
                        <a href="{{route('admin.owner.create')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.add_new_owner') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_owner')

                    <div class="menu-item {{ request()->routeIs('admin.owner.index') ? 'active' : '' }} ">
                        <a href="{{route('admin.owner.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.owners') }}</span>
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
            @endif
            @if(auth()->user()->can('create_boats') ||auth()->user()->can('read_boats') || auth()->user()->can('read_boat_types') )

            <div
                class="menu-item has-sub {{ request()->routeIs('admin.boats.index') ||request()->routeIs('admin.boats.create') ||request()->routeIs('admin.boats.edit')  ||request()->routeIs('admin.boats.show')   ||request()->routeIs('admin.boat_types.index') ? 'active' : '' }}">
                <a href="#" class="menu-link ">
                    <span class="menu-icon">
                        <svg xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                            <path
                                d="M19.875,21a1.174,1.174,0,0,1-.9-.466A9.338,9.338,0,0,0,22,13.5V12.438l-2-.7V7.5A3.5,3.5,0,0,0,16.5,4H15V2a2,2,0,0,0-2-2H11A2,2,0,0,0,9,2V4H7.5A3.5,3.5,0,0,0,4,7.5v4.233l-2,.705V13.5a9.34,9.34,0,0,0,3.02,7.029A1.145,1.145,0,0,1,4.125,21,1.173,1.173,0,0,1,3,20H0a4.171,4.171,0,0,0,4.125,4,4.147,4.147,0,0,0,2.63-.969,4.079,4.079,0,0,0,5.261.015,4.076,4.076,0,0,0,5.259-.015A4.084,4.084,0,0,0,24,20H21A1.158,1.158,0,0,1,19.875,21ZM7,7.5A.5.5,0,0,1,7.5,7h9a.5.5,0,0,1,.5.5v3.174L12,8.909,7,10.674ZM9.375,21A1.173,1.173,0,0,1,8.25,20l-.012-.828-.691-.443a6.147,6.147,0,0,1-2.475-4.193L10.5,12.62V20A1.158,1.158,0,0,1,9.375,21Zm5.25,0A1.173,1.173,0,0,1,13.5,20V12.62l5.428,1.916a6.161,6.161,0,0,1-2.472,4.192l-.706.434V20A1.158,1.158,0,0,1,14.625,21Z"></path>
                        </svg>
                    </span>
                    <span class="menu-text">{{ __('admin.menu.boats_management') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu ">
                    @can('create_boats')
                    <div class="menu-item {{ request()->routeIs('admin.boats.create') ? 'active' : '' }}">
                        <a href="{{route('admin.boats.create')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.add_new_boat') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_boat_types')

                    <div class="menu-item {{ request()->routeIs('admin.boat_types.index') ? 'active' : '' }}">
                        <a href="{{route('admin.boat_types.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.boat_types') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('admin.boats.index')  ||request()->routeIs('admin.boats.edit') ||request()->routeIs('admin.boats.show')? 'active' : '' }}">
                        <a href="{{route('admin.boats.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.boats') }}</span>
                        </a>
                    </div>

                    @endcan
                </div>
            </div>
            @endif
            @if(auth()->user()->can('create_captain') ||auth()->user()->can('read_captain'))

            <div
                class="menu-item has-sub {{ request()->routeIs('admin.captain.index') ||request()->routeIs('admin.captain.create') ||request()->routeIs('admin.captain.edit') ||request()->routeIs('admin.captain.show')  ? 'active' : '' }}">
                <a href="#" class="menu-link ">
                    <span class="menu-icon"><i class="bi bi-people"></i></span>
                    <span class="menu-text">{{ __('admin.menu.captains_management') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu ">
                    @can('create_captain')
                    <div class="menu-item {{ request()->routeIs('admin.captain.create') ? 'active' : '' }}">
                        <a href="{{route('admin.captain.create')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.add_new_captain') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_captain')
                    <div class="menu-item {{ request()->routeIs('admin.captain.index') ? 'active' : '' }} ">
                        <a href="{{route('admin.captain.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.captains') }}</span>
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
            @endif
            @if(auth()->user()->can('create_crews') ||auth()->user()->can('read_crews'))

            <div
                class="menu-item has-sub {{ request()->routeIs('admin.crew.index') ||request()->routeIs('admin.crew.create') ||request()->routeIs('admin.crew.edit') ||request()->routeIs('admin.crew.show')  ? 'active' : '' }}">
                <a href="#" class="menu-link ">
                    <span class="menu-icon"><i class="bi bi-people"></i></span>
                    <span class="menu-text">{{ __('admin.menu.crews_management') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu ">
                    @can('create_crews')

                    <div class="menu-item {{ request()->routeIs('admin.crew.create') ? 'active' : '' }}">
                        <a href="{{route('admin.crew.create')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.add_new_crew') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_crews')
                    <div class="menu-item {{ request()->routeIs('admin.crew.index') ? 'active' : '' }} ">
                        <a href="{{route('admin.crew.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.crews') }}</span>
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
            @endif
            @if(auth()->user()->can('create_counter') ||auth()->user()->can('read_counter'))

            <div
                class="menu-item has-sub {{ request()->routeIs('admin.counter.index') ||request()->routeIs('admin.counter.create') ||request()->routeIs('admin.counter.edit') ||request()->routeIs('admin.counter.show')  ? 'active' : '' }}">
                <a href="#" class="menu-link ">
                    <span class="menu-icon"><i class="bi bi-people"></i></span>
                    <span class="menu-text">{{ __('admin.menu.counters_management') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu ">
                    @can('create_counter')
                    <div class="menu-item {{ request()->routeIs('admin.counter.create') ? 'active' : '' }}">
                        <a href="{{route('admin.counter.create')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.add_new_counter') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_counter')

                    <div class="menu-item {{ request()->routeIs('admin.counter.index') ? 'active' : '' }} ">
                        <a href="{{route('admin.counter.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.counters') }}</span>
                        </a>
                    </div>
                    @endcan

                </div>
            </div>
            @endif

            @if(auth()->user()->can('create_dalal') ||auth()->user()->can('read_dalal') || auth()->user()->can('read_commission_settings'))

            <div
                class="menu-item has-sub {{ request()->routeIs('admin.dalal.index') ||request()->routeIs('admin.dalal.create') ||request()->routeIs('admin.dalal.edit') ||request()->routeIs('admin.commission_settings.index') ||request()->routeIs('admin.dalal.show')  ? 'active' : '' }}">
                <a href="#" class="menu-link ">
                    <span class="menu-icon"><i class="bi bi-people"></i></span>
                    <span class="menu-text">{{ __('admin.menu.dalal_management') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu ">
                    @can('create_dalal')
                    <div class="menu-item {{ request()->routeIs('admin.dalal.create') ? 'active' : '' }}">
                        <a href="{{route('admin.dalal.create')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.add_new_dalal') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_dalal')
                    <div class="menu-item {{ request()->routeIs('admin.dalal.index') ? 'active' : '' }} ">
                        <a href="{{route('admin.dalal.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.dalals') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_commission_settings')
                    <div
                        class="menu-item {{ request()->routeIs('admin.commission_settings.index') ? 'active' : '' }} ">
                        <a href="{{route('admin.commission_settings.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.dalal_commissions') }}</span>
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
            @endif
            @can('create_fish')
            <div class="menu-item {{ request()->routeIs('admin.fish.index') ? 'active' : '' }}">
                <a href="{{route('admin.fish.index')}}" class="menu-link  ">
                    <span class="menu-icon"><i class="fas fa-fish"></i></span>
                    <span class="menu-text">{{ __('admin.menu.fish_types') }}</span>
                </a>
            </div>
            @endcan
            @can('create_payment_methods')
            <div class="menu-item {{ request()->routeIs('admin.payment_methods.index') ? 'active' : '' }}">
                <a href="{{route('admin.payment_methods.index')}}" class="menu-link  ">
                    <span class="menu-icon"><i class="bi bi bi-credit-card"></i></span>
                    <span class="menu-text">{{ __('admin.menu.payment_methods') }}</span>
                </a>
            </div>
            @endcan
            @can('create_customers')
            <div class="menu-item {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}">
                <a href="{{route('admin.customers.index')}}" class="menu-link  ">
                    <span class="menu-icon"><i class="bi bi-person-lines-fill"></i></span>
                    <span class="menu-text">{{ __('admin.menu.customers') }}</span>
                </a>
            </div>
            @endcan


            @if(auth()->user()->can('read_regions') ||auth()->user()->can('read_governorates') ||auth()->user()->can('read_cities') ||auth()->user()->can('read_ports') )

            <div
                class="menu-item has-sub {{ request()->routeIs('admin.regions.index') || request()->routeIs('admin.governorates.index')  ||request()->routeIs('admin.cities.index') ||request()->routeIs('admin.ports.index')  ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-compass"></i></span>
                    <span class="menu-text">{{ __('admin.menu.locations') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">
                    @can('read_regions')
                    <div class="menu-item {{ request()->routeIs('admin.regions.index') ? 'active' : '' }}">
                        <a href="{{route('admin.regions.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.regions') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_governorates')
                    <div
                        class="menu-item {{ request()->routeIs('admin.governorates.index') ? 'active' : '' }}">
                        <a href="{{route('admin.governorates.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.governorates') }}</span>
                        </a>
                    </div>
                    @endcan
                    {{-- @can('read_cities')--}}

                    {{-- <div class="menu-item {{ request()->routeIs('admin.cities.index') ? 'active' : '' }}">--}}
                    {{-- <a href="{{route('admin.cities.index')}}" class="menu-link">--}}
                    {{-- <span class="menu-text">{{ __('admin.menu.cities') }}</span>--}}
                    {{-- </a>--}}
                    {{-- </div>--}}
                    {{-- @endcan--}}
                    @can('read_ports')
                    <div class="menu-item {{ request()->routeIs('admin.ports.index') ? 'active' : '' }}">
                        <a href="{{route('admin.ports.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.ports') }}</span>
                        </a>
                    </div>
                    @endcan
                </div>
            </div>

            @endcan

            @can('read_categories')
            <div class="menu-item {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
                <a href="{{route('admin.categories.index')}}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi bi-boxes"></i></span>
                    <span class="menu-text">{{ __('admin.menu.categories') }}</span>
                </a>
            </div>
            @endcan
            @can('read_pages')
            <div class="menu-item {{ request()->routeIs('admin.pages.index') ? 'active' : '' }}">
                <a href="{{route('admin.pages.index')}}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi bi-collection"></i></span>
                    <span class="menu-text">{{ __('admin.menu.pages') }}</span>
                </a>
            </div>
            @endcan
            @can('read_notifications')
            <div class="menu-item  {{ request()->routeIs('admin.notifications') ? 'active' : '' }}">
                <a href="{{route('admin.notifications')}}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-bell-fill"></i></span>
                    <span class="menu-text">{{ __('admin.menu.notifications') }}</span>
                </a>
            </div>
            @endcan

            <div class="menu-item {{ request()->routeIs('admin.profile.index') ? 'active' : '' }}">
                <a href="{{route('admin.profile.index')}}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-person-square"></i></span>
                    <span class="menu-text">{{ __('admin.menu.profile') }}</span>
                </a>
            </div>

            @if(auth()->user()->can('read_admins') ||auth()->user()->can('create_admins') )
            <div
                class="menu-item has-sub {{ request()->routeIs('admin.admins.index') || request()->routeIs('admin.admins.create') || request()->routeIs('admin.admins.edit')  || request()->routeIs('admin.admins.show')    ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-person-check-fill"></i></span>
                    <span class="menu-text">{{ __('admin.menu.admins_management') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">
                    <div class="menu-item {{ request()->routeIs('admin.admins.create') ? 'active' : '' }}">
                        <a href="{{route('admin.admins.create')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.add_new_admin') }}</span>
                        </a>
                    </div>
                    <div class="menu-item {{ request()->routeIs('admin.admins.index') ? 'active' : '' }}">
                        <a href="{{route('admin.admins.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.admins') }}</span>
                        </a>
                    </div>


                </div>
            </div>
            @endif
            @if(auth()->user()->can('read_roles') ||auth()->user()->can('create_roles') )
            <div
                class="menu-item has-sub {{ request()->routeIs('admin.roles.index') || request()->routeIs('admin.roles.create') || request()->routeIs('admin.roles.edit')  || request()->routeIs('admin.roles.show')    ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-ui-checks-grid"></i></span>
                    <span class="menu-text">{{ __('admin.menu.roles_management') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>
                <div class="menu-submenu">
                    @can('create_roles')
                    <div class="menu-item {{ request()->routeIs('admin.roles.create') ? 'active' : '' }}">
                        <a href="{{route('admin.roles.create')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.add_new_role') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_roles')

                    <div class="menu-item {{ request()->routeIs('admin.roles.index') ? 'active' : '' }}">
                        <a href="{{route('admin.roles.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.roles') }}</span>
                        </a>
                    </div>
                    @endcan

                </div>
            </div>
            @endif
            @if(auth()->user()->can('read_settings') || auth()->user()->can('read_user_request') || auth()->user()->can('create_gov') || auth()->user()->can('read_gov') || auth()->user()->can('read_gov-roles') )
            <div
                class="menu-item has-sub {{ request()->routeIs('admin.settings.index')|| request()->routeIs('admin.user_request.index') || request()->routeIs('admin.gov.index') ||request()->routeIs('admin.gov.create') ||request()->routeIs('admin.gov.edit')  ||request()->routeIs('admin.gov.show')   || request()->routeIs('admin.gov-roles.index') ||request()->routeIs('admin.gov-roles.create') ||request()->routeIs('admin.gov-roles.edit')  ||request()->routeIs('admin.gov-roles.show')   ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-gear"></i></span>
                    <span class="menu-text">{{ __('admin.menu.settings') }}</span>
                    <span class="menu-caret"><b class="caret"></b></span>
                </a>

                <div class="menu-submenu">
                    @can('read_settings')
                    <div class="menu-item  {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
                        <a href="{{route('admin.settings.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.settings') }}</span>
                        </a>
                    </div>
                    @endcan

                    @can('read_user_request')

                    <div class="menu-item {{ request()->routeIs('admin.user_request.index') ? 'active' : '' }}">
                        <a href="{{route('admin.user_request.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.user_requests') }}</span>
                        </a>

                    </div>
                    @endcan

                    @can('create_gov')

                    <div class="menu-item {{ request()->routeIs('admin.gov.create') ? 'active' : '' }}">
                        <a href="{{route('admin.gov.create')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.add_new_employee') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_gov')

                    <div class="menu-item {{ request()->routeIs('admin.gov.index') ? 'active' : '' }} ">
                        <a href="{{route('admin.gov.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.gov_employees') }}</span>
                        </a>
                    </div>
                    @endcan
                    @can('read_gov-roles')
                    <div class="menu-item {{ request()->routeIs('admin.gov-roles.index') ? 'active' : '' }} ">
                        <a href="{{route('admin.gov-roles.index')}}" class="menu-link">
                            <span class="menu-text">{{ __('admin.menu.gov_roles') }}</span>
                        </a>
                    </div>
                    @endcan

                </div>


            </div>

            @endif
            @can('create_contacts')
            <div class="menu-item {{ request()->routeIs('admin.contacts.index') ? 'active' : '' }}">
                <a href="{{route('admin.contacts.index')}}" class="menu-link  ">
                    <span class="menu-icon"><i class="bi bi-messenger"></i></span>
                    <span class="menu-text">{{ __('admin.menu.contacts') }}</span>
                </a>
            </div>
            @endcan
            <div class="menu-item">
                <a href="{{ route('admin.logout') }}" class="menu-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <span class="menu-icon">
                        <i class="bi bi-box-arrow-right"></i> {{-- أيقونة تسجيل الخروج --}}
                    </span>
                    <span class="menu-text">{{ __('admin.menu.logout') }}</span>
                </a>
            </div>

            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>


        <!-- END menu -->

    </div>
    <!-- END scrollbar -->
</div>
