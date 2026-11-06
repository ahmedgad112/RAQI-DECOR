<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'لوحة التحكم') — RAQI DECOR</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-ivory text-brown">
    <div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
        <aside class="admin-sidebar flex flex-col border-b border-white/5 bg-charcoal text-ivory lg:sticky lg:top-0 lg:h-screen lg:border-b-0 lg:border-e lg:border-white/5">
            <div class="admin-sidebar__brand shrink-0 px-6 py-7">
                <div class="flex items-center gap-3">
                    <span class="flex size-11 items-center justify-center rounded-2xl bg-white/5 ring-1 ring-white/10">
                        <img src="{{ asset('images/logo.png') }}" alt="RAQI DECOR" class="h-8 w-8 object-contain" width="32" height="32">
                    </span>
                    <div>
                        <p class="font-display text-lg tracking-[0.22em]">RAQI DECOR</p>
                        <p class="mt-1 text-[11px] tracking-wide text-beige/55">لوحة الإدارة</p>
                    </div>
                </div>
            </div>

            <nav class="admin-sidebar__nav flex-1 space-y-6 overflow-y-auto px-3 pb-4 text-sm">
                @php
                    $canDashboard = auth()->user()?->hasPermission(\App\Enums\AdminPermission::Dashboard);
                    $canProjects = auth()->user()?->hasPermission(\App\Enums\AdminPermission::Projects);
                    $canCategories = auth()->user()?->hasPermission(\App\Enums\AdminPermission::Categories);
                    $canMessages = auth()->user()?->hasPermission(\App\Enums\AdminPermission::Messages);
                    $canUsers = auth()->user()?->hasPermission(\App\Enums\AdminPermission::Users);
                    $canSettings = auth()->user()?->hasPermission(\App\Enums\AdminPermission::Settings);
                    $hasContent = $canProjects || $canCategories || $canMessages;
                    $hasSystem = $canUsers || $canSettings;
                @endphp

                @if ($canDashboard)
                    <div class="admin-sidebar__group">
                        <p class="admin-sidebar__label">الرئيسية</p>
                        <div class="space-y-1">
                            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                                <span class="admin-nav-link__dot"></span>
                                لوحة التحكم
                            </a>
                        </div>
                    </div>
                @endif

                @if ($hasContent)
                    <div class="admin-sidebar__group">
                        <p class="admin-sidebar__label">المحتوى</p>
                        <div class="space-y-1">
                            @if ($canProjects)
                                <a href="{{ route('admin.projects.index') }}" class="admin-nav-link {{ request()->routeIs('admin.projects.*') ? 'is-active' : '' }}">
                                    <span class="admin-nav-link__dot"></span>
                                    الأعمال
                                </a>
                            @endif
                            @if ($canCategories)
                                <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}">
                                    <span class="admin-nav-link__dot"></span>
                                    الفئات
                                </a>
                            @endif
                            @if ($canMessages)
                                <a href="{{ route('admin.messages.index') }}" class="admin-nav-link {{ request()->routeIs('admin.messages.*') ? 'is-active' : '' }}">
                                    <span class="admin-nav-link__dot"></span>
                                    الرسائل
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($hasSystem)
                    <div class="admin-sidebar__group">
                        <p class="admin-sidebar__label">النظام</p>
                        <div class="space-y-1">
                            @if ($canUsers)
                                <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                                    <span class="admin-nav-link__dot"></span>
                                    المستخدمون
                                </a>
                            @endif
                            @if ($canSettings)
                                <a href="{{ route('admin.settings.edit') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }}">
                                    <span class="admin-nav-link__dot"></span>
                                    إعدادات الموقع
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </nav>

            <div class="admin-sidebar__footer shrink-0 space-y-1 border-t border-white/8 px-3 py-4">
                <a href="{{ route('home') }}" target="_blank" class="admin-nav-link admin-nav-link--muted">
                    <span class="admin-nav-link__dot"></span>
                    عرض الموقع
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="admin-nav-link admin-nav-link--muted w-full text-start">
                        <span class="admin-nav-link__dot"></span>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        <div class="min-w-0">
            <header class="flex items-center justify-between border-b border-line bg-pure/80 px-5 py-4 backdrop-blur lg:px-8">
                <div>
                    <h1 class="text-lg font-medium">@yield('title', 'لوحة التحكم')</h1>
                    <p class="text-xs text-brown/45">{{ auth()->user()?->name }}</p>
                </div>
            </header>

            <main class="px-5 py-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-5 rounded-2xl border border-gold/30 bg-pure px-4 py-3 text-sm">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-5 rounded-2xl border border-red-200 bg-pure px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
