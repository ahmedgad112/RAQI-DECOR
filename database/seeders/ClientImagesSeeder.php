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

class ClientImagesSeeder extends Seeder
{
    public function run(): void
    {
        $imageService = app(ImageService::class);
        $projectService = app(ProjectService::class);

        $folder = public_path('images/WhatsApp Unknown 2026-11-06 at 2.57.01 AM');
        $extra = public_path('images/WhatsApp Image 2026-11-06 at 12.57.05 AM.jpeg');

        $files = collect(File::files($folder))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true))
            ->sortBy(fn ($file) => $file->getFilename())
            ->values();

        if (is_file($extra)) {
            $files->prepend(new \SplFileInfo($extra));
        }

        if ($files->isEmpty()) {
            $this->command?->warn('No client images found.');

            return;
        }

        $paths = $files->map(fn ($file) => $file->getPathname())->values()->all();

        // Hero: first strong living-room frame
        $heroPath = $paths[0] ?? null;
        if ($heroPath) {
            $stored = $imageService->store($this->upload($heroPath), 'hero');
            SiteSetting::query()->updateOrCreate(
                ['key' => 'hero_image'],
                ['value' => $stored['path_large'], 'group' => 'hero']
            );
            SiteSetting::forgetCache();
        }

        $categoryImages = [
            'residential' => $paths[1] ?? $paths[0],
            'commercial' => $paths[8] ?? $paths[2] ?? $paths[0],
            'hospitality' => $paths[12] ?? $paths[3] ?? $paths[0],
        ];

        foreach ($categoryImages as $slug => $path) {
            $category = Category::query()->where('slug', $slug)->first();
            if (! $category || ! $path) {
                continue;
            }

            $stored = $imageService->store($this->upload($path), 'categories');
            $category->update(['image_path' => $stored['path_medium']]);
        }

        $projectBuckets = [
            'villa-al-hudu' => array_slice($paths, 0, 7),
            'apartment-anaqa' => array_slice($paths, 7, 7),
            'horizon-office' => array_slice($paths, 14, 7),
        ];

        foreach ($projectBuckets as $slug => $projectPaths) {
            $project = Project::query()->where('slug', $slug)->first();
            if (! $project || $projectPaths === []) {
                continue;
            }

            foreach ($project->images as $image) {
                $projectService->deleteImage($image);
            }

            $project->refresh();

            $coverPath = $projectPaths[0];
            $storedCover = $imageService->store($this->upload($coverPath), 'projects');
            $project->update(['cover_image' => $storedCover['path_large']]);
            $project->images()->create([
                ...$storedCover,
                'sort_order' => 0,
                'is_cover' => true,
                'alt_ar' => $project->title_ar,
                'alt_en' => $project->title_en,
            ]);

            $order = 1;
            foreach (array_slice($projectPaths, 1) as $galleryPath) {
                $stored = $imageService->store($this->upload($galleryPath), 'projects');
                $project->images()->create([
                    ...$stored,
                    'sort_order' => $order++,
                    'is_cover' => false,
                    'alt_ar' => $project->title_ar,
                    'alt_en' => $project->title_en,
                ]);
            }
        }

        $this->command?->info('Client WhatsApp images imported successfully.');
    }

    private function upload(string $path): UploadedFile
    {
        return new UploadedFile(
            $path,
            basename($path),
            mime_content_type($path) ?: 'image/jpeg',
            null,
            true
        );
    }
}
