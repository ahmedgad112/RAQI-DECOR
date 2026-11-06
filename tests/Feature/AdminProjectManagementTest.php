<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_project(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $gallery = [
            UploadedFile::fake()->image('one.jpg', 1200, 800),
            UploadedFile::fake()->image('two.jpg', 1200, 800),
        ];

        $this->actingAs($admin)
            ->post(route('admin.projects.store'), [
                'category_id' => $category->id,
                'title_ar' => 'فيلا الاختبار',
                'is_published' => 1,
                'gallery' => $gallery,
            ])
            ->assertRedirect();

        $project = Project::query()->where('title_ar', 'فيلا الاختبار')->first();
        $this->assertNotNull($project);
        $this->assertSame('فيلا الاختبار', $project->title_en);
        $this->assertTrue($project->is_published);
        $this->assertNotNull($project->cover_image);
        $this->assertSame(2, $project->images()->count());
        $this->assertTrue($project->images()->where('is_cover', true)->exists());
        $this->assertNotEmpty($project->slug);

        $this->actingAs($admin)
            ->put(route('admin.projects.update', $project), [
                'category_id' => $category->id,
                'title_ar' => 'فيلا محدثة',
                'title_en' => 'Updated Villa',
                'is_published' => 1,
            ])
            ->assertRedirect();

        $this->assertSame('فيلا محدثة', $project->fresh()->title_ar);

        $this->actingAs($admin)
            ->delete(route('admin.projects.destroy', $project))
            ->assertRedirect(route('admin.projects.index'));

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_admin_can_manage_categories(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name_ar' => 'سكني',
                'name_en' => 'Residential Test',
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['slug' => 'residential-test']);
    }
}
