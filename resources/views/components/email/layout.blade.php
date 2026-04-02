<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'VIP GPI' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 24px 16px;
        }

        .email-wrapper {
            max-width: 680px;
            margin: auto;
        }

        /* ── Header ── */
        .email-header {
            background: {{ $headerColor ?? '#0E1030' }};
            border-radius: 12px 12px 0 0;
            padding: 24px 32px 20px;
            text-align: center;
        }

        .email-header img.logo {
            max-height: 56px;
            max-width: 200px;
            width: auto;
            display: block;
            margin: 0 auto 12px;
            object-fit: contain;
        }

        .email-header-title {
            color: {{ $accentColor ?? '#C9A050' }};
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .email-header-divider {
            width: 48px;
            height: 2px;
            background: {{ $accentColor ?? '#C9A050' }};
            border-radius: 2px;
            margin: 10px auto 0;
        }

        /* ── Body ── */
        .email-body {
            background: #ffffff;
            padding: 28px 32px;
        }

        /* ── Footer ── */
        .email-footer {
            background: {{ $headerColor ?? '#0E1030' }};
            border-radius: 0 0 12px 12px;
            padding: 16px 32px;
            text-align: center;
        }

        .email-footer p {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.6;
        }

        .email-footer a {
            color: {{ $accentColor ?? '#C9A050' }};
            text-decoration: none;
        }

        @media (max-width: 580px) {
            .email-body { padding: 20px 18px; }
            .email-header { padding: 20px 18px 16px; }
        }
    </style>

    {{ $styles ?? '' }}
</head>
<body>
    <div class="email-wrapper">

        <div class="email-header">
            @if(!empty($logoUrl))
                <img class="logo" src="{{ $logoUrl }}" alt="{{ $fromName ?? 'VIP GPI' }}">
            @endif
            @if(!empty($headerTitle))
                <div class="email-header-title">{{ $headerTitle }}</div>
            @endif
            <div class="email-header-divider"></div>
        </div>

        <div class="email-body">
            {{ $slot }}
        </div>

        <div class="email-footer">
            <p>
                {{ $footerText ?? 'VIP Gestion de Patrimoine & Investissement Inc.' }}
                &copy; {{ date('Y') }}<br>
                <a href="{{ $siteUrl ?? config('app.url') }}">{{ parse_url(config('app.url'), PHP_URL_HOST) }}</a>
            </p>
        </div>

    </div>
</body>
</html>
