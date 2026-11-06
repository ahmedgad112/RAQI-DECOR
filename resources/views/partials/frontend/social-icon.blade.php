@php
    $platform = (string) ($platform ?? '');
    $class = trim('text-lg leading-none ' . ($class ?? ''));

    $icon = match ($platform) {
        'Instagram' => 'fa-brands fa-instagram',
        'Facebook' => 'fa-brands fa-facebook-f',
        'TikTok' => 'fa-brands fa-tiktok',
        'YouTube' => 'fa-brands fa-youtube',
        'LinkedIn' => 'fa-brands fa-linkedin-in',
        'X' => 'fa-brands fa-x-twitter',
        'WhatsApp' => 'fa-brands fa-whatsapp',
        'Snapchat' => 'fa-brands fa-snapchat',
        'Pinterest' => 'fa-brands fa-pinterest-p',
        'Behance' => 'fa-brands fa-behance',
        'Threads' => 'fa-brands fa-threads',
        default => 'fa-solid fa-link',
    };
@endphp

<i class="{{ $icon }} {{ $class }}" aria-hidden="true"></i>
