<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageService
{
    /**
     * @var array<string, int>
     */
    private const VARIANTS = [
        'thumb' => 480,
        'medium' => 960,
        'large' => 1600,
    ];

    /**
     * Store an uploaded image and generate optimized variants.
     *
     * @return array{
     *     path: string,
     *     path_thumb: string,
     *     path_medium: string,
     *     path_large: string,
     *     width: int,
     *     height: int
     * }
     */
    public function store(UploadedFile $file, string $directory = 'projects'): array
    {
        $this->assertValidImage($file);

        $disk = Storage::disk('public');
        $basename = Str::uuid()->toString();
        $directory = trim($directory, '/');
        $originalRelative = $directory.'/original/'.$basename.'.webp';

        $source = $this->createImageResource($file->getRealPath());
        $width = imagesx($source);
        $height = imagesy($source);

        $disk->makeDirectory($directory.'/original');
        $this->writeWebp($source, $disk->path($originalRelative), 88);

        $paths = [
            'path' => $originalRelative,
            'path_thumb' => $this->storeVariant($source, $directory, $basename, 'thumb', $disk),
            'path_medium' => $this->storeVariant($source, $directory, $basename, 'medium', $disk),
            'path_large' => $this->storeVariant($source, $directory, $basename, 'large', $disk),
            'width' => $width,
            'height' => $height,
        ];

        imagedestroy($source);

        return $paths;
    }

    /**
     * @param  list<string>  $paths
     */
    public function deleteMany(array $paths): void
    {
        $paths = array_values(array_filter($paths));

        if ($paths === []) {
            return;
        }

        Storage::disk('public')->delete($paths);
    }

    private function storeVariant(
        \GdImage $source,
        string $directory,
        string $basename,
        string $variant,
        mixed $disk,
    ): string {
        $maxWidth = self::VARIANTS[$variant];
        $relative = $directory.'/'.$variant.'/'.$basename.'.webp';
        $disk->makeDirectory($directory.'/'.$variant);

        $resized = $this->resize($source, $maxWidth);
        $this->writeWebp($resized, $disk->path($relative), $variant === 'thumb' ? 80 : 85);

        if ($resized !== $source) {
            imagedestroy($resized);
        }

        return $relative;
    }

    private function resize(\GdImage $source, int $maxWidth): \GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);

        if ($width <= $maxWidth) {
            return $source;
        }

        $newWidth = $maxWidth;
        $newHeight = (int) round(($height / $width) * $newWidth);
        $canvas = imagecreatetruecolor($newWidth, $newHeight);

        if ($canvas === false) {
            throw new RuntimeException('Unable to create resized image canvas.');
        }

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $canvas;
    }

    private function writeWebp(\GdImage $image, string $absolutePath, int $quality): void
    {
        if (! imagewebp($image, $absolutePath, $quality)) {
            throw new RuntimeException('Unable to write WebP image.');
        }
    }

    private function createImageResource(string $path): \GdImage
    {
        $info = getimagesize($path);

        if ($info === false) {
            throw new RuntimeException('Invalid image file.');
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            default => false,
        };

        if ($image === false) {
            throw new RuntimeException('Unsupported image type.');
        }

        return $image;
    }

    private function assertValidImage(UploadedFile $file): void
    {
        $path = $file->getRealPath();

        if ($path === false || @getimagesize($path) === false) {
            throw new RuntimeException('The uploaded file is not a valid image.');
        }
    }
}
