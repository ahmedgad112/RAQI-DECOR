<?php

namespace Tests\Feature;

use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterCreditTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_shows_zeus_media_credit_link_and_contact_channels(): void
    {
        $this->seed(SiteSettingSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('صنع بواسطة zeus media')
            ->assertSee('https://zeusmediaeg.com/', false)
            ->assertDontSee('MODERN INTERIOR STUDIO')
            ->assertSee('fa-solid fa-phone', false)
            ->assertSee('fa-brands fa-whatsapp mt-0.5 w-4 shrink-0 text-gold', false)
            ->assertSee('fa-solid fa-location-dot', false)
            ->assertSee('https://wa.me/966501094405', false);
    }
}
