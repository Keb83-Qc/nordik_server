<!-- TOP BAR -->
<div class="vip-topbar">
    <div class="container d-flex align-items-center justify-content-between py-2">
        <div class="d-none d-md-flex align-items-center gap-2 vip-topbar-left">
            <i class="fas fa-shield-alt text-warning"></i>
            <span>{{ __('menu.topbar.tagline') }}</span>
        </div>

        <div class="d-flex align-items-center gap-3 ms-auto vip-topbar-right">

            @php
            $currentLocale = app()->getLocale();
            $base = '/' . $currentLocale;
            $loc = $currentLocale;

            // Langues actives depuis le cache (1h) — évite une requête DB brute sur chaque page
            $langs = collect(\App\Models\Language::activeCodes())->map(fn($c) => (object)['code' => $c]);
            // chemin courant sans préfixe /{locale}
            $path = request()->path(); // ex: fr/services/assurance/...
            $path = preg_replace('#^[a-zA-Z]{2,5}/#', '', $path);
            $path = $path === $currentLocale ? '' : $path; // au cas où
            if ($path === '' || $path === '/') $path = 'home'; // landing → home
            @endphp

            <div class="vip-lang">
                <select
                    class="form-select form-select-sm"
                    onchange="window.location.href=this.value"
                    aria-label="Choisir la langue">
                    @foreach($langs as $l)
                    @php
                    $code = $l->code;
                    $url = url("/switch-language/{$code}?next=/" . ltrim($path, '/'));
                    @endphp

                    <option value="{{ $url }}" @selected($currentLocale===$code)>
                        {{ strtoupper($code) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <a href="{{ $base }}/evenements" class="vip-topbar-link">
                <i class="fas fa-calendar-alt me-1"></i> {{ __('menu.topbar.events') }}
            </a>

            <a href="{{ $base }}/login" class="vip-topbar-link opacity-75">
                <i class="fas fa-lock me-1"></i> {{ __('menu.topbar.advisor') }}
            </a>
        </div>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-xxl vip-navbar sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="/{{ app()->getLocale() }}/home">
            <img src="{{ asset('assets/img/menu/VIP_Logo_Gold_Gradient10.png') }}" alt="VIP GPI" class="vip-logo" width="200" height="62">
        </a>

        <button class="navbar-toggler border-0 p-2" type="button"
            data-bs-toggle="collapse" data-bs-target="#vipNav"
            aria-controls="vipNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="fas fa-bars fa-lg text-white"></span>
        </button>

        <div class="collapse navbar-collapse ms-auto" id="vipNav">
            <ul class="navbar-nav ms-auto align-items-xl-center gap-xl-1">

                @php $base = '/' . app()->getLocale(); @endphp
                <li class="nav-item"><a class="nav-link" href="{{ $base }}/home">{{ __('menu.nav.home') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $base }}/about">{{ __('menu.nav.about') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $base }}/management">{{ __('menu.nav.management') }}</a></li>

                {{-- MEGA MENU (Desktop) --}}
                <li class="nav-item dropdown vip-mega d-none d-xl-block">
                    <a class="nav-link dropdown-toggle" href="#" id="vipServices"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        {{ __('menu.nav.services') }}
                    </a>

                    <div class="dropdown-menu vip-mega-menu p-0 border-0 shadow-lg" aria-labelledby="vipServices">
                        <div class="vip-mega-shell p-3 p-xxl-4">
                            <div class="row g-4">

                                {{-- Colonne gauche (tabs + CTA) --}}
                                <div class="col-4 col-xxl-3">
                                    <div class="nav flex-column nav-pills vip-mega-pills" id="vipMegaTabs" role="tablist" aria-orientation="vertical">
                                        @foreach(($menuServices ?? []) as $i => $cat)
                                        @php
                                        $tabId = "vip-tab-{$i}";
                                        $paneId = "vip-pane-{$i}";
                                        @endphp

                                        <button
                                            class="nav-link {{ $i === 0 ? 'active' : '' }}"
                                            id="{{ $tabId }}"
                                            data-bs-toggle="pill"
                                            data-bs-target="#{{ $paneId }}"
                                            type="button"
                                            role="tab"
                                            aria-controls="{{ $paneId }}"
                                            aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                                            {{ $cat['name'] }}
                                        </button>
                                        @endforeach
                                    </div>

                                    {{-- CTA (panel) --}}
                                    <div class="vip-mega-panel vip-mega-cta mt-3">
                                        <div class="vip-mega-cta-title">{{ __('menu.mega.cta.title') }}</div>
                                        <div class="vip-mega-cta-sub">{{ __('menu.mega.cta.sub') }}</div>

                                        <a class="btn vip-btn-gold w-100" href="{{ $base }}/contact">
                                            {{ __('menu.mega.cta.btn') }}
                                        </a>
                                    </div>
                                </div>

                                {{-- Colonne droite (contenu) --}}
                                <div class="col-8 col-xxl-9">
                                    <div class="tab-content vip-mega-content">
                                        @foreach(($menuServices ?? []) as $i => $cat)
                                        @php
                                        $tabId = "vip-tab-{$i}";
                                        $paneId = "vip-pane-{$i}";
                                        $services = $cat['services'] ?? [];
                                        // 2 cartes égales : on split en 2 colonnes
                                        $half = (int) ceil(count($services) / 2);
                                        $left = array_slice($services, 0, $half);
                                        $right = array_slice($services, $half);
                                        @endphp

                                        <div
                                            class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}"
                                            id="{{ $paneId }}"
                                            role="tabpanel"
                                            aria-labelledby="{{ $tabId }}"
                                            tabindex="0">

                                            <div class="vip-mega-grid">
                                                {{-- Card gauche --}}
                                                <div class="vip-mega-panel vip-mega-card">
                                                    @foreach($left as $srv)
                                                    <a class="vip-mega-link"
                                                        href="/{{ $loc }}/{{ $cat['slug'] }}/{{ $srv['slug'] }}">
                                                        {{ $srv['title'] }}
                                                    </a>
                                                    @endforeach
                                                </div>

                                                {{-- Card droite --}}
                                                <div class="vip-mega-panel vip-mega-card">
                                                    @foreach($right as $srv)
                                                    <a class="vip-mega-link"
                                                        href="/{{ $loc }}/{{ $cat['slug'] }}/{{ $srv['slug'] }}">
                                                        {{ $srv['title'] }}
                                                    </a>
                                                    @endforeach
                                                </div>
                                            </div>

                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </li>

                <!-- Liens normaux -->
                <li class="nav-item"><a class="nav-link" href="{{ $base }}/construction">{{ __('menu.nav.team') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $base }}/carrieres">{{ __('menu.nav.careers') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $base }}/partenaires">{{ __('menu.nav.partners') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $base }}/contact">{{ __('menu.nav.contact') }}</a></li>

                <!-- CTA -->
                <li class="nav-item ms-xl-2 my-3 my-xl-0">
                    <a href="{{ $base }}/contact" class="btn vip-btn-gold rounded-pill px-4 py-2 w-100 w-xl-auto">
                        {{ __('menu.nav.cta') }}
                    </a>
                </li>

                <!-- Mobile: lien Services simple -->
                <li class="nav-item d-xl-none">
                    <a class="nav-link" href="{{ $base }}/services">{{ __('menu.nav.services_mobile') }}</a>
                </li>

            </ul>
        </div>
    </div>
</nav>