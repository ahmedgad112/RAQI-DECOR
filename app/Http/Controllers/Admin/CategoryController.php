<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private ImageService $images) {}

    public function index(): View
    {
        $categories = Category::query()
            ->withCount('projects')
            ->ordered()
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['slug'] = Category::uniqueSlug($data['slug'] ?? $data['name_en']);

        if ($request->hasFile('image')) {
            $stored = $this->images->store($request->file('image'), 'categories');
            $data['image_path'] = $stored['path_medium'];
        }

        Category::query()->create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم إنشاء الفئة بنجاح.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->safe()->except(['image']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['slug'] = Category::uniqueSlug($data['slug'] ?? $data['name_en'], $category->id);

        if ($request->hasFile('image')) {
            $previous = $category->image_path;
            $stored = $this->images->store($request->file('image'), 'categories');
            $data['image_path'] = $stored['path_medium'];

            if ($previous) {
                Storage::disk('public')->delete($previous);
            }
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم تحديث الفئة بنجاح.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->projects()->exists()) {
            return back()->with('error', 'لا يمكن حذف فئة مرتبطة بأعمال.');
        }

        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم حذف الفئة بنجاح.');
    }
}
