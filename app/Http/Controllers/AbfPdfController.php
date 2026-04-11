<?php

namespace App\Http\Controllers;

use App\Models\AbfCase;
use App\Services\AbfCaseCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class AbfPdfController extends Controller
{
    public function generate(string $locale, AbfCase $abfCase)
    {
        abort_unless($abfCase->advisor_user_id === auth()->id(), 403);

        try {
            $calculator = app(AbfCaseCalculator::class);
            $results    = $calculator->calculate($abfCase->payload ?? []);

            $viewData = $this->prepareData($abfCase, $results);

            $clientFirst  = data_get($abfCase->payload, 'client.first_name', '');
            $clientLast   = data_get($abfCase->payload, 'client.last_name', 'Client');
            $hasSpousePdf = (bool) data_get($abfCase->payload, 'has_spouse', false);
            $spouseFirst  = $hasSpousePdf ? data_get($abfCase->payload, 'spouse.first_name', '') : '';
            $spouseLast   = $hasSpousePdf ? data_get($abfCase->payload, 'spouse.last_name', '') : '';

            $clientPart = Str::slug(trim($clientLast . ' ' . $clientFirst), '_');
            $spousePart = ($hasSpousePdf && ($spouseLast || $spouseFirst))
                ? '_' . Str::slug(trim($spouseLast . ' ' . $spouseFirst), '_')
                : '';
            $datePart  = now()->format('Y-m-d');
            $filename  = 'ABF_' . $clientPart . $spousePart . '_' . $datePart . '.pdf';

            $html = view('pdf.abf-report', $viewData)->render();

            $tempDir = storage_path('app/mpdf');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $mpdf = new Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A4',
                'margin_top'    => 0,
                'margin_bottom' => 0,
                'margin_left'   => 0,
                'margin_right'  => 0,
                'margin_header' => 0,
                'margin_footer' => 0,
                'default_font'  => 'dejavusans',
                'tempDir'       => $tempDir,
            ]);

            $mpdf->WriteHTML($html);

            Log::channel('daily')->info('ABF PDF generated', [
                'case_id'  => $abfCase->id,
                'user_id'  => auth()->id(),
                'filename' => $filename,
            ]);

            return response($mpdf->Output($filename, Destination::STRING_RETURN), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Throwable $e) {
            Log::channel('daily')->error('ABF PDF — generation error', [
                'case_id' => $abfCase->id,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
                'file'    => $e->getFile() . ':' . $e->getLine(),
                'class'   => get_class($e),
                'trace'   => collect(explode("\n", $e->getTraceAsString()))
                                ->take(15)
                                ->implode("\n"),
            ]);

            throw $e;
        }
    }

    private function prepareData(AbfCase $abfCase, array $results): array
    {
        $payload    = (array) ($abfCase->payload ?? []);
        $sections   = (array) data_get($payload, 'rapport.sections', []);
        $sec        = fn($k, $def = true) => (bool) ($sections[$k] ?? $def);

        $client     = (array) data_get($payload, 'client', []);
        $spouse     = (array) data_get($payload, 'spouse', []);
        $hasSpouse  = (bool) data_get($payload, 'has_spouse', false);

        $clientName = trim(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? '')) ?: 'Client';
        $spouseName = trim(($spouse['first_name'] ?? '') . ' ' . ($spouse['last_name'] ?? '')) ?: 'Conjoint';
        $dash       = "\xE2\x80\x94";

        // Date
        $docDateRaw = data_get($payload, 'document_meta.document_date');
        $docDate    = null;
        try {
            $docDate = $docDateRaw ? Carbon::parse($docDateRaw)->locale('fr_CA')->isoFormat('D MMMM YYYY') : null;
        } catch (\Throwable) {}
        $docDate ??= now()->locale('fr_CA')->isoFormat('D MMMM YYYY');

        // Logo
        $logoPath = public_path('assets/img/VIP_Logo_Gold_Gradient10.png');
        $logo     = file_exists($logoPath) ? ('data:image/png;base64,' . base64_encode(file_get_contents($logoPath))) : null;

        // Cover photo
        $photoFile  = data_get($payload, 'rapport.photo');
        $photoPath  = $photoFile ? public_path('assets/img/abf-covers/' . basename((string) $photoFile)) : null;
        $coverPhoto = ($photoPath && file_exists($photoPath))
            ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($photoPath))
            : null;

        // Helpers
        $money = fn($v) => number_format((float) $v, 0, '.', ' ') . ' $';
        $yesNo = fn($v) => (bool) $v ? 'Oui' : 'Non';
        $age   = function ($d) {
            if (blank($d)) return null;
            try { return Carbon::parse($d)->age; } catch (\Throwable) { return null; }
        };

        $maritalMap = [
            'single'     => 'Célibataire',
            'common_law' => 'Conjoint de fait',
            'married'    => 'Marié(e)',
            'divorced'   => 'Divorcé(e)',
            'separated'  => 'Séparé(e)',
            'widowed'    => 'Veuf(ve)',
        ];
        $citMap = [
            'canadian_citizen'   => 'Citoyen(ne) canadien(ne)',
            'permanent_resident' => 'Résident(e) permanent(e)',
            'temporary_resident' => 'Résident(e) temporaire / permis',
            'other'              => 'Autre',
        ];
        $smokerMap = [
            'smoker'        => 'Fumeur',
            'non_smoker'    => 'Non-fumeur',
            'former_smoker' => 'Ancien fumeur',
        ];
        $ownerMap = ['client' => 'Client', 'spouse' => 'Conjoint', 'joint' => 'Commun'];
        $assetTypeMap = [
            'cash'    => 'Liquidités',
            'tfsa'    => 'CELI',
            'rrsp'    => 'REER',
            'nonreg'  => 'Non-enregistré',
            'home'    => 'Résidence principale',
            'rental'  => 'Résidence secondaire / immeuble',
            'vehicle' => 'Véhicule',
            'business'=> 'Entreprise',
            'other'   => 'Autre',
        ];
        $liabTypeMap = [
            'mortgage' => 'Hypothèque',
            'loc'      => 'Marge de crédit',
            'loan'     => 'Prêt',
            'credit'   => 'Carte de crédit',
            'student'  => 'Prêt étudiant',
            'tax'      => 'Impôts',
            'other'    => 'Autre',
        ];

        // Advisor
        $advisorName = auth()->user()?->name ?? data_get($abfCase->advisor, 'name', '');

        // Section flags
        $showAssurances    = $sec('lifeInsurance') || $sec('disability') || $sec('seriousIllness');
        $showDeathBudget   = $sec('lifeInsurance');
        $showDashboard     = $sec('dashboard');
        $showReco          = $sec('recommendations');
        $hasAdvisorNotes   = !blank(data_get($payload, 'advisor_notes'));
        $showDelivery      = $sec('deliveryConfirmation', false);
        $showRetIncome     = $sec('annex', false) && $sec('retirementIncome', false);
        $showInvProjection = $sec('annex', false) && $sec('investmentProjection', false);

        // ── TOC ──
        $tocItems = [
            ['name' => 'Informations personnelles',           'show' => true],
            ['name' => 'Placements & actifs',                 'show' => true],
            ['name' => 'Dettes & passifs',                    'show' => true],
            ['name' => 'Bilan financier',                     'show' => true],
            ['name' => 'Assurances',                          'show' => $showAssurances],
            ['name' => "Budget au d\xC3\xA9c\xC3\xA8s",     'show' => $showDeathBudget],
            ['name' => "Profil d'investisseur",               'show' => $showDashboard],
            ['name' => 'Notes / recommandations',             'show' => $showReco],
            ['name' => "Accus\xC3\xA9 de r\xC3\xA9ception", 'show' => $showDelivery],
            ['name' => "Annexe \xE2\x80\x94 Revenus de retraite",       'show' => $showRetIncome],
            ['name' => "Annexe \xE2\x80\x94 \xC3\x89volution des placements", 'show' => $showInvProjection],
        ];

        // ── Personal info (pre-computed) ──
        $clientBirthDate   = $client['birth_date'] ?? $dash;
        $spouseBirthDate   = $spouse['birth_date'] ?? $dash;
        $clientAgeVal      = $age($client['birth_date'] ?? null);
        $spouseAgeVal      = $age($spouse['birth_date'] ?? null);
        $clientAgeText     = $clientAgeVal !== null ? ($clientAgeVal . ' ans') : null;
        $spouseAgeText     = $spouseAgeVal !== null ? ($spouseAgeVal . ' ans') : null;
        $clientMarital     = $maritalMap[$client['marital_status'] ?? ''] ?? $dash;
        $spouseMarital     = $maritalMap[$spouse['marital_status'] ?? ''] ?? $dash;
        $clientSmoker      = $smokerMap[$client['smoker_status'] ?? ''] ?? $dash;
        $spouseSmoker      = $smokerMap[$spouse['smoker_status'] ?? ''] ?? $dash;
        $clientSmokerSince = $client['smoker_since'] ?? '';
        $spouseSmokerSince = $spouse['smoker_since'] ?? '';
        $clientIsSmoker    = ($client['smoker_status'] ?? null) === 'smoker' && !blank($clientSmokerSince);
        $spouseIsSmoker    = ($spouse['smoker_status'] ?? null) === 'smoker' && !blank($spouseSmokerSince);
        $clientAddress     = $client['address'] ?? $dash;
        $spouseAddress     = $spouse['address'] ?? $dash;
        $clientPostal      = $client['postal_code'] ?? '';
        $spousePostal      = $spouse['postal_code'] ?? '';
        $clientPhone       = $client['home_phone'] ?? $dash;
        $spousePhone       = $spouse['home_phone'] ?? $dash;
        $clientEmail       = $client['email'] ?? $dash;
        $spouseEmail       = $spouse['email'] ?? $dash;
        $clientCit         = $citMap[$client['citizenship_status'] ?? ''] ?? $dash;
        $spouseCit         = $citMap[$spouse['citizenship_status'] ?? ''] ?? $dash;
        $clientHasSin      = data_get($client, 'has_sin');
        $clientSinText     = $clientHasSin === null ? $dash : $yesNo($clientHasSin);
        $spouseHasSin      = data_get($spouse, 'has_sin');
        $spouseSinText     = $spouseHasSin === null ? $dash : $yesNo($spouseHasSin);
        $clientWorkSince   = $client['work_in_canada_since'] ?? $dash;
        $spouseWorkSince   = $spouse['work_in_canada_since'] ?? $dash;

        // Employment
        $clientJobTitle  = data_get($client, 'jobs.0.occupation') ?? $dash;
        $spouseJobTitle  = data_get($spouse, 'jobs.0.occupation') ?? $dash;
        $clientEmployer  = data_get($client, 'jobs.0.employer') ?? $dash;
        $spouseEmployer  = data_get($spouse, 'jobs.0.employer') ?? $dash;
        $clientEmpSince  = $client['employment_since'] ?? $dash;
        $spouseEmpSince  = $spouse['employment_since'] ?? $dash;

        // Income
        $clientJobIncome     = $money(data_get($client, 'jobs.0.annual_income', 0));
        $spouseJobIncome     = $money(data_get($spouse, 'jobs.0.annual_income', 0));
        $clientOtherAnnual   = $money(data_get($client, 'other_income_annual', 0));
        $spouseOtherAnnual   = $money(data_get($spouse, 'other_income_annual', 0));
        $clientOtherMonthly  = $money(data_get($client, 'other_income_monthly', 0));
        $spouseOtherMonthly  = $money(data_get($spouse, 'other_income_monthly', 0));

        // Legal documents
        $legalDocs = [
            ['label' => 'Testament',
             'client' => $yesNo(data_get($client, 'legal.will_exists', false)),
             'spouse' => $yesNo(data_get($spouse, 'legal.will_exists', false))],
            ['label' => "Mandat en cas d'inaptitude (QC)",
             'client' => $yesNo(data_get($client, 'legal.mandate_incapacity_exists', false)),
             'spouse' => $yesNo(data_get($spouse, 'legal.mandate_incapacity_exists', false))],
            ['label' => 'Procuration (hors QC)',
             'client' => $yesNo(data_get($client, 'legal.power_of_attorney_exists', false)),
             'spouse' => $yesNo(data_get($spouse, 'legal.power_of_attorney_exists', false))],
        ];

        // ── Dependents (pre-computed) ──
        $deps = (array) data_get($payload, 'dependents', []);
        $relMap = ['child' => 'Enfant', 'dependent' => 'Personne à charge', 'other' => 'Autre'];
        $depMap = ['full' => 'Totale', 'partial' => 'Partielle', 'none' => 'Aucune'];
        $depsDisplay = collect($deps)->map(function ($d) use ($age, $dash, $yesNo, $relMap, $depMap) {
            $a = $age($d['birth_date'] ?? null);
            return [
                'name'         => $d['name'] ?? $dash,
                'birth_date'   => $d['birth_date'] ?? $dash,
                'age_text'     => $a === null ? $dash : ($a . ' ans'),
                'relationship' => $relMap[$d['relationship'] ?? ''] ?? $dash,
                'dependency'   => $depMap[$d['financial_dependency'] ?? ''] ?? $dash,
                'same_address' => $yesNo($d['same_contact_as_client'] ?? false),
            ];
        })->all();

        // ── Goals (pre-computed) ──
        $goalsSelected = (array) data_get($payload, 'goals.selected', []);
        $goalsAnswers  = (array) data_get($payload, 'goals.answers', []);
        $goalsLabels   = [
            'retirement'     => 'Retraite',
            'buy_house'      => "Achat / changement de propri\xC3\xA9t\xC3\xA9",
            'kids_education' => "Études des enfants",
            'debt_repayment' => 'Remboursement dettes',
            'insurance'      => 'Optimisation assurances',
            'investments'    => "Strat\xC3\xA9gie placements",
            'business'       => 'Projet entreprise',
            'travel'         => 'Voyages / style de vie',
        ];
        $goalsDisplay = collect($goalsSelected)->map(function ($k) use ($goalsLabels, $goalsAnswers, $dash) {
            return [
                'label'  => $goalsLabels[$k] ?? $k,
                'answer' => trim((string) ($goalsAnswers[$k] ?? '')) ?: $dash,
            ];
        })->all();

        // ── Assets & liabilities ──
        $assets = (array) data_get($payload, 'assets', []);
        $liabs  = (array) data_get($payload, 'liabilities', []);

        $sumByOwner = function (array $rows, string $key) {
            $out = ['client' => 0, 'spouse' => 0, 'joint' => 0];
            foreach ($rows as $r) {
                $o = $r['owner'] ?? 'client';
                $out[$o] = ($out[$o] ?? 0) + (float) ($r[$key] ?? 0);
            }
            return $out;
        };

        $assetSums  = $sumByOwner($assets, 'value');
        $liabSums   = $sumByOwner($liabs, 'balance');
        $assetTotal = array_sum($assetSums);
        $liabTotal  = array_sum($liabSums);
        $netTotal   = $assetTotal - $liabTotal;
        $netColor   = $netTotal >= 0 ? '#16A34A' : '#DC2626';

        // Pre-grouped assets by owner
        $ownersList    = ['client', 'spouse', 'joint'];
        $byOwnerAssets = collect($assets)->groupBy(fn($a) => $a['owner'] ?? 'client');
        $assetsByOwner = [];
        foreach ($ownersList as $owner) {
            if ($owner === 'spouse' && !$hasSpouse) continue;
            $rows = collect($byOwnerAssets[$owner] ?? []);
            if ($rows->isEmpty()) continue;
            $grouped = $rows->groupBy(fn($a) => $a['type'] ?? 'other');
            $assetsByOwner[] = [
                'label'   => $ownerMap[$owner] ?? 'Client',
                'grouped' => $grouped->map(fn($items, $type) => [
                    'type_label' => $assetTypeMap[$type] ?? $type,
                    'items'      => $items->map(fn($a) => [
                        'type_label'  => $assetTypeMap[$a['type'] ?? 'other'] ?? ($a['type'] ?? 'other'),
                        'description' => $a['description'] ?? $dash,
                        'value'       => $money($a['value'] ?? 0),
                    ])->all(),
                ])->values()->all(),
                'total' => $money($rows->sum(fn($r) => (float) ($r['value'] ?? 0))),
            ];
        }

        // Pre-grouped liabilities by owner
        $byOwnerLiabs = collect($liabs)->groupBy(fn($l) => $l['owner'] ?? 'client');
        $liabsByOwner = [];
        foreach ($ownersList as $owner) {
            if ($owner === 'spouse' && !$hasSpouse) continue;
            $rows = collect($byOwnerLiabs[$owner] ?? []);
            if ($rows->isEmpty()) continue;
            $grouped = $rows->groupBy(fn($l) => $l['type'] ?? 'other');
            $liabsByOwner[] = [
                'label'   => $ownerMap[$owner] ?? 'Client',
                'grouped' => $grouped->map(fn($items, $type) => [
                    'type_label' => $liabTypeMap[$type] ?? $type,
                    'items'      => $items->map(fn($l) => [
                        'type_label' => $liabTypeMap[$l['type'] ?? 'other'] ?? ($l['type'] ?? 'other'),
                        'creditor'   => $l['creditor'] ?? $dash,
                        'balance'    => $money($l['balance'] ?? 0),
                    ])->all(),
                ])->values()->all(),
                'total' => $money($rows->sum(fn($r) => (float) ($r['balance'] ?? 0))),
            ];
        }

        // Bilan display
        $bilan = [
            'asset_total'  => $money($assetTotal),
            'liab_total'   => $money($liabTotal),
            'net_total'    => $money($netTotal),
            'client_assets'=> $money($assetSums['client'] ?? 0),
            'client_liabs' => $money($liabSums['client'] ?? 0),
            'client_net'   => $money(($assetSums['client'] ?? 0) - ($liabSums['client'] ?? 0)),
            'spouse_assets'=> $money($assetSums['spouse'] ?? 0),
            'spouse_liabs' => $money($liabSums['spouse'] ?? 0),
            'spouse_net'   => $money(($assetSums['spouse'] ?? 0) - ($liabSums['spouse'] ?? 0)),
            'joint_assets' => $money($assetSums['joint'] ?? 0),
            'joint_liabs'  => $money($liabSums['joint'] ?? 0),
            'joint_net'    => $money(($assetSums['joint'] ?? 0) - ($liabSums['joint'] ?? 0)),
        ];

        // ── Insurance ──
        $prot = (array) data_get($payload, 'protections_details', []);
        $ppl  = [
            'client'   => ['name' => $clientName, 'data' => (array) ($prot['client'] ?? [])],
            'spouse'   => ['name' => $spouseName, 'data' => (array) ($prot['spouse'] ?? [])],
            'children' => ['name' => 'Enfants',   'data' => (array) ($prot['children'] ?? [])],
        ];
        $insuranceBlocks = [];
        foreach ($ppl as $key => $block) {
            if ($key === 'spouse' && !$hasSpouse) continue;
            $life = (array) data_get($block['data'], 'life', []);
            $dis  = (array) data_get($block['data'], 'disability', []);
            $ci   = (array) data_get($block['data'], 'critical_illness', []);
            if (count($life) + count($dis) + count($ci) === 0) continue;
            $insuranceBlocks[] = [
                'name' => $block['name'],
                'life' => collect($life)->map(fn($r) => [
                    'provider'       => $r['provider'] ?? $dash,
                    'contract_type'  => $r['contract_type'] ?? $dash,
                    'death_capital'  => $money($r['death_capital'] ?? 0),
                    'annual_premium' => $money($r['annual_premium'] ?? 0),
                ])->all(),
                'ci' => collect($ci)->map(fn($r) => [
                    'provider'        => $r['provider'] ?? $dash,
                    'insured_capital' => $money($r['insured_capital'] ?? 0),
                    'premium'         => $money($r['premium'] ?? 0),
                ])->all(),
                'dis' => collect($dis)->map(fn($r) => [
                    'provider'       => $r['provider'] ?? $dash,
                    'monthly_income' => $money($r['monthly_income'] ?? 0),
                    'premium'        => $money($r['premium'] ?? 0),
                ])->all(),
            ];
        }

        // ── Death budget ──
        $db = (array) data_get($results, 'death_budget.per_person', []);
        $deathBudget = [
            'width'             => $hasSpouse ? '50%' : '100%',
            'client_danger'     => data_get($db, 'client.e.additional_need', 0) > 0 ? 'danger' : '',
            'spouse_danger'     => data_get($db, 'spouse.e.additional_need', 0) > 0 ? 'danger' : '',
            'client_additional' => $money(data_get($db, 'client.e.additional_need', 0)),
            'client_capital'    => $money(data_get($db, 'client.d.capital_required', 0)),
            'spouse_additional' => $money(data_get($db, 'spouse.e.additional_need', 0)),
            'spouse_capital'    => $money(data_get($db, 'spouse.d.capital_required', 0)),
            'client_liquidities'=> $money(data_get($db, 'client.b.net_liquidities', 0)),
            'spouse_liquidities'=> $money(data_get($db, 'spouse.b.net_liquidities', 0)),
            'client_gap'        => $money(data_get($db, 'client.c.monthly_gap', 0)),
            'spouse_gap'        => $money(data_get($db, 'spouse.c.monthly_gap', 0)),
            'client_need'       => $money(data_get($db, 'client.e.additional_need', 0)),
            'spouse_need'       => $money(data_get($db, 'spouse.e.additional_need', 0)),
        ];

        // ── Investor profile ──
        $ip = (array) data_get($payload, 'investor_profile', []);
        $ipQuestionsDef = [
            'q1' => ['section' => "Horizon d'investissement", 'label' => '1. Quel âge avez-vous?',
                      'options' => [1 => 'Plus de 71 ans', 2 => 'Entre 65 et 70 ans', 5 => 'Entre 55 et 64 ans', 10 => 'Entre 41 et 54 ans', 20 => 'Entre 18 et 40 ans']],
            'q2' => ['section' => "Horizon d'investissement", 'label' => "2. Sorties de fonds (\xE2\x89\xA525 % de l'\xC3\xA9pargne)?",
                      'options' => [1 => 'Dans moins de 1 an', 2 => 'Entre 1 et 3 ans', 5 => 'Entre 4 et 5 ans', 10 => 'Entre 6 et 9 ans', 20 => 'Dans plus de 10 ans']],
            'q3' => ['section' => "Horizon d'investissement", 'label' => '3. Retraits prévus (5 prochaines années)?',
                      'options' => [1 => 'Retraits réguliers du capital', 2 => "Totalit\xC3\xA9 du rendement + partie du capital", 5 => "Tout le rendement sans toucher au capital", 10 => 'Partie du rendement seulement', 20 => 'Accumulation (aucun retrait)']],
            'q4' => ['section' => "Situation financi\xC3\xA8re", 'label' => "4. Revenu annuel brut (avant imp\xC3\xB4ts)?",
                      'options' => [1 => '25 000 $ et moins', 2 => '25 001 $ à 35 000 $', 5 => '35 001 $ à 50 000 $', 10 => '50 001 $ à 100 000 $', 20 => '100 001 $ et plus']],
            'q5' => ['section' => "Situation financi\xC3\xA8re", 'label' => '5. Valeur nette (actif moins passif)?',
                      'options' => [1 => '25 000 $ et moins', 2 => '25 001 $ à 50 000 $', 5 => '50 001 $ à 100 000 $', 10 => '100 001 $ à 200 000 $', 20 => '200 001 $ et plus']],
            'q6' => ['section' => "Tol\xC3\xA9rance au risque", 'label' => "6. Niveau de tol\xC3\xA9rance au risque?",
                      'options' => [1 => "Tr\xC3\xA8s faible", 2 => 'Faible', 5 => "Mod\xC3\xA9r\xC3\xA9", 10 => "\xC3\x89lev\xC3\xA9", 20 => "Tr\xC3\xA8s \xC3\xA9lev\xC3\xA9"]],
            'q7' => ['section' => "Tol\xC3\xA9rance au risque", 'label' => '7. Fourchette acceptée pour un placement de 10 000 $?',
                      'options' => [1 => '10 000 $ à 10 300 $', 2 => '9 500 $ à 11 000 $', 5 => '9 000 $ à 11 500 $', 10 => '8 500 $ à 12 000 $', 20 => '8 000 $ à 12 500 $']],
            'q8' => ['section' => 'Connaissance des placements', 'label' => '8. Niveau de connaissance des placements?',
                      'options' => [1 => "Tr\xC3\xA8s faible", 2 => 'Faible', 5 => "Mod\xC3\xA9r\xC3\xA9", 10 => "Avanc\xC3\xA9", 20 => "Tr\xC3\xA8s avanc\xC3\xA9"]],
        ];

        $ipScore = 0;
        foreach (array_keys($ipQuestionsDef) as $k) {
            $ipScore += (int) ($ip[$k] ?? 0);
        }
        $ipProfile = match (true) {
            $ipScore <= 25  => 'Conservateur',
            $ipScore <= 55  => "Mod\xC3\xA9r\xC3\xA9ment conservateur",
            $ipScore <= 90  => "\xC3\x89quilibr\xC3\xA9",
            $ipScore <= 120 => 'Croissance',
            default         => 'Croissance agressive',
        };
        $ipFilled = $ipScore > 0;
        $profileColor = match (true) {
            $ipScore <= 25  => '#3B82F6',
            $ipScore <= 55  => '#22C55E',
            $ipScore <= 90  => '#C9A050',
            $ipScore <= 120 => '#F97316',
            default         => '#EF4444',
        };

        // IP display (flat array with section headers tracked)
        $ipDisplay      = [];
        $currentSection = null;
        foreach ($ipQuestionsDef as $key => $q) {
            $pts            = (int) ($ip[$key] ?? 0);
            $sectionChanged = $q['section'] !== $currentSection;
            $currentSection = $q['section'];
            $ipDisplay[] = [
                'section'         => $q['section'],
                'section_changed' => $sectionChanged,
                'label'           => $q['label'],
                'answer'          => $pts > 0 ? ($q['options'][$pts] ?? $dash) : $dash,
                'pt_label'        => $pts === 1 ? '1 point' : ($pts > 0 ? "{$pts} points" : $dash),
            ];
        }

        // ── Delivery confirmation ──
        $signataires = array_values(array_filter([
            ['name' => $clientName,        'role' => ''],
            $hasSpouse ? ['name' => $spouseName, 'role' => ''] : null,
            !empty($advisorName) ? ['name' => $advisorName, 'role' => 'Conseiller'] : null,
        ]));

        // ── Retirement data ──
        $retData         = (array) data_get($payload, 'retraite', []);
        $regPubClient    = (array) data_get($retData, 'regimesPublics.client', []);
        $regPubConjoint  = (array) data_get($retData, 'regimesPublics.conjoint', []);
        $rpdList         = (array) data_get($retData, 'rpd', []);
        $retraitsList    = (array) data_get($retData, 'retraits', []);
        $retAgeClient    = (int) ($retData['ageClient'] ?? 65);
        $retAgeConjoint  = (int) ($retData['ageConjoint'] ?? 65);

        $fmtFreq = fn($f) => match ($f) {
            'mensuel' => '/mois', 'annuel' => '/an', 'bimensuel' => '/2 sem.', default => '',
        };
        $toAnnual = fn($montant, $freq) => match ($freq ?? 'mensuel') {
            'mensuel'   => (float) $montant * 12,
            'bimensuel' => (float) $montant * 26,
            default     => (float) $montant,
        };

        $retireBlockDefs = array_filter([
            ['role' => 'client',   'label' => $clientName, 'age' => $retAgeClient,   'pub' => $regPubClient],
            $hasSpouse ? ['role' => 'conjoint', 'label' => $spouseName, 'age' => $retAgeConjoint, 'pub' => $regPubConjoint] : null,
        ]);

        $retireBlocks = [];
        foreach ($retireBlockDefs as $bl) {
            $pubRows = collect($bl['pub'])->map(fn($r) => [
                'label'  => $r['label'] ?? $r['id'] ?? $dash,
                'montant'=> $money($r['montant'] ?? 0),
                'freq'   => $fmtFreq($r['frequence'] ?? 'mensuel'),
                'debut'  => ($r['debut'] ?? 65) . ' ans',
                'annuel' => $money($toAnnual($r['montant'] ?? 0, $r['frequence'] ?? 'mensuel')),
            ])->all();

            $rpdRows = collect($rpdList)->filter(fn($r) => ($r['role'] ?? '') === $bl['role'])->map(fn($r) => [
                'label'  => $r['nom'] ?? "Régime privé",
                'montant'=> $money($r['montant'] ?? 0),
                'freq'   => $fmtFreq($r['frequence'] ?? 'mensuel'),
                'debut'  => ($r['debut'] ?? $dash) . ' ans',
                'annuel' => $money($toAnnual($r['montant'] ?? 0, $r['frequence'] ?? 'mensuel')),
            ])->values()->all();

            $retraitRows = collect($retraitsList)->filter(fn($r) => ($r['role'] ?? '') === $bl['role'])->map(fn($r) => [
                'label'  => $r['desc'] ?? $r['type'] ?? 'Retrait',
                'montant'=> $money($r['montant'] ?? 0),
                'freq'   => $fmtFreq($r['frequence'] ?? 'mensuel'),
                'debut'  => $r['debut'] ?? $dash,
                'annuel' => $money($toAnnual($r['montant'] ?? 0, $r['frequence'] ?? 'mensuel')),
            ])->values()->all();

            $totalAnnuel = collect($bl['pub'])->sum(fn($r) => $toAnnual($r['montant'] ?? 0, $r['frequence'] ?? 'mensuel'))
                + collect($rpdList)->where('role', $bl['role'])->sum(fn($r) => $toAnnual($r['montant'] ?? 0, $r['frequence'] ?? 'mensuel'))
                + collect($retraitsList)->where('role', $bl['role'])->sum(fn($r) => $toAnnual($r['montant'] ?? 0, $r['frequence'] ?? 'mensuel'));

            $retireBlocks[] = [
                'label'         => $bl['label'],
                'age'           => $bl['age'],
                'pub_rows'      => $pubRows,
                'rpd_rows'      => $rpdRows,
                'retrait_rows'  => $retraitRows,
                'total_annuel'  => $money($totalAnnuel),
            ];
        }

        // ── Investment projection ──
        $currentAge    = $age($client['birth_date'] ?? null) ?? 40;
        $targetAge     = $retAgeClient ?: 65;
        $years         = max(1, $targetAge - $currentAge);
        $currentValue  = $assetTotal;
        $annualSavings = (float) data_get($client, 'jobs.0.annual_income', 0) * 0.10;
        $rate          = (float) (data_get($payload, 'hypotheses.return_rate', 5) ?: 5) / 100;
        $projRows      = [];
        $v             = $currentValue;
        for ($y = 1; $y <= min($years, 40); $y++) {
            $v = ($v + $annualSavings) * (1 + $rate);
            if ($y % 5 === 0 || $y === 1 || $y === $years) {
                $projRows[] = [
                    'age'       => $currentAge + $y,
                    'an'        => $y,
                    'valeur'    => $money($v),
                    'is_target' => ($currentAge + $y) >= $targetAge,
                    'an_label'  => $y > 1 ? "{$y} ans" : '1 an',
                ];
            }
        }
        $projInitialValue  = $money($currentValue);
        $projAnnualSavings = $money($annualSavings);
        $projRate          = number_format($rate * 100, 1);

        $advisorNotes = (string) data_get($payload, 'advisor_notes');
        $colSpan2or3  = $hasSpouse ? 3 : 2;

        return compact(
            'clientName', 'spouseName', 'hasSpouse', 'docDate', 'logo', 'coverPhoto',
            'dash', 'advisorName', 'tocItems', 'netColor', 'colSpan2or3',
            'clientBirthDate', 'spouseBirthDate', 'clientAgeText', 'spouseAgeText',
            'clientMarital', 'spouseMarital', 'clientSmoker', 'spouseSmoker',
            'clientSmokerSince', 'spouseSmokerSince', 'clientIsSmoker', 'spouseIsSmoker',
            'clientAddress', 'spouseAddress', 'clientPostal', 'spousePostal',
            'clientPhone', 'spousePhone', 'clientEmail', 'spouseEmail',
            'clientCit', 'spouseCit', 'clientSinText', 'spouseSinText',
            'clientWorkSince', 'spouseWorkSince', 'clientEmpSince', 'spouseEmpSince',
            'clientJobTitle', 'spouseJobTitle', 'clientEmployer', 'spouseEmployer',
            'clientJobIncome', 'spouseJobIncome', 'clientOtherAnnual', 'spouseOtherAnnual',
            'clientOtherMonthly', 'spouseOtherMonthly',
            'depsDisplay', 'legalDocs', 'goalsDisplay',
            'assetsByOwner', 'liabsByOwner', 'bilan',
            'showAssurances', 'insuranceBlocks',
            'showDeathBudget', 'deathBudget',
            'showDashboard', 'ipFilled', 'ipProfile', 'ipScore', 'profileColor', 'ipDisplay',
            'showReco', 'hasAdvisorNotes', 'advisorNotes',
            'showDelivery', 'signataires',
            'showRetIncome', 'retireBlocks',
            'showInvProjection', 'projRows', 'projInitialValue', 'projAnnualSavings', 'projRate'
        );
    }
}
