@php
    $emailSettings = app(\App\Settings\EmailSettings::class);

    $d = $submission->data ?? [];
    $type = strtolower($submission->type ?? '');

    // Couleurs du portail (priorité au portail, fallback sur les settings globaux)
    $headerColor  = $portal->primary_color   ?? $emailSettings->partner_fallback_color;
    $accentColor  = $portal->secondary_color ?? $emailSettings->global_accent_color;
    $headerTitle  = $emailSettings->partner_header_title;

    // Logo du portail (priorité) sinon logo global géré dans Filament
    $logoUrl = $portal->logo_path
        ? asset('storage/' . $portal->logo_path)
        : $emailSettings->global_logo_url;

    $v = fn(string $key, $fallback = '—') =>
        (($d[$key] ?? null) !== null && ($d[$key] ?? '') !== '') ? $d[$key] : $fallback;

    $fmtMoney = function ($val) {
        if ($val === null || $val === '' || !is_numeric($val)) return '—';
        return number_format((float) $val, 0, ',', ' ') . ' $';
    };

    $advisorName  = $advisor ? $advisor->first_name . ' ' . $advisor->last_name : 'Non assigné';
    $advisorEmail = $advisor?->email ?? '—';
    $advisorPhone = $advisor?->phone ?? '—';

    $typeLabel = match ($type) {
        'auto'       => 'Automobile',
        'habitation' => 'Habitation',
        'bundle'     => 'Auto + Habitation',
        default      => ucfirst($type ?: 'Soumission'),
    };
@endphp

<x-email.layout
    :headerColor="$headerColor"
    :headerTitle="$headerTitle"
    :accentColor="$accentColor"
    :logoUrl="$logoUrl"
    :footerText="$portal->name . ' — propulsé par ' . $emailSettings->global_from_name"
    title="Nouvelle soumission partenaire"
>
    <x-slot name="styles">
    <style>
        .section { margin-bottom: 16px; border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden; }
        .section-title { background: #f8f9fa; padding: 10px 14px; font-weight: 700; font-size: 13px; color: {{ $headerColor }}; border-bottom: 1px solid #e9ecef; text-transform: uppercase; letter-spacing: .5px; }
        .section-body { padding: 14px; }
        .info-row { margin: 7px 0; font-size: 14px; }
        .label { font-weight: 700; color: #444; }
        .value { color: #222; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        @media (max-width: 580px) { .grid { grid-template-columns: 1fr; } }
        .banner { background: {{ $accentColor }}; color: {{ $headerColor }}; padding: 10px 16px; font-size: 13px; font-weight: 700; text-align: center; border-radius: 6px; margin-bottom: 20px; }
    </style>
    </x-slot>

    <div class="banner">Nouvelle soumission — {{ $typeLabel }}</div>

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

    <p style="font-size:11px;color:#999;text-align:center;margin-top:16px;">
        Soumission reçue le {{ now()->format('d/m/Y à H:i') }}
    </p>
</x-email.layout>
