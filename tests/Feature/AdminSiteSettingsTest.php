<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_and_remove_hero_image(): void
    {
        Storage::fake('public');
        $this->seed(SiteSettingSeeder::class);

        $admin = User::factory()->admin()->create();
        $hero = UploadedFile::fake()->image('hero.jpg', 1920, 1080);

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'site_name_ar' => 'راقي ديكور',
                'site_name_en' => 'RAQI DECOR',
                'hero_image_position' => 'top',
                'hero_image' => $hero,
            ])
            ->assertRedirect();

        $path = SiteSetting::query()->where('key', 'hero_image')->value('value');
        $this->assertNotEmpty($path);
        $this->assertTrue(Storage::disk('public')->exists($path));
        $this->assertSame('top', SiteSetting::query()->where('key', 'hero_image_position')->value('value'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('object-top', false);

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'site_name_ar' => 'راقي ديكور',
                'site_name_en' => 'RAQI DECOR',
                'hero_image_position' => 'center',
                'remove_hero_image' => '1',
            ])
            ->assertRedirect();

        $this->assertSame('', (string) SiteSetting::query()->where('key', 'hero_image')->value('value'));
        $this->assertFalse(Storage::disk('public')->exists($path));
    }

    public function test_admin_can_update_settings_and_manage_social_links(): void
    {
        $this->seed(SiteSettingSeeder::class);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'site_name_ar' => 'راقي ديكور',
                'site_name_en' => 'RAQI DECOR',
                'tagline_ar' => 'تصميم راقٍ',
                'contact_email' => 'hello@raqidecor.com',
                'social_links' => [
                    ['label' => 'Instagram', 'url' => 'https://instagram.com/raqidecor'],
                    ['label' => 'Facebook', 'url' => 'https://facebook.com/raqidecor'],
                    ['label' => '', 'url' => ''],
                ],
                'projects_completed' => 12,
                'years_experience' => 5,
                'clients_count' => 40,
            ])
            ->assertRedirect();

        $stored = SiteSetting::query()->where('key', 'social_links')->value('value');
        $decoded = json_decode((string) $stored, true);

        $this->assertIsArray($decoded);
        $this->assertCount(2, $decoded);
        $this->assertSame('Instagram', $decoded[0]['label']);
        $this->assertSame('https://instagram.com/raqidecor', $decoded[0]['url']);
        $this->assertSame('12', SiteSetting::query()->where('key', 'projects_completed')->value('value'));

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'site_name_ar' => 'راقي ديكور',
                'site_name_en' => 'RAQI DECOR',
                'social_links' => [
                    ['label' => 'Instagram', 'url' => 'https://instagram.com/raqidecor'],
                ],
            ])
            ->assertRedirect();

        $stored = SiteSetting::query()->where('key', 'social_links')->value('value');
        $decoded = json_decode((string) $stored, true);

        $this->assertCount(1, $decoded);
        $this->assertSame('Instagram', $decoded[0]['label']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('https://instagram.com/raqidecor', false)
            ->assertSee('Instagram');
    }

    public function test_guest_cannot_update_settings(): void
    {
        $this->put(route('admin.settings.update'), [
            'site_name_ar' => 'راقي ديكور',
            'site_name_en' => 'RAQI DECOR',
        ])->assertRedirect(route('admin.login'));
    }
}
