<div id="sidebar" class="app-sidebar">
    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
        <div class="menu">

            @role('owner')
            @include('frontend.partial.sidebar.owner')
            @endrole

            @role('dalal')
            @include('frontend.partial.sidebar.dalal')
            @endrole



            <div class="menu-item  {{ request()->routeIs('frontend.profile.index') ? 'active' : '' }}">
                <a href="{{route('frontend.profile.index')}}" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-person-square"></i></span>
                    <span class="menu-text">الملف الشخصي</span>
                </a>
            </div>

            <div class="menu-item">
                <form method="POST" action="{{ route('frontend.logout') }}">
                    @csrf
                    <button type="submit" class="menu-link bg-transparent border-0">
                        <span class="menu-icon"><i class="bi bi-box-arrow-right"></i></span>
                        <span class="menu-text">تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>