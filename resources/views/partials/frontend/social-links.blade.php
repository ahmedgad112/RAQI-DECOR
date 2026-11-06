@php
    $tone = $tone ?? 'light';
    $linkClass = $tone === 'dark'
        ? 'inline-flex size-11 shrink-0 items-center justify-center rounded-full border border-ivory/30 bg-ivory/5 text-ivory transition hover:border-gold hover:bg-gold/15 hover:text-gold'
        : 'inline-flex size-11 shrink-0 items-center justify-center rounded-full border border-line bg-pure text-brown transition hover:border-gold hover:text-gold';
@endphp

@if (! empty($socialLinks))
    <div class="{{ $class ?? 'flex flex-wrap items-center gap-3' }}">
        @foreach ($socialLinks as $link)
            <a
                href="{{ $link['url'] }}"
                target="_blank"
                rel="noopener noreferrer"
                class="{{ $linkClass }}"
                aria-label="{{ $link['label'] }}"
                title="{{ $link['label'] }}"
            >
                @include('partials.frontend.social-icon', ['platform' => $link['label']])
            </a>
        @endforeach
    </div>
@endif
