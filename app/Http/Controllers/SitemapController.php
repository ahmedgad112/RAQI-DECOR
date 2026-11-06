<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $projects = Project::query()->published()->latest('updated_at')->get(['slug', 'updated_at']);
        $categories = Category::query()->active()->get(['slug', 'updated_at']);

        $content = view('seo.sitemap', [
            'projects' => $projects,
            'categories' => $categories,
        ])->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}
