<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // 1) Locale depuis l'URL /{locale}/...
        $locale = $request->route('locale');

        // 2) Sinon depuis la session
        if (!is_string($locale) || $locale === '') {
            $locale = Session::get('locale');
        }

        // 3) Sinon la langue par défaut DB (avec fallback)
        if (!is_string($locale) || $locale === '') {
            $locale = Language::defaultCode();
        }

        // 4) Validation: doit être active
        $active = Language::activeCodes();
        if (!in_array($locale, $active, true)) {
            $locale = Language::defaultCode();
        }

        // 5) Application
        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);

        // Écrire en session seulement si la valeur a changé (évite une écriture inutile à chaque requête)
        if (Session::get('locale') !== $locale) {
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}
