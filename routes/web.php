<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ConsentController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\AbfPdfController;
use App\Http\Controllers\AbfEditorController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\ServicePublicController;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\DiagnosticsController;

// ─── Statique ────────────────────────────────────────────────────────────────
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// ─── Diagnostics front-end ───────────────────────────────────────────────────
Route::post('/log-js-error',   [DiagnosticsController::class, 'logJsError'])->middleware('throttle:10,1')->name('log-js-error');
Route::post('/log-web-vitals', [DiagnosticsController::class, 'logWebVitals'])->middleware('throttle:30,1')->name('log-web-vitals');

// ─── 2FA ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('2fa')->name('2fa.')->group(function () {
    Route::get('/setup',    [TwoFactorController::class, 'setup'])->name('setup');
    Route::post('/enable',  [TwoFactorController::class, 'enable'])->name('enable')->middleware('throttle:10,1');
    Route::get('/verify',   [TwoFactorController::class, 'verify'])->name('verify');
    Route::post('/check',   [TwoFactorController::class, 'check'])->name('check')->middleware('throttle:10,1');
    Route::post('/disable', [TwoFactorController::class, 'disable'])->name('disable');
});

// ─── Raccourci /abf → ABF personnel du conseiller connecté ──────────────────
Route::middleware(['auth', '2fa'])->get('/abf', function () {
    $slug = auth()->user()->slug ?? 'conseiller';
    return redirect("/{$slug}/liste-bilan");
})->name('abf.redirect');

// ─── ABF Editor ({prenom-nom}/liste-bilan) ──────────────────────────────────
Route::middleware(['auth', '2fa'])->prefix('{advisorSlug}/liste-bilan')->group(function () {
    Route::get('/',                              [AbfEditorController::class, 'landing'])->name('abf.landing');
    Route::post('/nouveau',                      [AbfEditorController::class, 'createJson'])->name('abf.create.json');
    Route::get('/creer',                         [AbfEditorController::class, 'create'])->name('abf.new');
    Route::post('/parametres',                   [AbfEditorController::class, 'saveParams'])->name('abf.params.save');
    Route::post('/profil',                       [AbfEditorController::class, 'saveProfil'])->name('abf.profil.save');
    Route::post('/nouveautes/{id}/vu',           [AbfEditorController::class, 'markAnnouncementSeen'])->name('abf.announcement.seen');
    Route::get('/{record}',                      [AbfEditorController::class, 'show'])->name('abf.editor.show')->where('record', '.*');
    Route::post('/{record}/sauvegarder',         [AbfEditorController::class, 'save'])->name('abf.editor.save')->where('record', '.*');
});

// ─── Intake client (public, sans auth) ───────────────────────────────────────
Route::prefix('{advisorSlug}/intake')->name('intake.')->group(function () {
    Route::get('/{token}',         [IntakeController::class, 'show'])->name('show');
    Route::post('/{token}/verify', [IntakeController::class, 'verify'])->name('verify');
    Route::get('/{token}/merci',   [IntakeController::class, 'merci'])->name('merci');
});

Route::middleware(['auth', '2fa'])
    ->post('/{advisorSlug}/intake/create', [IntakeController::class, 'create'])
    ->name('intake.create');

// ─── Racine & langue ────────────────────────────────────────────────────────
Route::get('/', [WelcomeController::class, 'root'])->name('root');

Route::get('/switch-language/{locale}', [WelcomeController::class, 'switchLanguage'])
    ->where(['locale' => '[a-zA-Z]{2,5}'])
    ->name('switch.language');

