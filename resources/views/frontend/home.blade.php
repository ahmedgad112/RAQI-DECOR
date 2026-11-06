@extends('layouts.frontend')

@section('content')
@php
    $heroImage = ! empty($settings['hero_image'])
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($settings['hero_image'])
        : asset('images/hero-placeholder.svg');
    $brandEn = $settings['site_name_en'] ?? 'RAQI DECOR';
    $heroPosition = match ($settings['hero_image_position'] ?? 'center') {
        'top' => 'object-top',
        'bottom' => 'object-bottom',
        default => 'object-center',
    };
@endphp

<section class="relative min-h-[100svh] overflow-hidden">
    <img
        src="{{ $heroImage }}"
        alt="{{ $settings['site_name_ar'] ?? 'RAQI DECOR' }}"
        class="absolute inset-0 h-full w-full scale-105 object-cover {{ $heroPosition }} transition-transform duration-[8s] ease-out will-change-transform"
        fetchpriority="high"
        data-hero-image
    >
    <div class="hero-scrim absolute inset-0"></div>

    <div class="site-container relative flex min-h-[100svh] items-end pb-16 pt-28 md:items-end md:pb-20">
        <div class="w-full max-w-4xl text-ivory">
            <p class="font-display reveal text-sm uppercase tracking-[0.42em] text-beige/90">{{ $brandEn }}</p>
            <h1 class="reveal reveal-delay-1 mt-5 heading-xl">{{ $settings['hero_title_ar'] ?? 'نحوّل المساحات إلى تجارب معيشة راقية' }}</h1>
            <p class="reveal reveal-delay-2 mt-6 max-w-xl text-base leading-8 text-ivory/80 md:text-lg">{{ $settings['hero_subtitle_ar'] ?? '' }}</p>
            <div class="reveal reveal-delay-3 mt-10 flex flex-wrap items-center gap-4">
                <a href="{{ route('projects.index') }}" class="btn-gold-outline !border-ivory/40 !text-ivory hover:!border-gold hover:!bg-gold hover:!text-pure">استكشف أعمالنا</a>
                <a href="{{ route('contact') }}" class="btn-ghost !text-ivory/80 hover:!text-ivory">تواصل معنا</a>
            </div>
        </div>
    </div>
</section>

<section class="section-space">
    <div class="site-container">
        <div class="grid items-stretch gap-6 lg:grid-cols-12">
            <div class="reveal surface p-8 md:p-10 lg:col-span-5">
                <p class="eyebrow">Studio</p>
                <h2 class="heading-lg mt-5">تصميم مودرن بذوق هادئ</h2>
                <p class="mt-6 text-sm leading-8 text-brown/70">خطوط نظيفة، خامات فاخرة، وتفاصيل معمارية مدروسة تصنع حضوراً معاصراً دون ضوضاء.</p>
            </div>
            <div class="reveal reveal-delay-1 surface p-8 md:p-10 lg:col-span-7">
                <p class="text-base leading-9 text-brown/80 whitespace-pre-line">{{ \Illuminate\Support\Str::limit($settings['about_ar'] ?? '', 360) }}</p>
                <a href="{{ route('about') }}" class="btn-ghost mt-8">اعرف أكثر عن الاستوديو</a>
            </div>
        </div>

        @php
            $stats = collect([
                ['label' => 'مشروع مكتمل', 'value' => (int) ($settings['projects_completed'] ?? 0)],
                ['label' => 'سنوات خبرة', 'value' => (int) ($settings['years_experience'] ?? 0)],
                ['label' => 'عميل', 'value' => (int) ($settings['clients_count'] ?? 0)],
            ])->filter(fn ($item) => $item['value'] > 0);
        @endphp

        @if ($stats->isNotEmpty())
            <div class="mt-6 grid gap-6 sm:grid-cols-3">
                @foreach ($stats as $index => $stat)
                    <div class="reveal surface px-6 py-8 text-center" style="transition-delay: {{ $index * 0.1 }}s">
                        <p class="font-display text-5xl text-brown">{{ $stat['value'] }}+</p>
                        <p class="mt-3 text-sm text-brown/55">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@if ($categories->isNotEmpty())
<section class="pb-8 md:pb-12">
    <div class="site-container">
        <div class="reveal mb-10 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Spaces</p>
                <h2 class="heading-lg mt-3">مجالات التصميم</h2>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            @foreach ($categories as $index => $category)
                <a href="{{ route('projects.index', ['category' => $category->slug]) }}" class="reveal group relative block min-h-[22rem] overflow-hidden rounded-[1.75rem]" style="transition-delay: {{ $index * 0.1 }}s">
                    @if ($category->image_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($category->image_path) }}" alt="{{ $category->name_ar }}" class="absolute inset-0 h-full w-full object-cover transition duration-[900ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-105" loading="lazy">
                    @else
                        <div class="absolute inset-0 bg-gradient-to-br from-beige via-mist to-brown/30"></div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-charcoal/70 via-charcoal/15 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-7 text-ivory">
                        <p class="font-display text-xs uppercase tracking-[0.28em] text-beige">{{ $category->name_en }}</p>
                        <h3 class="mt-3 text-3xl">{{ $category->name_ar }}</h3>
                        <p class="mt-3 text-sm text-ivory/70 opacity-0 transition duration-500 group-hover:opacity-100">استكشف المشاريع</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@php $showcase = $featuredProjects->isNotEmpty() ? $featuredProjects : $latestProjects; @endphp

@if ($showcase->isNotEmpty())
<section class="section-space">
    <div class="site-container">
        <div class="reveal mb-12 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Selected Works</p>
                <h2 class="heading-lg mt-3">أعمال مختارة</h2>
            </div>
            <a href="{{ route('projects.index') }}" class="btn-ghost">عرض المعرض الكامل</a>
        </div>

        <div class="columns-1 gap-6 space-y-6 md:columns-2 xl:columns-3">
            @foreach ($showcase as $index => $project)
                @include('partials.frontend.project-card', ['project' => $project, 'tall' => $index % 3 === 1, 'revealDelay' => $index * 0.08])
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="pb-8 md:pb-12">
    <div class="site-container">
        <div class="reveal surface-dark overflow-hidden px-8 py-14 md:flex md:items-center md:justify-between md:px-12">
            <div class="max-w-xl">
                <p class="eyebrow !text-gold">Next Project</p>
                <h2 class="heading-md mt-4 text-ivory">هل لديك مساحة تستحق تصميماً أهدأ وأكثر دقة؟</h2>
            </div>
            <a href="{{ route('contact') }}" class="btn-gold-outline mt-8 !border-ivory/30 !text-ivory hover:!border-gold hover:!bg-gold hover:!text-pure md:mt-0">لنبدأ الحديث</a>
        </div>
    </div>
</section>
@endsection
