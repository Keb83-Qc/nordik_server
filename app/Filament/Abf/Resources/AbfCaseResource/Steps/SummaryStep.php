<?php

namespace App\Filament\Abf\Resources\AbfCaseResource\Steps;

use App\Filament\Abf\Support\Money;
use App\Models\AbfCase;
use App\Services\Abf\DeathBudget\DeathBudgetCalculator;
use App\Services\Abf\TfsaEstimator;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Illuminate\Support\Carbon;

final class SummaryStep
{
    public static function make(): Step
    {
        return Step::make('Résumé')
            ->id('resume')
            ->schema([
                View::make('filament.abf.bilan-summary')
                    ->viewData(function (?AbfCase $record) {
                        if (! $record) {
                            return ['results' => null, 'payload' => []];
                        }

                        return [
                            'results' => app(\App\Services\AbfCaseCalculator::class)->calculate($record->payload ?? []),
                            'payload' => $record->payload ?? [],
                            'case' => $record,
                        ];
                    }),

                Section::make('Vérification rapide')
                    ->columns(4)
                    ->schema([
                        Placeholder::make('resume_client_name')
                            ->label('Client')
                            ->content(fn(Get $get) => trim((string) ($get('payload.client.first_name') ?? '') . ' ' . (string) ($get('payload.client.last_name') ?? '')) ?: '—'),

                        Placeholder::make('resume_has_spouse')
                            ->label('Conjoint')
                            ->content(fn(Get $get) => $get('payload.has_spouse') ? 'Oui' : 'Non'),

                        Placeholder::make('resume_spouse_sync')
                            ->label('Conjoint — même adresse/tél.')
                            ->content(fn(Get $get) => ((bool) ($get('payload.spouse.same_contact_as_client') ?? false)) ? 'Oui' : 'Non')
                            ->visible(fn(Get $get) => (bool) $get('payload.has_spouse')),

                        Placeholder::make('resume_dependents_count')
                            ->label('Personnes à charge')
                            ->content(fn(Get $get) => (string) count((array) ($get('payload.dependents') ?? []))),

                        Placeholder::make('resume_dependents_minors')
                            ->label('Enfants mineurs')
                            ->content(function (Get $get) {
                                $deps = (array) ($get('payload.dependents') ?? []);
                                $minors = 0;

                                foreach ($deps as $d) {
                                    $birth = $d['birth_date'] ?? null;
                                    if (blank($birth)) {
                                        continue;
                                    }
                                    try {
                                        if (Carbon::parse($birth)->age < 18) {
                                            $minors++;
                                        }
                                    } catch (\Throwable) {}
                                }

                                return (string) $minors;
                            }),

                        Placeholder::make('resume_additional_need_total')
                            ->label('Besoin add. (client + conjoint)')
                            ->content(fn(Get $get) => Money::dollars(
                                self::calc()->eAdditionalNeed((array) ($get('payload') ?? []), 'client')
                                + self::calc()->eAdditionalNeed((array) ($get('payload') ?? []), 'spouse')
                            ))
                            ->columnSpan(2),
                    ]),

                Section::make('CELI (approximation théorique)')
                    ->columns(4)
                    ->schema([
                        Placeholder::make('resume_tfsa_room_client')
                            ->label('Vous — CELI max théorique')
                            ->content(fn(Get $get) => Money::dollars(self::tfsa()->maxContributionRoom((array) ($get('payload') ?? []), 'client'))),

                        Placeholder::make('resume_tfsa_start_client')
                            ->label('Vous — année début CELI')
                            ->content(fn(Get $get) => (string) (self::tfsa()->eligibilityStartYear((array) ($get('payload') ?? []), 'client') ?? '—')),

                        Placeholder::make('resume_tfsa_room_spouse')
                            ->label('Conjoint — CELI max théorique')
                            ->content(fn(Get $get) => Money::dollars(self::tfsa()->maxContributionRoom((array) ($get('payload') ?? []), 'spouse')))
                            ->visible(fn(Get $get) => (bool) $get('payload.has_spouse')),

                        Placeholder::make('resume_tfsa_start_spouse')
                            ->label('Conjoint — année début CELI')
                            ->content(fn(Get $get) => (string) (self::tfsa()->eligibilityStartYear((array) ($get('payload') ?? []), 'spouse') ?? '—'))
                            ->visible(fn(Get $get) => (bool) $get('payload.has_spouse')),

                        Placeholder::make('resume_tfsa_disclaimer')
                            ->label('Note')
                            ->content("Approximation sans historique de cotisations/retraits. Pour PR/TR: basée sur la date 'Travaille au Canada depuis' (si NAS). À valider avec les dossiers du client / ARC.")
                            ->columnSpanFull(),
                    ]),

                Textarea::make('payload.advisor_notes')
                    ->label('Notes du conseiller')
                    ->rows(6),
            ]);
    }

    private static function tfsa(): TfsaEstimator
    {
        return app(TfsaEstimator::class);
    }

    private static function calc(): DeathBudgetCalculator
    {
        return app(DeathBudgetCalculator::class);
    }
}
