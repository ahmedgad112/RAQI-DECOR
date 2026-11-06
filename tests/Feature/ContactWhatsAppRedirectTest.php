<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactWhatsAppRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_flashes_whatsapp_url_for_configured_number(): void
    {
        $this->seed(SiteSettingSeeder::class);

        SiteSetting::query()->where('key', 'contact_whatsapp')->update([
            'value' => '00966501094405',
        ]);
        SiteSetting::forgetCache();

        $this->from(route('contact'))
            ->post(route('contact.store'), [
                'name' => 'عميل تجريبي',
                'email' => 'client@example.com',
                'phone' => '+966511111111',
                'subject' => 'استشارة تصميم',
                'message' => 'أرغب في تصميم فيلا جديدة بتفاصيل فاخرة وهادئة.',
            ])
            ->assertRedirect(route('contact'))
            ->assertSessionHas('whatsapp_url')
            ->assertSessionHas('success');

        $whatsappUrl = session('whatsapp_url');

        $this->assertIsString($whatsappUrl);
        $this->assertStringStartsWith('https://wa.me/966501094405?text=', $whatsappUrl);
        $this->assertStringContainsString(rawurlencode('عميل تجريبي'), $whatsappUrl);
        $this->assertStringContainsString(rawurlencode('client@example.com'), $whatsappUrl);
    }

    public function test_contact_page_shows_whatsapp_link_and_success_popup_markup_after_submit(): void
    {
        $this->seed(SiteSettingSeeder::class);

        SiteSetting::query()->where('key', 'contact_whatsapp')->update([
            'value' => '00966501094405',
        ]);
        SiteSetting::forgetCache();

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('https://wa.me/966501094405', false)
            ->assertSee('تواصل عبر واتساب')
            ->assertDontSee('data-success-popup', false);

        $this->from(route('contact'))
            ->followingRedirects()
            ->post(route('contact.store'), [
                'name' => 'عميل تجريبي',
                'email' => 'client@example.com',
                'phone' => '+966511111111',
                'subject' => 'استشارة تصميم',
                'message' => 'أرغب في تصميم فيلا جديدة بتفاصيل فاخرة وهادئة.',
            ])
            ->assertOk()
            ->assertSee('data-success-popup', false)
            ->assertSee('وصلتنا رسالتك')
            ->assertSee('فتح واتساب');
    }
}
