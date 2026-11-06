<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Services\SeoService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(SeoService $seo): View
    {
        $settings = SiteSetting::cached();

        return view('frontend.home', [
            'settings' => $settings,
            'seoMeta' => $seo->forHome($settings),
            'categories' => Category::query()->active()->ordered()->withCount(['projects' => fn ($q) => $q->published()])->get(),
            'featuredProjects' => Project::query()
                ->with(['category', 'coverImageRelation'])
                ->published()
                ->featured()
                ->ordered()
                ->limit(6)
                ->get(),
            'latestProjects' => Project::query()
                ->with(['category', 'coverImageRelation'])
                ->published()
                ->ordered()
                ->limit(8)
                ->get(),
        ]);
    }
}
