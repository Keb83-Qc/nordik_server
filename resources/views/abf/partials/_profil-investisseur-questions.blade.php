@php
    // $role est 'client' ou 'conjoint'
    $r = $role;

    $questions = [
        // ── Horizon d'investissement ──────────────────────────────────────
    'horizon' => [
        'titre' => 'Horizon d\'investissement',
            'questions' => [
                [
                    'id' => "pi-{$r}-q1",
                    'label' => '1. Dans quel groupe d’âge vous situez-vous ?',
                    'options' => [
                        1 => 'Plus de 71 ans',
                        2 => 'Entre 65 et 70 ans',
                        5 => 'Entre 55 et 64 ans',
                        10 => 'Entre 41 et 54 ans',
                        20 => 'Entre 18 et 40 ans',
                    ],
                ],
                [
                    'id' => "pi-{$r}-q2",
                    'label' => '2. Dans combien de temps pensez-vous utiliser, en totalité ou en partie, cet argent ?',
                    'options' => [
                        1 => 'Moins de 1 an',
                        2 => 'De 1 à 3 ans',
                        5 => 'De 4 à 5 ans',
                        10 => 'De 6 à 9 ans',
                        20 => 'Dans plus de 10 ans',
                    ],
                ],
                [
                    'id' => "pi-{$r}-q3",
                    'label' =>
                        '3. Au cours des cinq prochaines années, comment prévoyez vous utiliser votre capital et vos rendements ?',
                    'options' => [
                        1 => 'Effectuer des retraits forfaitaires ou réguliers du capital (ex. RAP, retraite, décaissement planifié) ',
                        2 => 'Retirer la totalité des rendements et une partie du capital',
                        5 => 'Retirer uniquement les rendements, sans toucher au capital',
                        10 => 'Retirer une partie des rendements seulement',
                        20 => 'Réinvestir les rendements et accumuler sans effectuer de retraits',
                    ],
                ],
            ],
        ],

        // ── Situation financière ──────────────────────────────────────────
        'situation' => [
            'titre' => 'Situation financière',
            'questions' => [
                [
                    'id' => "pi-{$r}-q4",
                    'label' => '4. Quel est votre revenu annuel avant impôt ?',
                    'options' => [
                        1 => '25 000 $ et moins',
                        2 => '25 001 $ à 35 000 $',
                        5 => '35 001 $ à 50 000 $',
                        10 => '50 001 $ à 100 000 $',
                        20 => '100 001 $ et plus',
                    ],
                ],
                [
                    'id' => "pi-{$r}-q5",
                    'label' => '5. Quelle est votre valeur nette approximative (actifs moins passifs) ?*',
                    'note' =>
                        '* Si toute perte de capital est inacceptable, des solutions sans volatilité devraient être envisagées.',
                    'options' => [
                        1 => '25 000 $ et moins',
                        2 => '25 001 $ à 50 000 $',
                        5 => '50 001 $ à 100 000 $',
                        10 => '100 001 $ à 200 000 $',
                        20 => '200 001 $ et plus',
                    ],
                ],
            ],
        ],

        // ── Tolérance au risque ───────────────────────────────────────────
        'tolerance' => [
            'titre' => 'Tolérance au risque',
            'questions' => [
                [
                    'id' => "pi-{$r}-q6",
                    'label' => '6. Quel est votre seuil de tolérance au risque lorsque vous investissez votre argent ?',
                    'note' =>
                        '* Si vous ne pouvez tolérer aucune diminution de votre capital, envisagez les placements sans fluctuation à la baisse.',
                    'options' => [
                        1 => '<strong>Très faible</strong> — Je ne veux prendre aucun risque. Je veux surtout ne pas perdre d’argent, même temporairement.*',
                        2 => '<strong>Faible</strong> — Une baisse de mes placements me rend inconfortable, mais je peux accepter une petite baisse occasionnelle (jusqu’à environ 5 %).',
                        5 => '<strong>Modéré</strong> — Je peux accepter des baisses temporaires de 5 % à 10 % si cela peut améliorer mes rendements à long terme.',
                        10 => '<strong>Élevé</strong> — Les baisses à court terme de 10 % à 20 % ne m’inquiètent pas si mes placements peuvent mieux croître à long terme.',
                        20 => '<strong>Très élevé</strong> — Je vise une forte croissance à long terme et je suis à l’aise avec des baisses importantes à court terme de plus de 20 %.',
                    ],
                ],
                [
                    'id' => "pi-{$r}-q7",
                    'label' =>
                        '7. Si vous investissez 10 000 $ pour une durée d’un an, dans quelle fourchette de valeur seriez-vous à l’aise à la fin de l’année ?',
                    'note' =>
                        '* Si toute baisse de capital est inacceptable, privilégiez des placements sans volatilité.',
                    'options' => [
                        1 => '<strong>Aucun risque n\'est accepté</strong> - Je veux seulement des gains, même très modestes. (Entre 10 000 $ et 10 300 $) *',
                        2 => '<strong>Faible variation</strong> - Je peux accepter une petite baisse ou un gain modéré. (Entre 9 500 $ et 11 000 $)',
                        5 => '<strong>Variation modérée</strong> - Je suis à l’aise avec des baisses et des gains raisonnables. (Entre 9 000 $ et 11 500 $)',
                        10 => '<strong>Variation importante</strong> - J’accepte des baisses plus marquées si le potentiel de gain est plus élevé. (Entre 8 500 $ et 12 000 $)',
                        20 => '<strong>Variation élevée</strong> - Je suis très à l’aise avec des fluctuations importantes, à la hausse comme à la baisse. (Entre 8 000 $ et 12 500 $)',
                    ],
                ],
            ],
        ],

        // ── Connaissance des placements ───────────────────────────────────
        'connaissance' => [
            'titre' => 'Connaissance des placements',
            'questions' => [
                [
                    'id' => "pi-{$r}-q8",
                    'label' => '8. Comment évaluez-vous votre niveau de connaissance en matière de placements ?',
                    'options' => [
                        1 => '<strong>Très faible</strong> — Je débute et me familiarise progressivement avec les concepts de base.',
                        2 => '<strong>Faible</strong> — Je comprends que certains placements comportent plus de risques que d’autres.',
                        5 => '<strong>Modéré</strong> — Je connais les principales catégories de placements ainsi que les risques qui y sont associés.',
                        10 => '<strong>Avancé</strong> — Je maîtrise la relation entre le risque, le rendement et les fluctuations des différents types de placements',
                        20 => '<strong>Très avancé</strong> — Je possède une compréhension approfondie des marchés et en assure un suivi régulier et éclairé.',
                    ],
                ],
            ],
        ],
    ];
@endphp

@foreach ($questions as $section)
    <div class="pi-section-header">{{ $section['titre'] }}</div>
    <div class="pi-questions">
        @foreach ($section['questions'] as $q)
            <div class="pi-question-row">
                <div class="pi-question-label">{{ $q['label'] }}</div>
                @foreach ($q['options'] as $pts => $text)
                    <label class="pi-option">
                        <input type="radio" name="{{ $q['id'] }}" value="{{ $pts }}"
                            onchange="piCalcScore('{{ $r }}')">
                        <span class="pi-option-text">{!! $text !!}</span>
                        <span class="pi-option-pts">{{ $pts === 1 ? '1 point' : $pts . ' points' }}</span>
                    </label>
                @endforeach
                @if (!empty($q['note']))
                    <div class="pi-question-note">{{ $q['note'] }}</div>
                @endif
            </div>
        @endforeach
    </div>
@endforeach

{{-- Résultat déplacé dans la colonne droite de _page-profil-investisseur.blade.php --}}
