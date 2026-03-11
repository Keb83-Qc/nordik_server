<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ConsentController extends Controller
{
    // --- 1. CHANGER LA LANGUE ---
    public function switchLanguage($locale, $advisorCode = null)
    {
        // Valider les langues acceptées (fr, en, ht = Haïtien/Créole)
        if (in_array($locale, ['fr', 'en', 'ht', 'es'], true)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        // Rediriger vers la page de consentement avec le code conseiller
        return redirect()->route('consent.show', ['locale' => $locale, 'code' => $advisorCode]);
    }

    // --- 2. AFFICHER LA PAGE (FUSIONNÉE) ---
    public function show(string $locale, ?string $advisorCode = null)
    {
        // Appliquer la langue depuis l'URL
        if (in_array($locale, ['fr', 'en', 'ht', 'es'], true)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        } else {
            $locale = config('app.fallback_locale', 'fr');
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        // Recherche du conseiller via le code
        $advisor = User::where('advisor_code', $advisorCode)->first();

        // Si introuvable, fallback sur le premier super_admin
        if (! $advisor) {
            $advisor = User::where('role_id', 1)->orderBy('id')->first();
        }

        // Aucun admin trouvé : page 404 propre
        abort_if(! $advisor, 404);

        session([
            'current_advisor_code' => $advisor->advisor_code,
            'has_consented' => false,
        ]);

        $advisorName  = $advisor->first_name . ' ' . $advisor->last_name;
        $advisorPhone = $advisor->phone ?? '1-888-123-4567';

        return view('consentement', compact('advisor', 'advisorName', 'advisorPhone'));
    }

    // --- 3. ACCEPTER LE CONSENTEMENT ---
    public function accept(Request $request, string $locale)
    {
        if (in_array($locale, ['fr', 'en', 'ht', 'es'], true)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        session(['has_consented' => true]);

        $nextRoute = $request->input('next_route');

        $allowed = ['quote.auto', 'quote.habitation', 'quote.bundle']; // commercial plus tard
        if (in_array($nextRoute, $allowed, true)) {
            return redirect()->route($nextRoute, ['locale' => $locale]);
        }

        return redirect()->route('quote.auto', ['locale' => $locale]);
    }
}
