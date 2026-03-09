<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use App\Models\PublicServiceCategory;
use Illuminate\Support\Facades\Cache;
use App\Settings\MailSettings;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        Paginator::useBootstrapFive();

        // Empêche les erreurs de migration sur certaines versions MySQL
        Schema::defaultStringLength(191);

        // MailSettings: chargé en lazy via config() + callback
        // Avant, ça faisait une requête DB (Spatie Settings) à CHAQUE requête,
        // même pour afficher une simple page. Maintenant, ça ne charge que
        // quand on accède réellement à mail.submission_broker_to.
        $this->app->booted(function () {
            try {
                /** @var MailSettings $mail */
                $mail = app(MailSettings::class);

                $recipient = $mail->submission_to;

                if ($mail->test_mode && filled($mail->test_to)) {
                    $recipient = $mail->test_to;
                }

                if (! empty($recipient)) {
                    config(['mail.submission_broker_to' => $recipient]);
                }
            } catch (\Throwable $e) {
                $fallback = config('mail.from.address');
                if (! empty($fallback)) {
                    config(['mail.submission_broker_to' => $fallback]);
                }
            }
        });

        // =========================
        // SETTINGS (mis en cache 30 min)
        // Production: on sait que la table existe — Schema::hasTable() faisait
        // une requête DB à chaque boot, éliminée ici.
        // =========================
        try {
            $settingsData = Cache::remember('app_settings', 1800, function () {
                return DB::table('settings')
                    ->pluck('setting_value', 'setting_key')
                    ->toArray();
            });

            View::share('settings', $settingsData);
        } catch (\Exception $e) {
            View::share('settings', []);
        }

        // ✅ Injecte les catégories/services (slugs + titres par langue) dans le menu
        try {
            View::composer('partials.menu', function ($view) {
                $locale = app()->getLocale();

                $menuServices = Cache::remember("menu_services_$locale", 1800, function () use ($locale) {
                    return PublicServiceCategory::query()
                        ->where('is_active', 1)
                        ->orderBy('sort_order')
                        ->with([
                            'translations' => fn($q) => $q->where('locale', $locale),
                            'services' => fn($q) => $q->where('is_active', 1)->orderBy('sort_order'),
                            'services.translations' => fn($q) => $q->where('locale', $locale),
                        ])
                        ->get()
                        ->map(function ($cat) {
                            $catTr = $cat->translations->first();

                            return [
                                // code sert à l’id HTML des tabs (stable)
                                'code' => $cat->code,
                                // label visible dans le menu
                                'name' => $catTr?->name ?? $cat->code,
                                // slug utilisé dans l’URL (par langue)
                                'slug' => $catTr?->slug ?? $cat->code,
                                'services' => $cat->services->map(function ($srv) {
                                    $srvTr = $srv->translations->first();

                                    return [
                                        'title' => $srvTr?->title ?? $srv->code,
                                        'slug'  => $srvTr?->slug ?? $srv->code,
                                    ];
                                })->values()->all(),
                            ];
                        })
                        ->values()
                        ->all();
                });

                $view->with('menuServices', $menuServices);
            });
        } catch (\Throwable $e) {
            // Fallback silencieux (si DB pas prête)
            View::composer('partials.menu', fn($view) => $view->with('menuServices', []));
        }
    }
}
