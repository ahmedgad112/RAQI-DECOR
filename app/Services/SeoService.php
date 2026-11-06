<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeoService
{
    /**
     * @param  array<string, mixed>  $settings
     * @return array{
     *     title: string,
     *     description: string,
     *     canonical: string,
     *     image: string,
     *     imageAlt: string,
     *     robots: string,
     *     type: string,
     *     siteName: string
     * }
     */
    public function forHome(array $settings): array
    {
        return $this->meta(
            title: (string) ($settings['seo_title'] ?? ($settings['site_name_en'] ?? config('app.name'))),
            description: (string) ($settings['seo_description'] ?? ($settings['tagline_ar'] ?? '')),
            canonical: route('home'),
            image: $this->defaultOgImage($settings),
            imageAlt: (string) ($settings['site_name_ar'] ?? 'RAQI DECOR'),
            settings: $settings,
        );
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array{
     *     title: string,
     *     description: string,
     *     canonical: string,
     *     image: string,
     *     imageAlt: string,
     *     robots: string,
     *     type: string,
     *     siteName: string
     * }
     */
    public function forAbout(array $settings): array
    {
        $siteName = (string) ($settings['site_name_en'] ?? 'RAQI DECOR');
        $about = (string) ($settings['about_ar'] ?? $settings['seo_description'] ?? '');

        return $this->meta(
            title: 'من نحن | '.$siteName,
            description: $about !== '' ? $about : (string) ($settings['seo_description'] ?? ''),
            canonical: route('about'),
            image: $this->defaultOgImage($settings),
            imageAlt: (string) ($settings['site_name_ar'] ?? $siteName),
            settings: $settings,
        );
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array{
     *     title: string,
     *     description: string,
     *     canonical: string,
     *     image: string,
     *     imageAlt: string,
     *     robots: string,
     *     type: string,
     *     siteName: string
     * }
     */
    public function forContact(array $settings): array
    {
        $siteName = (string) ($settings['site_name_en'] ?? 'RAQI DECOR');

        return $this->meta(
            title: 'تواصل معنا | '.$siteName,
            description: 'تواصل مع '.$siteName.' لتصميم داخلي فاخر واستشارة لمساحتك السكنية أو التجارية.',
            canonical: route('contact'),
            image: $this->defaultOgImage($settings),
            imageAlt: (string) ($settings['site_name_ar'] ?? $siteName),
            settings: $settings,
        );
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array{
     *     title: string,
     *     description: string,
     *     canonical: string,
     *     image: string,
     *     imageAlt: string,
     *     robots: string,
     *     type: string,
     *     siteName: string
     * }
     */
    public function forProjectsIndex(array $settings, ?Category $category = null, ?int $page = null): array
    {
        $siteName = (string) ($settings['site_name_en'] ?? 'RAQI DECOR');

        if ($category) {
            $title = $category->name_ar.' | أعمالنا | '.$siteName;
            $description = filled($category->description_ar)
                ? (string) $category->description_ar
                : 'استعرض مشاريع '.$category->name_ar.' من استوديو '.$siteName.' للتصميم الداخلي الفاخر.';
            $canonical = route('projects.index', array_filter([
                'category' => $category->slug,
                'page' => ($page !== null && $page > 1) ? $page : null,
            ]));
        } else {
            $title = 'أعمالنا | '.$siteName;
            $description = 'معرض أعمال '.$siteName.' في التصميم الداخلي الفاخر للمنازل والمساحات التجارية.';
            $canonical = route('projects.index', array_filter([
                'page' => ($page !== null && $page > 1) ? $page : null,
            ]));
        }

        return $this->meta(
            title: $title,
            description: $description,
            canonical: $canonical,
            image: $this->defaultOgImage($settings),
            imageAlt: (string) ($settings['site_name_ar'] ?? $siteName),
            settings: $settings,
        );
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array{
     *     title: string,
     *     description: string,
     *     canonical: string,
     *     image: string,
     *     imageAlt: string,
     *     robots: string,
     *     type: string,
     *     siteName: string
     * }
     */
    public function forProject(Project $project, array $settings, bool $indexable = true): array
    {
        $siteName = (string) ($settings['site_name_en'] ?? 'RAQI DECOR');
        $title = filled($project->meta_title)
            ? (string) $project->meta_title
            : $project->title_ar.' | '.$siteName;
        $description = filled($project->meta_description)
            ? (string) $project->meta_description
            : (string) ($project->excerpt_ar ?: $project->excerpt_en ?: $settings['seo_description'] ?? '');

        $cover = $project->coverUrl('large');
        $image = $this->absoluteUrl($cover) ?? $this->defaultOgImage($settings);

        return $this->meta(
            title: $title,
            description: $description,
            canonical: route('projects.show', $project),
            image: $image,
            imageAlt: $project->title_ar,
            settings: $settings,
            robots: $indexable
                ? 'index,follow,max-image-preview:large'
                : 'noindex,nofollow',
            type: 'article',
        );
    }

    /**
     * @param  array<string, mixed>  $settings
     * @param  list<array{label: string, url: string}>  $socialLinks
     * @return array<string, mixed>
     */
    public function organizationSchema(array $settings, array $socialLinks = [], ?string $mapExternalUrl = null): array
    {
        $sameAs = array_values(array_filter(array_map(
            static fn (array $link): string => $link['url'],
            $socialLinks,
        )));

        $logo = $this->storageUrl($settings['logo_path'] ?? null) ?? url('images/logo.png');

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'InteriorDesigner',
            'name' => $settings['site_name_en'] ?? 'RAQI DECOR',
            'alternateName' => $settings['site_name_ar'] ?? 'راقي ديكور',
            'url' => url('/'),
            'logo' => $logo,
            'image' => $logo,
            'email' => $settings['contact_email'] ?? null,
            'telephone' => $settings['contact_phone'] ?? null,
            'description' => $this->truncate((string) ($settings['seo_description'] ?? '')),
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'SA',
                'streetAddress' => $settings['contact_address_ar']
                    ?? $settings['contact_address_en']
                    ?? 'Saudi Arabia',
                'addressLocality' => 'Riyadh',
            ],
            'geo' => (! empty($settings['contact_map_lat']) && ! empty($settings['contact_map_lng'])) ? [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $settings['contact_map_lat'],
                'longitude' => (float) $settings['contact_map_lng'],
            ] : null,
            'hasMap' => $mapExternalUrl,
            'sameAs' => $sameAs !== [] ? $sameAs : null,
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Saudi Arabia',
            ],
            'inLanguage' => ['ar', 'en'],
        ], static fn ($value) => $value !== null && $value !== '');
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public function websiteSchema(array $settings): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $settings['site_name_en'] ?? 'RAQI DECOR',
            'alternateName' => $settings['site_name_ar'] ?? 'راقي ديكور',
            'url' => url('/'),
            'inLanguage' => 'ar',
            'publisher' => [
                '@type' => 'Organization',
                'name' => $settings['site_name_en'] ?? 'RAQI DECOR',
            ],
        ];
    }

    /**
     * @param  list<array{name: string, url: string}>  $items
     * @return array<string, mixed>
     */
    public function breadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(function (array $item, int $index): array {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ];
            })->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function projectSchema(Project $project): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $project->title_en ?: $project->title_ar,
            'alternateName' => $project->title_ar,
            'description' => $this->truncate((string) ($project->excerpt_ar ?: $project->excerpt_en ?: '')),
            'datePublished' => optional($project->published_at)->toAtomString(),
            'dateModified' => optional($project->updated_at)->toAtomString(),
            'image' => $this->absoluteUrl($project->coverUrl('large')),
            'url' => route('projects.show', $project),
            'inLanguage' => 'ar',
            'genre' => $project->category?->name_en ?: $project->category?->name_ar,
            'locationCreated' => filled($project->location) ? [
                '@type' => 'Place',
                'name' => $project->location,
            ] : null,
        ], static fn ($value) => $value !== null && $value !== '');
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public function defaultOgImage(array $settings): string
    {
        return $this->storageUrl($settings['hero_image'] ?? null)
            ?? url('images/og-default.jpg');
    }

    public function absoluteUrl(?string $pathOrUrl): ?string
    {
        if (blank($pathOrUrl)) {
            return null;
        }

        if (Str::startsWith($pathOrUrl, ['http://', 'https://', '//'])) {
            return $pathOrUrl;
        }

        return url($pathOrUrl);
    }

    public function storageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return $this->absoluteUrl(Storage::disk('public')->url($path));
    }

    public function truncate(?string $text, int $limit = 160): string
    {
        $cleaned = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)) ?? '');

        if ($cleaned === '') {
            return '';
        }

        return Str::limit($cleaned, $limit, '');
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array{
     *     title: string,
     *     description: string,
     *     canonical: string,
     *     image: string,
     *     imageAlt: string,
     *     robots: string,
     *     type: string,
     *     siteName: string
     * }
     */
    private function meta(
        string $title,
        string $description,
        string $canonical,
        string $image,
        string $imageAlt,
        array $settings,
        string $robots = 'index,follow,max-image-preview:large',
        string $type = 'website',
    ): array {
        return [
            'title' => $title,
            'description' => $this->truncate($description),
            'canonical' => $canonical,
            'image' => $image,
            'imageAlt' => $imageAlt,
            'robots' => $robots,
            'type' => $type,
            'siteName' => (string) ($settings['site_name_en'] ?? 'RAQI DECOR'),
        ];
    }
}
