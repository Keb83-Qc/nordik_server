<?php

namespace App\Providers\Filament;

use App\Filament\Pages\EditProfile;
use App\Filament\Widgets\DocumentsWidget;
use App\Filament\Widgets\LatestUsersWidget;
use App\Filament\Widgets\LatestWikiWidget;
use App\Filament\Widgets\LinksWidget;
use App\Filament\Widgets\QuickLinks;
use App\Filament\Widgets\WelcomeOverview;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Forms\Components\Section;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Models\Language;
use Filament\View\PanelsRenderHook;


class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        // Toutes les Sections Filament collapsibles + persist
        Section::configureUsing(static function (Section $section): void {
            $section->collapsible()->persistCollapsed();
        });
    }

    public function panel(Panel $panel): Panel
    {
        $navConfig = config('filament-navigation.groups', []);

        return $panel
            ->default()
            ->id('admin')
            ->path('espace-conseiller')
            ->login()

            // ✅ FULL SCREEN GLOBAL (toutes les pages Filament)
            ->maxContentWidth('full')

            ->colors([
                'primary' => Color::hex('#c9a050'),
                'gray'    => Color::Slate,
            ])
            ->brandName('VIP GPI')
            ->brandLogo(asset('assets/img/VIP_Logo_Gold_Gradient10.png'))
            ->brandLogoHeight('3rem')
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups()
            ->darkMode(true)

            // ->assetsVersion(fn() => (string) @filemtime(public_path('js/filament/filament/app.js')))


            // ✅ Inline CSS + JS (pas de Vite, pas de manifest, pas de npm requis)
            ->renderHook('panels::head.end', fn(): string => $this->globalStyles())
            ->renderHook('panels::body.end', fn(): string => $this->globalScripts())

            // ✅ Ajout "Bonjour, Nom, Date" + compteurs dans la topbar
            ->renderHook('panels::topbar.start', fn(): string => $this->topbarWelcome())
            // ->renderHook('panels::page.header.actions.after', function (): string {
            //     // On ne veut la toolbar QUE sur la page edit d'un blog post
            //     if (! request()->is('admin/blog-posts/*/edit')) {
            //         return '';
            //     }

            //     // Vue: resources/views/filament/blog-posts/header-toolbar.blade.php
            //     if (! view()->exists('filament.blog-posts.header-toolbar')) {
            //         return '';
            //     }

            //     return view('filament.blog-posts.header-toolbar')->render();
            // })

            ->navigationGroups(
                collect($navConfig)
                    ->sortBy('sort')
                    ->map(
                        fn($group) => NavigationGroup::make()
                            ->label($group['label'])
                            ->icon($group['icon'] ?? null)
                            ->collapsible()
                            ->collapsed($group['collapsed'] ?? true)
                    )
                    ->toArray()
            )

            ->navigationItems($this->buildNavigationItems())
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                EditProfile::class,
            ])

            // ✅ Dashboard widgets (tes widgets)
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                WelcomeOverview::class,
                QuickLinks::class,
                LinksWidget::class,
                DocumentsWidget::class,
                LatestUsersWidget::class,
                LatestWikiWidget::class,
            ])

            ->userMenuItems([
                MenuItem::make()
                    ->label('Mon Profil')
                    ->url(fn(): string => EditProfile::getUrl())
                    ->icon('heroicon-o-user-circle'),
            ])

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

            ->plugins([
                FilamentShieldPlugin::make(),
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales($this->filamentLocales()),
                FilamentApexChartsPlugin::make(),
            ])

            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    private function topbarWelcome(): string
    {
        // Vue Blade dédiée à la topbar (garde le Provider clean)
        return view('filament.partials.topbar-welcome')->render();
    }

    private function filamentLocales(): array
    {
        try {
            return Language::query()
                ->where('is_active', 1)
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->pluck('code')
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return ['fr', 'en']; // fallback safe pour package:discover
        }
    }

    private function buildNavigationItems(): array
    {
        $links = config('filament-navigation.links', []);

        return collect($links)
            ->sortBy('sort')
            ->map(function (array $link) {
                $item = NavigationItem::make($link['label'])
                    ->label($link['label'])
                    ->icon($link['icon'] ?? null)
                    ->url(url($link['url']))
                    ->group($link['group'] ?? null);

                if (!empty($link['new_tab'])) {
                    $item->openUrlInNewTab();
                }

                return $item;
            })
            ->toArray();
    }

    private function globalStyles(): string
    {
        $docsUrl = url('/espace-conseiller/wiki');      // Wiki Filament
        try {
            $supportUrl = app(\App\Settings\EmailSettings::class)->support_url;
        } catch (\Throwable) {
            $supportUrl = 'mailto:' . config('mail.from.address', 'support@vipgpi.ca');
        }
        $build = $this->resolveBuildVersion();         // build auto

        return <<<HTML
<style>

@import url("/assets/css/fonts.css");
html, body { font-family: "Montserrat", ui-sans-serif, system-ui, sans-serif !important; }
/* =============================================================================
   VIPGPI – Admin UI (Clean & Pro)
   ============================================================================= */

/* ---------- Tokens ---------- */
:root{
    --vip-accent: #c9a050;
    --vip-accent-soft: rgba(201,160,80,.18);
    --vip-border: rgba(148,163,184,.22);
    --vip-shadow: 0 10px 25px rgba(0,0,0,.20);
    --vip-shadow-soft: 0 8px 24px rgba(0,0,0,.14);
    --vip-glass-1: rgba(255,255,255,0.06);
    --vip-glass-2: rgba(255,255,255,0.03);
    --vip-bg-soft: rgba(148,163,184,.10);
}

.dark{
    --vip-border: rgba(148,163,184,.16);
    --vip-shadow: 0 10px 25px rgba(0,0,0,.35);
    --vip-shadow-soft: 0 8px 24px rgba(0,0,0,.28);
    --vip-bg-soft: rgba(148,163,184,.09);
}

/* =============================================================================
   LAYOUT – Full width global
   ============================================================================= */
.fi-page,
.fi-page-header,
.fi-page-body,
.fi-main,
.fi-main-ctn,
.fi-container{
    max-width:none !important;
}

.fi-main,
.fi-page-body{
    padding-left: 1rem !important;
    padding-right: 1rem !important;
}

/* =============================================================================
   TOPBAR (barre du haut)
   ============================================================================= */
.fi-topbar{
    border-bottom: 1px solid var(--vip-border) !important;
    box-shadow: none !important;
}

/* Masquer la recherche globale du topbar (celle en haut à droite) */
.fi-topbar .fi-global-search,
.fi-topbar [data-global-search],
.fi-topbar .fi-input-wrp[type="search"],
.fi-topbar input[type="search"],
.fi-topbar input[placeholder*="Rechercher"]{
    display: none !important;
}

/* =============================================================================
   SIDEBAR
   ============================================================================= */
.fi-sidebar{ z-index: 30 !important; }

/* ── Modal centré (les z-index laissés aux défaults Filament z-50 > sidebar z-30) ── */
.fi-dialog-panel      { margin-left: auto !important; margin-right: auto !important; }

.fi-sidebar-header{
    background: linear-gradient(180deg, rgba(14,16,48,1) 0%, rgba(14,16,48,.92) 100%) !important;
    border-bottom: 1px solid rgba(201,160,80,.45) !important;
}

.fi-sidebar-header .fi-sidebar-header-brand{
    padding: .65rem .75rem !important;
}

.fi-sidebar-nav{
    padding: .75rem .75rem 0.75rem .75rem !important;
}

/* Group label */
.fi-sidebar-group-label{
    letter-spacing: .03em;
    font-weight: 800 !important;
    font-size: .78rem !important;
    opacity: .82;
    margin: .95rem .25rem .35rem !important;
    text-transform: uppercase;
}

/* Groupe ouvert */
.fi-sidebar-group{
    border-radius: 14px !important;
    padding: .15rem .15rem .25rem .15rem !important;
}

/* NOTE: :has() = ok sur navigateurs récents. Si tu veux 0 risque, je te donne une version sans :has. */
.fi-sidebar-group:has(button[aria-expanded="true"]){
    background: rgba(201,160,80,.06);
    border: 1px solid rgba(201,160,80,.22);
}
.fi-sidebar-group:has(button[aria-expanded="true"]) .fi-sidebar-group-label{
    color: var(--vip-accent) !important;
    opacity: 1 !important;
}

/* Items pills */
.fi-sidebar-item,
.fi-sidebar-item a,
.fi-sidebar-item button{ border-radius: 12px !important; }

.fi-sidebar-item a,
.fi-sidebar-item button{
    padding: .60rem .75rem !important;
    transition: background-color .15s ease, transform .15s ease, box-shadow .15s ease;
}

.fi-sidebar-item a:hover,
.fi-sidebar-item button:hover{
    background: var(--vip-bg-soft) !important;
    transform: translateY(-1px);
}

/* Actif */
.fi-sidebar-item[aria-current="page"] a,
.fi-sidebar-item a[aria-current="page"],
.fi-sidebar-item.fi-active a,
.fi-sidebar-item.fi-active button{
    background: var(--vip-accent-soft) !important;
    position: relative;
    box-shadow: 0 8px 18px rgba(201,160,80,.12);
}

.fi-sidebar-item[aria-current="page"] a::before,
.fi-sidebar-item a[aria-current="page"]::before,
.fi-sidebar-item.fi-active a::before,
.fi-sidebar-item.fi-active button::before{
    content: "";
    position: absolute;
    left: 6px;
    top: 10px;
    bottom: 10px;
    width: 3px;
    border-radius: 999px;
    background: var(--vip-accent);
}

/* =============================================================================
   HEADER (PAGE HEADER) – carte comme la table (IMPORTANT)
   ============================================================================= */

/* On neutralise le “bandeau” */
.fi-header{
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
}

/* Filament v3: le header est souvent dans .fi-page-header */
.fi-page-header{
    padding-top: .75rem !important;
    padding-bottom: .50rem !important;
}

/* On stylise le <header> lui-même (ton HTML: <header class="fi-header flex ...">) */
.fi-page-header header.fi-header{
    border-radius: 18px !important;
    overflow: hidden !important;

    background: linear-gradient(180deg, var(--vip-glass-1) 0%, var(--vip-glass-2) 100%) !important;
    border: 1px solid rgba(255,255,255,0.08) !important;
    box-shadow: var(--vip-shadow) !important;

    padding: 1.05rem 1.25rem !important;
    gap: 1rem !important;
}

/* Mode clair : carte claire */
html:not(.dark) .fi-page-header header.fi-header{
    background: rgba(255,255,255,0.92) !important;
    border: 1px solid rgba(15,23,42,0.06) !important;
    box-shadow: 0 10px 26px rgba(15,23,42,.08) !important;
}

/* Typo titre */
.fi-page-header header.fi-header .fi-header-heading{
    font-size: 1.85rem !important;
    font-weight: 850 !important;
    line-height: 1.1 !important;
    letter-spacing: -0.02em !important;

    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Actions à droite */
.fi-page-header header.fi-header .flex.shrink-0.items-center.gap-3{
    gap: .6rem !important;
}

/* Si actions vides -> hide */
.fi-page-header header.fi-header .flex.shrink-0.items-center.gap-3:empty{
    display: none !important;
}

/* Boutons du header */
.fi-page-header header.fi-header .fi-btn{
    border-radius: 14px !important;
}

/* Primaire (or) */
.fi-page-header header.fi-header .fi-btn-color-primary{
    background: rgba(201,160,80,.20) !important;
    border: 1px solid rgba(201,160,80,.35) !important;
    box-shadow: 0 10px 24px rgba(201,160,80,.12) !important;
}
.fi-page-header header.fi-header .fi-btn-color-primary:hover{
    background: rgba(201,160,80,.28) !important;
}

/* Breadcrumbs: on les enlève (plus clean) */
.fi-breadcrumbs{ display:none !important; }

/* Mobile */
@media (max-width: 640px){
    .fi-page-header header.fi-header{
        padding: .9rem 1rem !important;
    }
    .fi-page-header header.fi-header .fi-header-heading{
        font-size: 1.45rem !important;
    }
}

/* =============================================================================
   CARDS / TABLES
   ============================================================================= */
.fi-section,
.fi-wi,
.fi-card,
.fi-ta-ctn,
.fi-fo-component-ctn{
    border: 1px solid var(--vip-border) !important;
    border-radius: 16px !important;
    box-shadow: var(--vip-shadow-soft);
}

/* Tables compact */
.fi-ta-cell{
    padding-top: 0.35rem !important;
    padding-bottom: 0.25rem !important;
    padding-left: 0.5rem !important;
    padding-right: 0.35rem !important;
}
.fi-ta-row{ min-height: 2.25rem !important; }
.fi-ta-row:hover{ background: rgba(2, 6, 23, 0.04) !important; }
.dark .fi-ta-row:hover{ background: rgba(255, 255, 255, 0.03) !important; }

/* Inputs focus */
.fi-input:focus,
.fi-select:focus,
.fi-fo-field-wrp:focus-within{
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(201,160,80,.22) !important;
    border-color: rgba(201,160,80,.55) !important;
}

/* Sidebar footer style */
.vip-sidebar-footer{
    margin-top: .85rem;
    padding: .85rem .75rem .75rem .75rem;
    border-top: 1px solid var(--vip-border);
    display: grid;
    gap: .35rem;
}
.vip-sidebar-footer .vip-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:.5rem;
    font-size:.78rem;
    opacity:.9;
}
.vip-sidebar-footer a{
    text-decoration:none;
    color: inherit;
    opacity:.95;
}
.vip-sidebar-footer a:hover{
    color: var(--vip-accent);
    opacity: 1;
}
.vip-pill{
    display:inline-flex;
    align-items:center;
    gap:.35rem;
    padding:.20rem .55rem;
    border-radius: 999px;
    border: 1px solid var(--vip-border);
    background: rgba(148,163,184,.10);
    font-size: .72rem;
}
.vip-dot{
    width: .45rem;
    height: .45rem;
    border-radius: 999px;
    background: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,.15);
}

