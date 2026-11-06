<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Models\Category;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projects) {}

    public function index(Request $request): View
    {
        $projects = Project::query()
            ->with('category')
            ->withCount('images')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($builder) use ($term) {
                    $builder->where('title_ar', 'like', $term)
                        ->orWhere('title_en', 'like', $term);
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'categories' => Category::query()->ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.projects.create', [
            'categories' => Category::query()->active()->ordered()->get(),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['cover', 'gallery']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['title_en'] = filled($data['title_en'] ?? null)
            ? $data['title_en']
            : ($data['title_ar'] ?? 'Project');

        $project = $this->projects->create(
            $data,
            null,
            $request->file('gallery', [])
        );

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', 'تم إنشاء العمل بنجاح.');
    }

    public function edit(Project $project): View
    {
        $project->load('images');

        return view('admin.projects.edit', [
            'project' => $project,
            'categories' => Category::query()->ordered()->get(),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $data = $request->safe()->except(['cover', 'gallery']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $this->projects->update(
            $project,
            $data,
            null,
            $request->file('gallery', [])
        );

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', 'تم تحديث العمل بنجاح.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->projects->delete($project);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'تم حذف العمل بنجاح.');
    }
}
