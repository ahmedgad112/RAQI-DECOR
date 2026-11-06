<footer class="mt-8 px-4 pb-6 sm:px-6">
    <div class="site-container surface-dark overflow-hidden !max-w-[1120px] px-6 py-14 sm:px-10 md:py-16">
        <div class="grid gap-12 md:grid-cols-12">
            <div class="md:col-span-6">
                @php
                    $footerLogoUrl = ! empty($siteSettings['logo_path'])
                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings['logo_path'])
                        : asset('images/logo.png');
                @endphp
                <img src="{{ $footerLogoUrl }}" alt="{{ $siteSettings['site_name_en'] ?? 'RAQI DECOR' }}" class="h-14 w-14 object-contain" width="56" height="56">
                <p class="font-display mt-4 text-3xl tracking-[0.2em]">{{ $siteSettings['site_name_en'] ?? 'RAQI DECOR' }}</p>
                <p class="mt-3 text-sm text-beige">{{ $siteSettings['site_name_ar'] ?? 'راقي ديكور' }}</p>
                <p class="mt-6 max-w-md text-sm leading-8 text-ivory/70">{{ $siteSettings['tagline_ar'] ?? '' }}</p>
                <a href="{{ route('contact') }}" class="btn-gold mt-8">ابدأ مشروعك</a>
            </div>

            <div class="md:col-span-3">
                <p class="eyebrow !text-gold">استكشف</p>
                <div class="mt-5 space-y-3 text-sm text-ivory/75">
                    <a href="{{ route('projects.index') }}" class="block hover:text-gold">أعمالنا</a>
                    <a href="{{ route('about') }}" class="block hover:text-gold">من نحن</a>
                    <a href="{{ route('contact') }}" class="block hover:text-gold">تواصل</a>
                </div>
            </div>

            <div class="md:col-span-3">
                <p class="eyebrow !text-gold">تواصل</p>
                <div class="mt-5 space-y-4 text-sm text-ivory/75">
                    @if (! empty($siteSettings['contact_email']))
                        <a href="mailto:{{ $siteSettings['contact_email'] }}" class="flex items-start gap-3 hover:text-gold">
                            <i class="fa-solid fa-envelope mt-0.5 w-4 shrink-0 text-gold" aria-hidden="true"></i>
                            <span class="min-w-0 break-all">{{ $siteSettings['contact_email'] }}</span>
                        </a>
                    @endif

                    @if (! empty($siteSettings['contact_phone']))
                        <a href="tel:{{ $siteSettings['contact_phone'] }}" class="flex items-start gap-3 hover:text-gold">
                            <i class="fa-solid fa-phone mt-0.5 w-4 shrink-0 text-gold" aria-hidden="true"></i>
                            <span dir="ltr">{{ $siteSettings['contact_phone'] }}</span>
                        </a>
                    @endif

                    @if (! empty($siteSettings['contact_address_ar']))
                        @if (! empty($mapExternalUrl))
                            <a href="{{ $mapExternalUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-start gap-3 hover:text-gold">
                                <i class="fa-solid fa-location-dot mt-0.5 w-4 shrink-0 text-gold" aria-hidden="true"></i>
                                <span>{{ $siteSettings['contact_address_ar'] }}</span>
                            </a>
                        @else
                            <p class="flex items-start gap-3">
                                <i class="fa-solid fa-location-dot mt-0.5 w-4 shrink-0 text-gold" aria-hidden="true"></i>
                                <span>{{ $siteSettings['contact_address_ar'] }}</span>
                            </p>
                        @endif
                    @endif
                </div>

                @if (! empty($socialLinks))
                    <div class="mt-6">
                        @include('partials.frontend.social-links', [
                            'tone' => 'dark',
                            'class' => 'flex flex-wrap items-center gap-2.5',
                        ])
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-3 border-t border-ivory/10 pt-6 text-xs text-ivory/45 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ $siteSettings['site_name_en'] ?? 'RAQI DECOR' }}</p>
            <a
                href="https://zeusmediaeg.com/"
                target="_blank"
                rel="noopener noreferrer"
                class="hover:text-gold"
            >
                صنع بواسطة zeus media
            </a>
        </div>
    </div>
</footer>
