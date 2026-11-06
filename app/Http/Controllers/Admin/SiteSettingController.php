<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSiteSettingsRequest;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function __construct(private SettingsService $settings) {}

    public function edit(): View
    {
        $settings = $this->settings->all();

        return view('admin.settings.edit', [
            'settings' => $settings,
            'socialLinks' => $this->settings->socialLinks($settings),
            'socialPlatforms' => $this->settings->socialPlatforms(),
        ]);
    }

    public function update(UpdateSiteSettingsRequest $request): RedirectResponse
    {
        $this->settings->update(
            $request->safe()->except(['logo', 'hero_image']),
            $request->file('logo'),
            $request->file('hero_image')
        );

        return back()->with('success', 'تم حفظ إعدادات الموقع.');
    }
}
