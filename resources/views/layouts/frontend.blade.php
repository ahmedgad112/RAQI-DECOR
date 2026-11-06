<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = $seoTitle ?? ($siteSettings['seo_title'] ?? config('app.name'));
        $pageDescription = $seoDescription ?? ($siteSettings['seo_description'] ?? '');
        $canonical = $canonicalUrl ?? url()->current();
        $ogImage = $ogImageUrl ?? (isset($siteSettings['hero_image']) && $siteSettings['hero_image']
            ? url(\Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings['hero_image']))
            : url('images/og-default.jpg'));
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ar_SA">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <meta name="theme-color" content="#F5F0E8">

    @stack('meta')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $schemaLogo = ! empty($siteSettings['logo_path'] ?? null)
            ? url(\Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings['logo_path']))
            : url('images/logo.png');
    @endphp
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'InteriorDesigner',
            'name' => $siteSettings['site_name_en'] ?? 'RAQI DECOR',
            'alternateName' => $siteSettings['site_name_ar'] ?? 'راقي ديكور',
            'url' => config('app.url'),
            'logo' => $schemaLogo,
            'image' => $schemaLogo,
            'email' => $siteSettings['contact_email'] ?? null,
            'telephone' => $siteSettings['contact_phone'] ?? null,
            'description' => $pageDescription,
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'SA',
                'addressLocality' => $siteSettings['contact_address_en'] ?? 'Saudi Arabia',
            ],
        ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
    </script>
</head>
<body class="min-h-screen bg-ivory text-brown">
    @include('partials.frontend.header')

    <main class="page-enter">
        @yield('content')
    </main>

    @include('partials.frontend.footer')
    @include('partials.frontend.success-popup')

    <div class="lightbox" data-lightbox role="dialog" aria-modal="true" aria-label="معاينة الصورة">
        <button type="button" class="absolute end-5 top-5 z-10 text-sm tracking-[0.18em] text-ivory/80 transition hover:text-ivory" data-lightbox-close aria-label="إغلاق">إغلاق</button>
        <img src="" alt="" data-lightbox-image>
    </div>
</body>
</html>
