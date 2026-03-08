<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        // si déjà choisi, on skip
        if (session()->has('locale') && session('welcome_seen') === true) {
            return redirect()->route('home', ['locale' => session('locale')]);
        }

        $langs = Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $activeCodes = $langs->pluck('code')
            ->map(fn($c) => strtolower($c))
            ->values()
            ->all();

        $detected = $this->detectBestLocale($request->getLanguages(), $activeCodes)
            ?? Language::defaultCode();

        // Render landing in detected language (no session write yet)
        app()->setLocale($detected);

        return view('landing', [
            'langs' => $langs,
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
