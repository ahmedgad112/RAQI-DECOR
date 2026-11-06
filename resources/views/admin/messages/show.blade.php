@extends('layouts.admin')

@section('title', 'رسالة')

@section('content')
<div class="admin-card max-w-3xl">
    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-beige pb-4">
        <div>
            <h2 class="text-lg">{{ $message->name }}</h2>
            <p class="mt-1 text-sm text-brown/60">{{ $message->email }} @if($message->phone) · {{ $message->phone }} @endif</p>
            <p class="mt-1 text-xs text-brown/45">{{ $message->created_at->format('Y-m-d H:i') }}</p>
        </div>
        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('حذف الرسالة؟')">
            @csrf
            @method('DELETE')
            <button type="submit" class="admin-action-btn admin-action-btn--danger">حذف</button>
        </form>
    </div>

    @if ($message->subject)
        <p class="mt-5 text-sm text-brown/50">الموضوع: <span class="text-brown">{{ $message->subject }}</span></p>
    @endif

    <p class="mt-5 whitespace-pre-line text-base leading-8">{{ $message->message }}</p>

    <a href="{{ route('admin.messages.index') }}" class="btn-gold-outline mt-8 inline-flex">رجوع</a>
</div>
@endsection
