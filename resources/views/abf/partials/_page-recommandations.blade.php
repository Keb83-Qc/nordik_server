<!-- ── PAGE: Recommandations ── -->
@php
$recomCats = [
  ['id'=>'deces',        'label'=>'Décès',        'hasTime'=>true,  'options'=>[
    ['key'=>'personalized',             'label'=>'Recommandation personnalisée'],
    ['key'=>'temporaryLifeInsurance',   'label'=>'Souscrire une assurance vie temporaire'],
    ['key'=>'permanentLifeInsurance',   'label'=>'Souscrire une assurance vie permanente'],
    ['key'=>'mortgageInsurance',        'label'=>"Réviser l'assurance prêt hypothécaire"],
    ['key'=>'childrenLifeInsurance',    'label'=>'Souscrire une assurance vie pour enfants'],
    ['key'=>'reviewExistingContracts',  'label'=>"Réviser les contrats d'assurance existants"],
    ['key'=>'acceleratedPayments',      'label'=>"Prévoir des paiements d'assurance accélérés"],
  ]],
  ['id'=>'invalidite',   'label'=>'Invalidité',   'hasTime'=>false, 'options'=>[
    ['key'=>'personalized',             'label'=>'Recommandation personnalisée'],
    ['key'=>'disabilityInsurance',      'label'=>"Souscrire une assurance invalidité"],
    ['key'=>'reviewCollective',         'label'=>"Réviser la couverture collective"],
    ['key'=>'supplemental',             'label'=>"Ajouter une protection complémentaire"],
  ]],
  ['id'=>'maladie-grave','label'=>'Maladie grave','hasTime'=>false, 'options'=>[
    ['key'=>'personalized',             'label'=>'Recommandation personnalisée'],
    ['key'=>'criticalIllness',          'label'=>"Souscrire une assurance maladie grave"],
    ['key'=>'returnOfPremium',          'label'=>"Ajouter le remboursement de primes"],
  ]],
  ['id'=>'fonds-urgence','label'=>'Fonds urgence','hasTime'=>false, 'options'=>[
    ['key'=>'personalized',             'label'=>'Recommandation personnalisée'],
    ['key'=>'buildFund',                'label'=>"Constituer un fonds d'urgence"],
    ['key'=>'highInterestSavings',      'label'=>"Compte épargne à intérêt élevé"],
  ]],
  ['id'=>'retraite',     'label'=>'Retraite',     'hasTime'=>false, 'options'=>[
    ['key'=>'personalized',             'label'=>'Recommandation personnalisée'],
    ['key'=>'reer',                     'label'=>"Cotiser au REER"],
    ['key'=>'celi',                     'label'=>"Cotiser au CELI"],
    ['key'=>'rrq',                      'label'=>"Optimiser la rente RRQ/RPC"],
  ]],
  ['id'=>'conseils',     'label'=>'Conseils',     'hasTime'=>false, 'options'=>[
    ['key'=>'personalized',             'label'=>'Recommandation personnalisée'],
    ['key'=>'estateReview',             'label'=>"Révision du plan successoral"],
    ['key'=>'taxPlanning',              'label'=>"Planification fiscale"],
  ]],
];
@endphp

