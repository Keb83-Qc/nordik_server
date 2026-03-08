<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VIP GPI — Landing</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}">

    <style>
        :root {
            --bg: #0E1030;
            --bg2: #12154b;

            --gold1: #b88b4a;
            --gold2: #e6c67c;
            --gold3: #c9a050;

            --text: rgba(255, 255, 255, .92);
            --muted: rgba(255, 255, 255, .58);

            --card: rgba(14, 16, 48, .62);
            --cardBorder: rgba(201, 160, 80, .35);
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: "Open Sans", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            background: var(--bg);
            overflow: hidden;
        }

        h1,
        h2,
        h3,
        .title-cinema {
            font-family: "Montserrat", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        /* =========================
           BACKGROUND CINEMA
           ========================= */
        .bg-wrap {
            position: fixed;
            inset: 0;
            z-index: -8;
            background:
                radial-gradient(1200px 800px at 25% 30%, rgba(230, 198, 124, .10), transparent 60%),
                radial-gradient(1200px 800px at 75% 70%, rgba(201, 160, 80, .08), transparent 60%),
                radial-gradient(circle at center, #1a1d4d 0%, var(--bg) 100%);
        }

        .bg-logo {
            position: fixed;
            inset: 0;
            z-index: -7;
            background-image:url("{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 56%;
            opacity: .03;
            filter: blur(.2px);
            animation: bgPulse 10s ease-in-out infinite alternate;
            pointer-events: none;
        }

        @keyframes bgPulse {
            from {
                transform: scale(1);
                opacity: .028;
            }

            to {
                transform: scale(1.06);
                opacity: .055;
            }
        }

        /* Grain + vignette */
        .grain {
            position: fixed;
            inset: 0;
            z-index: -6;
            pointer-events: none;
            opacity: .11;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='160' height='160' filter='url(%23n)' opacity='.35'/%3E%3C/svg%3E");
            mix-blend-mode: overlay;
            animation: grainMove 6s steps(6) infinite;
        }

        @keyframes grainMove {
            0% {
                transform: translate(0, 0)
            }

            20% {
                transform: translate(-2%, 1%)
            }

            40% {
                transform: translate(1%, -2%)
            }

            60% {
                transform: translate(2%, 2%)
            }

            80% {
                transform: translate(-1%, 2%)
            }

            100% {
                transform: translate(0, 0)
            }
        }

        .vignette {
            position: fixed;
            inset: -10%;
            z-index: -5;
            pointer-events: none;
            background: radial-gradient(closest-side, transparent 55%, rgba(0, 0, 0, .48) 100%);
        }

        /* =========================
           CARD (LUXURY)
           ========================= */
        .welcome-card {
            width: min(980px, 92vw);
            border-radius: 26px;
            background: var(--card);
            border: 1px solid var(--cardBorder);
            backdrop-filter: blur(18px);
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, .05) inset,
                0 60px 120px -40px rgba(0, 0, 0, .82),
                0 0 44px rgba(201, 160, 80, .12);
            padding: 2.2rem 2.2rem 1.9rem;
            position: relative;
            overflow: hidden;

            opacity: 0;
            transform: translateY(70px) scale(.965);
            animation: cardIn 900ms cubic-bezier(.2, .85, .2, 1) forwards;
        }

        @keyframes cardIn {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Gold edge highlight */
        .welcome-card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 26px;
            padding: 1px;
            background: linear-gradient(135deg,
                    rgba(230, 198, 124, .38),
                    rgba(201, 160, 80, .12),
                    rgba(184, 139, 74, .30));
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            opacity: .60;
        }

        /* Spotlight moving */
        .welcome-card::after {
            content: "";
            position: absolute;
            inset: -30%;
            background: radial-gradient(circle at 20% 20%, rgba(230, 198, 124, .20), transparent 42%);
            filter: blur(18px);
            opacity: .75;
            mix-blend-mode: screen;
            pointer-events: none;
            animation: spotMove 6.8s ease-in-out infinite;
        }

        @keyframes spotMove {
            0% {
                transform: translate(-8%, -6%);
                opacity: .55;
            }

            50% {
                transform: translate(10%, 8%);
                opacity: .75;
            }

            100% {
                transform: translate(-6%, -8%);
                opacity: .55;
            }
        }

        /* Film burn (very subtle edges) */
        .film-burn {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 2;
            opacity: .18;
            background:
                radial-gradient(120px 120px at 8% 12%, rgba(255, 110, 0, .22), transparent 60%),
                radial-gradient(180px 140px at 92% 18%, rgba(255, 160, 40, .18), transparent 62%),
                radial-gradient(140px 160px at 16% 90%, rgba(255, 90, 0, .16), transparent 65%),
                radial-gradient(220px 200px at 88% 88%, rgba(255, 140, 40, .14), transparent 70%),
                radial-gradient(closest-side, transparent 60%, rgba(0, 0, 0, .50) 100%);
            mix-blend-mode: overlay;
            filter: blur(0.3px);
        }

        .inner {
            position: relative;
            z-index: 5;
            /* above spotlight/burn */
        }

        .logo {
            width: min(280px, 70%);
            height: auto;
            filter: drop-shadow(0 12px 18px rgba(0, 0, 0, .55));
            animation: logoGlow 8s ease-in-out infinite;
        }

        @keyframes logoGlow {

            0%,
            100% {
                filter: drop-shadow(0 12px 18px rgba(0, 0, 0, .55)) brightness(1);
            }

            50% {
                filter: drop-shadow(0 14px 24px rgba(201, 160, 80, .25)) brightness(1.10);
            }
        }

        /* =========================
           TITLES (2x2) + CINEMA FX
           ========================= */
        .titles-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 18px;
            margin-top: 1.15rem;
        }

        .title-cinema {
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 4px;
            font-weight: 800;
            font-size: clamp(1.05rem, 2.2vw, 1.50rem);
            color: var(--text);
            text-shadow: 0 12px 26px rgba(0, 0, 0, .55);
            position: relative;

            opacity: 0;
            transform: translateY(16px);
        }

        /* animate in sync */
        .title-cinema.t1 {
            animation: riseIn 720ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 260ms;
        }

        .title-cinema.t2 {
            animation: riseIn 720ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 300ms;
        }

        .title-cinema.t3 {
            animation: riseIn 720ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 340ms;
        }

        .title-cinema.t4 {
            animation: riseIn 720ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 380ms;
        }

        @keyframes riseIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Gold glow halo behind titles */
        .title-cinema::after {
            content: "";
            position: absolute;
            inset: -18px -10px;
            background:
                radial-gradient(circle at 30% 50%, rgba(230, 198, 124, .25), transparent 45%),
                radial-gradient(circle at 70% 50%, rgba(201, 160, 80, .18), transparent 50%);
            filter: blur(14px);
            opacity: .55;
            z-index: -1;
            animation: glowDrift 5.5s ease-in-out infinite;
        }

        @keyframes glowDrift {

            0%,
            100% {
                transform: translateX(-6px);
                opacity: .42;
            }

            50% {
                transform: translateX(10px);
                opacity: .68;
            }
        }

        /* Shimmer on letters (cinema) */
        .shimmer {
            background: linear-gradient(110deg,
                    rgba(255, 255, 255, .85) 0%,
                    rgba(230, 198, 124, .95) 16%,
                    rgba(255, 255, 255, .92) 34%,
                    rgba(255, 255, 255, .66) 58%,
                    rgba(230, 198, 124, .95) 78%,
                    rgba(255, 255, 255, .85) 100%);
            background-size: 260% 100%;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: shimmerMove 2.6s ease-in-out infinite;
        }

        @keyframes shimmerMove {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 100% 50%;
            }
        }

        /* =========================
           SUBTITLES (2 cols)
           ========================= */
        .sub-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 18px;
            margin-top: .70rem;
        }

        .sub {
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, .62);
            font-weight: 700;
            font-size: .95rem;

            opacity: 0;
            transform: translateY(14px);
        }

        .sub.s1 {
            animation: riseIn2 650ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 560ms;
        }

        .sub.s2 {
            animation: riseIn2 650ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 560ms;
        }

        .sub.s3 {
            animation: riseIn2 650ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 610ms;
        }

        .sub.s4 {
            animation: riseIn2 650ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 610ms;
        }

        @keyframes riseIn2 {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* =========================
           BUTTONS
           ========================= */
        .btn-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px 18px;
            margin-top: 1.1rem;
        }

        .lang-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;

            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;

            text-decoration: none;
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;

            color: rgba(255, 255, 255, .92);
            background: rgba(255, 255, 255, .03);
            border: 1px solid rgba(201, 160, 80, .55);

            position: relative;
            overflow: hidden;

            box-shadow:
                0 0 0 1px rgba(255, 255, 255, .04) inset,
                0 18px 40px -24px rgba(0, 0, 0, .90);
            transition:
                transform .18s ease,
                background .18s ease,
                border-color .18s ease,
                box-shadow .18s ease;

            opacity: 0;
            transform: translateY(18px);
        }

        .lang-btn::before {
            content: "";
            position: absolute;
            inset: -40% -60%;
            background: linear-gradient(90deg, transparent, rgba(230, 198, 124, .30), transparent);
            transform: rotate(12deg) translateX(-60%);
            transition: .6s ease;
            opacity: .9;
        }

        .lang-btn:hover {
            background: rgba(201, 160, 80, .16);
            border-color: rgba(230, 198, 124, .88);
            transform: translateY(-2px);
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, .06) inset,
                0 26px 55px -28px rgba(0, 0, 0, .95),
                0 0 28px rgba(201, 160, 80, .18);
        }

        .lang-btn:hover::before {
            transform: rotate(12deg) translateX(60%);
        }

        .tag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 34px;
            border-radius: 999px;
            background: linear-gradient(45deg, rgba(184, 139, 74, .38), rgba(230, 198, 124, .30));
            border: 1px solid rgba(230, 198, 124, .42);
            color: rgba(255, 255, 255, .95);
            font-weight: 900;
            letter-spacing: 1px;
            box-shadow: 0 10px 22px -18px rgba(0, 0, 0, .9);
        }

        /* Button entrance (synced) */
        .lang-btn.b1 {
            animation: riseBtn 700ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 820ms;
        }

        .lang-btn.b2 {
            animation: riseBtn 700ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 900ms;
        }

        .lang-btn.b3 {
            animation: riseBtn 700ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 980ms;
        }

        .lang-btn.b4 {
            animation: riseBtn 700ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 1060ms;
        }

        @keyframes riseBtn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* =========================
           FOOT NOTES under each col
           ========================= */
        .foot-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 18px;
            margin-top: .78rem;
        }

        .foot {
            color: rgba(255, 255, 255, .44);
            font-size: .78rem;
            letter-spacing: .8px;
            text-transform: uppercase;
            line-height: 1.35;

            opacity: 0;
            transform: translateY(12px);
        }

        .foot.f1 {
            animation: riseFoot 650ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 1160ms;
        }

        .foot.f2 {
            animation: riseFoot 650ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 1160ms;
        }

        .foot.f3 {
            animation: riseFoot 650ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 1220ms;
        }

        .foot.f4 {
            animation: riseFoot 650ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 1220ms;
        }

        @keyframes riseFoot {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* =========================
           Audio prompt (tiny badge)
           ========================= */
        .sound-badge {
            position: absolute;
            right: 16px;
            top: 16px;
            z-index: 6;

            display: inline-flex;
            align-items: center;
            gap: 8px;

            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            letter-spacing: .6px;
            font-size: .75rem;
            text-transform: uppercase;

            color: rgba(255, 255, 255, .78);
            background: rgba(0, 0, 0, .20);
            border: 1px solid rgba(230, 198, 124, .22);
            border-radius: 999px;
            padding: 8px 12px;

            backdrop-filter: blur(10px);
            box-shadow: 0 12px 24px -18px rgba(0, 0, 0, .85);

            opacity: 0;
            transform: translateY(-10px);
            animation: badgeIn 650ms cubic-bezier(.2, .85, .2, 1) forwards;
            animation-delay: 980ms;
        }

        @keyframes badgeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 99px;
            background: linear-gradient(45deg, var(--gold1), var(--gold2));
            box-shadow: 0 0 18px rgba(230, 198, 124, .35);
        }

        /* Responsive */
        @media (max-width: 720px) {
            body {
                overflow: auto;
            }

            .welcome-card {
                margin: 22px 0;
                padding: 2rem 1.5rem 1.7rem;
            }

            .titles-grid,
            .sub-grid,
            .btn-grid,
            .foot-grid {
                grid-template-columns: 1fr;
            }

            .title-cinema {
                text-align: center;
                letter-spacing: 3px;
            }

            .sub,
            .foot {
                text-align: center;
            }

            .sound-badge {
                position: static;
                margin: 10px auto 0;
            }
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation: none !important;
                transition: none !important;
            }

            .welcome-card {
                opacity: 1;
                transform: none;
            }

            .title-cinema,
            .sub,
            .lang-btn,
            .foot,
            .sound-badge {
                opacity: 1;
                transform: none;
            }
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">

    @php
    $next = '/home';

    $links = [
    'fr' => url("/switch-language/fr?next={$next}"),
    'en' => url("/switch-language/en?next={$next}"),
    'ht' => url("/switch-language/ht?next={$next}"),
    'es' => url("/switch-language/es?next={$next}"),
    ];
    @endphp

    <div class="bg-wrap"></div>
    <div class="bg-logo"></div>
    <div class="grain"></div>
    <div class="vignette"></div>

    <div class="welcome-card text-center" id="welcomeCard">
        <div class="film-burn"></div>

        <div class="inner">
            <div class="d-flex justify-content-center">
                <img src="{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}" alt="VIP GPI" class="logo">
            </div>

            {{-- Badge son (déclenché au clic) --}}
            <div class="sound-badge" id="soundBadge" title="Clique pour activer le son">
                <span class="dot"></span>
                Click / Tap — Sound
            </div>

            {{-- TITRES (2 x 2) --}}
            <div class="titles-grid">
                <div class="title-cinema t1"><span class="shimmer">Bienvenue</span></div>
                <div class="title-cinema t2"><span class="shimmer">Welcome</span></div>
                <div class="title-cinema t3"><span class="shimmer">Byenveni</span></div>
                <div class="title-cinema t4"><span class="shimmer">Bienvenido</span></div>
            </div>

            {{-- SOUS-TITRES (2 colonnes) --}}
            <div class="sub-grid">
                <div class="sub s1">Choisissez votre langue</div>
                <div class="sub s2">Choose your language</div>
                <div class="sub s3">Chwazi lang ou</div>
                <div class="sub s4">Elija su idioma</div>
            </div>

            {{-- BOUTONS (FR/EN puis HT/ES) --}}
            <div class="btn-grid">
                <a class="lang-btn b1" data-lang="fr" href="{{ $links['fr'] }}">
                    <span class="tag">FR</span> Français
                </a>

                <a class="lang-btn b2" data-lang="en" href="{{ $links['en'] }}">
                    <span class="tag">EN</span> English
                </a>

                <a class="lang-btn b3" data-lang="ht" href="{{ $links['ht'] }}">
                    <span class="tag">HT</span> Kreyòl
                </a>

                <a class="lang-btn b4" data-lang="es" href="{{ $links['es'] }}">
                    <span class="tag">ES</span> Español
                </a>
            </div>

            {{-- TEXTES SOUS BOUTONS --}}
            <div class="foot-grid">
                <div class="foot f1">Cliquez sur une langue pour continuer.</div>
                <div class="foot f2">Select a language to continue.</div>
                <div class="foot f3">Klike sou yon lang pou kontinye.</div>
                <div class="foot f4">Seleccione un idioma para continuar.</div>
            </div>
        </div>
    </div>

    {{-- Audio: déclenché seulement au clic/tap --}}
    <audio id="introSound" preload="auto">
        {{-- Mets ton vrai fichier ici si tu veux --}}
        <source src="{{ asset('assets/audio/intro.mp3') }}" type="audio/mpeg">
    </audio>

    <script>
        (() => {
            const audio = document.getElementById('introSound');
            const badge = document.getElementById('soundBadge');
            const card = document.getElementById('welcomeCard');

            if (!audio) return;

            audio.volume = 0.40;
            let armed = true; // only first interaction

            const armAndPlay = () => {
                if (!armed) return;
                armed = false;

                // try play (allowed because user interaction)
                const p = audio.play();
                if (p && typeof p.then === 'function') {
                    p.then(() => {
                        if (badge) {
                            badge.style.opacity = '0';
                            badge.style.transform = 'translateY(-6px)';
                            setTimeout(() => badge.remove(), 220);
                        }
                    }).catch(() => {
                        // if it still fails, keep badge
                        armed = true;
                    });
                }
            };

            // Play sound on FIRST click/tap anywhere on card (or on buttons)
            const onFirstInteraction = (e) => {
                armAndPlay();
            };

            card?.addEventListener('click', onFirstInteraction, {
                once: true
            });
            card?.addEventListener('touchstart', onFirstInteraction, {
                once: true
            });

            // Also: clicking a language button should attempt sound before navigation
            document.querySelectorAll('.lang-btn').forEach(a => {
                a.addEventListener('click', (e) => {
                    // ensure sound is triggered right before leaving
                    armAndPlay();
                }, {
                    passive: true
                });
            });

        })();
    </script>

</body>

</html>