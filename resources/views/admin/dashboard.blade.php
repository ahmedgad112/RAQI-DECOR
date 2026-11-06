@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ([
        ['label' => 'إجمالي الأعمال', 'value' => $stats['projects']],
        ['label' => 'منشور', 'value' => $stats['published']],
        ['label' => 'الفئات', 'value' => $stats['categories']],
        ['label' => 'رسائل غير مقروءة', 'value' => $stats['unread_messages']],
    ] as $card)
        <div class="admin-card">
            <p class="text-xs text-brown/50">{{ $card['label'] }}</p>
            <p class="font-display mt-3 text-3xl">{{ $card['value'] }}</p>
        </div>
    @endforeach
</div>

<div class="mt-8 grid gap-6 xl:grid-cols-2">
    <div class="admin-card">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-base font-medium">أحدث الأعمال</h2>
            <a href="{{ route('admin.projects.index') }}" class="text-xs text-gold">عرض الكل</a>
        </div>
        <div class="space-y-3">
            @forelse ($recentProjects as $project)
                <a href="{{ route('admin.projects.edit', $project) }}" class="flex items-center justify-between border-b border-beige/70 py-2 text-sm last:border-0">
                    <span>{{ $project->title_ar }}</span>
                    <span class="text-xs text-brown/45">{{ $project->category?->name_ar }}</span>
                </a>
            @empty
                <p class="text-sm text-brown/50">لا توجد أعمال بعد.</p>
            @endforelse
        </div>
    </div>

    <div class="admin-card">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-base font-medium">أحدث الرسائل</h2>
            <a href="{{ route('admin.messages.index') }}" class="text-xs text-gold">عرض الكل</a>
        </div>
        <div class="space-y-3">
            @forelse ($recentMessages as $message)
                <a href="{{ route('admin.messages.show', $message) }}" class="block border-b border-beige/70 py-2 text-sm last:border-0">
                    <div class="flex items-center justify-between gap-3">
                        <span>{{ $message->name }}</span>
                        @unless ($message->is_read)
                            <span class="text-[10px] tracking-wide text-gold">جديد</span>
                        @endunless
                    </div>
                    <p class="mt-1 text-xs text-brown/50">{{ \Illuminate\Support\Str::limit($message->message, 70) }}</p>
                </a>
            @empty
                <p class="text-sm text-brown/50">لا توجد رسائل.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
