<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SiteSettingSeeder::class,
            CategorySeeder::class,
            ProjectSeeder::class,
            ClientImagesSeeder::class,
        ]);
    }
}
