<?php

namespace App\Filament\Abf\Resources\AbfCaseResource\Steps;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;

final class InvestorProfileStep
{
    /** Point values per question key */
    private const QUESTIONS = [
        'q1' => [
            'label' => '1. Quel âge avez-vous?',
            'options' => [
                1  => 'Plus de 71 ans',
                2  => 'Entre 65 et 70 ans',
                5  => 'Entre 55 et 64 ans',
                10 => 'Entre 41 et 54 ans',
                20 => 'Entre 18 et 40 ans',
            ],
        ],
        'q2' => [
            'label' => '2. Quand prévoyez-vous commencer à faire des sorties de fonds d\'au moins 25 % de votre épargne?',
            'options' => [
                1  => 'Dans moins de 1 an',
                2  => 'Entre 1 et 3 ans',
                5  => 'Entre 4 et 5 ans',
                10 => 'Entre 6 et 9 ans',
                20 => 'Dans plus de 10 ans',
            ],
        ],
        'q3' => [
            'label' => '3. Au cours des 5 prochaines années, prévoyez-vous…',
            'options' => [
                1  => 'Faire des retraits de votre capital sur une base régulière (RAP, retraite, etc.)',
                2  => 'Retirer la totalité de votre rendement et une partie de votre capital',
                5  => 'Retirer tout votre rendement sans toucher à votre capital',
                10 => 'Retirer une partie de votre rendement seulement',
                20 => 'Accumuler des épargnes avec votre rendement (aucun retrait)',
            ],
        ],
        'q4' => [
            'label' => '4. Quel est votre revenu annuel brut (avant impôts)?',
            'options' => [
                1  => '25 000 $ et moins',
                2  => '25 001 $ à 35 000 $',
                5  => '35 001 $ à 50 000 $',
                10 => '50 001 $ à 100 000 $',
                20 => '100 001 $ et plus',
            ],
        ],
        'q5' => [
            'label' => '5. Quelle est votre valeur nette (actif moins passif)?',
            'options' => [
                1  => '25 000 $ et moins',
                2  => '25 001 $ à 50 000 $',
                5  => '50 001 $ à 100 000 $',
                10 => '100 001 $ à 200 000 $',
                20 => '200 001 $ et plus',
            ],
        ],
        'q6' => [
            'label' => '6. Indiquez votre niveau de tolérance au risque lorsque vous investissez votre argent.',
            'options' => [
                1  => 'Très faible — Je n\'aime pas l\'idée de risquer mon argent. Mon seul objectif est de conserver mes sommes en toute sécurité à l\'abri des hausses et des baisses de marché.',
                2  => 'Faible — Bien qu\'une baisse de la valeur de mes placements me dérange, je suis prêt à tolérer une baisse occasionnelle de 5 % maximum.',
                5  => 'Modéré — Je suis prêt à tolérer une baisse à court terme de 5 % à 10 % de la valeur de mes placements pour autant que je puisse compter sur un rendement à long terme plus élevé.',
                10 => 'Élevé — Je suis à l\'aise avec une baisse à court terme de 10 % à 20 % de la valeur de mes placements parce que je sais qu\'à long terme mon rendement me permettra de rattraper cette baisse.',
                20 => 'Très élevé — J\'ai bon espoir d\'obtenir une croissance à long terme. Une baisse à court terme (moins d\'un an) de 20 % de la valeur de mes placements ne m\'inquiète pas.',
            ],
        ],
        'q7' => [
            'label' => '7. Vous avez la possibilité de faire un placement de 10 000 $ pendant un an. Dans quelle fourchette accepteriez-vous que la valeur finale de votre placement initial se situe?',
            'options' => [
                1  => 'Gains uniquement : entre 10 000 $ et 10 300 $',
                2  => 'Entre 9 500 $ et 11 000 $',
                5  => 'Entre 9 000 $ et 11 500 $',
                10 => 'Entre 8 500 $ et 12 000 $',
                20 => 'Entre 8 000 $ et 12 500 $',
            ],
        ],
        'q8' => [
            'label' => '8. Quel est votre niveau de connaissance des placements?',
            'options' => [
                1  => 'Très faible — Je commence à me familiariser avec les placements.',
                2  => 'Faible — Je sais que certains placements sont plus risqués que d\'autres.',
                5  => 'Modéré — Je connais les différents types de placements et les risques qui s\'y rattachent.',
                10 => 'Avancé — Je comprends les niveaux de risque et de rendement rattachés à chacun des types de placements et leurs fluctuations dans le temps.',
                20 => 'Très avancé — Je surveille assidûment les marchés et j\'en ai une connaissance approfondie.',
            ],
        ],
    ];

