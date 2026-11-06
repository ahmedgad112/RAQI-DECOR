@php
    $hasPhone = ! empty($siteSettings['contact_phone']);
    $hasWhatsapp = ! empty($whatsappUrl);
    $hasEmail = ! empty($siteSettings['contact_email']);
@endphp

@if ($hasPhone || $hasWhatsapp || $hasEmail)
    <div class="contact-fab" data-contact-fab>
        <div class="contact-fab__actions" id="contact-fab-actions" data-contact-fab-actions aria-hidden="true">
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
                    <span class="contact-fab__tooltip">واتساب</span>
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
                    <span class="contact-fab__tooltip">اتصال</span>
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
                    <span class="contact-fab__tooltip">بريد</span>
                </a>
            @endif
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
