@extends('layouts.frontend')

@section('content')
<section class="pt-32">
    <div class="site-container section-space !pb-12 !pt-10">
        <p class="reveal eyebrow">About Studio</p>
        <h1 class="reveal reveal-delay-1 heading-xl mt-5 max-w-4xl">من نحن</h1>
        <p class="reveal reveal-delay-2 font-display mt-5 text-xl tracking-[0.14em] text-brown/45">{{ $settings['site_name_en'] ?? 'RAQI DECOR' }}</p>
    </div>
</section>

<section class="pb-24 md:pb-32">
    <div class="site-container grid gap-8 lg:grid-cols-12">
        <div class="reveal lg:col-span-5">
            <div class="sticky top-28 overflow-hidden rounded-[1.75rem] bg-mist">
                @if (! empty($settings['hero_image']))
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($settings['hero_image']) }}" alt="RAQI DECOR" class="aspect-[4/5] h-full w-full object-cover" loading="lazy">
                @else
                    <img src="{{ asset('images/hero-placeholder.svg') }}" alt="RAQI DECOR" class="aspect-[4/5] h-full w-full object-cover">
                @endif
            </div>
        </div>

        <div class="reveal reveal-delay-1 lg:col-span-6 lg:col-start-7">
            <div class="surface p-8 md:p-10">
                <p class="text-base leading-9 text-brown/80 whitespace-pre-line">{{ $settings['about_ar'] ?? '' }}</p>
                @if (! empty($settings['about_en']))
                    <p class="font-sans-en mt-10 border-t border-line pt-8 text-sm leading-7 text-brown/55">{{ $settings['about_en'] }}</p>
                @endif

                @php
                    $stats = collect([
                        ['label' => 'مشروع منشور', 'value' => $projectsCount],
                        ['label' => 'سنوات خبرة', 'value' => (int) ($settings['years_experience'] ?? 0)],
                        ['label' => 'عميل', 'value' => (int) ($settings['clients_count'] ?? 0)],
                    ])->filter(fn ($item) => $item['value'] > 0);
                @endphp

                @if ($stats->isNotEmpty())
                    <div class="mt-10 grid gap-4 sm:grid-cols-3">
                        @foreach ($stats as $stat)
                            <div class="rounded-2xl bg-mist px-4 py-5">
                                <p class="font-display text-3xl">{{ $stat['value'] }}</p>
                                <p class="mt-2 text-xs text-brown/55">{{ $stat['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <a href="{{ route('contact') }}" class="btn-solid mt-10">ابدأ مشروعك</a>
            </div>
        </div>
    </div>
</section>
@endsection