    public static function make(): Step
    {
        return Step::make('Profil investisseur')
            ->id('profil-investisseur')
            ->schema([
                // ── Horizon d'investissement ──────────────────────────────
                Section::make('Horizon d\'investissement')
                    ->schema([
                        Grid::make(['default' => 1, 'xl' => 2])->schema([
                            self::radioField('q1'),
                            self::radioField('q2'),
                        ]),
                        self::radioField('q3'),
                    ]),

                // ── Situation financière ───────────────────────────────────
                Section::make('Situation financière')
                    ->schema([
                        Grid::make(['default' => 1, 'xl' => 2])->schema([
                            self::radioField('q4'),
                            self::radioField('q5'),
                        ]),
                    ]),

                // ── Tolérance au risque ────────────────────────────────────
                Section::make('Tolérance au risque')
                    ->schema([
                        self::radioField('q6'),
                        self::radioField('q7'),
                    ]),

                // ── Connaissance des placements ────────────────────────────
                Section::make('Connaissance des placements')
                    ->schema([
                        self::radioField('q8'),
                    ]),

                // ── Score et profil ────────────────────────────────────────
                Section::make('Résultat du profil')
                    ->description('Le profil est calculé automatiquement à partir du score total des 8 questions.')
                    ->columns(3)
                    ->schema([
                        Placeholder::make('_score_total')
                            ->label('Score total')
                            ->content(fn(Get $get): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                                '<span style="font-size:1.5rem;font-weight:700;color:#1d4ed8;">'
                                . self::totalScore($get) . ' <span style="font-size:1rem;font-weight:400;color:#6b7280;">/ 160 pts</span></span>'
                            )),

                        Placeholder::make('_profil_label')
                            ->label('Profil d\'investisseur')
                            ->content(fn(Get $get): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                                '<span style="font-size:1.25rem;font-weight:600;">'
                                . self::profileLabel(self::totalScore($get)) . '</span>'
                            )),

                        Placeholder::make('_profil_grille')
                            ->label('Grille de référence')
                            ->content(new \Illuminate\Support\HtmlString(
                                '<ul style="margin:0;padding:0;list-style:none;font-size:0.8rem;line-height:1.6;">'
                                . '<li>🛡️ <strong>Prudent</strong> — 8 à 25 pts</li>'
                                . '<li>⚖️ <strong>Modéré</strong> — 26 à 55 pts</li>'
                                . '<li>🔄 <strong>Équilibré</strong> — 56 à 90 pts</li>'
                                . '<li>📈 <strong>Croissance</strong> — 91 à 120 pts</li>'
                                . '<li>🚀 <strong>Audacieux</strong> — 121 à 160 pts</li>'
                                . '</ul>'
                            )),
                    ]),
            ]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private static function radioField(string $key): Radio
    {
        $def = self::QUESTIONS[$key];

        // Build options array with point labels appended
        $opts = [];
        foreach ($def['options'] as $pts => $label) {
            $ptLabel = $pts === 1 ? '1 point' : "{$pts} points";
            $opts[(string) $pts] = "{$label}  ({$ptLabel})";
        }

        return Radio::make("payload.investor_profile.{$key}")
            ->label($def['label'])
            ->options($opts)
            ->live()
            ->required();
    }

    private static function totalScore(Get $get): int
    {
        $total = 0;
        foreach (array_keys(self::QUESTIONS) as $key) {
            $total += (int) ($get("payload.investor_profile.{$key}") ?? 0);
        }
        return $total;
    }

    /**
     * Seuils de score sur 160 points — 5 profils d'investisseur.
     * Prudent    :   8 –  25 pts
     * Modéré     :  26 –  55 pts
     * Équilibré  :  56 –  90 pts
     * Croissance :  91 – 120 pts
     * Audacieux  : 121 – 160 pts
     */
    public static function profileLabel(int $score): string
    {
        return match (true) {
            $score <= 25  => '🛡️ Prudent',
            $score <= 55  => '⚖️ Modéré',
            $score <= 90  => '🔄 Équilibré',
            $score <= 120 => '📈 Croissance',
            default       => '🚀 Audacieux',
        };
    }

    /**
     * Clé machine du profil (pour stockage dans payload).
     */
    public static function profileKey(int $score): string
    {
        return match (true) {
            $score <= 25  => 'prudent',
            $score <= 55  => 'modere',
            $score <= 90  => 'equilibre',
            $score <= 120 => 'croissance',
            default       => 'audacieux',
        };
    }
}
