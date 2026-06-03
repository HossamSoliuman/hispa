<div id="header" class="app-header">

    <!-- BEGIN desktop-toggler -->
    <div class="desktop-toggler">
        <button type="button" class="menu-toggler" data-toggle-class="app-sidebar-collapsed" data-dismiss-class="app-sidebar-toggled" data-toggle-target=".app">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
    <!-- BEGIN desktop-toggler -->

    <!-- BEGIN mobile-toggler -->
    <div class="mobile-toggler">
        <button type="button" class="menu-toggler" data-toggle-class="app-sidebar-mobile-toggled" data-toggle-target=".app">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
    <!-- END mobile-toggler -->



    <!-- BEGIN brand -->
    <div class="brand">
        <a href="{{route('frontend.dashboard.user')}}" class="brand-logo">
					<span class="brand-img">
<span class="brand-img-text text-theme">
    <img src="{{ isset($settings['logo']) ? asset($settings['logo']) : asset('default-logo.png') }}"
         alt="{{ $settings['title'] ?? 'حسبة' }}">
</span>
					</span>
            <span class="brand-text">{{ $settings['title'] ?? 'حسبة' }}</span>
        </a>
    </div>
    <!-- END brand -->

    <!-- BEGIN menu -->
    <div class="menu">
{{--        <div class="menu-item dropdown">--}}
{{--            <a href="#" data-toggle-class="app-header-menu-search-toggled" data-toggle-target=".app" class="menu-link">--}}
{{--                <div class="menu-icon"><i class="bi bi-search nav-icon"></i></div>--}}
{{--            </a>--}}
{{--        </div>--}}
        <div class="menu-item dropdown dropdown-mobile-full">
            <a href="#" data-bs-toggle="dropdown" data-bs-display="static" class="menu-link position-relative">
                <div class="menu-icon"><i class="bi bi-bell nav-icon"></i></div>
                <div class="menu-badge">
                    @php
                        $unreadCount = auth()->user()->unreadNotifications()->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                    @endif
                </div>
            </a>

            <div class="dropdown-menu dropdown-menu-end mt-1 w-300px fs-11px pt-1"
                 style="max-height: 400px; overflow-y: auto;" id="notificationsContainer">
                <h6 class="dropdown-header fs-10px mb-1">الإشعارات</h6>
                <div class="dropdown-divider mt-1"></div>

                <div id="notificationsContent">
                    <div class="text-center p-2 text-muted">تحميل الإشعارات...</div>
                </div>

                <hr class="my-0">
                <div class="py-10px mb-n2 text-center">
                    <a href="#" id="readAllBtn" class="text-decoration-none fw-bold">قراءة الكل</a>
                </div>
            </div>
        </div>

        <div class="menu-item dropdown dropdown-mobile-full">
            <a href="#" data-bs-toggle="dropdown" data-bs-display="static" class="menu-link">
                <div class="menu-img online">
                    <img src="{{asset(auth()->user()->logo)}}" alt="Profile" height="60">
                </div>
                <div class="menu-text d-sm-block d-none w-170px">{{auth()->user()->name}}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-end me-lg-3 fs-11px mt-1">
                <a class="dropdown-item d-flex align-items-center" href="{{route('frontend.profile.index')}}">
                    الملف الشخصي                     <img src="{{ auth()->user()->logo }}" alt="Admin Logo" class="ms-auto" style="width: 20px; height: 20px;">
                </a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item d-flex align-items-center"  href="{{ route('frontend.logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">تسجيل خروج<i class="bi bi-toggle-off ms-auto text-theme fs-16px my-n1"></i></a>
                <form id="logout-form" action="{{ route('frontend.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
    <!-- END menu -->

    <!-- BEGIN menu-search -->
{{--    <form class="menu-search" method="POST" name="header_search_form">--}}
{{--        <div class="menu-search-container">--}}
{{--            <div class="menu-search-icon"><i class="bi bi-search"></i></div>--}}
{{--            <div class="menu-search-input">--}}
{{--                <input type="text" class="form-control form-control-lg" placeholder="Search menu...">--}}
{{--            </div>--}}
{{--            <div class="menu-search-icon">--}}
{{--                <a href="#" data-toggle-class="app-header-menu-search-toggled" data-toggle-target=".app"><i class="bi bi-x-lg"></i></a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </form>--}}
    <!-- END menu-search -->
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const readAllBtn = document.getElementById('readAllBtn');
        const container = document.getElementById('notificationsContainer');
        const content = document.getElementById('notificationsContent');
        const dropdown = document.querySelector('.menu-link');

        // جلب الإشعارات عند الفتح
        dropdown?.addEventListener('click', function () {
            fetchNotifications();
            setTimeout(() => markAllAsRead(), 1000); // تعليمها كمقروءة بعد ثانية
        });

        // عند الضغط على "قراءة الكل"
        readAllBtn?.addEventListener('click', function (e) {
            e.preventDefault();
            markAllAsRead();
        });

        // عند النزول إلى آخر القائمة
        container?.addEventListener('scroll', function () {
            if (container.scrollTop + container.clientHeight >= container.scrollHeight - 10) {
                markAllAsRead();
            }
        });

        function fetchNotifications() {
            fetch('{{ route('frontend.notifications.fetch') }}')
                .then(res => res.json())
                .then(notifications => {
                    content.innerHTML = '';

                    if (notifications.length === 0) {
                        content.innerHTML = '<div class="text-center text-muted p-2">لا توجد إشعارات جديدة</div>';
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
                    //     <a href="/frontend/notifications/${notification.id}/read" class="dropdown-item text-wrap py-10px mb-0">
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
            fetch('{{ route('frontend.notifications.readAll') }}', {
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

