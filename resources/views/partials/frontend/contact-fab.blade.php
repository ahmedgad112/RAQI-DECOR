@php
    $hasPhone = ! empty($siteSettings['contact_phone']);
    $hasWhatsapp = ! empty($whatsappUrl);
    $hasEmail = ! empty($siteSettings['contact_email']);
    $fabSocialLinks = collect($socialLinks ?? [])
        ->reject(fn (array $link): bool => ($link['label'] ?? '') === 'WhatsApp' && $hasWhatsapp)
        ->values()
        ->all();
    $hasSocial = $fabSocialLinks !== [];
@endphp

@if ($hasPhone || $hasWhatsapp || $hasEmail || $hasSocial)
    <div class="contact-fab" data-contact-fab>
        <div
            class="contact-fab__panel surface"
            id="contact-fab-actions"
            data-contact-fab-actions
            role="dialog"
            aria-labelledby="contact-fab-title"
            aria-hidden="true"
        >
            <p class="eyebrow" id="contact-fab-title">تواصل معنا</p>
            <p class="mt-2 text-sm leading-6 text-brown/65">اختر وسيلة التواصل المناسبة لك</p>

            <div class="contact-fab__grid">
                @if ($hasWhatsapp)
                    <a
                        href="{{ $whatsappUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="contact-fab__action contact-fab__action--whatsapp"
                        aria-label="تواصل عبر واتساب"
                        title="واتساب"
                    >
                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                        <span>واتساب</span>
                    </a>
                @endif

                @if ($hasPhone)
                    <a
                        href="tel:{{ $siteSettings['contact_phone'] }}"
                        class="contact-fab__action contact-fab__action--phone"
                        aria-label="اتصل بنا"
                        title="اتصال"
                        dir="ltr"
                    >
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                        <span>اتصال</span>
                    </a>
                @endif

                @if ($hasEmail)
                    <a
                        href="mailto:{{ $siteSettings['contact_email'] }}"
                        class="contact-fab__action contact-fab__action--email"
                        aria-label="راسلنا عبر البريد"
                        title="البريد"
                    >
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                        <span>بريد</span>
                    </a>
                @endif

                @foreach ($fabSocialLinks as $link)
                    @php
                        $platform = (string) ($link['label'] ?? '');
                        $slug = \Illuminate\Support\Str::slug($platform);
                    @endphp
                    <a
                        href="{{ $link['url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="contact-fab__action contact-fab__action--social contact-fab__action--{{ $slug }}"
                        aria-label="{{ $platform }}"
                        title="{{ $platform }}"
                    >
                        @include('partials.frontend.social-icon', ['platform' => $platform])
                        <span>{{ $platform }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <button
            type="button"
            class="contact-fab__toggle"
            data-contact-fab-toggle
            aria-expanded="false"
            aria-controls="contact-fab-actions"
            aria-label="خيارات التواصل"
        >
            <i class="fa-solid fa-comment-dots contact-fab__icon-open" aria-hidden="true"></i>
            <i class="fa-solid fa-xmark contact-fab__icon-close" aria-hidden="true"></i>
        </button>
    </div>
@endif
