<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $settings = SiteSetting::cached();

        return view('frontend.home', [
            'settings' => $settings,
            'categories' => Category::query()->active()->ordered()->withCount(['projects' => fn ($q) => $q->published()])->get(),
            'featuredProjects' => Project::query()
                ->with(['category', 'images'])
                ->published()
                ->featured()
                ->ordered()
                ->limit(6)
                ->get(),
            'latestProjects' => Project::query()
                ->with(['category', 'images'])
                ->published()
                ->ordered()
                ->limit(8)
                ->get(),
        ]);
    }
}
