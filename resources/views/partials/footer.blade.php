@php
    // Récupération des paramètres dynamiques (toujours nécessaire)
    $copyrightText = $settings['footer_copyright'] ?? __('footer.default_copyright');

    // Description : On utilise la traduction du fichier PHP si le setting est vide
    $lang = app()->getLocale();
    $footerDesc =
        $lang == 'en' && !empty($settings['footer_description_en'])
            ? $settings['footer_description_en']
            : $settings['footer_description'] ?? __('footer.description');
@endphp

<footer class="footer-premium">
    <div class="container">
        <div class="row g-4">

            {{-- COLONNE 1 : LOGO & DESCRIPTION --}}
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <a href="{{ route('home') }}" class="d-inline-block mb-4">
                    <img src="{{ asset('assets/img/footer/VIP_Logo_Gold_Gradient10.png') }}" alt="VIP GPI"
                        style="height: 90px;">
                </a>
                <p class="pe-lg-5 mb-4">
                    {{ $footerDesc }}
                </p>

                <div class="d-flex gap-3">
                    @if (!empty($settings['facebook_url']))
                        <a href="{{ $settings['facebook_url'] }}" target="_blank"
                            class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center"
                            style="width:40px;height:40px;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif

                    @if (!empty($settings['linkedin_url']))
                        <a href="{{ $settings['linkedin_url'] }}" target="_blank"
                            class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center"
                            style="width:40px;height:40px;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- COLONNE 2 : NAVIGATION --}}
            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                <h5 class="footer-title">{{ __('footer.navigation') }}</h5>
                <ul class="footer-links">
                    <li><a href="{{ url('/about') }}"><i class="fas fa-chevron-right"></i> {{ __('footer.about') }}</a>
                    </li>
                    <li><a href="{{ url('/blog') }}"><i class="fas fa-chevron-right"></i> {{ __('footer.blog') }}</a>
                    </li>
                    <li><a href="{{ url('/contact') }}"><i class="fas fa-chevron-right"></i>
                            {{ __('footer.contact') }}</a></li>
                    <li><a href="{{ url('/login') }}"><i class="fas fa-lock"></i>
                            {{ __('footer.advisor_access') }}</a>
                    </li>
                </ul>
            </div>

            {{-- COLONNE 3 : CONTACT --}}
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="footer-title">{{ __('footer.contact_us') }}</h5>

                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="footer-contact-text">
                        <h6>{{ __('footer.address') }}</h6>
                        <a href="https://maps.google.com/?q=2990+av.+Pierre-Péladeau,+Laval" target="_blank">
                            2990 av. Pierre-Péladeau,<br>Suite 400, Laval, QC H7T 3B3
                        </a>
                    </div>
                </div>

                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="footer-contact-text">
                        <h6>{{ __('footer.phone') }}</h6>
                        @php
                            $phone = $settings['site_phone'] ?? '579 640-3334';
                            $phoneLink = preg_replace('/[^0-9]/', '', $phone);
                        @endphp
                        <a href="tel:{{ $phoneLink }}">
                            {{ $phone }}
                        </a>
                    </div>
                </div>

                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><i class="fas fa-envelope"></i></div>
                    <div class="footer-contact-text">
                        <h6>{{ __('footer.email') }}</h6>
                        @php $email = $settings['site_email'] ?? 'admin@vipgpi.ca'; @endphp
                        <a href="mailto:{{ $email }}">
                            {{ $email }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- COLONNE 4 : IMAGE AIGLE --}}
            <div
                class="col-lg-3 col-md-6 text-center text-lg-end d-flex align-items-end justify-content-center justify-content-lg-end">
                <img src="{{ asset('assets/img/footer/imageVIP-2.webp') }}" class="img-fluid footer-eagle"
                    alt="VIP" loading="lazy" width="180" height="220">
            </div>

        </div>
    </div>

    {{-- COPYRIGHT & LIENS LEGAUX --}}
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    © {{ date('Y') }} <strong class="text-white">{{ $copyrightText }}</strong>
                    {{ __('footer.rights_reserved') }}
                </div>
                <div class="col-md-6 text-center text-md-end">
                    @php
                        $locale = app()->getLocale();
                    @endphp
                    <a href="{{ route('legal.privacy', ['locale' => $locale]) }}"
                        class="text-decoration-none text-muted small me-3 hover-white">{{ __('footer.privacy') }}</a>
                    <a href="{{ route('legal.terms', ['locale' => $locale]) }}"
                        class="text-decoration-none text-muted small hover-white">{{ __('footer.terms') }}</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* 1. Style de base (Mobile & Général) */
        .footer-eagle {
            max-width: 180px;
            margin-top: 20px;
            /* Transition très douce et "élastique" pour un effet premium */
            transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            /* On prépare le filtre pour qu'il s'anime aussi */
            filter: drop-shadow(0 0 0 rgba(0, 0, 0, 0)) brightness(1);
        }

        /* 2. Effet au survol (Général) */
        .footer-eagle:hover {
            /* Petite lueur dorée et augmentation de la luminosité */
            filter: drop-shadow(0 10px 15px rgba(212, 175, 55, 0.3)) brightness(1.1);
            cursor: pointer;
        }

        /* 3. Sur Ordinateur (Écrans larges > 992px) */
        @@media (min-width: 992px) {
            .footer-eagle {
                /* État initial sur PC : Déjà grossi à 1.25 */
                transform: scale(1.25);
                transform-origin: bottom right;
                margin-right: 10px;
                margin-bottom: 10px;
            }

            /* État survol sur PC */
            .footer-eagle:hover {
                /* On grossi encore un peu (1.25 -> 1.35)
               On le fait pivoter très légèrement (-3deg) pour donner de la vie
               On le monte un peu (translateY)
            */
                transform: scale(1.35) rotate(-3deg) translateY(-10px);

                /* Lueur plus intense sur PC */
                filter: drop-shadow(0 15px 25px rgba(212, 175, 55, 0.4)) brightness(1.15);
            }
        }
    </style>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer
    crossorigin="anonymous"></script>
