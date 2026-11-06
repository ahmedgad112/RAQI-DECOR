<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $residential = Category::query()->where('slug', 'residential')->first();
        $commercial = Category::query()->where('slug', 'commercial')->first();

        if (! $residential || ! $commercial) {
            return;
        }

        $projects = [
            [
                'category_id' => $residential->id,
                'title_ar' => 'فيلا الهدوء',
                'title_en' => 'Villa Al Hudu',
                'slug' => 'villa-al-hudu',
                'excerpt_ar' => 'مسكن يجمع بين الدفء الكلاسيكي والخطوط المعاصرة.',
                'excerpt_en' => 'A residence balancing classical warmth with contemporary lines.',
                'description_ar' => "اعتمد التصميم على لوحة ألوان هادئة ومساحات مفتوحة تسمح للضوء الطبيعي بتشكيل الإيقاع اليومي للمنزل.\nتم اختيار خامات طبيعية وتفاصيل معمارية دقيقة لخلق إحساس بالفخامة الهادئة دون مبالغة.",
                'description_en' => 'A calm residential composition built around natural light, refined materials, and restrained luxury.',
                'location' => 'Riyadh',
                'year' => '2024',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now()->subMonths(2),
                'sort_order' => 1,
            ],
            [
                'category_id' => $residential->id,
                'title_ar' => 'شقة الأناقة',
                'title_en' => 'Apartment Anaqa',
                'slug' => 'apartment-anaqa',
                'excerpt_ar' => 'مساحة حضرية أنيقة بتفاصيل دقيقة وإيقاع بصري هادئ.',
                'excerpt_en' => 'An urban apartment with precise detailing and quiet rhythm.',
                'description_ar' => 'ركز المشروع على تحسين التدفق البصري بين المناطق المفتوحة، مع أثاث مخصص وإضاءة معمارية ناعمة.',
                'description_en' => 'Focused on spatial flow, custom furniture, and soft architectural lighting.',
                'location' => 'Jeddah',
                'year' => '2025',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now()->subMonth(),
                'sort_order' => 2,
            ],
            [
                'category_id' => $commercial->id,
                'title_ar' => 'مكتب الأفق',
                'title_en' => 'Horizon Office',
                'slug' => 'horizon-office',
                'excerpt_ar' => 'بيئة عمل راقية تعزز التركيز والوضوح.',
                'excerpt_en' => 'A refined workplace designed for clarity and focus.',
                'description_ar' => 'تصميم مكتبي يجمع بين المواد الدافئة والخطوط النظيفة ليخلق حضوراً مهنياً هادئاً.',
                'description_en' => 'Warm materials and clean geometry create a confident professional presence.',
                'location' => 'Dammam',
                'year' => '2025',
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subWeeks(2),
                'sort_order' => 3,
            ],
        ];

        foreach ($projects as $project) {
            Project::query()->updateOrCreate(
                ['slug' => $project['slug']],
                [
                    ...$project,
                    'meta_title' => $project['title_en'].' | RAQI DECOR',
                    'meta_description' => $project['excerpt_en'],
                ]
            );
        }
    }
}
