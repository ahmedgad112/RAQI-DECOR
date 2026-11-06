<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>دخول الإدارة — RAQI DECOR</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-ivory px-4">
    <div class="surface w-full max-w-md p-8 md:p-10">
        <img src="{{ asset('images/logo.png') }}" alt="RAQI DECOR" class="mx-auto h-14 w-14 object-contain" width="56" height="56">
        <p class="font-display mt-4 text-center text-2xl tracking-[0.22em]">RAQI DECOR</p>
        <p class="mt-3 text-center text-sm text-brown/55">تسجيل الدخول إلى لوحة التحكم</p>

        <form method="POST" action="{{ route('admin.login.store') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label class="admin-label" for="email">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="admin-input" required autofocus>
                @error('email') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="admin-label" for="password">كلمة المرور</label>
                <input id="password" type="password" name="password" class="admin-input" required>
            </div>
            <label class="flex items-center gap-2 text-sm text-brown/65">
                <input type="checkbox" name="remember" value="1" class="rounded border-line text-gold focus:ring-gold">
                تذكرني
            </label>
            <button type="submit" class="btn-solid w-full">دخول</button>
        </form>
    </div>
</body>
</html>
