<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ProjectImageController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/about', AboutController::class)->name('about');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'create'])->name('login');
        Route::post('/login', [LoginController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

        Route::middleware('permission:dashboard')->group(function () {
            Route::get('/', DashboardController::class)->name('dashboard');
        });

        Route::middleware('permission:categories')->group(function () {
            Route::resource('categories', AdminCategoryController::class)->except(['show']);
        });

        Route::middleware('permission:projects')->group(function () {
            Route::resource('projects', AdminProjectController::class)->except(['show']);
            Route::post('projects/{project}/images/reorder', [ProjectImageController::class, 'reorder'])
                ->name('projects.images.reorder');
            Route::delete('projects/{project}/images/{image}', [ProjectImageController::class, 'destroy'])
                ->name('projects.images.destroy');
            Route::post('projects/{project}/images/{image}/cover', [ProjectImageController::class, 'cover'])
                ->name('projects.images.cover');
        });

        Route::middleware('permission:users')->group(function () {
            Route::resource('users', AdminUserController::class)->except(['show']);
        });

        Route::middleware('permission:settings')->group(function () {
            Route::get('settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [SiteSettingController::class, 'update'])->name('settings.update');
        });

        Route::middleware('permission:messages')->group(function () {
            Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
            Route::get('messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
            Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');
        });
    });
});
