<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    /**
     * Route /{locale}/ — page de sélection de langue (landing).
     *
     * Raccourci session : si le visiteur a déjà choisi sa langue (welcome_seen = true)
     * ET qu'il n'arrive PAS depuis le domaine racine (/), on le renvoie directement
     * sur le home. Sinon, on affiche toujours la landing.
     */
    public function index(Request $request)
    {
        // Si arrivé depuis la route racine "/" on ne skip jamais la landing
        $fromRoot = $request->headers->get('referer') === null
            && $request->getPathInfo() === '/';

        if (! $fromRoot && session()->has('locale') && session('welcome_seen') === true) {
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
