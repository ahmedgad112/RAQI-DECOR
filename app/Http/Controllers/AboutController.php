<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        return view('frontend.about', [
            'settings' => SiteSetting::cached(),
            'projectsCount' => Project::query()->published()->count(),
        ]);
    }
}
