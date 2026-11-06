<?php

namespace Tests\Feature;

use App\Enums\AdminPermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_all_permissions_can_access_every_section(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.projects.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.categories.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.messages.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_limited_admin_can_only_access_assigned_sections(): void
    {
        $admin = User::factory()->withPermissions([
            AdminPermission::Messages,
        ])->create();

        $this->actingAs($admin)
            ->get(route('admin.messages.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.projects.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertForbidden();
    }

    public function test_admin_can_assign_permissions_when_creating_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'محرر',
                'email' => 'editor@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'is_admin' => '1',
                'permissions' => [
                    AdminPermission::Projects->value,
                    AdminPermission::Categories->value,
                ],
            ])
            ->assertRedirect(route('admin.users.index'));

        $user = User::query()->where('email', 'editor@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->is_admin);
        $this->assertTrue($user->hasPermission(AdminPermission::Projects));
        $this->assertTrue($user->hasPermission(AdminPermission::Categories));
        $this->assertFalse($user->hasPermission(AdminPermission::Users));
    }

    public function test_admin_user_requires_at_least_one_permission(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'بدون صلاحيات',
                'email' => 'noperms@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'is_admin' => '1',
                'permissions' => [],
            ])
            ->assertSessionHasErrors('permissions');
    }

    public function test_nav_hides_sections_without_permission(): void
    {
        $admin = User::factory()->withPermissions([
            AdminPermission::Dashboard,
            AdminPermission::Settings,
        ])->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('إعدادات الموقع')
            ->assertDontSee('>الأعمال<', false)
            ->assertDontSee('>المستخدمون<', false);
    }
}
