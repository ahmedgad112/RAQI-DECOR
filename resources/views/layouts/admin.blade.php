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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ivory text-brown">
    <div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
        <aside class="border-b border-charcoal/10 bg-charcoal text-ivory lg:min-h-screen lg:border-b-0">
            <div class="px-6 py-7">
                <img src="{{ asset('images/logo.png') }}" alt="RAQI DECOR" class="h-10 w-10 object-contain" width="40" height="40">
                <p class="font-display mt-3 text-lg tracking-[0.22em]">RAQI DECOR</p>
                <p class="mt-2 text-xs text-beige/70">Modern Admin</p>
            </div>
            <nav class="space-y-1 px-3 pb-8 text-sm">
                @if (auth()->user()?->hasPermission(\App\Enums\AdminPermission::Dashboard))
                    <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'bg-gold/15 text-gold' : 'hover:bg-white/5' }}">لوحة التحكم</a>
                @endif
                @if (auth()->user()?->hasPermission(\App\Enums\AdminPermission::Projects))
                    <a href="{{ route('admin.projects.index') }}" class="admin-nav-link {{ request()->routeIs('admin.projects.*') ? 'bg-gold/15 text-gold' : 'hover:bg-white/5' }}">الأعمال</a>
                @endif
                @if (auth()->user()?->hasPermission(\App\Enums\AdminPermission::Categories))
                    <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'bg-gold/15 text-gold' : 'hover:bg-white/5' }}">الفئات</a>
                @endif
                @if (auth()->user()?->hasPermission(\App\Enums\AdminPermission::Messages))
                    <a href="{{ route('admin.messages.index') }}" class="admin-nav-link {{ request()->routeIs('admin.messages.*') ? 'bg-gold/15 text-gold' : 'hover:bg-white/5' }}">الرسائل</a>
                @endif
                @if (auth()->user()?->hasPermission(\App\Enums\AdminPermission::Users))
                    <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'bg-gold/15 text-gold' : 'hover:bg-white/5' }}">المستخدمون</a>
                @endif
                @if (auth()->user()?->hasPermission(\App\Enums\AdminPermission::Settings))
                    <a href="{{ route('admin.settings.edit') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'bg-gold/15 text-gold' : 'hover:bg-white/5' }}">إعدادات الموقع</a>
                @endif
                <a href="{{ route('home') }}" target="_blank" class="admin-nav-link hover:bg-white/5">عرض الموقع</a>
                <form method="POST" action="{{ route('admin.logout') }}" class="pt-3">
                    @csrf
                    <button type="submit" class="admin-nav-link w-full text-start hover:bg-white/5">تسجيل الخروج</button>
                </form>
            </nav>
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
