<div class="menu-header">لوحة التحكم</div>
<div class="menu-item  {{ request()->routeIs('frontend.dashboard.user') ? 'active' : '' }}">
    <a href="{{route('frontend.dashboard.user')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-speedometer2"></i></span>
        <span class="menu-text">لوحة التحكم</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.dalal.stock.index') ? 'active' : '' }}">
    <a href="{{route('frontend.dalal.stock.index')}}" {{ request()->routeIs('frontend.dalal.stock.index') ? 'active' : '' }} class="menu-link">
        <span class="menu-icon"><i class="bi bi-boxes"></i></span>
        <span class="menu-text">المخزون</span>
    </a>
</div>

<!-- المبيعات -->
<div class="menu-item {{ request()->routeIs('frontend.dalal.sales.index') ? 'active' : '' }}">
    <a href="{{route('frontend.dalal.sales.index')}}" {{ request()->routeIs('frontend.dalal.sales.index') ? 'active' : '' }} class="menu-link">
        <span class="menu-icon"><i class="bi bi-cart"></i></span>
        <span class="menu-text">المبيعات</span>
    </a>
</div>

<!-- الصيادين -->
<div class="menu-item {{ request()->routeIs('frontend.dalal.owners.index') ? 'active' : '' }}">
    <a href="{{route('frontend.dalal.owners.index')}}" {{ request()->routeIs('frontend.dalal.owners.index') ? 'active' : '' }} class="menu-link">
        <span class="menu-icon"><i class="bi bi-people"></i></span>
        <span class="menu-text">الصيادين</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.dalal.customers.index') ? 'active' : '' }}">
    <a href="{{route('frontend.dalal.customers.index')}}" {{ request()->routeIs('frontend.dalal.customers.index') ? 'active' : '' }} class="menu-link">
        <span class="menu-icon"><i class="bi bi-person-lines-fill"></i></span>
        <span class="menu-text">العملاء</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.dalal.reports.index') ? 'active' : '' }}">
    <a href="{{route('frontend.dalal.reports.index')}}" {{ request()->routeIs('frontend.dalal.reports.index') ? 'active' : '' }} class="menu-link">
        <span class="menu-icon"><i class="bi bi-file-earmark-bar-graph"></i></span>
        <span class="menu-text">التقارير</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.dalal.settings.index') ? 'active' : '' }}">
    <a href="{{route('frontend.dalal.settings.index')}}" {{ request()->routeIs('frontend.dalal.settings.index') ? 'active' : '' }} class="menu-link">
        <span class="menu-icon"><i class="bi bi-gear-fill"></i></span>
        <span class="menu-text">الإعدادات</span>
    </a>
</div>