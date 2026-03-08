<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AbfPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('abf')
            ->path('abf')

            // Si tu veux que ABF ait son propre login, garde ->login().
            // Sinon, si tes users sont déjà connectés via admin, ça marche aussi.
            ->login()

            // Full width ABF (global au panel ABF)
            ->maxContentWidth('full')

            ->colors([
                'primary' => Color::hex('#c9a050'),
                'gray'    => Color::Slate,
            ])
            ->brandName('VIP GPI — ABF')
            ->brandLogo(asset('assets/img/VIP_Logo_Gold_Gradient10.png'))
            ->brandLogoHeight('3rem')

            // ✅ Panel “focus” : pas de sidebar (CSS-only, stable)

            ->renderHook('panels::head.end', fn() => $this->abfFocusCss() . $this->abfFontsCss())
            ->renderHook('panels::body.start', fn() => '<div class="abf-panel"></div>')

            // ✅ Ne découvre QUE les ressources ABF (dans un dossier dédié)
            ->discoverResources(in: app_path('Filament/Abf/Resources'), for: 'App\\Filament\\Abf\\Resources')
            ->discoverPages(in: app_path('Filament/Abf/Pages'), for: 'App\\Filament\\Abf\\Pages')
            ->discoverWidgets(in: app_path('Filament/Abf/Widgets'), for: 'App\\Filament\\Abf\\Widgets')

            // Middleware standard Filament
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    private function abfFontsCss(): string
    {
        return <<<HTML
<style>
@import url("/assets/css/fonts.css");
html, body { font-family: "Montserrat", ui-sans-serif, system-ui, sans-serif !important; }
</style>
HTML;
    }

    private function abfFocusCss(): string
    {
        return <<<'HTML'
<style>
/* ==========================================================================
ABF PANEL (focus)
- Cache sidebar + topbar
- Met le contenu en vrai full width
========================================================================== */
.abf-panel ~ .fi-layout .fi-sidebar,
.abf-panel ~ .fi-layout .fi-topbar {
    display: none !important;
}

/* ✅ On garde le header des pages (où sont Retour / Sauvegarder / Créer / PDF) */
.abf-panel ~ .fi-layout .fi-layout-main-topbar {
    display: block !important;
}

.abf-panel ~ .fi-layout .fi-main,
.abf-panel ~ .fi-layout .fi-page-body,
.abf-panel ~ .fi-layout .fi-container,
.abf-panel ~ .fi-layout .fi-main-ctn,
.abf-panel ~ .fi-layout .fi-page,
.abf-panel ~ .fi-layout .fi-page-header {
    max-width: none !important;
    width: 100% !important;
}

.abf-panel ~ .fi-layout .fi-main,
.abf-panel ~ .fi-layout .fi-page-body {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
}

/* ==========================================================================
ABF — A) Bilan au décès (mise en forme "PDF")
========================================================================== */
.abf-bilan-deces .abf-bilan-colhead {
    background: #0069b3;
    color: #fff;
    text-align: center;
    font-weight: 700;
    letter-spacing: .03em;
    text-transform: uppercase;
    padding: .35rem .5rem;
    border-radius: 0.125rem;
}

.abf-bilan-deces .abf-bilan-label { padding-top: .35rem; }

.abf-bilan-deces .abf-bilan-total-label {
    color: #0069b3;
    font-weight: 800;
    text-transform: uppercase;
    padding-top: .35rem;
}

.abf-bilan-deces .abf-bilan-total-value {
    border-bottom: 1px solid #9aa3af;
    padding: .25rem .25rem;
    text-align: right;
    font-weight: 700;
}

.abf-bilan-deces .abf-underline-input {
    border: 0 !important;
    border-bottom: 1px solid #9aa3af !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    background: transparent !important;
    text-align: right !important;
    padding-left: .25rem !important;
    padding-right: .25rem !important;
}

.abf-bilan-deces input[type=number]::-webkit-outer-spin-button,
.abf-bilan-deces input[type=number]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.abf-bilan-deces input[type=number] { -moz-appearance: textfield; }
</style>
HTML;
    }
}
