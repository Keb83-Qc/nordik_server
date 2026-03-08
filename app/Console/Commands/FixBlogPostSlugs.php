<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Language;
use Illuminate\Console\Command;

class FixBlogPostSlugs extends Command
{
    protected $signature = 'blog:fix-slugs {--dry-run : Ne sauve pas, affiche seulement}';
    protected $description = 'Normalise et corrige les slugs multilingues des articles de blog (accents, tirets finaux, unicité).';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $locales = Language::activeCodes();
        $fallback = Language::defaultCode() ?? config('app.fallback_locale', 'fr');

        $posts = BlogPost::query()->orderBy('id')->get();

        $this->info("Posts: {$posts->count()} | Locales: " . implode(',', $locales) . " | Dry-run: " . ($dry ? 'yes' : 'no'));

        foreach ($posts as $post) {
            $changed = false;

            foreach ($locales as $locale) {
                $title = $post->getTranslation('title', $locale, false)
                    ?: $post->getTranslation('title', $fallback, false);

                $old = (string) $post->getTranslation('slug', $locale, false);

                $new = $old !== ''
                    ? BlogPost::makeSeoSlug($old, $locale)
                    : (filled($title) ? BlogPost::makeSeoSlug($title, $locale) : '');

                if ($new !== '') {
                    $new = BlogPost::ensureUniqueSlug($post, $locale, $new);
                }

                if ($new !== $old) {
                    $changed = true;
                    $this->line("ID {$post->id} [{$locale}] {$old}  =>  {$new}");
                    if (!$dry) {
                        $post->setTranslation('slug', $locale, $new);
                    }
                }
            }

            if ($changed && !$dry) {
                // saving() du modèle va re-check unicité/normalisation aussi
                $post->save();
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}
