<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Project;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'projects' => Project::query()->count(),
                'published' => Project::query()->published()->count(),
                'categories' => Category::query()->count(),
                'unread_messages' => ContactMessage::query()->unread()->count(),
            ],
            'recentProjects' => Project::query()->with('category')->latest()->limit(5)->get(),
            'recentMessages' => ContactMessage::query()->latest()->limit(5)->get(),
        ]);
    }
}
