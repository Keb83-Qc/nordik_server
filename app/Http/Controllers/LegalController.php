<?php

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function privacy()
    {
        return view('pages.legal.privacy', [
            'header_title'    => __('legal.privacy_title'),
            'header_bg'       => asset('assets/img/header/canvas.png'),
            'seo_title'       => __('legal.privacy_title'),
            'seo_description' => __('legal.privacy_description'),
            'seo_robots'      => 'noindex, follow',
        ]);
    }

    public function terms()
    {
        return view('pages.legal.terms', [
            'header_title'    => __('legal.terms_title'),
            'header_bg'       => asset('assets/img/header/canvas.png'),
            'seo_title'       => __('legal.terms_title'),
            'seo_description' => __('legal.terms_description'),
            'seo_robots'      => 'noindex, follow',
        ]);
    }
}
