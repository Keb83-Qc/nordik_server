<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Votre portrait financier</title>

    <style>
        /* ============
           PRINT SETUP
           ============ */
        @page {
            margin: 28mm 18mm 22mm 18mm;
        }

        /* Cover full bleed */
        @page :first {
            margin: 0mm;
        }

        /* ============
           VIP PALETTE
           ============ */
        /* VIP Navy / Gold */
        /* Navy: #0E1030  Deep: #090A1F  Gold: #C9A050  GoldLight: #E6C885 */
        /* Paper (gold tint): #FBF6EA  Border: #EAD9B6 */
        /* Text: #0B0D2A  Muted: #6B7280 */

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11.5px;
            color: #0B0D2A;
        }

        h1,
        h2,
        h3 {
            font-family: DejaVu Serif, serif;
            font-weight: 500;
            color: #0E1030;
            margin: 0;
        }

        h1 {
            font-size: 34px;
            margin-top: 6mm;
            margin-bottom: 10mm;
        }

        h2 {
            font-size: 18px;
            margin-top: 8mm;
            margin-bottom: 3mm;
        }

        h3 {
            font-size: 14px;
            margin-top: 6mm;
            margin-bottom: 2mm;
        }

        p {
            margin: 0 0 8px 0;
            line-height: 1.45;
        }

        .page-break {
            page-break-after: always;
        }

        .muted {
            color: #6B7280;
        }

        .small {
            font-size: 10px;
        }

        /* ============
           COVER
           ============ */
        .cover {
            position: relative;
            height: 297mm;
            /* A4 */
            width: 210mm;
            /* A4 */
        }

        .cover-photo {
            position: absolute;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .cover-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, #0E1030 0%, #090A1F 100%);
        }

        .cover-inner {
            position: relative;
            padding: 34mm 18mm 22mm 18mm;
            color: #FFFFFF;
        }

        .cover-logo {
            position: absolute;
            right: 18mm;
            top: 22mm;
            width: 40mm;
            height: auto;
        }

        .cover-title {
            font-family: DejaVu Serif, serif;
            font-size: 46px;
            line-height: 1.02;
            margin-top: 30mm;
            color: #FFFFFF;
        }

        .cover-underline {
            width: 70%;
            height: 2px;
            margin-top: 10px;
            background: linear-gradient(90deg, #C9A050 0%, #E6C885 50%, #C9A050 100%);
        }

        .cover-names {
            margin-top: 10px;
            font-size: 12px;
            opacity: 0.95;
        }

        .cover-conf {
            position: absolute;
            left: 18mm;
            right: 18mm;
            bottom: 18mm;
            font-size: 9.5px;
            color: rgba(255, 255, 255, 0.85);
        }

        .cover-conf .title {
            color: #C9A050;
            font-weight: 700;
            letter-spacing: .3px;
            margin-bottom: 6px;
        }

        /* ============
           HEADER / FOOTER (from page 2)
           ============ */
        .header {
            position: fixed;
            top: 12mm;
            left: 18mm;
            right: 18mm;
            height: 10mm;
            font-size: 10px;
            color: #0E1030;
        }

        .header .left {
            float: left;
            font-weight: 700;
            letter-spacing: .3px;
        }

        .header .right {
            float: right;
            font-weight: 700;
            color: #C9A050;
        }

        .footer {
            position: fixed;
            bottom: 10mm;
            left: 18mm;
            right: 18mm;
            height: 10mm;
            font-size: 10px;
            color: #6B7280;
        }

        .footer .left {
            float: left;
            font-weight: 700;
            color: #0E1030;
            letter-spacing: .2px;
        }

        .footer .right {
            float: right;
        }

        .pagenum:before {
            content: counter(page);
        }

        /* ============
           TABLES
           ============ */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .compare thead th,
        .simple thead th {
            font-weight: 700;
            color: #0E1030;
            padding: 6px 8px;
            border-bottom: 2px solid #C9A050;
            text-align: left;
        }

        .compare tbody td,
        .simple tbody td {
            padding: 8px 8px;
            border-bottom: 1px solid #EAD9B6;
            vertical-align: top;
        }

        .compare tbody td:first-child {
            width: 34%;
            color: #0B0D2A;
        }

        .total-row td {
            background: #FBF6EA;
            font-weight: 700;
        }

        /* Premium total */
        .grand-total td {
            background: #0E1030;
            color: #C9A050;
            font-weight: 700;
            border-bottom: none;
        }

        /* TOC */
        .toc h1 {
            margin-top: 40mm;
        }

        .toc-item {
            border-bottom: 1px solid #EAD9B6;
            padding: 10px 0;
        }

        .toc-item .name {
            float: left;
        }

        .toc-item .page {
            float: right;
            color: #0E1030;
        }

        .clearfix:after {
            content: "";
            display: block;
            clear: both;
        }

        /* Optional: section spacing on page */
        .section-gap {
            margin-top: 4mm;
        }
    </style>
</head>

<body>
    @php
    $sections = (array) ($sections ?? []);
    $sec = fn($k, $def = true) => (bool) ($sections[$k] ?? $def);

    $payload = (array) ($case->payload ?? []);
    $client = (array) data_get($payload, 'client', []);
    $spouse = (array) data_get($payload, 'spouse', []);
    $hasSpouse = (bool) data_get($payload, 'has_spouse', false);

    $clientName = trim((string)(($client['first_name'] ?? '').' '.($client['last_name'] ?? ''))) ?: 'Client';
    $spouseName = trim((string)(($spouse['first_name'] ?? '').' '.($spouse['last_name'] ?? ''))) ?: 'Conjoint';

    $docDateRaw = data_get($payload, 'document_meta.document_date');
    $docDate = null;
    try {
    $docDate = $docDateRaw ? \Carbon\Carbon::parse($docDateRaw)->locale('fr_CA')->isoFormat('D MMMM YYYY') : null;
    } catch(\Throwable) {}
    $docDate ??= now()->locale('fr_CA')->isoFormat('D MMMM YYYY');

    // Logo (transparent OK)
    $logoPath = public_path('assets/img/VIP_Logo_Gold_Gradient10.png');
    $logo = file_exists($logoPath) ? ('data:image/png;base64,' . base64_encode(file_get_contents($logoPath))) : null;

    $money = fn($v) => number_format((float)$v, 0, '.', ' ') . ' $';

    $age = function($d){
    if (blank($d)) return null;
    try { return \Carbon\Carbon::parse($d)->age; } catch(\Throwable) { return null; }
    };

    $maritalLabel = fn($k) => [
    'single' => 'Célibataire',
    'common_law' => 'Conjoint de fait',
    'married' => 'Marié(e)',
    'divorced' => 'Divorcé(e)',
    'separated' => 'Séparé(e)',
    'widowed' => 'Veuf(ve)',
    ][$k ?? ''] ?? '—';

    $citLabel = fn($k) => [
    'canadian_citizen' => 'Citoyen(ne) canadien(ne)',
    'permanent_resident' => 'Résident(e) permanent(e)',
    'temporary_resident' => 'Résident(e) temporaire / permis',
    'other' => 'Autre',
    ][$k ?? ''] ?? '—';

    $smokerLabel = fn($k) => [
    'smoker' => 'Fumeur',
    'non_smoker' => 'Non-fumeur',
    'former_smoker' => 'Ancien fumeur',
    ][$k ?? ''] ?? '—';

    $assets = (array) data_get($payload, 'assets', []);
    $liabs = (array) data_get($payload, 'liabilities', []);
    $deps = (array) data_get($payload, 'dependents', []);

    $assetType = [
    'cash' => 'Liquidités',
    'tfsa' => 'CELI',
    'rrsp' => 'REER',
    'nonreg' => 'Non-enregistré',
    'home' => 'Résidence principale',
    'rental' => 'Résidence secondaire / immeuble',
    'vehicle' => 'Véhicule',
    'business' => 'Entreprise',
    'other' => 'Autre',
    ];

    $liabType = [
    'mortgage' => 'Hypothèque',
    'loc' => 'Marge de crédit',
    'loan' => 'Prêt',
    'credit' => 'Carte de crédit',
    'student' => 'Prêt étudiant',
    'tax' => 'Impôts',
    'other' => 'Autre',
    ];

    $ownerLabel = fn($o) => ['client'=>'Client','spouse'=>'Conjoint','joint'=>'Commun'][$o ?? 'client'] ?? 'Client';

    $sumByOwner = function(array $rows, string $key){
    $out = ['client'=>0,'spouse'=>0,'joint'=>0];
    foreach($rows as $r){
    $o = $r['owner'] ?? 'client';
    $out[$o] = ($out[$o] ?? 0) + (float) ($r[$key] ?? 0);
    }
    return $out;
    };

    $assetSums = $sumByOwner($assets,'value');
    $liabSums = $sumByOwner($liabs,'balance');
    $assetTotal = array_sum($assetSums);
    $liabTotal = array_sum($liabSums);
    $netTotal = $assetTotal - $liabTotal;

    // ── Profil investisseur ──────────────────────────────────────────────
    $ip = (array) data_get($payload, 'investor_profile', []);
    $ipQuestions = [
        'q1' => ['section' => 'Horizon d\'investissement', 'label' => '1. Quel âge avez-vous?', 'options' => [1=>'Plus de 71 ans',2=>'Entre 65 et 70 ans',5=>'Entre 55 et 64 ans',10=>'Entre 41 et 54 ans',20=>'Entre 18 et 40 ans']],
        'q2' => ['section' => 'Horizon d\'investissement', 'label' => '2. Sorties de fonds (≥25 % de l\'épargne)?', 'options' => [1=>'Dans moins de 1 an',2=>'Entre 1 et 3 ans',5=>'Entre 4 et 5 ans',10=>'Entre 6 et 9 ans',20=>'Dans plus de 10 ans']],
        'q3' => ['section' => 'Horizon d\'investissement', 'label' => '3. Retraits prévus (5 prochaines années)?', 'options' => [1=>'Retraits réguliers du capital',2=>'Totalité du rendement + partie du capital',5=>'Tout le rendement sans toucher au capital',10=>'Partie du rendement seulement',20=>'Accumulation (aucun retrait)']],
        'q4' => ['section' => 'Situation financière', 'label' => '4. Revenu annuel brut (avant impôts)?', 'options' => [1=>'25 000 $ et moins',2=>'25 001 $ à 35 000 $',5=>'35 001 $ à 50 000 $',10=>'50 001 $ à 100 000 $',20=>'100 001 $ et plus']],
        'q5' => ['section' => 'Situation financière', 'label' => '5. Valeur nette (actif moins passif)?', 'options' => [1=>'25 000 $ et moins',2=>'25 001 $ à 50 000 $',5=>'50 001 $ à 100 000 $',10=>'100 001 $ à 200 000 $',20=>'200 001 $ et plus']],
        'q6' => ['section' => 'Tolérance au risque', 'label' => '6. Niveau de tolérance au risque?', 'options' => [1=>'Très faible',2=>'Faible',5=>'Modéré',10=>'Élevé',20=>'Très élevé']],
        'q7' => ['section' => 'Tolérance au risque', 'label' => '7. Fourchette acceptée pour un placement de 10 000 $?', 'options' => [1=>'10 000 $ à 10 300 $',2=>'9 500 $ à 11 000 $',5=>'9 000 $ à 11 500 $',10=>'8 500 $ à 12 000 $',20=>'8 000 $ à 12 500 $']],
        'q8' => ['section' => 'Connaissance des placements', 'label' => '8. Niveau de connaissance des placements?', 'options' => [1=>'Très faible',2=>'Faible',5=>'Modéré',10=>'Avancé',20=>'Très avancé']],
    ];
    $ipScore = 0;
    foreach (array_keys($ipQuestions) as $k) { $ipScore += (int) ($ip[$k] ?? 0); }
    $ipProfile = match(true) {
        $ipScore <= 25  => 'Conservateur',
        $ipScore <= 55  => 'Modérément conservateur',
        $ipScore <= 90  => 'Équilibré',
        $ipScore <= 120 => 'Croissance',
        default         => 'Croissance agressive',
    };
    $ipFilled = $ipScore > 0;

    $goalsSelected = (array) data_get($payload, 'goals.selected', []);
    $goalsAnswers = (array) data_get($payload, 'goals.answers', []);
    $goalsLabels = [
    'retirement' => 'Retraite',
    'buy_house' => 'Achat / changement de propriété',
    'kids_education' => 'Études des enfants',
    'debt_repayment' => 'Remboursement dettes',
    'insurance' => 'Optimisation assurances',
    'investments' => 'Stratégie placements',
    'business' => 'Projet entreprise',
    'travel' => 'Voyages / style de vie',
    ];

    $prot = (array) data_get($payload, 'protections_details', []);
    @endphp

    {{-- ======================
     COVER (PAGE 1)
     ====================== --}}
    <div class="cover">
        @if($coverPhoto ?? null)
        <img class="cover-photo" src="{{ $coverPhoto }}" alt="">
        @endif
        <div class="cover-bg" style="{{ ($coverPhoto ?? null) ? 'background:rgba(14,16,48,.72)' : '' }}"></div>
        <div class="cover-inner">
            @if($logo)
            <img class="cover-logo" src="{{ $logo }}" alt="Logo VIP">
            @endif

            <div class="cover-title">
                Votre portrait<br>financier
                <div class="cover-underline"></div>
            </div>

            <div class="cover-names">
                {{ $clientName }}@if($hasSpouse)<br>{{ $spouseName }}@endif
            </div>

            <div class="cover-conf">
                <div class="title">AVIS DE CONFIDENTIALITÉ</div>
                <div>
                    Le contenu de ce document est strictement confidentiel. Il est interdit de copier, de faire suivre ou
                    d’utiliser les informations qu’il contient sans autorisation.
                </div>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- ======================
     HEADER / FOOTER (PAGE 2+)
     ====================== --}}
    <div class="header">
        <div class="left">VOTRE PORTRAIT FINANCIER</div>
        <div class="right">{{ mb_strtoupper($docDate) }}</div>
    </div>
    <div class="footer">
        <div class="left">vipgpi</div>
        <div class="right"><span class="pagenum"></span></div>
    </div>

    {{-- ======================
     TOC
     ====================== --}}
    <div class="toc">
        <div style="margin-top:18mm;"></div>
        <h1>Table des matières</h1>

        <div class="toc-item clearfix">
            <div class="name">Informations personnelles</div>
            <div class="page">—</div>
        </div>
        <div class="toc-item clearfix">
            <div class="name">Placements & actifs</div>
            <div class="page">—</div>
        </div>
        <div class="toc-item clearfix">
            <div class="name">Dettes & passifs</div>
            <div class="page">—</div>
        </div>
        <div class="toc-item clearfix">
            <div class="name">Bilan financier</div>
            <div class="page">—</div>
        </div>
        <div class="toc-item clearfix">
            <div class="name">Assurances</div>
            <div class="page">—</div>
        </div>
        <div class="toc-item clearfix">
            <div class="name">Budget au décès</div>
            <div class="page">—</div>
        </div>
        <div class="toc-item clearfix">
            <div class="name">Profil d'investisseur</div>
            <div class="page">—</div>
        </div>
        <div class="toc-item clearfix">
            <div class="name">Notes / recommandations</div>
            <div class="page">—</div>
        </div>
        <div class="toc-item clearfix">
            <div class="name">Accusé de réception</div>
            <div class="page">—</div>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- ======================
     INFORMATIONS PERSONNELLES
     ====================== --}}
    <h1>Informations personnelles</h1>

    <h2>Informations de base</h2>
    <table class="compare">
        <thead>
            <tr>
                <th></th>
                <th>{{ $clientName }}</th>
                @if($hasSpouse)<th>{{ $spouseName }}</th>@endif
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Date de naissance</td>
                <td>
                    {{ $client['birth_date'] ?? '—' }}
                    @if($age($client['birth_date'] ?? null) !== null)
                    <span class="muted">({{ $age($client['birth_date'] ?? null) }} ans)</span>
                    @endif
                </td>
                @if($hasSpouse)
                <td>
                    {{ $spouse['birth_date'] ?? '—' }}
                    @if($age($spouse['birth_date'] ?? null) !== null)
                    <span class="muted">({{ $age($spouse['birth_date'] ?? null) }} ans)</span>
                    @endif
                </td>
                @endif
            </tr>
            <tr>
                <td>État civil</td>
                <td>{{ $maritalLabel($client['marital_status'] ?? null) }}</td>
                @if($hasSpouse)<td>{{ $maritalLabel($spouse['marital_status'] ?? null) }}</td>@endif
            </tr>
            <tr>
                <td>Tabagisme</td>
                <td>
                    {{ $smokerLabel($client['smoker_status'] ?? null) }}
                    @if(($client['smoker_status'] ?? null) === 'smoker' && !blank($client['smoker_since'] ?? null))
                    <span class="muted">({{ $client['smoker_since'] }})</span>
                    @endif
                </td>
                @if($hasSpouse)
                <td>
                    {{ $smokerLabel($spouse['smoker_status'] ?? null) }}
                    @if(($spouse['smoker_status'] ?? null) === 'smoker' && !blank($spouse['smoker_since'] ?? null))
                    <span class="muted">({{ $spouse['smoker_since'] }})</span>
                    @endif
                </td>
                @endif
            </tr>
            <tr>
                <td>Adresse (principale)</td>
                <td>{{ $client['address'] ?? '—' }} @if(!blank($client['postal_code'] ?? null))<span class="muted">{{ $client['postal_code'] }}</span>@endif</td>
                @if($hasSpouse)
                <td>{{ $spouse['address'] ?? '—' }} @if(!blank($spouse['postal_code'] ?? null))<span class="muted">{{ $spouse['postal_code'] }}</span>@endif</td>
                @endif
            </tr>
            <tr>
                <td>Téléphone (domicile)</td>
                <td>{{ $client['home_phone'] ?? '—' }}</td>
                @if($hasSpouse)<td>{{ $spouse['home_phone'] ?? '—' }}</td>@endif
            </tr>
            <tr>
                <td>Courriel (principal)</td>
                <td>{{ $client['email'] ?? '—' }}</td>
                @if($hasSpouse)<td>{{ $spouse['email'] ?? '—' }}</td>@endif
            </tr>
            <tr>
                <td>Statut au Canada</td>
                <td>{{ $citLabel($client['citizenship_status'] ?? null) }}</td>
                @if($hasSpouse)<td>{{ $citLabel($spouse['citizenship_status'] ?? null) }}</td>@endif
            </tr>
            <tr>
                <td>NAS</td>
                <td>
                    @php $hs = data_get($client, 'has_sin'); @endphp
                    {{ $hs === null ? '—' : ((bool) $hs ? 'Oui' : 'Non') }}
                </td>
                @if($hasSpouse)
                <td>
                    @php $hs2 = data_get($spouse, 'has_sin'); @endphp
                    {{ $hs2 === null ? '—' : ((bool) $hs2 ? 'Oui' : 'Non') }}
                </td>
                @endif
            </tr>
            <tr>
                <td>Travaille au Canada depuis</td>
                <td>{{ $client['work_in_canada_since'] ?? '—' }}</td>
                @if($hasSpouse)<td>{{ $spouse['work_in_canada_since'] ?? '—' }}</td>@endif
            </tr>
        </tbody>
    </table>

    <h2>Emploi actuel</h2>
    <table class="compare">
        <thead>
            <tr>
                <th></th>
                <th>{{ $clientName }}</th>
                @if($hasSpouse)<th>{{ $spouseName }}</th>@endif
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Titre</td>
                <td>{{ data_get($client,'jobs.0.occupation') ?? '—' }}</td>
                @if($hasSpouse)<td>{{ data_get($spouse,'jobs.0.occupation') ?? '—' }}</td>@endif
            </tr>
            <tr>
                <td>Entreprise</td>
                <td>{{ data_get($client,'jobs.0.employer') ?? '—' }}</td>
                @if($hasSpouse)<td>{{ data_get($spouse,'jobs.0.employer') ?? '—' }}</td>@endif
            </tr>
            <tr>
                <td>Depuis</td>
                <td>{{ $client['employment_since'] ?? '—' }}</td>
                @if($hasSpouse)<td>{{ $spouse['employment_since'] ?? '—' }}</td>@endif
            </tr>
        </tbody>
    </table>

    <h2>Revenus</h2>
    <table class="compare">
        <thead>
            <tr>
                <th></th>
                <th>{{ $clientName }}</th>
                @if($hasSpouse)<th>{{ $spouseName }}</th>@endif
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Revenu d'emploi (annuel)</td>
                <td>{{ $money(data_get($client,'jobs.0.annual_income', 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(data_get($spouse,'jobs.0.annual_income', 0)) }}</td>@endif
            </tr>
            <tr>
                <td>Autres revenus (annuels)</td>
                <td>{{ $money(data_get($client,'other_income_annual', 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(data_get($spouse,'other_income_annual', 0)) }}</td>@endif
            </tr>
            <tr>
                <td>Autres revenus (mensuels)</td>
                <td>{{ $money(data_get($client,'other_income_monthly', 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(data_get($spouse,'other_income_monthly', 0)) }}</td>@endif
            </tr>
            <tr class="small">
                <td colspan="{{ $hasSpouse ? 3 : 2 }}" class="muted">* Montants bruts selon les informations fournies.</td>
            </tr>
        </tbody>
    </table>

    <h2>Personnes à charge</h2>
    @if(count($deps) === 0)
    <p class="muted">Aucune personne à charge.</p>
    @else
    <table class="simple">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Date de naissance</th>
                <th>Âge</th>
                <th>Lien</th>
                <th>Dépendance</th>
                <th>Même adresse</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deps as $d)
            @php
            $a = $age($d['birth_date'] ?? null);
            $rel = ['child'=>'Enfant','dependent'=>'Personne à charge','other'=>'Autre'][$d['relationship'] ?? ''] ?? '—';
            $dep = ['full'=>'Totale','partial'=>'Partielle','none'=>'Aucune'][$d['financial_dependency'] ?? ''] ?? '—';
            @endphp
            <tr>
                <td>{{ $d['name'] ?? '—' }}</td>
                <td>{{ $d['birth_date'] ?? '—' }}</td>
                <td>{{ $a === null ? '—' : ($a . ' ans') }}</td>
                <td>{{ $rel }}</td>
                <td>{{ $dep }}</td>
                <td>{{ (bool) ($d['same_contact_as_client'] ?? false) ? 'Oui' : 'Non' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h2>Documents légaux</h2>
    <table class="compare">
        <thead>
            <tr>
                <th></th>
                <th>{{ $clientName }}</th>
                @if($hasSpouse)<th>{{ $spouseName }}</th>@endif
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Testament</td>
                <td>{{ (bool) data_get($client,'legal.will_exists', false) ? 'Oui' : 'Non' }}</td>
                @if($hasSpouse)<td>{{ (bool) data_get($spouse,'legal.will_exists', false) ? 'Oui' : 'Non' }}</td>@endif
            </tr>
            <tr>
                <td>Mandat en cas d'inaptitude (QC)</td>
                <td>{{ (bool) data_get($client,'legal.mandate_incapacity_exists', false) ? 'Oui' : 'Non' }}</td>
                @if($hasSpouse)<td>{{ (bool) data_get($spouse,'legal.mandate_incapacity_exists', false) ? 'Oui' : 'Non' }}</td>@endif
            </tr>
            <tr>
                <td>Procuration (hors QC)</td>
                <td>{{ (bool) data_get($client,'legal.power_of_attorney_exists', false) ? 'Oui' : 'Non' }}</td>
                @if($hasSpouse)<td>{{ (bool) data_get($spouse,'legal.power_of_attorney_exists', false) ? 'Oui' : 'Non' }}</td>@endif
            </tr>
        </tbody>
    </table>

    <h2>Objectifs</h2>
    @if(count($goalsSelected) === 0)
    <p class="muted">Aucun objectif sélectionné.</p>
    @else
    <table class="simple">
        <thead>
            <tr>
                <th>Objectif</th>
                <th>Réponse / notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($goalsSelected as $k)
            <tr>
                <td style="width:35%;">{{ $goalsLabels[$k] ?? $k }}</td>
                <td>{{ trim((string) ($goalsAnswers[$k] ?? '')) ?: '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="page-break"></div>

    {{-- ======================
     PLACEMENTS & ACTIFS
     ====================== --}}
    <h1>Placements & actifs</h1>

    @php $byOwner = collect($assets)->groupBy(fn($a) => $a['owner'] ?? 'client'); @endphp

    @foreach(['client','spouse','joint'] as $owner)
    @php
    if ($owner === 'spouse' && ! $hasSpouse) continue;
    $rows = (array) ($byOwner[$owner] ?? []);
    if (count($rows) === 0) continue;
    $grouped = collect($rows)->groupBy(fn($a) => $a['type'] ?? 'other');
    @endphp

    <h2>{{ $ownerLabel($owner) }}</h2>
    <table class="simple">
        <thead>
            <tr>
                <th style="width:22%;">Type</th>
                <th>Description</th>
                <th style="width:18%;">Valeur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grouped as $type => $rows2)
            <tr class="total-row">
                <td colspan="3">{{ $assetType[$type] ?? $type }}</td>
            </tr>
            @foreach($rows2 as $aRow)
            <tr>
                <td class="muted">{{ $assetType[$type] ?? $type }}</td>
                <td>{{ $aRow['description'] ?? '—' }}</td>
                <td>{{ $money($aRow['value'] ?? 0) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Total</td>
                <td></td>
                <td>{{ $money(collect($rows2)->sum(fn($r)=> (float) ($r['value'] ?? 0))) }}</td>
            </tr>
            @endforeach
            <tr class="grand-total">
                <td>Total</td>
                <td></td>
                <td>{{ $money(collect($rows)->sum(fn($r)=> (float) ($r['value'] ?? 0))) }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach

    <div class="page-break"></div>

    {{-- ======================
     DETTES & PASSIFS
     ====================== --}}
    <h1>Dettes & passifs</h1>

    @php $liabsByOwner = collect($liabs)->groupBy(fn($l) => $l['owner'] ?? 'client'); @endphp

    @foreach(['client','spouse','joint'] as $owner)
    @php
    if ($owner === 'spouse' && ! $hasSpouse) continue;
    $rows = (array) ($liabsByOwner[$owner] ?? []);
    if (count($rows) === 0) continue;
    $grouped = collect($rows)->groupBy(fn($l) => $l['type'] ?? 'other');
    @endphp

    <h2>{{ $ownerLabel($owner) }}</h2>
    <table class="simple">
        <thead>
            <tr>
                <th style="width:24%;">Type</th>
                <th>Créancier</th>
                <th style="width:18%;">Solde</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grouped as $type => $rows2)
            <tr class="total-row">
                <td colspan="3">{{ $liabType[$type] ?? $type }}</td>
            </tr>
            @foreach($rows2 as $lRow)
            <tr>
                <td class="muted">{{ $liabType[$type] ?? $type }}</td>
                <td>{{ $lRow['creditor'] ?? '—' }}</td>
                <td>{{ $money($lRow['balance'] ?? 0) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Total</td>
                <td></td>
                <td>{{ $money(collect($rows2)->sum(fn($r)=> (float) ($r['balance'] ?? 0))) }}</td>
            </tr>
            @endforeach
            <tr class="grand-total">
                <td>Total</td>
                <td></td>
                <td>{{ $money(collect($rows)->sum(fn($r)=> (float) ($r['balance'] ?? 0))) }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach

    <div class="page-break"></div>

    {{-- ======================
     BILAN FINANCIER
     ====================== --}}
    <h1>Bilan financier</h1>

    <table class="simple">
        <thead>
            <tr>
                <th></th>
                <th>{{ $clientName }}</th>
                @if($hasSpouse)<th>{{ $spouseName }}</th>@endif
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Actifs</td>
                <td>{{ $money($assetSums['client'] ?? 0) }}</td>
                @if($hasSpouse)<td>{{ $money($assetSums['spouse'] ?? 0) }}</td>@endif
                <td>{{ $money($assetTotal) }}</td>
            </tr>
            <tr>
                <td>Passifs</td>
                <td>{{ $money($liabSums['client'] ?? 0) }}</td>
                @if($hasSpouse)<td>{{ $money($liabSums['spouse'] ?? 0) }}</td>@endif
                <td>{{ $money($liabTotal) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Net</td>
                <td>{{ $money(($assetSums['client'] ?? 0) - ($liabSums['client'] ?? 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(($assetSums['spouse'] ?? 0) - ($liabSums['spouse'] ?? 0)) }}</td>@endif
                <td>{{ $money($netTotal) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    @if($sec('lifeInsurance') || $sec('disability') || $sec('seriousIllness'))
    {{-- ======================
     ASSURANCES
     ====================== --}}
    <h1>Assurances</h1>

    @php
    $ppl = [
    'client' => ['name' => $clientName, 'data' => (array) ($prot['client'] ?? [])],
    'spouse' => ['name' => $spouseName, 'data' => (array) ($prot['spouse'] ?? [])],
    'children' => ['name' => 'Enfants', 'data' => (array) ($prot['children'] ?? [])],
    ];
    @endphp

    @foreach($ppl as $key => $block)
    @php
    if ($key === 'spouse' && ! $hasSpouse) continue;
    $life = (array) data_get($block['data'],'life', []);
    $dis = (array) data_get($block['data'],'disability', []);
    $ci = (array) data_get($block['data'],'critical_illness', []);
    if (count($life)+count($dis)+count($ci) === 0) continue;
    @endphp

    <h2>{{ $block['name'] }}</h2>

    @if(count($life) > 0)
    <h3>Assurance vie</h3>
    <table class="simple">
        <thead>
            <tr>
                <th>Société</th>
                <th>Type</th>
                <th style="width:22%;">Capital-décès</th>
                <th style="width:22%;">Prime annuelle</th>
            </tr>
        </thead>
        <tbody>
            @foreach($life as $r)
            <tr>
                <td>{{ $r['provider'] ?? '—' }}</td>
                <td>{{ $r['contract_type'] ?? '—' }}</td>
                <td>{{ $money($r['death_capital'] ?? 0) }}</td>
                <td>{{ $money($r['annual_premium'] ?? 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($ci) > 0)
    <h3>Assurance maladie grave</h3>
    <table class="simple">
        <thead>
            <tr>
                <th>Société</th>
                <th style="width:22%;">Capital assuré</th>
                <th style="width:22%;">Prime</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ci as $r)
            <tr>
                <td>{{ $r['provider'] ?? '—' }}</td>
                <td>{{ $money($r['insured_capital'] ?? 0) }}</td>
                <td>{{ $money($r['premium'] ?? 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($dis) > 0)
    <h3>Assurance invalidité</h3>
    <table class="simple">
        <thead>
            <tr>
                <th>Société</th>
                <th style="width:22%;">Revenu mensuel</th>
                <th style="width:22%;">Prime</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dis as $r)
            <tr>
                <td>{{ $r['provider'] ?? '—' }}</td>
                <td>{{ $money($r['monthly_income'] ?? 0) }}</td>
                <td>{{ $money($r['premium'] ?? 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endforeach
    @endif {{-- /sec lifeInsurance|disability|seriousIllness --}}

    @if($sec('lifeInsurance'))
    <div class="page-break"></div>

    {{-- ======================
     BUDGET AU DÉCÈS
     ====================== --}}
    <h1>Budget au décès</h1>

    @php $db = (array) data_get($results, 'death_budget.per_person', []); @endphp

    <table class="compare">
        <thead>
            <tr>
                <th></th>
                <th>{{ $clientName }}</th>
                @if($hasSpouse)<th>{{ $spouseName }}</th>@endif
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Liquidités nettes au décès (B1-B2)</td>
                <td>{{ $money(data_get($db,'client.b.net_liquidities', 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(data_get($db,'spouse.b.net_liquidities', 0)) }}</td>@endif
            </tr>
            <tr>
                <td>Revenu mensuel à combler (C2-C3)</td>
                <td>{{ $money(data_get($db,'client.c.monthly_gap', 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(data_get($db,'spouse.c.monthly_gap', 0)) }}</td>@endif
            </tr>
            <tr>
                <td>Capital requis (D)</td>
                <td>{{ $money(data_get($db,'client.d.capital_required', 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(data_get($db,'spouse.d.capital_required', 0)) }}</td>@endif
            </tr>
            <tr class="total-row">
                <td>Besoin d'assurance additionnel (E)</td>
                <td>{{ $money(data_get($db,'client.e.additional_need', 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(data_get($db,'spouse.e.additional_need', 0)) }}</td>@endif
            </tr>
        </tbody>
    </table>

    <p class="small muted" style="margin-top:6mm;">
        * Les résultats ci-dessus sont calculés à partir des hypothèses et des informations fournies. Ils doivent être validés et ajustés selon la situation réelle.
    </p>
    @endif {{-- /sec lifeInsurance --}}

    @if($sec('dashboard'))
    <div class="page-break"></div>

    {{-- ======================
     PROFIL INVESTISSEUR
     ====================== --}}
    <h1>Profil d'investisseur</h1>

    @if($ipFilled)
    {{-- Score badge --}}
    <table class="simple" style="margin-bottom:6mm;">
        <tbody>
            <tr>
                <td style="width:50%;"><strong>Score total</strong></td>
                <td>{{ $ipScore }} / 160</td>
            </tr>
            <tr>
                <td><strong>Profil déterminé</strong></td>
                <td><strong>{{ $ipProfile }}</strong></td>
            </tr>
        </tbody>
    </table>

    {{-- Questions par section --}}
    @php
    $currentSection = null;
    @endphp
    @foreach($ipQuestions as $key => $q)
    @if($q['section'] !== $currentSection)
    @php $currentSection = $q['section']; @endphp
    <h2>{{ $currentSection }}</h2>
    @endif
    @php
    $pts = (int) ($ip[$key] ?? 0);
    $answer = $pts > 0 ? ($q['options'][$pts] ?? '—') : '—';
    $ptLabel = $pts === 1 ? '1 point' : ($pts > 0 ? "{$pts} points" : '—');
    @endphp
    <table class="simple" style="margin-bottom:2mm;">
        <tbody>
            <tr>
                <td style="width:70%;">{{ $q['label'] }}<br><em style="color:#555;">{{ $answer }}</em></td>
                <td style="text-align:right;font-weight:700;">{{ $ptLabel }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
    @else
    <p class="muted">Le questionnaire du profil d'investisseur n'a pas encore été complété.</p>
    @endif
    @endif {{-- /sec dashboard --}}

    @if($sec('recommendations'))
    <div class="page-break"></div>

    {{-- ======================
     NOTES / RECO
     ====================== --}}
    <h1>Notes / recommandations</h1>

    @if(!blank(data_get($payload,'advisor_notes')))
    <h2>Notes du conseiller</h2>
    <p>{!! nl2br(e((string) data_get($payload,'advisor_notes'))) !!}</p>
    @else
    <p class="muted">Aucune note du conseiller.</p>
    @endif

    <h2>Avis de non-responsabilité</h2>
    <p class="muted">
        Cette analyse est basée sur les informations fournies par le client et sur les hypothèses retenues à la date du rapport.
        Les recommandations ne constituent pas une garantie de rendement ni de résultat futur.
    </p>
    @endif {{-- /sec recommendations --}}

    @if($sec('deliveryConfirmation', false))
    <div class="page-break"></div>

    {{-- ======================
     ACCUSÉ
     ====================== --}}
    <h1>Accusé de réception</h1>
    <p>Je confirme avoir pris connaissance du présent document « Votre portrait financier » et des hypothèses utilisées.</p>

    <table class="simple" style="margin-top:10mm;">
        <thead>
            <tr>
                <th style="width:40%;">Signature</th>
                <th style="width:30%;">Nom</th>
                <th style="width:30%;">Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="height:18mm;"></td>
                <td>{{ $clientName }}</td>
                <td>{{ $docDate }}</td>
            </tr>
            @if($hasSpouse)
            <tr>
                <td style="height:18mm;"></td>
                <td>{{ $spouseName }}</td>
                <td>{{ $docDate }}</td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif {{-- /sec deliveryConfirmation --}}

    @php
    $retData         = (array) data_get($payload, 'retraite', []);
    $regPubClient    = (array) data_get($retData, 'regimesPublics.client', []);
    $regPubConjoint  = (array) data_get($retData, 'regimesPublics.conjoint', []);
    $rpdList         = (array) data_get($retData, 'rpd', []);
    $retraitsList    = (array) data_get($retData, 'retraits', []);
    $retAgeClient    = (int) ($retData['ageClient'] ?? 65);
    $retAgeConjoint  = (int) ($retData['ageConjoint'] ?? 65);

    $fmtFreq = fn($f) => match($f) {
        'mensuel'   => '/mois',
        'annuel'    => '/an',
        'bimensuel' => '/2 sem.',
        default     => '',
    };
    $toAnnual = function($montant, $freq) {
        return match($freq ?? 'mensuel') {
            'mensuel'   => (float)$montant * 12,
            'bimensuel' => (float)$montant * 26,
            default     => (float)$montant,
        };
    };
    @endphp

    @if($sec('annex', false) && $sec('retirementIncome', false))
    <div class="page-break"></div>

    {{-- ======================
     ANNEXE : REVENUS DE RETRAITE
     ====================== --}}
    <h1>Annexe — Revenus de retraite</h1>
    <p class="muted small">Estimations basées sur les informations fournies. Les montants des régimes publics sont indexés.</p>

    @foreach([['role'=>'client','label'=>$clientName,'age'=>$retAgeClient,'pub'=>$regPubClient],
              ($hasSpouse ? ['role'=>'conjoint','label'=>$spouseName,'age'=>$retAgeConjoint,'pub'=>$regPubConjoint] : null)] as $_block)
    @if($_block === null) @continue @endif
    <h2>{{ $_block['label'] }} — retraite à {{ $_block['age'] }} ans</h2>
    <table class="simple">
        <thead>
            <tr>
                <th>Source de revenu</th>
                <th style="width:20%;">Montant</th>
                <th style="width:18%;">Fréquence</th>
                <th style="width:20%;">Âge de début</th>
                <th style="width:20%;">Annuel estimé</th>
            </tr>
        </thead>
        <tbody>
            @foreach($_block['pub'] as $_r)
            <tr>
                <td>{{ $_r['label'] ?? $_r['id'] ?? '—' }}</td>
                <td>{{ $money($_r['montant'] ?? 0) }}</td>
                <td>{{ $fmtFreq($_r['frequence'] ?? 'mensuel') }}</td>
                <td>{{ $_r['debut'] ?? 65 }} ans</td>
                <td>{{ $money($toAnnual($_r['montant'] ?? 0, $_r['frequence'] ?? 'mensuel')) }}</td>
            </tr>
            @endforeach
            @foreach($rpdList as $_r)
            @if(($_r['role'] ?? '') === $_block['role'])
            <tr>
                <td>{{ $_r['nom'] ?? 'Régime privé' }}</td>
                <td>{{ $money($_r['montant'] ?? 0) }}</td>
                <td>{{ $fmtFreq($_r['frequence'] ?? 'mensuel') }}</td>
                <td>{{ $_r['debut'] ?? '—' }} ans</td>
                <td>{{ $money($toAnnual($_r['montant'] ?? 0, $_r['frequence'] ?? 'mensuel')) }}</td>
            </tr>
            @endif
            @endforeach
            @foreach($retraitsList as $_r)
            @if(($_r['role'] ?? '') === $_block['role'])
            <tr>
                <td>{{ $_r['desc'] ?? $_r['type'] ?? 'Retrait' }}</td>
                <td>{{ $money($_r['montant'] ?? 0) }}</td>
                <td>{{ $fmtFreq($_r['frequence'] ?? 'mensuel') }}</td>
                <td>{{ $_r['debut'] ?? '—' }}</td>
                <td>{{ $money($toAnnual($_r['montant'] ?? 0, $_r['frequence'] ?? 'mensuel')) }}</td>
            </tr>
            @endif
            @endforeach
            @php
            $totalAnnuel = collect($_block['pub'])->sum(fn($r) => $toAnnual($r['montant'] ?? 0, $r['frequence'] ?? 'mensuel'))
                + collect($rpdList)->where('role', $_block['role'])->sum(fn($r) => $toAnnual($r['montant'] ?? 0, $r['frequence'] ?? 'mensuel'))
                + collect($retraitsList)->where('role', $_block['role'])->sum(fn($r) => $toAnnual($r['montant'] ?? 0, $r['frequence'] ?? 'mensuel'));
            @endphp
            <tr class="grand-total">
                <td colspan="4">Total annuel estimé</td>
                <td>{{ $money($totalAnnuel) }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
    @endif {{-- /sec retirementIncome --}}

    @if($sec('annex', false) && $sec('investmentProjection', false))
    @php
    $currentAge   = $age($client['birth_date'] ?? null) ?? 40;
    $targetAge    = $retAgeClient ?: 65;
    $years        = max(1, $targetAge - $currentAge);
    $currentValue = $assetTotal;
    $annualSavings= (float) data_get($client, 'jobs.0.annual_income', 0) * 0.10; // 10% savings assumption
    $rate         = (float) (data_get($payload, 'hypotheses.return_rate', 5) ?: 5) / 100;
    $projRows     = [];
    $v = $currentValue;
    for ($y = 1; $y <= min($years, 40); $y++) {
        $v = ($v + $annualSavings) * (1 + $rate);
        if ($y % 5 === 0 || $y === 1 || $y === $years) {
            $projRows[] = ['age' => $currentAge + $y, 'an' => $y, 'valeur' => $v];
        }
    }
    @endphp
    <div class="page-break"></div>

    {{-- ======================
     ANNEXE : ÉVOLUTION DES PLACEMENTS
     ====================== --}}
    <h1>Annexe — Évolution des placements</h1>
    <p class="muted small">
        Projection basée sur une valeur initiale de {{ $money($currentValue) }},
        des cotisations annuelles estimées de {{ $money($annualSavings) }}
        et un taux de rendement hypothétique de {{ number_format($rate * 100, 1) }}% par année.
        Ces projections sont indicatives et ne constituent pas une garantie.
    </p>
    <table class="simple">
        <thead>
            <tr>
                <th style="width:20%;">Âge</th>
                <th style="width:20%;">Années</th>
                <th>Valeur projetée</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projRows as $_row)
            <tr @if($_row['age'] >= $targetAge) class="total-row" @endif>
                <td>{{ $_row['age'] }} ans</td>
                <td>{{ $_row['an'] }} an{{ $_row['an'] > 1 ? 's' : '' }}</td>
                <td>{{ $money($_row['valeur']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif {{-- /sec investmentProjection --}}

</body>

</html>