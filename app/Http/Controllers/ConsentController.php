<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuoteType;
use App\Models\User;
use App\Enums\UserRole;

class ConsentController extends Controller
{
    /** Locales supportées — lues depuis la table languages (avec cache) */
    private function supportedLocales(): array
    {
        return Language::activeCodes();
    }

    private function applyLocale(string $locale): string
    {
        if (in_array($locale, $this->supportedLocales(), true)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
            return $locale;
        }

        $fallback = config('app.fallback_locale', 'fr');
        session(['locale' => $fallback]);
        app()->setLocale($fallback);
        return $fallback;
    }

    // --- 1. CHANGER LA LANGUE ---
    public function switchLanguage($locale, $advisorCode = null)
    {
        $locale = $this->applyLocale($locale);

        return redirect()->route('consent.show', ['locale' => $locale, 'code' => $advisorCode]);
    }

    // --- 2. AFFICHER LA PAGE ---
    public function show(string $locale, ?string $advisorCode = null)
    {
        $locale = $this->applyLocale($locale);

        $advisor = User::where('advisor_code', $advisorCode)->first()
            ?? User::where('role_id', UserRole::ADMIN)->orderBy('id')->first();

        abort_if(! $advisor, 404);

        session([
            'current_advisor_code' => $advisor->advisor_code,
            'has_consented'        => false,
        ]);

        $advisorName  = $advisor->first_name . ' ' . $advisor->last_name;
        $advisorPhone = $advisor->phone ?? '1-888-123-4567';

        $availableTypes = QuoteType::active()->get();

        return view('consentement', compact('advisor', 'advisorName', 'advisorPhone', 'availableTypes'));
    }

    // --- 3. ACCEPTER LE CONSENTEMENT ---
    public function accept(Request $request, string $locale)
    {
        $this->applyLocale($locale);
        session(['has_consented' => true]);

        $typeSlug = (string) $request->input('quote_type', '');

        $exists = QuoteType::active()->where('slug', $typeSlug)->exists();

        if ($exists) {
            return redirect()->route('quote.chat', ['locale' => $locale, 'typeSlug' => $typeSlug]);
        }

        $fallbackSlug = QuoteType::active()->value('slug') ?? 'auto';
        return redirect()->route('quote.chat', ['locale' => $locale, 'typeSlug' => $fallbackSlug]);
    }
}
