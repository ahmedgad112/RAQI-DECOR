<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectService
{
    public function __construct(private ImageService $images) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<UploadedFile>|null  $gallery
     */
    public function create(array $data, ?UploadedFile $cover = null, ?array $gallery = null): Project
    {
        return DB::transaction(function () use ($data, $cover, $gallery) {
            $data = $this->preparePayload($data);
            $gallery = array_values(array_filter(
                $gallery ?? [],
                fn ($file): bool => $file instanceof UploadedFile
            ));

            if (! $cover instanceof UploadedFile && $gallery !== []) {
                $cover = array_shift($gallery);
            }

            if ($cover instanceof UploadedFile) {
                $stored = $this->images->store($cover, 'projects');
                $data['cover_image'] = $stored['path_large'];
            }

            $project = Project::query()->create($data);

            if ($cover instanceof UploadedFile && isset($stored)) {
                $this->attachImage($project, $stored, true);
            }

            foreach ($gallery as $file) {
                $this->attachImage($project, $this->images->store($file, 'projects'));
            }

            return $project->load(['category', 'images']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<UploadedFile>|null  $gallery
     */
    public function update(Project $project, array $data, ?UploadedFile $cover = null, ?array $gallery = null): Project
    {
        return DB::transaction(function () use ($project, $data, $cover, $gallery) {
            $data = $this->preparePayload($data, $project);
            $gallery = array_values(array_filter(
                $gallery ?? [],
                fn ($file): bool => $file instanceof UploadedFile
            ));

            $needsCover = ! $cover instanceof UploadedFile
                && blank($project->cover_image)
                && $gallery !== [];

            if ($needsCover) {
                $cover = array_shift($gallery);
            }

            if ($cover instanceof UploadedFile) {
                $stored = $this->images->store($cover, 'projects');
                $data['cover_image'] = $stored['path_large'];
                $project->images()->where('is_cover', true)->update(['is_cover' => false]);
                $this->attachImage($project, $stored, true);
            }

            $project->update($data);

            foreach ($gallery as $file) {
                $this->attachImage($project, $this->images->store($file, 'projects'));
            }

            return $project->fresh(['category', 'images']);
        });
    }

    public function delete(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $paths = [];

            foreach ($project->images as $image) {
                $paths = array_merge($paths, $image->allPaths());
            }

            if ($project->cover_image) {
                $paths[] = $project->cover_image;
            }

            $project->delete();
            $this->images->deleteMany(array_unique($paths));
        });
    }

    /**
     * @param  list<int>  $orderedIds
     */
    public function reorderImages(Project $project, array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            ProjectImage::query()
                ->where('project_id', $project->id)
                ->where('id', $id)
                ->update(['sort_order' => $index]);
        }
    }

    public function deleteImage(ProjectImage $image): void
    {
        $project = $image->project;
        $wasCover = $image->is_cover;
        $paths = $image->allPaths();

        $image->delete();
        $this->images->deleteMany($paths);

        if ($wasCover && $project) {
            $next = $project->images()->orderBy('sort_order')->first();

            if ($next) {
                $next->update(['is_cover' => true]);
                $project->update(['cover_image' => $next->path_large ?: $next->path]);
            } else {
                $project->update(['cover_image' => null]);
            }
        }
    }

    public function setCover(ProjectImage $image): void
    {
        $project = $image->project;

        $project->images()->update(['is_cover' => false]);
        $image->update(['is_cover' => true]);
        $project->update(['cover_image' => $image->path_large ?: $image->path]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function preparePayload(array $data, ?Project $project = null): array
    {
        if (filled($data['slug'] ?? null)) {
            $data['slug'] = Project::uniqueSlug(
                Str::slug((string) $data['slug']) ?: 'project',
                $project?->id
            );
        } elseif ($project) {
            unset($data['slug']);
        } else {
            $slugSource = $data['title_en'] ?? $data['title_ar'] ?? 'project';
            $data['slug'] = Project::uniqueSlug(
                Str::slug((string) $slugSource) ?: 'project'
            );
        }

        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        if ($data['is_published'] && empty($data['published_at']) && ! $project?->published_at) {
            $data['published_at'] = now();
        }

        if (! $data['is_published']) {
            $data['published_at'] = $data['published_at'] ?? null;
        }

        return $data;
    }

    /**
     * @param  array{
     *     path: string,
     *     path_thumb: string,
     *     path_medium: string,
     *     path_large: string,
     *     width: int,
     *     height: int
     * }  $stored
     */
    private function attachImage(Project $project, array $stored, bool $isCover = false): ProjectImage
    {
        $sortOrder = (int) $project->images()->max('sort_order') + 1;

        return $project->images()->create([
            ...$stored,
            'sort_order' => $sortOrder,
            'is_cover' => $isCover,
            'alt_ar' => $project->title_ar,
            'alt_en' => $project->title_en,
        ]);
    }
}
