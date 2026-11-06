<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectImage>
 */
class ProjectImageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $path = 'projects/original/'.fake()->uuid().'.webp';

        return [
            'project_id' => Project::factory(),
            'path' => $path,
            'path_thumb' => str_replace('/original/', '/thumb/', $path),
            'path_medium' => str_replace('/original/', '/medium/', $path),
            'path_large' => str_replace('/original/', '/large/', $path),
            'alt_ar' => fake()->words(3, true),
            'alt_en' => fake()->words(3, true),
            'sort_order' => 0,
            'is_cover' => false,
            'width' => 1600,
            'height' => 1200,
        ];
    }
}
