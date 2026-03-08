<?php

namespace App\Services;

use Illuminate\Filesystem\Filesystem;

class LangFilesManager
{
    public function __construct(private Filesystem $files) {}

    public function ensureLocaleFromFrench(string $locale): void
    {
        $locale = strtolower(trim($locale));

        // Sécurité simple: lettres + tiret seulement (ex: fr, en, pt-br)
        if (!preg_match('/^[a-z]{2,5}(-[a-z0-9]{2,5})?$/i', $locale)) {
            return;
        }

        $source = resource_path('lang/fr');
        $target = resource_path("lang/{$locale}");

        if (!$this->files->isDirectory($source)) {
            return;
        }

        // Ne pas écraser si existe déjà
        if ($this->files->isDirectory($target)) {
            return;
        }

        $this->files->makeDirectory($target, 0755, true);
        $this->files->copyDirectory($source, $target);
    }
}
