<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Services\SettingsService;
use App\Services\WhatsAppLinkBuilder;
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
        View::composer(['layouts.frontend', 'frontend.*', 'partials.frontend.*', 'components.frontend.*'], function ($view): void {
            $settings = SiteSetting::cached();

            $view->with('siteSettings', $settings);
            $view->with('socialLinks', app(SettingsService::class)->socialLinks($settings));
            $view->with('whatsappUrl', app(WhatsAppLinkBuilder::class)->build($settings['contact_whatsapp'] ?? null));
        });
    }
}
