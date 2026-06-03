<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', __('site.meta.title'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
    <style>
        body { font-family: 'Almarai', sans-serif; }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin { animation: spin 1s linear infinite; }
    </style>
</head>
<body class="bg-white">
    <div id="pageLoader" class="fixed inset-0 bg-white/90 backdrop-blur-sm z-[100] hidden flex items-center justify-center">
        <div class="flex flex-col items-center gap-6">
            <div class="relative">
                <div class="w-16 h-16 border-4 border-[#3C74BE]/20 rounded-full"></div>
                <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-t-[#3C74BE] rounded-full animate-spin"></div>
            </div>
            <p class="text-[#3C74BE] font-semibold text-lg">{{ __('site.loading') }}</p>
        </div>
    </div>
    @yield('content')
    @stack('scripts')
    <script>
        document.getElementById('pageLoader')?.classList.add('hidden');
    </script>
</body>
</html>
