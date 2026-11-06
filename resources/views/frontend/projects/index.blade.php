@extends('layouts.frontend')

@section('content')
<section class="pt-32">
    <div class="site-container section-space !pb-8 !pt-10">
        <p class="reveal eyebrow">Portfolio</p>
        <h1 class="reveal reveal-delay-1 heading-xl mt-5">أعمالنا</h1>
        <p class="reveal reveal-delay-2 font-display mt-4 text-lg tracking-[0.14em] text-brown/45">Selected Interior Projects</p>

        <div class="reveal reveal-delay-3 mt-10 flex flex-wrap gap-3">
            <a href="{{ route('projects.index') }}" class="filter-chip {{ $activeCategory === '' ? 'is-active' : '' }}">الكل</a>
            @foreach ($categories as $category)
                <a href="{{ route('projects.index', ['category' => $category->slug]) }}" class="filter-chip {{ $activeCategory === $category->slug ? 'is-active' : '' }}">{{ $category->name_ar }}</a>
            @endforeach
        </div>
    </div>
</section>

<section class="pb-24 md:pb-32">
    <div class="site-container">
        @if ($projects->isEmpty())
            <div class="surface px-6 py-20 text-center">
                <p class="text-brown/65">لا توجد أعمال منشورة في هذا القسم حالياً.</p>
            </div>
        @else
            <div class="columns-1 gap-6 space-y-6 md:columns-2 xl:columns-3">
                @foreach ($projects as $index => $project)
                    @include('partials.frontend.project-card', ['project' => $project, 'tall' => $index % 4 === 0, 'revealDelay' => ($index % 6) * 0.06])
                @endforeach
            </div>

            <div class="mt-14">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
