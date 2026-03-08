<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    private array $supported = ['fr', 'en', 'es', 'ht'];

    public function switch(string $locale, Request $request)
    {
        if (!in_array($locale, $this->supported, true)) {
            $locale = config('app.fallback_locale', 'fr');
        }

        session([
            'locale' => $locale,
            'welcome_seen' => true,
        ]);

        App::setLocale($locale);

        // Redirige vers la page précédente mais en version /{locale}/...
        $previous = url()->previous();
        $path = parse_url($previous, PHP_URL_PATH) ?? '/';

        // Si l'ancienne URL commence déjà par /fr ou /en etc, on remplace
        $path = preg_replace('#^/(fr|en|es|ht)(/|$)#', '/', $path);
        $path = '/' . trim($path, '/');

        // Cas homepage
        if ($path === '/' || $path === '') {
            return redirect("/{$locale}");
        }

        return redirect("/{$locale}{$path}");
    }
}
