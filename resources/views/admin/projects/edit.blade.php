@extends('layouts.admin')

@section('title', 'تعديل عمل')

@section('content')
@include('admin.projects._form', [
    'project' => $project,
    'categories' => $categories,
    'action' => route('admin.projects.update', $project),
    'method' => 'PUT',
])

@if ($project->images->isNotEmpty())
    <div class="admin-card mt-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-base font-medium">معرض الصور</h2>
            <p class="text-xs text-brown/50">اسحب لإعادة الترتيب</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-sortable-images data-reorder-url="{{ route('admin.projects.images.reorder', $project) }}">
            @foreach ($project->images as $image)
                <div class="border border-beige bg-ivory p-3" data-image-id="{{ $image->id }}">
                    <img src="{{ $image->url('thumb') }}" alt="" class="aspect-[4/3] w-full object-cover">
                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                        @if ($image->is_cover)
                            <span class="text-gold">غلاف</span>
                        @else
                            <form method="POST" action="{{ route('admin.projects.images.cover', [$project, $image]) }}">
                                @csrf
                                <button class="text-brown/70 hover:text-gold">تعيين كغلاف</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.projects.images.destroy', [$project, $image]) }}" onsubmit="return confirm('حذف الصورة؟')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-700">حذف</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection
