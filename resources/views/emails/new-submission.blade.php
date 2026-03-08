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
            HABITATION (flat)
        ========================================================== --}}
            @if($type === 'habitation')
            <div class="section">
                <div class="section-title">🏠 Habitation</div>
                <div class="section-body">
                    <div class="grid">
                        <div class="info-row"><span class="label">Occupation : </span><span class="value">{{ $occLabel($v('occupancy')) }}</span></div>
                        <div class="info-row"><span class="label">Type de propriété : </span><span class="value">{{ $propLabel($v('property_type')) }}</span></div>

                        <div class="info-row"><span class="label">Adresse : </span><span class="value">{{ $v('address') }}</span></div>
                        <div class="info-row"><span class="label">Vit à cette adresse : </span><span class="value">{{ $yesNoLabel($v('living_there')) }}</span></div>

                        @php $living = strtolower(trim((string)($d['living_there'] ?? ''))); @endphp
                        @if($living === 'oui')
                        <div class="info-row"><span class="label">Date d’emménagement : </span><span class="value">{{ $v('move_in_date') }}</span></div>
                        @else
                        <div class="info-row"><span class="label">Date d’emménagement : </span><span class="value">N/A</span></div>
                        @endif

                        <div class="info-row"><span class="label">Nb d’unités immeuble : </span><span class="value">{{ $v('units_in_building') }}</span></div>
                        <div class="info-row"><span class="label">Montant des biens : </span><span class="value">{{ $fmtMoney($d['contents_amount'] ?? null) }}</span></div>

                        <div class="info-row"><span class="label">Plinthes électriques : </span><span class="value">{{ $yesNoLabel($v('electric_baseboard')) }}</span></div>
                        <div class="info-row"><span class="label">Chauffage d’appoint : </span><span class="value">{{ $yesNoLabel($v('supp_heating')) }}</span></div>

                        <div class="info-row"><span class="label">Années d’assurance habitation : </span><span class="value">{{ $yearsInsuredLabel($d['years_insured'] ?? null) }}</span></div>
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

                        <div class="info-row"><span class="label">Secteur d’activité : </span><span class="value">{{ $v('industry') }}</span></div>
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

                        <div class="info-row"><span class="label">Produits existants : </span><span class="value">{{ $v('existing_products') }}</span></div>
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
                    </div>
                </div>
            </div>

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
                        <div class="info-row"><span class="label">Produits existants : </span><span class="value">{{ $val($a,'existing_products') }}</span></div>
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

                        <div class="info-row"><span class="label">Adresse : </span><span class="value">{{ $val($h,'address') }}</span></div>
                        <div class="info-row"><span class="label">Vit à cette adresse : </span><span class="value">{{ $yesNoLabel($h['living_there'] ?? null) }}</span></div>

                        @php $livingH = strtolower(trim((string)($h['living_there'] ?? ''))); @endphp
                        @if($livingH === 'oui' || $livingH === 'yes' || $livingH === '1' || $livingH === 'true')
                        <div class="info-row"><span class="label">Date d’emménagement : </span><span class="value">{{ $val($h,'move_in_date') }}</span></div>
                        @else
                        <div class="info-row"><span class="label">Date d’emménagement : </span><span class="value">N/A</span></div>
                        @endif

                        @php $prop = strtolower(trim((string)($h['property_type'] ?? ''))); @endphp
                        @if($prop !== 'maison')
                        <div class="info-row"><span class="label">Nb d’unités immeuble : </span><span class="value">{{ $val($h,'units_in_building') }}</span></div>
                        @else
                        <div class="info-row"><span class="label">Nb d’unités immeuble : </span><span class="value">N/A</span></div>
                        @endif

                        <div class="info-row"><span class="label">Montant des biens : </span><span class="value">{{ $fmtMoney($h['contents_amount'] ?? null) }}</span></div>

                        <div class="info-row"><span class="label">Plinthes électriques : </span><span class="value">{{ $yesNoLabel($h['electric_baseboard'] ?? null) }}</span></div>
                        <div class="info-row"><span class="label">Chauffage d’appoint : </span><span class="value">{{ $yesNoLabel($h['supp_heating'] ?? null) }}</span></div>

                        <div class="info-row"><span class="label">Années d’assurance habitation : </span><span class="value">{{ $yearsInsuredLabel($h['years_insured'] ?? null) }}</span></div>
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
                        <div class="info-row"><span class="label">Secteur d’activité : </span><span class="value">{{ $val($h,'industry') }}</span></div>
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