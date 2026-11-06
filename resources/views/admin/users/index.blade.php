@extends('layouts.admin')

@section('title', 'المستخدمون')

@section('content')
<div class="mb-5 flex items-center justify-between gap-3">
    <p class="text-sm text-brown/60">إدارة المستخدمين والبروفايلات</p>
    <a href="{{ route('admin.users.create') }}" class="btn-solid !py-2 text-xs">إضافة مستخدم</a>
</div>

<div class="overflow-x-auto border border-beige bg-pure">
    <table class="min-w-full text-sm">
        <thead class="bg-ivory text-xs text-brown/60">
            <tr>
                <th class="px-4 py-3 text-start">المستخدم</th>
                <th class="px-4 py-3 text-start">البريد</th>
                <th class="px-4 py-3 text-start">المسمى</th>
                <th class="px-4 py-3 text-start">الهاتف</th>
                <th class="px-4 py-3 text-start">الدور</th>
                <th class="px-4 py-3 text-start">الصلاحيات</th>
                <th class="px-4 py-3 text-start">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr class="border-t border-beige/70">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if ($user->profile?->avatar_path)
                                <img src="{{ $user->profile->avatarUrl() }}" alt="" class="h-10 w-10 rounded-full object-cover">
                            @else
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-ivory text-xs text-brown/50">{{ mb_substr($user->name, 0, 1) }}</span>
                            @endif
                            <div>
                                <p>{{ $user->name }}</p>
                                @if ($user->profile?->bio)
                                    <p class="mt-0.5 text-xs text-brown/45">{{ \Illuminate\Support\Str::limit($user->profile->bio, 40) }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 font-sans-en" dir="ltr">{{ $user->email }}</td>
                    <td class="px-4 py-3">{{ $user->profile?->job_title ?: '—' }}</td>
                    <td class="px-4 py-3 font-sans-en" dir="ltr">{{ $user->profile?->phone ?: '—' }}</td>
                    <td class="px-4 py-3">{{ $user->is_admin ? 'أدمن' : 'مستخدم' }}</td>
                    <td class="px-4 py-3 text-xs text-brown/60">
                        @if (! $user->is_admin)
                            —
                        @elseif (count($user->normalizedPermissions()) === count(\App\Enums\AdminPermission::cases()))
                            كل الصلاحيات
                        @else
                            {{ collect($user->normalizedPermissions())->map(fn ($permission) => \App\Enums\AdminPermission::from($permission)->label())->join('، ') }}
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-3">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-gold">تعديل</a>
                            @unless ($user->is(auth()->user()))
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('حذف المستخدم؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700">حذف</button>
                                </form>
                            @endunless
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $users->links() }}</div>
@endsection
