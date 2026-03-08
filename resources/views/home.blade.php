@include('partials.header')

@php
use Illuminate\Support\Str;
@endphp

{{-- SECTION HERO CARROUSEL --}}
<section class="p-0 position-relative">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

        {{-- Indicateurs --}}
        @if(($slides ?? collect())->count())
        <div class="carousel-indicators">
            @foreach($slides as $key => $slide)
            <button type="button"
                data-bs-target="#heroCarousel"
                data-bs-slide-to="{{ $key }}"
                class="{{ $loop->first ? 'active' : '' }}"
                aria-current="{{ $loop->first ? 'true' : 'false' }}"
                aria-label="Slide {{ $key + 1 }}">
            </button>
            @endforeach
        </div>
        @endif

        <div class="carousel-inner">

            @forelse($slides as $slide)
            @php
            // IMAGE (slide)
            $rawImage = (string) ($slide->image ?? '');
            if (Str::startsWith($rawImage, ['http://', 'https://'])) {
            $imgUrl = $rawImage;
            } elseif (Str::startsWith($rawImage, ['assets/', '/assets/'])) {
            $imgUrl = asset(ltrim($rawImage, '/'));
            } else {
            $imgUrl = asset('storage/' . ltrim($rawImage, '/'));
            }

            // BUTTON LINK (locale-safe)
            $rawLink = trim((string) ($slide->button_link ?? ''));

            if ($rawLink === '') {
            $buttonUrl = null;
            } elseif (Str::startsWith($rawLink, ['http://', 'https://'])) {
            $buttonUrl = $rawLink;
            } else {
            $rawLink = '/' . ltrim($rawLink, '/');
            $rawLink = preg_replace('#^/[a-zA-Z]{2,5}(?=/|$)#', '', $rawLink); // retire /fr, /en...
            $rawLink = preg_replace('#\.php$#i', '', $rawLink); // retire .php
            $rawLink = $rawLink === '' ? '/home' : $rawLink;

            $buttonUrl = url('/' . app()->getLocale() . $rawLink);
            }
            @endphp

            <div class="carousel-item {{ $loop->first ? 'active' : '' }}" style="height:65vh; min-height:500px;">
                <div class="w-100 h-100"
                    style="background-image:url('{{ $imgUrl }}'); background-size:cover; background-position:center;">
                </div>

                <div class="position-absolute top-0 start-0 w-100 h-100"
                    style="background: linear-gradient(to bottom, rgba(14,16,48,.02), rgba(14,16,48,.55));">
                </div>

                <div class="carousel-caption d-flex flex-column h-100 align-items-center justify-content-center pb-5">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-12 text-center" data-aos="fade-up">

                                @if(!empty($slide->title))
                                <h1 class="fw-bold text-white mb-4 animate__animated animate__fadeInDown"
                                    style="text-shadow:0 4px 15px rgba(0,0,0,0.6);
                                                   font-size:clamp(1.5rem,4vw,3.5rem);">
                                    {{ $slide->title }}
                                </h1>
                                @endif

                                @if(!empty($slide->subtitle))
                                <div class="lead text-white-50 mb-5 fs-4 animate__animated animate__fadeInUp animate__delay-1s">
                                    {!! $slide->subtitle !!}
                                </div>
                                @endif

                                @if(!empty($slide->button_text) && !empty($buttonUrl))
                                <div class="slide_vip animate__animated animate__fadeInUp animate__delay-2s">
                                    <a href="{{ $buttonUrl }}"
                                        class="vip-btn-gold btn btn-cta btn-lg px-5 py-3 fw-bold shadow hover-scale">
                                        {{ $slide->button_text }}
                                    </a>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @empty
            {{-- Fallback si aucun slide --}}
            <div class="carousel-item active" style="height:65vh; min-height:500px;">
                <div class="w-100 h-100"
                    style="background-image:url('{{ asset('assets/img/home/canvas.png') }}'); background-size:cover; background-position:center;">
                </div>
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(14,16,48,.7);"></div>
                <div class="carousel-caption d-flex flex-column h-100 align-items-center justify-content-center">
                    <h1 class="display-3 fw-bold text-white">Votre sécurité, notre priorité</h1>
                    <p class="lead text-white-50 mb-5">Bienvenue chez VIP GPI.</p>
                </div>
            </div>
            @endforelse

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Précédent</span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Suivant</span>
        </button>
    </div>
</section>

<style>
    .hover-scale {
        transition: transform .3s ease;
    }

    .hover-scale:hover {
        transform: scale(1.05);
    }

    .carousel-indicators button {
        width: 12px !important;
        height: 12px !important;
        border-radius: 50%;
        margin: 0 5px !important;
    }
</style>

