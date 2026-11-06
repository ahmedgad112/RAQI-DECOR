@extends('layouts.admin')

@section('title', 'الفئات')

@section('content')
<div class="mb-5 flex items-center justify-between gap-3">
    <p class="text-sm text-brown/60">إدارة فئات الأعمال</p>
    <a href="{{ route('admin.categories.create') }}" class="btn-solid !py-2 text-xs">إضافة فئة</a>
</div>

<div class="overflow-x-auto border border-beige bg-pure">
    <table class="min-w-full text-sm">
        <thead class="bg-ivory text-xs text-brown/60">
            <tr>
                <th class="px-4 py-3 text-start">الاسم</th>
                <th class="px-4 py-3 text-start">Slug</th>
                <th class="px-4 py-3 text-start">الأعمال</th>
                <th class="px-4 py-3 text-start">الحالة</th>
                <th class="px-4 py-3 text-start">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr class="border-t border-beige/70">
                    <td class="px-4 py-3">{{ $category->name_ar }} <span class="text-brown/40">({{ $category->name_en }})</span></td>
                    <td class="px-4 py-3 font-sans-en" dir="ltr">{{ $category->slug }}</td>
                    <td class="px-4 py-3">{{ $category->projects_count }}</td>
                    <td class="px-4 py-3">{{ $category->is_active ? 'نشطة' : 'موقوفة' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-3">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-gold">تعديل</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('حذف الفئة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-700">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $categories->links() }}</div>
@endsection
