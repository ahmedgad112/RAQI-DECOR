<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titleEn = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'title_ar' => 'مشروع '.fake()->unique()->numerify('###'),
            'title_en' => Str::title($titleEn),
            'slug' => Str::slug($titleEn),
            'excerpt_ar' => fake()->sentence(),
            'excerpt_en' => fake()->sentence(),
            'description_ar' => fake()->paragraphs(2, true),
            'description_en' => fake()->paragraphs(2, true),
            'location' => fake()->city(),
            'year' => (string) fake()->numberBetween(2020, 2026),
            'client' => null,
            'is_featured' => false,
            'is_published' => true,
            'published_at' => now(),
            'sort_order' => 0,
            'meta_title' => Str::title($titleEn).' | RAQI DECOR',
            'meta_description' => fake()->sentence(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
        ]);
    }
}
