<?php

namespace App\Filament\Abf\Resources\AbfCaseResource\Steps;

use App\Filament\Abf\Support\BilanUi;
use App\Services\Abf\DeathBudget\DeathBudgetCalculator;
use App\Services\Abf\DeathBudget\Filament\DeathBudgetFilamentAutofill;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\HtmlString;

final class DeathBudgetStep
{
    public static function make(): Step
    {
        return Step::make('Budget au décès')
            ->id('budget_deces')
            ->schema([
                Hidden::make('payload.death_bilan.meta.initialized')
                    ->default(false)
                    ->afterStateHydrated(function (Get $get, Set $set, $state) {
                        if (! $state) {
                            self::autofill()->prefillAll($get, $set, true);
                            $set('payload.death_bilan.meta.initialized', true);
                        }
                    }),

                Actions::make([
                    Actions\Action::make('fill_death_budget')
                        ->label('Remplir depuis Actifs / Passifs / Protections')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function (Get $get, Set $set) {
                            self::autofill()->prefillAll($get, $set, false);
                            $set('payload.death_bilan.meta.last_autofill_at', now()->toIso8601String());
                        }),
                ])->alignEnd(),

                self::sectionA(),
                self::sectionB(),
                self::sectionC(),
                self::sectionD(),
                self::sectionE(),
            ]);
    }

    private static function autofill(): DeathBudgetFilamentAutofill
    {
        return app(DeathBudgetFilamentAutofill::class);
    }

    private static function calc(): DeathBudgetCalculator
    {
        return app(DeathBudgetCalculator::class);
    }

    private static function sectionA(): Section
    {
        return Section::make('A — Bilan au décès')
            ->extraAttributes(['class' => 'abf-bilan-deces'])
            ->schema([
                \Filament\Forms\Components\Grid::make(2)->schema([
                    Section::make('ACTIF')->schema([
                        BilanUi::headerRow('actif'),
                        BilanUi::moneyRow('Assurance vie individuelle', 'payload.death_bilan.actif.life_individual', 'actif_life_individual'),
                        BilanUi::moneyRow('Assurance collective', 'payload.death_bilan.actif.group_insurance', 'actif_group_insurance'),
                        BilanUi::moneyRow('Assurance hypothécaire', 'payload.death_bilan.actif.mortgage_insurance', 'actif_mortgage_insurance'),
                        BilanUi::moneyRow('Prestations de décès RRQ/RPC', 'payload.death_bilan.actif.rrq_rpc', 'actif_rrq_rpc'),
                        BilanUi::moneyRow('Liquidités (ex. : épargne)', 'payload.death_bilan.actif.liquidities', 'actif_liquidities'),
                        BilanUi::moneyRow('REER (si encaissés)', 'payload.death_bilan.actif.rrsp', 'actif_rrsp'),
                        BilanUi::moneyRow('Résidence principale (si vendue)', 'payload.death_bilan.actif.primary_residence', 'actif_primary_residence'),
                        BilanUi::moneyRow('Résidence secondaire (si vendue)', 'payload.death_bilan.actif.secondary_residence', 'actif_secondary_residence'),
                        BilanUi::moneyRow('Autres actifs', 'payload.death_bilan.actif.other_assets', 'actif_other_assets'),
                        BilanUi::totalRow('TOTAL ACTIF', 'total_actif', fn(Get $get, string $person) => self::calc()->deathBilanTotal((array) ($get('payload') ?? []), 'actif', $person)),
                    ]),

                    Section::make('PASSIF')->schema([
                        BilanUi::headerRow('passif'),
                        BilanUi::moneyRow('Dernières dépenses', 'payload.death_bilan.passif.last_expenses', 'passif_last_expenses'),
                        BilanUi::moneyRow('Frais de liquidation', 'payload.death_bilan.passif.liquidation_fees', 'passif_liquidation_fees'),
                        BilanUi::moneyRow('Fonds d’urgence', 'payload.death_bilan.passif.emergency_fund', 'passif_emergency_fund'),
                        BilanUi::moneyRow('Fonds d’études', 'payload.death_bilan.passif.education_fund', 'passif_education_fund'),
                        BilanUi::moneyRow('Solde hypothécaire', 'payload.death_bilan.passif.mortgage_balance', 'passif_mortgage_balance'),
                        BilanUi::moneyRow('Solde — carte de crédit', 'payload.death_bilan.passif.credit_card_balance', 'passif_credit_card_balance'),
                        BilanUi::moneyRow('Solde — marge de crédit', 'payload.death_bilan.passif.line_of_credit_balance', 'passif_line_of_credit_balance'),
                        BilanUi::moneyRow('Solde — prêt auto', 'payload.death_bilan.passif.auto_loan_balance', 'passif_auto_loan_balance'),
                        BilanUi::moneyRow('Impôts (personnels, REER si encaissés, résidence secondaire si vendue)', 'payload.death_bilan.passif.taxes', 'passif_taxes'),
                        BilanUi::moneyRow('Dons de bienfaisance', 'payload.death_bilan.passif.charity', 'passif_charity'),
                        BilanUi::moneyRow('Autres', 'payload.death_bilan.passif.other_liabilities', 'passif_other_liabilities'),
                        BilanUi::totalRow('TOTAL PASSIF', 'total_passif', fn(Get $get, string $person) => self::calc()->deathBilanTotal((array) ($get('payload') ?? []), 'passif', $person)),
                    ]),
                ]),
            ]);
    }

    private static function sectionB(): Section
    {
        return Section::make('B — Liquidités nettes disponibles au décès')
            ->schema([
                \Filament\Forms\Components\Grid::make(3)->schema([
                    Placeholder::make('b_hdr_lbl')->label(false)->content(''),
                    Placeholder::make('b_hdr_vous')->label(false)->content(new HtmlString('<div class="abf-bilan-colhead">VOUS</div>')),
                    Placeholder::make('b_hdr_conjoint')->label(false)->content(new HtmlString('<div class="abf-bilan-colhead">VOTRE CONJOINT</div>')),
                ]),
                BilanUi::dualMoneyInputRow('B1 — Total des liquidités disponibles (actif)', 'payload.death_budget.b.total_liquidities'),
                BilanUi::dualMoneyInputRow('B2 — Total des besoins immédiats au décès (passif)', 'payload.death_budget.b.total_immediate_needs'),
                BilanUi::dualMoneyComputedRow('LIQUIDITÉS NETTES DISPONIBLES AU DÉCÈS (B1 - B2)', fn(Get $get, string $person) => self::calc()->bNetLiquidities((array) ($get('payload') ?? []), $person)),
            ]);
    }

    private static function sectionC(): Section
    {
        return Section::make('C — Revenu mensuel nécessaire après le décès')
            ->schema([
                \Filament\Forms\Components\Grid::make(3)->schema([
                    Placeholder::make('c_hdr_lbl')->label(false)->content(''),
                    Placeholder::make('c_hdr_vous')->label(false)->content(new HtmlString('<div class="abf-bilan-colhead">VOUS</div>')),
                    Placeholder::make('c_hdr_conjoint')->label(false)->content(new HtmlString('<div class="abf-bilan-colhead">VOTRE CONJOINT</div>')),
                ]),
                BilanUi::dualMoneyInputRow('C1 — Revenu mensuel brut actuel (toutes les sources)', 'payload.death_budget.c.current_gross_monthly'),
                BilanUi::dualMoneyInputRow('C2 — Estimation du revenu mensuel à conserver après le décès', 'payload.death_budget.c.target_monthly_after_death'),
                BilanUi::dualMoneyInputRow('C3 — Rente du conjoint/orphelin (RRQ/RPC)', 'payload.death_budget.c.rrq_rpc_survivor_rente'),
                BilanUi::dualMoneyComputedRow('REVENU MENSUEL À COMBLER (C2 - C3)', fn(Get $get, string $person) => self::calc()->cMonthlyGap((array) ($get('payload') ?? []), $person)),
            ]);
    }

    private static function sectionD(): Section
    {
        return Section::make('D — Capital requis pour le revenu mensuel à combler')
            ->schema([
                \Filament\Forms\Components\Grid::make(3)->schema([
                    Placeholder::make('d_hdr_lbl')->label(false)->content(''),
                    Placeholder::make('d_hdr_vous')->label(false)->content(new HtmlString('<div class="abf-bilan-colhead">VOUS</div>')),
                    Placeholder::make('d_hdr_conjoint')->label(false)->content(new HtmlString('<div class="abf-bilan-colhead">VOTRE CONJOINT</div>')),
                ]),

                BilanUi::dualMoneyComputedRow('D1 — Revenu mensuel à combler', fn(Get $get, string $person) => self::calc()->cMonthlyGap((array) ($get('payload') ?? []), $person)),
                BilanUi::dualPercentInputRow('D2 — Hypothèse de taux de rendement (net inflation)', 'payload.death_budget.d.return_rate_percent'),
                BilanUi::dualYearsInputRow('D3 — Durée', 'payload.death_budget.d.duration_years'),
                BilanUi::dualNumberComputedRow('D4 — Facteur', fn(Get $get, string $person) => self::calc()->dCapitalFactor((array) ($get('payload') ?? []), $person), 2),
                BilanUi::dualMoneyComputedRow('D5 — Revenu mensuel à combler', fn(Get $get, string $person) => self::calc()->cMonthlyGap((array) ($get('payload') ?? []), $person)),
                BilanUi::dualMoneyComputedRow('CAPITAL REQUIS POUR LE REVENU MENSUEL À COMBLER (D4 x D5)', fn(Get $get, string $person) => self::calc()->dCapitalRequired((array) ($get('payload') ?? []), $person)),

                Section::make('Tableau de facteurs (référence PDF)')
                    ->collapsed()
                    ->schema([
                        Placeholder::make('d_factor_table')
                            ->label(false)
                            ->content(new HtmlString(self::calc()->renderCapitalFactorTableHtml())),
                    ]),
            ]);
    }

    private static function sectionE(): Section
    {
        return Section::make("E — Montant d'assurance additionnel nécessaire")
            ->schema([
                \Filament\Forms\Components\Grid::make(3)->schema([
                    Placeholder::make('e_hdr_lbl')->label(false)->content(''),
                    Placeholder::make('e_hdr_vous')->label(false)->content(new HtmlString('<div class="abf-bilan-colhead">VOUS</div>')),
                    Placeholder::make('e_hdr_conjoint')->label(false)->content(new HtmlString('<div class="abf-bilan-colhead">VOTRE CONJOINT</div>')),
                ]),

                BilanUi::dualMoneyComputedRow('E1 — Capital requis pour le revenu mensuel à combler', fn(Get $get, string $person) => self::calc()->dCapitalRequired((array) ($get('payload') ?? []), $person)),
                BilanUi::dualMoneyComputedRow('E2 — Liquidités nettes au décès', fn(Get $get, string $person) => self::calc()->bNetLiquidities((array) ($get('payload') ?? []), $person)),
                BilanUi::dualMoneyComputedRow("MONTANT D'ASSURANCE ADDITIONNEL NÉCESSAIRE (E1 - E2)", fn(Get $get, string $person) => self::calc()->eAdditionalNeed((array) ($get('payload') ?? []), $person)),

                TextInput::make('payload.death_budget.e.weekly_savings_capacity')
                    ->label("Combien d'argent de plus par semaine pouvez-vous investir ?")
                    ->numeric()
                    ->prefix('$'),

                BilanUi::dualMoneyInputRow("Montant d'assurance additionnel couvert", 'payload.death_budget.e.additional_covered_amount'),

                \Filament\Forms\Components\Grid::make(2)->schema([
                    Toggle::make('payload.death_budget.e.wants_critical_illness')->label('Désire une protection complémentaire en cas de maladie grave ?'),
                    Toggle::make('payload.death_budget.e.wants_renewal_submission')->label('Désire une soumission pour renouvellements ?'),
                ]),

                \Filament\Forms\Components\Grid::make(3)->schema([
                    TextInput::make('payload.death_budget.e.renewals.auto_insurance_date')->label('Assurance auto (A/M/J)'),
                    TextInput::make('payload.death_budget.e.renewals.home_insurance_date')->label('Assurance habitation (A/M/J)'),
                    TextInput::make('payload.death_budget.e.renewals.mortgage_loan_date')->label('Prêt hypothécaire (A/M/J)'),
                ]),

                Section::make('Signatures (capture texte / métadonnées)')
                    ->columns(3)
                    ->schema([
                        TextInput::make('payload.death_budget.e.signatures.client_name')->label('Signature — Vous (nom)'),
                        TextInput::make('payload.death_budget.e.signatures.spouse_name')->label('Signature — Votre conjoint (nom)')
                            ->visible(fn(Get $get) => (bool) $get('payload.has_spouse')),
                        TextInput::make('payload.death_budget.e.signatures.signed_on')->label('Date signature'),
                    ]),
            ]);
    }
}
