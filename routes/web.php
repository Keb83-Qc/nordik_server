<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\SystemLog;

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
use App\Http\Controllers\AbfPdfController;
use App\Http\Controllers\AbfEditorController;
use App\Http\Controllers\ServicePublicController;
use App\Models\Language;
use App\Http\Controllers\AccessRequestController;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

/**
 * ABF Editor — standalone page (hors Filament)
 */
Route::middleware(['auth'])->group(function () {
    Route::get('/conseiller/bilan', [AbfEditorController::class, 'landing'])->name('abf.landing');
    Route::post('/conseiller/bilan/nouveau', [AbfEditorController::class, 'createJson'])->name('abf.create.json');
    Route::get('/conseiller/bilan/creer', [AbfEditorController::class, 'create'])->name('abf.new');
    Route::post('/conseiller/bilan/parametres', [AbfEditorController::class, 'saveParams'])->name('abf.params.save');
    Route::get('/conseiller/bilan/{record}', [AbfEditorController::class, 'show'])->name('abf.editor.show')->where('record', '.*');
    Route::post('/conseiller/bilan/{record}/sauvegarder', [AbfEditorController::class, 'save'])->name('abf.editor.save')->where('record', '.*');
});

/**
 * Récepteur d'erreurs JavaScript — toutes les pages l'utilisent.
 * Protégé par CSRF + rate-limit (10 req/min/IP).
 */
Route::post('/log-js-error', function (Request $request) {
    $type    = Str::limit($request->input('type', 'js_error'), 50);
    $message = Str::limit($request->input('message', 'Unknown JS error'), 300);

    SystemLog::record('warning', "[JS] {$type}: {$message}", [
        'source'   => Str::limit($request->input('source', ''), 300),
        'line'     => $request->input('line', ''),
        'column'   => $request->input('column', ''),
        'stack'    => Str::limit($request->input('stack', ''), 600),
        'page_url' => Str::limit($request->input('url', ''), 300),
    ]);

    return response()->json(['ok' => true]);
})->middleware('throttle:10,1')->name('log-js-error');

/**
 * 1) Redirige / vers /{locale} (locale déterminée par middleware/service)
 *    NB: ici on met une route simple qui appelle une route localisée.
 */
Route::get('/', function (Request $request) {
    // 1) Si l'utilisateur a déjà choisi une langue, on respecte son choix
    if (session()->has('locale')) {
        return redirect('/' . session('locale'), 302);
    }

    // 2) Langues actives en DB
    $active = Language::activeCodes(); // ex: ['fr', 'en', 'es']

    // 3) Détection navigateur
    // Renvoie ex: ['fr_CA', 'fr', 'en_US', 'en']
    $preferred = $request->getLanguages();

    $picked = null;

    foreach ($preferred as $lang) {
        $lang = strtolower(str_replace('_', '-', $lang)); // ex: fr-ca

        // match exact (fr-ca)
        if (in_array($lang, $active, true)) {
            $picked = $lang;
            break;
        }

        // match base (fr)
        $base = explode('-', $lang)[0];
        if (in_array($base, $active, true)) {
            $picked = $base;
            break;
        }
    }

    // 4) fallback
    $picked ??= Language::defaultCode() ?? config('app.fallback_locale', 'fr');

    // Option : set session locale automatiquement
    if ($picked) {
        session(['locale' => $picked]);
        return redirect("/{$picked}", 302);
    }

    return redirect('/fr', 302); // ou return view('landing');
});

/**
 * 2) Switch langue (optionnel)
 *    Ici on garde simple: set session locale + redirect back/next
 */
Route::get('/switch-language/{locale}', function (Request $request, string $locale) {
    $active = Language::activeCodes();
    if (!in_array($locale, $active, true)) {
        $locale = Language::defaultCode();
    }

    session(['locale' => $locale, 'welcome_seen' => true]);
    session()->save();

    $next = $request->query('next');
    if (is_string($next) && $next !== '') {
        $next = '/' . ltrim($next, '/');
        $next = preg_replace('#^/[a-zA-Z]{2,5}(/|$)#', '/', $next);
        // Si next est juste "/", rediriger vers /home au lieu de la landing
        if ($next === '/') $next = '/home';
        return redirect("/{$locale}{$next}");
    }

    return redirect("/{$locale}/home");
})
    ->where(['locale' => '[a-zA-Z]{2,5}'])
    ->name('switch.language');

/**
 * 3) LEGACY: anciens liens sans locale → 301 vers version localisée
 *    IMPORTANT: destination doit être une string, donc on passe par Route::get + redirect()
 */
Route::middleware('set-locale')->group(function () {
    // pages simples
    // Route::get('/home', fn() => redirect('/' . app()->getLocale() . '/home', 301));
    Route::get('/about', fn() => redirect('/' . app()->getLocale() . '/about', 301));
    Route::get('/management', fn() => redirect('/' . app()->getLocale() . '/management', 301));
    Route::get('/equipe', fn() => redirect('/' . app()->getLocale() . '/equipe', 301));
    Route::get('/evenements', fn() => redirect('/' . app()->getLocale() . '/evenements', 301));
    Route::get('/blog', fn() => redirect('/' . app()->getLocale() . '/blog', 301));
    Route::get('/contact', fn() => redirect('/' . app()->getLocale() . '/contact', 301));
    Route::get('/login', fn() => redirect('/' . app()->getLocale() . '/login', 301));
    Route::get('/partenaires', fn() => redirect('/' . app()->getLocale() . '/partenaires', 301));
    Route::get('/carrieres', fn() => redirect('/' . app()->getLocale() . '/carrieres', 301));
    Route::get('/construction', fn() => redirect('/' . app()->getLocale() . '/construction', 301));

    // dynamiques legacy
    Route::get(
        '/conseiller/{slug}',
        fn($slug) => redirect('/' . app()->getLocale() . "/conseiller/{$slug}", 301),
    );
    Route::get('/article/{slug}', function (string $slug) {
        $locale = session('locale') ?? (\App\Models\Language::defaultCode() ?? 'fr');
        return redirect("/{$locale}/article/{$slug}", 301);
    });

    Route::get(
        '/consentement/{code?}',
        fn($code = null) => redirect(
            '/' . app()->getLocale() . '/consentement/' . ($code ?? ''),
            301,
        ),
    );
});

/**
 * 4) SITE LOCALISÉ: TOUTES les pages vivent ici et “fonctionnent pareil”
 */
Route::prefix('{locale}')
    ->where(['locale' => '[a-zA-Z]{2,5}'])
    ->middleware(['set-locale'])
    ->group(function () {
        Route::get('/admin', fn(string $locale) => redirect('/admin', 302));
        Route::get('/conseiller', fn(string $locale) => redirect('/conseiller', 302));
        Route::get('/abf', fn(string $locale) => redirect('/abf', 302));

        Route::get('/admin', fn() => redirect('/admin'));
        Route::get('/conseiller', fn() => redirect('/conseiller'));
        Route::get('/abf', fn() => redirect('/abf'));

        Route::get('/demande-acces', function (string $locale) {
            return redirect()->route('login', [
                'locale' => $locale,
                'register' => 1,
            ]);
        })->name('access.request');

        // Landing / accueil
        Route::get('/', [WelcomeController::class, 'index'])->name('landing');
        Route::get('/home', [HomeController::class, 'index'])->name('home');

        // Pages
        Route::get('/about', [PageController::class, 'about'])->name('about');
        Route::get('/management', [PageController::class, 'management'])->name('management');

        // Équipe
        Route::get('/equipe', [TeamController::class, 'index'])->name('equipe');
        Route::get('/conseiller/{slug}', [TeamController::class, 'show'])
            ->where(['slug' => '[a-z0-9\-]+'])
            ->name('team.show');

        // Événements
        Route::get('/evenements', [EventController::class, 'index'])->name('evenements');

        // Blog
        Route::get('/blog', [BlogController::class, 'index'])->name('blog');
        Route::get('/article/{post}', [BlogController::class, 'show'])
            ->where(['post' => '[a-z0-9\-]+'])
            ->name('blog.show');

        // Contact
        Route::get('/contact', [ContactController::class, 'index'])->name('contact');
        Route::post('/contact', [ContactController::class, 'send'])
            ->middleware(['throttle:10,1'])
            ->name('contact.send');

        // Auth
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware(['throttle:5,1'])
            ->name('login.post');
        Route::post('/register-ajax', [AuthController::class, 'registerAjax'])
            ->middleware(['throttle:10,1'])
            ->name('register.ajax');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Mot de passe oublié
        Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])
            ->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
            ->middleware(['throttle:5,1'])
            ->name('password.email');
        Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])
            ->name('password.reset');
        Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])
            ->middleware(['throttle:5,1'])
            ->name('password.store');

        // Partenaires / carrières / construction
        Route::get('/partenaires', [PageController::class, 'partenaires'])->name('partenaires');
        Route::get('/carrieres', [PageController::class, 'carrieres'])->name('carrieres');
        Route::get('/construction', [PageController::class, 'construction'])->name('construction');

        // Consentement
        Route::get('/consentement/language/{code?}', [
            ConsentController::class,
            'switchLanguage',
        ])->name('consent.language');
        Route::get('/consentement/{code?}', [ConsentController::class, 'show'])->name(
            'consent.show',
        );
        Route::post('/consentement/accept', [ConsentController::class, 'accept'])->name(
            'consent.accept',
        );

        Route::middleware(['throttle:60,1'])->group(function () {
            Route::get('/quote/auto', function () {
                abort_unless(session('has_consented') === true, 403);
                return view('quote.auto');
            })->name('quote.auto');

            Route::get('/quote/habitation', function () {
                abort_unless(session('has_consented') === true, 403);
                return view('quote.home');
            })->name('quote.habitation');

            Route::get('/quote/bundle', function () {
                abort_unless(session('has_consented') === true, 403);
                return view('quote.bundle');
            })->name('quote.bundle');
        });

        Route::get('/quote/success', function () {
            return view('quote.success');
        })->name('quote.success');

        // ABF PDF
        Route::middleware(['auth'])
            ->get('/abf/{abfCase}/pdf', [AbfPdfController::class, 'generate'])
            ->name('abf.pdf');

        // Services dynamiques (TOUJOURS en dernier)
        Route::get('/{categorySlug}', [ServicePublicController::class, 'category'])
            ->where(['categorySlug' => '[a-z0-9\-]+'])
            ->name('services.category');

        Route::get('/{categorySlug}/{serviceSlug}', [ServicePublicController::class, 'show'])
            ->where([
                'categorySlug' => '[a-z0-9\-]+',
                'serviceSlug' => '[a-z0-9\-]+',
            ])
            ->name('services.show');
    });
