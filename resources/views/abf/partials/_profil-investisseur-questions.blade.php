@php
  // $role est 'client' ou 'conjoint'
  $r = $role;

  $questions = [
    // ── Horizon d'investissement ──────────────────────────────────────
    'horizon' => [
      'titre' => 'Horizon d\'investissement',
      'questions' => [
        [
          'id'    => "pi-{$r}-q1",
          'label' => '1. Quel âge avez-vous?',
          'options' => [
            1  => 'Plus de 71 ans',
            2  => 'Entre 65 et 70 ans',
            5  => 'Entre 55 et 64 ans',
            10 => 'Entre 41 et 54 ans',
            20 => 'Entre 18 et 40 ans',
          ],
        ],
        [
          'id'    => "pi-{$r}-q2",
          'label' => '2. Quand prévoyez-vous commencer à faire des sorties de fonds d\'au moins 25 % de votre épargne?',
          'options' => [
            1  => 'Dans moins de 1 an',
            2  => 'Entre 1 et 3 ans',
            5  => 'Entre 4 et 5 ans',
            10 => 'Entre 6 et 9 ans',
            20 => 'Dans plus de 10 ans',
          ],
        ],
        [
          'id'    => "pi-{$r}-q3",
          'label' => '3. Au cours des 5 prochaines années, prévoyez-vous :',
          'options' => [
            1  => 'Faire des retraits de votre capital sur une base régulière (RAP, retraite, etc.)',
            2  => 'Retirer la totalité de votre rendement et une partie de votre capital',
            5  => 'Retirer tout votre rendement sans toucher à votre capital',
            10 => 'Retirer une partie de votre rendement seulement',
            20 => 'Accumuler des épargnes avec votre rendement (aucun retrait)',
          ],
        ],
      ],
    ],

    // ── Situation financière ──────────────────────────────────────────
    'situation' => [
      'titre' => 'Situation financière',
      'questions' => [
        [
          'id'    => "pi-{$r}-q4",
          'label' => '4. Quel est votre revenu annuel brut (avant impôts)?',
          'options' => [
            1  => '25 000 $ et moins',
            2  => '25 001 $ à 35 000 $',
            5  => '35 001 $ à 50 000 $',
            10 => '50 001 $ à 100 000 $',
            20 => '100 001 $ et plus',
          ],
        ],
        [
          'id'    => "pi-{$r}-q5",
          'label' => '5. Quelle est votre valeur nette (actif moins passif)?',
          'options' => [
            1  => '25 000 $ et moins',
            2  => '25 001 $ à 50 000 $',
            5  => '50 001 $ à 100 000 $',
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
          'id'    => "pi-{$r}-q6",
          'label' => '6. Indiquez votre niveau de tolérance au risque lorsque vous investissez votre argent.',
          'note'  => '* Si vous ne pouvez tolérer aucune diminution de votre capital, envisagez les placements sans fluctuation à la baisse.',
          'options' => [
            1  => '<strong>Très faible</strong> — Je n\'aime pas l\'idée de risquer mon argent. Mon seul objectif est de conserver les sommes que j\'ai investies en toute sécurité à l\'abri des hausses et des baisses de marché.*',
            2  => '<strong>Faible</strong> — Bien qu\'une baisse de la valeur de mes placements me dérange, je suis prêt à tolérer une baisse occasionnelle de 5 % maximum, sachant qu\'à long terme le rendement de mes placements sera plus élevé.',
            5  => '<strong>Modéré</strong> — Je suis prêt à tolérer une baisse à court terme de 5 % à 10 % de la valeur de mes placements pour autant que je puisse compter sur un rendement à long terme plus élevé.',
            10 => '<strong>Élevé</strong> — Je suis à l\'aise avec une baisse à court terme de 10 % à 20 % de la valeur de mes placements parce que je sais qu\'à long terme mon rendement me permettra de rattraper cette baisse et d\'obtenir un rendement élevé.',
            20 => '<strong>Très élevé</strong> — J\'ai bon espoir d\'obtenir une croissance à long terme. Une baisse à court terme (moins d\'un an) de 20 % de la valeur de mes placements ne m\'inquiète pas.',
          ],
        ],
        [
          'id'    => "pi-{$r}-q7",
          'label' => '7. Vous avez la possibilité de faire un placement de 10 000 $ pendant un an. Dans quelle fourchette accepteriez-vous que la valeur finale de votre placement initial se situe?',
          'note'  => '* Si vous ne pouvez tolérer aucune diminution de votre capital, envisagez les placements sans fluctuation à la baisse.',
          'options' => [
            1  => 'Gains uniquement : entre 10 000 $ et 10 300 $*',
            2  => 'Entre 9 500 $ et 11 000 $',
            5  => 'Entre 9 000 $ et 11 500 $',
            10 => 'Entre 8 500 $ et 12 000 $',
            20 => 'Entre 8 000 $ et 12 500 $',
          ],
        ],
      ],
    ],

    // ── Connaissance des placements ───────────────────────────────────
    'connaissance' => [
      'titre' => 'Connaissance des placements',
      'questions' => [
        [
          'id'    => "pi-{$r}-q8",
          'label' => '8. Quel est votre niveau de connaissance des placements?',
          'options' => [
            1  => '<strong>Très faible</strong> — Je commence à me familiariser avec les placements.',
            2  => '<strong>Faible</strong> — Je sais que certains placements sont plus risqués que d\'autres.',
            5  => '<strong>Modéré</strong> — Je connais les différents types de placements et les risques qui s\'y rattachent (ex. les actions sont plus risquées que les obligations).',
            10 => '<strong>Avancé</strong> — Je comprends les niveaux de risque et de rendement rattachés à chacun des types de placements et leurs fluctuations dans le temps.',
            20 => '<strong>Très avancé</strong> — Je surveille assidûment les marchés (les actions, les obligations, les fonds, l\'immobilier, etc.) et j\'en ai une connaissance approfondie.',
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
            <input type="radio"
                   name="{{ $q['id'] }}"
                   value="{{ $pts }}"
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
