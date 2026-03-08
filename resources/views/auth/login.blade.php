<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Important pour AJAX --}}
    <title>Connexion | VIP GPI</title>
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
            padding: 0;
            /* on gère padding par sections */
            background: rgba(255, 255, 255, .95);
            border-radius: 22px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .15);
            backdrop-filter: blur(10px);
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #c9a050, #e6c885, #c9a050);
        }

        /* ✅ Header premium bleu derrière le logo */
        .card-header-vip {
            padding: 26px 26px 18px 26px;
            background: linear-gradient(135deg, #0B0D2A 0%, #13174A 50%, #0B0D2A 100%);
            position: relative;
            text-align: center;
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

        .logo-wrap img {
            height: 74px;
            filter: drop-shadow(0 10px 18px rgba(0, 0, 0, .35));
        }

        /* Section contenu */
        .card-body-vip {
            padding: 26px;
        }

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

        /* petites finitions premium */
        .divider-soft {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0, 0, 0, .15), transparent);
            margin: 18px 0;
        }

        .muted-footer {
            padding: 14px 26px 20px 26px;
            font-size: .78rem;
            color: rgba(0, 0, 0, .45);
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="card-header-vip">
            <div class="logo-wrap">
                <img src="{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}" alt="Logo VIP GPI">
            </div>
        </div>

        <div class="card-body-vip text-center">
            {{-- SECTION LOGIN --}}
            <div id="login-section" class="fade-in">
                <h4 class="mb-1 fw-bold text-dark">Espace Membre</h4>
                <p class="text-muted small mb-4">Connectez-vous pour accéder à votre portail.</p>

                @if ($errors->any())
                <div
                    class="alert alert-danger text-start py-2 fs-6 border-0 bg-danger bg-opacity-10 text-danger rounded-3 mb-3">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                </div>
                @endif

                @if ($errors->any())
  <div class="alert alert-danger text-start">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if (session('status'))
  <div class="alert alert-info">{{ session('status') }}</div>
@endif

                <form action="{{ route('login.post', ['locale' => app()->getLocale()]) }}" method="post">
                    @csrf
                    {{-- Honeypot: champ invisible à laisser vide --}}
                    <div style="position:absolute; left:-9999px; top:-9999px;" aria-hidden="true">
                        <label>Ne pas remplir</label>
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    {{-- Timer: détecte les soumissions trop rapides --}}
                    <input type="hidden" name="form_time" value="{{ time() }}">

                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" id="floatingInput"
                            placeholder="name@example.com" value="{{ old('email') }}" required>
                        <label for="floatingInput">Adresse Email</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="password" name="password" class="form-control" id="floatingPassword"
                            placeholder="Mot de passe" required>
                        <label for="floatingPassword">Mot de passe</label>
                    </div>

                    <button type="submit" class="btn btn-login w-100 py-3 rounded-3 shadow-sm">
                        <i class="fas fa-sign-in-alt me-2"></i> Se connecter
                    </button>

                    <div class="text-center mt-4 pt-3 border-top">
                        <small class="text-muted d-block mb-2">Vous n'avez pas d'accès ?</small>
                        <a href="{{ route('access.request', ['locale' => app()->getLocale()]) }}"
                            class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            Demandez votre accès.
                        </a>
                    </div>
                </form>
            </div>

            {{-- SECTION REGISTER --}}
            <div id="register-section" class="fade-in" style="display: none;">
                <h4 class="mb-3 fw-bold text-dark">Rejoindre l'équipe</h4>
                <p class="text-muted small mb-4">Remplissez ce formulaire pour demander un accès.</p>

                <form id="registerForm">
                    @csrf
                    {{-- Honeypot: champ invisible à laisser vide --}}
                    <div style="position:absolute; left:-9999px; top:-9999px;" aria-hidden="true">
                        <label>Ne pas remplir</label>
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    {{-- Timer: détecte les soumissions trop rapides --}}
                    <input type="hidden" name="form_time" value="{{ time() }}">

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" name="first_name" class="form-control" id="regPr" placeholder="Prénom"
                                    required>
                                <label for="regPr" class="small">Prénom</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" name="last_name" class="form-control" id="regNm" placeholder="Nom"
                                    required>
                                <label for="regNm" class="small">Nom</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" id="regEm" placeholder="Email" required>
                        <label for="regEm">Courriel professionnel</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="text" name="phone" class="form-control" id="regPh" placeholder="Téléphone" required>
                        <label for="regPh">Téléphone</label>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow-sm">
                        <i class="fas fa-paper-plane me-2"></i> Envoyer la demande
                    </button>
                </form>

                <div id="regFeedback" class="mt-3" style="display:none;"></div>

                <div class="mt-4 pt-2 border-top">
                    <button type="button" onclick="toggleForms('login')"
                        class="btn btn-link text-secondary text-decoration-none small">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la connexion
                    </button>
                </div>
            </div>

            <div class="mt-4 text-muted small opacity-50" style="font-size: 0.75rem;">
                &copy; {{ date('Y') }} VIP Services Financiers
            </div>
        </div>

        {{-- MODAL SUCCESS --}}
        <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 p-4 text-center rounded-4 shadow-lg">
                    <div class="mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;">
                            <i class="fas fa-check fa-3x text-success"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">Demande envoyée !</h3>
                    <p class="text-muted mb-4">Nous avons bien reçu votre demande.<br>Un administrateur validera votre
                        compte sous peu.</p>
                    <button type="button" class="btn btn-dark px-5 py-2 fw-bold rounded-pill"
                        onclick="window.location.reload();">Terminer</button>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            function toggleForms(view) {
                const loginBox = document.getElementById('login-section');
                const regBox = document.getElementById('register-section');
                const feedback = document.getElementById('regFeedback');
                if (feedback) feedback.style.display = 'none';

                if (view === 'register') {
                    loginBox.style.display = 'none';
                    regBox.style.display = 'block';
                } else {
                    regBox.style.display = 'none';
                    loginBox.style.display = 'block';
                }
            }
            // Ouvrir automatiquement "Demandez votre accès" si ?register=1
            document.addEventListener('DOMContentLoaded', () => {
                const params = new URLSearchParams(window.location.search);
                if (params.get('register') === '1') {
                    toggleForms('register');
                }
            });

            document.getElementById('registerForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const feedback = document.getElementById('regFeedback');
                const btn = this.querySelector('button[type="submit"]');
                const originalBtnText = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';

                fetch("{{ route('register.ajax', ['locale' => app()->getLocale()]) }}", { // Route Laravel
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                            myModal.show();
                            this.reset();
                        } else {
                            feedback.style.display = 'block';
                            feedback.innerHTML =
                                '<div class="alert alert-danger py-2 small border-0 bg-danger bg-opacity-10 text-danger rounded-3"><i class="fas fa-exclamation-circle me-1"></i> ' +
                                data.message + '</div>';
                            btn.disabled = false;
                            btn.innerHTML = originalBtnText;
                        }
                    })
                    .catch(err => {
                        feedback.style.display = 'block';
                        feedback.innerHTML = '<div class="alert alert-danger py-2 small">Erreur serveur (' + err +
                            ')</div>';
                        btn.disabled = false;
                        btn.innerHTML = originalBtnText;
                    });
            });
        </script>
</body>

</html>