{{-- SECTION POURQUOI NOUS --}}
<section class="section-padding"
    style="background: linear-gradient(rgba(0,0,0,.8), rgba(0,0,0,.8)), url('{{ asset('assets/img/home/bg-frame-work.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">

                <h3 class="mb-4 fw-bold text-white">{{ __('home.why_us_title') }}</h3>
                <h4 class="text-white-50 mb-3 fw-light">{{ __('home.why_us_subtitle') }}</h4>

                <ul class="list-unstyled mb-4">
                    @foreach(__('home.why_us_pain_points') as $point)
                    <li class="d-flex align-items-start mb-2 text-white">
                        <i class="fas fa-check-circle text-warning mt-1 me-3"></i>
                        <span>{{ $point }}</span>
                    </li>
                    @endforeach
                </ul>

                <div class="text-white text-justify opacity-90 mb-4" style="line-height: 1.8;">
                    <p>{{ __('home.why_us_solution') }}</p>
                </div>

                <p class="text-white fw-bold mb-3">{{ __('home.why_us_summary') }}</p>

                <ul class="list-unstyled mb-4">
                    @foreach(__('home.why_us_benefits') as $benefit)
                    <li class="d-flex align-items-start mb-2 text-white">
                        <i class="fas fa-star text-warning mt-1 me-3"></i>
                        <span>{{ $benefit }}</span>
                    </li>
                    @endforeach
                </ul>

            </div>

            <div class="col-lg-5">
                <img src="{{ asset('assets/img/home/pourquoi_avec_nous.jpg') }}"
                    class="img-fluid rounded shadow-lg border border-4 border-white"
                    alt="Pourquoi nous choisir">
            </div>
        </div>
    </div>
</section>

{{-- SECTION SERVICES --}}
<section class="section-padding bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color: var(--primary-color);">{{ __('home.services_title') }}</h2>
            <div style="width:60px; height:3px; background: var(--secondary-color); margin: 15px auto;"></div>
        </div>

        <div class="row g-4">
            @forelse($services as $svc)
            @php
            // IMAGE (service)
            $rawImage = (string) ($svc->image ?? '');
            if (Str::startsWith($rawImage, ['http://', 'https://'])) {
            $imgSrc = $rawImage;
            } elseif (Str::startsWith($rawImage, ['assets/', '/assets/'])) {
            $imgSrc = asset(ltrim($rawImage, '/'));
            } else {
            $imgSrc = asset('storage/' . ltrim($rawImage, '/'));
            }

            // LINK (locale-safe)
            $rawLink = trim((string) ($svc->link ?? ''));

            if ($rawLink === '') {
            $linkUrl = url('/' . app()->getLocale() . '/home');
            } elseif (Str::startsWith($rawLink, ['http://', 'https://'])) {
            $linkUrl = $rawLink;
            } else {
            $rawLink = '/' . ltrim($rawLink, '/');
            $rawLink = preg_replace('#^/[a-zA-Z]{2,5}(?=/|$)#', '', $rawLink);
            $rawLink = preg_replace('#\.php$#i', '', $rawLink);
            $rawLink = $rawLink === '' ? '/home' : $rawLink;

            $linkUrl = url('/' . app()->getLocale() . $rawLink);
            }
            @endphp

            <div class="col-md-6 col-lg-3">
                <a href="{{ $linkUrl }}" class="service-grid-card" style="background-image:url('{{ $imgSrc }}');">
                    <div class="service-grid-content">
                        <h3>{{ $svc->title }}</h3>
                    </div>

                    <div class="service-overlay">
                        <div class="service-overlay-content">
                            <h4 class="text-warning mb-3">{{ $svc->title }}</h4>
                            <p>{!! nl2br(e($svc->description)) !!}</p>
                            <span class="btn btn-sm btn-outline-light rounded-pill mt-2">
                                {{ __('home.learn_more') }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            @empty
            <div class="col-12 text-center text-muted">
                <p>{{ __('home.no_services') }}</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

{{-- SECTION STATS --}}
<section class="position-relative w-100" id="stats-section">
    <img src="{{ asset('assets/img/home/Background-conservation.jpg') }}" alt="Stats Background"
        class="w-100 d-block" style="height:auto; min-height:250px; object-fit:cover;">

    <div class="position-absolute top-50 start-0 w-100 translate-middle-y text-white text-center"
        style="text-shadow: 0 2px 10px rgba(0,0,0,0.8);">
        <div class="container">
            <div class="row">
                @foreach($stats as $s)
                <div class="col-6 col-md-3 mb-4 mb-md-0">
                    <div class="display-4 fw-bold" style="color: var(--secondary-color);">
                        <span class="counter" data-target="{{ $s->value }}">0</span>{{ $s->suffix }}
                    </div>
                    <div class="fw-bold text-uppercase letter-spacing-1">{{ $s->label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@include('sections.google-reviews')
<x-blog-section />

@include('partials.footer')