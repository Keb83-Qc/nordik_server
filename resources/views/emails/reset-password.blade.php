<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 30px 16px;
        }

        .wrapper {
            max-width: 580px;
            margin: auto;
        }

        /* ── Header ── */
        .header {
            background: #0E1030;
            border-radius: 12px 12px 0 0;
            padding: 28px 32px 22px;
            text-align: center;
        }

        .header img {
            height: 52px;
            width: auto;
            display: block;
            margin: 0 auto 14px;
        }

        .header-divider {
            width: 48px;
            height: 2px;
            background: linear-gradient(90deg, #C9A050, #e8c97a);
            border-radius: 2px;
            margin: 0 auto;
        }

        /* ── Body ── */
        .body {
            background: #ffffff;
            padding: 32px;
        }

        .greeting {
            font-size: 20px;
            font-weight: 700;
            color: #0E1030;
            margin-bottom: 14px;
        }

        .text {
            font-size: 15px;
            color: #444;
            line-height: 1.65;
            margin-bottom: 16px;
        }

        /* ── Button ── */
        .btn-wrap {
            text-align: center;
            margin: 28px 0;
        }

        .btn {
            display: inline-block;
            padding: 14px 36px;
            background: linear-gradient(135deg, #C9A050, #e0b86a);
            color: #0E1030 !important;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            border-radius: 8px;
            letter-spacing: .3px;
        }

        /* ── Info box ── */
        .info-box {
            background: #fafafa;
            border: 1px solid #e8e8e8;
            border-left: 3px solid #C9A050;
            border-radius: 6px;
            padding: 14px 16px;
            font-size: 13px;
            color: #666;
            line-height: 1.55;
            margin-bottom: 20px;
        }

        /* ── Fallback link ── */
        .fallback {
            font-size: 12px;
            color: #999;
            line-height: 1.6;
            border-top: 1px solid #eee;
            padding-top: 18px;
            margin-top: 8px;
        }

        .fallback a {
            color: #0E1030;
            word-break: break-all;
        }

        /* ── Footer ── */
        .footer {
            background: #0E1030;
            border-radius: 0 0 12px 12px;
            padding: 18px 32px;
            text-align: center;
        }

        .footer p {
            font-size: 11px;
            color: rgba(255,255,255,.5);
            line-height: 1.6;
        }

        .footer a {
            color: #C9A050;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        <div class="header">
            <img src="https://vipgpi.ca/assets/img/VIP_Logo_Gold_Gradient10.png" alt="VIP GPI">
            <div class="header-divider"></div>
        </div>

        <div class="body">
            <p class="greeting">Réinitialisation de mot de passe</p>

            <p class="text">
                Vous recevez ce message parce qu'une demande de réinitialisation de mot de passe
                a été soumise pour le compte associé à <strong>{{ $email }}</strong>.
            </p>

            <p class="text">
                Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
                Ce lien est valide pendant <strong>60 minutes</strong>.
            </p>

            <div class="btn-wrap">
                <a href="{{ $url }}" class="btn">Réinitialiser mon mot de passe</a>
            </div>

            <div class="info-box">
                Si vous n'avez pas demandé de réinitialisation, aucune action n'est requise.
                Votre mot de passe actuel reste inchangé.
            </div>

            <div class="fallback">
                Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :<br>
                <a href="{{ $url }}">{{ $url }}</a>
            </div>
        </div>

        <div class="footer">
            <p>
                VIP Gestion de Patrimoine &amp; Investissement Inc. &copy; {{ date('Y') }}<br>
                <a href="https://vipgpi.ca">vipgpi.ca</a>
            </p>
        </div>

    </div>
</body>
</html>
