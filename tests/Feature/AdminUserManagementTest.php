<?php

namespace Tests\Feature;

use App\Enums\AdminPermission;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user_with_profile(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $avatar = UploadedFile::fake()->image('avatar.jpg', 400, 400);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'سارة أحمد',
                'email' => 'sara@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'is_admin' => '1',
                'permissions' => AdminPermission::values(),
                'phone' => '0501094405',
                'job_title' => 'مصممة داخلية',
                'bio' => 'مهتمة بالتصميم الفاخر',
                'avatar' => $avatar,
            ])
            ->assertRedirect(route('admin.users.index'));

        $user = User::query()->where('email', 'sara@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->is_admin);
        $this->assertSame(AdminPermission::values(), $user->normalizedPermissions());
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'phone' => '0501094405',
            'job_title' => 'مصممة داخلية',
            'bio' => 'مهتمة بالتصميم الفاخر',
        ]);
        $this->assertNotEmpty($user->profile?->avatar_path);
        $this->assertTrue(Storage::disk('public')->exists($user->profile->avatar_path));
    }

    public function test_new_user_always_gets_a_profile(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(Profile::class, $user->profile);
        $this->assertDatabaseHas('profiles', ['user_id' => $user->id]);
    }

    public function test_admin_can_update_user_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'name' => 'قديم',
            'email' => 'old@example.com',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'جديد',
                'email' => 'new@example.com',
                'is_admin' => '1',
                'permissions' => [
                    AdminPermission::Dashboard->value,
                    AdminPermission::Messages->value,
                ],
                'phone' => '0555555555',
                'job_title' => 'مدير مشاريع',
                'bio' => 'تحديث النبذة',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'جديد',
            'email' => 'new@example.com',
        ]);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'phone' => '0555555555',
            'job_title' => 'مدير مشاريع',
            'bio' => 'تحديث النبذة',
        ]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('profiles', ['user_id' => $user->id]);
    }

    public function test_guest_cannot_manage_users(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }
}
