<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_message_from_index(): void
    {
        $admin = User::factory()->admin()->create();
        $message = ContactMessage::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.messages.index'))
            ->assertOk()
            ->assertSee('حذف');

        $this->actingAs($admin)
            ->from(route('admin.messages.index'))
            ->delete(route('admin.messages.destroy', $message))
            ->assertRedirect(route('admin.messages.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('contact_messages', [
            'id' => $message->id,
        ]);
    }

    public function test_guest_cannot_delete_message(): void
    {
        $message = ContactMessage::factory()->create();

        $this->delete(route('admin.messages.destroy', $message))
            ->assertRedirect();

        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
        ]);
    }
}
