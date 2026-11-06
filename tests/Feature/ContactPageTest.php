<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_embeds_map_from_coordinates(): void
    {
        $this->seed(SiteSettingSeeder::class);

        SiteSetting::query()->updateOrCreate(
            ['key' => 'social_links'],
            [
                'value' => json_encode([
                    ['label' => 'Instagram', 'url' => 'https://instagram.com/raqidecor'],
                    ['label' => 'Snapchat', 'url' => 'https://snapchat.com/add/raqidecor'],
                ], JSON_UNESCAPED_SLASHES),
                'group' => 'social',
            ]
        );

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('maps.google.com/maps?q=24.527918,46.710465', false)
            ->assertSee('output=embed', false)
            ->assertSee('aria-label="Instagram"', false)
            ->assertSee('aria-label="Snapchat"', false);
    }

    public function test_contact_page_embeds_map_from_coordinate_url_without_saved_lat_lng(): void
    {
        $this->seed(SiteSettingSeeder::class);

        SiteSetting::query()->updateOrCreate(
            ['key' => 'contact_map_lat'],
            ['value' => '', 'group' => 'contact']
        );
        SiteSetting::query()->updateOrCreate(
            ['key' => 'contact_map_lng'],
            ['value' => '', 'group' => 'contact']
        );
        SiteSetting::query()->updateOrCreate(
            ['key' => 'contact_map_url'],
            [
                'value' => 'https://www.google.com/maps/@24.527918,46.710465,16z',
                'group' => 'contact',
            ]
        );
        SiteSetting::forgetCache();

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('maps.google.com/maps?q=24.527918,46.710465', false)
            ->assertSee('output=embed', false);
    }

    public function test_saving_map_url_with_coordinates_hydrates_missing_lat_lng(): void
    {
        $this->seed(SiteSettingSeeder::class);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'site_name_ar' => 'راقي ديكور',
                'site_name_en' => 'RAQI DECOR',
                'contact_map_url' => 'https://www.google.com/maps/@24.527918,46.710465,16z',
                'contact_map_lat' => '',
                'contact_map_lng' => '',
            ])
            ->assertRedirect();

        $this->assertSame('24.527918', SiteSetting::query()->where('key', 'contact_map_lat')->value('value'));
        $this->assertSame('46.710465', SiteSetting::query()->where('key', 'contact_map_lng')->value('value'));

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('output=embed', false);
    }
}
