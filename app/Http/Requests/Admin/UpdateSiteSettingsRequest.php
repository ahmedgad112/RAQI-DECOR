<?php

namespace App\Http\Requests\Admin;

use App\Enums\AdminPermission;
use App\Services\SettingsService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission(AdminPermission::Settings) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'site_name_ar' => ['required', 'string', 'max:255'],
            'site_name_en' => ['required', 'string', 'max:255'],
            'tagline_ar' => ['nullable', 'string', 'max:500'],
            'tagline_en' => ['nullable', 'string', 'max:500'],
            'about_ar' => ['nullable', 'string', 'max:10000'],
            'about_en' => ['nullable', 'string', 'max:10000'],
            'hero_title_ar' => ['nullable', 'string', 'max:255'],
            'hero_title_en' => ['nullable', 'string', 'max:255'],
            'hero_subtitle_ar' => ['nullable', 'string', 'max:500'],
            'hero_subtitle_en' => ['nullable', 'string', 'max:500'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_whatsapp' => ['nullable', 'string', 'max:50'],
            'contact_address_ar' => ['nullable', 'string', 'max:500'],
            'contact_address_en' => ['nullable', 'string', 'max:500'],
            'social_links' => ['nullable', 'array', 'max:30'],
            'social_links.*.label' => [
                'nullable',
                'required_with:social_links.*.url',
                'string',
                Rule::in(SettingsService::SOCIAL_PLATFORMS),
            ],
            'social_links.*.url' => ['nullable', 'required_with:social_links.*.label', 'url', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'projects_completed' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:200'],
            'clients_count' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'hero_image_position' => ['nullable', 'string', Rule::in(SettingsService::HERO_POSITIONS)],
            'remove_hero_image' => ['sometimes', 'boolean'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'social_links.*.label' => 'نوع التواصل',
            'social_links.*.url' => 'رابط التواصل',
        ];
    }
}
