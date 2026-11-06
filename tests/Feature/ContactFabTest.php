<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFabTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_contact_fab_with_available_channels(): void
    {
        $this->seed(SiteSettingSeeder::class);

        SiteSetting::query()->where('key', 'contact_whatsapp')->update([
            'value' => '00966501094405',
        ]);
        SiteSetting::query()->where('key', 'contact_phone')->update([
            'value' => '+966501094405',
        ]);
        SiteSetting::query()->where('key', 'contact_email')->update([
            'value' => 'hello@raqidecor.test',
        ]);
        SiteSetting::query()->where('key', 'social_links')->update([
            'value' => json_encode([
                ['label' => 'Instagram', 'url' => 'https://instagram.com/raqidecor'],
                ['label' => 'Facebook', 'url' => 'https://facebook.com/raqidecor'],
                ['label' => 'TikTok', 'url' => 'https://tiktok.com/@raqidecor'],
                ['label' => 'Snapchat', 'url' => 'https://snapchat.com/add/raqidecor'],
            ], JSON_UNESCAPED_SLASHES),
        ]);
        SiteSetting::forgetCache();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-contact-fab', false)
            ->assertSee('https://wa.me/966501094405', false)
            ->assertSee('tel:+966501094405', false)
            ->assertSee('mailto:hello@raqidecor.test', false)
            ->assertSee('https://instagram.com/raqidecor', false)
            ->assertSee('https://facebook.com/raqidecor', false)
            ->assertSee('https://tiktok.com/@raqidecor', false)
            ->assertSee('https://snapchat.com/add/raqidecor', false)
            ->assertSee('خيارات التواصل')
            ->assertSee('تواصل معنا');
    }

    public function test_contact_fab_is_hidden_when_no_channels_are_configured(): void
    {
        $this->seed(SiteSettingSeeder::class);

        SiteSetting::query()->whereIn('key', [
            'contact_whatsapp',
            'contact_phone',
            'contact_email',
        ])->update([
            'value' => '',
        ]);
        SiteSetting::query()->where('key', 'social_links')->update([
            'value' => '[]',
        ]);
        SiteSetting::forgetCache();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('data-contact-fab', false);
    }
}
