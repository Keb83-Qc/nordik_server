<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Votre portrait financier</title>

    <style>
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
        .muted      { color: #6B7280; }
        .small      { font-size: 9.5px; }
        .strong     { font-weight: 700; }

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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4mm;
        }
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
        .subtotal-row td {
            background: #F0EBE0;
            font-weight: 700;
            font-size: 10px;
            color: #0E1030;
            border-top: 1px solid #C9A050;
        }
        .total-row td {
            background: #0E1030;
            color: #C9A050;
            font-weight: 700;
            font-size: 10.5px;
            border-bottom: none;
        }
        .grand-total td {
            background: #0E1030;
            color: #C9A050;
            font-weight: 700;
            border-bottom: none;
        }

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

        .info-box {
            background: #F8F5EE;
            border-left: 3px solid #C9A050;
            padding: 3mm 4mm;
            margin: 3mm 0 4mm 0;
            font-size: 10px;
            color: #6B7280;
        }

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

    {{-- mPDF running header & footer --}}
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

    {{-- ====== COVER (PAGE 1) ====== --}}
    <div class="cover">
        @if($coverPhoto)
        <img class="cover-photo" src="{{ $coverPhoto }}" alt="">
        @endif
        <div class="cover-bg" @if($coverPhoto) style="background:rgba(9,10,31,.78)" @endif></div>
        <div class="cover-inner">
            <div class="cover-header clearfix">
                @if($logo)
                <img class="cover-logo-img" src="{{ $logo }}" alt="VIP GPI" style="float:left">
                @endif
                <div style="float:right;text-align:right;padding-top:1mm">
                    <div style="font-size:9px;color:rgba(255,255,255,.5);letter-spacing:.5px">VIP GESTION DE PATRIMOINE</div>
                    <div style="font-size:8px;color:#C9A050;letter-spacing:.3px">ET INVESTISSEMENTS INC.</div>
                </div>
            </div>
            <div class="cover-body">
                <div class="cover-label">Analyse des besoins financiers</div>
                <div class="cover-title">Votre portrait<br>financier</div>
                <div class="cover-gold-bar"></div>
                <div class="cover-subtitle">Préparé le {{ $docDate }}</div>
                <div class="cover-client-block">
                    <div class="cover-client-label">Préparé pour</div>
                    <div class="cover-client-name">{{ $clientName }}</div>
                    @if($hasSpouse)
                    <div class="cover-client-name" style="font-size:14px;opacity:.85;margin-top:1mm">{{ $spouseName }}</div>
                    @endif
                </div>
                @if($advisorName)
                <div class="cover-date-block">
                    Conseiller&nbsp;: <strong style="color:rgba(255,255,255,.85)">{{ $advisorName }}</strong>
                </div>
                @endif
            </div>
            <div class="cover-footer-band">
                <div class="cover-footer-conf">
                    <strong>DOCUMENT CONFIDENTIEL</strong>
                    Ce document est strictement confidentiel. Il est réservé à l'usage exclusif du ou des destinataires désignés. Toute reproduction, distribution ou utilisation non autorisée est interdite.
                </div>
            </div>
        </div>
    </div>

    <pagebreak odd-header-name="main-header" odd-footer-name="main-footer"
               margin-top="30" margin-bottom="20" margin-left="18" margin-right="18"
               margin-header="3" margin-footer="3" />

    {{-- ====== TABLE DES MATIÈRES ====== --}}
    <h1>Table des matières</h1>
    <div class="toc-section">
        @foreach($tocItems as $tocIdx => $tocItem)
        @if($tocItem["show"])
        <div class="toc-item clearfix">
            <span class="toc-num">{{ $loop->iteration }}</span>
            <span class="toc-name">{{ $tocItem["name"] }}</span>
        </div>
        @endif
        @endforeach
    </div>

    <pagebreak />

    {{-- ====== INFORMATIONS PERSONNELLES ====== --}}
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
                    {{ $clientBirthDate }}
                    @if($clientAgeText)
                    <span class="muted">({{ $clientAgeText }})</span>
                    @endif
                </td>
                @if($hasSpouse)
                <td>
                    {{ $spouseBirthDate }}
                    @if($spouseAgeText)
                    <span class="muted">({{ $spouseAgeText }})</span>
                    @endif
                </td>
                @endif
            </tr>
            <tr>
                <td>État civil</td>
                <td>{{ $clientMarital }}</td>
                @if($hasSpouse)<td>{{ $spouseMarital }}</td>@endif
            </tr>
            <tr>
                <td>Tabagisme</td>
                <td>
                    {{ $clientSmoker }}
                    @if($clientIsSmoker)
                    <span class="muted">({{ $clientSmokerSince }})</span>
                    @endif
                </td>
                @if($hasSpouse)
                <td>
                    {{ $spouseSmoker }}
                    @if($spouseIsSmoker)
                    <span class="muted">({{ $spouseSmokerSince }})</span>
                    @endif
                </td>
                @endif
            </tr>
            <tr>
                <td>Adresse (principale)</td>
                <td>{{ $clientAddress }} @if(!blank($clientPostal))<span class="muted">{{ $clientPostal }}</span>@endif</td>
                @if($hasSpouse)
                <td>{{ $spouseAddress }} @if(!blank($spousePostal))<span class="muted">{{ $spousePostal }}</span>@endif</td>
                @endif
            </tr>
            <tr>
                <td>Téléphone (domicile)</td>
                <td>{{ $clientPhone }}</td>
                @if($hasSpouse)<td>{{ $spousePhone }}</td>@endif
            </tr>
            <tr>
                <td>Courriel (principal)</td>
                <td>{{ $clientEmail }}</td>
                @if($hasSpouse)<td>{{ $spouseEmail }}</td>@endif
            </tr>
            <tr>
                <td>Statut au Canada</td>
                <td>{{ $clientCit }}</td>
                @if($hasSpouse)<td>{{ $spouseCit }}</td>@endif
            </tr>
            <tr>
                <td>NAS</td>
                <td>{{ $clientSinText }}</td>
                @if($hasSpouse)<td>{{ $spouseSinText }}</td>@endif
            </tr>
            <tr>
                <td>Travaille au Canada depuis</td>
                <td>{{ $clientWorkSince }}</td>
                @if($hasSpouse)<td>{{ $spouseWorkSince }}</td>@endif
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
                <td>{{ $clientJobTitle }}</td>
                @if($hasSpouse)<td>{{ $spouseJobTitle }}</td>@endif
            </tr>
            <tr>
                <td>Entreprise</td>
                <td>{{ $clientEmployer }}</td>
                @if($hasSpouse)<td>{{ $spouseEmployer }}</td>@endif
            </tr>
            <tr>
                <td>Depuis</td>
                <td>{{ $clientEmpSince }}</td>
                @if($hasSpouse)<td>{{ $spouseEmpSince }}</td>@endif
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
                <td>{{ $clientJobIncome }}</td>
                @if($hasSpouse)<td>{{ $spouseJobIncome }}</td>@endif
            </tr>
            <tr>
                <td>Autres revenus (annuels)</td>
                <td>{{ $clientOtherAnnual }}</td>
                @if($hasSpouse)<td>{{ $spouseOtherAnnual }}</td>@endif
            </tr>
            <tr>
                <td>Autres revenus (mensuels)</td>
                <td>{{ $clientOtherMonthly }}</td>
                @if($hasSpouse)<td>{{ $spouseOtherMonthly }}</td>@endif
            </tr>
            <tr class="small">
                <td colspan="{{ $colSpan2or3 }}" class="muted">* Montants bruts selon les informations fournies.</td>
            </tr>
        </tbody>
    </table>

    <h2>Personnes à charge</h2>
    @if(count($depsDisplay) === 0)
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
            @foreach($depsDisplay as $dep)
            <tr>
                <td>{{ $dep["name"] }}</td>
                <td>{{ $dep["birth_date"] }}</td>
                <td>{{ $dep["age_text"] }}</td>
                <td>{{ $dep["relationship"] }}</td>
                <td>{{ $dep["dependency"] }}</td>
                <td>{{ $dep["same_address"] }}</td>
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
            @foreach($legalDocs as $doc)
            <tr>
                <td>{{ $doc["label"] }}</td>
                <td>{{ $doc["client"] }}</td>
                @if($hasSpouse)<td>{{ $doc["spouse"] }}</td>@endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Objectifs</h2>
    @if(count($goalsDisplay) === 0)
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
            @foreach($goalsDisplay as $goal)
            <tr>
                <td style="width:35%;">{{ $goal["label"] }}</td>
                <td>{{ $goal["answer"] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <pagebreak />

    {{-- ====== PLACEMENTS & ACTIFS ====== --}}
    <h1>Placements & actifs</h1>

    @foreach($assetsByOwner as $ownerBlock)
    <h2>{{ $ownerBlock["label"] }}</h2>
    <table class="simple">
        <thead>
            <tr>
                <th style="width:26%;">Type</th>
                <th>Description</th>
                <th style="width:20%;">Valeur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ownerBlock["grouped"] as $group)
            <tr style="background:#F0EBE0;">
                <td colspan="3" style="font-weight:700;font-size:9.5px;color:#0E1030;padding:4px 8px;border-bottom:1px solid #C9A050;">{{ $group["type_label"] }}</td>
            </tr>
            @foreach($group["items"] as $aRow)
            <tr>
                <td class="muted">{{ $aRow["type_label"] }}</td>
                <td>{{ $aRow["description"] }}</td>
                <td>{{ $aRow["value"] }}</td>
            </tr>
            @endforeach
            @endforeach
            <tr class="grand-total">
                <td colspan="2">Total {{ $dash }} {{ $ownerBlock["label"] }}</td>
                <td>{{ $ownerBlock["total"] }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach

    <pagebreak />

    {{-- ====== DETTES & PASSIFS ====== --}}
    <h1>Dettes & passifs</h1>

    @foreach($liabsByOwner as $ownerBlock)
    <h2>{{ $ownerBlock["label"] }}</h2>
    <table class="simple">
        <thead>
            <tr>
                <th style="width:24%;">Type</th>
                <th>Créancier</th>
                <th style="width:18%;">Solde</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ownerBlock["grouped"] as $group)
            <tr style="background:#FEF2F2;">
                <td colspan="3" style="font-weight:700;font-size:9.5px;color:#DC2626;padding:4px 8px;border-bottom:1px solid #FECACA;">{{ $group["type_label"] }}</td>
            </tr>
            @foreach($group["items"] as $lRow)
            <tr>
                <td class="muted">{{ $lRow["type_label"] }}</td>
                <td>{{ $lRow["creditor"] }}</td>
                <td>{{ $lRow["balance"] }}</td>
            </tr>
            @endforeach
            @endforeach
            <tr class="grand-total">
                <td colspan="2">Total {{ $dash }} {{ $ownerBlock["label"] }}</td>
                <td>{{ $ownerBlock["total"] }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach

    <pagebreak />

    {{-- ====== BILAN FINANCIER ====== --}}
    <h1>Bilan financier</h1>

    <table class="stat-grid">
        <tr>
            <td class="stat-card success" style="width:33%">
                <div class="stat-card-label">Total des actifs</div>
                <div class="stat-card-value">{{ $bilan["asset_total"] }}</div>
            </td>
            <td class="stat-card danger" style="width:33%">
                <div class="stat-card-label">Total des passifs</div>
                <div class="stat-card-value">{{ $bilan["liab_total"] }}</div>
            </td>
            <td class="stat-card" style="width:33%;border-top-color:{{ $netColor }}">
                <div class="stat-card-label">Valeur nette</div>
                <div class="stat-card-value" style="color:{{ $netColor }}">{{ $bilan["net_total"] }}</div>
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
                <td>{{ $bilan["client_assets"] }}</td>
                @if($hasSpouse)<td>{{ $bilan["spouse_assets"] }}</td>@endif
                @if($hasSpouse)<td>{{ $bilan["joint_assets"] }}</td>@endif
                <td>{{ $bilan["asset_total"] }}</td>
            </tr>
            <tr>
                <td>Passifs</td>
                <td>{{ $bilan["client_liabs"] }}</td>
                @if($hasSpouse)<td>{{ $bilan["spouse_liabs"] }}</td>@endif
                @if($hasSpouse)<td>{{ $bilan["joint_liabs"] }}</td>@endif
                <td>{{ $bilan["liab_total"] }}</td>
            </tr>
            <tr class="grand-total">
                <td>Valeur nette</td>
                <td>{{ $bilan["client_net"] }}</td>
                @if($hasSpouse)<td>{{ $bilan["spouse_net"] }}</td>@endif
                @if($hasSpouse)<td>{{ $bilan["joint_net"] }}</td>@endif
                <td>{{ $bilan["net_total"] }}</td>
            </tr>
        </tbody>
    </table>

    <pagebreak />

    {{-- ====== ASSURANCES ====== --}}
    @if($showAssurances)
    <h1>Assurances</h1>

    @foreach($insuranceBlocks as $insBlock)
    <h2>{{ $insBlock["name"] }}</h2>

    @if(count($insBlock["life"]) > 0)
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
            @foreach($insBlock["life"] as $r)
            <tr>
                <td>{{ $r["provider"] }}</td>
                <td>{{ $r["contract_type"] }}</td>
                <td>{{ $r["death_capital"] }}</td>
                <td>{{ $r["annual_premium"] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($insBlock["ci"]) > 0)
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
            @foreach($insBlock["ci"] as $r)
            <tr>
                <td>{{ $r["provider"] }}</td>
                <td>{{ $r["insured_capital"] }}</td>
                <td>{{ $r["premium"] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($insBlock["dis"]) > 0)
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
            @foreach($insBlock["dis"] as $r)
            <tr>
                <td>{{ $r["provider"] }}</td>
                <td>{{ $r["monthly_income"] }}</td>
                <td>{{ $r["premium"] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endforeach
    @endif

    {{-- ====== BUDGET AU DÉCÈS ====== --}}
    @if($showDeathBudget)
    <pagebreak />
    <h1>Budget au décès</h1>

    <table class="stat-grid">
        <tr>
            <td class="stat-card {{ $deathBudget["client_danger"] }}" style="width:{{ $deathBudget["width"] }}">
                <div class="stat-card-label">{{ $clientName }} {{ $dash }} Besoin additionnel</div>
                <div class="stat-card-value">{{ $deathBudget["client_additional"] }}</div>
                <div class="stat-card-sub">Capital-décès requis&nbsp;: {{ $deathBudget["client_capital"] }}</div>
            </td>
            @if($hasSpouse)
            <td class="stat-card {{ $deathBudget["spouse_danger"] }}">
                <div class="stat-card-label">{{ $spouseName }} {{ $dash }} Besoin additionnel</div>
                <div class="stat-card-value">{{ $deathBudget["spouse_additional"] }}</div>
                <div class="stat-card-sub">Capital-décès requis&nbsp;: {{ $deathBudget["spouse_capital"] }}</div>
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
                <td>{{ $deathBudget["client_liquidities"] }}</td>
                @if($hasSpouse)<td>{{ $deathBudget["spouse_liquidities"] }}</td>@endif
            </tr>
            <tr>
                <td>Revenu mensuel à combler</td>
                <td>{{ $deathBudget["client_gap"] }}</td>
                @if($hasSpouse)<td>{{ $deathBudget["spouse_gap"] }}</td>@endif
            </tr>
            <tr>
                <td>Capital requis</td>
                <td>{{ $deathBudget["client_capital"] }}</td>
                @if($hasSpouse)<td>{{ $deathBudget["spouse_capital"] }}</td>@endif
            </tr>
            <tr class="total-row">
                <td>Besoin d'assurance additionnel</td>
                <td>{{ $deathBudget["client_need"] }}</td>
                @if($hasSpouse)<td>{{ $deathBudget["spouse_need"] }}</td>@endif
            </tr>
        </tbody>
    </table>

    <div class="info-box">
        Les résultats sont calculés à partir des hypothèses et des informations fournies. Ils doivent être validés selon la situation réelle du client.
    </div>
    @endif

    {{-- ====== PROFIL INVESTISSEUR ====== --}}
    @if($showDashboard)
    <pagebreak />
    <h1>Profil d'investisseur</h1>

    @if($ipFilled)
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

    @foreach($ipDisplay as $ipQ)
    @if($ipQ["section_changed"])
    <h2>{{ $ipQ["section"] }}</h2>
    @endif
    <table class="simple" style="margin-bottom:2mm;">
        <tbody>
            <tr>
                <td style="width:70%;">{{ $ipQ["label"] }}<br><em style="color:#555;">{{ $ipQ["answer"] }}</em></td>
                <td style="text-align:right;font-weight:700;">{{ $ipQ["pt_label"] }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
    @else
    <p class="muted">Le questionnaire du profil d'investisseur n'a pas encore été complété.</p>
    @endif
    @endif

    {{-- ====== NOTES / RECOMMANDATIONS ====== --}}
    @if($showReco)
    <pagebreak />
    <h1>Notes / recommandations</h1>

    @if($hasAdvisorNotes)
    <h2>Notes du conseiller</h2>
    <p>{!! nl2br(e($advisorNotes)) !!}</p>
    @else
    <p class="muted">Aucune note du conseiller.</p>
    @endif

    <div class="info-box">
        <strong>Avis de non-responsabilité :</strong> Cette analyse est basée sur les informations fournies par le client et sur les hypothèses retenues à la date du rapport. Les recommandations ne constituent pas une garantie de rendement ni de résultat futur.
    </div>
    @endif

    {{-- ====== ACCUSÉ DE RÉCEPTION ====== --}}
    @if($showDelivery)
    <pagebreak />
    <h1>Confirmation de remise</h1>
    <p style="margin-bottom:6mm;">Je, soussigné(e), confirme avoir reçu et pris connaissance du document « Votre portrait financier » préparé par <strong>VIP Gestion de patrimoine et investissements inc.</strong> en date du {{ $docDate }}, ainsi que des hypothèses et des informations qui y sont contenues.</p>

    @foreach($signataires as $sig)
    <table class="simple" style="margin-bottom:8mm;">
        <tbody>
            <tr>
                <td style="width:45%;height:16mm;border-bottom:1px solid #0E1030;vertical-align:bottom;padding-bottom:2mm;font-size:9px;color:#6B7280">
                    Signature {{ $sig["role"] ?? "" }}
                </td>
                <td style="width:5%;"></td>
                <td style="width:30%;border-bottom:1px solid #0E1030;vertical-align:bottom;padding-bottom:2mm">
                    <div style="font-size:9px;color:#6B7280">Nom</div>
                    <div style="font-size:11px;font-weight:700;color:#0E1030">{{ $sig["name"] }}</div>
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
    @endif

    {{-- ====== ANNEXE : REVENUS DE RETRAITE ====== --}}
    @if($showRetIncome)
    <pagebreak />
    <h1>Annexe {{ $dash }} Revenus de retraite</h1>
    <p class="muted small">Estimations basées sur les informations fournies. Les montants des régimes publics sont indexés.</p>

    @foreach($retireBlocks as $retBlock)
    <h2>{{ $retBlock["label"] }} {{ $dash }} retraite à {{ $retBlock["age"] }} ans</h2>
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
            @foreach($retBlock["pub_rows"] as $rr)
            <tr>
                <td>{{ $rr["label"] }}</td>
                <td>{{ $rr["montant"] }}</td>
                <td>{{ $rr["freq"] }}</td>
                <td>{{ $rr["debut"] }}</td>
                <td>{{ $rr["annuel"] }}</td>
            </tr>
            @endforeach
            @foreach($retBlock["rpd_rows"] as $rr)
            <tr>
                <td>{{ $rr["label"] }}</td>
                <td>{{ $rr["montant"] }}</td>
                <td>{{ $rr["freq"] }}</td>
                <td>{{ $rr["debut"] }}</td>
                <td>{{ $rr["annuel"] }}</td>
            </tr>
            @endforeach
            @foreach($retBlock["retrait_rows"] as $rr)
            <tr>
                <td>{{ $rr["label"] }}</td>
                <td>{{ $rr["montant"] }}</td>
                <td>{{ $rr["freq"] }}</td>
                <td>{{ $rr["debut"] }}</td>
                <td>{{ $rr["annuel"] }}</td>
            </tr>
            @endforeach
            <tr class="grand-total">
                <td colspan="4">Total annuel estimé</td>
                <td>{{ $retBlock["total_annuel"] }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
    @endif

    {{-- ====== ANNEXE : ÉVOLUTION DES PLACEMENTS ====== --}}
    @if($showInvProjection)
    <pagebreak />
    <h1>Annexe {{ $dash }} Évolution des placements</h1>
    <p class="muted small">
        Projection basée sur une valeur initiale de {{ $projInitialValue }},
        des cotisations annuelles estimées de {{ $projAnnualSavings }}
        et un taux de rendement hypothétique de {{ $projRate }}% par année.
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
            @foreach($projRows as $pRow)
            <tr @if($pRow["is_target"]) class="total-row" @endif>
                <td>{{ $pRow["age"] }} ans</td>
                <td>{{ $pRow["an_label"] }}</td>
                <td>{{ $pRow["valeur"] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

</body>

</html>
