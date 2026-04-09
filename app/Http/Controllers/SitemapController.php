<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    /** Locales disponibles sur le site */
    private const LOCALES = ['fr', 'en', 'es', 'ht'];

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
        $appUrl = rtrim(config('app.url'), '/');

        // Pages statiques localisées
        $staticUrls = [];
        foreach (self::STATIC_PAGES as $page => $meta) {
            foreach (self::LOCALES as $locale) {
                $path = $page ? "/{$locale}/{$page}" : "/{$locale}";
                $staticUrls[] = array_merge($meta, ['loc' => $appUrl . $path]);
            }
        }

        // Conseillers (URL sans locale — redirigent vers /fr/conseiller/{slug})
        $members = DB::table('users')
            ->whereNotNull('slug')
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get(['slug', 'updated_at']);

        $memberUrls = [];
        foreach ($members as $m) {
            foreach (self::LOCALES as $locale) {
                $memberUrls[] = [
                    'loc'        => $appUrl . "/{$locale}/conseiller/{$m->slug}",
                    'priority'   => '0.7',
                    'changefreq' => 'monthly',
                    'lastmod'    => $m->updated_at ? substr($m->updated_at, 0, 10) : null,
                ];
            }
        }

        // Articles de blog (slugs traduits)
        $posts = BlogPost::select('slug', 'updated_at')
            ->where('status', 'published')
            ->latest()
            ->get();

        $postUrls = [];
        foreach ($posts as $post) {
            foreach (self::LOCALES as $locale) {
                $slug = $post->getTranslation('slug', $locale, false)
                    ?? $post->getTranslation('slug', 'fr', false)
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
            ->view('sitemap', [
                'staticUrls' => $staticUrls,
                'memberUrls' => $memberUrls,
                'postUrls'   => $postUrls,
            ])
            ->header('Content-Type', 'text/xml');
    }
}
