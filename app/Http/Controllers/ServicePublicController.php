<?php

namespace App\Http\Controllers;

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

    // /{locale}/{categorySlug} => liste des services d’une catégorie
    public function category(string $locale, string $categorySlug)
    {
        $catTr = PublicServiceCategoryTranslation::query()
            ->where('locale', $locale)
            ->where('slug', $categorySlug)
            ->firstOrFail();

        $category = $catTr->category()
            ->where('is_active', 1)
            ->with([
                'translations',
                'services' => fn($q) => $q->where('is_active', 1)->orderBy('sort_order'),
                'services.translations',
            ])
            ->firstOrFail();

        // pages catégories déjà présentes: resources/views/services/{code}/{code}.blade.php
        $categoryCode = $category->code;            // ex: pret
        $view = "services.$categoryCode.$categoryCode";

        abort_unless(\Illuminate\Support\Facades\View::exists($view), 404, "Vue introuvable: $view");

        return view($view, [
            'category' => $category,
            'catTr' => $catTr,
            'services' => $category->services,
            'locale' => $locale,
        ]);
    }

    // /{locale}/{categorySlug}/{serviceSlug} => affiche le service (blade)
    public function show(string $locale, string $categorySlug, string $serviceSlug)
    {
        $catTr = PublicServiceCategoryTranslation::query()
            ->where('locale', $locale)
            ->where('slug', $categorySlug)
            ->firstOrFail();

        $serviceTr = PublicServiceTranslation::query()
            ->where('locale', $locale)
            ->where('slug', $serviceSlug)
            ->firstOrFail();

        $service = PublicService::query()
            ->whereKey($serviceTr->public_service_id)
            ->where('public_service_category_id', $catTr->public_service_category_id)
            ->where('is_active', 1)
            ->with(['translations', 'category'])
            ->firstOrFail();

        // ✅ mapping vers blade: resources/views/services/{categoryCode}/{serviceCode}.blade.php
        $categoryCode = $service->category->code;   // ex: assurance
        $serviceCode  = $service->code;             // ex: assurance-vie

        $view = "services.$categoryCode.$serviceCode";

        abort_unless(View::exists($view), 404, "Vue introuvable: $view");

        return view($view, [
            'service' => $service,
            'serviceTr' => $serviceTr,
            'catTr' => $catTr,
            'locale' => $locale,
        ]);
    }
}
