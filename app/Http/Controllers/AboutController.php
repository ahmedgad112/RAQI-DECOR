<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SiteSetting;
use App\Services\SeoService;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(SeoService $seo): View
    {
        $settings = SiteSetting::cached();

        return view('frontend.about', [
            'settings' => $settings,
            'seoMeta' => $seo->forAbout($settings),
            'projectsCount' => Project::query()->published()->count(),
        ]);
    }
}
