<?php

namespace App\Filament\Pages;

use App\Models\AbfParameter;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\Action;

class AbfConfigPage extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationLabel = 'Configuration ABF';
    protected static ?string $navigationIcon  = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Gestion Clients';
    protected static ?int    $navigationSort  = 10;
    protected static string  $view = 'filament.pages.abf-config';

    public array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;
    }

    public function mount(): void
    {
        $p = AbfParameter::allAsMap();

        $celiRaw = $p['celi']['plafonds'] ?? null;
        $celiPlafonds = [];
        if ($celiRaw) {
            $decoded = json_decode($celiRaw, true);
            if (is_array($decoded)) {
                usort($decoded, fn($a, $b) => ($a['annee'] ?? 0) <=> ($b['annee'] ?? 0));
                $celiPlafonds = $decoded;
            }
        }
        if (empty($celiPlafonds)) {
            $celiPlafonds = [
                ['annee' => 2009, 'montant' => 5000], ['annee' => 2010, 'montant' => 5000],
                ['annee' => 2011, 'montant' => 5000], ['annee' => 2012, 'montant' => 5000],
                ['annee' => 2013, 'montant' => 5500], ['annee' => 2014, 'montant' => 5500],
                ['annee' => 2015, 'montant' => 10000], ['annee' => 2016, 'montant' => 5500],
                ['annee' => 2017, 'montant' => 5500], ['annee' => 2018, 'montant' => 5500],
                ['annee' => 2019, 'montant' => 6000], ['annee' => 2020, 'montant' => 6000],
                ['annee' => 2021, 'montant' => 6000], ['annee' => 2022, 'montant' => 6000],
                ['annee' => 2023, 'montant' => 6500], ['annee' => 2024, 'montant' => 7000],
                ['annee' => 2025, 'montant' => 7000], ['annee' => 2026, 'montant' => 7000],
            ];
        }

        $bracketsFedRaw = $p['fiscalite']['brackets_fed'] ?? null;
        $bracketsFed = [];
        if ($bracketsFedRaw) {
            $decoded = json_decode($bracketsFedRaw, true);
            if (is_array($decoded)) {
                $bracketsFed = array_map(fn($b) => [
                    'max'  => isset($b['max']) ? (string)$b['max'] : '',
                    'rate' => isset($b['rate']) ? (string)$b['rate'] : '',
                ], $decoded);
            }
        }
        if (empty($bracketsFed)) {
            $bracketsFed = [
                ['max' => '58523',  'rate' => '14'],
                ['max' => '117045', 'rate' => '20.5'],
                ['max' => '181440', 'rate' => '26'],
                ['max' => '258482', 'rate' => '29'],
                ['max' => '',       'rate' => '33'],
            ];
        }

        $bracketsQcRaw = $p['fiscalite']['brackets_qc'] ?? null;
        $bracketsQc = [];
        if ($bracketsQcRaw) {
            $decoded = json_decode($bracketsQcRaw, true);
            if (is_array($decoded)) {
                $bracketsQc = array_map(fn($b) => [
                    'max'  => isset($b['max']) ? (string)$b['max'] : '',
                    'rate' => isset($b['rate']) ? (string)$b['rate'] : '',
                ], $decoded);
            }
        }
        if (empty($bracketsQc)) {
            $bracketsQc = [
                ['max' => '54345',  'rate' => '14'],
                ['max' => '108680', 'rate' => '19'],
                ['max' => '132245', 'rate' => '24'],
                ['max' => '',       'rate' => '25.75'],
            ];
        }

        $this->form->fill([
            // Valeurs par défaut
            'province'          => $p['abf']['province_defaut']          ?? 'Québec',
            'fu_type'           => $p['fonds_urgence']['type']            ?? 'income',
            'fu_mois'           => $p['fonds_urgence']['mois']            ?? '3',
            'deces_funerailles' => $p['deces']['funerailles']             ?? '10000',
            'deces_rr_type'     => $p['deces']['rr_type']                 ?? 'family',
            'deces_rr_pct'      => $p['deces']['rr_pct']                  ?? '70',
            'deces_salaire_type'=> $p['deces']['salaire_type']            ?? 'gross',
            'deces_frequence'   => $p['deces']['frequence']               ?? 'yearly',
            'inv_type'          => $p['invalidite']['type']               ?? 'incomeReplacement',
            'inv_salaire_type'  => $p['invalidite']['salaire_type']       ?? 'gross',
            'inv_rr_pct'        => $p['invalidite']['rr_pct']             ?? '70',
            'mg_niveau'         => $p['maladie_grave']['niveau']          ?? 'comfort',
            'ret_rr_pct'        => $p['retraite']['rr_pct']               ?? '70',
            'ret_frequence'     => $p['retraite']['frequence']            ?? 'yearly',
            'ret_calcul'        => $p['retraite']['calcul']               ?? 'average',
            'inflation'         => $p['hypotheses']['inflation']          ?? '2.10',
            'ev_client'         => $p['hypotheses']['ev_client']          ?? '85',
            'ev_conjoint'       => $p['hypotheses']['ev_conjoint']        ?? '87',
            'port_prudent'      => $p['portefeuilles']['prudent']         ?? '3.00',
            'port_modere'       => $p['portefeuilles']['modere']          ?? '3.30',
            'port_equilibre'    => $p['portefeuilles']['equilibre']       ?? '3.70',
            'port_croissance'   => $p['portefeuilles']['croissance']      ?? '4.00',
            'port_audacieux'    => $p['portefeuilles']['audacieux']       ?? '4.30',
            'abf_return_rate'   => $p['abf']['return_rate']              ?? '5.00',
            'abf_replace_years' => $p['abf']['replace_years']            ?? '20',

            // Fiscalité
            'brackets_fed'          => $bracketsFed,
            'brackets_qc'           => $bracketsQc,
            'fed_base_max'          => $p['fiscalite']['fed_base_max']          ?? '16452',
            'fed_base_min'          => $p['fiscalite']['fed_base_min']          ?? '14829',
            'fed_base_seuil_bas'    => $p['fiscalite']['fed_base_seuil_bas']    ?? '173205',
            'fed_base_seuil_haut'   => $p['fiscalite']['fed_base_seuil_haut']   ?? '235675',
            'fed_credit_rate'       => $p['fiscalite']['fed_credit_rate']       ?? '15',
            'qc_base'               => $p['fiscalite']['qc_base']               ?? '18952',
            'qc_credit_rate'        => $p['fiscalite']['qc_credit_rate']        ?? '14',
            'rrq_exemption'         => $p['fiscalite']['rrq_exemption']         ?? '3500',
            'rrq_ceil1'             => $p['fiscalite']['rrq_ceil1']             ?? '74600',
            'rrq_rate1'             => $p['fiscalite']['rrq_rate1']             ?? '5.4',
            'rrq_ceil2'             => $p['fiscalite']['rrq_ceil2']             ?? '85000',
            'rrq_rate2'             => $p['fiscalite']['rrq_rate2']             ?? '1.0',
            'ae_ceil'               => $p['fiscalite']['ae_ceil']               ?? '68900',
            'ae_rate'               => $p['fiscalite']['ae_rate']               ?? '1.30',
            'rqap_ceil'             => $p['fiscalite']['rqap_ceil']             ?? '103000',
            'rqap_rate'             => $p['fiscalite']['rqap_rate']             ?? '0.430',

            // Rente conjoint survivant
            'rrq_rente_45_sans'     => $p['rrq']['rente_45_sans_conjoint'] ?? '719.50',
            'rrq_rente_45_avec'     => $p['rrq']['rente_45_avec_conjoint'] ?? '1129.95',
            'rrq_rente_45_inv'      => $p['rrq']['rente_45_invalidite']    ?? '1134.61',
            'rrq_prestation_deces'  => $p['rrq']['prestation_deces']       ?? '2500.00',
            'cpp_fixe'              => $p['rrq']['cpp_fixe']               ?? '217.83',

            // Rente orphelin
            'orphelin_rrq_simple'   => $p['rente_orphelin']['rrq_simple']  ?? '290.00',
            'orphelin_rrq_double'   => $p['rente_orphelin']['rrq_double']  ?? '728.00',
            'orphelin_cpp'          => $p['rente_orphelin']['cpp']         ?? '294.12',

            // CELI
            'celi_plafonds'         => $celiPlafonds,

            // Formulaire client (intake)
            'intake_steps'     => json_decode($p['intake']['steps_enabled'] ?? '["adresse","famille","revenus","epargne","actifs","dettes","retraite","objectifs"]', true) ?? [],
            'intake_mode_test' => filter_var($p['intake']['mode_test'] ?? false, FILTER_VALIDATE_BOOLEAN),

            // SV
            'sv_max_65_74'   => $p['sv']['max_mensuel_65_74']  ?? '727.67',
            'sv_max_75_plus' => $p['sv']['max_mensuel_75_plus'] ?? '800.44',
            'sv_srg_seul'    => $p['sv']['srg_seul_max']       ?? '1086.88',
            'sv_srg_couple'  => $p['sv']['srg_couple_max']     ?? '654.23',
            'sv_seuil_recup' => $p['sv']['seuil_recuperation'] ?? '93454',
            'sv_taux_recup'  => $p['sv']['taux_recuperation']  ?? '15',
            'sv_bonification'=> $p['sv']['bonification_report'] ?? '0.6',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer la configuration')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action(fn () => $this->save()),
        ];
    }

    public function form(Form $form): Form
    {
        $isAdmin = auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;

        return $form
            ->schema([
                Tabs::make('tabs')
                    ->tabs([
                        // ── Tab 1: Valeurs par défaut ──────────────────────────
                        Tabs\Tab::make('Valeurs par défaut')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Section::make('Province')
                                    ->schema([
                                        Select::make('province')
                                            ->label('Province d\'imposition')
                                            ->options([
                                                'Alberta'                    => 'Alberta',
                                                'Colombie-Britannique'       => 'Colombie-Britannique',
                                                'Île-du-Prince-Édouard'     => 'Île-du-Prince-Édouard',
                                                'Manitoba'                   => 'Manitoba',
                                                'Nouveau-Brunswick'          => 'Nouveau-Brunswick',
                                                'Nouvelle-Écosse'            => 'Nouvelle-Écosse',
                                                'Nunavut'                    => 'Nunavut',
                                                'Ontario'                    => 'Ontario',
                                                'Québec'                     => 'Québec',
                                                'Saskatchewan'               => 'Saskatchewan',
                                                'Terre-Neuve-et-Labrador'    => 'Terre-Neuve-et-Labrador',
                                                'Territoires du Nord-Ouest'  => 'Territoires du Nord-Ouest',
                                                'Yukon'                      => 'Yukon',
                                            ]),
                                    ]),

                                Section::make('Fonds d\'urgence')
                                    ->columns(2)
                                    ->schema([
                                        Radio::make('fu_type')
                                            ->label('Type de fonds d\'urgence')
                                            ->inline()
                                            ->options([
                                                'income'   => 'Revenu mensuel',
                                                'expenses' => 'Dépenses mensuelles',
                                                'amount'   => 'Montant fixe',
                                                'none'     => 'Aucun',
                                            ]),
                                        TextInput::make('fu_mois')
                                            ->label('Mois de réserve')
                                            ->suffix('mois')
                                            ->numeric(),
                                    ]),

                                Section::make('Décès')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('deces_funerailles')
                                            ->label('Frais funéraires')
                                            ->prefix('$')
                                            ->numeric(),
                                        Radio::make('deces_rr_type')
                                            ->label('Type de remplacement du revenu')
                                            ->options([
                                                'family'     => 'Familial',
                                                'individual' => 'Individuel',
                                            ]),
                                        TextInput::make('deces_rr_pct')
                                            ->label('Pourcentage de remplacement')
                                            ->suffix('%')
                                            ->numeric(),
                                        Radio::make('deces_salaire_type')
                                            ->label('Type de salaire')
                                            ->options([
                                                'gross' => 'Brut',
                                                'net'   => 'Net',
                                            ]),
                                        Radio::make('deces_frequence')
                                            ->label('Fréquence')
                                            ->options([
                                                'yearly'  => 'Annuel',
                                                'monthly' => 'Mensuel',
                                            ]),
                                    ]),

                                Section::make('Invalidité')
                                    ->columns(2)
                                    ->schema([
                                        Radio::make('inv_type')
                                            ->label('Approche de calcul')
                                            ->options([
                                                'incomeReplacement' => 'Remplacement du revenu',
                                                'expensesCoverage'  => 'Dépenses courantes',
                                            ]),
                                        Radio::make('inv_salaire_type')
                                            ->label('Type de salaire')
                                            ->options([
                                                'gross' => 'Brut',
                                                'net'   => 'Net',
                                            ]),
                                        TextInput::make('inv_rr_pct')
                                            ->label('Pourcentage de remplacement')
                                            ->suffix('%')
                                            ->numeric(),
                                    ]),

                                Section::make('Maladie grave')
                                    ->schema([
                                        Radio::make('mg_niveau')
                                            ->label('Niveau de protection')
                                            ->inline()
                                            ->options([
                                                'none'    => 'Aucun',
                                                'base'    => 'Base',
                                                'comfort' => 'Confort',
                                                'premium' => 'Supérieur',
                                            ]),
                                    ]),

                                Section::make('Retraite')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('ret_rr_pct')
                                            ->label('Objectif de remplacement du revenu')
                                            ->suffix('%')
                                            ->numeric(),
                                        Radio::make('ret_frequence')
                                            ->label('Fréquence')
                                            ->options([
                                                'yearly'  => 'Annuel',
                                                'monthly' => 'Mensuel',
                                            ]),
                                        Radio::make('ret_calcul')
                                            ->label('Approche de calcul du sommaire')
                                            ->options([
                                                'average' => 'Moyenne',
                                                'total'   => 'Total',
                                            ]),
                                    ]),

                                Section::make('Hypothèses')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('inflation')
                                            ->label('Taux d\'inflation')
                                            ->suffix('%')
                                            ->numeric(),
                                        TextInput::make('ev_client')
                                            ->label('Espérance de vie — client')
                                            ->suffix('ans')
                                            ->numeric(),
                                        TextInput::make('ev_conjoint')
                                            ->label('Espérance de vie — conjoint')
                                            ->suffix('ans')
                                            ->numeric(),
                                    ]),

                                Section::make('Portefeuilles — Rendements nets')
                                    ->columns(5)
                                    ->schema([
                                        TextInput::make('port_prudent')
                                            ->label('Prudent')
                                            ->suffix('%')
                                            ->numeric(),
                                        TextInput::make('port_modere')
                                            ->label('Modéré')
                                            ->suffix('%')
                                            ->numeric(),
                                        TextInput::make('port_equilibre')
                                            ->label('Équilibré')
                                            ->suffix('%')
                                            ->numeric(),
                                        TextInput::make('port_croissance')
                                            ->label('Croissance')
                                            ->suffix('%')
                                            ->numeric(),
                                        TextInput::make('port_audacieux')
                                            ->label('Audacieux')
                                            ->suffix('%')
                                            ->numeric(),
                                    ]),

                                Section::make('Paramètres ABF')
                                    ->columns(2)
                                    ->collapsed()
                                    ->schema([
                                        TextInput::make('abf_return_rate')
                                            ->label('Taux de rendement ABF')
                                            ->suffix('%')
                                            ->numeric(),
                                        TextInput::make('abf_replace_years')
                                            ->label('Années de remplacement ABF')
                                            ->suffix('ans')
                                            ->numeric(),
                                    ]),
                            ]),

                        // ── Tab 2: Gestion de l'impôt ─────────────────────────
                        Tabs\Tab::make('Gestion de l\'impôt')
                            ->icon('heroicon-o-calculator')
                            ->visible($isAdmin)
                            ->schema([
                                Section::make('Paliers fédéraux')
                                    ->schema([
                                        Repeater::make('brackets_fed')
                                            ->label('')
                                            ->schema([
                                                TextInput::make('max')
                                                    ->label('Revenu max ($)')
                                                    ->placeholder('Illimité')
                                                    ->numeric(),
                                                TextInput::make('rate')
                                                    ->label('Taux (%)')
                                                    ->suffix('%')
                                                    ->numeric(),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('+ Ajouter un palier')
                                            ->reorderable(),
                                    ]),

                                Section::make('Montant personnel de base — Fédéral')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('fed_base_max')
                                            ->label('Montant max ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('fed_base_min')
                                            ->label('Montant min ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('fed_credit_rate')
                                            ->label('Taux du crédit (%)')
                                            ->suffix('%')
                                            ->numeric(),
                                        TextInput::make('fed_base_seuil_bas')
                                            ->label('Seuil bas ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('fed_base_seuil_haut')
                                            ->label('Seuil haut ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                    ]),

                                Section::make('Paliers Québec')
                                    ->schema([
                                        Repeater::make('brackets_qc')
                                            ->label('')
                                            ->schema([
                                                TextInput::make('max')
                                                    ->label('Revenu max ($)')
                                                    ->placeholder('Illimité')
                                                    ->numeric(),
                                                TextInput::make('rate')
                                                    ->label('Taux (%)')
                                                    ->suffix('%')
                                                    ->numeric(),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('+ Ajouter un palier')
                                            ->reorderable(),
                                    ]),

                                Section::make('Montant personnel de base — Québec')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('qc_base')
                                            ->label('Montant ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('qc_credit_rate')
                                            ->label('Taux du crédit (%)')
                                            ->suffix('%')
                                            ->numeric(),
                                    ]),

                                Section::make('Cotisations sociales')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('rrq_exemption')
                                            ->label('RRQ — Exemption ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('rrq_ceil1')
                                            ->label('RRQ — Plafond 1 ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('rrq_rate1')
                                            ->label('RRQ — Taux 1 (%)')
                                            ->suffix('%')
                                            ->numeric(),
                                        TextInput::make('rrq_ceil2')
                                            ->label('RRQ — Plafond 2 ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('rrq_rate2')
                                            ->label('RRQ — Taux 2 (%)')
                                            ->suffix('%')
                                            ->numeric(),
                                        TextInput::make('ae_ceil')
                                            ->label('AE — Plafond ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('ae_rate')
                                            ->label('AE — Taux (%)')
                                            ->suffix('%')
                                            ->numeric(),
                                        TextInput::make('rqap_ceil')
                                            ->label('RQAP — Plafond ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('rqap_rate')
                                            ->label('RQAP — Taux (%)')
                                            ->suffix('%')
                                            ->numeric(),
                                    ]),
                            ]),

                        // ── Tab 3: Rente conjoint survivant ───────────────────
                        Tabs\Tab::make('Rente conjoint survivant')
                            ->icon('heroicon-o-heart')
                            ->schema([
                                Section::make('RRQ — Montants maximaux mensuels')
                                    ->schema([
                                        TextInput::make('rrq_rente_45_sans')
                                            ->label('Moins de 45 ans — sans enfant')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('rrq_rente_45_avec')
                                            ->label('Moins de 45 ans — avec enfant(s)')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('rrq_rente_45_inv')
                                            ->label('Moins de 45 ans — invalide')
                                            ->prefix('$')
                                            ->numeric(),
                                    ]),

                                Section::make('CPP / RPC')
                                    ->schema([
                                        TextInput::make('cpp_fixe')
                                            ->label('Portion fixe mensuelle')
                                            ->prefix('$')
                                            ->numeric(),
                                    ]),

                                Section::make('Prestation de décès')
                                    ->schema([
                                        TextInput::make('rrq_prestation_deces')
                                            ->label('Prestation de décès RRQ ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                    ]),
                            ]),

                        // ── Tab 4: Rente orphelin ─────────────────────────────
                        Tabs\Tab::make('Rente orphelin')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Placeholder::make('orphelin_info')
                                    ->label('')
                                    ->content('Ces montants sont utilisés pour calculer les prestations d\'orphelin versées par le RRQ et le CPP/RPC. Ils sont exprimés en dollars par mois.'),

                                Section::make('RRQ')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('orphelin_rrq_simple')
                                            ->label('Orphelin d\'un parent (mensuel)')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('orphelin_rrq_double')
                                            ->label('Orphelin des deux parents (mensuel)')
                                            ->prefix('$')
                                            ->numeric(),
                                    ]),

                                Section::make('CPP / RPC')
                                    ->schema([
                                        TextInput::make('orphelin_cpp')
                                            ->label('Prestation mensuelle')
                                            ->prefix('$')
                                            ->numeric(),
                                    ]),
                            ]),

                        // ── Tab 5: CELI ────────────────────────────────────────
                        Tabs\Tab::make('CELI')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Placeholder::make('celi_info')
                                    ->label('')
                                    ->content('Plafonds annuels de cotisation depuis 2009. Ajouter une ligne pour les nouvelles années.'),

                                Repeater::make('celi_plafonds')
                                    ->label('Plafonds annuels')
                                    ->schema([
                                        TextInput::make('annee')
                                            ->label('Année')
                                            ->numeric()
                                            ->maxLength(4),
                                        TextInput::make('montant')
                                            ->label('Montant ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                    ])
                                    ->columns(2)
                                    ->grid(4)
                                    ->reorderableWithDragAndDrop(false)
                                    ->addActionLabel('+ Ajouter une année'),
                            ]),

                        // ── Tab 6: Sécurité de vieillesse ─────────────────────
                        Tabs\Tab::make('Sécurité de vieillesse')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Section::make('Prestations SV')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('sv_max_65_74')
                                            ->label('Max mensuel 65-74 ans')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('sv_max_75_plus')
                                            ->label('Max mensuel 75+ ans')
                                            ->prefix('$')
                                            ->numeric(),
                                    ]),

                                Section::make('Supplément de revenu garanti (SRG)')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('sv_srg_seul')
                                            ->label('Maximum — personne seule')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('sv_srg_couple')
                                            ->label('Maximum — conjoint')
                                            ->prefix('$')
                                            ->numeric(),
                                    ]),

                                Section::make('Récupération (clawback)')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('sv_seuil_recup')
                                            ->label('Seuil de récupération ($)')
                                            ->prefix('$')
                                            ->numeric(),
                                        TextInput::make('sv_taux_recup')
                                            ->label('Taux de récupération (%)')
                                            ->suffix('%')
                                            ->numeric(),
                                    ]),

                                Section::make('Report de la SV')
                                    ->schema([
                                        TextInput::make('sv_bonification')
                                            ->label('Bonification par mois de report')
                                            ->suffix('%')
                                            ->numeric(),
                                    ]),
                            ]),

                        // ── Tab 7: Photos de couverture ────────────────────────
                        Tabs\Tab::make('Photos de couverture')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Gestion des photos de couverture ABF')
                                    ->description('Téléversez une image JPEG/PNG pour chaque type de couverture. Résolution recommandée : 900×1200 px minimum. La photo sélectionnée sera intégrée dans le PDF en pleine page.')
                                    ->schema([
                                        \Filament\Forms\Components\Grid::make(3)->schema([

                                            // ── Neutre
                                            Section::make('Neutre 1')->schema([
                                                FileUpload::make('cover_neutre_1')->label('')->disk('abf_covers')->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->image()->imagePreviewHeight('100')->maxSize(4096)->getUploadedFileNameForStorageUsing(fn() => 'couv-neutre-1.jpg'),
                                            ]),
                                            Section::make('Neutre 2')->schema([
                                                FileUpload::make('cover_neutre_2')->label('')->disk('abf_covers')->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->image()->imagePreviewHeight('100')->maxSize(4096)->getUploadedFileNameForStorageUsing(fn() => 'couv-neutre-2.jpg'),
                                            ]),
                                            Section::make('Neutre 3')->schema([
                                                FileUpload::make('cover_neutre_3')->label('')->disk('abf_covers')->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->image()->imagePreviewHeight('100')->maxSize(4096)->getUploadedFileNameForStorageUsing(fn() => 'couv-neutre-3.jpg'),
                                            ]),

                                            // ── Couple avec enfants
                                            Section::make('Couple avec enfants 1')->schema([
                                                FileUpload::make('cover_couple_enfants_1')->label('')->disk('abf_covers')->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->image()->imagePreviewHeight('100')->maxSize(4096)->getUploadedFileNameForStorageUsing(fn() => 'couv-couple-enfants-1.jpg'),
                                            ]),
                                            Section::make('Couple avec enfants 2')->schema([
                                                FileUpload::make('cover_couple_enfants_2')->label('')->disk('abf_covers')->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->image()->imagePreviewHeight('100')->maxSize(4096)->getUploadedFileNameForStorageUsing(fn() => 'couv-couple-enfants-2.jpg'),
                                            ]),

                                            // ── Couple sans enfants
                                            Section::make('Couple sans enfants 1')->schema([
                                                FileUpload::make('cover_couple_sans_enfants_1')->label('')->disk('abf_covers')->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->image()->imagePreviewHeight('100')->maxSize(4096)->getUploadedFileNameForStorageUsing(fn() => 'couv-couple-sans-enfants-1.jpg'),
                                            ]),
                                            Section::make('Couple sans enfants 2')->schema([
                                                FileUpload::make('cover_couple_sans_enfants_2')->label('')->disk('abf_covers')->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->image()->imagePreviewHeight('100')->maxSize(4096)->getUploadedFileNameForStorageUsing(fn() => 'couv-couple-sans-enfants-2.jpg'),
                                            ]),

                                            // ── Homme seul
                                            Section::make('Homme seul 1')->schema([
                                                FileUpload::make('cover_homme_1')->label('')->disk('abf_covers')->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->image()->imagePreviewHeight('100')->maxSize(4096)->getUploadedFileNameForStorageUsing(fn() => 'couv-homme-1.jpg'),
                                            ]),
                                            Section::make('Homme seul 2')->schema([
                                                FileUpload::make('cover_homme_2')->label('')->disk('abf_covers')->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->image()->imagePreviewHeight('100')->maxSize(4096)->getUploadedFileNameForStorageUsing(fn() => 'couv-homme-2.jpg'),
                                            ]),

                                            // ── Femme seule
                                            Section::make('Femme seule 1')->schema([
                                                FileUpload::make('cover_femme_1')->label('')->disk('abf_covers')->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->image()->imagePreviewHeight('100')->maxSize(4096)->getUploadedFileNameForStorageUsing(fn() => 'couv-femme-1.jpg'),
                                            ]),
                                            Section::make('Femme seule 2')->schema([
                                                FileUpload::make('cover_femme_2')->label('')->disk('abf_covers')->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->image()->imagePreviewHeight('100')->maxSize(4096)->getUploadedFileNameForStorageUsing(fn() => 'couv-femme-2.jpg'),
                                            ]),

                                        ]),
                                    ]),
                            ]),

                        // ── Tab 8: Formulaire client ───────────────────────────
                        Tabs\Tab::make('Formulaire client')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Section::make('Sections incluses dans le formulaire envoyé au client')
                                    ->description('L\'identité (nom, date de naissance, courriel, téléphone) est toujours incluse. Le conjoint apparaît automatiquement si le client indique être marié.')
                                    ->schema([
                                        CheckboxList::make('intake_steps')
                                            ->label('Sections à inclure')
                                            ->options([
                                                // Infos de base
                                                'adresse'             => '📍 Adresse postale',
                                                'famille'             => '👨‍👩‍👧 Famille (état civil, nombre d\'enfants)',

                                                // Revenus & épargne
                                                'revenus'             => '💼 Revenus annuels (emploi)',
                                                'autres_revenus'      => '📈 Autres revenus (rentes, loyers, dividendes…)',
                                                'epargne'             => '🏦 Épargne et placements (REER, CELI, placements non enregistrés)',

                                                // Actifs & passifs
                                                'actifs'              => '🏠 Actifs (propriété, valeur marchande)',
                                                'dettes'              => '💳 Dettes et passifs (hypothèque, prêts, cartes de crédit)',

                                                // Assurances actuelles
                                                'assurance_vie'       => '🛡️ Assurance vie en vigueur',
                                                'assurance_invalidite'=> '🏥 Assurance invalidité en vigueur',
                                                'assurance_mg'        => '⚕️ Assurance maladie grave en vigueur',

                                                // Analyse de besoins
                                                'fonds_urgence'       => '🆘 Fonds d\'urgence (montant disponible)',
                                                'retraite'            => '🌅 Retraite (âge visé, objectif de revenus)',
                                                'objectifs'           => '🎯 Objectifs et projets de vie',

                                                // Santé
                                                'sante'               => '🚬 Usage du tabac (santé)',

                                                // Profil
                                                'profil_investisseur' => '📊 Profil d\'investisseur (tolérance au risque)',
                                            ])
                                            ->columns(1)
                                            ->gridDirection('row'),
                                    ]),
                                Section::make('Mode test')
                                    ->description('Quand activé, les réponses complètes du client sont enregistrées dans les logs système à la soumission. À désactiver après validation.')
                                    ->schema([
                                        \Filament\Forms\Components\Toggle::make('intake_mode_test')
                                            ->label('Activer le mode test')
                                            ->helperText('Les données du formulaire client seront visibles dans Logs Système → source "public" après chaque soumission.')
                                            ->onColor('warning')
                                            ->offColor('gray'),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        // Valeurs par défaut
        $this->saveParam('abf',            'province_defaut',    $state['province']           ?? '');
        $this->saveParam('fonds_urgence',  'type',               $state['fu_type']            ?? '');
        $this->saveParam('fonds_urgence',  'mois',               $state['fu_mois']            ?? '');
        $this->saveParam('deces',          'funerailles',        $state['deces_funerailles']  ?? '');
        $this->saveParam('deces',          'rr_type',            $state['deces_rr_type']      ?? '');
        $this->saveParam('deces',          'rr_pct',             $state['deces_rr_pct']       ?? '');
        $this->saveParam('deces',          'salaire_type',       $state['deces_salaire_type'] ?? '');
        $this->saveParam('deces',          'frequence',          $state['deces_frequence']    ?? '');
        $this->saveParam('invalidite',     'type',               $state['inv_type']           ?? '');
        $this->saveParam('invalidite',     'salaire_type',       $state['inv_salaire_type']   ?? '');
        $this->saveParam('invalidite',     'rr_pct',             $state['inv_rr_pct']         ?? '');
        $this->saveParam('maladie_grave',  'niveau',             $state['mg_niveau']          ?? '');
        $this->saveParam('retraite',       'rr_pct',             $state['ret_rr_pct']         ?? '');
        $this->saveParam('retraite',       'frequence',          $state['ret_frequence']      ?? '');
        $this->saveParam('retraite',       'calcul',             $state['ret_calcul']         ?? '');
        $this->saveParam('hypotheses',     'inflation',          $state['inflation']          ?? '');
        $this->saveParam('hypotheses',     'ev_client',          $state['ev_client']          ?? '');
        $this->saveParam('hypotheses',     'ev_conjoint',        $state['ev_conjoint']        ?? '');
        $this->saveParam('portefeuilles',  'prudent',            $state['port_prudent']       ?? '');
        $this->saveParam('portefeuilles',  'modere',             $state['port_modere']        ?? '');
        $this->saveParam('portefeuilles',  'equilibre',          $state['port_equilibre']     ?? '');
        $this->saveParam('portefeuilles',  'croissance',         $state['port_croissance']    ?? '');
        $this->saveParam('portefeuilles',  'audacieux',          $state['port_audacieux']     ?? '');
        $this->saveParam('abf',            'return_rate',        $state['abf_return_rate']    ?? '');
        $this->saveParam('abf',            'replace_years',      $state['abf_replace_years']  ?? '');

        // Fiscalité — brackets (JSON)
        $bracketsFed = array_values($state['brackets_fed'] ?? []);
        $bracketsFed = array_map(fn($b) => [
            'max'  => ($b['max'] === '' || $b['max'] === null) ? null : (float)$b['max'],
            'rate' => (float)($b['rate'] ?? 0),
        ], $bracketsFed);
        $this->saveParam('fiscalite', 'brackets_fed', json_encode($bracketsFed), 'Paliers fédéraux', 'json');

        $bracketsQc = array_values($state['brackets_qc'] ?? []);
        $bracketsQc = array_map(fn($b) => [
            'max'  => ($b['max'] === '' || $b['max'] === null) ? null : (float)$b['max'],
            'rate' => (float)($b['rate'] ?? 0),
        ], $bracketsQc);
        $this->saveParam('fiscalite', 'brackets_qc', json_encode($bracketsQc), 'Paliers Québec', 'json');

        $this->saveParam('fiscalite', 'fed_base_max',        $state['fed_base_max']        ?? '');
        $this->saveParam('fiscalite', 'fed_base_min',        $state['fed_base_min']        ?? '');
        $this->saveParam('fiscalite', 'fed_base_seuil_bas',  $state['fed_base_seuil_bas']  ?? '');
        $this->saveParam('fiscalite', 'fed_base_seuil_haut', $state['fed_base_seuil_haut'] ?? '');
        $this->saveParam('fiscalite', 'fed_credit_rate',     $state['fed_credit_rate']     ?? '');
        $this->saveParam('fiscalite', 'qc_base',             $state['qc_base']             ?? '');
        $this->saveParam('fiscalite', 'qc_credit_rate',      $state['qc_credit_rate']      ?? '');
        $this->saveParam('fiscalite', 'rrq_exemption',       $state['rrq_exemption']       ?? '');
        $this->saveParam('fiscalite', 'rrq_ceil1',           $state['rrq_ceil1']           ?? '');
        $this->saveParam('fiscalite', 'rrq_rate1',           $state['rrq_rate1']           ?? '');
        $this->saveParam('fiscalite', 'rrq_ceil2',           $state['rrq_ceil2']           ?? '');
        $this->saveParam('fiscalite', 'rrq_rate2',           $state['rrq_rate2']           ?? '');
        $this->saveParam('fiscalite', 'ae_ceil',             $state['ae_ceil']             ?? '');
        $this->saveParam('fiscalite', 'ae_rate',             $state['ae_rate']             ?? '');
        $this->saveParam('fiscalite', 'rqap_ceil',           $state['rqap_ceil']           ?? '');
        $this->saveParam('fiscalite', 'rqap_rate',           $state['rqap_rate']           ?? '');

        // Rente conjoint survivant
        $this->saveParam('rrq', 'rente_45_sans_conjoint', $state['rrq_rente_45_sans']    ?? '');
        $this->saveParam('rrq', 'rente_45_avec_conjoint', $state['rrq_rente_45_avec']    ?? '');
        $this->saveParam('rrq', 'rente_45_invalidite',    $state['rrq_rente_45_inv']     ?? '');
        $this->saveParam('rrq', 'prestation_deces',       $state['rrq_prestation_deces'] ?? '');
        $this->saveParam('rrq', 'cpp_fixe',               $state['cpp_fixe']             ?? '');

        // Rente orphelin
        $this->saveParam('rente_orphelin', 'rrq_simple', $state['orphelin_rrq_simple'] ?? '');
        $this->saveParam('rente_orphelin', 'rrq_double', $state['orphelin_rrq_double'] ?? '');
        $this->saveParam('rente_orphelin', 'cpp',        $state['orphelin_cpp']        ?? '');

        // CELI plafonds (JSON sorted by annee)
        $celiPlafonds = array_values($state['celi_plafonds'] ?? []);
        usort($celiPlafonds, fn($a, $b) => (int)($a['annee'] ?? 0) <=> (int)($b['annee'] ?? 0));
        $celiPlafonds = array_map(fn($row) => [
            'annee'   => (int)($row['annee'] ?? 0),
            'montant' => (float)($row['montant'] ?? 0),
        ], $celiPlafonds);
        $this->saveParam('celi', 'plafonds', json_encode($celiPlafonds), 'Plafonds annuels CELI', 'json');

        // Formulaire client (intake)
        $intakeSteps = array_values(array_filter($state['intake_steps'] ?? [], fn($s) => is_string($s)));
        $this->saveParam('intake', 'steps_enabled', json_encode($intakeSteps), 'Sections du formulaire client', 'json');
        $this->saveParam('intake', 'mode_test', $state['intake_mode_test'] ? '1' : '0', 'Mode test (log des réponses)', 'boolean');

        // SV
        $this->saveParam('sv', 'max_mensuel_65_74',  $state['sv_max_65_74']    ?? '');
        $this->saveParam('sv', 'max_mensuel_75_plus', $state['sv_max_75_plus'] ?? '');
        $this->saveParam('sv', 'srg_seul_max',        $state['sv_srg_seul']    ?? '');
        $this->saveParam('sv', 'srg_couple_max',      $state['sv_srg_couple']  ?? '');
        $this->saveParam('sv', 'seuil_recuperation',  $state['sv_seuil_recup'] ?? '');
        $this->saveParam('sv', 'taux_recuperation',   $state['sv_taux_recup']  ?? '');
        $this->saveParam('sv', 'bonification_report', $state['sv_bonification'] ?? '');

        Notification::make()
            ->title('Configuration ABF enregistrée')
            ->success()
            ->send();
    }

    private function saveParam(string $group, string $key, mixed $value, string $label = '', string $type = 'text'): void
    {
        AbfParameter::updateOrCreate(
            ['group' => $group, 'key' => $key],
            [
                'value'      => (string)$value,
                'label'      => $label ?: "{$group}.{$key}",
                'type'       => $type,
                'sort_order' => 0,
            ]
        );
    }
}
