<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle soumission</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; margin: 0; }
        .container { max-width: 680px; margin: auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 6px 18px rgba(0,0,0,.08); }
        .header { background: {{ $portal->primary_color ?? '#1a2e4a' }}; padding: 24px; text-align: center; }
        .header img { max-height: 70px; max-width: 200px; object-fit: contain; }
        .header-name { color: {{ $portal->secondary_color ?? '#e8b84b' }}; font-size: 20px; font-weight: 800; letter-spacing: 1px; }
        .banner { background: {{ $portal->secondary_color ?? '#e8b84b' }}; color: {{ $portal->primary_color ?? '#1a2e4a' }}; padding: 10px 24px; font-size: 13px; font-weight: 700; text-align: center; }
        .content { padding: 24px; }
        .section { margin-bottom: 16px; border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden; }
        .section-title { background: #f8f9fa; padding: 10px 14px; font-weight: 700; font-size: 13px; color: {{ $portal->primary_color ?? '#1a2e4a' }}; border-bottom: 1px solid #e9ecef; text-transform: uppercase; letter-spacing: .5px; }
        .section-body { padding: 14px; }
        .info-row { margin: 7px 0; font-size: 14px; }
        .label { font-weight: 700; color: #444; }
        .value { color: #222; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        @media (max-width: 580px) { .grid { grid-template-columns: 1fr; } }
        .footer { background: #f8f9fa; border-top: 1px solid #e9ecef; padding: 16px 24px; text-align: center; font-size: 12px; color: #888; }
        .footer strong { color: #555; }
    </style>
</head>
<body>
@php
    $d = $submission->data ?? [];
    $type = strtolower($submission->type ?? '');

    $v = fn(string $key, $fallback = '—') =>
        (($d[$key] ?? null) !== null && ($d[$key] ?? '') !== '') ? $d[$key] : $fallback;

    $fmtMoney = function ($val) {
        if ($val === null || $val === '' || !is_numeric($val)) return '—';
        return number_format((float) $val, 0, ',', ' ') . ' $';
    };

    $advisorName = $advisor
        ? $advisor->first_name . ' ' . $advisor->last_name
        : 'Non assigné';

    $advisorEmail = $advisor?->email ?? '—';
    $advisorPhone = $advisor?->phone ?? '—';

    $typeLabel = match ($type) {
        'auto'       => 'Automobile',
        'habitation' => 'Habitation',
        'bundle'     => 'Auto + Habitation',
        default      => ucfirst($type ?: 'Soumission'),
    };
@endphp

<div class="container">

    {{-- En-tête avec logo partenaire --}}
    <div class="header">
        @if($portal->logo_path)
            <img src="{{ asset('storage/' . $portal->logo_path) }}" alt="{{ $portal->name }}">
        @else
            <div class="header-name">{{ $portal->name }}</div>
        @endif
    </div>

    <div class="banner">
        Nouvelle soumission — {{ $typeLabel }}
    </div>

    <div class="content">

        {{-- Client --}}
        <div class="section">
            <div class="section-title">Informations client</div>
            <div class="section-body">
                <div class="grid">
                    <div class="info-row"><span class="label">Prénom :</span> <span class="value">{{ $v('first_name') }}</span></div>
                    <div class="info-row"><span class="label">Nom :</span> <span class="value">{{ $v('last_name') }}</span></div>
                    <div class="info-row"><span class="label">Téléphone :</span> <span class="value">{{ $v('phone') }}</span></div>
                    <div class="info-row"><span class="label">Email :</span> <span class="value">{{ $v('email') }}</span></div>
                    @if($v('dob', null))
                    <div class="info-row"><span class="label">Date de naissance :</span> <span class="value">{{ $v('dob') }}</span></div>
                    @endif
                    @if($v('address', null))
                    <div class="info-row"><span class="label">Adresse :</span> <span class="value">{{ $v('address') }}</span></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Détails selon le type --}}
        @if($type === 'auto' || $type === 'bundle')
        <div class="section">
            <div class="section-title">Véhicule</div>
            <div class="section-body">
                <div class="grid">
                    <div class="info-row"><span class="label">Année :</span> <span class="value">{{ $v('year') }}</span></div>
                    <div class="info-row"><span class="label">Marque :</span> <span class="value">{{ $v('make') }}</span></div>
                    <div class="info-row"><span class="label">Modèle :</span> <span class="value">{{ $v('model') }}</span></div>
                    <div class="info-row"><span class="label">Usage :</span> <span class="value">{{ $v('usage') }}</span></div>
                    @if($v('km', null))
                    <div class="info-row"><span class="label">Kilométrage annuel :</span> <span class="value">{{ $v('km') }} km</span></div>
                    @endif
                    @if($v('financing', null))
                    <div class="info-row"><span class="label">Financement :</span> <span class="value">{{ $v('financing') }}</span></div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($type === 'habitation' || $type === 'bundle')
        <div class="section">
            <div class="section-title">Habitation</div>
            <div class="section-body">
                @php
                    $h = is_array($d['habitation'] ?? null) ? $d['habitation'] : $d;
                    $hv = fn($k, $fb='—') => ($h[$k] ?? null) !== null && ($h[$k] ?? '') !== '' ? $h[$k] : $fb;
                @endphp
                <div class="grid">
                    <div class="info-row"><span class="label">Type :</span> <span class="value">{{ $hv('dwelling_type', $v('dwelling_type')) }}</span></div>
                    <div class="info-row"><span class="label">Occupation :</span> <span class="value">{{ $hv('occupancy', $v('occupancy')) }}</span></div>
                    @if($hv('year_built', null) || $v('year_built', null))
                    <div class="info-row"><span class="label">Année construction :</span> <span class="value">{{ $hv('year_built', $v('year_built')) }}</span></div>
                    @endif
                    @if($hv('coverage', null) || $v('coverage', null))
                    <div class="info-row"><span class="label">Couverture souhaitée :</span> <span class="value">{{ $fmtMoney($hv('coverage', $v('coverage', null))) }}</span></div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Conseiller --}}
        <div class="section">
            <div class="section-title">Conseiller assigné</div>
            <div class="section-body">
                <div class="grid">
                    <div class="info-row"><span class="label">Nom :</span> <span class="value">{{ $advisorName }}</span></div>
                    <div class="info-row"><span class="label">Email :</span> <span class="value">{{ $advisorEmail }}</span></div>
                    @if($advisorPhone !== '—')
                    <div class="info-row"><span class="label">Téléphone :</span> <span class="value">{{ $advisorPhone }}</span></div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <div class="footer">
        <strong>{{ $portal->name }}</strong> &mdash; propulsé par <strong>VIP GPI</strong><br>
        Soumission reçue le {{ now()->format('d/m/Y à H:i') }}
    </div>

</div>
</body>
</html>
