<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Soumission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 720px;
            margin: auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
        }

        .header {
            background: #0E1030;
            color: #fff;
            padding: 18px 24px;
            text-align: center;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
            letter-spacing: .3px;
        }

        .sub {
            margin-top: 6px;
            font-size: 13px;
            opacity: .92;
            line-height: 1.4;
        }

        .pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            background: rgba(201, 160, 80, .14);
            color: #6b4d12;
            border: 1px solid rgba(201, 160, 80, .35);
            text-transform: uppercase;
        }

        .content {
            padding: 22px 24px;
        }

        .section {
            margin-bottom: 18px;
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
        }

        .section-title {
            background: #fafafa;
            padding: 10px 14px;
            font-weight: 700;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }

        .section-body {
            padding: 12px 14px;
        }

        .info-row {
            margin: 8px 0;
        }

        .label {
            font-weight: 700;
            color: #0E1030;
        }

        .value {
            color: #333;
        }

        a {
            color: #0E1030;
            text-decoration: none;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        @media (max-width: 640px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            padding: 16px 12px;
            background: #fafafa;
            border-top: 1px solid #eee;
        }

        .muted {
            color: #666;
            font-size: 12px;
        }
    </style>
</head>

<body>
    @php
    // ------------------------------------------------------------------
    // Data sources
    // ------------------------------------------------------------------
    $d = $submission->data ?? [];
    $type = strtolower($submission->type ?? '');
    $isBundle = ($type === 'bundle');

    // Buckets pour bundle (si présents)
    $c = (is_array($d['common'] ?? null)) ? ($d['common'] ?? []) : [];
    $a = (is_array($d['auto'] ?? null)) ? ($d['auto'] ?? []) : [];
    $h = (is_array($d['habitation'] ?? null)) ? ($d['habitation'] ?? []) : [];

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------
    $v = function(string $key, $fallback='-') use ($d) {
    $vv = $d[$key] ?? null;
    return ($vv === null || $vv === '') ? $fallback : $vv;
    };

    $val = function(array $bucket, string $key, $fallback='-') {
    $vv = $bucket[$key] ?? null;
    return ($vv === null || $vv === '') ? $fallback : $vv;
    };

    $fmtMoney = function ($val) {
    if ($val === null || $val === '' || !is_numeric($val)) return '-';
    return number_format((float)$val, 0, ',', ' ') . ' $';
    };

    $yesNoLabel = function($val) {
    $v = is_string($val) ? strtolower(trim($val)) : $val;
    return match ($v ?? '') {
    'yes','oui','y','true','1' => 'Oui',
    'no','non','n','false','0' => 'Non',
    default => ($v === null || $v === '') ? '-' : (string)$val,
    };
    };

    $consentLabel = function($val) use ($yesNoLabel) {
    $v = is_string($val) ? strtolower(trim($val)) : $val;
    return match ($v ?? '') {
    'accept' => "J'accepte",
    'refuse' => 'Je refuse',
    default => $yesNoLabel($val),
    };
    };

    $occLabel = function($val) {
    $v = is_string($val) ? strtolower(trim($val)) : $val;
    return match ($v ?? '') {
    'proprietaire' => 'Propriétaire',
    'locataire' => 'Locataire',
    default => ($val === null || $val === '') ? '-' : (string)$val,
    };
    };

    $propLabel = function($val) {
    $v = is_string($val) ? strtolower(trim($val)) : $val;
    return match ($v ?? '') {
    'maison' => 'Maison',
    'condo' => 'Condo',
    'appartement' => 'Appartement',
    default => ($val === null || $val === '') ? '-' : (string)$val,
    };
    };

    $yearsInsuredLabel = function($val) {
    $v = is_string($val) ? strtolower(trim($val)) : $val;
    return match ($v ?? '') {
    '0' => '0 an',
    '1_2','1-2' => '1 à 2 ans',
    '3_5','3-5' => '3 à 5 ans',
    '6_10','6-10' => '6 à 10 ans',
    '11_plus','11+' => '11 ans et plus',
    default => ($val === null || $val === '') ? '-' : (string)$val,
    };
    };

    $genderLabel = function($val) {
    $v = is_string($val) ? strtolower(trim($val)) : $val;
    return match ($v ?? '') {
    'homme' => 'Homme',
    'femme' => 'Femme',
    'autre' => 'Autre',
    'prefer_not' => 'Préfère ne pas répondre',
    default => ($val === null || $val === '') ? '-' : (string)$val,
    };
    };

    $maritalLabel = function($val) {
    $v = is_string($val) ? strtolower(trim($val)) : $val;
    return match ($v ?? '') {
    'celibataire' => 'Célibataire',
    'conjoint' => 'Conjoint(e)',
    'marie' => 'Marié(e)',
    'autre' => 'Autre',
    default => ($val === null || $val === '') ? '-' : (string)$val,
    };
    };

    $jobLabel = function($val) {
    $v = is_string($val) ? strtolower(trim($val)) : $val;
    return match ($v ?? '') {
    'employe' => 'Employé(e)',
    'travailleur_autonome' => 'Travailleur autonome',
    'etudiant' => 'Étudiant(e)',
    'retraite' => 'Retraité(e)',
    'sans_emploi' => 'Sans emploi',
    default => ($val === null || $val === '') ? '-' : (string)$val,
    };
    };

    $eduLabel = function($val) {
    $v = is_string($val) ? strtolower(trim($val)) : $val;
    return match ($v ?? '') {
    'secondaire' => 'Secondaire',
    'college' => 'Collège',
    'universite' => 'Université',
    'autre' => 'Autre',
    default => ($val === null || $val === '') ? '-' : (string)$val,
    };
    };

    $autoUsageLabel = function($val) {
    $v = is_string($val) ? strtolower(trim($val)) : $val;
    return match ($v ?? '') {
    'personnel' => 'Personnel',
    'commercial' => 'Commercial',
    default => ($val === null || $val === '') ? '-' : (string)$val,
    };
    };

    $existingProductsLabel = function($val) {
    $v = is_string($val) ? strtolower(trim($val)) : $val;
    return match ($v ?? '') {
    'assurance' => 'Assurances',
    'placement' => 'Placements',
    'both' => 'Assurances et Placements',
    'none' => 'Aucun',
    default => ($val === null || $val === '') ? '-' : (string)$val,
    };
    };

    $contactTimeLabel = function($val) {
    $v = is_string($val) ? strtolower(trim($val)) : $val;
    return match ($v ?? '') {
    'matin' => 'Matin (8h-12h)',
    'apres_midi' => 'Après-midi (12h-17h)',
    'soir' => 'Soir (17h-20h)',
    'nimporte_quand' => "N'importe quand",
    default => ($val === null || $val === '') ? '-' : (string)$val,
    };
    };

    // ------------------------------------------------------------------
    // Header info
    // ------------------------------------------------------------------
    $advisorName = isset($advisor) && $advisor
    ? trim(($advisor->first_name ?? '').' '.($advisor->last_name ?? ''))
    : 'Général';

    $badgeType = match ($type) {
    'auto' => 'AUTO',
    'habitation' => 'HABITATION',
    'bundle' => 'AUTO + HABITATION',
    default => $type ? strtoupper($type) : 'SOUMISSION',
    };

    // client name for display inside email
    $clientFirst = $isBundle ? ($c['first_name'] ?? '') : ($d['first_name'] ?? '');
    $clientLast = $isBundle ? ($c['last_name'] ?? '') : ($d['last_name'] ?? '');
    $clientName = trim(($clientFirst ?: 'Client').' '.$clientLast);
    @endphp

    <div class="container">
        <div class="header">
            <h2>Nouvelle Soumission</h2>
            <div class="sub">
                <span class="pill">{{ $badgeType }}</span>
                &nbsp;• Conseiller : <strong>{{ $advisorName }}</strong>
                &nbsp;• Client : <strong>{{ $clientName }}</strong>
                &nbsp;• ID: <strong>#{{ $submission->id }}</strong>
            </div>
        </div>

        <div class="content">

            {{-- =========================================================
            PORTAIL PARTENAIRE — Bannière d'origine
        ========================================================== --}}
            @if(isset($portal) && $portal && $portal->isPartner())
            <div style="background:#e8f4fd;border:2px solid #3b82f6;border-radius:10px;padding:14px 18px;margin-bottom:16px;">
                <div style="font-size:15px;font-weight:800;color:#1e40af;margin-bottom:6px;">
                    🤝 Soumission via portail partenaire
                </div>
                <div style="font-size:13px;color:#1e3a8a;line-height:1.7;">
                    <strong>Portail :</strong> {{ $portal->name }}<br>
                    <strong>Assignation conseiller :</strong>
                    @if($portal->advisor_code)
                        🔒 Conseiller fixe — {{ $advisor?->first_name }} {{ $advisor?->last_name }} (<code>{{ $portal->advisor_code }}</code>)
                    @else
                        🔄 Rotation automatique — {{ $advisor?->first_name }} {{ $advisor?->last_name }}
                    @endif
                </div>
            </div>
            @endif

            {{-- =========================================================
            LNNTE — Bannière d'avertissement numéro exclu
        ========================================================== --}}
            @if($submission->is_phone_excluded || ($d['_phone_excluded'] ?? false))
            @php
                $excludedPhone = \App\Models\ExcludedPhone::findByPhone($d['phone'] ?? '');
                $excludedReason = $excludedPhone
                    ? (\App\Models\ExcludedPhone::REASONS[$excludedPhone->reason] ?? $excludedPhone->reason)
                    : 'Non précisé';
            @endphp
            <div style="background:#fff3cd;border:2px solid #ffc107;border-radius:10px;padding:14px 18px;margin-bottom:20px;">
                <div style="font-size:16px;font-weight:800;color:#7a4f00;margin-bottom:6px;">
                    ⚠️ ATTENTION — Numéro exclu (LNNTE interne)
                </div>
                <div style="font-size:13px;color:#664d03;line-height:1.6;">
                    Le numéro <strong>{{ $d['phone'] ?? 'inconnu' }}</strong> est inscrit dans votre liste interne de numéros exclus.<br>
                    <strong>Ne pas contacter par téléphone</strong> sauf consentement explicite du client.<br>
                    <span style="font-size:12px;">Raison : {{ $excludedReason }}</span>
                    @if($excludedPhone?->notes)
                        <br><span style="font-size:12px;">Notes : {{ $excludedPhone->notes }}</span>
                    @endif
                </div>
            </div>
            @endif

            {{-- =========================================================
            HABITATION (flat)
        ========================================================== --}}
            @if($type === 'habitation')
            <div class="section">
                <div class="section-title">🏠 Habitation</div>
                <div class="section-body">
                    <div class="grid">
                        <div class="info-row"><span class="label">Occupation : </span><span class="value">{{ $occLabel($v('occupancy')) }}</span></div>
                        <div class="info-row"><span class="label">Type de propriété : </span><span class="value">{{ $propLabel($v('property_type')) }}</span></div>
                        <div class="info-row"><span class="label">Renouvellement habitation : </span><span class="value">{{ $v('hab_renewal_date') }}</span></div>

                        <div class="info-row"><span class="label">Adresse : </span><span class="value">{{ $v('address') }}</span></div>
                        <div class="info-row"><span class="label">Vit à cette adresse : </span><span class="value">{{ $yesNoLabel($v('living_there')) }}</span></div>

                        @php $living = strtolower(trim((string)($d['living_there'] ?? ''))); @endphp
                        @if($living === 'yes' || $living === 'oui')
                        <div class="info-row"><span class="label">Années à cette adresse : </span><span class="value">{{ $v('years_at_address') !== '-' ? ($v('years_at_address').' an(s)') : '-' }}</span></div>
                        @else
                        <div class="info-row"><span class="label">Années à cette adresse : </span><span class="value">N/A</span></div>
                        @endif

                        <div class="info-row"><span class="label">Nb d'unités immeuble : </span><span class="value">{{ $v('units_in_building') }}</span></div>
                        <div class="info-row"><span class="label">Montant des biens : </span><span class="value">{{ $fmtMoney($d['contents_amount'] ?? null) }}</span></div>

                        <div class="info-row"><span class="label">Plinthes électriques : </span><span class="value">{{ $yesNoLabel($v('electric_baseboard')) }}</span></div>
                        <div class="info-row"><span class="label">Chauffage d'appoint : </span><span class="value">{{ $yesNoLabel($v('supp_heating')) }}</span></div>

                        <div class="info-row"><span class="label">Années d'assurance habitation : </span><span class="value">{{ $yearsInsuredLabel($d['years_insured'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">Années chez assureur actuel : </span><span class="value">{{ $v('years_with_insurer') }}</span></div>

                        <div class="info-row"><span class="label">Assureur actuel : </span><span class="value">{{ $v('current_insurer') }}</span></div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">👤 Assuré / Profil</div>
                <div class="section-body">
                    <div class="grid">
                        <div class="info-row"><span class="label">Nom : </span><span class="value">{{ $v('first_name') }} {{ $v('last_name','') }}</span></div>
                        <div class="info-row"><span class="label">Genre : </span><span class="value">{{ $genderLabel($d['gender'] ?? null) }}</span></div>

                        <div class="info-row"><span class="label">Âge : </span><span class="value">{{ isset($d['age']) && $d['age'] !== '' ? ($d['age'].' ans') : '-' }}</span></div>
                        <div class="info-row"><span class="label">État civil : </span><span class="value">{{ $maritalLabel($d['marital_status'] ?? null) }}</span></div>

                        <div class="info-row"><span class="label">Statut professionnel : </span><span class="value">{{ $jobLabel($d['employment_status'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">Dernier diplôme : </span><span class="value">{{ $eduLabel($d['education_level'] ?? null) }}</span></div>

                        <div class="info-row"><span class="label">Secteur d'activité : </span><span class="value">{{ $v('industry') }}</span></div>
                        <div class="info-row"><span class="label">Produits iA sous le même toit : </span><span class="value">{{ $yesNoLabel($v('has_ia_products')) }}</span></div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">✅ Consentements</div>
                <div class="section-body">
                    <div class="info-row"><span class="label">Profilage : </span><span class="value">{{ $consentLabel($d['consent_profile'] ?? null) }}</span></div>
                    <div class="info-row"><span class="label">Marketing : </span><span class="value">{{ $consentLabel($d['consent_marketing'] ?? null) }}</span></div>
                    <div class="info-row"><span class="label">Marketing par courriel : </span><span class="value">{{ $consentLabel($d['marketing_email'] ?? null) }}</span></div>
                    <div class="info-row"><span class="label">Vérification crédit : </span><span class="value">{{ $consentLabel($d['consent_credit'] ?? null) }}</span></div>
                </div>
            </div>

            {{-- Champs additionnels habitation (nouveaux steps Filament) --}}
            @php
            $__knownHab = ['occupancy','property_type','hab_renewal_date','address','living_there',
                           'years_at_address','units_in_building','contents_amount','electric_baseboard',
                           'supp_heating','years_insured','years_with_insurer','current_insurer',
                           'first_name','last_name','gender','age','email','phone','phone_is_cell',
                           'best_contact_time','marital_status','employment_status','education_level',
                           'industry','has_ia_products','consent_profile','consent_marketing',
                           'marketing_email','consent_credit'];
            $__extraHab = $chatSteps->filter(
                fn($s) => !in_array($s->identifier, $__knownHab)
                       && isset($d[$s->identifier])
                       && $d[$s->identifier] !== ''
            );
            @endphp
            @if($__extraHab->isNotEmpty())
            <div class="section">
                <div class="section-title">📋 Champs additionnels</div>
                <div class="section-body">
                    <div class="grid">
                        @foreach($__extraHab as $__step)
                        @php
                        $__q = is_array($__step->question)
                            ? ($__step->question['fr'] ?? $__step->identifier)
                            : ($__step->question ?? $__step->identifier);
                        @endphp
                        <div class="info-row">
                            <span class="label">{{ $__q }} : </span>
                            <span class="value">{{ $d[$__step->identifier] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- =========================================================
            AUTO (flat)
        ========================================================== --}}
            @elseif($type === 'auto')
            <div class="section">
                <div class="section-title">🚗 Auto</div>
                <div class="section-body">
                    <div class="grid">
                        <div class="info-row"><span class="label">Année : </span><span class="value">{{ $v('year') }}</span></div>
                        <div class="info-row"><span class="label">Usage : </span><span class="value">{{ $autoUsageLabel($d['usage'] ?? null) }}</span></div>

                        <div class="info-row"><span class="label">Marque : </span><span class="value">{{ $v('brand') }}</span></div>
                        <div class="info-row"><span class="label">Modèle : </span><span class="value">{{ $v('model') }}</span></div>

                        <div class="info-row"><span class="label">Renouvellement : </span><span class="value">{{ $v('renewal_date') }}</span></div>
                        <div class="info-row"><span class="label">KM annuel : </span><span class="value">{{ $v('km_annuel') }}</span></div>

                        <div class="info-row"><span class="label">Adresse : </span><span class="value">{{ $v('address') }}</span></div>
                        <div class="info-row"><span class="label">Profession : </span><span class="value">{{ $v('profession') }}</span></div>

                        <div class="info-row"><span class="label">Produits existants : </span><span class="value">{{ $existingProductsLabel($d['existing_products'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">No permis : </span><span class="value">{{ $v('license_number') }}</span></div>
                    </div>
                    <div class="muted" style="margin-top:10px;">
                        * Champs affichés selon les données disponibles.
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">👤 Client</div>
                <div class="section-body">
                    <div class="grid">
                        <div class="info-row"><span class="label">Nom : </span><span class="value">{{ $v('first_name') }} {{ $v('last_name','') }}</span></div>
                        <div class="info-row"><span class="label">Âge : </span><span class="value">{{ isset($d['age']) && $d['age'] !== '' ? ($d['age'].' ans') : '-' }}</span></div>
                        <div class="info-row"><span class="label">Courriel : </span><span class="value">{{ $v('email') }}</span></div>
                        <div class="info-row"><span class="label">Téléphone : </span><span class="value">{{ $v('phone') }}</span></div>
                        <div class="info-row"><span class="label">Meilleur moment : </span><span class="value">{{ $contactTimeLabel($d['best_contact_time'] ?? null) }}</span></div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">✅ Consentements</div>
                <div class="section-body">
                    <div class="info-row"><span class="label">Profilage : </span><span class="value">{{ $consentLabel($d['consent_profile'] ?? null) }}</span></div>
                    <div class="info-row"><span class="label">Marketing : </span><span class="value">{{ $consentLabel($d['consent_marketing'] ?? null) }}</span></div>
                    <div class="info-row"><span class="label">Marketing par courriel : </span><span class="value">{{ $yesNoLabel($d['marketing_email'] ?? null) }}</span></div>
                    <div class="info-row"><span class="label">Vérification crédit : </span><span class="value">{{ $yesNoLabel($d['consent_credit'] ?? null) }}</span></div>
                </div>
            </div>

            {{-- Champs additionnels auto (nouveaux steps Filament) --}}
            @php
            $__knownAuto = ['year','usage','brand','brand_id','model','model_id','renewal_date',
                            'km_annuel','address','profession','existing_products','license_number',
                            'first_name','last_name','age','email','phone','best_contact_time',
                            'consent_profile','consent_marketing','marketing_email','consent_credit'];
            $__extraAuto = $chatSteps->filter(
                fn($s) => !in_array($s->identifier, $__knownAuto)
                       && isset($d[$s->identifier])
                       && $d[$s->identifier] !== ''
            );
            @endphp
            @if($__extraAuto->isNotEmpty())
            <div class="section">
                <div class="section-title">📋 Champs additionnels</div>
                <div class="section-body">
                    <div class="grid">
                        @foreach($__extraAuto as $__step)
                        @php
                        $__q = is_array($__step->question)
                            ? ($__step->question['fr'] ?? $__step->identifier)
                            : ($__step->question ?? $__step->identifier);
                        @endphp
                        <div class="info-row">
                            <span class="label">{{ $__q }} : </span>
                            <span class="value">{{ $d[$__step->identifier] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- =========================================================
            BUNDLE (bucket common/auto/habitation)
        ========================================================== --}}
            @elseif($isBundle)
            <div class="section">
                <div class="section-title">👤 Client / Contact</div>
                <div class="section-body">
                    <div class="grid">
                        <div class="info-row"><span class="label">Nom : </span><span class="value">{{ $val($c,'first_name') }} {{ $val($c,'last_name','') }}</span></div>
                        <div class="info-row"><span class="label">Genre : </span><span class="value">{{ $genderLabel($c['gender'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">Âge : </span><span class="value">{{ isset($c['age']) && $c['age'] !== '' ? ($c['age'].' ans') : '-' }}</span></div>
                        <div class="info-row"><span class="label">Courriel : </span><span class="value">{{ $val($c,'email') }}</span></div>
                        <div class="info-row"><span class="label">Téléphone : </span><span class="value">{{ $val($c,'phone') }}</span></div>
                        <div class="info-row"><span class="label">Meilleur moment : </span><span class="value">{{ $contactTimeLabel($c['best_contact_time'] ?? null) }}</span></div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">🚗 Auto</div>
                <div class="section-body">
                    <div class="grid">
                        <div class="info-row"><span class="label">Année : </span><span class="value">{{ $val($a,'year') }}</span></div>
                        <div class="info-row"><span class="label">Usage : </span><span class="value">{{ $autoUsageLabel($a['usage'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">Marque : </span><span class="value">{{ $val($a,'brand') }}</span></div>
                        <div class="info-row"><span class="label">Modèle : </span><span class="value">{{ $val($a,'model') }}</span></div>
                        <div class="info-row"><span class="label">Renouvellement : </span><span class="value">{{ $val($a,'renewal_date') }}</span></div>
                        <div class="info-row"><span class="label">KM annuel : </span><span class="value">{{ $val($a,'km_annuel') }}</span></div>
                        <div class="info-row"><span class="label">Produits existants : </span><span class="value">{{ $existingProductsLabel($a['existing_products'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">No permis : </span><span class="value">{{ $val($a,'license_number') }}</span></div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">🏠 Habitation</div>
                <div class="section-body">
                    <div class="grid">
                        <div class="info-row"><span class="label">Occupation : </span><span class="value">{{ $occLabel($h['occupancy'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">Type de propriété : </span><span class="value">{{ $propLabel($h['property_type'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">Renouvellement habitation : </span><span class="value">{{ $val($h,'renewal_date') }}</span></div>

                        <div class="info-row"><span class="label">Adresse : </span><span class="value">{{ $val($h,'address') }}</span></div>
                        <div class="info-row"><span class="label">Vit à cette adresse : </span><span class="value">{{ $yesNoLabel($h['living_there'] ?? null) }}</span></div>

                        @php $livingH = strtolower(trim((string)($h['living_there'] ?? ''))); @endphp
                        @if($livingH === 'oui' || $livingH === 'yes' || $livingH === '1' || $livingH === 'true')
                        <div class="info-row"><span class="label">Années à cette adresse : </span><span class="value">{{ $val($h,'years_at_address') !== '-' ? ($val($h,'years_at_address').' an(s)') : '-' }}</span></div>
                        @else
                        <div class="info-row"><span class="label">Années à cette adresse : </span><span class="value">N/A</span></div>
                        @endif

                        @php $prop = strtolower(trim((string)($h['property_type'] ?? ''))); @endphp
                        @if($prop !== 'maison')
                        <div class="info-row"><span class="label">Nb d'unités immeuble : </span><span class="value">{{ $val($h,'units_in_building') }}</span></div>
                        @else
                        <div class="info-row"><span class="label">Nb d’unités immeuble : </span><span class="value">N/A</span></div>
                        @endif

                        <div class="info-row"><span class="label">Montant des biens : </span><span class="value">{{ $fmtMoney($h['contents_amount'] ?? null) }}</span></div>

                        <div class="info-row"><span class="label">Plinthes électriques : </span><span class="value">{{ $yesNoLabel($h['electric_baseboard'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">Chauffage d'appoint : </span><span class="value">{{ $yesNoLabel($h['supp_heating'] ?? null) }}</span></div>

                        <div class="info-row"><span class="label">Années d'assurance habitation : </span><span class="value">{{ $yearsInsuredLabel($h['years_insured'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">Années chez assureur actuel : </span><span class="value">{{ $val($h,'years_with_insurer') }}</span></div>

                        <div class="info-row"><span class="label">Assureur actuel : </span><span class="value">{{ $val($h,'current_insurer') }}</span></div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">📌 Profil complémentaire</div>
                <div class="section-body">
                    <div class="grid">
                        <div class="info-row"><span class="label">État civil : </span><span class="value">{{ $maritalLabel($h['marital_status'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">Statut professionnel : </span><span class="value">{{ $jobLabel($h['employment_status'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">Dernier diplôme : </span><span class="value">{{ $eduLabel($h['education_level'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">Secteur d'activité : </span><span class="value">{{ $val($h,'industry') }}</span></div>
                        <div class="info-row"><span class="label">Produits iA sous le même toit : </span><span class="value">{{ $yesNoLabel($h['has_ia_products'] ?? null) }}</span></div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">✅ Consentements</div>
                <div class="section-body">
                    <div class="info-row"><span class="label">Profilage : </span><span class="value">{{ $consentLabel($h['consent_profile'] ?? null) }}</span></div>
                    <div class="info-row"><span class="label">Marketing : </span><span class="value">{{ $consentLabel($h['consent_marketing'] ?? null) }}</span></div>
                    <div class="info-row"><span class="label">Marketing par courriel : </span><span class="value">{{ $consentLabel($h['marketing_email'] ?? null) }}</span></div>
                    <div class="info-row"><span class="label">Vérification crédit : </span><span class="value">{{ $consentLabel($h['consent_credit'] ?? null) }}</span></div>
                </div>
            </div>

            {{-- =========================================================
            FALLBACK
        ========================================================== --}}
            @else
            <div class="section">
                <div class="section-title">Soumission</div>
                <div class="section-body">
                    <div class="info-row"><span class="label">Type : </span><span class="value">{{ $type ?: '-' }}</span></div>
                    <div class="info-row"><span class="label">ID : </span><span class="value">#{{ $submission->id }}</span></div>
                </div>
            </div>
            @endif

            {{-- =========================================================
            CONTACT (pour auto/habitation flat)
        ========================================================== --}}
            @if(!$isBundle)
            <div class="section">
                <div class="section-title">📞 Contact</div>
                <div class="section-body">
                    <div class="info-row">
                        <span class="label">Courriel :&nbsp;</span>
                        <span class="value">
                            @php $em = $d['email'] ?? ''; @endphp
                            @if($em)
                            <a href="mailto:{{ $em }}">{{ $em }}</a>
                            @else
                            -
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Téléphone :&nbsp;</span>
                        <span class="value">
                            @php $ph = $d['phone'] ?? ''; @endphp
                            @if($ph)
                            <a href="tel:{{ $ph }}">{{ $ph }}</a>
                            @else
                            -
                            @endif
                        </span>
                    </div>
                    <div class="info-row"><span class="label">Meilleur moment :&nbsp;</span><span class="value">{{ $contactTimeLabel($d['best_contact_time'] ?? null) }}</span></div>
                </div>
            </div>
            @endif

        </div>

        <div class="footer">
            Système VIP Gestion de Patrimoine &amp; Investissement Inc. &copy; {{ date('Y') }}<br>
            Message envoyé automatiquement. ID: #{{ $submission->id }}
        </div>
    </div>
</body>

</html>