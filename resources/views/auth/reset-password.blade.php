<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe | VIP GPI</title>
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
            max-width: 460px;
            background: rgba(255, 255, 255, .95);
            border-radius: 22px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .15);
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, #c9a050, #e6c885, #c9a050);
        }

        .card-header-vip {
            padding: 26px 26px 18px 26px;
            background: linear-gradient(135deg, #0B0D2A 0%, #13174A 50%, #0B0D2A 100%);
            text-align: center;
        }

        .logo-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 18px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .12);
        }

        .logo-wrap img { height: 74px; }

        .card-body-vip { padding: 26px; }

        .btn-login {
            background: linear-gradient(180deg, #0E1030, #0B0D2A);
            border: none;
            color: white;
            font-weight: 700;
            transition: all 0.25s ease;
        }

        .btn-login:hover {
            background: linear-gradient(180deg, #c9a050, #e6c885);
            color: #0B0D2A;
            transform: translateY(-2px);
        }

        .form-control:focus {
            border-color: #c9a050;
            box-shadow: 0 0 0 0.25rem rgba(201, 160, 80, 0.15);
        }
    </style>
</head>

<body>
    <div class="login-card position-relative">
        <div class="card-header-vip">
            <div class="logo-wrap">
                <img src="{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}" alt="Logo VIP GPI">
            </div>
        </div>

        <div class="card-body-vip">
            <h4 class="mb-1 fw-bold text-dark text-center">Nouveau mot de passe</h4>
            <p class="text-muted small mb-4 text-center">Choisissez un nouveau mot de passe sécurisé.</p>

            @if ($errors->any())
                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 mb-3 py-2 fs-6">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.store', ['locale' => app()->getLocale()]) }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="floatingEmail"
                        placeholder="name@example.com" value="{{ old('email', $email ?? '') }}" required>
                    <label for="floatingEmail">Adresse courriel</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" name="password" class="form-control" id="floatingPassword"
                        placeholder="Nouveau mot de passe" required minlength="8">
                    <label for="floatingPassword">Nouveau mot de passe</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" name="password_confirmation" class="form-control" id="floatingConfirm"
                        placeholder="Confirmer le mot de passe" required minlength="8">
                    <label for="floatingConfirm">Confirmer le mot de passe</label>
                </div>

                <button type="submit" class="btn btn-login w-100 py-3 rounded-3 shadow-sm">
                    <i class="fas fa-lock me-2"></i> Réinitialiser le mot de passe
                </button>
            </form>

            <div class="mt-4 text-muted text-center opacity-50" style="font-size: 0.75rem;">
                &copy; {{ date('Y') }} VIP Services Financiers
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
