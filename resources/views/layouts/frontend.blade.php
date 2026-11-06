<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        /** @var \App\Services\SeoService $seo */
        $seo = app(\App\Services\SeoService::class);
        $pageTitle = $seoMeta['title'] ?? ($seoTitle ?? ($siteSettings['seo_title'] ?? config('app.name')));
        $pageDescription = $seoMeta['description'] ?? ($seoDescription ?? ($siteSettings['seo_description'] ?? ''));
        $canonical = $seoMeta['canonical'] ?? ($canonicalUrl ?? url()->current());
        $ogImage = $seoMeta['image'] ?? ($ogImageUrl ?? $seo->defaultOgImage($siteSettings));
        $ogImageAlt = $seoMeta['imageAlt'] ?? ($siteSettings['site_name_ar'] ?? 'RAQI DECOR');
        $ogType = $seoMeta['type'] ?? 'website';
        $robots = $seoMeta['robots'] ?? 'index,follow,max-image-preview:large';
        $siteName = $seoMeta['siteName'] ?? ($siteSettings['site_name_en'] ?? 'RAQI DECOR');
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="author" content="{{ $siteName }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="alternate" hreflang="ar" href="{{ $canonical }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonical }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="ar_SA">
    <meta property="og:locale:alternate" content="en_US">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="{{ $ogImageAlt }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="twitter:image:alt" content="{{ $ogImageAlt }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="googlebot" content="{{ $robots }}">
    <meta name="theme-color" content="#F5F0E8">

    @stack('meta')
    @yield('preload')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <script type="application/ld+json">
        {!! json_encode(
            $seo->organizationSchema($siteSettings, $socialLinks ?? [], $mapExternalUrl ?? null),
            JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT
        ) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode(
            $seo->websiteSchema($siteSettings),
            JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT
        ) !!}
    </script>
</head>
<body class="min-h-screen bg-ivory text-brown">
    @include('partials.frontend.header')

    <main class="page-enter">
        @yield('content')
    </main>

    @include('partials.frontend.footer')
    @include('partials.frontend.contact-fab')
    @include('partials.frontend.success-popup')

    <div class="lightbox" data-lightbox role="dialog" aria-modal="true" aria-label="معاينة الصورة">
        <button type="button" class="absolute end-5 top-5 z-10 text-sm tracking-[0.18em] text-ivory/80 transition hover:text-ivory" data-lightbox-close aria-label="إغلاق">إغلاق</button>
        <img src="" alt="" data-lightbox-image>
    </div>
</body>
</html>
