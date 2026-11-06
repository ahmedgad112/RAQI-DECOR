@extends('layouts.admin')

@section('title', 'الأعمال')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="text-sm text-brown/60">إدارة معرض الأعمال</p>
        <p class="mt-1 text-xs text-brown/40">{{ $projects->total() }} عمل</p>
    </div>
    <a href="{{ route('admin.projects.create') }}" class="btn-solid !py-2.5 text-xs">إضافة عمل</a>
</div>

<form method="GET" class="admin-card mb-6 flex flex-wrap items-end gap-3 !p-4">
    <div class="min-w-[12rem] flex-1">
        <label class="admin-label">بحث</label>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="عنوان العمل..." class="admin-input">
    </div>
    <div class="w-full sm:w-48">
        <label class="admin-label">الفئة</label>
        <select name="category" class="admin-input">
            <option value="">كل الفئات</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name_ar }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2">
        <button class="btn-solid !rounded-2xl !px-5 !py-3 text-xs tracking-normal">تصفية</button>
        @if (request()->filled('q') || request()->filled('category'))
            <a href="{{ route('admin.projects.index') }}" class="btn-gold-outline !rounded-2xl !px-5 !py-3 text-xs tracking-normal">مسح</a>
        @endif
    </div>
</form>

@if ($projects->isEmpty())
    <div class="admin-card py-16 text-center">
        <p class="text-sm text-brown/55">لا توجد أعمال مطابقة.</p>
        <a href="{{ route('admin.projects.create') }}" class="btn-solid mt-5 inline-flex !py-2.5 text-xs">أضف أول عمل</a>
    </div>
@else
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($projects as $project)
            @php
                $cover = $project->cover_image
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($project->cover_image)
                    : asset('images/project-placeholder.svg');
            @endphp
            <article class="admin-card group overflow-hidden !p-0">
                <div class="relative aspect-[16/10] overflow-hidden bg-mist">
                    <img src="{{ $cover }}" alt="{{ $project->title_ar }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" loading="lazy">
                    <div class="absolute inset-x-0 top-0 flex flex-wrap gap-2 p-3">
                        <span class="rounded-full px-2.5 py-1 text-[10px] font-medium tracking-wide {{ $project->is_published ? 'bg-emerald-700/90 text-pure' : 'bg-charcoal/70 text-ivory/80' }}">
                            {{ $project->is_published ? 'منشور' : 'مسودة' }}
                        </span>
                        @if ($project->is_featured)
                            <span class="rounded-full bg-gold/95 px-2.5 py-1 text-[10px] font-medium tracking-wide text-pure">مميز</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-4 p-4">
                    <div>
                        <p class="text-[11px] tracking-wide text-brown/45">{{ $project->category?->name_ar ?? 'بدون فئة' }}</p>
                        <h2 class="mt-1 text-base font-medium leading-snug">{{ $project->title_ar }}</h2>
                        @if ($project->title_en && $project->title_en !== $project->title_ar)
                            <p class="font-display mt-1 text-sm tracking-[0.06em] text-brown/45">{{ $project->title_en }}</p>
                        @endif
                        <p class="mt-2 text-xs text-brown/40">{{ $project->images_count }} صورة</p>
                    </div>

                    <div class="flex flex-wrap gap-2 border-t border-line pt-4">
                        <a href="{{ route('projects.show', $project) }}" target="_blank" class="admin-action-btn">عرض</a>
                        <a href="{{ route('admin.projects.edit', $project) }}" class="admin-action-btn admin-action-btn--gold">تعديل</a>
                        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('حذف العمل وكل صوره؟')" class="ms-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-action-btn admin-action-btn--danger">حذف</button>
                        </form>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@endif

<div class="mt-6">{{ $projects->links() }}</div>
@endsection
