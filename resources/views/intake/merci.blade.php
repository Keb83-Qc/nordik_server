<!DOCTYPE html>
<html lang="{{ $intake->locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merci — VIP GPI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --vip-navy: #1a2e4a; --vip-gold: #e8b84b; }
        body { background: #f4f6f9; font-family: Arial, sans-serif; }
        .intake-header { background: var(--vip-navy); padding: 18px 24px; display:flex; align-items:center; justify-content:space-between; }
        .intake-header img { height: 40px; object-fit: contain; }
        .advisor-tag { color: var(--vip-gold); font-size: 13px; font-weight: 700; }
        .merci-card { background: #fff; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,.08); padding: 48px 32px; border-top: 4px solid var(--vip-gold); text-align: center; }
        .check-icon { width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #28a745, #20c997); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
        .check-icon i { color: #fff; font-size: 2rem; }
    </style>
</head>
<body>
<header class="intake-header">
    <img src="{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}" alt="VIP GPI">
    <div class="advisor-tag"><i class="fas fa-user-tie me-1"></i>{{ $advisor->first_name }} {{ $advisor->last_name }}</div>
</header>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-7 col-lg-5">
            <div class="merci-card">
                <div class="check-icon"><i class="fas fa-check"></i></div>

                <h1 class="h3 fw-bold mb-3" style="color:var(--vip-navy);">
                    @if($intake->locale === 'en') Thank you!
                    @elseif($intake->locale === 'es') ¡Gracias!
                    @elseif($intake->locale === 'ht') Mèsi!
                    @else Merci !
                    @endif
                </h1>

                <p class="text-muted mb-4">
                    @if($intake->locale === 'en')
                        Your financial profile has been received. Your advisor <strong>{{ $advisor->first_name }} {{ $advisor->last_name }}</strong> will contact you shortly.
                    @elseif($intake->locale === 'es')
                        Su perfil financiero fue recibido. Su asesor <strong>{{ $advisor->first_name }} {{ $advisor->last_name }}</strong> se pondrá en contacto con usted en breve.
                    @elseif($intake->locale === 'ht')
                        Pwofil finansye ou a resevwa. Konseyè ou <strong>{{ $advisor->first_name }} {{ $advisor->last_name }}</strong> ap kontakte ou byento.
                    @else
                        Votre profil financier a bien été reçu. Votre conseiller <strong>{{ $advisor->first_name }} {{ $advisor->last_name }}</strong> vous contactera sous peu.
                    @endif
                </p>

                @if($advisor->phone)
                <a href="tel:{{ $advisor->phone }}" class="btn btn-outline-secondary">
                    <i class="fas fa-phone me-1"></i> {{ $advisor->phone }}
                </a>
                @endif
            </div>
            <div class="text-center mt-3" style="font-size:11px;color:#aaa;">Propulsé par <strong>VIP GPI</strong></div>
        </div>
    </div>
</div>
</body>
</html>
