<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="admin-card max-w-3xl space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="admin-label">الاسم بالعربي</label>
            <input type="text" name="name_ar" value="{{ old('name_ar', $category?->name_ar) }}" class="admin-input" required>
            @error('name_ar') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="admin-label">الاسم بالإنجليزي</label>
            <input type="text" name="name_en" value="{{ old('name_en', $category?->name_en) }}" class="admin-input" required>
            @error('name_en') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="admin-label">Slug (اختياري)</label>
        <input type="text" name="slug" value="{{ old('slug', $category?->slug) }}" class="admin-input" dir="ltr">
        @error('slug') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="admin-label">الوصف بالعربي</label>
            <textarea name="description_ar" rows="4" class="admin-input">{{ old('description_ar', $category?->description_ar) }}</textarea>
        </div>
        <div>
            <label class="admin-label">الوصف بالإنجليزي</label>
            <textarea name="description_en" rows="4" class="admin-input">{{ old('description_en', $category?->description_en) }}</textarea>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="admin-label">الترتيب</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $category?->sort_order ?? 0) }}" class="admin-input" min="0">
        </div>
        <div class="flex items-end">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category?->is_active ?? true))>
                فئة نشطة
            </label>
        </div>
    </div>

    <div>
        <label class="admin-label">صورة الفئة</label>
        <input type="file" name="image" accept="image/*" class="admin-input" data-preview-input="#category-preview">
        <img id="category-preview" src="{{ $category?->image_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($category->image_path) : '' }}" alt="" class="mt-3 h-32 w-48 object-cover {{ $category?->image_path ? '' : 'hidden' }}">
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn-solid">حفظ</button>
        <a href="{{ route('admin.categories.index') }}" class="btn-gold-outline">رجوع</a>
    </div>
</form>
