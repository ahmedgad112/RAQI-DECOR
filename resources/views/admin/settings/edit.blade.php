@extends('layouts.admin')

@section('title', 'إعدادات الموقع')

@section('content')
@php
    $oldLinks = old('social_links');
    $linkRows = is_array($oldLinks) ? $oldLinks : $socialLinks;
    if ($linkRows === []) {
        $linkRows = [['label' => '', 'url' => '']];
    }
@endphp

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <section class="admin-card space-y-5">
        <div>
            <h2 class="text-lg font-medium text-brown">الهوية العامة</h2>
            <p class="mt-1 text-sm text-brown/55">اسم الموقع والشعار الظاهري في الواجهة.</p>
        </div>
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="admin-label">اسم الموقع عربي</label>
                <input type="text" name="site_name_ar" value="{{ old('site_name_ar', $settings['site_name_ar'] ?? '') }}" class="admin-input" required>
            </div>
            <div>
                <label class="admin-label">اسم الموقع إنجليزي</label>
                <input type="text" name="site_name_en" value="{{ old('site_name_en', $settings['site_name_en'] ?? '') }}" class="admin-input" required>
            </div>
            <div>
                <label class="admin-label">الشعار عربي</label>
                <input type="text" name="tagline_ar" value="{{ old('tagline_ar', $settings['tagline_ar'] ?? '') }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">الشعار إنجليزي</label>
                <input type="text" name="tagline_en" value="{{ old('tagline_en', $settings['tagline_en'] ?? '') }}" class="admin-input">
            </div>
            <div class="md:col-span-2">
                <label class="admin-label">الشعار / Logo</label>
                @php
                    $logoPreview = ! empty($settings['logo_path'])
                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($settings['logo_path'])
                        : asset('images/logo.png');
                @endphp
                <div class="mb-3 flex items-center gap-4 rounded-2xl border border-line bg-mist/40 p-4">
                    <img src="{{ $logoPreview }}" alt="Logo preview" class="h-14 w-14 object-contain" width="56" height="56">
                    <p class="text-sm text-brown/60">الشعار الحالي. ارفع صورة جديدة لاستبداله.</p>
                </div>
                <input type="file" name="logo" accept="image/*" class="admin-input">
            </div>
        </div>
    </section>

    @php
        $heroImageUrl = ! empty($settings['hero_image'])
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($settings['hero_image'])
            : '';
        $heroPosition = old('hero_image_position', $settings['hero_image_position'] ?? 'center');
    @endphp

    <section class="admin-card space-y-5">
        <div>
            <h2 class="text-lg font-medium text-brown">قسم Hero</h2>
            <p class="mt-1 text-sm text-brown/55">العنوان الرئيسي وصورة الغلاف في الصفحة الرئيسية.</p>
        </div>
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="admin-label">عنوان Hero عربي</label>
                <input type="text" name="hero_title_ar" value="{{ old('hero_title_ar', $settings['hero_title_ar'] ?? '') }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">عنوان Hero إنجليزي</label>
                <input type="text" name="hero_title_en" value="{{ old('hero_title_en', $settings['hero_title_en'] ?? '') }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">وصف Hero عربي</label>
                <textarea name="hero_subtitle_ar" rows="3" class="admin-input">{{ old('hero_subtitle_ar', $settings['hero_subtitle_ar'] ?? '') }}</textarea>
            </div>
            <div>
                <label class="admin-label">وصف Hero إنجليزي</label>
                <textarea name="hero_subtitle_en" rows="3" class="admin-input">{{ old('hero_subtitle_en', $settings['hero_subtitle_en'] ?? '') }}</textarea>
            </div>
        </div>

        <div class="rounded-2xl border border-line bg-mist/40 p-4 md:p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h3 class="text-base font-medium text-brown">صورة Hero</h3>
                    <p class="mt-1 text-sm text-brown/55">ارفع صورة أفقية عالية الجودة. الأنسب: 1920×1080 أو أكبر (JPG / PNG / WebP).</p>
                </div>
            </div>

            <div class="mt-4 overflow-hidden rounded-2xl border border-line bg-brown/5">
                <img
                    id="hero-preview"
                    src="{{ $heroImageUrl }}"
                    alt="معاينة صورة Hero"
                    class="aspect-[16/9] w-full object-cover {{ $heroImageUrl ? '' : 'hidden' }}"
                    data-hero-preview
                    @if ($heroImageUrl)
                        onerror="this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');"
                    @endif
                >
                <div
                    class="flex aspect-[16/9] w-full items-center justify-center text-sm text-brown/45 {{ $heroImageUrl ? 'hidden' : '' }}"
                    data-hero-placeholder
                >
                    لا توجد صورة حالياً — اختر صورة لرفعها
                </div>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-[1.4fr_1fr]">
                <div>
                    <label class="admin-label">رفع / استبدال الصورة</label>
                    <input
                        type="file"
                        name="hero_image"
                        accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                        class="admin-input"
                        data-preview-input="#hero-preview"
                        data-hero-image-input
                    >
                    @error('hero_image')
                        <p class="mt-2 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="admin-label">موضع الصورة</label>
                    <select name="hero_image_position" class="admin-input">
                        <option value="center" @selected($heroPosition === 'center')>وسط</option>
                        <option value="top" @selected($heroPosition === 'top')>أعلى</option>
                        <option value="bottom" @selected($heroPosition === 'bottom')>أسفل</option>
                    </select>
                </div>
            </div>

            @if ($heroImageUrl)
                <label class="admin-toggle mt-4">
                    <input type="checkbox" name="remove_hero_image" value="1" @checked(old('remove_hero_image')) data-hero-remove>
                    <span class="admin-toggle-track">
                        <span class="admin-toggle-knob"></span>
                        <span class="admin-toggle-on">نعم</span>
                        <span class="admin-toggle-off">لا</span>
                    </span>
                    <span class="text-sm text-brown/70">حذف صورة Hero الحالية</span>
                </label>
            @endif
        </div>
    </section>

    <section class="admin-card space-y-5">
        <div>
            <h2 class="text-lg font-medium text-brown">من نحن</h2>
            <p class="mt-1 text-sm text-brown/55">نص صفحة من نحن والملخص في الرئيسية.</p>
        </div>
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="admin-label">من نحن (عربي)</label>
                <textarea name="about_ar" rows="8" class="admin-input">{{ old('about_ar', $settings['about_ar'] ?? '') }}</textarea>
            </div>
            <div>
                <label class="admin-label">من نحن (إنجليزي)</label>
                <textarea name="about_en" rows="8" class="admin-input">{{ old('about_en', $settings['about_en'] ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <section class="admin-card space-y-5">
        <div>
            <h2 class="text-lg font-medium text-brown">بيانات التواصل</h2>
            <p class="mt-1 text-sm text-brown/55">تظهر في صفحة التواصل والفوتر.</p>
        </div>
        <div class="grid gap-5 md:grid-cols-3">
            <div>
                <label class="admin-label">البريد</label>
                <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">الهاتف</label>
                <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" class="admin-input" dir="ltr">
            </div>
            <div>
                <label class="admin-label">واتساب</label>
                <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $settings['contact_whatsapp'] ?? '') }}" class="admin-input" dir="ltr">
            </div>
            <div>
                <label class="admin-label">العنوان عربي</label>
                <input type="text" name="contact_address_ar" value="{{ old('contact_address_ar', $settings['contact_address_ar'] ?? '') }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">العنوان إنجليزي</label>
                <input type="text" name="contact_address_en" value="{{ old('contact_address_en', $settings['contact_address_en'] ?? '') }}" class="admin-input">
            </div>
        </div>
    </section>

    <section class="admin-card space-y-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-medium text-brown">روابط تواصل اجتماعي</h2>
                <p class="mt-1 text-sm text-brown/55">اختر نوع المنصة وأضف الرابط، ويمكنك إضافة أو حذف أي رابط في أي وقت.</p>
            </div>
            <button type="button" class="btn-gold-outline !py-2 text-xs" data-social-link-add>إضافة رابط</button>
        </div>

        <div class="space-y-3" data-social-links>
            @foreach ($linkRows as $index => $link)
                <div class="grid gap-3 rounded-2xl border border-line bg-mist/40 p-4 md:grid-cols-[1fr_2fr_auto]" data-social-link-row>
                    <div>
                        <label class="admin-label">نوع التواصل</label>
                        <select name="social_links[{{ $index }}][label]" class="admin-input" data-social-label>
                            <option value="">اختر المنصة</option>
                            @foreach ($socialPlatforms as $platform)
                                <option value="{{ $platform }}" @selected(($link['label'] ?? '') === $platform)>{{ $platform }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="admin-label">الرابط</label>
                        <input type="url" name="social_links[{{ $index }}][url]" value="{{ $link['url'] ?? '' }}" class="admin-input" dir="ltr" placeholder="https://..." data-social-url>
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="btn-ghost !text-brown/55 hover:!text-brown" data-social-link-remove>حذف</button>
                    </div>
                </div>
            @endforeach
        </div>

        <template data-social-link-template>
            <div class="grid gap-3 rounded-2xl border border-line bg-mist/40 p-4 md:grid-cols-[1fr_2fr_auto]" data-social-link-row>
                <div>
                    <label class="admin-label">نوع التواصل</label>
                    <select name="social_links[__INDEX__][label]" class="admin-input" data-social-label>
                        <option value="">اختر المنصة</option>
                        @foreach ($socialPlatforms as $platform)
                            <option value="{{ $platform }}">{{ $platform }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label">الرابط</label>
                    <input type="url" name="social_links[__INDEX__][url]" value="" class="admin-input" dir="ltr" placeholder="https://..." data-social-url>
                </div>
                <div class="flex items-end">
                    <button type="button" class="btn-ghost !text-brown/55 hover:!text-brown" data-social-link-remove>حذف</button>
                </div>
            </div>
        </template>
    </section>

    <section class="admin-card space-y-5">
        <div>
            <h2 class="text-lg font-medium text-brown">SEO والإحصائيات</h2>
            <p class="mt-1 text-sm text-brown/55">عناوين محركات البحث والأرقام الظاهرة في الموقع.</p>
        </div>
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="admin-label">SEO Title</label>
                <input type="text" name="seo_title" value="{{ old('seo_title', $settings['seo_title'] ?? '') }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">SEO Description</label>
                <input type="text" name="seo_description" value="{{ old('seo_description', $settings['seo_description'] ?? '') }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">مشاريع مكتملة (رقم حقيقي فقط)</label>
                <input type="number" name="projects_completed" value="{{ old('projects_completed', $settings['projects_completed'] ?? 0) }}" class="admin-input" min="0">
            </div>
            <div>
                <label class="admin-label">سنوات الخبرة</label>
                <input type="number" name="years_experience" value="{{ old('years_experience', $settings['years_experience'] ?? 0) }}" class="admin-input" min="0">
            </div>
            <div>
                <label class="admin-label">عدد العملاء</label>
                <input type="number" name="clients_count" value="{{ old('clients_count', $settings['clients_count'] ?? 0) }}" class="admin-input" min="0">
            </div>
        </div>
    </section>

    <button type="submit" class="btn-solid">حفظ الإعدادات</button>
</form>
@endsection
