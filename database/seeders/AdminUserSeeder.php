<?php

namespace Database\Seeders;

use App\Enums\AdminPermission;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@raqidecor.com'],
            [
                'name' => 'RAQI Admin',
                'password' => 'password',
                'is_admin' => true,
                'permissions' => AdminPermission::values(),
                'email_verified_at' => now(),
            ]
        );
    }
}
