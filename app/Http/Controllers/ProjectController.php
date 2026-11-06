<?php

namespace App\Http\Controllers;

use App\Enums\AdminPermission;
use App\Models\Category;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request, SeoService $seo): View
    {
        $categorySlug = $request->string('category')->toString();
        $settings = SiteSetting::cached();

        $category = $categorySlug !== ''
            ? Category::query()->active()->where('slug', $categorySlug)->first()
            : null;

        $projects = Project::query()
            ->with(['category', 'coverImageRelation'])
            ->published()
            ->when($category, function ($query) use ($category) {
                $query->where('category_id', $category->id);
            })
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('frontend.projects.index', [
            'projects' => $projects,
            'categories' => Category::query()->active()->ordered()->get(),
            'activeCategory' => $category?->slug ?? '',
            'seoMeta' => $seo->forProjectsIndex($settings, $category, $projects->currentPage()),
        ]);
    }

    public function show(Project $project, SeoService $seo): View
    {
        $canPreviewDraft = auth()->check()
            && auth()->user()?->hasPermission(AdminPermission::Projects);

        abort_unless($project->is_published || $canPreviewDraft, 404);

        $project->load(['category', 'images']);
        $settings = SiteSetting::cached();
        $indexable = (bool) $project->is_published;

        $related = Project::query()
            ->with(['category', 'coverImageRelation'])
            ->published()
            ->where('category_id', $project->category_id)
            ->where('id', '!=', $project->id)
            ->ordered()
            ->limit(3)
            ->get();

        return view('frontend.projects.show', [
            'project' => $project,
            'related' => $related,
            'seoMeta' => $seo->forProject($project, $settings, $indexable),
            'projectSchema' => $seo->projectSchema($project),
            'breadcrumbSchema' => $seo->breadcrumbSchema(array_values(array_filter([
                ['name' => 'الرئيسية', 'url' => route('home')],
                ['name' => 'أعمالنا', 'url' => route('projects.index')],
                $project->category ? [
                    'name' => $project->category->name_ar,
                    'url' => route('projects.index', ['category' => $project->category->slug]),
                ] : null,
                ['name' => $project->title_ar, 'url' => route('projects.show', $project)],
            ]))),
        ]);
    }
}
