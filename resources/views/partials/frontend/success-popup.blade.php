@if (session('success'))
    <div
        class="success-popup is-open"
        data-success-popup
        role="dialog"
        aria-modal="true"
        aria-labelledby="success-popup-title"
        aria-hidden="false"
    >
        <div class="success-popup__panel surface" data-success-panel>
            <p class="eyebrow">تم الإرسال</p>
            <h2 id="success-popup-title" class="mt-4 font-display text-3xl tracking-wide text-brown md:text-4xl">وصلتنا رسالتك</h2>
            <p class="mt-4 text-sm leading-7 text-brown/70">{{ session('success') }}</p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                @if (session('whatsapp_url'))
                    <a href="{{ session('whatsapp_url') }}" target="_blank" rel="noopener noreferrer" class="btn-solid" data-success-whatsapp>
                        فتح واتساب
                    </a>
                @endif
                <button type="button" class="btn-gold-outline" data-success-close>حسناً</button>
            </div>
        </div>
    </div>

    @if (session('whatsapp_url'))
        <script>
            window.open(@json(session('whatsapp_url')), '_blank', 'noopener,noreferrer');
        </script>
    @endif
@endif
