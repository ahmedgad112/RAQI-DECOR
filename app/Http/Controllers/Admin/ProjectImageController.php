<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectImageController extends Controller
{
    public function __construct(private ProjectService $projects) {}

    public function reorder(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array', 'min:1'],
            'order.*' => ['integer', 'exists:project_images,id'],
        ]);

        $ownedIds = $project->images()->pluck('id')->all();
        $order = array_values(array_intersect($validated['order'], $ownedIds));

        $this->projects->reorderImages($project, $order);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('success', 'تم تحديث ترتيب الصور.');
    }

    public function destroy(Project $project, ProjectImage $image): RedirectResponse
    {
        abort_unless($image->project_id === $project->id, 404);

        $this->projects->deleteImage($image);

        return back()->with('success', 'تم حذف الصورة.');
    }

    public function cover(Project $project, ProjectImage $image): RedirectResponse
    {
        abort_unless($image->project_id === $project->id, 404);

        $this->projects->setCover($image);

        return back()->with('success', 'تم تعيين صورة الغلاف.');
    }
}
