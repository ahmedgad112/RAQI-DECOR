@extends('layouts.admin')

@section('title', 'إضافة عمل')

@php
    $detailsWereFilled = collect([
        'title_en', 'location', 'year', 'client',
        'excerpt_ar', 'excerpt_en', 'description_ar', 'description_en',
        'meta_title', 'meta_description', 'slug', 'sort_order',
    ])->contains(fn ($field) => filled(old($field))) || old('is_featured');

    $detailsHaveErrors = $errors->hasAny([
        'title_en', 'location', 'year', 'client',
        'excerpt_ar', 'excerpt_en', 'description_ar', 'description_en',
        'meta_title', 'meta_description', 'slug', 'sort_order', 'is_featured',
    ]);
@endphp

@section('content')
<form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data" class="admin-card max-w-3xl space-y-5">
    @csrf

    <p class="text-sm text-brown/60">الأساسيات كافية للحفظ. التفاصيل اختيارية من القسم اللي تحت.</p>

    <div>
        <label class="admin-label">عنوان العمل</label>
        <input type="text" name="title_ar" value="{{ old('title_ar') }}" class="admin-input" required autofocus placeholder="مثال: فيلا التجمع">
        @error('title_ar') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="admin-label">الفئة</label>
        <select name="category_id" class="admin-input" required>
            <option value="">اختر فئة</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name_ar }}</option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="admin-label">الصور</label>
        <input type="file" name="gallery[]" accept="image/*" class="admin-input" multiple>
        <p class="mt-1 text-xs text-brown/50">أول صورة هتبقى الغلاف تلقائيًا.</p>
        @error('gallery') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        @error('gallery.*') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center justify-between gap-4 rounded-2xl border border-line bg-ivory/60 px-4 py-3">
        <div>
            <p class="text-sm text-brown">نشر العمل مباشرة</p>
            <p class="mt-0.5 text-xs text-brown/50">تشغيل = ظاهر في الموقع · إيقاف = مسودة</p>
        </div>
        <label class="admin-toggle">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', true))>
            <span class="admin-toggle-track" aria-hidden="true">
                <span class="admin-toggle-knob"></span>
                <span class="admin-toggle-on">ON</span>
                <span class="admin-toggle-off">OFF</span>
            </span>
        </label>
    </div>

    <details class="rounded-2xl border border-line bg-ivory/40" @if ($detailsWereFilled || $detailsHaveErrors) open @endif>
        <summary class="cursor-pointer list-none px-4 py-3 text-sm font-medium text-brown marker:content-none [&::-webkit-details-marker]:hidden">
            <span class="flex items-center justify-between gap-3">
                <span>تفاصيل إضافية</span>
                <span class="text-xs font-normal text-brown/45">عنوان إنجليزي · وصف · موقع · SEO</span>
            </span>
        </summary>

        <div class="space-y-5 border-t border-line px-4 py-5">
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="admin-label">العنوان بالإنجليزي</label>
                    <input type="text" name="title_en" value="{{ old('title_en') }}" class="admin-input" placeholder="اختياري — لو فاضي هيتنسخ من العربي">
                    @error('title_en') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="admin-label">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="admin-input" dir="ltr" placeholder="اختياري">
                    @error('slug') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-3">
                <div>
                    <label class="admin-label">الموقع</label>
                    <input type="text" name="location" value="{{ old('location') }}" class="admin-input">
                    @error('location') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="admin-label">السنة</label>
                    <input type="text" name="year" value="{{ old('year') }}" class="admin-input">
                    @error('year') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="admin-label">العميل</label>
                    <input type="text" name="client" value="{{ old('client') }}" class="admin-input">
                    @error('client') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="admin-label">مقتطف عربي</label>
                    <textarea name="excerpt_ar" rows="3" class="admin-input">{{ old('excerpt_ar') }}</textarea>
                    @error('excerpt_ar') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="admin-label">مقتطف إنجليزي</label>
                    <textarea name="excerpt_en" rows="3" class="admin-input">{{ old('excerpt_en') }}</textarea>
                    @error('excerpt_en') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="admin-label">الوصف العربي</label>
                    <textarea name="description_ar" rows="5" class="admin-input">{{ old('description_ar') }}</textarea>
                    @error('description_ar') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="admin-label">الوصف الإنجليزي</label>
                    <textarea name="description_en" rows="5" class="admin-input">{{ old('description_en') }}</textarea>
                    @error('description_en') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="admin-label">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="admin-input">
                    @error('meta_title') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="admin-label">Meta Description</label>
                    <input type="text" name="meta_description" value="{{ old('meta_description') }}" class="admin-input">
                    @error('meta_description') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="admin-label">الترتيب</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="admin-input" min="0">
                    @error('sort_order') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-line bg-pure px-4 py-3">
                    <div>
                        <p class="text-sm text-brown">مميز في الرئيسية</p>
                        <p class="mt-0.5 text-xs text-brown/50">تشغيل = يظهر في الصفحة الرئيسية</p>
                    </div>
                    <label class="admin-toggle">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured'))>
                        <span class="admin-toggle-track" aria-hidden="true">
                            <span class="admin-toggle-knob"></span>
                            <span class="admin-toggle-on">ON</span>
                            <span class="admin-toggle-off">OFF</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </details>

    <div class="flex gap-3">
        <button type="submit" class="btn-solid">حفظ العمل</button>
        <a href="{{ route('admin.projects.index') }}" class="btn-gold-outline">رجوع</a>
    </div>
</form>
@endsection
