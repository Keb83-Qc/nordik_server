<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration 2FA | VIP GPI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: radial-gradient(1200px 800px at 20% 10%, rgba(201, 160, 80, .18), transparent 55%),
                radial-gradient(900px 700px at 90% 30%, rgba(230, 200, 133, .12), transparent 60%),
                linear-gradient(135deg, #0E1030 0%, #1f2354 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, .95);
            border-radius: 22px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .15);
        }
        .login-card::before {
            content: '';
            display: block;
            height: 6px;
            background: linear-gradient(90deg, #c9a050, #e6c885, #c9a050);
        }
        .card-header-vip {
            padding: 26px 26px 18px 26px;
            background: linear-gradient(135deg, #0B0D2A 0%, #13174A 50%, #0B0D2A 100%);
            text-align: center;
            position: relative;
        }
        .card-header-vip::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(500px 140px at 50% 0%, rgba(201, 160, 80, .25), transparent 60%);
            pointer-events: none;
        }
        .logo-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 18px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .12);
            box-shadow: 0 10px 25px rgba(0, 0, 0, .25);
        }
        .logo-wrap img { height: 60px; filter: drop-shadow(0 10px 18px rgba(0,0,0,.35)); }
        .card-body-vip { padding: 28px; }
        .btn-primary-vip {
            background: linear-gradient(180deg, #0E1030, #0B0D2A);
            border: none;
            color: white;
            font-weight: 700;
            transition: all 0.25s ease;
        }
        .btn-primary-vip:hover {
            background: linear-gradient(180deg, #c9a050, #e6c885);
            color: #0B0D2A;
            transform: translateY(-2px);
        }
        .form-control:focus {
            border-color: #c9a050;
            box-shadow: 0 0 0 0.25rem rgba(201, 160, 80, 0.15);
        }
        .qr-box {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 16px;
            padding: 16px;
            display: inline-block;
        }
        .secret-box {
            background: #f8f9fa;
            border: 1px dashed #c9a050;
            border-radius: 10px;
            padding: 10px 16px;
            font-family: monospace;
            font-size: 1.1rem;
            letter-spacing: 3px;
            color: #0B0D2A;
            word-break: break-all;
        }
        .step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c9a050, #e6c885);
            color: #0B0D2A;
            font-weight: 800;
            font-size: .85rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card-header-vip">
            <div class="logo-wrap">
                <img src="{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}" alt="Logo VIP GPI">
            </div>
            <p class="text-white mt-3 mb-0 fw-semibold" style="font-size:.9rem; opacity:.8;">
                <i class="fas fa-shield-halved me-1"></i> Authentification à deux facteurs
            </p>
        </div>

        <div class="card-body-vip">
            <h5 class="fw-bold text-dark mb-1">Configuration de votre 2FA</h5>
            <p class="text-muted small mb-4">Protégez votre compte avec une application d'authentification (Google Authenticator, Authy, etc.)</p>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small border-0 bg-danger bg-opacity-10 text-danger rounded-3 mb-3">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            {{-- Étape 1 : Télécharger l'app --}}
            <div class="d-flex gap-3 align-items-start mb-3">
                <div class="step-badge">1</div>
                <div>
                    <p class="mb-0 fw-semibold text-dark" style="font-size:.9rem;">Téléchargez une application d'authentification</p>
                    <p class="mb-0 text-muted" style="font-size:.8rem;">Google Authenticator, Authy, Microsoft Authenticator…</p>
                </div>
            </div>

            {{-- Étape 2 : Scanner le QR --}}
            <div class="d-flex gap-3 align-items-start mb-3">
                <div class="step-badge">2</div>
                <div class="w-100">
                    <p class="mb-2 fw-semibold text-dark" style="font-size:.9rem;">Scannez ce code QR avec l'application</p>
                    <div class="text-center mb-2">
                        <div class="qr-box">
                            {!! $qrSvg !!}
                        </div>
                    </div>
                    <p class="text-muted text-center mb-1" style="font-size:.75rem;">Ou entrez ce code manuellement :</p>
                    <div class="secret-box text-center">{{ $secret }}</div>
                </div>
            </div>

            {{-- Étape 3 : Confirmer le code --}}
            <div class="d-flex gap-3 align-items-start mb-4">
                <div class="step-badge">3</div>
                <div class="w-100">
                    <p class="mb-2 fw-semibold text-dark" style="font-size:.9rem;">Entrez le code à 6 chiffres affiché dans l'application</p>
                    <form action="{{ route('2fa.enable') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text"
                                   name="code"
                                   class="form-control form-control-lg text-center fw-bold"
                                   placeholder="000 000"
                                   maxlength="6"
                                   inputmode="numeric"
                                   autocomplete="one-time-code"
                                   autofocus
                                   style="letter-spacing: 6px; font-size: 1.4rem;">
                            <button type="submit" class="btn btn-primary-vip px-4">
                                <i class="fas fa-check me-1"></i> Activer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-2">
                <form action="{{ route('logout', ['locale' => app()->getLocale()]) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-secondary text-decoration-none small">
                        <i class="fas fa-arrow-left me-1"></i> Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Format automatique X XX XX X
        document.querySelector('input[name="code"]')?.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').substring(0, 6);
        });
    </script>
</body>
</html>
