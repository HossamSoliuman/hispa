@php
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
@endphp
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
        <a href="{{ route('admin.dashboard') }}" class="brand-logo d-flex align-items-center">
            @php
                $locale = app()->getLocale();
                $logoPath = $locale === 'ar' ? asset('logo/arabic/main.png') : asset('logo/english/main.png');
            @endphp
            <img src="{{ $logoPath }}" alt="{{ __('admin.title') }}"
                style="height: 120px; width: auto; object-fit: contain;">
        </a>
    </div>
    <!-- END brand -->

    <!-- BEGIN menu -->
    <div class="menu">
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

        <div class="menu-item dropdown dropdown-mobile-full">
            <a href="#" data-bs-toggle="dropdown" data-bs-display="static" class="menu-link position-relative">
                <div class="menu-icon"><i class="bi bi-bell nav-icon"></i></div>
                <div class="menu-badge">
                    @php
                        $unreadCount = auth('admin')->user()->unreadNotifications()->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                    @endif
                </div>
            </a>

            <div class="dropdown-menu dropdown-menu-end mt-1 w-300px fs-11px pt-1"
                style="max-height: 400px; overflow-y: auto;" id="notificationsContainer">
                <h6 class="dropdown-header fs-10px mb-1">{{ __('admin.menu.notifications') }}</h6>
                <div class="dropdown-divider mt-1"></div>

                <div id="notificationsContent">
                    <div class="text-center p-2 text-muted">{{ __('admin.generated.load_notifications') }}</div>
                </div>

                <hr class="my-0">
                <div class="py-10px mb-n2 text-center">
                    <a href="#" id="readAllBtn"
                        class="text-decoration-none fw-bold">{{ __('admin.generated.read_all') }}</a>
                </div>
            </div>
        </div>

        <div class="menu-item dropdown dropdown-mobile-full">
            <a href="#" data-bs-toggle="dropdown" data-bs-display="static" class="menu-link">
                <div class="menu-img online">
                    <img src="{{ asset(auth('admin')->user()->logo ?? 'default-logo.png') }}" alt="Profile" height="60">
                </div>
                <div class="menu-text d-sm-block d-none w-auto">{{ auth('admin')->user()->name }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-end me-lg-3 fs-11px mt-1">
                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.profile.index') }}">
                    {{ __('admin.menu.profile') }}<img src="{{ asset(auth('admin')->user()->logo ?? 'default-logo.png') }}" alt="Admin Logo"
                        class="ms-auto" style="width: 20px; height: 20px;">
                </a>
            </div>
        </div>
        <div class="menu-item">
            <a class="menu-link" href="{{ route('admin.logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                title="{{ __('admin.generated.dropdown_logout') }}">
                <i class="bi bi-box-arrow-in-left px-4 mx-1 text-white ms-auto text-theme fs-20px my-n1"></i>
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>


    </div>
    <!-- END menu -->
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const readAllBtn = document.getElementById('readAllBtn');
        const container = document.getElementById('notificationsContainer');
        const content = document.getElementById('notificationsContent');
        const dropdown = document.querySelector('.menu-link');

        dropdown?.addEventListener('click', function() {
            fetchNotifications();
            setTimeout(() => markAllAsRead(), 1000);
        });

        readAllBtn?.addEventListener('click', function(e) {
            e.preventDefault();
            markAllAsRead();
        });

        container?.addEventListener('scroll', function() {
            if (container.scrollTop + container.clientHeight >= container.scrollHeight - 10) {
                markAllAsRead();
            }
        });

        function fetchNotifications() {
            fetch('{{ route('admin.notifications.fetch') }}')
                .then(res => res.json())
                .then(notifications => {
                    content.innerHTML = '';

                    if (notifications.length === 0) {
                        content.innerHTML =
                            '<div class="text-center text-muted p-2">{{ __('admin.generated.no_new_notifications') }}</div>';
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
                });
        }

        function markAllAsRead() {
            fetch('{{ route('admin.notifications.readAll') }}', {
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
                        if (badge) badge.remove();
                    }
                });
        }
    });
</script>