<div id="page-recommandations" class="page">
  <div class="page-title">Recommandations</div>

  <!-- Onglets catégories -->
  <div style="display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:20px;overflow-x:auto">
    @foreach($recomCats as $i => $cat)
    <button
      class="recom-tab{{ $i === 0 ? ' active' : '' }}"
      id="recom-tab-{{ $cat['id'] }}"
      onclick="switchRecomTab('{{ $cat['id'] }}',this)"
      style="padding:10px 16px;border:none;background:none;cursor:pointer;border-bottom:{{ $i===0 ? '2px solid var(--navy)' : '2px solid transparent' }};margin-bottom:-2px;flex-shrink:0;text-align:center;transition:all .15s">
      <div style="font-size:13px;font-weight:600;color:{{ $i===0 ? 'var(--navy)' : 'var(--muted)' }};margin-bottom:4px">{{ $cat['label'] }}</div>
      <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap">
        <span id="recom-pct-c-{{ $cat['id'] }}" style="font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;background:#f0f3fa;color:var(--muted)">—</span>
        <span id="recom-pct-j-{{ $cat['id'] }}" style="font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;background:#f0f3fa;color:var(--muted);display:none">—</span>
      </div>
    </button>
    @endforeach
  </div>

  <!-- Panels par catégorie -->
  @foreach($recomCats as $i => $cat)
  <div id="recom-panel-{{ $cat['id'] }}" style="{{ $i > 0 ? 'display:none' : '' }}">

    @if($cat['id'] !== 'conseils')
    <!-- Contrôles : personne + horizon -->
    <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;align-items:center">
      <div id="recom-person-wrap-{{ $cat['id'] }}" style="display:none">
        <div style="display:flex;gap:6px">
          <label class="fu-radio-pill">
            <input type="radio" name="recom-person-{{ $cat['id'] }}" value="client" checked onchange="switchRecomPerson('{{ $cat['id'] }}','client',this)"/>
            <span id="recom-person-label-c-{{ $cat['id'] }}">Client</span>
          </label>
          <label class="fu-radio-pill">
            <input type="radio" name="recom-person-{{ $cat['id'] }}" value="conjoint" onchange="switchRecomPerson('{{ $cat['id'] }}','conjoint',this)"/>
            <span id="recom-person-label-j-{{ $cat['id'] }}">Conjoint(e)</span>
          </label>
        </div>
      </div>
      @if($cat['hasTime'])
      <div style="display:flex;gap:6px">
        <label class="fu-radio-pill"><input type="radio" name="recom-timeframe-{{ $cat['id'] }}" value="today" checked onchange="switchRecomTab('{{ $cat['id'] }}',document.getElementById('recom-tab-{{ $cat['id'] }}'))"/> Aujourd'hui</label>
        <label class="fu-radio-pill"><input type="radio" name="recom-timeframe-{{ $cat['id'] }}" value="lifetime"/> À l'espérance de vie</label>
      </div>
      @endif
    </div>

    <!-- 2 colonnes : résumé | recommandations -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

      <!-- Colonne gauche : situation actuelle -->
      <div>
        <!-- Barre de couverture -->
        <div class="card" style="margin-bottom:12px">
          <div class="card-body" style="padding:14px 16px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
              <span style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em">Situation actuelle</span>
              <strong id="recom-coverage-pct-{{ $cat['id'] }}" style="font-size:16px;font-weight:800;color:var(--navy)">—</strong>
            </div>
            <div style="height:8px;background:#e9ecef;border-radius:4px;overflow:hidden">
              <div id="recom-coverage-bar-{{ $cat['id'] }}" style="height:100%;background:var(--navy);border-radius:4px;width:0%;transition:width .4s ease"></div>
            </div>
          </div>
        </div>
        <!-- Besoins -->
        <div class="card" style="margin-bottom:12px">
          <div class="card-header" style="font-weight:700;font-size:11px;padding:8px 14px;border-bottom:1px solid var(--border);color:var(--muted);text-transform:uppercase;letter-spacing:.04em">Besoins</div>
          <div id="recom-besoins-{{ $cat['id'] }}" style="padding:10px 14px;font-size:13px">
            <div style="color:var(--muted);font-size:12px;text-align:center;padding:8px 0">—</div>
          </div>
        </div>
        <!-- Montants disponibles -->
        <div class="card" style="margin-bottom:12px">
          <div class="card-header" style="font-weight:700;font-size:11px;padding:8px 14px;border-bottom:1px solid var(--border);color:var(--muted);text-transform:uppercase;letter-spacing:.04em">Montants disponibles</div>
          <div id="recom-disponible-{{ $cat['id'] }}" style="padding:10px 14px;font-size:13px">
            <div style="color:var(--muted);font-size:12px;text-align:center;padding:8px 0">—</div>
          </div>
        </div>
        <!-- Manque à gagner -->
        <div class="card">
          <div class="card-body" style="padding:12px 16px;display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:13px;font-weight:700;color:var(--navy)">Manque à gagner</span>
            <strong id="recom-manque-{{ $cat['id'] }}" style="font-size:16px;font-weight:800;color:#ef4444">—</strong>
          </div>
        </div>
      </div>

      <!-- Colonne droite : recommandations -->
      <div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <h6 style="margin:0;font-size:14px;font-weight:700;color:var(--navy)">Recommandations</h6>
          <!-- Dropdown Ajouter -->
          <div class="recom-add-dropdown" id="recom-add-wrap-{{ $cat['id'] }}" style="position:relative">
            <button class="btn btn-primary btn-sm" onclick="recomToggleMenu('{{ $cat['id'] }}',event)">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 24" width="13" height="13" style="vertical-align:middle;margin-right:4px;fill:currentColor"><path d="M12 0q-2.484 0-4.676 0.938t-3.82 2.566-2.566 3.82-0.938 4.676 0.938 4.676 2.566 3.82 3.82 2.566 4.676 0.938 4.676-0.938 3.82-2.566 2.566-3.82 0.938-4.676-0.938-4.676-2.566-3.82-3.82-2.566-4.676-0.938zM18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
              Ajouter
            </button>
            <div id="recom-add-menu-{{ $cat['id'] }}" style="display:none;position:absolute;right:0;top:calc(100% + 4px);background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);z-index:200;min-width:280px;overflow:hidden">
              @foreach($cat['options'] as $opt)
              <button onclick="recomAddItem('{{ $cat['id'] }}','{{ $opt['key'] }}');recomCloseMenu('{{ $cat['id'] }}')" style="display:block;width:100%;text-align:left;padding:10px 14px;border:none;background:none;cursor:pointer;font-size:13px;color:var(--text)" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='none'">{{ $opt['label'] }}</button>
              @endforeach
            </div>
          </div>
        </div>
        <!-- Liste d'items -->
        <div id="recom-items-{{ $cat['id'] }}">
          <div style="color:var(--muted);font-size:12px;text-align:center;padding:20px 0">Aucune recommandation. Cliquez Ajouter pour commencer.</div>
        </div>
      </div>

    </div><!-- /grid -->
    @else
    <!-- Conseils : pleine largeur -->
    <div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <h6 style="margin:0;font-size:14px;font-weight:700;color:var(--navy)">Conseils généraux</h6>
        <div class="recom-add-dropdown" id="recom-add-wrap-{{ $cat['id'] }}" style="position:relative">
          <button class="btn btn-primary btn-sm" onclick="recomToggleMenu('{{ $cat['id'] }}',event)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 24" width="13" height="13" style="vertical-align:middle;margin-right:4px;fill:currentColor"><path d="M12 0q-2.484 0-4.676 0.938t-3.82 2.566-2.566 3.82-0.938 4.676 0.938 4.676 2.566 3.82 3.82 2.566 4.676 0.938 4.676-0.938 3.82-2.566 2.566-3.82 0.938-4.676-0.938-4.676-2.566-3.82-3.82-2.566-4.676-0.938zM18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
            Ajouter
          </button>
          <div id="recom-add-menu-{{ $cat['id'] }}" style="display:none;position:absolute;right:0;top:calc(100% + 4px);background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);z-index:200;min-width:280px;overflow:hidden">
            @foreach($cat['options'] as $opt)
            <button onclick="recomAddItem('{{ $cat['id'] }}','{{ $opt['key'] }}');recomCloseMenu('{{ $cat['id'] }}')" style="display:block;width:100%;text-align:left;padding:10px 14px;border:none;background:none;cursor:pointer;font-size:13px;color:var(--text)" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='none'">{{ $opt['label'] }}</button>
            @endforeach
          </div>
        </div>
      </div>
      <div id="recom-items-{{ $cat['id'] }}">
        <div style="color:var(--muted);font-size:12px;text-align:center;padding:20px 0">Aucun conseil. Cliquez Ajouter pour commencer.</div>
      </div>
    </div>
    @endif

  </div><!-- /recom-panel -->
  @endforeach

</div><!-- /page-recommandations -->
