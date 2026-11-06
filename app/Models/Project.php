<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'category_id',
    'title_ar',
    'title_en',
    'slug',
    'excerpt_ar',
    'excerpt_en',
    'description_ar',
    'description_en',
    'location',
    'year',
    'client',
    'cover_image',
    'meta_title',
    'meta_description',
    'is_featured',
    'is_published',
    'published_at',
    'sort_order',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project): void {
            if (blank($project->slug)) {
                $project->slug = static::uniqueSlug($project->title_en ?: $project->title_ar);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function coverImageRelation(): HasOne
    {
        return $this->hasOne(ProjectImage::class)->where('is_cover', true);
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(function (Builder $builder): void {
                $builder->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    #[Scope]
    protected function featured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('published_at')->orderByDesc('id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function coverImage(): ?ProjectImage
    {
        if ($this->relationLoaded('coverImageRelation') && $this->coverImageRelation) {
            return $this->coverImageRelation;
        }

        if ($this->relationLoaded('images')) {
            return $this->images->firstWhere('is_cover', true) ?? $this->images->first();
        }

        return $this->coverImageRelation()->first()
            ?? $this->images()->orderBy('sort_order')->first();
    }

    public function coverUrl(string $variant = 'medium'): string
    {
        $image = $this->coverImage();

        if ($image) {
            return $image->url($variant);
        }

        if (filled($this->cover_image)) {
            $candidate = preg_replace('#/(?:original|thumb|medium|large)/#', "/{$variant}/", $this->cover_image) ?: $this->cover_image;

            if ($candidate !== $this->cover_image && Storage::disk('public')->exists($candidate)) {
                return Storage::disk('public')->url($candidate);
            }

            return Storage::disk('public')->url($this->cover_image);
        }

        return asset('images/project-placeholder.svg');
    }

    public static function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'project';
        $slug = $base;
        $counter = 1;

        while (static::query()
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
