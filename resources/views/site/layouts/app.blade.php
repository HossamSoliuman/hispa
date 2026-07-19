@php
    $siteUrl = rtrim(config('seo.site_url'), '/');
    $supportedLocales = array_keys(config('laravellocalization.supportedLocales', []));
    $pathSegments = array_values(array_filter(explode('/', trim(request()->path(), '/'))));

    if (! in_array($pathSegments[0] ?? null, $supportedLocales, true)) {
        array_unshift($pathSegments, app()->getLocale());
    }

    $currentPath = '/'.implode('/', $pathSegments);
    $canonicalUrl = $siteUrl.$currentPath;
    $seoTitle = trim($__env->yieldContent('title', __('site.meta.title')));
    $seoDescription = preg_replace('/\s+/', ' ', strip_tags($__env->yieldContent('description', __('site.meta.description'))));
    $seoImage = trim($__env->yieldContent('seo_image', config('seo.default_image_path')));
    $seoImageUrl = str_starts_with($seoImage, 'http') ? $seoImage : $siteUrl.'/'.ltrim($seoImage, '/');
    $robots = trim($__env->yieldContent('robots', 'index, follow'));
    $isIndexable = ! str_contains($robots, 'noindex');
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="robots" content="{{ $robots }}" />
    <meta name="theme-color" content="#155680" />
    <link rel="canonical" href="{{ $canonicalUrl }}" />
    @foreach ($supportedLocales as $locale)
        @php
            $localizedSegments = $pathSegments;

            if (in_array($localizedSegments[0] ?? null, $supportedLocales, true)) {
                $localizedSegments[0] = $locale;
            } else {
                array_unshift($localizedSegments, $locale);
            }
        @endphp
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ $siteUrl }}/{{ implode('/', $localizedSegments) }}" />
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $siteUrl }}/ar{{ count($pathSegments) > 1 ? '/'.implode('/', array_slice($pathSegments, 1)) : '' }}" />
    <script>
        document.documentElement.dataset.theme = localStorage.getItem('public_theme') === 'dark' ? 'dark' : 'light';
    </script>
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="{{ __('site.meta.title') }}" />
    <meta property="og:title" content="{{ $seoTitle }}" />
    <meta property="og:description" content="{{ $seoDescription }}" />
    <meta property="og:url" content="{{ $canonicalUrl }}" />
    <meta property="og:image" content="{{ $seoImageUrl }}" />
    <meta property="og:image:alt" content="{{ __('site.meta.title') }}" />
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_SA' : 'en_GB' }}" />
    @foreach (array_diff($supportedLocales, [app()->getLocale()]) as $locale)
        <meta property="og:locale:alternate" content="{{ $locale === 'ar' ? 'ar_SA' : 'en_GB' }}" />
    @endforeach
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $seoTitle }}" />
    <meta name="twitter:description" content="{{ $seoDescription }}" />
    <meta name="twitter:image" content="{{ $seoImageUrl }}" />
    <link rel="icon" href="{{ asset('site/assets/hisbah-huwat-logo.png') }}" type="image/png" />
    @if ($isIndexable)
        <script type="application/ld+json">{!! json_encode([
            '@'.'context' => 'https://schema.org',
            '@'.'type' => 'SoftwareApplication',
            'name' => __('site.meta.title'),
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'url' => $canonicalUrl,
            'inLanguage' => app()->getLocale(),
            'description' => $seoDescription,
            'image' => $seoImageUrl,
            'publisher' => [
                '@'.'type' => 'Organization',
                'name' => __('site.meta.title'),
                'url' => $siteUrl,
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    @if(app()->getLocale() === 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet" />
    @else
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="public-site min-h-screen text-ink antialiased">
    <a href="#main-content" class="fixed start-4 top-4 z-[100] -translate-y-24 rounded-lg bg-white px-4 py-3 text-sm font-bold text-ink shadow-xl focus:translate-y-0">
        {{ __('marketing.a11y.skip') }}
    </a>

    @include('site.partials.header')
    @include('site.partials.mobile-menu')

    <div id="main-content">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
