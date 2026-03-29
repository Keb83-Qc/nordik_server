<!DOCTYPE html>
<html lang="{{ session('locale', 'fr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $portal->getConsentTitle() ?: $portal->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --portal-primary:   {{ $portal->primary_color   ?? '#1a2e4a' }};
            --portal-secondary: {{ $portal->secondary_color ?? '#e8b84b' }};
        }

        body {
            background-color: #f4f6f9;
            font-family: 'Montserrat', sans-serif;
        }

        .portal-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.07);
            overflow: hidden;
            border-top: 5px solid var(--portal-secondary);
        }

        .portal-header {
            background: linear-gradient(160deg, var(--portal-primary) 0%, color-mix(in srgb, var(--portal-primary) 80%, black) 100%);
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .portal-logo {
            max-height: 80px;
            max-width: 220px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.25));
        }

        .portal-name {
            color: var(--portal-secondary);
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }

        .consent-body {
            padding: 2rem;
        }

        .consent-title {
            color: var(--portal-primary);
            font-weight: 700;
        }

        .consent-text {
            color: #555;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .btn-quote-type {
            background: var(--portal-primary);
            border: 2px solid var(--portal-secondary);
            color: #fff;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.25s;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }

        .btn-quote-type:hover {
            background: var(--portal-secondary);
            color: var(--portal-primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .btn-quote-type i {
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .divider {
            border-color: #e9ecef;
        }

        .powered-by {
            font-size: 0.75rem;
            color: #aaa;
        }
    </style>
</head>
<body>

<div class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="row justify-content-center w-100">
        <div class="col-12 col-sm-10 col-md-7 col-lg-6 col-xl-5">

            <div class="portal-card">

                {{-- ─── En-tête avec logo/nom du partenaire ─── --}}
                <div class="portal-header">
                    @if($portal->logo_path)
                        <img src="{{ asset('storage/' . $portal->logo_path) }}"
                             alt="{{ $portal->name }}"
                             class="portal-logo">
                    @else
                        <div class="portal-name">{{ $portal->name }}</div>
                    @endif
                </div>

                {{-- ─── Corps ─── --}}
                <div class="consent-body">

                    {{-- Titre du consentement --}}
                    @php $title = $portal->getConsentTitle(); @endphp
                    @if($title)
                        <h1 class="consent-title h4 mb-3">{{ $title }}</h1>
                    @endif

                    {{-- Texte du consentement --}}
                    @php $text = $portal->getConsentText(); @endphp
                    @if($text)
                        <div class="consent-text mb-4">{!! $text !!}</div>
                        <hr class="divider mb-4">
                    @endif

                    {{-- ─── Boutons de sélection du type de soumission ─── --}}
                    <p class="fw-bold mb-3" style="color: var(--portal-primary);">
                        @if(session('locale') === 'en') Choose your quote type
                        @elseif(session('locale') === 'es') Elija el tipo de cotización
                        @elseif(session('locale') === 'ht') Chwazi kalite soumisyon an
                        @else Choisissez votre type de soumission
                        @endif
                    </p>

                    @php
                        $acceptLabel = match(session('locale', 'fr')) {
                            'en' => "I accept",
                            'es' => "Acepto",
                            'ht' => "Mwen aksepte",
                            default => "J'accepte",
                        };
                    @endphp

                    <div class="d-flex flex-column gap-3">
                        @foreach($activeTypes as $quoteType)
                            <form method="POST"
                                  action="{{ route('portal.accept', ['locale' => session('locale', 'fr'), 'portalSlug' => $portal->slug]) }}">
                                @csrf
                                <input type="hidden" name="quote_type" value="{{ $quoteType->slug }}">

                                <button type="submit" class="btn-quote-type">
                                    @switch($quoteType->slug)
                                        @case('auto')        <i class="fas fa-car"></i>         @break
                                        @case('habitation')  <i class="fas fa-home"></i>        @break
                                        @case('bundle')      <i class="fas fa-layer-group"></i> @break
                                        @case('commercial')  <i class="fas fa-building"></i>    @break
                                        @default             <i class="fas fa-file-alt"></i>
                                    @endswitch

                                    <span>
                                        {{ $acceptLabel }}
                                        <i class="fas fa-check" style="font-size:0.85em;"></i>,
                                        {{ $quoteType->getLabel(session('locale', 'fr')) }}
                                    </span>
                                </button>
                            </form>
                        @endforeach
                    </div>

                    {{-- Powered by --}}
                    <div class="text-center mt-4 powered-by">
                        Propulsé par <strong>VIP GPI</strong>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
