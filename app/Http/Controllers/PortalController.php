<?php

namespace App\Http\Controllers;

use App\Models\QuotePortal;
use App\Models\User;
use App\Services\LeadDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    // ─── Types supportés → nom du composant Livewire ─────────────────────────
    private const TYPE_MAP = [
        'auto'       => 'quote-auto-chat',
        'habitation' => 'quote-home-chat',
        'bundle'     => 'quote-bundle-chat',
    ];

    // ─── 1. Page de consentement du portail ───────────────────────────────────
    public function consent(string $locale, string $portalSlug): View
    {
        $this->applyLocale($locale);

        $portal = QuotePortal::where('slug', $portalSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $activeTypes = $portal->activeQuoteTypes()->get();

        // Réinitialise le consentement à chaque visite
        session(['has_consented' => false, 'portal_slug' => $portalSlug]);

        return view('quote.portal-consent', compact('portal', 'activeTypes', 'locale'));
    }

    // ─── 2. Acceptation du consentement ──────────────────────────────────────
    public function accept(Request $request, string $locale, string $portalSlug): RedirectResponse
    {
        $this->applyLocale($locale);

        $portal = QuotePortal::where('slug', $portalSlug)
            ->where('is_active', true)
            ->firstOrFail();

        // Assigner un conseiller :
        // - Si le portail a un conseiller fixe → on l'utilise directement
        // - Sinon → rotation automatique (LeadDispatcher)
        if ($portal->advisor_code) {
            $advisor      = User::where('advisor_code', $portal->advisor_code)->first();
            $assignedType = 'fixed';

            // Fallback si le conseiller fixe n'existe plus
            if (! $advisor) {
                $advisor      = app(LeadDispatcher::class)->assignAdvisor();
                $assignedType = 'roundrobin';
            }
        } else {
            $advisor      = app(LeadDispatcher::class)->assignAdvisor();
            $assignedType = 'roundrobin';
        }

        session([
            'has_consented'        => true,
            'portal_slug'          => $portalSlug,
            'portal_id'            => $portal->id,
            'advisor_assigned_type' => $assignedType,
            'current_advisor_code' => $advisor?->advisor_code,
        ]);

        $typeSlug = $request->input('quote_type', 'auto');

        // Vérifier que le type demandé est actif pour ce portail
        $validSlugs = $portal->activeQuoteTypes()->pluck('slug')->toArray();
        if (! in_array($typeSlug, $validSlugs, true)) {
            $typeSlug = $validSlugs[0] ?? 'auto';
        }

        return redirect()->route('portal.quote.chat', [
            'locale'     => $locale,
            'portalSlug' => $portalSlug,
            'typeSlug'   => $typeSlug,
        ]);
    }

    // ─── 3. Page de chat (auto / habitation / bundle) ─────────────────────────
    public function chat(string $locale, string $portalSlug, string $typeSlug): View
    {
        abort_unless(session('has_consented') === true, 403);

        $this->applyLocale($locale);

        $portal    = QuotePortal::where('slug', $portalSlug)->firstOrFail();
        $component = self::TYPE_MAP[$typeSlug] ?? 'quote-auto-chat';

        return view('quote.portal-chat', compact('portal', 'component', 'typeSlug'));
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function applyLocale(string $locale): void
    {
        $supported = ['fr', 'en', 'es', 'ht'];
        $locale    = in_array($locale, $supported, true) ? $locale : 'fr';

        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
}
