<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Services\SettingsService;
use App\Services\WhatsAppLinkBuilder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        View::composer(['layouts.frontend', 'frontend.*', 'partials.frontend.*', 'components.frontend.*'], function ($view): void {
            $settings = SiteSetting::cached();
            $settingsService = app(SettingsService::class);

            $view->with('siteSettings', $settings);
            $view->with('socialLinks', $settingsService->socialLinks($settings));
            $view->with('whatsappUrl', app(WhatsAppLinkBuilder::class)->build($settings['contact_whatsapp'] ?? null));
            $view->with('mapExternalUrl', $settingsService->mapExternalUrl($settings));
        });
    }
}
