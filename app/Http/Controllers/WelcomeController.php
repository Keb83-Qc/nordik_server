<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    /**
     * Route /{locale}/ — landing localisée.
     *
     * Si le visiteur a déjà choisi sa langue (welcome_seen), on le renvoie
     * directement sur le home. La route racine / utilise root() qui affiche
     * toujours la landing.
     */
    public function index(Request $request)
    {
        if (session()->has('locale') && session('welcome_seen') === true) {
            return redirect()->route('home', ['locale' => session('locale')]);
        }

        return $this->renderLanding($request);
    }

    /**
     * Route / — toujours afficher la landing, peu importe la session.
     * Évite que les visiteurs récurrents soient silencieusement renvoyés sur home.
     */
    public function root(Request $request)
    {
        return $this->renderLanding($request);
    }

    /**
     * GET /switch-language/{locale}?next=...
     * Change la locale en session et redirige vers la page demandée.
     */
    public function switchLanguage(Request $request, string $locale)
    {
        $active = Language::activeCodes();
        if (! in_array($locale, $active, true)) {
            $locale = Language::defaultCode();
        }

        session(['locale' => $locale, 'welcome_seen' => true]);
        session()->save();

        $next = $request->query('next');
        if (is_string($next) && $next !== '') {
            $next = '/' . ltrim($next, '/');
            $next = preg_replace('#^/[a-zA-Z]{2,5}(/|$)#', '/', $next);
            if ($next === '/') {
                $next = '/home';
            }
            return redirect("/{$locale}{$next}");
        }

        return redirect("/{$locale}/home");
    }

    private function renderLanding(Request $request): \Illuminate\View\View
    {
        // Utilise le cache Language (1h) — évite une requête DB brute à chaque visite
        $activeCodes = Language::activeCodes();

        $langs = Cache::remember('languages.all_active', 3600, function () {
            return Language::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });

        $detected = $this->detectBestLocale($request->getLanguages(), $activeCodes)
            ?? Language::defaultCode();

        // Respecte la session si l'utilisateur a déjà choisi sa langue
        if (session()->has('locale') && in_array(session('locale'), $activeCodes, true)) {
            $detected = session('locale');
        }

        app()->setLocale($detected);

        return view('landing', [
            'langs'          => $langs,
            'detectedLocale' => $detected,
        ]);
    }

    private function detectBestLocale(array $preferred, array $activeCodes): ?string
    {
        foreach ($preferred as $lang) {
            $lang = strtolower(str_replace('_', '-', $lang)); // ex: fr-ca

            if (in_array($lang, $activeCodes, true)) {
                return $lang;
            }

            $base = explode('-', $lang)[0]; // ex: fr
            if (in_array($base, $activeCodes, true)) {
                return $base;
            }
        }

        return null;
    }
}
