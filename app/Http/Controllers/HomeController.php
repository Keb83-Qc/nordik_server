<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Slide;
use App\Models\Service;
use App\Models\HomepageStat;
use App\Models\BlogPost;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        if (!session()->has('welcome_seen')) {
            return redirect()->route('landing');
        }

        $currentLocale = app()->getLocale();

        $homeData = Cache::remember("home_data_{$currentLocale}", 1800, function () {
            return [
                'testimonials' => Testimonial::orderBy('created_at', 'desc')->get(),
                'slides'       => Slide::where('is_active', true)->orderBy('sort_order')->get(),
                'services'     => Service::orderBy('sort_order')->get(),
                'stats'        => HomepageStat::orderBy('sort_order')->get(),
                'posts'        => BlogPost::latest()->take(3)->get(),
            ];
        });

        return view('home', array_merge($homeData, [
            'seo_title'       => __('seo.home_title'),
            'seo_description' => __('seo.default_description'),
            'og_type'         => 'website',
        ]));
    }
}
