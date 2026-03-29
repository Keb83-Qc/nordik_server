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
use Filament\Navigation\NavigationItem;
use Filament\Facades\Filament;

class ConseillerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('conseiller')
            ->path('conseiller')
            ->login()
            ->maxContentWidth('full')

            ->colors([
                'primary' => Color::hex('#c9a050'),
                'gray'    => Color::Slate,
            ])

            // ✅ Pas de ->font('Montserrat') (sinon Bunny fonts)
            ->brandName('Espace Conseiller VIPGPI')
            ->brandLogo(asset('assets/img/VIP_Logo_Gold_Gradient10.png'))
            ->brandLogoHeight('3rem')
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups()
            ->darkMode(true)

            // ✅ CSS + JS (reprend la même approche que ton AdminPanelProvider)
            ->renderHook('panels::head.end', fn(): string => $this->styles())
            ->renderHook('panels::body.end', fn(): string => $this->scripts())

            // Le panel conseiller n'expose aucune resource admin.
            // Les conseillers accèdent à ABF via /abf (panel dédié) et au reste via /admin (admins seulement).
            ->resources([])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->widgets([
                \App\Filament\Widgets\WelcomeOverview::class,
                \App\Filament\Widgets\DocumentsWidget::class,
                \App\Filament\Widgets\LatestWikiWidget::class,
            ])
            ->pages([
                \App\Filament\Conseiller\Pages\Dashboard::class,
                \App\Filament\Pages\EditProfile::class,
            ])
            ->navigationItems([
                NavigationItem::make('Administration')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url('/admin')
                    ->group('Accès')
                    ->sort(900)
                    ->visible(function () {
                        $user = auth()->user();
                        if (! $user) return false;

                        // Si tu utilises spatie:
                        if (method_exists($user, 'hasAnyRole')) {
                            return $user->hasAnyRole(['admin', 'super_admin']);
                        }

                        // Fallback si tu utilises role_id (ajuste IDs si besoin)
                        return in_array((int) ($user->role_id ?? 0), [1, 2], true);
                    }),

                NavigationItem::make('Analyse des bilans financiers')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url('/abf')
                    ->openUrlInNewTab()
                    ->group('Accès')
                    ->sort(999),

                NavigationItem::make('Mon profil')
                    ->icon('heroicon-o-user')
                    ->url('/conseiller/profil')
                    ->group('Espace Conseiller')
                    ->sort(20),
            ])
            // ✅ Accès : tout le monde SAUF candidat / en attente
            ->authMiddleware([Authenticate::class])
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
            ]);
    }

    private function styles(): string
    {
        return <<<HTML
<style>
@import url("/assets/css/fonts.css");
html, body { font-family: "Montserrat", ui-sans-serif, system-ui, sans-serif !important; }

/* ===== Ultrawide Premium (auto) ===== */
@media (min-width: 3000px) {
    html.vip-ultra .fi-main-ctn,
    html.vip-ultra .fi-page,
    html.vip-ultra .fi-page-header,
    html.vip-ultra .fi-page-body {
        max-width: 2600px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    html.vip-ultra .fi-sidebar { width: 320px !important; }

    html.vip-ultra .fi-wi {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 1.25rem !important;
        align-items: start !important;
    }

    html.vip-ultra .fi-ta { font-size: 1rem !important; }
    html.vip-ultra .fi-ta th, html.vip-ultra .fi-ta td {
        padding-top: .9rem !important;
        padding-bottom: .9rem !important;
    }
}

/* Toggle button (si tu l’ajoutes en topbar) */
.vip-ultra-toggle {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .35rem .6rem;
    border-radius: 999px;
    border: 1px solid rgba(148,163,184,.22);
    background: rgba(255,255,255,.04);
    color: rgba(255,255,255,.9);
    font-size: .85rem;
    line-height: 1;
}
.vip-ultra-toggle .dot {
    width: 8px; height: 8px; border-radius: 999px;
    background: rgba(201,160,80,.95);
    box-shadow: 0 0 0 4px rgba(201,160,80,.15);
}
.vip-ultra-toggle.off .dot {
    background: rgba(148,163,184,.85);
    box-shadow: 0 0 0 4px rgba(148,163,184,.12);
}
</style>
HTML;
    }

    private function scripts(): string
    {
        return <<<HTML
<script>
(function () {
    const KEY = 'vipgpi_ultra_mode_v1'; // 'on' | 'off' | null
    const MIN_WIDTH = 3000;

    function pref() {
        try { return localStorage.getItem(KEY); } catch { return null; }
    }
    function setPref(v) {
        try { localStorage.setItem(KEY, v); } catch {}
    }
    function isUltraScreen() {
        return window.matchMedia('(min-width: 3000px)').matches;
    }
    function apply(force) {
        const p = pref();
        let on = false;

        if (force === true) on = true;
        else if (force === false) on = false;
        else if (p === 'on') on = true;
        else if (p === 'off') on = false;
        else on = isUltraScreen();

        document.documentElement.classList.toggle('vip-ultra', on);

        const btn = document.querySelector('[data-vip-ultra-toggle]');
        if (btn) {
            btn.classList.toggle('off', !on);
            btn.setAttribute('aria-pressed', on ? 'true' : 'false');
        }
    }

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-vip-ultra-toggle]');
        if (!btn) return;

        const enabled = document.documentElement.classList.contains('vip-ultra');
        const next = !enabled;
        setPref(next ? 'on' : 'off');
        apply(next);
    });

    document.addEventListener('keydown', (e) => {
        if (!e.altKey) return;
        if ((e.key || '').toLowerCase() !== 'u') return;

        const enabled = document.documentElement.classList.contains('vip-ultra');
        const next = !enabled;
        setPref(next ? 'on' : 'off');
        apply(next);
    });

    window.addEventListener('resize', () => {
        const p = pref();
        if (p === 'on' || p === 'off') return;
        apply();
    });

    // Auto init
    apply();
})();
</script>
HTML;
    }
}
