<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsService
{
    /**
     * @var list<string>
     */
    public const SOCIAL_PLATFORMS = [
        'Instagram',
        'Facebook',
        'TikTok',
        'YouTube',
        'LinkedIn',
        'X',
        'WhatsApp',
        'Snapchat',
        'Pinterest',
        'Behance',
        'Threads',
    ];

    public function __construct(private ImageService $images) {}

    /**
     * @return list<string>
     */
    public function socialPlatforms(): array
    {
        return self::SOCIAL_PLATFORMS;
    }

    /**
     * @var list<string>
     */
    public const HERO_POSITIONS = [
        'center',
        'top',
        'bottom',
    ];

    /**
     * @param  array<string, mixed>  $values
     */
    public function update(array $values, ?UploadedFile $logo = null, ?UploadedFile $hero = null): void
    {
        $removeHero = filter_var($values['remove_hero_image'] ?? false, FILTER_VALIDATE_BOOLEAN);
        unset($values['remove_hero_image']);

        if (array_key_exists('social_links', $values)) {
            $values['social_links'] = $this->normalizeSocialLinks($values['social_links'] ?? []);
        }

        if (array_key_exists('hero_image_position', $values)) {
            $position = (string) ($values['hero_image_position'] ?? 'center');
            $values['hero_image_position'] = in_array($position, self::HERO_POSITIONS, true)
                ? $position
                : 'center';
        }

        foreach ($values as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $this->encodeValue($value),
                    'group' => $this->groupFor($key),
                ]
            );
        }

        if ($logo instanceof UploadedFile) {
            $this->storeSettingImage('logo_path', $logo, 'branding');
        }

        if ($hero instanceof UploadedFile) {
            $this->storeSettingImage('hero_image', $hero, 'hero');
        } elseif ($removeHero) {
            $this->clearSettingImage('hero_image');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return SiteSetting::cached();
    }

    /**
     * @return list<array{label: string, url: string}>
     */
    public function socialLinks(?array $settings = null): array
    {
        $settings ??= $this->all();
        $raw = $settings['social_links'] ?? null;

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);

            if (is_array($decoded)) {
                return $this->normalizeSocialLinks($decoded);
            }
        }

        if (is_array($raw)) {
            return $this->normalizeSocialLinks($raw);
        }

        return $this->legacySocialLinks($settings);
    }

    /**
     * @param  array<int, mixed>  $links
     * @return list<array{label: string, url: string}>
     */
    public function normalizeSocialLinks(array $links): array
    {
        $normalized = [];

        foreach ($links as $link) {
            if (! is_array($link)) {
                continue;
            }

            $url = trim((string) ($link['url'] ?? ''));

            if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            $label = trim((string) ($link['label'] ?? ''));

            if ($label === '' || ! in_array($label, self::SOCIAL_PLATFORMS, true)) {
                $label = $this->labelFromUrl($url);
            }

            if (! in_array($label, self::SOCIAL_PLATFORMS, true)) {
                continue;
            }

            $normalized[] = [
                'label' => $label,
                'url' => Str::limit($url, 255, ''),
            ];
        }

        return array_values($normalized);
    }

    private function storeSettingImage(string $key, UploadedFile $file, string $directory): void
    {
        $previous = SiteSetting::getValue($key);
        $stored = $this->images->store($file, $directory);

        SiteSetting::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $stored['path_large'],
                'group' => $this->groupFor($key),
            ]
        );

        if (is_string($previous) && $previous !== '') {
            $this->deleteSettingImageFiles($previous);
        }
    }

    private function clearSettingImage(string $key): void
    {
        $previous = SiteSetting::getValue($key);

        SiteSetting::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => '',
                'group' => $this->groupFor($key),
            ]
        );

        if (is_string($previous) && $previous !== '') {
            $this->deleteSettingImageFiles($previous);
        }
    }

    private function deleteSettingImageFiles(string $path): void
    {
        $basename = pathinfo($path, PATHINFO_FILENAME);
        $directory = Str::before($path, '/') ?: 'hero';

        Storage::disk('public')->delete([
            $path,
            "{$directory}/original/{$basename}.webp",
            "{$directory}/thumb/{$basename}.webp",
            "{$directory}/medium/{$basename}.webp",
            "{$directory}/large/{$basename}.webp",
        ]);
    }

    private function encodeValue(mixed $value): string
    {
        return match (true) {
            is_bool($value) => $value ? '1' : '0',
            is_array($value) => json_encode(
                array_values($value),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ) ?: '[]',
            default => (string) ($value ?? ''),
        };
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return list<array{label: string, url: string}>
     */
    private function legacySocialLinks(array $settings): array
    {
        $legacy = [
            'Instagram' => $settings['social_instagram'] ?? '',
            'Facebook' => $settings['social_facebook'] ?? '',
            'LinkedIn' => $settings['social_linkedin'] ?? '',
        ];

        $links = [];

        foreach ($legacy as $label => $url) {
            $url = trim((string) $url);

            if ($url === '') {
                continue;
            }

            $links[] = [
                'label' => $label,
                'url' => $url,
            ];
        }

        return $links;
    }

    private function labelFromUrl(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return '';
        }

        $host = Str::lower(preg_replace('/^www\./', '', $host) ?? $host);

        return match (true) {
            str_contains($host, 'instagram') => 'Instagram',
            str_contains($host, 'facebook') => 'Facebook',
            str_contains($host, 'tiktok') => 'TikTok',
            str_contains($host, 'youtube') || str_contains($host, 'youtu.be') => 'YouTube',
            str_contains($host, 'linkedin') => 'LinkedIn',
            str_contains($host, 'x.com') || str_contains($host, 'twitter') => 'X',
            str_contains($host, 'whatsapp') => 'WhatsApp',
            str_contains($host, 'snapchat') => 'Snapchat',
            str_contains($host, 'pinterest') => 'Pinterest',
            str_contains($host, 'behance') => 'Behance',
            str_contains($host, 'threads') => 'Threads',
            default => '',
        };
    }

    private function groupFor(string $key): string
    {
        return match (true) {
            str_starts_with($key, 'seo_') => 'seo',
            $key === 'social_links' || str_starts_with($key, 'social_') => 'social',
            str_starts_with($key, 'contact_') => 'contact',
            str_starts_with($key, 'about_') => 'about',
            str_starts_with($key, 'hero_') => 'hero',
            default => 'general',
        };
    }
}
