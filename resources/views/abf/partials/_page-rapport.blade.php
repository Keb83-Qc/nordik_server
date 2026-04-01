<!-- ── PAGE: Rapport ── -->
<div id="page-rapport" class="page">
  <div class="page-title">Rapport</div>
  <div class="page-subtitle">Configuration et génération du rapport PDF</div>

  <div style="display:flex;gap:20px;align-items:start">

    <!-- Colonne gauche: sections + couverture -->
    <div style="flex:1;min-width:0">

      <!-- Sections du rapport -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          Sections à inclure dans le rapport
        </div>
        <div class="card-body">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:0">

            <!-- Colonne 1 -->
            <div style="padding-right:16px;border-right:1px solid var(--border)">
              @foreach([
                ['id'=>'lifeInsurance',    'label'=>'Besoins en cas de décès',       'default'=>true,  'children'=>[]],
                ['id'=>'disability',       'label'=>'Besoins en cas d\'invalidité',  'default'=>true,  'children'=>[]],
                ['id'=>'seriousIllness',   'label'=>'Besoins en cas de maladie grave','default'=>true, 'children'=>[]],
                ['id'=>'emergencyFund',    'label'=>'Fonds d\'urgence',              'default'=>true,  'children'=>[]],
                ['id'=>'projects',         'label'=>'Projets',                       'default'=>false, 'children'=>[], 'disabled'=>true],
                ['id'=>'retirement',       'label'=>'Retraite',                      'default'=>true,  'children'=>[
                  ['id'=>'retirementAvailableAmounts','label'=>'Montants disponibles détaillés','default'=>true],
                  ['id'=>'retirementGraph',           'label'=>'Graphiques de projection',      'default'=>true],
                  ['id'=>'currentSituation',          'label'=>'Situation actuelle',            'default'=>true],
                ]],
              ] as $sec)
              <div style="padding:8px 0;{{ !empty($sec['children']) ? 'border-bottom:1px solid var(--border)' : '' }}">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px{{ isset($sec['disabled']) && $sec['disabled'] ? ';opacity:.45' : '' }}">
                  <input type="checkbox"
                         id="rapport-sec-{{ $sec['id'] }}"
                         {{ $sec['default'] ? 'checked' : '' }}
                         {{ isset($sec['disabled']) && $sec['disabled'] ? 'disabled' : '' }}
                         onchange="rapportToggleSection('{{ $sec['id'] }}')"
                         style="accent-color:var(--navy);width:15px;height:15px;flex-shrink:0"/>
                  <span style="font-weight:600">{{ $sec['label'] }}</span>
                  @if(isset($sec['disabled']) && $sec['disabled'])
                    <span style="font-size:10px;background:#f0f3fa;border-radius:4px;padding:1px 6px;color:var(--muted)">Aucun projet</span>
                  @endif
                </label>
                @if(!empty($sec['children']))
                <div id="rapport-sec-{{ $sec['id'] }}-children" style="margin-left:26px;margin-top:4px">
                  @foreach($sec['children'] as $child)
                  <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12px;padding:4px 0;color:var(--muted)">
                    <input type="checkbox"
                           id="rapport-sec-{{ $child['id'] }}"
                           {{ $child['default'] ? 'checked' : '' }}
                           onchange="rapportToggleSection('{{ $child['id'] }}')"
                           style="accent-color:var(--navy);width:14px;height:14px;flex-shrink:0"/>
                    {{ $child['label'] }}
                  </label>
                  @endforeach
                </div>
                @endif
              </div>
              @endforeach
            </div>

            <!-- Colonne 2 -->
            <div style="padding-left:16px">
              @foreach([
                ['id'=>'dashboard',                'label'=>'Tableau de bord',                 'default'=>true,  'children'=>[]],
                ['id'=>'financialPrioritiesPyramid','label'=>'Pyramide des priorités financières','default'=>false,'children'=>[]],
                ['id'=>'recommendations',          'label'=>'Recommandations',                 'default'=>true,  'children'=>[
                  ['id'=>'recoDeces',        'label'=>'Décès',             'default'=>true],
                  ['id'=>'recoInvalidite',   'label'=>'Invalidité',        'default'=>true],
                  ['id'=>'recoMaladieGrave', 'label'=>'Maladie grave',     'default'=>true],
                  ['id'=>'recoFondsUrgence', 'label'=>"Fonds d'urgence",   'default'=>true],
                  ['id'=>'recoRetraite',     'label'=>'Retraite',          'default'=>true],
                  ['id'=>'recoConseils',     'label'=>'Conseils généraux', 'default'=>true],
                ]],
                ['id'=>'deliveryConfirmation',     'label'=>'Confirmation de remise',           'default'=>false, 'tooltip'=>'Requis pour la remise par voie électronique ou en personne (conformité AMF).',  'children'=>[]],
                ['id'=>'annex',                    'label'=>'Tableaux détaillés (annexes)',     'default'=>false, 'children'=>[
                  ['id'=>'retirementIncome',        'label'=>'Revenus de retraite',     'default'=>false],
                  ['id'=>'investmentProjection',    'label'=>'Évolution des placements', 'default'=>false],
                ]],
              ] as $sec)
              <div style="padding:8px 0;{{ !empty($sec['children']) ? 'border-bottom:1px solid var(--border)' : '' }}">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
                  <input type="checkbox"
                         id="rapport-sec-{{ $sec['id'] }}"
                         {{ $sec['default'] ? 'checked' : '' }}
                         onchange="rapportToggleSection('{{ $sec['id'] }}')"
                         style="accent-color:var(--navy);width:15px;height:15px;flex-shrink:0"/>
                  <span style="font-weight:600;display:flex;align-items:center;gap:5px">
                    {{ $sec['label'] }}
                    @if(isset($sec['tooltip']))
                    <span class="abf-tooltip-wrap">
                      <span class="abf-tooltip-icon">&#9432;</span>
                      <span class="abf-tooltip-box">{{ $sec['tooltip'] }}</span>
                    </span>
                    @endif
                  </span>
                </label>
                @if(!empty($sec['children']))
                <div id="rapport-sec-{{ $sec['id'] }}-children" style="margin-left:26px;margin-top:4px">
                  @foreach($sec['children'] as $child)
                  <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12px;padding:4px 0;color:var(--muted)">
                    <input type="checkbox"
                           id="rapport-sec-{{ $child['id'] }}"
                           {{ $child['default'] ? 'checked' : '' }}
                           onchange="rapportToggleSection('{{ $child['id'] }}')"
                           style="accent-color:var(--navy);width:14px;height:14px;flex-shrink:0"/>
                    {{ $child['label'] }}
                  </label>
                  @endforeach
                </div>
                @endif
              </div>
              @endforeach
            </div>

          </div>
        </div>
      </div>

      <!-- Photo de couverture -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          Photo de couverture
        </div>
        <div class="card-body">
          <!-- Filtre -->
          <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px">
            @foreach(['Tous', 'Neutre', 'Couple avec enfants', 'Couple sans enfants', 'Homme seul', 'Femme seule'] as $i => $filter)
            <button
              class="toggle-btn{{ $i===0 ? ' active' : '' }}"
              onclick="rapportFilterPhotos('{{ $filter }}')"
              style="font-size:11px;padding:4px 10px">
              {{ $filter }}
            </button>
            @endforeach
          </div>
          <!-- Grille de photos -->
          <div id="rapport-photo-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:10px">
            @foreach([
              ['file'=>'couv-neutre-1.jpg',              'categorie'=>'Neutre'],
              ['file'=>'couv-neutre-2.jpg',              'categorie'=>'Neutre'],
              ['file'=>'couv-neutre-3.jpg',              'categorie'=>'Neutre'],
              ['file'=>'couv-couple-enfants-1.jpg',      'categorie'=>'Couple avec enfants'],
              ['file'=>'couv-couple-enfants-2.jpg',      'categorie'=>'Couple avec enfants'],
              ['file'=>'couv-couple-sans-enfants-1.jpg', 'categorie'=>'Couple sans enfants'],
              ['file'=>'couv-couple-sans-enfants-2.jpg', 'categorie'=>'Couple sans enfants'],
              ['file'=>'couv-homme-1.jpg',               'categorie'=>'Homme seul'],
              ['file'=>'couv-homme-2.jpg',               'categorie'=>'Homme seul'],
              ['file'=>'couv-femme-1.jpg',               'categorie'=>'Femme seule'],
              ['file'=>'couv-femme-2.jpg',               'categorie'=>'Femme seule'],
            ] as $idx => $photo)
            <div
              class="rapport-photo-item"
              data-categorie="{{ $photo['categorie'] }}"
              data-file="{{ $photo['file'] }}"
              onclick="rapportSelectPhoto(this)"
              style="position:relative;cursor:pointer;border-radius:8px;overflow:hidden;border:2px solid var(--border);aspect-ratio:4/3;background:#f0f3fa;display:flex;align-items:center;justify-content:center;transition:border-color .15s">
              <!-- Placeholder: remplacer par <img> quand les photos existent -->
              <div style="text-align:center;padding:8px;opacity:.4">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="var(--navy)"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                <div style="font-size:9px;color:var(--muted);margin-top:4px">{{ $photo['categorie'] }}</div>
              </div>
              <!-- Overlay "sélectionné" -->
              <div class="rapport-photo-check" style="display:none;position:absolute;inset:0;background:rgba(14,16,48,.5);align-items:center;justify-content:center">
                <svg viewBox="0 0 24 24" width="28" height="28" fill="white"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>

    </div><!-- /col gauche -->

    <!-- Colonne droite: aperçu + bouton -->
    <div style="width:260px;flex-shrink:0;position:sticky;top:80px">

      <!-- Aperçu du dossier -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Dossier</div>
        <div style="padding:14px 16px;font-size:12px">
          <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--muted)">Client</span>
            <strong id="rapport-client-nom" style="color:var(--navy)">—</strong>
          </div>
          <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--muted)">Conseiller</span>
            <span id="rapport-conseiller-nom">—</span>
          </div>
          <div style="display:flex;justify-content:space-between;padding:5px 0">
            <span style="color:var(--muted)">Date</span>
            <span>{{ now()->translatedFormat('j F Y') }}</span>
          </div>
        </div>
      </div>

      <!-- Statut sections -->
      <div class="card" style="margin-bottom:20px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Complétude</div>
        <div id="rapport-completude" style="padding:12px 16px;font-size:12px;color:var(--muted)">
          Calcul en cours…
        </div>
      </div>

      <!-- Bouton génération -->
      <button id="btn-rapport-generer" class="btn btn-gold" style="width:100%;padding:14px;font-size:14px;font-weight:700;border-radius:10px" onclick="rapportGenerer()">
        <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
        Générer le rapport PDF
      </button>
      <p style="font-size:11px;color:var(--muted);text-align:center;margin-top:8px">Le PDF s'ouvrira dans un nouvel onglet.</p>

    </div><!-- /col droite -->

  </div>
</div><!-- /page-rapport -->

  </main>
</div>

<!-- BOTTOM BAR -->
<div class="bottom-bar">
  <button class="btn btn-secondary" onclick="goPrev()">← Précédent</button>
  <button class="btn btn-primary" onclick="goNext()">Suivant →</button>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>
