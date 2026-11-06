<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders(): void
    {
        $this->seed();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('RAQI DECOR');
    }

    public function test_about_contact_and_projects_pages_render(): void
    {
        $this->seed();

        $this->get(route('about'))->assertOk();
        $this->get(route('contact'))->assertOk();
        $this->get(route('projects.index'))->assertOk();
    }

    public function test_published_project_show_page_renders(): void
    {
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'is_published' => true,
            'title_ar' => 'مشروع اختبار',
        ]);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('مشروع اختبار');
    }

    public function test_draft_project_is_not_public(): void
    {
        $project = Project::factory()->draft()->create();

        $this->get(route('projects.show', $project))->assertNotFound();
    }

    public function test_admin_can_preview_draft_project(): void
    {
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->draft()->create([
            'title_ar' => 'مسودة اختبار',
        ]);

        $this->actingAs($admin)
            ->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('مسودة اختبار');
    }

    public function test_sitemap_is_available(): void
    {
        $this->seed();

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml');
    }
}
