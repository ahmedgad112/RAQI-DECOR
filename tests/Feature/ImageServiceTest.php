<?php

namespace Tests\Feature;

use App\Services\ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_webp_variants(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('room.jpg', 1800, 1200);
        $paths = app(ImageService::class)->store($file, 'projects');

        Storage::disk('public')->assertExists($paths['path']);
        Storage::disk('public')->assertExists($paths['path_thumb']);
        Storage::disk('public')->assertExists($paths['path_medium']);
        Storage::disk('public')->assertExists($paths['path_large']);

        $this->assertSame(1800, $paths['width']);
        $this->assertSame(1200, $paths['height']);
    }
}
