@extends('layouts.frontend')

@section('content')
<section class="pt-32">
    <div class="site-container section-space !pb-10 !pt-10">
        <p class="reveal eyebrow">Contact</p>
        <h1 class="reveal reveal-delay-1 heading-xl mt-5">تواصل معنا</h1>
        <p class="reveal reveal-delay-2 mt-5 max-w-xl text-brown/65">أخبرنا عن مساحتك، وسنعود إليك بخطوات واضحة لبدء التصميم.</p>
    </div>
</section>

<section class="pb-24 md:pb-32">
    <div class="site-container grid gap-6 lg:grid-cols-12">
        <div class="reveal space-y-5 lg:col-span-4">
            @if (! empty($settings['contact_email']))
                <div class="surface p-6">
                    <p class="eyebrow">Email</p>
                    <a href="mailto:{{ $settings['contact_email'] }}" class="mt-3 block text-base text-brown">{{ $settings['contact_email'] }}</a>
                </div>
            @endif
            @if (! empty($settings['contact_phone']))
                <div class="surface p-6">
                    <p class="eyebrow">Phone</p>
                    <a href="tel:{{ $settings['contact_phone'] }}" class="mt-3 block text-base text-brown" dir="ltr">{{ $settings['contact_phone'] }}</a>
                </div>
            @endif
            @if (! empty($whatsappUrl))
                <div class="surface p-6">
                    <p class="eyebrow">WhatsApp</p>
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="mt-3 block text-base text-brown hover:text-gold" dir="ltr">{{ $settings['contact_whatsapp'] }}</a>
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-gold-outline mt-4 inline-flex">تواصل عبر واتساب</a>
                </div>
            @endif
            @if (! empty($settings['contact_address_ar']))
                <div class="surface p-6">
                    <p class="eyebrow">Address</p>
                    <p class="mt-3 text-base text-brown">{{ $settings['contact_address_ar'] }}</p>
                    @if (! empty($mapExternalUrl))
                        <a href="{{ $mapExternalUrl }}" target="_blank" rel="noopener noreferrer" class="btn-ghost mt-5">افتح في خرائط جوجل</a>
                    @endif
                </div>
            @endif
            @if (! empty($socialLinks))
                <div class="surface p-6">
                    <p class="eyebrow">Social</p>
                    <div class="mt-4 space-y-3 text-base text-brown">
                        @foreach ($socialLinks as $link)
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="block hover:text-gold">{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="reveal reveal-delay-1 space-y-6 lg:col-span-8">
            <form method="POST" action="{{ route('contact.store') }}" class="surface p-6 md:p-9">
                @csrf
                <div class="hidden" aria-hidden="true">
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="admin-label" for="name">الاسم</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" class="admin-input" required>
                        @error('name') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="admin-label" for="email">البريد الإلكتروني</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="admin-input" required>
                        @error('email') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="admin-label" for="phone">الهاتف</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="admin-input" dir="ltr">
                        @error('phone') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="admin-label" for="subject">الموضوع</label>
                        <input id="subject" name="subject" type="text" value="{{ old('subject') }}" class="admin-input">
                        @error('subject') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label class="admin-label" for="message">رسالتك</label>
                    <textarea id="message" name="message" rows="6" class="admin-input" required>{{ old('message') }}</textarea>
                    @error('message') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>

                <p class="mt-6 text-sm text-brown/55">بعد الإرسال تُحفظ رسالتك لدينا، ويُفتح واتساب لإرسالها مباشرة.</p>
                <button type="submit" class="btn-solid mt-4">إرسال الرسالة</button>
            </form>

            @if (! empty($mapEmbedUrl))
                <div class="surface overflow-hidden p-0">
                    <div class="flex flex-wrap items-end justify-between gap-3 border-b border-line px-6 py-5">
                        <div>
                            <p class="eyebrow">Location</p>
                            <h2 class="mt-2 text-xl font-medium">موقعنا على الخريطة</h2>
                        </div>
                        @if (! empty($mapExternalUrl))
                            <a href="{{ $mapExternalUrl }}" target="_blank" rel="noopener noreferrer" class="btn-gold-outline !px-5 !py-2 text-xs">فتح الخريطة</a>
                        @endif
                    </div>
                    <div class="aspect-[16/10] w-full bg-mist">
                        <iframe
                            title="موقع راقي ديكور على خرائط جوجل"
                            src="{{ $mapEmbedUrl }}"
                            class="h-full w-full border-0"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
