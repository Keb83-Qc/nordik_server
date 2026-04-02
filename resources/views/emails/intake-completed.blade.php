@php
    $emailSettings = app(\App\Settings\EmailSettings::class);

    $p = $case->payload ?? [];
    $c = $p['client'] ?? [];
    $j = $p['conjoint'] ?? [];
    $hasSpouse = $p['has_spouse'] ?? false;
    $rev = $p['revenus'] ?? [];
    $actifs = $p['actifs'] ?? [];
    $retraite = $p['retraite'] ?? [];
    $objectifsTexte = $p['navigation']['objectifs_client'] ?? '';

    $advisor = $intake->advisor;

    $clientName = trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')) ?: 'Client';
    $fmtMoney = fn($v) => $v ? number_format((float)$v, 0, ',', ' ') . ' $' : '—';
@endphp

<x-email.layout
    :headerColor="$emailSettings->abf_header_color"
    :headerTitle="$emailSettings->abf_header_title"
    :accentColor="$emailSettings->global_accent_color"
    :logoUrl="$emailSettings->global_logo_url"
    :footerText="$emailSettings->global_footer_text"
    title="Nouveau profil client reçu"
>
    <x-slot name="styles">
    <style>
        h1 { color: #0E1030; font-size: 20px; margin: 0 0 14px; }
        p { color: #444; line-height: 1.6; font-size: 14px; margin-bottom: 12px; }
        .section { border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden; margin-bottom: 16px; }
        .section-title { background: #f8f9fa; padding: 10px 14px; font-weight: 700; font-size: 12px; color: #0E1030; border-bottom: 1px solid #e9ecef; text-transform: uppercase; letter-spacing: .5px; }
        .section-body { padding: 14px; }
        .info-row { margin: 7px 0; font-size: 14px; }
        .label { font-weight: 700; color: #0E1030; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
        @media (max-width: 580px) { .grid { grid-template-columns: 1fr; } }
        .btn-wrap { text-align: center; margin: 20px 0; }
        .btn-link { display: inline-block; background: #0E1030; color: #fff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 14px; border: 2px solid #e8b84b; }
        .banner { background: #e8b84b; color: #1a2e4a; padding: 10px 16px; text-align: center; font-weight: 800; font-size: 14px; border-radius: 6px; margin-bottom: 20px; }
    </style>
    </x-slot>

    <div class="banner">Nouveau profil client reçu</div>

    <h1>Bonjour {{ $advisor->first_name }},</h1>
    <p><strong>{{ $clientName }}</strong> vient de compléter son profil financier. Un dossier ABF a été créé automatiquement dans votre espace conseiller.</p>

    <div class="btn-wrap">
        <a href="{{ $case->editor_url }}" class="btn-link">Ouvrir le dossier ABF →</a>
    </div>

    {{-- Client --}}
    <div class="section">
        <div class="section-title">Informations client</div>
        <div class="section-body">
            <div class="grid">
                <div class="info-row"><span class="label">Nom :</span> {{ $clientName }}</div>
                <div class="info-row"><span class="label">Sexe :</span> {{ $c['sexe'] ?? '—' }}</div>
                <div class="info-row"><span class="label">Date de naissance :</span> {{ implode('/', array_filter([$c['ddn_jour']??'',$c['ddn_mois']??'',$c['ddn_annee']??''])) ?: '—' }}</div>
                <div class="info-row"><span class="label">État civil :</span> {{ $c['etat_civil'] ?? '—' }}</div>
                <div class="info-row"><span class="label">Courriel :</span> {{ $c['courriel'] ?? '—' }}</div>
                <div class="info-row"><span class="label">Cellulaire :</span> {{ $c['cellulaire'] ?? '—' }}</div>
                <div class="info-row" style="grid-column:span 2;"><span class="label">Adresse :</span> {{ trim(implode(' ', array_filter([$c['addr_civique']??'',$c['addr_rue']??'',$c['addr_ville']??'',$c['addr_province']??'',$c['addr_postal']??'']))) ?: '—' }}</div>
            </div>
        </div>
    </div>

    @if($hasSpouse && ($j['prenom'] ?? ''))
    <div class="section">
        <div class="section-title">Conjoint(e)</div>
        <div class="section-body">
            <div class="grid">
                <div class="info-row"><span class="label">Nom :</span> {{ $j['prenom'] }} {{ $j['nom'] ?? '' }}</div>
                <div class="info-row"><span class="label">Date de naissance :</span> {{ implode('/', array_filter([$j['ddn_jour']??'',$j['ddn_mois']??'',$j['ddn_annee']??''])) ?: '—' }}</div>
            </div>
        </div>
    </div>
    @endif

    @if(!empty($rev))
    <div class="section">
        <div class="section-title">Revenus déclarés</div>
        <div class="section-body">
            @foreach($rev as $r)
            <div class="info-row">
                <span class="label">{{ ucfirst($r['owner'] ?? 'Client') }} :</span>
                {{ $fmtMoney($r['annuel'] ?? $r['montant'] ?? 0) }} / an
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(!empty($actifs))
    <div class="section">
        <div class="section-title">Actifs déclarés</div>
        <div class="section-body">
            @foreach($actifs as $a)
            <div class="info-row">
                <span class="label">{{ $a['description'] ?? $a['_type'] ?? '—' }} :</span>
                {{ $fmtMoney($a['_valeur'] ?? $a['valeur'] ?? 0) }}
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Objectifs</div>
        <div class="section-body">
            <div class="info-row"><span class="label">Âge retraite client :</span> {{ $retraite['ageClient'] ?? '—' }} ans</div>
            @if($hasSpouse && ($retraite['ageConjoint'] ?? ''))
            <div class="info-row"><span class="label">Âge retraite conjoint :</span> {{ $retraite['ageConjoint'] }} ans</div>
            @endif
            @if($objectifsTexte)
            <div class="info-row" style="margin-top:8px;"><span class="label">Objectifs :</span><br><span style="color:#555;">{{ $objectifsTexte }}</span></div>
            @endif
        </div>
    </div>
</x-email.layout>
