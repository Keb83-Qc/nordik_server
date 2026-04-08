<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Employee;
use App\Models\PublicService;
use App\Models\PublicServiceCategory;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature   = 'sitemap:generate {--force}';
    protected $description = 'Génère le fichier public/sitemap.xml';

    private array $locales = ['fr', 'en'];

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        // ── Pages statiques ──────────────────────────────────────────────────
        $staticPages = [
            'home'         => ['/', 1.0, 'weekly'],
            'about'        => ['/about', 0.8, 'monthly'],
            'equipe'       => ['/equipe', 0.8, 'weekly'],
            'evenements'   => ['/evenements', 0.7, 'weekly'],
            'blog'         => ['/blog', 0.8, 'weekly'],
            'partenaires'  => ['/partenaires', 0.6, 'monthly'],
            'carrieres'    => ['/carrieres', 0.6, 'monthly'],
            'contact'      => ['/contact', 0.7, 'monthly'],
        ];

        foreach ($this->locales as $locale) {
            foreach ($staticPages as [$path, $priority, $freq]) {
                $url = $path === '/'
                    ? url("/{$locale}")
                    : url("/{$locale}{$path}");

                $sitemap->add(
                    Url::create($url)
                        ->setPriority($priority)
                        ->setChangeFrequency($freq)
                );
            }
        }

        // ── Conseillers (employees avec slug) ────────────────────────────────
        try {
            Employee::whereNotNull('slug')
                ->where('is_active', true)
                ->each(function (Employee $emp) use ($sitemap) {
                    foreach ($this->locales as $locale) {
                        $slug = $emp->getTranslation('slug', $locale, false)
                             ?: $emp->getTranslation('slug', 'fr', false)
                             ?: $emp->slug;

                        if (! $slug) return;

                        $sitemap->add(
                            Url::create(url("/{$locale}/conseiller/{$slug}"))
                                ->setPriority(0.7)
                                ->setChangeFrequency('monthly')
                                ->setLastModificationDate($emp->updated_at)
                        );
                    }
                });
        } catch (\Throwable) {}

        // ── Articles de blog ─────────────────────────────────────────────────
        try {
            BlogPost::where('is_published', true)
                ->each(function (BlogPost $post) use ($sitemap) {
                    foreach ($this->locales as $locale) {
                        $slug = $post->getTranslation('slug', $locale, false)
                             ?: $post->getTranslation('slug', 'fr', false);

                        if (! $slug) return;

                        $sitemap->add(
                            Url::create(url("/{$locale}/article/{$slug}"))
                                ->setPriority(0.6)
                                ->setChangeFrequency('monthly')
                                ->setLastModificationDate($post->updated_at)
                        );
                    }
                });
        } catch (\Throwable) {}

        // ── Services publics ─────────────────────────────────────────────────
        try {
            PublicServiceCategory::where('is_active', true)
                ->with(['translations', 'services' => fn($q) => $q->where('is_active', true), 'services.translations'])
                ->each(function (PublicServiceCategory $cat) use ($sitemap) {
                    foreach ($this->locales as $locale) {
                        $catSlug = optional($cat->translations->firstWhere('locale', $locale))->slug
                                ?: optional($cat->translations->firstWhere('locale', 'fr'))->slug;

                        if (! $catSlug) return;

                        foreach ($cat->services as $service) {
                            $srvSlug = optional($service->translations->firstWhere('locale', $locale))->slug
                                    ?: optional($service->translations->firstWhere('locale', 'fr'))->slug;

                            if (! $srvSlug) continue;

                            $sitemap->add(
                                Url::create(url("/{$locale}/{$catSlug}/{$srvSlug}"))
                                    ->setPriority(0.7)
                                    ->setChangeFrequency('monthly')
                                    ->setLastModificationDate($service->updated_at)
                            );
                        }
                    }
                });
        } catch (\Throwable) {}

        // ── Écriture ─────────────────────────────────────────────────────────
        $dest = public_path('sitemap.xml');
        $sitemap->writeToFile($dest);

        $count = substr_count(file_get_contents($dest), '<url>');
        $this->info("sitemap.xml généré : {$count} URLs → {$dest}");

        return self::SUCCESS;
    }
}
