<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\BlogPost;
use App\Models\Partner;
use App\Models\CareerPage;

class PageController extends Controller
{
    /**
     * Récupère 3 articles de blog liés à une liste de mots-clés.
     * Mis en cache 30 min par combinaison de mots-clés.
     */
    private function getConseils(array $keywords)
    {
        $cacheKey = 'conseils_' . md5(implode('|', $keywords));

        return Cache::remember($cacheKey, 1800, function () use ($keywords) {
            return BlogPost::where(function ($query) use ($keywords) {
                foreach ($keywords as $word) {
                    $query->orWhere('category->fr', 'LIKE', "%{$word}%");
                }
            })
                ->latest()
                ->take(3)
                ->get();
        });
    }

    // --- PAGES GÉNÉRALES ---

    public function about()
    {
        return view('pages.about', [
            'header_title'    => __('PageController.about.header_title'),
            'header_subtitle' => __('PageController.about.header_subtitle'),
            'header_bg'       => asset('assets/img/about/En-tete-A-propos-de-nous.jpg'),
        ]);
    }

    public function construction()
    {
        return view('pages.construction', [
            'header_title' => __('PageController.construction.header_title'),
            'header_bg'    => asset('assets/img/header/canvas.png'),
        ]);
    }

    public function partenaires()
    {
        $partners = Cache::remember('partners_visible', 3600, fn() =>
            Partner::where('is_visible', true)->orderBy('sort_order', 'asc')->get()
        );

        return view('pages.partenaires', [
            'header_title'    => __('PageController.partenaires.header_title'),
            'header_subtitle' => __('PageController.partenaires.header_subtitle'),
            'header_bg'       => asset('assets/img/header/canvas.png'),
            'partners'        => $partners,
        ]);
    }

    public function carrieres()
    {
        $careerSettings = Cache::remember('career_page_settings', 3600, fn() => CareerPage::first());

        return view('pages.carrieres', [
            'header_title'    => __('carrieres.hero_title'),
            'header_subtitle' => __('carrieres.hero_subtitle'),
            'header_bg'       => asset('assets/img/carrieres/carrieres1.jpg'),
            'header_btn_text' => __('carrieres.hero_cta'),
            'header_btn_link' => '#pipelines',
            'careerSettings'  => $careerSettings,
        ]);
    }

    // --- PAGE GESTION DE PATRIMOINE ---

    public function management()
    {
        return view('pages.management', [
            'header_title' => __('management.hero_title'),
            'header_bg'    => asset('assets/img/management/hero-image.jpg'),
            'conseils'     => $this->getConseils(['Patrimoine', 'Finance', 'Investissement', 'Planification']),
        ]);
    }
}
