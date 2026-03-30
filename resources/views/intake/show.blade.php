<!DOCTYPE html>
<html lang="{{ $intake->locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil financier — VIP GPI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @livewireStyles

    <style>
        :root {
            --vip-navy: #1a2e4a;
            --vip-gold:  #e8b84b;
        }
        body { background: #f4f6f9; font-family: 'Arial', sans-serif; }

        .intake-header {
            background: var(--vip-navy);
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
        }
        .intake-header img { height: 40px; object-fit: contain; }
        .advisor-tag {
            color: var(--vip-gold);
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .intake-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,.08);
            overflow: hidden;
            border-top: 4px solid var(--vip-gold);
        }
        .intake-card-body { padding: 32px 28px; }
        @media (max-width: 576px) { .intake-card-body { padding: 24px 18px; } }

        .intake-step-title {
            color: var(--vip-navy);
            font-size: 1.3rem;
            font-weight: 800;
        }

        .intake-btn-primary {
            background: var(--vip-navy);
            color: #fff;
            border: 2px solid var(--vip-gold);
            font-weight: 700;
            padding: .55rem 1.4rem;
            border-radius: 8px;
            transition: all .2s;
        }
        .intake-btn-primary:hover { background: var(--vip-gold); color: var(--vip-navy); }

        /* Pills pour sélection */
        .intake-pill {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 999px;
            border: 2px solid #dee2e6;
            font-size: 14px;
            cursor: pointer;
            transition: all .18s;
            font-weight: 600;
            background: #fff;
            color: #444;
        }
        .intake-pill:hover { border-color: var(--vip-gold); color: var(--vip-navy); }
        .intake-pill.active { background: var(--vip-navy); color: #fff; border-color: var(--vip-navy); }

        /* Code d'accès */
        .access-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,.08);
            padding: 40px 32px;
            border-top: 4px solid var(--vip-gold);
            text-align: center;
        }
        .access-card .lock-icon { font-size: 2.5rem; color: var(--vip-gold); margin-bottom: 16px; }
        .access-input { font-size: 1.3rem; font-weight: 700; letter-spacing: .2em; text-align: center; text-transform: uppercase; }
    </style>
</head>
<body>

{{-- En-tête --}}
<header class="intake-header">
    <img src="{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}" alt="VIP GPI">
    <div class="advisor-tag">
        <i class="fas fa-user-tie"></i>
        {{ $advisor->first_name }} {{ $advisor->last_name }}
    </div>
</header>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            @if(!$verified)
                {{-- ─── Formulaire de code d'accès ──────────────────────── --}}
                <div class="access-card">
                    <div class="lock-icon"><i class="fas fa-lock"></i></div>
                    <h1 class="h4 fw-bold mb-2" style="color:var(--vip-navy);">
                        @if($intake->locale === 'en') Enter your access code
                        @elseif($intake->locale === 'es') Ingrese su código de acceso
                        @elseif($intake->locale === 'ht') Antre kòd aksè ou
                        @else Entrez votre code d'accès
                        @endif
                    </h1>
                    <p class="text-muted mb-4" style="font-size:14px;">
                        @if($intake->locale === 'en') Your advisor has sent you a unique access code by email.
                        @elseif($intake->locale === 'es') Su asesor le ha enviado un código de acceso único por correo electrónico.
                        @elseif($intake->locale === 'ht') Konseyè ou voye yon kòd aksè inik pa imèl.
                        @else Votre conseiller vous a envoyé un code d'accès unique par courriel.
                        @endif
                    </p>

                    @if($errors->any())
                        <div class="alert alert-danger py-2 mb-3" style="font-size:14px;">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('intake.verify', ['advisorSlug' => $advisor->slug, 'token' => $intake->token]) }}">
                        @csrf
                        <input type="text"
                               name="access_code"
                               class="form-control access-input mb-3"
                               placeholder="XXX-XXX"
                               maxlength="10"
                               autocomplete="off"
                               autofocus
                               required>
                        <button type="submit" class="btn intake-btn-primary w-100 py-2">
                            @if($intake->locale === 'en') Access my form
                            @elseif($intake->locale === 'es') Acceder a mi formulario
                            @elseif($intake->locale === 'ht') Accede fòm mwen
                            @else Accéder à mon formulaire
                            @endif
                        </button>
                    </form>

                    <p class="text-muted mt-4 mb-0" style="font-size:12px;">
                        @if($intake->locale === 'en') No code? Contact your advisor at {{ $advisor->email }}
                        @elseif($intake->locale === 'es') ¿Sin código? Contacte a su asesor en {{ $advisor->email }}
                        @elseif($intake->locale === 'ht') Pa gen kòd? Kontakte konseyè ou nan {{ $advisor->email }}
                        @else Pas de code? Contactez votre conseiller à {{ $advisor->email }}
                        @endif
                    </p>
                </div>

            @else
                {{-- ─── Wizard Livewire ──────────────────────────────────── --}}
                <div class="intake-card">
                    <div class="intake-card-body">
                        @livewire('intake-wizard', ['intakeId' => $intake->id, 'locale' => $intake->locale])
                    </div>
                </div>
            @endif

            {{-- Powered by --}}
            <div class="text-center mt-3" style="font-size:11px;color:#aaa;">
                Propulsé par <strong>VIP GPI</strong>
            </div>

        </div>
    </div>
</div>

@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    function sendJsError(data) {
        try { fetch('/log-js-error', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(data), keepalive: true }); } catch (_) {}
    }
    window.onerror = function (msg, src, line, col, err) {
        sendJsError({ type: 'js_error', message: String(msg).slice(0,300), source: String(src||'').slice(0,300), line: line, column: col, stack: err?.stack ? String(err.stack).slice(0,600) : '', url: location.href.slice(0,300) });
        return false;
    };
    window.addEventListener('unhandledrejection', function (e) {
        var r = e.reason, m = r instanceof Error ? r.message : String(r);
        sendJsError({ type: 'unhandled_promise', message: String(m).slice(0,300), source: '', line: '', column: '', stack: r instanceof Error && r.stack ? String(r.stack).slice(0,600) : '', url: location.href.slice(0,300) });
    });
})();
</script>
</body>
</html>
