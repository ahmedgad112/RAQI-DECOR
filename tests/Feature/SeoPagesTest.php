<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_exposes_core_seo_tags(): void
    {
        $this->seed();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<meta name="description"', false)
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('property="og:site_name"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('InteriorDesigner', false)
            ->assertSee('WebSite', false);
    }

    public function test_static_pages_have_unique_titles(): void
    {
        $this->seed();

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('<title>من نحن | RAQI DECOR</title>', false);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('<title>تواصل معنا | RAQI DECOR</title>', false);

        $this->get(route('projects.index'))
            ->assertOk()
            ->assertSee('<title>أعمالنا | RAQI DECOR</title>', false);
    }

    public function test_category_page_has_targeted_seo(): void
    {
        $this->seed();

        $category = Category::factory()->create([
            'name_ar' => 'فيلات فاخرة',
            'slug' => 'luxury-villas',
            'is_active' => true,
        ]);

        $this->get(route('projects.index', ['category' => $category->slug]))
            ->assertOk()
            ->assertSee('<title>فيلات فاخرة | أعمالنا | RAQI DECOR</title>', false)
            ->assertSee('category=luxury-villas', false);
    }

    public function test_published_project_has_article_seo_and_schemas(): void
    {
        $this->seed();

        $category = Category::factory()->create(['name_ar' => 'شقق']);
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'is_published' => true,
            'title_ar' => 'فيلا النخيل',
            'meta_title' => 'فيلا النخيل | تصميم داخلي',
            'meta_description' => 'وصف مخصص لمحركات البحث عن فيلا النخيل.',
        ]);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('<title>فيلا النخيل | تصميم داخلي</title>', false)
            ->assertSee('وصف مخصص لمحركات البحث عن فيلا النخيل.', false)
            ->assertSee('property="og:type" content="article"', false)
            ->assertSee('BreadcrumbList', false)
            ->assertSee('CreativeWork', false);
    }

    public function test_draft_project_preview_is_noindex(): void
    {
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->draft()->create([
            'title_ar' => 'مسودة خاصة',
        ]);

        $this->actingAs($admin)
            ->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('content="noindex,nofollow"', false);
    }

    public function test_robots_and_sitemap_are_available(): void
    {
        $this->seed();

        $category = Category::factory()->create(['slug' => 'classic', 'is_active' => true]);
        Project::factory()->create([
            'category_id' => $category->id,
            'is_published' => true,
            'slug' => 'seo-project',
            'cover_image' => 'projects/seo-project/cover.jpg',
        ]);

        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin', false)
            ->assertSee('Sitemap:', false);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('home'), false)
            ->assertSee(route('projects.show', 'seo-project'), false)
            ->assertSee('image:image', false)
            ->assertSee('category=classic', false);
    }
}
