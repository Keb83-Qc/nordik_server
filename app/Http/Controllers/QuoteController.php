<?php

namespace App\Http\Controllers;

use App\Models\QuoteType;
use Illuminate\Support\Str;
use Illuminate\View\View;

class QuoteController extends Controller
{
    public function chat(string $locale, string $typeSlug): View
    {
        abort_unless(session('has_consented') === true, 403);

        if (in_array($locale, ['fr', 'en', 'ht', 'es'], true)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        $quoteType = QuoteType::active()
            ->where('slug', $typeSlug)
            ->firstOrFail();

        return view('quote.dynamic', [
            'quoteType'  => $quoteType,
            'component'  => $this->resolveComponent($quoteType->slug),
        ]);
    }

    private function resolveComponent(string $typeSlug): string
    {
        $class = 'App\\Livewire\\Quote' . Str::studly($typeSlug) . 'Chat';

        if (! class_exists($class)) {
            return 'quote-commercial-chat';
        }

        return Str::of(class_basename($class))
            ->kebab()
            ->toString();
    }
}
