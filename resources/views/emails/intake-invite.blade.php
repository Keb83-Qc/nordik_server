<!DOCTYPE html>
<html lang="{{ $intake->locale }}">
<head>
<meta charset="UTF-8">
<title>Votre profil financier — VIP GPI</title>
<style>
body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; margin: 0; }
.container { max-width: 620px; margin: auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 6px 18px rgba(0,0,0,.08); }
.header { background: #1a2e4a; padding: 24px; text-align: center; }
.header img { height: 48px; object-fit: contain; }
.banner { background: #e8b84b; color: #1a2e4a; padding: 12px 24px; text-align: center; font-weight: 800; font-size: 15px; }
.content { padding: 28px 32px; }
h1 { color: #1a2e4a; font-size: 20px; margin-top: 0; }
p { color: #444; line-height: 1.6; font-size: 14px; }
.code-box { background: #f8f9fa; border: 2px solid #e8b84b; border-radius: 10px; padding: 18px 24px; text-align: center; margin: 20px 0; }
.code-label { font-size: 12px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 1px; }
.code-value { font-size: 2.2rem; font-weight: 900; color: #1a2e4a; letter-spacing: .3em; margin-top: 4px; }
.btn-link { display: inline-block; background: #1a2e4a; color: #fff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 14px; border: 2px solid #e8b84b; margin-top: 8px; }
.footer { background: #f8f9fa; border-top: 1px solid #eee; padding: 16px 24px; text-align: center; font-size: 12px; color: #999; }
</style>
</head>
<body>
<div class="container">

    <div class="header">
        <img src="{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}" alt="VIP GPI">
    </div>

    @php $advisor = $intake->advisor; @endphp

    <div class="banner">
        @if($intake->locale === 'en') Financial Profile Request
        @elseif($intake->locale === 'es') Solicitud de Perfil Financiero
        @elseif($intake->locale === 'ht') Demann Pwofil Finansye
        @else Demande de profil financier
        @endif
    </div>

    <div class="content">

        @if($intake->client_first_name)
        <h1>
            @if($intake->locale === 'en') Hello {{ $intake->client_first_name }},
            @elseif($intake->locale === 'es') Hola {{ $intake->client_first_name }},
            @elseif($intake->locale === 'ht') Bonjou {{ $intake->client_first_name }},
            @else Bonjour {{ $intake->client_first_name }},
            @endif
        </h1>
        @else
        <h1>
            @if($intake->locale === 'en') Hello,
            @elseif($intake->locale === 'es') Hola,
            @elseif($intake->locale === 'ht') Bonjou,
            @else Bonjour,
            @endif
        </h1>
        @endif

        <p>
            @if($intake->locale === 'en')
                Your advisor <strong>{{ $advisor->first_name }} {{ $advisor->last_name }}</strong> has shared a financial profile form with you. It only takes about 5 minutes to complete and will allow your advisor to better prepare for your meeting.
            @elseif($intake->locale === 'es')
                Su asesor <strong>{{ $advisor->first_name }} {{ $advisor->last_name }}</strong> le ha compartido un formulario de perfil financiero. Solo toma unos 5 minutos completarlo y permitirá a su asesor prepararse mejor para su reunión.
            @elseif($intake->locale === 'ht')
                Konseyè ou <strong>{{ $advisor->first_name }} {{ $advisor->last_name }}</strong> pataje yon fòm pwofil finansye avèk ou. Li pran sèlman 5 minit pou ranpli epi li pral pèmèt konseyè ou prepare pi byen pou reyinyon ou a.
            @else
                Votre conseiller <strong>{{ $advisor->first_name }} {{ $advisor->last_name }}</strong> vous a partagé un formulaire de profil financier. Il ne prend que 5 minutes à remplir et permettra à votre conseiller de mieux se préparer pour votre rencontre.
            @endif
        </p>

        {{-- Code d'accès --}}
        <div class="code-box">
            <div class="code-label">
                @if($intake->locale === 'en') Your access code
                @elseif($intake->locale === 'es') Su código de acceso
                @elseif($intake->locale === 'ht') Kòd aksè ou
                @else Votre code d'accès
                @endif
            </div>
            <div class="code-value">{{ $intake->access_code }}</div>
        </div>

        <p style="text-align:center;">
            @if($intake->locale === 'en') Click the button below to access your form:
            @elseif($intake->locale === 'es') Haga clic en el botón a continuación para acceder a su formulario:
            @elseif($intake->locale === 'ht') Klike bouton ki anba a pou aksede fòm ou:
            @else Cliquez sur le bouton ci-dessous pour accéder à votre formulaire :
            @endif
        </p>

        <div style="text-align:center;">
            <a href="{{ $intake->url }}" class="btn-link">
                @if($intake->locale === 'en') Access my form →
                @elseif($intake->locale === 'es') Acceder a mi formulario →
                @elseif($intake->locale === 'ht') Aksede fòm mwen →
                @else Accéder à mon formulaire →
                @endif
            </a>
        </div>

        <p style="margin-top:24px;font-size:12px;color:#999;word-break:break-all;">
            @if($intake->locale === 'en') Or copy this link: {{ $intake->url }}
            @elseif($intake->locale === 'es') O copie este enlace: {{ $intake->url }}
            @elseif($intake->locale === 'ht') Oubyen kopye lyen sa a: {{ $intake->url }}
            @else Ou copiez ce lien : {{ $intake->url }}
            @endif
        </p>

    </div>

    <div class="footer">
        VIP GPI &mdash; {{ $advisor->first_name }} {{ $advisor->last_name }}
        @if($advisor->email) &mdash; {{ $advisor->email }} @endif
        @if($advisor->phone) &mdash; {{ $advisor->phone }} @endif
    </div>
</div>
</body>
</html>