// ─── Site localisé /{locale}/... ─────────────────────────────────────────────
Route::prefix('{locale}')
    ->where(['locale' => '[a-zA-Z]{2,5}'])
    ->middleware(['set-locale'])
    ->group(function () {
        // Raccourcis vers zones hors locale
        Route::get('/espace-conseiller', fn () => redirect('/espace-conseiller', 302));
        Route::get('/admin',             fn () => redirect('/espace-conseiller', 302));
        Route::get('/conseiller',        fn () => redirect('/espace-conseiller', 302));
        Route::get('/abf',               fn () => redirect('/abf', 302));

        Route::get('/demande-acces', function (string $locale) {
            return redirect()->route('login', [
                'locale'   => $locale,
                'register' => 1,
            ]);
        })->name('access.request');

        // Landing / accueil
        Route::get('/',     [WelcomeController::class, 'index'])->name('landing');
        Route::get('/home', [HomeController::class, 'index'])->name('home');

        // Pages
        Route::get('/about',      [PageController::class, 'about'])->name('about');
        Route::get('/management', [PageController::class, 'management'])->name('management');

        // Equipe
        Route::get('/equipe', [TeamController::class, 'index'])->name('equipe');
        Route::get('/conseiller/{slug}', [TeamController::class, 'show'])
            ->where(['slug' => '[a-z0-9\-]+'])
            ->name('team.show');

        // Evenements
        Route::get('/evenements', [EventController::class, 'index'])->name('evenements');

        // Blog
        Route::get('/blog', [BlogController::class, 'index'])->name('blog');
        Route::get('/article/{post}', [BlogController::class, 'show'])
            ->where(['post' => '[a-z0-9\-]+'])
            ->name('blog.show');

        // Pages legales
        Route::get('/politique-confidentialite', [LegalController::class, 'privacy'])->name('legal.privacy');
        Route::get('/termes-conditions',         [LegalController::class, 'terms'])->name('legal.terms');

        // Contact
        Route::get('/contact',  [ContactController::class, 'index'])->name('contact');
        Route::post('/contact', [ContactController::class, 'send'])
            ->middleware(['throttle:10,1'])
            ->name('contact.send');

        // Auth
        Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware(['throttle:5,1'])
            ->name('login.post');
        Route::post('/register-ajax', [AuthController::class, 'registerAjax'])
            ->middleware(['throttle:10,1'])
            ->name('register.ajax');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Mot de passe oublie
        Route::get('/forgot-password',          [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
        Route::post('/forgot-password',         [ForgotPasswordController::class, 'sendResetLink'])->middleware(['throttle:5,1'])->name('password.email');
        Route::get('/reset-password/{token}',   [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password',          [ForgotPasswordController::class, 'resetPassword'])->middleware(['throttle:5,1'])->name('password.store');

        // Partenaires / carrieres / construction
        Route::get('/partenaires',  [PageController::class, 'partenaires'])->name('partenaires');
        Route::get('/carrieres',    [PageController::class, 'carrieres'])->name('carrieres');
        Route::get('/construction', [PageController::class, 'construction'])->name('construction');

        // Consentement
        Route::get('/consentement/language/{code?}', [ConsentController::class, 'switchLanguage'])->name('consent.language');
        Route::get('/consentement/{code?}',          [ConsentController::class, 'show'])->name('consent.show');
        Route::post('/consentement/accept',          [ConsentController::class, 'accept'])->name('consent.accept');

        // Soumissions (quote) — success AVANT le wildcard {typeSlug}
        Route::get('/quote/success', [QuoteController::class, 'success'])->name('quote.success');

        Route::middleware(['throttle:60,1'])->group(function () {
            Route::get('/quote/{typeSlug}', [QuoteController::class, 'chat'])->name('quote.chat');
        });

        // Portails partenaires
        Route::get('/p/{portalSlug}/quote',          [PortalController::class, 'consent'])->name('portal.consent');
        Route::post('/p/{portalSlug}/quote/accept',   [PortalController::class, 'accept'])->name('portal.accept');

        Route::middleware(['throttle:60,1'])->group(function () {
            Route::get('/p/{portalSlug}/quote/{typeSlug}', [PortalController::class, 'chat'])->name('portal.quote.chat');
        });

        // ABF PDF
        Route::middleware(['auth'])
            ->get('/abf/{abfCase}/pdf', [AbfPdfController::class, 'generate'])
            ->name('abf.pdf');

        // Services dynamiques (TOUJOURS en dernier — wildcard catch-all)
        Route::get('/{categorySlug}', [ServicePublicController::class, 'category'])
            ->where(['categorySlug' => '[a-z0-9\-]+'])
            ->name('services.category');

        Route::get('/{categorySlug}/{serviceSlug}', [ServicePublicController::class, 'show'])
            ->where([
                'categorySlug' => '[a-z0-9\-]+',
                'serviceSlug'  => '[a-z0-9\-]+',
            ])
            ->name('services.show');
    });
