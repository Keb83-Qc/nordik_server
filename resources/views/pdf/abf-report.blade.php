<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Votre portrait financier</title>

    <style>
        /* =============================================
           PAGE SETUP
           ============================================= */
        /* Margins controlled by mPDF constructor + <pagebreak> tags */

        /* =============================================
           BASE
           ============================================= */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            color: #1A1C2E;
            line-height: 1.5;
        }

        p {
            margin: 0 0 6px 0;
            line-height: 1.5;
        }

        /* Page breaks handled by mPDF <pagebreak /> tags */
        .muted      { color: #6B7280; }
        .small      { font-size: 9.5px; }
        .strong     { font-weight: 700; }

        /* =============================================
           SECTION HEADINGS
           ============================================= */
        h1 {
            font-family: DejaVu Serif, serif;
            font-size: 20px;
            font-weight: 700;
            color: #FFFFFF;
            background-color: #0E1030;
            margin: 0 0 6mm 0;
            padding: 5mm 6mm 5mm 8mm;
            border-left: 5px solid #C9A050;
            page-break-after: avoid;
        }

        h2 {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            font-weight: 700;
            color: #0E1030;
            margin: 6mm 0 2mm 0;
            padding: 0 0 3px 8px;
            border-left: 3px solid #C9A050;
            page-break-after: avoid;
        }

        h3 {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin: 4mm 0 2mm 0;
            page-break-after: avoid;
        }

        /* =============================================
           HEADER / FOOTER (page 2+)
           ============================================= */
        .header {
            width: 100%;
            height: 14mm;
            background: #0E1030;
            padding: 0 2mm;
            font-size: 9px;
            color: #FFFFFF;
            overflow: hidden;
        }
        .header .left {
            float: left;
            line-height: 14mm;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header .right {
            float: right;
            line-height: 14mm;
            color: #C9A050;
            font-weight: 700;
        }
        .header-logo {
            float: right;
            height: 8mm;
            margin-top: 3mm;
            margin-left: 6mm;
        }

        .footer {
            width: 100%;
            height: 12mm;
            background: #F8F5EE;
            border-top: 2px solid #C9A050;
            padding: 0 2mm;
            font-size: 9px;
            overflow: hidden;
        }
        .footer .left {
            float: left;
            line-height: 12mm;
            color: #6B7280;
        }
        .footer .right {
            float: right;
            line-height: 12mm;
            color: #0E1030;
            font-weight: 700;
        }
        /* Page numbers use mPDF {PAGENO} placeholder */

        /* =============================================
           COVER PAGE
           ============================================= */
        .cover {
            position: relative;
            height: 297mm;
            width: 210mm;
            overflow: hidden;
        }
        .cover-photo {
            position: absolute;
            top: 0; left: 0;
            width: 210mm;
            height: 297mm;
            object-fit: cover;
            object-position: center;
        }
        .cover-bg {
            position: absolute;
            top: 0; left: 0;
            width: 210mm;
            height: 297mm;
            background: #0E1030;
        }
        .cover-inner {
            position: relative;
            height: 297mm;
            padding: 0 18mm;
            color: #FFFFFF;
        }
        .cover-header {
            padding-top: 14mm;
            padding-bottom: 10mm;
            border-bottom: 1px solid rgba(201,160,80,.35);
        }
        .cover-logo-img {
            height: 12mm;
            width: auto;
        }
        .cover-body {
            padding-top: 28mm;
        }
        .cover-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #C9A050;
            margin-bottom: 4mm;
        }
        .cover-title {
            font-family: DejaVu Serif, serif;
            font-size: 42px;
            font-weight: 700;
            line-height: 1.05;
            color: #FFFFFF;
        }
        .cover-gold-bar {
            width: 18mm;
            height: 3px;
            background-color: #C9A050;
            margin: 6mm 0;
        }
        .cover-subtitle {
            font-size: 12px;
            color: rgba(255,255,255,.75);
            margin-bottom: 10mm;
        }
        .cover-client-block {
            margin-top: 12mm;
            padding: 5mm 6mm;
            border-left: 3px solid #C9A050;
            background: rgba(255,255,255,.06);
        }
        .cover-client-label {
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,.55);
            margin-bottom: 2mm;
        }
        .cover-client-name {
            font-size: 16px;
            font-weight: 700;
            color: #FFFFFF;
            line-height: 1.3;
        }
        .cover-date-block {
            margin-top: 6mm;
            font-size: 10px;
            color: rgba(255,255,255,.65);
        }
        .cover-footer-band {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 5mm 18mm;
            background: rgba(9,10,31,.85);
            border-top: 1px solid rgba(201,160,80,.25);
        }
        .cover-footer-conf {
            font-size: 8px;
            color: rgba(255,255,255,.55);
            line-height: 1.5;
        }
        .cover-footer-conf strong {
            color: #C9A050;
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
            letter-spacing: .5px;
        }

        /* =============================================
           TABLES
           ============================================= */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4mm;
        }

        /* --- Tableau comparatif (client / conjoint) --- */
        .compare thead tr {
            background: #0E1030;
        }
        .compare thead th {
            font-size: 10px;
            font-weight: 700;
            color: #FFFFFF;
            padding: 5px 8px;
            text-align: left;
        }
        .compare thead th:first-child {
            background: #1A234A;
        }
        .compare tbody tr:nth-child(even) td {
            background: #F8F5EE;
        }
        .compare tbody td {
            padding: 6px 8px;
            border-bottom: 1px solid #EAD9B6;
            vertical-align: top;
            font-size: 10.5px;
        }
        .compare tbody td:first-child {
            width: 36%;
            color: #6B7280;
            font-size: 10px;
        }

        /* --- Tableau simple (listes) --- */
        .simple thead tr {
            background: #F8F5EE;
            border-bottom: 2px solid #C9A050;
        }
        .simple thead th {
            font-size: 10px;
            font-weight: 700;
            color: #0E1030;
            padding: 5px 8px;
            text-align: left;
        }
        .simple tbody tr:nth-child(even) td {
            background: #FDFCF8;
        }
        .simple tbody td {
            padding: 6px 8px;
            border-bottom: 1px solid #EAD9B6;
            vertical-align: top;
            font-size: 10.5px;
        }

        /* Ligne de sous-total */
        .subtotal-row td {
            background: #F0EBE0;
            font-weight: 700;
            font-size: 10px;
            color: #0E1030;
            border-top: 1px solid #C9A050;
        }

        /* Ligne de total général */
        .total-row td {
            background: #0E1030;
            color: #C9A050;
            font-weight: 700;
            font-size: 10.5px;
            border-bottom: none;
        }

        /* Ligne grand total (alias) */
        .grand-total td {
            background: #0E1030;
            color: #C9A050;
            font-weight: 700;
            border-bottom: none;
        }

        /* =============================================
           STAT CARDS (budget décès, etc.)
           ============================================= */
        .stat-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4mm 0;
            margin-bottom: 4mm;
        }
        .stat-card {
            padding: 4mm 5mm;
            border: 1px solid #EAD9B6;
            border-top: 3px solid #C9A050;
            background: #FDFCF8;
            vertical-align: top;
        }
        .stat-card.danger {
            border-top-color: #DC2626;
        }
        .stat-card.success {
            border-top-color: #16A34A;
        }
        .stat-card-label {
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6B7280;
            margin-bottom: 2mm;
        }
        .stat-card-value {
            font-size: 16px;
            font-weight: 700;
            color: #0E1030;
        }
        .stat-card-sub {
            font-size: 9px;
            color: #6B7280;
            margin-top: 1mm;
        }

        /* =============================================
           BADGE / PILL
           ============================================= */
        .badge {
            display: inline;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 3px;
            letter-spacing: .3px;
        }
        .badge-navy   { background: #0E1030; color: #FFFFFF; }
        .badge-gold   { background: #FBF6EA; color: #C9A050; border: 1px solid #C9A050; }
        .badge-green  { background: #DCFCE7; color: #15803D; }
        .badge-red    { background: #FEE2E2; color: #DC2626; }
        .badge-gray   { background: #F3F4F6; color: #6B7280; }

        /* =============================================
           INFO BOX
           ============================================= */
        .info-box {
            background: #F8F5EE;
            border-left: 3px solid #C9A050;
            padding: 3mm 4mm;
            margin: 3mm 0 4mm 0;
            font-size: 10px;
            color: #6B7280;
        }

        /* =============================================
           TABLE OF CONTENTS
           ============================================= */
        .toc-section {
            margin-top: 8mm;
        }
        .toc-item {
            padding: 5px 0;
            border-bottom: 1px solid #EAD9B6;
        }
        .toc-num {
            float: left;
            width: 6mm;
            font-size: 9px;
            color: #C9A050;
            font-weight: 700;
        }
        .toc-name {
            margin-left: 8mm;
            font-size: 11px;
            color: #1A1C2E;
        }
        .toc-dots {
            float: right;
            color: #EAD9B6;
        }
        .clearfix:after { content: ""; display: block; clear: both; }

        /* =============================================
           MISC
           ============================================= */
        .section-gap   { margin-top: 4mm; }
        .divider {
            border: none;
            border-top: 1px solid #EAD9B6;
            margin: 4mm 0;
        }
        .advisor-sig-line {
            border-top: 1px solid #0E1030;
            margin-top: 14mm;
            padding-top: 2mm;
            font-size: 9px;
            color: #6B7280;
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

    // Pre-computed section flags (Blade cannot parse single quotes inside directives)
    $ownersList        = ['client', 'spouse', 'joint'];
    $showAssurances    = $sec('lifeInsurance') || $sec('disability') || $sec('seriousIllness');
    $showDeathBudget   = $sec('lifeInsurance');
    $showDashboard     = $sec('dashboard');
    $showReco          = $sec('recommendations');
    $hasAdvisorNotes   = !blank(data_get($payload, 'advisor_notes'));
    $showDelivery      = $sec('deliveryConfirmation', false);
    $showRetIncome     = $sec('annex', false) && $sec('retirementIncome', false);
    $showInvProjection = $sec('annex', false) && $sec('investmentProjection', false);
    @endphp

    {{-- ====== mPDF: running header & footer (not rendered inline) ====== --}}
    <htmlpageheader name="main-header">
        <div class="header">
            @if($logo)
            <img class="header-logo" src="{{ $logo }}" alt="VIP GPI">
            @endif
            <div class="left">Portrait financier &mdash; {{ $clientName }}@if($hasSpouse) &amp; {{ $spouseName }}@endif</div>
            <div class="right">{{ $docDate }}</div>
        </div>
    </htmlpageheader>

    <htmlpagefooter name="main-footer">
        <div class="footer">
            <div class="left">VIP Gestion de patrimoine et investissements inc.</div>
            <div class="right">Page&nbsp;{PAGENO}</div>
        </div>
    </htmlpagefooter>

    {{-- ======================
     COVER (PAGE 1)
     ====================== --}}
    <div class="cover">
        @if($coverPhoto ?? null)
        <img class="cover-photo" src="{{ $coverPhoto }}" alt="">
        @endif
        <div class="cover-bg" style="{{ ($coverPhoto ?? null) ? ‘background:rgba(9,10,31,.78)’ : ‘’ }}"></div>
        <div class="cover-inner">

            {{-- En-tête : logo + nom firme --}}
            <div class="cover-header clearfix">
                @if($logo)
                <img class="cover-logo-img" src="{{ $logo }}" alt="VIP GPI" style="float:left">
                @endif
                <div style="float:right;text-align:right;padding-top:1mm">
                    <div style="font-size:9px;color:rgba(255,255,255,.5);letter-spacing:.5px">VIP GESTION DE PATRIMOINE</div>
                    <div style="font-size:8px;color:#C9A050;letter-spacing:.3px">ET INVESTISSEMENTS INC.</div>
                </div>
            </div>

            {{-- Corps principal --}}
            <div class="cover-body">
                <div class="cover-label">Analyse des besoins financiers</div>
                <div class="cover-title">Votre portrait<br>financier</div>
                <div class="cover-gold-bar"></div>
                <div class="cover-subtitle">Préparé le {{ $docDate }}</div>

                {{-- Bloc client --}}
                <div class="cover-client-block">
                    <div class="cover-client-label">Préparé pour</div>
                    <div class="cover-client-name">{{ $clientName }}</div>
                    @if($hasSpouse)
                    <div class="cover-client-name" style="font-size:14px;opacity:.85;margin-top:1mm">{{ $spouseName }}</div>
                    @endif
                </div>

                @php
                $advisorName = auth()->user()?->name ?? data_get($case->advisor, ‘name’, ‘’);
                @endphp
                @if($advisorName)
                <div class="cover-date-block">
                    Conseiller&nbsp;: <strong style="color:rgba(255,255,255,.85)">{{ $advisorName }}</strong>
                </div>
                @endif
            </div>

            {{-- Pied de page couverture --}}
            <div class="cover-footer-band">
                <div class="cover-footer-conf">
                    <strong>DOCUMENT CONFIDENTIEL</strong>
                    Ce document est strictement confidentiel. Il est réservé à l’usage exclusif du ou des destinataires désignés. Toute reproduction, distribution ou utilisation non autorisée est interdite.
                </div>
            </div>

        </div>
    </div>

    <pagebreak odd-header-name="main-header" odd-footer-name="main-footer"
               margin-top="30" margin-bottom="20" margin-left="18" margin-right="18"
               margin-header="3" margin-footer="3" />

    {{-- ======================
     TOC
     ====================== --}}
    <h1>Table des matières</h1>

    <div class="toc-section">
        @php
        $tocItems = [
            [‘Informations personnelles’,  true],
            [‘Placements & actifs’,        true],
            [‘Dettes & passifs’,           true],
            [‘Bilan financier’,            true],
            [‘Assurances’,                 $sec(‘lifeInsurance’) || $sec(‘disability’) || $sec(‘seriousIllness’)],
            [‘Budget au décès’,            $sec(‘lifeInsurance’)],
            [‘Profil d\’investisseur’,     $sec(‘dashboard’)],
            [‘Notes / recommandations’,    $sec(‘recommendations’)],
            [‘Accusé de réception’,        $sec(‘deliveryConfirmation’, false)],
            [‘Annexe — Revenus de retraite’, $sec(‘annex’, false) && $sec(‘retirementIncome’, false)],
            [‘Annexe — Évolution des placements’, $sec(‘annex’, false) && $sec(‘investmentProjection’, false)],
        ];
        $n = 1;
        @endphp
        @foreach($tocItems as [$name, $show])
        @if($show)
        <div class="toc-item clearfix">
            <span class="toc-num">{{ $n++ }}</span>
            <span class="toc-name">{{ $name }}</span>
        </div>
        @endif
        @endforeach
    </div>

    <pagebreak />

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

    <pagebreak />

    {{-- ======================
     PLACEMENTS & ACTIFS
     ====================== --}}
    <h1>Placements & actifs</h1>

    @php $byOwner = collect($assets)->groupBy(fn($a) => $a['owner'] ?? 'client'); @endphp

    @foreach($ownersList as $owner)
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
                <th style="width:26%;">Type</th>
                <th>Description</th>
                <th style="width:20%;">Valeur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grouped as $type => $rows2)
            <tr style="background:#F0EBE0;">
                <td colspan="3" style="font-weight:700;font-size:9.5px;color:#0E1030;padding:4px 8px;border-bottom:1px solid #C9A050;">{{ $assetType[$type] ?? $type }}</td>
            </tr>
            @foreach($rows2 as $aRow)
            <tr>
                <td class="muted">{{ $assetType[$type] ?? $type }}</td>
                <td>{{ $aRow['description'] ?? '—' }}</td>
                <td>{{ $money($aRow['value'] ?? 0) }}</td>
            </tr>
            @endforeach
            @endforeach
            <tr class="grand-total">
                <td colspan="2">Total — {{ $ownerLabel($owner) }}</td>
                <td>{{ $money(collect($rows)->sum(fn($r)=> (float) ($r['value'] ?? 0))) }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach

    <pagebreak />

    {{-- ======================
     DETTES & PASSIFS
     ====================== --}}
    <h1>Dettes & passifs</h1>

    @php $liabsByOwner = collect($liabs)->groupBy(fn($l) => $l['owner'] ?? 'client'); @endphp

    @foreach($ownersList as $owner)
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
            <tr style="background:#FEF2F2;">
                <td colspan="3" style="font-weight:700;font-size:9.5px;color:#DC2626;padding:4px 8px;border-bottom:1px solid #FECACA;">{{ $liabType[$type] ?? $type }}</td>
            </tr>
            @foreach($rows2 as $lRow)
            <tr>
                <td class="muted">{{ $liabType[$type] ?? $type }}</td>
                <td>{{ $lRow['creditor'] ?? '—' }}</td>
                <td>{{ $money($lRow['balance'] ?? 0) }}</td>
            </tr>
            @endforeach
            @endforeach
            <tr class="grand-total">
                <td colspan="2">Total — {{ $ownerLabel($owner) }}</td>
                <td>{{ $money(collect($rows)->sum(fn($r)=> (float) ($r['balance'] ?? 0))) }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach

    <pagebreak />

    {{-- ======================
     BILAN FINANCIER
     ====================== --}}
    <h1>Bilan financier</h1>

    {{-- Stat cards résumé --}}
    <table class="stat-grid">
        <tr>
            <td class="stat-card success" style="width:33%">
                <div class="stat-card-label">Total des actifs</div>
                <div class="stat-card-value">{{ $money($assetTotal) }}</div>
            </td>
            <td class="stat-card danger" style="width:33%">
                <div class="stat-card-label">Total des passifs</div>
                <div class="stat-card-value">{{ $money($liabTotal) }}</div>
            </td>
            <td class="stat-card" style="width:33%;border-top-color:{{ $netTotal >= 0 ? '#16A34A' : '#DC2626' }}">
                <div class="stat-card-label">Valeur nette</div>
                <div class="stat-card-value" style="color:{{ $netTotal >= 0 ? '#16A34A' : '#DC2626' }}">{{ $money($netTotal) }}</div>
            </td>
        </tr>
    </table>

    <h2>Par propriétaire</h2>
    <table class="compare">
        <thead>
            <tr>
                <th></th>
                <th>{{ $clientName }}</th>
                @if($hasSpouse)<th>{{ $spouseName }}</th>@endif
                @if($hasSpouse)<th>Commun</th>@endif
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Actifs</td>
                <td>{{ $money($assetSums['client'] ?? 0) }}</td>
                @if($hasSpouse)<td>{{ $money($assetSums['spouse'] ?? 0) }}</td>@endif
                @if($hasSpouse)<td>{{ $money($assetSums['joint'] ?? 0) }}</td>@endif
                <td>{{ $money($assetTotal) }}</td>
            </tr>
            <tr>
                <td>Passifs</td>
                <td>{{ $money($liabSums['client'] ?? 0) }}</td>
                @if($hasSpouse)<td>{{ $money($liabSums['spouse'] ?? 0) }}</td>@endif
                @if($hasSpouse)<td>{{ $money($liabSums['joint'] ?? 0) }}</td>@endif
                <td>{{ $money($liabTotal) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Valeur nette</td>
                <td>{{ $money(($assetSums['client'] ?? 0) - ($liabSums['client'] ?? 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(($assetSums['spouse'] ?? 0) - ($liabSums['spouse'] ?? 0)) }}</td>@endif
                @if($hasSpouse)<td>{{ $money(($assetSums['joint'] ?? 0) - ($liabSums['joint'] ?? 0)) }}</td>@endif
                <td>{{ $money($netTotal) }}</td>
            </tr>
        </tbody>
    </table>

    <pagebreak />

    @if($showAssurances)
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

    @if($showDeathBudget)
    <pagebreak />

    {{-- ======================
     BUDGET AU DÉCÈS
     ====================== --}}
    <h1>Budget au décès</h1>

    @php $db = (array) data_get($results, 'death_budget.per_person', []); @endphp

    {{-- Stat cards résumé --}}
    <table class="stat-grid">
        <tr>
            <td class="stat-card" style="width:{{ $hasSpouse ? '50%' : '100%' }}">
                <div class="stat-card-label">{{ $clientName }} — Besoin additionnel</div>
                <div class="stat-card-value {{ data_get($db,'client.e.additional_need',0) > 0 ? 'danger' : '' }}">
                    {{ $money(data_get($db,'client.e.additional_need', 0)) }}
                </div>
                <div class="stat-card-sub">Capital-décès requis&nbsp;: {{ $money(data_get($db,'client.d.capital_required', 0)) }}</div>
            </td>
            @if($hasSpouse)
            <td class="stat-card">
                <div class="stat-card-label">{{ $spouseName }} — Besoin additionnel</div>
                <div class="stat-card-value {{ data_get($db,'spouse.e.additional_need',0) > 0 ? 'danger' : '' }}">
                    {{ $money(data_get($db,'spouse.e.additional_need', 0)) }}
                </div>
                <div class="stat-card-sub">Capital-décès requis&nbsp;: {{ $money(data_get($db,'spouse.d.capital_required', 0)) }}</div>
            </td>
            @endif
        </tr>
    </table>

    <h2>Détail du calcul</h2>
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
                <td>Liquidités nettes au décès</td>
                <td>{{ $money(data_get($db,'client.b.net_liquidities', 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(data_get($db,'spouse.b.net_liquidities', 0)) }}</td>@endif
            </tr>
            <tr>
                <td>Revenu mensuel à combler</td>
                <td>{{ $money(data_get($db,'client.c.monthly_gap', 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(data_get($db,'spouse.c.monthly_gap', 0)) }}</td>@endif
            </tr>
            <tr>
                <td>Capital requis</td>
                <td>{{ $money(data_get($db,'client.d.capital_required', 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(data_get($db,'spouse.d.capital_required', 0)) }}</td>@endif
            </tr>
            <tr class="total-row">
                <td>Besoin d'assurance additionnel</td>
                <td>{{ $money(data_get($db,'client.e.additional_need', 0)) }}</td>
                @if($hasSpouse)<td>{{ $money(data_get($db,'spouse.e.additional_need', 0)) }}</td>@endif
            </tr>
        </tbody>
    </table>

    <div class="info-box">
        Les résultats sont calculés à partir des hypothèses et des informations fournies. Ils doivent être validés selon la situation réelle du client.
    </div>
    @endif {{-- /sec lifeInsurance --}}

    @if($showDashboard)
    <pagebreak />

    {{-- ======================
     PROFIL INVESTISSEUR
     ====================== --}}
    <h1>Profil d'investisseur</h1>

    @if($ipFilled)
    {{-- Stat card profil --}}
    @php
    $profileColor = match(true) {
        $ipScore <= 25  => '#3B82F6',
        $ipScore <= 55  => '#22C55E',
        $ipScore <= 90  => '#C9A050',
        $ipScore <= 120 => '#F97316',
        default         => '#EF4444',
    };
    @endphp
    <table class="stat-grid">
        <tr>
            <td class="stat-card" style="width:50%;border-top-color:{{ $profileColor }}">
                <div class="stat-card-label">Profil d'investisseur</div>
                <div class="stat-card-value">{{ $ipProfile }}</div>
                <div class="stat-card-sub">Score&nbsp;: {{ $ipScore }} / 160</div>
            </td>
            <td style="width:50%;padding-left:4mm;vertical-align:middle">
                <div class="info-box" style="margin:0">
                    Ce profil détermine l'allocation d'actifs recommandée selon la tolérance au risque, l'horizon de placement et la situation financière du client.
                </div>
            </td>
        </tr>
    </table>

    {{-- Questions par section --}}
    @php
    $currentSection = null;
    @endphp
    @foreach($ipQuestions as $key => $q)
    @php $sectionChanged = $q['section'] !== $currentSection; @endphp
    @if($sectionChanged)
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

    @if($showReco)
    <pagebreak />

    {{-- ======================
     NOTES / RECO
     ====================== --}}
    <h1>Notes / recommandations</h1>

    @if($hasAdvisorNotes)
    <h2>Notes du conseiller</h2>
    <p>{!! nl2br(e((string) data_get($payload,'advisor_notes'))) !!}</p>
    @else
    <p class="muted">Aucune note du conseiller.</p>
    @endif

    <div class="info-box">
        <strong>Avis de non-responsabilité :</strong> Cette analyse est basée sur les informations fournies par le client et sur les hypothèses retenues à la date du rapport. Les recommandations ne constituent pas une garantie de rendement ni de résultat futur.
    </div>
    @endif {{-- /sec recommendations --}}

    @if($showDelivery)
    <pagebreak />

    {{-- ======================
     ACCUSÉ
     ====================== --}}
    <h1>Confirmation de remise</h1>
    <p style="margin-bottom:6mm;">Je, soussigné(e), confirme avoir reçu et pris connaissance du document « Votre portrait financier » préparé par <strong>VIP Gestion de patrimoine et investissements inc.</strong> en date du {{ $docDate }}, ainsi que des hypothèses et des informations qui y sont contenues.</p>

    @php
    $signataires = array_values(array_filter([
        ['name' => $clientName,          'show' => true,                          'role' => ''],
        ['name' => $spouseName,          'show' => $hasSpouse,                    'role' => ''],
        ['name' => $advisorName ?? '',   'show' => !empty($advisorName ?? ''),    'role' => 'Conseiller'],
    ], fn($r) => $r['show']));
    @endphp
    @foreach($signataires as $sig)
    <table class="simple" style="margin-bottom:8mm;">
        <tbody>
            <tr>
                <td style="width:45%;height:16mm;border-bottom:1px solid #0E1030;vertical-align:bottom;padding-bottom:2mm;font-size:9px;color:#6B7280">
                    Signature {{ $sig['role'] ?? '' }}
                </td>
                <td style="width:5%;"></td>
                <td style="width:30%;border-bottom:1px solid #0E1030;vertical-align:bottom;padding-bottom:2mm">
                    <div style="font-size:9px;color:#6B7280">Nom</div>
                    <div style="font-size:11px;font-weight:700;color:#0E1030">{{ $sig['name'] }}</div>
                </td>
                <td style="width:5%;"></td>
                <td style="width:15%;border-bottom:1px solid #0E1030;vertical-align:bottom;padding-bottom:2mm">
                    <div style="font-size:9px;color:#6B7280">Date</div>
                    <div style="font-size:10px;color:#0E1030">{{ $docDate }}</div>
                </td>
            </tr>
        </tbody>
    </table>
    @endforeach
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

    @if($showRetIncome)
    <pagebreak />

    {{-- ======================
     ANNEXE : REVENUS DE RETRAITE
     ====================== --}}
    <h1>Annexe — Revenus de retraite</h1>
    <p class="muted small">Estimations basées sur les informations fournies. Les montants des régimes publics sont indexés.</p>

    @php
    $retireBlocks = array_filter([
        ['role' => 'client',  'label' => $clientName,  'age' => $retAgeClient,  'pub' => $regPubClient],
        $hasSpouse ? ['role' => 'conjoint', 'label' => $spouseName, 'age' => $retAgeConjoint, 'pub' => $regPubConjoint] : null,
    ]);
    @endphp
    @foreach($retireBlocks as $_block)
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
            @php $rpdMatch = ($_r['role'] ?? '') === $_block['role']; @endphp
            @if($rpdMatch)
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
            @php $retraitMatch = ($_r['role'] ?? '') === $_block['role']; @endphp
            @if($retraitMatch)
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

    @if($showInvProjection)
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
    <pagebreak />

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