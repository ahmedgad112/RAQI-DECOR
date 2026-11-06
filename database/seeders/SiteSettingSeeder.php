<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name_ar' => 'راقي ديكور',
            'site_name_en' => 'RAQI DECOR',
            'tagline_ar' => 'تصميم داخلي فاخر بطابع معماري راقٍ',
            'tagline_en' => 'Classic luxury meets modern minimalism',
            'hero_title_ar' => 'نحوّل المساحات إلى تجارب معيشة راقية',
            'hero_title_en' => 'Interior Architecture',
            'hero_subtitle_ar' => 'استوديو تصميم داخلي يجمع بين الفخامة الكلاسيكية والبساطة المعاصرة.',
            'hero_subtitle_en' => 'Thoughtful interiors crafted with calm confidence.',
            'hero_image_position' => 'center',
            'about_ar' => "راقي ديكور استوديو متخصص في التصميم الداخلي والأثاث الفاخر.\nنؤمن بأن المساحة الجيدة تولد من التوازن بين الجمال والوظيفة، وبين الهدوء البصري والتفاصيل المعمارية الدقيقة.\nنعمل مع عملائنا لتحويل المنازل والمساحات التجارية إلى بيئات تعكس شخصيتهم وتدوم مع الزمن.",
            'about_en' => 'RAQI DECOR is an interior design studio focused on refined residential and commercial spaces. We blend classic luxury with modern restraint to create calm, confident interiors.',
            'contact_email' => 'hello@raqidecor.com',
            'contact_phone' => '00966501094405',
            'contact_whatsapp' => '00966501094405',
            'contact_address_ar' => 'صناعية الشفا، بدر، الرياض 14727',
            'contact_address_en' => 'Al Shifa Industrial Area, Badr, Riyadh 14727',
            'contact_map_url' => 'https://maps.app.goo.gl/aUPpKLkPHEEJkAREA',
            'contact_map_lat' => '24.527918',
            'contact_map_lng' => '46.710465',
            'social_links' => '[]',
            'seo_title' => 'RAQI DECOR | راقي ديكور — تصميم داخلي فاخر',
            'seo_description' => 'استوديو راقي ديكور للتصميم الداخلي الفاخر: كلاسيكي أنيق، مودرن هادئ، وتفاصيل معمارية راقية.',
            'projects_completed' => '0',
            'years_experience' => '0',
            'clients_count' => '0',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => match (true) {
                        str_starts_with($key, 'seo_') => 'seo',
                        $key === 'social_links' || str_starts_with($key, 'social_') => 'social',
                        str_starts_with($key, 'contact_') => 'contact',
                        str_starts_with($key, 'about_') => 'about',
                        str_starts_with($key, 'hero_') => 'hero',
                        default => 'general',
                    },
                ]
            );
        }
    }
}
