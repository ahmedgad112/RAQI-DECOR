<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_ar' => 'سكني',
                'name_en' => 'Residential',
                'slug' => 'residential',
                'description_ar' => 'تصاميم داخلية للمنازل والفلل والشقق.',
                'description_en' => 'Refined interiors for homes, villas, and apartments.',
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'تجاري',
                'name_en' => 'Commercial',
                'slug' => 'commercial',
                'description_ar' => 'مساحات عمل وتجارة بطابع معماري فاخر.',
                'description_en' => 'Elegant commercial and workplace environments.',
                'sort_order' => 2,
            ],
            [
                'name_ar' => 'ضيافة',
                'name_en' => 'Hospitality',
                'slug' => 'hospitality',
                'description_ar' => 'تجارب ضيافة هادئة ومميزة.',
                'description_en' => 'Calm hospitality experiences with lasting presence.',
                'sort_order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [...$category, 'is_active' => true]
            );
        }
    }
}
