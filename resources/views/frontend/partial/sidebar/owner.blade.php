<div class="menu-header">لوحة التحكم</div>
<div class="menu-item  {{ request()->routeIs('frontend.dashboard.user') ? 'active' : '' }}">
    <a href="{{route('frontend.dashboard.user')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-speedometer2"></i></span>
        <span class="menu-text">لوحة التحكم</span>
    </a>
</div>


<div class="menu-item {{ request()->routeIs('frontend.trips.index') ? 'active' : '' }}">
    <a href="{{route('frontend.trips.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-map"></i></span>
        <span class="menu-text">إدارة الرحلات</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.boats.index') ? 'active' : '' }}">
    <a href="{{ route('frontend.boats.index') }}" class="menu-link">
        <span class="menu-icon">
            <svg xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                <path d="M19.875,21a1.174,1.174,0,0,1-.9-.466A9.338,9.338,0,0,0,22,13.5V12.438l-2-.7V7.5A3.5,3.5,0,0,0,16.5,4H15V2a2,2,0,0,0-2-2H11A2,2,0,0,0,9,2V4H7.5A3.5,3.5,0,0,0,4,7.5v4.233l-2,.705V13.5a9.34,9.34,0,0,0,3.02,7.029A1.145,1.145,0,0,1,4.125,21,1.173,1.173,0,0,1,3,20H0a4.171,4.171,0,0,0,4.125,4,4.147,4.147,0,0,0,2.63-.969,4.079,4.079,0,0,0,5.261.015,4.076,4.076,0,0,0,5.259-.015A4.084,4.084,0,0,0,24,20H21A1.158,1.158,0,0,1,19.875,21ZM7,7.5A.5.5,0,0,1,7.5,7h9a.5.5,0,0,1,.5.5v3.174L12,8.909,7,10.674ZM9.375,21A1.173,1.173,0,0,1,8.25,20l-.012-.828-.691-.443a6.147,6.147,0,0,1-2.475-4.193L10.5,12.62V20A1.158,1.158,0,0,1,9.375,21Zm5.25,0A1.173,1.173,0,0,1,13.5,20V12.62l5.428,1.916a6.161,6.161,0,0,1-2.472,4.192l-.706.434V20A1.158,1.158,0,0,1,14.625,21Z"></path>
            </svg>
        </span>
        <span class="menu-text">إدارة القوارب</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.stock.index') ? 'active' : '' }}">
    <a href="{{route('frontend.stock.index')}}" {{ request()->routeIs('frontend.stock.index') ? 'active' : '' }} class="menu-link">
        <span class="menu-icon"><i class="bi bi-boxes"></i></span>
        <span class="menu-text">المخزون</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.customers.index') ? 'active' : '' }}">
    <a href="{{route('frontend.customers.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-person-lines-fill"></i></span>
        <span class="menu-text">العملاء</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.catch-records.index') ? 'active' : '' }}">
    <a href="{{route('frontend.catch-records.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-journal-richtext"></i></span>
        <span class="menu-text">سجل المصيد</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.expenses.index') ? 'active' : '' }}">
    <a href="{{route('frontend.expenses.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-credit-card-2-back"></i></span>
        <span class="menu-text">النفقات</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.crew-check.index') ? 'active' : '' }}">
    <a href="{{route('frontend.crew-check.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-shield-check"></i></span>
        <span class="menu-text">فحص الطاقم</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.financial-reports.index') ? 'active' : '' }}">
    <a href="{{route('frontend.financial-reports.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-file-earmark-bar-graph"></i></span>
        <span class="menu-text">التقارير المالية</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.analytics.index') ? 'active' : '' }}">
    <a href="{{route('frontend.analytics.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-graph-up-arrow"></i></span>
        <span class="menu-text">التحليلات المتقدمة</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.payroll.index') ? 'active' : '' }}">
    <a href="{{route('frontend.payroll.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-cash-coin"></i></span>
        <span class="menu-text">الرواتب</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.documents.index') ? 'active' : '' }}">
    <a href="{{route('frontend.documents.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-folder2-open"></i></span>
        <span class="menu-text">الوثائق</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.data-entry.index') ? 'active' : '' }}">
    <a href="{{route('frontend.data-entry.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-pencil-square"></i></span>
        <span class="menu-text">إدخال البيانات</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.daily-report.index') ? 'active' : '' }}">
    <a href="{{route('frontend.daily-report.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-journal-album"></i></span>
        <span class="menu-text">تقرير يومي</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.vendors.index') ? 'active' : '' }}">
    <a href="{{route('frontend.vendors.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-briefcase"></i></span>
        <span class="menu-text">الموردون</span>
    </a>
</div>

<div class="menu-item">
    <a href="#" class="menu-link">
        <span class="menu-icon"><i class="bi bi-bell-fill"></i></span>
        <span class="menu-text">الإشعارات</span>
    </a>
</div>

<div class="menu-item {{ request()->routeIs('frontend.settings.index') ? 'active' : '' }}">
    <a href="{{route('frontend.settings.index')}}" class="menu-link">
        <span class="menu-icon"><i class="bi bi-gear-fill"></i></span>
        <span class="menu-text">الإعدادات</span>
    </a>
</div>