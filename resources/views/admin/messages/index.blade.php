@extends('layouts.admin')

@section('title', 'الرسائل')

@section('content')
<div class="overflow-x-auto border border-beige bg-pure">
    <table class="min-w-full text-sm">
        <thead class="bg-ivory text-xs text-brown/60">
            <tr>
                <th class="px-4 py-3 text-start">المرسل</th>
                <th class="px-4 py-3 text-start">الموضوع</th>
                <th class="px-4 py-3 text-start">التاريخ</th>
                <th class="px-4 py-3 text-start">الحالة</th>
                <th class="px-4 py-3 text-start">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($messages as $message)
                <tr class="border-t border-beige/70 {{ $message->is_read ? '' : 'bg-ivory/70' }}">
                    <td class="px-4 py-3">
                        <div>{{ $message->name }}</div>
                        <div class="text-xs text-brown/45">{{ $message->email }}</div>
                    </td>
                    <td class="px-4 py-3">{{ $message->subject ?: '—' }}</td>
                    <td class="px-4 py-3">{{ $message->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3">{{ $message->is_read ? 'مقروءة' : 'جديدة' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('admin.messages.show', $message) }}" class="admin-action-btn admin-action-btn--gold">فتح</a>
                            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('حذف الرسالة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-action-btn admin-action-btn--danger">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-brown/45">لا توجد رسائل حالياً.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-5">{{ $messages->links() }}</div>
@endsection
