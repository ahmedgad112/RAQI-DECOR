@php
    $brandAr = $siteSettings['site_name_ar'] ?? 'راقي ديكور';
    $brandEn = $siteSettings['site_name_en'] ?? 'RAQI DECOR';
    $logoUrl = ! empty($siteSettings['logo_path'])
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings['logo_path'])
        : asset('images/logo.png');
    $transparentHeader = request()->routeIs('home') || request()->routeIs('projects.show');
@endphp

<header class="site-header {{ $transparentHeader ? 'is-transparent' : 'is-scrolled' }}" data-site-header>
    <div class="header-shell">
        <div class="flex h-[4.25rem] items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="header-brand">
                <img src="{{ $logoUrl }}" alt="{{ $brandEn }}" class="header-logo" width="44" height="44">
                <span class="text-start">
                    <span class="font-display block text-[0.95rem] tracking-[0.24em] md:text-lg">{{ $brandEn }}</span>
                    <span class="header-brand-sub mt-0.5 block text-[10px] tracking-wide md:text-[11px]">{{ $brandAr }}</span>
                </span>
            </a>

            <div class="flex items-center gap-4 md:gap-7">
                <nav class="hidden items-center gap-7 md:flex">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}">الرئيسية</a>
                    <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'is-active' : '' }}">أعمالنا</a>
                    <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'is-active' : '' }}">من نحن</a>
                </nav>

                <a href="{{ route('contact') }}" class="btn-gold-outline hidden !px-5 !py-2 text-[11px] tracking-[0.18em] md:inline-flex">تواصل</a>

                <button type="button" class="menu-toggle md:hidden" data-mobile-toggle aria-expanded="false" aria-controls="mobile-nav" aria-label="القائمة">
                    <span class="menu-line"></span>
                    <span class="menu-line mt-[9px]"></span>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-nav" class="mobile-panel" data-mobile-panel>
        <div class="space-y-1 px-6 py-6">
            <a href="{{ route('home') }}" class="block rounded-2xl px-3 py-3 text-lg hover:bg-mist">الرئيسية</a>
            <a href="{{ route('projects.index') }}" class="block rounded-2xl px-3 py-3 text-lg hover:bg-mist">أعمالنا</a>
            <a href="{{ route('about') }}" class="block rounded-2xl px-3 py-3 text-lg hover:bg-mist">من نحن</a>
            <a href="{{ route('contact') }}" class="btn-gold-outline mt-4 w-full">تواصل معنا</a>
        </div>
    </div>
</header>
