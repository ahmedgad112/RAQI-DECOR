<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Services\ImageService;
use App\Services\ProjectService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class DemoImagesSeeder extends Seeder
{
    public function run(): void
    {
        $imageService = app(ImageService::class);
        $projectService = app(ProjectService::class);
        $base = storage_path('app/seed-images');

        if (! File::isDirectory($base)) {
            $this->command?->warn('seed-images folder missing.');

            return;
        }

        $hero = $this->asUpload($base.'/hero.jpg', 'hero.jpg');
        if ($hero) {
            $stored = $imageService->store($hero, 'hero');
            SiteSetting::query()->updateOrCreate(
                ['key' => 'hero_image'],
                ['value' => $stored['path_large'], 'group' => 'hero']
            );
            SiteSetting::forgetCache();
        }

        $categoryMap = [
            'residential' => 'residential.jpg',
            'commercial' => 'commercial.jpg',
            'hospitality' => 'hospitality.jpg',
        ];

        foreach ($categoryMap as $slug => $file) {
            $category = Category::query()->where('slug', $slug)->first();
            $upload = $this->asUpload($base.'/'.$file, $file);

            if (! $category || ! $upload) {
                continue;
            }

            $stored = $imageService->store($upload, 'categories');
            $category->update(['image_path' => $stored['path_medium']]);
        }

        $projectMap = [
            'villa-al-hudu' => [
                'cover' => 'villa.jpg',
                'gallery' => ['villa-2.jpg', 'residential.jpg'],
            ],
            'apartment-anaqa' => [
                'cover' => 'apartment.jpg',
                'gallery' => ['apartment-2.jpg', 'hospitality.jpg'],
            ],
            'horizon-office' => [
                'cover' => 'office.jpg',
                'gallery' => ['office-2.jpg', 'commercial.jpg'],
            ],
        ];

        foreach ($projectMap as $slug => $files) {
            $project = Project::query()->where('slug', $slug)->first();
            $cover = $this->asUpload($base.'/'.$files['cover'], $files['cover']);

            if (! $project || ! $cover) {
                continue;
            }

            foreach ($project->images as $image) {
                $projectService->deleteImage($image);
            }

            $project->refresh();

            $storedCover = $imageService->store($cover, 'projects');
            $project->update(['cover_image' => $storedCover['path_large']]);
            $project->images()->create([
                ...$storedCover,
                'sort_order' => 0,
                'is_cover' => true,
                'alt_ar' => $project->title_ar,
                'alt_en' => $project->title_en,
            ]);

            $order = 1;
            foreach ($files['gallery'] as $galleryFile) {
                $galleryUpload = $this->asUpload($base.'/'.$galleryFile, $galleryFile);
                if (! $galleryUpload) {
                    continue;
                }

                $stored = $imageService->store($galleryUpload, 'projects');
                $project->images()->create([
                    ...$stored,
                    'sort_order' => $order++,
                    'is_cover' => false,
                    'alt_ar' => $project->title_ar,
                    'alt_en' => $project->title_en,
                ]);
            }
        }
    }

    private function asUpload(string $path, string $name): ?UploadedFile
    {
        if (! is_file($path)) {
            return null;
        }

        return new UploadedFile($path, $name, mime_content_type($path) ?: 'image/jpeg', null, true);
    }
}
