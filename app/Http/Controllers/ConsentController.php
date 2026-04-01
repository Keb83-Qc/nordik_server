<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuoteType;
use App\Models\User;

class ConsentController extends Controller
{
    // --- 1. CHANGER LA LANGUE ---
    public function switchLanguage($locale, $advisorCode = null)
    {
        if (in_array($locale, ['fr', 'en', 'ht', 'es'], true)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        return redirect()->route('consent.show', ['locale' => $locale, 'code' => $advisorCode]);
    }

    // --- 2. AFFICHER LA PAGE ---
    public function show(string $locale, ?string $advisorCode = null)
    {
        if (in_array($locale, ['fr', 'en', 'ht', 'es'], true)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        } else {
            $locale = config('app.fallback_locale', 'fr');
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        $advisor = User::where('advisor_code', $advisorCode)->first();

        if (! $advisor) {
            $advisor = User::where('role_id', 1)->orderBy('id')->first();
        }

        abort_if(! $advisor, 404);

        session([
            'current_advisor_code' => $advisor->advisor_code,
            'has_consented' => false,
        ]);

        $advisorName  = $advisor->first_name . ' ' . $advisor->last_name;
        $advisorPhone = $advisor->phone ?? '1-888-123-4567';

        $availableTypes = QuoteType::active()->get();

        return view('consentement', compact('advisor', 'advisorName', 'advisorPhone', 'availableTypes'));
    }

    // --- 3. ACCEPTER LE CONSENTEMENT ---
    public function accept(Request $request, string $locale)
    {
        if (in_array($locale, ['fr', 'en', 'ht', 'es'], true)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        session(['has_consented' => true]);

        $typeSlug = (string) $request->input('quote_type', '');

        $exists = QuoteType::active()
            ->where('slug', $typeSlug)
            ->exists();

        if ($exists) {
            return redirect()->route('quote.chat', ['locale' => $locale, 'typeSlug' => $typeSlug]);
        }

        $fallbackSlug = QuoteType::active()->value('slug') ?? 'auto';
        return redirect()->route('quote.chat', ['locale' => $locale, 'typeSlug' => $fallbackSlug]);
    }
}
