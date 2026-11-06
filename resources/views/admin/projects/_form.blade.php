<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="admin-card space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="admin-label">العنوان بالعربي</label>
            <input type="text" name="title_ar" value="{{ old('title_ar', $project?->title_ar) }}" class="admin-input" required>
            @error('title_ar') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="admin-label">العنوان بالإنجليزي</label>
            <input type="text" name="title_en" value="{{ old('title_en', $project?->title_en) }}" class="admin-input" required>
            @error('title_en') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-3">
        <div>
            <label class="admin-label">الفئة</label>
            <select name="category_id" class="admin-input" required>
                <option value="">اختر فئة</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $project?->category_id) == $category->id)>{{ $category->name_ar }}</option>
                @endforeach
            </select>
            @error('category_id') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="admin-label">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $project?->slug) }}" class="admin-input" dir="ltr">
        </div>
        <div>
            <label class="admin-label">الترتيب</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $project?->sort_order ?? 0) }}" class="admin-input" min="0">
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-3">
        <div>
            <label class="admin-label">الموقع</label>
            <input type="text" name="location" value="{{ old('location', $project?->location) }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">السنة</label>
            <input type="text" name="year" value="{{ old('year', $project?->year) }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">العميل</label>
            <input type="text" name="client" value="{{ old('client', $project?->client) }}" class="admin-input">
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="admin-label">مقتطف عربي</label>
            <textarea name="excerpt_ar" rows="3" class="admin-input">{{ old('excerpt_ar', $project?->excerpt_ar) }}</textarea>
        </div>
        <div>
            <label class="admin-label">مقتطف إنجليزي</label>
            <textarea name="excerpt_en" rows="3" class="admin-input">{{ old('excerpt_en', $project?->excerpt_en) }}</textarea>
        </div>
        <div>
            <label class="admin-label">الوصف العربي</label>
            <textarea name="description_ar" rows="6" class="admin-input">{{ old('description_ar', $project?->description_ar) }}</textarea>
        </div>
        <div>
            <label class="admin-label">الوصف الإنجليزي</label>
            <textarea name="description_en" rows="6" class="admin-input">{{ old('description_en', $project?->description_en) }}</textarea>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="admin-label">Meta Title</label>
            <input type="text" name="meta_title" value="{{ old('meta_title', $project?->meta_title) }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Meta Description</label>
            <input type="text" name="meta_description" value="{{ old('meta_description', $project?->meta_description) }}" class="admin-input">
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="flex items-center justify-between gap-4 rounded-2xl border border-line bg-ivory/60 px-4 py-3">
            <div>
                <p class="text-sm text-brown">منشور</p>
                <p class="mt-0.5 text-xs text-brown/50">تشغيل = ظاهر في الموقع</p>
            </div>
            <label class="admin-toggle">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $project?->is_published))>
                <span class="admin-toggle-track" aria-hidden="true">
                    <span class="admin-toggle-knob"></span>
                    <span class="admin-toggle-on">ON</span>
                    <span class="admin-toggle-off">OFF</span>
                </span>
            </label>
        </div>
        <div class="flex items-center justify-between gap-4 rounded-2xl border border-line bg-ivory/60 px-4 py-3">
            <div>
                <p class="text-sm text-brown">مميز في الرئيسية</p>
                <p class="mt-0.5 text-xs text-brown/50">تشغيل = يظهر في الصفحة الرئيسية</p>
            </div>
            <label class="admin-toggle">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $project?->is_featured))>
                <span class="admin-toggle-track" aria-hidden="true">
                    <span class="admin-toggle-knob"></span>
                    <span class="admin-toggle-on">ON</span>
                    <span class="admin-toggle-off">OFF</span>
                </span>
            </label>
        </div>
    </div>

    <div>
        <label class="admin-label">إضافة صور للمعرض</label>
        <input type="file" name="gallery[]" accept="image/*" class="admin-input" multiple>
        <p class="mt-1 text-xs text-brown/50">الصور بتتحط في المعرض. تقدر تعيّن أي صورة كغلاف من تحت.</p>
        @error('gallery') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        @error('gallery.*') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn-solid">حفظ العمل</button>
        <a href="{{ route('admin.projects.index') }}" class="btn-gold-outline">رجوع</a>
        @if ($project)
            <a href="{{ route('projects.show', $project) }}" target="_blank" class="btn-gold-outline">معاينة</a>
        @endif
    </div>
</form>
