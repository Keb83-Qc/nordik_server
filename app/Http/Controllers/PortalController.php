<?php

namespace App\Http\Controllers;

use App\Models\QuotePortal;
use App\Models\User;
use App\Services\LeadDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function consent(string $locale, string $portalSlug): View
    {
        $this->applyLocale($locale);

        $portal = QuotePortal::where('slug', $portalSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $activeTypes = $portal->activeQuoteTypes()->get();

        session(['has_consented' => false, 'portal_slug' => $portalSlug]);

        return view('quote.portal-consent', compact('portal', 'activeTypes', 'locale'));
    }

    public function accept(Request $request, string $locale, string $portalSlug): RedirectResponse
    {
        $this->applyLocale($locale);

        $portal = QuotePortal::where('slug', $portalSlug)
            ->where('is_active', true)
            ->firstOrFail();

        if ($portal->advisor_code) {
            $advisor      = User::where('advisor_code', $portal->advisor_code)->first();
            $assignedType = 'fixed';

            if (! $advisor) {
                $advisor      = app(LeadDispatcher::class)->assignAdvisor();
                $assignedType = 'roundrobin';
            }
        } else {
            $advisor      = app(LeadDispatcher::class)->assignAdvisor();
            $assignedType = 'roundrobin';
        }

        session([
            'has_consented'         => true,
            'portal_slug'           => $portalSlug,
            'portal_id'             => $portal->id,
            'advisor_assigned_type' => $assignedType,
            'current_advisor_code'  => $advisor?->advisor_code,
        ]);

        $typeSlug = (string) $request->input('quote_type', '');

        $validSlugs = $portal->activeQuoteTypes()->pluck('slug')->toArray();
        if (! in_array($typeSlug, $validSlugs, true)) {
            $typeSlug = $validSlugs[0] ?? '';
        }

        abort_if($typeSlug === '', 404);

        return redirect()->route('portal.quote.chat', [
            'locale'     => $locale,
            'portalSlug' => $portalSlug,
            'typeSlug'   => $typeSlug,
        ]);
    }

    public function chat(string $locale, string $portalSlug, string $typeSlug): View
    {
        abort_unless(session('has_consented') === true, 403);

        $this->applyLocale($locale);

        $portal    = QuotePortal::where('slug', $portalSlug)->firstOrFail();
        $component = $this->resolveComponent($typeSlug);

        return view('quote.portal-chat', compact('portal', 'component', 'typeSlug'));
    }

    private function resolveComponent(string $typeSlug): string
    {
        $class = 'App\\Livewire\\Quote' . Str::studly($typeSlug) . 'Chat';

        if (! class_exists($class)) {
            return 'quote-commercial-chat';
        }

        return Str::of(class_basename($class))->kebab()->toString();
    }

    private function applyLocale(string $locale): void
    {
        $supported = ['fr', 'en', 'es', 'ht'];
        $locale    = in_array($locale, $supported, true) ? $locale : 'fr';

        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
}
