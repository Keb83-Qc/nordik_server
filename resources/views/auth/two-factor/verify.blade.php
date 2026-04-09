<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification 2FA | VIP GPI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: radial-gradient(1200px 800px at 20% 10%, rgba(201, 160, 80, .18), transparent 55%),
                radial-gradient(900px 700px at 90% 30%, rgba(230, 200, 133, .12), transparent 60%),
                linear-gradient(135deg, #0E1030 0%, #1f2354 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
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
        .shield-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(201,160,80,.15), rgba(230,200,133,.2));
            border: 2px solid rgba(201,160,80,.4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
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

        <div class="card-body-vip text-center">
            <div class="shield-icon mx-auto">
                <i class="fas fa-mobile-screen-button fa-2x" style="color:#c9a050;"></i>
            </div>

            <h5 class="fw-bold text-dark mb-1">Vérification requise</h5>
            <p class="text-muted small mb-4">
                Entrez le code à 6 chiffres affiché dans votre application d'authentification (Google Authenticator, Authy…)
            </p>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small border-0 bg-danger bg-opacity-10 text-danger rounded-3 mb-3 text-start">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('2fa.check') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <input type="text"
                           name="code"
                           class="form-control form-control-lg text-center fw-bold"
                           placeholder="000 000"
                           maxlength="6"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           autofocus
                           style="letter-spacing: 8px; font-size: 1.6rem; border-radius: 12px;">
                </div>
                <button type="submit" class="btn btn-primary-vip w-100 py-3 rounded-3">
                    <i class="fas fa-unlock me-2"></i> Vérifier et accéder
                </button>
            </form>

            <div class="mt-4 pt-3 border-top">
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
        document.querySelector('input[name="code"]')?.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').substring(0, 6);
        });
    </script>
</body>
</html>
