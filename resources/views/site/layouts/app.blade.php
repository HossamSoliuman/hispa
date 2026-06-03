<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', __('site.meta.title'))</title>
    <meta name="description" content="@yield('description', __('site.meta.description'))" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
    <link rel="stylesheet" href="{{ asset('site/css/global.css') }}" />
    <link rel="stylesheet" href="{{ asset('site/css/mobile-menu.css') }}" />
    @stack('styles')
    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin { animation: spin 1s linear infinite; }
    </style>
</head>
<body class="bg-white text-slate-900">
    @include('site.partials.header')
    @include('site.partials.mobile-menu')

    <div id="pageLoader" class="fixed inset-0 bg-white/90 backdrop-blur-sm z-[100] hidden flex items-center justify-center" aria-hidden="true">
        <div class="flex flex-col items-center gap-6">
            <div class="relative">
                <div class="w-16 h-16 border-4 border-[#3C74BE]/20 rounded-full"></div>
                <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-t-[#3C74BE] rounded-full animate-spin"></div>
            </div>
            <p class="text-[#3C74BE] font-semibold text-lg">{{ __('site.loading') }}</p>
        </div>
    </div>

    @yield('content')

    @include('site.partials.footer')

    <script src="{{ asset('site/js/navigation.js') }}"></script>
    <script src="{{ asset('site/js/mobile-menu.js') }}"></script>
    <script src="{{ asset('site/js/hero.js') }}"></script>
    <script src="{{ asset('site/js/pricing.js') }}"></script>
    <script src="{{ asset('site/js/scroll-observer.js') }}"></script>
    <script src="{{ asset('site/js/main.js') }}"></script>
    @stack('scripts')
    <script>
        document.getElementById('pageLoader')?.classList.add('hidden');
        var cy = document.getElementById('currentYear');
        if (cy) cy.textContent = new Date().getFullYear();
        document.getElementById('newsletterForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            var email = document.getElementById('newsletterEmail')?.value?.trim();
            if (email) {
                console.log('Newsletter:', email);
                alert('{{ __("site.footer.newsletter") }} - {{ app()->getLocale() === "ar" ? "شكراً لك! تم تسجيل بريدك." : "Thank you! Your email has been registered." }}');
                document.getElementById('newsletterEmail').value = '';
            }
        });
    </script>
</body>
</html>
