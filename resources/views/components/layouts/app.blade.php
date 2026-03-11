<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Soumission Auto - VIP GPI' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

    @livewireStyles

    <style>
        :root {
            --vip-gold: #c9a050;
            --vip-blue: #0E1030;
        }

        body {
            background-color: #f4f7f6;
            font-family: 'Montserrat', sans-serif;
            padding-top: 80px;
            /* Pour ne pas cacher le contenu sous la barre fixe */
        }

        /* Style de la Top Bar */
        .vip-navbar {
            background-color: #ffffff;
            border-bottom: 3px solid var(--vip-gold);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            height: 72px;
            padding: 0;
        }

        .navbar .container {
            height: 72px;
            /* centre verticalement */
            display: flex;
            align-items: center;
        }


        .advisor-box {
            border-left: 1px solid #eee;
            padding-left: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-end;
        }

        .advisor-name {
            font-weight: 700;
            color: var(--vip-blue);
            font-size: 1rem;
            margin-bottom: 2px;
        }

        .advisor-phone {
            color: var(--vip-gold);
            font-weight: 600;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .advisor-phone:hover {
            color: var(--vip-blue);
            text-decoration: underline;
        }

        .advisor-label {
            font-size: 0.7rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Logo responsive */
        .navbar-brand {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .navbar-brand img {
            height: 44px;
            /* <-- c’est ici le “gros” gain */
            width: auto;
        }

        /* --- Logo premium badge --- */
        .brand-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 6px 10px;
            border-radius: 14px;

            background: linear-gradient(180deg, #0E1030 0%, #090a1f 100%);
            border: 1px solid rgba(201, 160, 80, .55);

            box-shadow: 0 8px 14px rgba(14, 16, 48, .12);
        }

        .brand-badge::before,
        .brand-badge::after {
            content: none !important;
            /* enlève le reflet + inset */
        }

        .navbar-brand img {
            height: 46px;
            /* ↑ lisibilité */
            width: auto;
            display: block;
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, .25));
        }

        @media (max-width:576px) {

            .vip-navbar {
                height: 64px;
            }

            .navbar-brand img {
                height: 38px;
            }

            .navbar .container {
                height: 64px;
            }

            .brand-badge {
                padding: 8px 12px;
                border-radius: 14px;
            }
        }

        @media (max-width: 576px) {
            .advisor-name {
                font-size: 0.9rem;
            }

            .advisor-phone {
                font-size: 0.8rem;
            }

            .navbar-brand img {
                height: 40px;
            }
        }
    </style>
</head>

<body>

    {{-- TOP BAR --}}
    <nav class="navbar fixed-top vip-navbar">
        <div class="container">
            <a class="navbar-brand" href="/" aria-label="VIP GPI">
                <span class="brand-badge">
                    <img src="{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}" alt="VIP GPI Logo">
                </span>
            </a>

            {{-- INFO CONSEILLER À DROITE --}}
            <div class="d-flex align-items-center">
                <div class="advisor-box text-end">
                    {{-- On vérifie si les variables existent, sinon on met des valeurs par défaut --}}
                    <span class="advisor-label">Votre conseiller</span>

                    <div class="advisor-name">
                        <i class="fas fa-user-tie me-1 text-muted"></i>
                        {{ $advisorName ?? 'Conseiller VIP' }}
                    </div>

                    <a href="tel:{{ $advisorPhone ?? '1-800-000-0000' }}" class="advisor-phone">
                        <i class="fas fa-phone-alt me-1"></i>
                        {{ $advisorPhone ?? 'Contactez-nous' }}
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- CONTENU PRINCIPAL --}}
    <main>
        @if(isset($slot))
        {{ $slot }}
        @else
        @yield('content')
        @endif
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts

    {{-- ▸ Capture globale des erreurs JavaScript → SystemLog --}}
    <script>
    (function () {
        var endpoint = '{{ route("log-js-error") }}';
        var token    = document.querySelector('meta[name="csrf-token"]')?.content;
        var count    = 0; // max 3 envois par chargement de page

        function send(type, message, detail) {
            if (!token || count >= 3) return;
            count++;
            var body = Object.assign({ type: type, message: String(message).substring(0, 300), url: window.location.href }, detail);
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(body)
            }).catch(function () {}); // échec silencieux
        }

        // Erreurs JS non rattrapées
        window.addEventListener('error', function (e) {
            // Ignorer les erreurs de ressources (images, scripts externes)
            if (e.target && e.target !== window) return;
            send('js_error', e.message || 'Unknown error', {
                source: (e.filename || '').substring(0, 200),
                line:   e.lineno   || '',
                column: e.colno    || '',
                stack:  (e.error && e.error.stack ? e.error.stack.substring(0, 500) : '')
            });
        }, true);

        // Promesses rejetées non rattrapées
        window.addEventListener('unhandledrejection', function (e) {
            var msg   = (e.reason && e.reason.message) ? e.reason.message : String(e.reason || 'Unhandled promise rejection');
            var stack = (e.reason && e.reason.stack)   ? e.reason.stack.substring(0, 500) : '';
            send('promise_rejection', msg, { stack: stack });
        });
    })();
    </script>
</body>

</html>