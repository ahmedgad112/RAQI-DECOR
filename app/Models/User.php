<?php

namespace App\Models;

use App\Enums\AdminPermission;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'permissions'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'permissions' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (User $user): void {
            $user->profile()->firstOrCreate([]);
        });
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function hasPermission(AdminPermission|string $permission): bool
    {
        if (! $this->isAdmin()) {
            return false;
        }

        $value = $permission instanceof AdminPermission
            ? $permission->value
            : $permission;

        $permissions = $this->normalizedPermissions();

        return in_array('*', $permissions, true)
            || in_array($value, $permissions, true);
    }

    public function hasAnyPermission(AdminPermission ...$permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    public function normalizedPermissions(): array
    {
        $permissions = $this->permissions;

        if (! is_array($permissions) || $permissions === []) {
            return $this->isAdmin() ? AdminPermission::values() : [];
        }

        if (in_array('*', $permissions, true)) {
            return ['*'];
        }

        return array_values(array_intersect($permissions, AdminPermission::values()));
    }

    public function defaultAdminRoute(): string
    {
        foreach (AdminPermission::cases() as $permission) {
            if ($this->hasPermission($permission)) {
                return route($permission->routeName());
            }
        }

        return route('admin.login');
    }
}
