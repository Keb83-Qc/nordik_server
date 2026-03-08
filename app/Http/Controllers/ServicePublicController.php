<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use App\Models\PublicService;
use App\Models\PublicServiceCategoryTranslation;
use App\Models\PublicServiceTranslation;

class ServicePublicController extends Controller
{
    // /{locale}/services (optionnel: page qui liste tout)
    public function index(string $locale)
    {
        return redirect()->route('services.categories', ['locale' => $locale]);
    }

    // /{locale}/{categorySlug} => liste des services d'une catégorie
    public function category(string $locale, string $categorySlug)
    {
        // Cache 1h — les catégories de services changent très rarement
        $data = Cache::remember("svc_cat_{$locale}_{$categorySlug}", 3600, function () use ($locale, $categorySlug) {
            $catTr = PublicServiceCategoryTranslation::query()
                ->where('locale', $locale)
                ->where('slug', $categorySlug)
                ->first();

            if (! $catTr) {
                return [];
            }

            $category = $catTr->category()
                ->where('is_active', 1)
                ->with([
                    'translations',
                    'services' => fn($q) => $q->where('is_active', 1)->orderBy('sort_order'),
                    'services.translations',
                ])
                ->first();

            return $category ? compact('catTr', 'category') : [];
        });

        abort_if(empty($data), 404);

        ['catTr' => $catTr, 'category' => $category] = $data;

        $categoryCode = $category->code;            // ex: pret
        $view = "services.$categoryCode.$categoryCode";

        abort_unless(View::exists($view), 404, "Vue introuvable: $view");

        return view($view, [
            'category' => $category,
            'catTr'    => $catTr,
            'services' => $category->services,
            'locale'   => $locale,
        ]);
    }

    // /{locale}/{categorySlug}/{serviceSlug} => affiche le service (blade)
    public function show(string $locale, string $categorySlug, string $serviceSlug)
    {
        // Cache 1h — les services changent très rarement
        $data = Cache::remember("svc_show_{$locale}_{$categorySlug}_{$serviceSlug}", 3600, function () use ($locale, $categorySlug, $serviceSlug) {
            $catTr = PublicServiceCategoryTranslation::query()
                ->where('locale', $locale)
                ->where('slug', $categorySlug)
                ->first();

            if (! $catTr) {
                return [];
            }

            $serviceTr = PublicServiceTranslation::query()
                ->where('locale', $locale)
                ->where('slug', $serviceSlug)
                ->first();

            if (! $serviceTr) {
                return [];
            }

            $service = PublicService::query()
                ->whereKey($serviceTr->public_service_id)
                ->where('public_service_category_id', $catTr->public_service_category_id)
                ->where('is_active', 1)
                ->with(['translations', 'category'])
                ->first();

            return $service ? compact('catTr', 'serviceTr', 'service') : [];
        });

        abort_if(empty($data), 404);

        ['catTr' => $catTr, 'serviceTr' => $serviceTr, 'service' => $service] = $data;

        // ✅ mapping vers blade: resources/views/services/{categoryCode}/{serviceCode}.blade.php
        $categoryCode = $service->category->code;   // ex: assurance
        $serviceCode  = $service->code;             // ex: assurance-vie

        $view = "services.$categoryCode.$serviceCode";

        abort_unless(View::exists($view), 404, "Vue introuvable: $view");

        return view($view, [
            'service'   => $service,
            'serviceTr' => $serviceTr,
            'catTr'     => $catTr,
            'locale'    => $locale,
        ]);
    }
}
