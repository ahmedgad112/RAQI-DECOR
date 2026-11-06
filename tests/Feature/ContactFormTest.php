<?php

namespace Tests\Feature;

use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_stores_message(): void
    {
        $this->seed(SiteSettingSeeder::class);

        $this->post(route('contact.store'), [
            'name' => 'عميل تجريبي',
            'email' => 'client@example.com',
            'phone' => '+966500000000',
            'subject' => 'استشارة تصميم',
            'message' => 'أرغب في تصميم فيلا جديدة بتفاصيل فاخرة وهادئة.',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'client@example.com',
            'name' => 'عميل تجريبي',
        ]);
    }

    public function test_honeypot_field_is_rejected(): void
    {
        $this->from(route('contact'))
            ->post(route('contact.store'), [
                'name' => 'Bot',
                'email' => 'bot@example.com',
                'message' => 'This should fail validation for bots.',
                'website' => 'http://spam.test',
            ])
            ->assertSessionHasErrors('website');
    }
}