/* =============================================================================
   ULTRAWIDE PREMIUM (VIPGPI)
   Active avec: <html class="vip-ultra">
   ============================================================================= */

@media (min-width: 3000px) {

    /* 1) On centre le contenu au lieu d'étirer 3440px */
    .vip-ultra .fi-main-ctn,
    .vip-ultra .fi-page,
    .vip-ultra .fi-page-header,
    .vip-ultra .fi-page-body {
        max-width: 2600px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    /* 2) Paddings plus luxueux */
    .vip-ultra .fi-main,
    .vip-ultra .fi-page-body {
        padding-left: 2.25rem !important;
        padding-right: 2.25rem !important;
    }

    /* 3) Sidebar plus large (confort) */
    .vip-ultra .fi-sidebar {
        width: 320px !important;
    }

    /* 4) Topbar plus "premium" */
    .vip-ultra .fi-topbar {
        min-height: 64px !important;
    }

    /* 5) Dashboard widgets: 4 colonnes */
    .vip-ultra .fi-wi {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 1.25rem !important;
        align-items: start !important;
    }

    /* 6) Cartes / sections un peu plus respirantes */
    .vip-ultra .fi-section,
    .vip-ultra .fi-ta-content,
    .vip-ultra .fi-wi-widget {
        border-radius: 18px !important;
    }

    /* 7) Tables : plus lisible */
    .vip-ultra .fi-ta {
        font-size: 1rem !important;
    }
    .vip-ultra .fi-ta th,
    .vip-ultra .fi-ta td {
        padding-top: .9rem !important;
        padding-bottom: .9rem !important;
    }

    /* 8) Petites touches “premium” */
    .vip-ultra .fi-topbar,
    .vip-ultra .fi-sidebar-header {
        backdrop-filter: blur(10px);
    }
}

/* Bouton toggle ultrawide (topbar) */
.vip-ultra-toggle {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .35rem .6rem;
    border-radius: 999px;
    border: 1px solid var(--vip-border);
    background: rgba(255,255,255,.04);
    color: rgba(255,255,255,.9);
    font-size: .85rem;
    line-height: 1;
}
.vip-ultra-toggle:hover { border-color: rgba(201,160,80,.55); }
.vip-ultra-toggle .dot {
    width: 8px; height: 8px; border-radius: 999px;
    background: rgba(201,160,80,.95);
    box-shadow: 0 0 0 4px rgba(201,160,80,.15);
}
.vip-ultra-toggle.off .dot {
    background: rgba(148,163,184,.85);
    box-shadow: 0 0 0 4px rgba(148,163,184,.12);
}
/* Header actions: autorise 2 rangées */
.fi-page-header header.fi-header .flex.shrink-0.items-center.gap-3{
    flex-wrap: wrap !important;
    justify-content: flex-end;
}

/* évite que ça prenne toute la largeur sur mobile */
@media (max-width: 900px){
    .fi-page-header header.fi-header .flex.shrink-0.items-center.gap-3{
        justify-content: flex-start;
    }
}

/* =============================================================================
   SECTIONS — Bordure gauche dorée sur les headers de section
   ============================================================================= */
.fi-section-header{
    border-left: 3px solid var(--vip-accent) !important;
    padding-left: .85rem !important;
    border-radius: 0 !important;
}
.fi-section-header-heading{
    font-weight: 700 !important;
    letter-spacing: -.015em !important;
    font-size: .92rem !important;
}

/* =============================================================================
   TABLES — Alternance douce des lignes (zebra)
   ============================================================================= */
.fi-ta-row:nth-child(even){
    background: rgba(148,163,184,.045) !important;
}
.dark .fi-ta-row:nth-child(even){
    background: rgba(255,255,255,.024) !important;
}

/* =============================================================================
   BADGES — Plus arrondis, padding harmonisé, fond plus doux
   ============================================================================= */
.fi-badge{
    border-radius: 999px !important;
    padding: .2rem .65rem !important;
    font-weight: 600 !important;
    font-size: .72rem !important;
    letter-spacing: .01em !important;
}

/* =============================================================================
   FORMULAIRES — Meilleur style des labels
   ============================================================================= */
.fi-fo-field-wrp > label,
.fi-fo-field-wrp .fi-fo-field-wrp-label{
    font-weight: 600 !important;
    font-size: .82rem !important;
    letter-spacing: .005em !important;
    opacity: .92;
}

/* =============================================================================
   MODAL — Header avec dégradé doré
   ============================================================================= */
.fi-modal-header,
.fi-dialog-header{
    background: linear-gradient(135deg, rgba(201,160,80,.12) 0%, rgba(201,160,80,.04) 100%) !important;
    border-bottom: 1px solid rgba(201,160,80,.25) !important;
    padding: 1rem 1.25rem !important;
}
.fi-modal-header-heading,
.fi-dialog-header-heading{
    font-weight: 700 !important;
    font-size: 1.05rem !important;
    color: var(--vip-accent) !important;
}

/* =============================================================================
   SCROLLBAR — Fine et dorée (webkit)
   ============================================================================= */
::-webkit-scrollbar{
    width: 5px;
    height: 5px;
}
::-webkit-scrollbar-track{
    background: transparent;
}
::-webkit-scrollbar-thumb{
    background: rgba(201,160,80,.45);
    border-radius: 999px;
}
::-webkit-scrollbar-thumb:hover{
    background: rgba(201,160,80,.70);
}

/* =============================================================================
   WIDGETS DASHBOARD — Légère ombre au hover
   NOTE: pas de transform ici — transform crée un stacking context qui
   interfère avec les overlays/modals de Filament (dialogs z-index).
   ============================================================================= */
.fi-wi-widget{
    transition: box-shadow .18s ease;
}
.fi-wi-widget:hover{
    box-shadow: 0 14px 32px rgba(0,0,0,.22) !important;
}

/* =============================================================================
   DARK MODE — Harmonisation couleurs
   ============================================================================= */
.dark .fi-section-header{
    border-left-color: rgba(201,160,80,.7) !important;
}
.dark .fi-fo-field-wrp > label,
.dark .fi-fo-field-wrp .fi-fo-field-wrp-label{
    color: rgba(255,255,255,.80) !important;
}
.dark .fi-modal-header,
.dark .fi-dialog-header{
    background: linear-gradient(135deg, rgba(201,160,80,.09) 0%, rgba(201,160,80,.03) 100%) !important;
}

/* =============================================================================
   RESPONSIVE MOBILE
   ============================================================================= */
@media (max-width: 767px){

    /* Réduction des paddings principaux */
    .fi-main,
    .fi-page-body{
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }

    /* Prévenir le débordement horizontal */
    .fi-page,
    .fi-page-header,
    .fi-page-body,
    .fi-main,
    .fi-main-ctn,
    .fi-container,
    .fi-section,
    .fi-card,
    .fi-ta-ctn,
    .fi-fo-component-ctn{
        min-width: 0 !important;
        overflow-x: hidden !important;
    }

    /* Colonnes de formulaire en une seule colonne sur mobile */
    .fi-fo-grid-ctn{
        grid-template-columns: 1fr !important;
    }

    /* Tables: texte plus petit et cellules compactes */
    .fi-ta-header-cell,
    .fi-ta-cell{
        font-size: 0.73rem !important;
        padding-left: 0.35rem !important;
        padding-right: 0.35rem !important;
    }

    /* Topbar: réduire le padding */
    .fi-topbar{
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }

    /* Sidebar: s'assurer qu'elle couvre tout sur mobile */
    .fi-sidebar{
        z-index: 50 !important;
    }
}
</style>
HTML;
    }

    private function globalScripts(): string
    {
        $docsUrl = url('/espace-conseiller/wiki');      // Wiki Filament
        try {
            $supportUrl = app(\App\Settings\EmailSettings::class)->support_url;
        } catch (\Throwable) {
            $supportUrl = 'mailto:' . config('mail.from.address', 'support@vipgpi.ca');
        }
        $build = $this->resolveBuildVersion();         // build auto

        return <<<HTML
<script>
/**
 * VIPGPI – JS
 * 1) Persistance des groupes de navigation (multi-ouvert)
 * 2) Footer sidebar (version + docs + support)
 */
(function () {
    const STORAGE_KEY = 'vipgpi_nav_groups_v3';

    const DOCS_URL = "{$docsUrl}";
    const SUPPORT_URL = "{$supportUrl}";
    const BUILD_LABEL = "{$build}";

    function getState() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}'); }
        catch { return {}; }
    }

    function setState(state) {
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(state)); }
        catch {}
    }

    function findGroupToggles() {
        const buttons = Array.from(document.querySelectorAll('.fi-sidebar button[aria-expanded]'));
        return buttons.filter(btn => !!btn.closest('.fi-sidebar-group'));
    }

    function getGroupKey(btn) {
        const group = btn.closest('.fi-sidebar-group');
        if (!group) return null;

        const labelEl = group.querySelector('.fi-sidebar-group-label');
        const label = (labelEl?.textContent || btn.textContent || '').trim();
        if (label) return label;

        const allGroups = Array.from(document.querySelectorAll('.fi-sidebar .fi-sidebar-group'));
        return 'group_' + allGroups.indexOf(group);
    }

    function applySavedState() {
        const state = getState();
        const toggles = findGroupToggles();

        toggles.forEach(btn => {
            const key = getGroupKey(btn);
            if (!key || state[key] === undefined) return;

            const expanded = btn.getAttribute('aria-expanded') === 'true';
            const shouldBeOpen = !!state[key];
            if (expanded !== shouldBeOpen) btn.click();
        });
    }

    function bindSaveOnClick() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.fi-sidebar button[aria-expanded]');
            if (!btn) return;

            const key = getGroupKey(btn);
            if (!key) return;

            const open = btn.getAttribute('aria-expanded') === 'true';
            const state = getState();
            state[key] = open;
            setState(state);
        });
    }

    function injectSidebarFooter() {
        const sidebarNav = document.querySelector('.fi-sidebar .fi-sidebar-nav');
        if (!sidebarNav) return;

        if (sidebarNav.querySelector('.vip-sidebar-footer')) return;

        const footer = document.createElement('div');
        footer.className = 'vip-sidebar-footer';
        footer.innerHTML =
            '<div class="vip-row">' +
                '<span class="vip-pill"><span class="vip-dot"></span>En ligne</span>' +
                '<span style="opacity:.85">' + BUILD_LABEL + '</span>' +
            '</div>' +
            '<div class="vip-row">' +
                '<a href="' + SUPPORT_URL + '" title="Support">Support</a>' +
            '</div>';

        sidebarNav.appendChild(footer);
    }

        // ===========================
    // ULTRAWIDE PREMIUM MODE
    // ===========================
    const ULTRA_STORAGE_KEY = 'vipgpi_ultra_mode_v1'; // 'on' | 'off' | null
    const ULTRA_MIN_WIDTH = 3000; // ajuste si tu veux (ex 2560)

    function getUltraPref() {
        try { return localStorage.getItem(ULTRA_STORAGE_KEY); }
        catch { return null; }
    }

    function setUltraPref(val) {
        try { localStorage.setItem(ULTRA_STORAGE_KEY, val); } catch {}
    }

    function isUltraScreen() {
        return window.matchMedia('(min-width: 3000px)').matches;
    }

    function applyUltraClass(force) {
        const pref = getUltraPref(); // 'on' | 'off' | null
        const shouldAuto = isUltraScreen();

        let enable = false;

        if (force === true) enable = true;
        else if (force === false) enable = false;
        else if (pref === 'on') enable = true;
        else if (pref === 'off') enable = false;
        else enable = shouldAuto;

        document.documentElement.classList.toggle('vip-ultra', enable);

        // update bouton si présent
        const btn = document.querySelector('[data-vip-ultra-toggle]');
        if (btn) {
            btn.classList.toggle('off', !enable);
            btn.setAttribute('aria-pressed', enable ? 'true' : 'false');
        }
    }

    function bindUltraToggle() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-vip-ultra-toggle]');
            if (!btn) return;

            const enabled = document.documentElement.classList.contains('vip-ultra');
            const next = !enabled;

            setUltraPref(next ? 'on' : 'off');
            applyUltraClass(next);
        });

        // Raccourci clavier: Alt + U
        document.addEventListener('keydown', (e) => {
            if (!e.altKey) return;
            if ((e.key || '').toLowerCase() !== 'u') return;

            const enabled = document.documentElement.classList.contains('vip-ultra');
            const next = !enabled;
            setUltraPref(next ? 'on' : 'off');
            applyUltraClass(next);
        });

        // Au resize, on réapplique seulement si pas de préférence explicite
        window.addEventListener('resize', () => {
            const pref = getUltraPref();
            if (pref === 'on' || pref === 'off') return;
            applyUltraClass();
        });
    }

    function init() {
        bindUltraToggle();
        applyUltraClass();
        bindSaveOnClick();
        setTimeout(() => {
            applySavedState();
            injectSidebarFooter();
        }, 200);
    }

    document.addEventListener('DOMContentLoaded', init);
    document.addEventListener('livewire:initialized', () => setTimeout(() => { applySavedState(); injectSidebarFooter(); }, 150));
    document.addEventListener('livewire:navigated', () => setTimeout(() => { applySavedState(); injectSidebarFooter(); }, 150));
})();
</script>
HTML;
    }


    private function resolveBuildVersion(): string
    {
        // 1) Essaie Git sans exec (lecture .git/HEAD)
        $gitHead = base_path('.git/HEAD');
        if (is_file($gitHead)) {
            $head = trim((string) @file_get_contents($gitHead));

            // HEAD: ref: refs/heads/main
            if (str_starts_with($head, 'ref: ')) {
                $refPath = base_path('.git/' . trim(substr($head, 5)));
                if (is_file($refPath)) {
                    $hash = trim((string) @file_get_contents($refPath));
                    if ($hash !== '') {
                        return 'build ' . substr($hash, 0, 7);
                    }
                }
            }

            // HEAD directement un hash (detached)
            if (preg_match('/^[0-9a-f]{40}$/i', $head)) {
                return 'build ' . substr($head, 0, 7);
            }
        }

        // 2) Fallback: timestamp d’un fichier “déploiement” (change souvent)
        $fallbackFile = base_path('composer.lock');
        if (!is_file($fallbackFile)) {
            $fallbackFile = base_path('routes/web.php');
        }

        $ts = @filemtime($fallbackFile) ?: time();
        return 'build ' . date('Ymd-His', $ts);
    }
}
