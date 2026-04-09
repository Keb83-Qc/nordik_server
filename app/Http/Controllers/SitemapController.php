<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Language;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    /** Pages statiques avec leur priorité */
    private const STATIC_PAGES = [
        ''        => ['priority' => '1.0', 'changefreq' => 'weekly'],
        'about'   => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'equipe'  => ['priority' => '0.9', 'changefreq' => 'weekly'],
        'contact' => ['priority' => '0.7', 'changefreq' => 'monthly'],
        'blog'    => ['priority' => '0.8', 'changefreq' => 'daily'],
    ];

    public function index(): Response
    {
        $appUrl  = rtrim(config('app.url'), '/');
        $locales = Language::activeCodes(); // depuis la table languages, mis en cache 1h

        // Pages statiques localisées
        $staticUrls = [];
        foreach (self::STATIC_PAGES as $page => $meta) {
            foreach ($locales as $locale) {
                $path = $page ? "/{$locale}/{$page}" : "/{$locale}";
                $staticUrls[] = array_merge($meta, ['loc' => $appUrl . $path]);
            }
        }

        // Conseillers — une URL par locale
        $members = DB::table('users')
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get(['slug', 'updated_at']);

        $memberUrls = [];
        foreach ($members as $m) {
            foreach ($locales as $locale) {
                $memberUrls[] = [
                    'loc'        => $appUrl . "/{$locale}/conseiller/{$m->slug}",
                    'priority'   => '0.7',
                    'changefreq' => 'monthly',
                    'lastmod'    => $m->updated_at ? substr($m->updated_at, 0, 10) : null,
                ];
            }
        }

        // Articles de blog publiés avec slugs traduits
        $defaultLocale = Language::defaultCode();
        $posts = BlogPost::select('slug', 'updated_at')
            ->where('status', 'published')
            ->latest()
            ->get();

        $postUrls = [];
        foreach ($posts as $post) {
            foreach ($locales as $locale) {
                $slug = $post->getTranslation('slug', $locale, false)
                    ?? $post->getTranslation('slug', $defaultLocale, false)
                    ?? null;
                if (!$slug) {
                    continue;
                }
                $postUrls[] = [
                    'loc'        => $appUrl . "/{$locale}/article/{$slug}",
                    'priority'   => '0.6',
                    'changefreq' => 'monthly',
                    'lastmod'    => $post->updated_at ? $post->updated_at->toDateString() : null,
                ];
            }
        }

        return response()
            ->view('sitemap', compact('staticUrls', 'memberUrls', 'postUrls'))
            ->header('Content-Type', 'text/xml');
    }
}
