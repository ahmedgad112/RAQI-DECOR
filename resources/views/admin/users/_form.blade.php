@php
    $selectedPermissions = old('permissions', $user?->normalizedPermissions() ?? \App\Enums\AdminPermission::values());
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="admin-card max-w-3xl space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <h2 class="text-sm font-medium text-brown/70">بيانات الحساب</h2>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="admin-label">الاسم</label>
            <input type="text" name="name" value="{{ old('name', $user?->name) }}" class="admin-input" required>
            @error('name') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="admin-label">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ old('email', $user?->email) }}" class="admin-input" dir="ltr" required>
            @error('email') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="admin-label">{{ $user ? 'كلمة المرور (اختياري)' : 'كلمة المرور' }}</label>
            <input type="password" name="password" class="admin-input" dir="ltr" @unless($user) required @endunless>
            @error('password') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="admin-label">تأكيد كلمة المرور</label>
            <input type="password" name="password_confirmation" class="admin-input" dir="ltr" @unless($user) required @endunless>
        </div>
    </div>

    <div class="border-t border-beige/70 pt-5">
        <h2 class="text-sm font-medium text-brown/70">الصلاحيات</h2>
        <p class="mt-1 text-xs text-brown/45">حدد دخول لوحة التحكم ثم اختر الأقسام المسموح بها.</p>
    </div>

    <div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_admin" value="1" id="is-admin-toggle" @checked(old('is_admin', $user?->is_admin ?? false))>
            دخول لوحة التحكم
        </label>
        @error('is_admin') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
    </div>

    <div id="permissions-panel" class="space-y-3 rounded-2xl border border-beige/80 bg-ivory/50 p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-brown/70">أقسام الموقع</p>
            <button type="button" id="select-all-permissions" class="text-xs text-gold">تحديد الكل</button>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            @foreach ($permissionOptions as $value => $label)
                <label class="flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $value }}"
                        class="permission-checkbox"
                        @checked(in_array($value, $selectedPermissions, true))
                    >
                    {{ $label }}
                </label>
            @endforeach
        </div>
        @error('permissions') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
    </div>

    <div class="border-t border-beige/70 pt-5">
        <h2 class="text-sm font-medium text-brown/70">البروفايل</h2>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="admin-label">المسمى الوظيفي</label>
            <input type="text" name="job_title" value="{{ old('job_title', $user?->profile?->job_title) }}" class="admin-input">
            @error('job_title') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="admin-label">رقم الهاتف</label>
            <input type="text" name="phone" value="{{ old('phone', $user?->profile?->phone) }}" class="admin-input" dir="ltr">
            @error('phone') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="admin-label">نبذة مختصرة</label>
        <textarea name="bio" rows="4" class="admin-input">{{ old('bio', $user?->profile?->bio) }}</textarea>
        @error('bio') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="admin-label">صورة البروفايل</label>
        <input type="file" name="avatar" accept="image/*" class="admin-input" data-preview-input="#avatar-preview">
        @error('avatar') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror

        <img
            id="avatar-preview"
            src="{{ $user?->profile?->avatarUrl() ?: '' }}"
            alt=""
            class="mt-3 h-24 w-24 rounded-full object-cover {{ $user?->profile?->avatar_path ? '' : 'hidden' }}"
        >

        @if ($user?->profile?->avatar_path)
            <label class="mt-3 flex items-center gap-2 text-sm">
                <input type="checkbox" name="remove_avatar" value="1" @checked(old('remove_avatar'))>
                حذف الصورة الحالية
            </label>
        @endif
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn-solid">حفظ</button>
        <a href="{{ route('admin.users.index') }}" class="btn-gold-outline">رجوع</a>
    </div>
</form>

<script>
    (() => {
        const toggle = document.getElementById('is-admin-toggle');
        const panel = document.getElementById('permissions-panel');
        const selectAll = document.getElementById('select-all-permissions');
        const boxes = () => Array.from(document.querySelectorAll('.permission-checkbox'));

        const syncPanel = () => {
            const enabled = toggle?.checked;
            panel.style.opacity = enabled ? '1' : '0.45';
            boxes().forEach((box) => { box.disabled = !enabled; });
            if (selectAll) selectAll.disabled = !enabled;
        };

        toggle?.addEventListener('change', syncPanel);
        selectAll?.addEventListener('click', () => {
            boxes().forEach((box) => { box.checked = true; });
        });
        syncPanel();
    })();
</script>
