@extends('layouts.frontend', [
    'seoTitle' => $project->meta_title ?: ($project->title_ar.' | RAQI DECOR'),
    'seoDescription' => $project->meta_description ?: ($project->excerpt_ar ?: $project->excerpt_en),
    'ogImageUrl' => $project->cover_image ? url(\Illuminate\Support\Facades\Storage::disk('public')->url($project->cover_image)) : null,
])

@section('content')
@php
    $cover = $project->cover_image
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($project->cover_image)
        : ($project->coverImage()?->url('large') ?? asset('images/project-placeholder.svg'));
@endphp

<article>
    <section class="relative min-h-[78vh] overflow-hidden">
        <img src="{{ $cover }}" alt="{{ $project->title_ar }}" class="absolute inset-0 h-full w-full object-cover" fetchpriority="high">
        <div class="hero-scrim absolute inset-0"></div>
        <div class="site-container relative flex min-h-[78vh] items-end pb-16 pt-32">
            <div class="max-w-3xl text-ivory">
                <p class="font-display reveal text-xs uppercase tracking-[0.28em] text-beige">{{ $project->category?->name_en }}</p>
                <h1 class="reveal reveal-delay-1 mt-4 heading-xl">{{ $project->title_ar }}</h1>
                <p class="reveal reveal-delay-2 font-display mt-4 text-xl tracking-[0.12em] text-ivory/75">{{ $project->title_en }}</p>
            </div>
        </div>
    </section>

    <section class="section-space">
        <div class="site-container grid gap-8 lg:grid-cols-12">
            <div class="reveal lg:col-span-7">
                <div class="surface p-8 md:p-10">
                    @if ($project->excerpt_ar)
                        <p class="text-xl leading-10 text-brown/85">{{ $project->excerpt_ar }}</p>
                    @endif
                    @if ($project->description_ar)
                        <div class="mt-8 space-y-6 text-base leading-9 text-brown/70 whitespace-pre-line">{{ $project->description_ar }}</div>
                    @endif
                </div>
            </div>

            <aside class="reveal reveal-delay-1 lg:col-span-4 lg:col-start-9">
                <div class="surface p-7">
                    <p class="eyebrow">Project Details</p>
                    <dl class="mt-7 space-y-5 text-sm">
                        @if ($project->category)
                            <div class="flex justify-between gap-4 border-b border-line pb-4">
                                <dt class="text-brown/45">الفئة</dt>
                                <dd>{{ $project->category->name_ar }}</dd>
                            </div>
                        @endif
                        @if ($project->location)
                            <div class="flex justify-between gap-4 border-b border-line pb-4">
                                <dt class="text-brown/45">الموقع</dt>
                                <dd>{{ $project->location }}</dd>
                            </div>
                        @endif
                        @if ($project->year)
                            <div class="flex justify-between gap-4 border-b border-line pb-4">
                                <dt class="text-brown/45">السنة</dt>
                                <dd>{{ $project->year }}</dd>
                            </div>
                        @endif
                        @if ($project->client)
                            <div class="flex justify-between gap-4">
                                <dt class="text-brown/45">العميل</dt>
                                <dd>{{ $project->client }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </aside>
        </div>
    </section>

    @if ($project->images->isNotEmpty())
        <section class="pb-24 md:pb-32">
            <div class="site-container space-y-6 md:space-y-8">
                @foreach ($project->images as $index => $image)
                    @php
                        $isWide = $index % 3 === 0;
                        $src = $image->url('large');
                    @endphp
                    <figure class="reveal overflow-hidden rounded-[1.75rem] {{ $isWide ? '' : 'md:mx-[10%]' }}">
                        <a href="{{ $src }}" data-lightbox-trigger data-alt="{{ $image->alt_ar ?: $project->title_ar }}" class="block overflow-hidden bg-mist">
                            <img src="{{ $image->url($isWide ? 'large' : 'medium') }}" alt="{{ $image->alt_ar ?: $project->title_ar }}" class="w-full object-cover transition duration-700 hover:scale-[1.02] {{ $isWide ? 'aspect-[16/9]' : 'aspect-[4/3]' }}" loading="lazy">
                        </a>
                        @if ($image->caption_ar)
                            <figcaption class="mt-3 px-1 text-sm text-brown/50">{{ $image->caption_ar }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    @if ($related->isNotEmpty())
        <section class="pb-24 md:pb-32">
            <div class="site-container">
                <p class="reveal eyebrow">More Projects</p>
                <h2 class="reveal reveal-delay-1 heading-lg mt-3">أعمال ذات صلة</h2>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach ($related as $item)
                        @include('partials.frontend.project-card', ['project' => $item])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</article>

@push('meta')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CreativeWork',
    'name' => $project->title_en,
    'alternateName' => $project->title_ar,
    'description' => $project->excerpt_en ?: $project->excerpt_ar,
    'datePublished' => optional($project->published_at)->toAtomString(),
    'image' => $cover,
    'url' => route('projects.show', $project),
], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
</script>
@endpush
@endsection
