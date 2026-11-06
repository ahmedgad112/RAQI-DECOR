<?php

namespace App\Models;

use Database\Factories\ProjectImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'project_id',
    'path',
    'path_thumb',
    'path_medium',
    'path_large',
    'alt_ar',
    'alt_en',
    'caption_ar',
    'caption_en',
    'sort_order',
    'is_cover',
    'width',
    'height',
])]
class ProjectImage extends Model
{
    /** @use HasFactory<ProjectImageFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_cover' => 'boolean',
            'sort_order' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function url(?string $variant = 'large'): string
    {
        $path = match ($variant) {
            'thumb' => $this->path_thumb ?: $this->path,
            'medium' => $this->path_medium ?: $this->path,
            'large' => $this->path_large ?: $this->path,
            default => $this->path,
        };

        return Storage::disk('public')->url($path);
    }

    /**
     * @return list<string>
     */
    public function allPaths(): array
    {
        return array_values(array_filter([
            $this->path,
            $this->path_thumb,
            $this->path_medium,
            $this->path_large,
        ]));
    }
}