@php $jsVer = @filemtime(public_path('assets/js/script.js')) ?: '1'; @endphp
<script src="{{ asset('assets/js/script.js') }}?v={{ $jsVer }}" defer></script>
@php $menuJsVer = @filemtime(public_path('assets/js/mega-menu.js')) ?: '1'; @endphp
<script src="{{ asset('assets/js/mega-menu.js') }}?v={{ $menuJsVer }}" defer></script>

{{-- Prefetching: précharge les pages internes au survol pour navigation quasi-instantanée --}}
<script>
    (function() {
        // Utilise Speculation Rules API si disponible (Chrome 109+) — le plus rapide
        if (HTMLScriptElement.supports && HTMLScriptElement.supports('speculationrules')) {
            const s = document.createElement('script');
            s.type = 'speculationrules';
            s.textContent = JSON.stringify({
                prefetch: [{
                    source: 'document',
                    eagerness: 'moderate',
                    where: {
                        and: [{
                            href_matches: '/*'
                        }, {
                            not: {
                                href_matches: '/admin/*'
                            }
                        }, {
                            not: {
                                href_matches: '/abf/*'
                            }
                        }, {
                            not: {
                                href_matches: '/conseiller/*'
                            }
                        }]
                    }
                }]
            });
            document.head.appendChild(s);
            return;
        }

        // Fallback: prefetch manuel au survol (65ms de délai pour éviter les prefetchs inutiles)
        const prefetched = new Set();
        let timer;

        function prefetch(url) {
            if (prefetched.has(url)) return;
            prefetched.add(url);
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            link.as = 'document';
            document.head.appendChild(link);
        }

        document.addEventListener('mouseover', function(e) {
            const a = e.target.closest('a[href]');
            if (!a) return;
            const href = a.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:'))
                return;
            if (/\/(admin|abf|conseiller|logout)/.test(href)) return;

            clearTimeout(timer);
            timer = setTimeout(() => prefetch(a.href), 65);
        }, {
            passive: true
        });

        document.addEventListener('mouseout', function() {
            clearTimeout(timer);
        }, {
            passive: true
        });
    })();

    {{-- Web Vitals: suivi LCP/CLS/INP/FCP/TTFB vers SystemLog --}}
        <
        script >
        (function() {
            var endpoint = '{{ route('log-web-vitals') }}';
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!token || !('PerformanceObserver' in window)) return;

            var sent = new Set();

            function rating(metric, value) {
                if (metric === 'LCP') return value <= 2500 ? 'good' : value <= 4000 ? 'needs-improvement' : 'poor';
                if (metric === 'INP') return value <= 200 ? 'good' : value <= 500 ? 'needs-improvement' : 'poor';
                if (metric === 'CLS') return value <= 0.1 ? 'good' : value <= 0.25 ? 'needs-improvement' : 'poor';
                if (metric === 'FCP') return value <= 1800 ? 'good' : value <= 3000 ? 'needs-improvement' : 'poor';
                if (metric === 'TTFB') return value <= 800 ? 'good' : value <= 1800 ? 'needs-improvement' : 'poor';
                return 'unknown';
            }

            function send(metric, value, delta, id) {
                var key = metric + ':' + id;
                if (sent.has(key)) return;
                sent.add(key);

                var payload = {
                    _token: token,
                    metric: metric,
                    value: Number(value || 0),
                    delta: Number(delta || 0),
                    id: String(id || ''),
                    rating: rating(metric, Number(value || 0)),
                    navigationType: performance.getEntriesByType('navigation')?.[0]?.type || '',
                    url: window.location.href
                };

                var body = JSON.stringify(payload);
                if (navigator.sendBeacon) {
                    var blob = new Blob([body], {
                        type: 'application/json'
                    });
                    navigator.sendBeacon(endpoint, blob);
                    return;
                }

                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: body,
                    keepalive: true
                }).catch(function() {});
            }

            // LCP
            (function() {
                var lcp;
                var po = new PerformanceObserver(function(entryList) {
                    var entries = entryList.getEntries();
                    lcp = entries[entries.length - 1];
                });
                po.observe({
                    type: 'largest-contentful-paint',
                    buffered: true
                });
                document.addEventListener('visibilitychange', function() {
                    if (document.visibilityState === 'hidden' && lcp) {
                        send('LCP', lcp.startTime, 0, lcp.id || 'lcp');
                        po.disconnect();
                    }
                }, {
                    once: true
                });
            })();

            // CLS
            (function() {
                var cls = 0;
                var po = new PerformanceObserver(function(entryList) {
                    entryList.getEntries().forEach(function(entry) {
                        if (!entry.hadRecentInput) cls += entry.value;
                    });
                });
                po.observe({
                    type: 'layout-shift',
                    buffered: true
                });
                document.addEventListener('visibilitychange', function() {
                    if (document.visibilityState === 'hidden') {
                        send('CLS', cls, 0, 'cls');
                        po.disconnect();
                    }
                }, {
                    once: true
                });
            })();

            // INP (Event Timing)
            (function() {
                var maxInp = 0;
                var po = new PerformanceObserver(function(entryList) {
                    entryList.getEntries().forEach(function(entry) {
                        if (entry.interactionId && entry.duration > maxInp) {
                            maxInp = entry.duration;
                        }
                    });
                });
                try {
                    po.observe({
                        type: 'event',
                        durationThreshold: 40,
                        buffered: true
                    });
                    document.addEventListener('visibilitychange', function() {
                        if (document.visibilityState === 'hidden') {
                            if (maxInp > 0) send('INP', maxInp, 0, 'inp');
                            po.disconnect();
                        }
                    }, {
                        once: true
                    });
                } catch (e) {
                    // API non supportée sur certains navigateurs
                }
            })();

            // FCP + TTFB
            (function() {
                var paint = performance.getEntriesByName('first-contentful-paint')[0];
                if (paint) send('FCP', paint.startTime, 0, 'fcp');

                var nav = performance.getEntriesByType('navigation')[0];
                if (nav) send('TTFB', nav.responseStart, 0, 'ttfb');
            })();
        })();
</script>


</body>

</html>
