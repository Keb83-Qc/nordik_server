@php
    $locale = app()->getLocale();
    $siteName = 'VIP GPI Services Financiers';
    $appUrl = rtrim(config('app.url'), '/');

    // Titre : priorité à $seo_title, sinon $header_title nettoyé, sinon nom du site
    $rawTitle = $seo_title ?? (isset($header_title) ? strip_tags((string) $header_title) : null);
    $pageTitle = $rawTitle ? $rawTitle . ' | ' . $siteName : $siteName;

    // Description : priorité à $seo_description, sinon $header_subtitle, sinon défaut
    $pageDesc =
        $seo_description ??
        ((isset($header_subtitle) ? Str::limit(strip_tags((string) $header_subtitle), 155) : null) ??
            __('seo.default_description', [], $locale));

    // Image OG : priorité à $seo_image, sinon image de fond, sinon logo
    $ogImage = $seo_image ?? ($header_bg ?? null ?? asset('assets/img/header/VIP_Logo_Gold_Gradient10.png'));

    // URL canonique : $seo_canonical ou URL courante sans query string
    $canonical = $seo_canonical ?? url()->current();

    // Locales supportées pour hreflang — lues depuis la table languages (avec cache 1h)
    $supportedLocales = \App\Models\Language::activeCodes();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ── Titre de la page ─────────────────────────────────────────── --}}
    <title>{{ $pageTitle }}</title>

    {{-- ── SEO de base ──────────────────────────────────────────────── --}}
    <meta name="description" content="{{ $pageDesc }}">
    <meta name="robots" content="{{ $seo_robots ?? 'index, follow' }}">
    <link rel="canonical" href="{{ $canonical }}">

    {{-- ── Open Graph (Facebook / LinkedIn) ────────────────────────── --}}
    <meta property="og:type" content="{{ $og_type ?? 'website' }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:title" content="{{ $rawTitle ?? $siteName }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="{{ $locale }}_CA">

    {{-- ── Twitter Card ─────────────────────────────────────────────── --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $rawTitle ?? $siteName }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    {{-- ── hreflang multilingue ────────────────────────────────────── --}}
    @foreach ($supportedLocales as $lang)
        @php
            try {
                $hrefUrl = preg_replace(
                    '#^(https?://[^/]+)/[a-z]{2}(/)#',
                    '$1/' . $lang . '$2',
                    preg_replace('#^(https?://[^/]+)$#', '$1/' . $lang, $canonical),
                );
            } catch (\Throwable $e) {
                $hrefUrl = $canonical;
            }
        @endphp
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ $hrefUrl }}">
    @endforeach
    <link rel="alternate" hreflang="x-default"
        href="{{ preg_replace('#^(https?://[^/]+)/[a-z]{2}(/|$)#', '$1/fr$2', $canonical) }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/header/favicon.ico') }}">

    {{-- Préconnexions CDN pour réduire la latence --}}
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

    {{-- CSS local en premier (évite le FOUC) --}}
    <link rel="stylesheet" href="assets/css/fonts.css">

    {{-- Bootstrap + Font Awesome depuis CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous">

    {{-- CSS principal — version basée sur la date de modification du fichier (évite le rechargement inutile) --}}
    @php $vipCssVer = @filemtime(public_path('assets/css/vip.css')) ?: '1'; @endphp
    <link rel="stylesheet" href="assets/css/vip.css?v={{ $vipCssVer }}">

    {{-- Preload de l'image hero (LCP) sur la page d'accueil uniquement --}}
    @if (request()->routeIs('home') && isset($slides) && $slides->isNotEmpty())
        @php
            $firstSlide = $slides->first();
            $heroImg = (string) ($firstSlide->image ?? '');
            if (\Illuminate\Support\Str::startsWith($heroImg, ['http://', 'https://'])) {
                $heroPreload = $heroImg;
            } elseif (\Illuminate\Support\Str::startsWith($heroImg, ['assets/', '/assets/'])) {
                $heroPreload = asset(ltrim($heroImg, '/'));
            } else {
                $heroPreload = asset('storage/' . ltrim($heroImg, '/'));
            }
        @endphp
        <link rel="preload" as="image" href="{{ $heroPreload }}" fetchpriority="high">
    @endif

    {{-- Preload de l'image de fond sur les pages internes --}}
    @if (!request()->routeIs('home') && !request()->routeIs('landing') && isset($header_bg))
        <link rel="preload" as="image" href="{{ $header_bg }}" fetchpriority="high">
    @endif



    {{-- Schema.org Spécifique --}}
    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "FinancialService",
            "name": "VIP GPI Services Financiers",
            "image": "{{ asset('assets/img/header/VIP_Logo_Gold_Gradient10.png') }}",
            "telephone": "+14388382630",
            "email": "admin@vipgpi.ca",
            "address": {
                "@@type": "PostalAddress",
                "streetAddress": "2990 av. Pierre-Péladeau, suite 400",
                "addressLocality": "Laval",
                "addressRegion": "QC",
                "postalCode": "H7T 3B3",
                "addressCountry": "CA"
            },
            "url": "{{ url('/') }}"
        }
    </script>
</head>

<body class="d-flex flex-column min-vh-100">



    {{-- MENU PRINCIPAL --}}
    @include('partials.menu')

    {{-- PAGE HEADER (IMAGE + TITRE) --}}
    @if (!request()->routeIs('home') && !request()->routeIs('landing') && isset($header_title))
        @php
            $bg_image = $header_bg ?? asset('assets/img/header/canvas.png');
            $display_title = $header_title;
            $display_subtitle = $header_subtitle ?? '';
        @endphp

        <section class="page-header d-flex align-items-center justify-content-center text-center"
            style="background-image: url('{{ $bg_image }}');
           min-height: 400px;
           padding: 60px 20px;
           background-size: cover;
           background-position: center top;
           position: relative;">

            <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(14, 16, 48, 0.1);">
            </div>

            <div class="container position-relative z-2">
                {{-- 1. TITRE --}}
                <h1 class="display-4 fw-bold text-white mb-3" style="font-size: clamp(2rem, 5vw, 3.5rem);">
                    {!! $display_title !!} {{-- Utilisation de {!! !!} pour permettre le HTML (span coloré) --}}
                </h1>

                {{-- 2. SOUS-TITRE --}}
                @if (!empty($display_subtitle))
                    <p class="lead text-white-50 mx-auto mb-4"
                        style="max-width: 700px; font-size: clamp(1rem, 3vw, 1.25rem);">
                        {{ $display_subtitle }}
                    </p>
                @endif

                {{-- 3. BOUTON DYNAMIQUE (NOUVEAU) --}}
                @if (isset($header_btn_text) && isset($header_btn_link))
                    <div class="mt-4 animate__animated animate__fadeInUp">
                        <a href="{{ $header_btn_link }}"
                            class="btn btn-warning btn-lg px-5 py-3 fw-bold rounded-pill text-dark shadow-lg hover-scale">
                            {{ $header_btn_text }}
                        </a>
                    </div>
                @endif
            </div>

        </section>
    @endif
