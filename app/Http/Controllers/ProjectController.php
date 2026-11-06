<?php

namespace App\Http\Controllers;

use App\Enums\AdminPermission;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = $request->string('category')->toString();

        $projects = Project::query()
            ->with(['category', 'images'])
            ->published()
            ->when($categorySlug !== '', function ($query) use ($categorySlug) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
            })
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('frontend.projects.index', [
            'projects' => $projects,
            'categories' => Category::query()->active()->ordered()->get(),
            'activeCategory' => $categorySlug,
        ]);
    }

    public function show(Project $project): View
    {
        $canPreviewDraft = auth()->check()
            && auth()->user()?->hasPermission(AdminPermission::Projects);

        abort_unless($project->is_published || $canPreviewDraft, 404);

        $project->load(['category', 'images']);

        $related = Project::query()
            ->with(['category', 'images'])
            ->published()
            ->where('category_id', $project->category_id)
            ->where('id', '!=', $project->id)
            ->ordered()
            ->limit(3)
            ->get();

        return view('frontend.projects.show', [
            'project' => $project,
            'related' => $related,
        ]);
    }
}
