<?php

namespace Database\Factories;

use App\Enums\AdminPermission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'is_admin' => false,
            'permissions' => [],
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
            'permissions' => AdminPermission::values(),
        ]);
    }

    /**
     * @param  list<string|AdminPermission>  $permissions
     */
    public function withPermissions(array $permissions): static
    {
        $values = array_map(
            fn ($permission) => $permission instanceof AdminPermission ? $permission->value : $permission,
            $permissions,
        );

        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
            'permissions' => array_values(array_intersect($values, AdminPermission::values())),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
