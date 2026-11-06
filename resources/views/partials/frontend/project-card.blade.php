@php
    $cover = $project->cover_image
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($project->cover_image)
        : ($project->coverImage()?->url('medium') ?? asset('images/project-placeholder.svg'));
    $tall = $tall ?? false;
    $revealDelay = $revealDelay ?? 0;
@endphp

<article class="project-card reveal group break-inside-avoid" style="transition-delay: {{ $revealDelay }}s">
    <a href="{{ route('projects.show', $project) }}" class="block">
        <div class="relative overflow-hidden {{ $tall ? 'aspect-[3/4]' : 'aspect-[4/5]' }} bg-mist">
            <img src="{{ $cover }}" alt="{{ $project->title_ar }}" class="h-full w-full object-cover" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-charcoal/55 via-charcoal/0 to-transparent opacity-80 transition duration-500 group-hover:opacity-90"></div>
            <div class="absolute inset-x-0 bottom-0 p-5 text-ivory">
                <p class="font-display text-[11px] uppercase tracking-[0.22em] text-beige/90">{{ $project->category?->name_en }}</p>
                <h3 class="mt-2 text-xl">{{ $project->title_ar }}</h3>
                <p class="font-display mt-1 text-sm tracking-[0.08em] text-ivory/65">{{ $project->title_en }}</p>
            </div>
        </div>
    </a>
</article>
