<div id="header" class="app-header">

    <!-- BEGIN desktop-toggler -->
    <div class="desktop-toggler">
        <button type="button" class="menu-toggler" data-toggle-class="app-sidebar-collapsed"
            data-dismiss-class="app-sidebar-toggled" data-toggle-target=".app">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
    <!-- BEGIN desktop-toggler -->

    <!-- BEGIN mobile-toggler -->
    <div class="mobile-toggler">
        <button type="button" class="menu-toggler" data-toggle-class="app-sidebar-mobile-toggled"
            data-toggle-target=".app">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
    <!-- END mobile-toggler -->



    <!-- BEGIN brand -->
    <div class="brand">
        <a href="{{ route('owner.dashboard') }}" class="brand-logo d-flex align-items-center">
            <img src="{{ $platformLogoOnDarkUrl }}" alt="{{ $settings['title'] ?? __('owner.generated.item_bc71f8') }}"
                style="height: 120px; width: auto; object-fit: contain;">
        </a>
    </div>
    <!-- END brand -->

    <!-- BEGIN menu -->
    <div class="menu">
        {{-- Dark / Light mode toggle --}}
        @php $isDarkMode = request()->cookie('owner_theme') === 'dark'; @endphp
        <div class="menu-item">
            <a href="#" class="menu-link" onclick="event.preventDefault(); ownerToggleTheme();"
                title="{{ app()->getLocale() === 'ar' ? 'تبديل الوضع الليلي' : 'Toggle dark mode' }}">
                <div class="menu-icon">
                    <i class="bi {{ $isDarkMode ? 'bi-sun' : 'bi-moon-stars' }} nav-icon" id="themeToggleIcon"></i>
                </div>
            </a>
        </div>

        {{-- Language Dropdown --}}
        <div class="menu-item dropdown dropdown-mobile-full">
            <a href="#" data-bs-toggle="dropdown" data-bs-display="static" class="menu-link">
                <div class="menu-icon"><i class="bi bi-translate nav-icon"></i></div>
                <div class="menu-text d-sm-block d-none">
                    {{ LaravelLocalization::getCurrentLocaleNative() }}
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end me-lg-3 fs-11px mt-1">
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <a class="dropdown-item d-flex align-items-center"
                        href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                        {{ $properties['native'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{--        <div class="menu-item dropdown"> --}}
        {{--            <a href="#" data-toggle-class="app-header-menu-search-toggled" data-toggle-target=".app" class="menu-link"> --}}
        {{--                <div class="menu-icon"><i class="bi bi-search nav-icon"></i></div> --}}
        {{--            </a> --}}
        {{--        </div> --}}
       

        <div class="menu-item dropdown dropdown-mobile-full">
            <a href="#" data-bs-toggle="dropdown" data-bs-display="static" class="menu-link">
                <div class="menu-img online">
                    <img src="{{ asset(auth()->user()->logo) }}" alt="Profile" height="60">
                </div>
                <div class="menu-text d-sm-block d-none w-auto">{{ auth()->user()->name }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-end me-lg-3 fs-11px mt-1">
                <a class="dropdown-item d-flex align-items-center" href="{{ route('owner.profile.index') }}">
                    {{ __('owner.menu.profile') }}<img src="{{ auth()->user()->logo }}" alt="Admin Logo"
                        class="ms-auto" style="width: 20px; height: 20px;">
                </a>

                {{-- <div class="dropdown-divider"></div> --}}
                {{-- <a class="dropdown-item d-flex align-items-center"  href="{{ route('owner.logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">{{ __('owner.generated.logout') }}<i class="bi bi-toggle-off ms-auto text-theme fs-16px my-n1"></i></a>
                <form id="logout-form" action="{{ route('owner.logout') }}" method="POST" class="d-none">
                    @csrf
                </form> --}}
            </div>
        </div>
        <div class="menu-item">
            <a class="menu-link" href="{{ route('owner.logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                title="{{ __('owner.generated.dropdown_logout') }}">
                <i class="bi bi-box-arrow-in-left px-4 mx-1 text-white ms-auto text-theme fs-20px my-n1"></i>
            </a>
            <form id="logout-form" action="{{ route('owner.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>


    </div>
    <!-- END menu -->

    <!-- BEGIN menu-search -->
    {{--    <form class="menu-search" method="POST" name="header_search_form"> --}}
    {{--        <div class="menu-search-container"> --}}
    {{--            <div class="menu-search-icon"><i class="bi bi-search"></i></div> --}}
    {{--            <div class="menu-search-input"> --}}
    {{--                <input type="text" class="form-control form-control-lg" placeholder="Search menu..."> --}}
    {{--            </div> --}}
    {{--            <div class="menu-search-icon"> --}}
    {{--                <a href="#" data-toggle-class="app-header-menu-search-toggled" data-toggle-target=".app"><i class="bi bi-x-lg"></i></a> --}}
    {{--            </div> --}}
    {{--        </div> --}}
    {{--    </form> --}}
    <!-- END menu-search -->
</div>
<script>
    function ownerToggleTheme() {
        var html = document.documentElement;
        var next = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        document.cookie = 'owner_theme=' + next + ';path=/;max-age=31536000;SameSite=Lax';
        location.reload();
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const readAllBtn = document.getElementById('readAllBtn');
        const container = document.getElementById('notificationsContainer');
        const content = document.getElementById('notificationsContent');
        const dropdown = document.querySelector('.menu-link');

        // جلب الإشعارات عند الفتح
        dropdown?.addEventListener('click', function() {
            fetchNotifications();
            setTimeout(() => markAllAsRead(), 1000); // تعليمها كمقروءة بعد ثانية
        });

        // عند الضغط على "{{ __('owner.generated.read_all') }}"
        readAllBtn?.addEventListener('click', function(e) {
            e.preventDefault();
            markAllAsRead();
        });

        // عند النزول إلى آخر القائمة
        container?.addEventListener('scroll', function() {
            if (container.scrollTop + container.clientHeight >= container.scrollHeight - 10) {
                markAllAsRead();
            }
        });

        function fetchNotifications() {
            fetch('{{ route('owner.notifications.fetch') }}')
                .then(res => res.json())
                .then(notifications => {
                    content.innerHTML = '';

                    if (notifications.length === 0) {
                        content.innerHTML =
                            '<div class="text-center text-muted p-2">{{ __('owner.generated.no_new_notifications') }}</div>';
                        return;
                    }

                    notifications.forEach(notification => {
                        const item = `
                            <div class="fw-bold">${notification.title}</div>
                            <small class="text-muted">${notification.body}</small>
                            <div class="text-muted fs-10px mt-1">${notification.created_at}</div>
                        <div class="dropdown-divider my-0"></div>
                    `;
                        content.insertAdjacentHTML('beforeend', item);
                    });

                    // notifications.forEach(notification => {
                    //     const item = `
                    //     <a href="/owner.notifications/${notification.id}/read" class="dropdown-item text-wrap py-10px mb-0">
                    //         <div class="fw-bold">${notification.title}</div>
                    //         <small class="text-muted">${notification.body}</small>
                    //         <div class="text-muted fs-10px mt-1">${notification.created_at}</div>
                    //     </a>
                    //     <div class="dropdown-divider my-0"></div>
                    // `;
                    //     content.insertAdjacentHTML('beforeend', item);
                    // });
                });
        }

        function markAllAsRead() {
            fetch('{{ route('owner.notifications.readAll') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const badge = document.querySelector('.menu-badge span');
                        if (badge) badge.remove(); // إزالة شارة الإشعارات
                    }
                });
        }
    });
</script>
