<!-- ── PAGE: Recommandations ── -->
<div id="page-recommandations" class="page">
  <div class="page-title">Recommandations</div>
  <div class="page-subtitle">Synthèse des besoins et recommandations par catégorie</div>

  <!-- Onglets catégories -->
  <div style="display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:20px;overflow-x:auto">
    @foreach([
      ['id'=>'deces',        'label'=>'Décès',        'icon'=>'heroicon-heart'],
      ['id'=>'invalidite',   'label'=>'Invalidité',   'icon'=>''],
      ['id'=>'maladie-grave','label'=>'Maladie grave','icon'=>''],
      ['id'=>'fonds-urgence','label'=>'Fonds urgence','icon'=>''],
      ['id'=>'retraite',     'label'=>'Retraite',     'icon'=>''],
      ['id'=>'conseils',     'label'=>'Conseils',     'icon'=>''],
    ] as $i => $cat)
    <button
      class="recom-tab{{ $i === 0 ? ' active' : '' }}"
      id="recom-tab-{{ $cat['id'] }}"
      onclick="switchRecomTab('{{ $cat['id'] }}',this)"
      style="padding:10px 14px;border:none;background:none;cursor:pointer;font-size:13px;font-weight:600;color:{{ $i===0 ? 'var(--navy)' : 'var(--muted)' }};border-bottom:{{ $i===0 ? '2px solid var(--navy)' : '2px solid transparent' }};margin-bottom:-2px;white-space:nowrap;display:flex;flex-direction:column;align-items:center;gap:2px;flex-shrink:0">
      {{ $cat['label'] }}
      <span id="recom-pct-{{ $cat['id'] }}" style="font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;background:#f0f3fa;color:var(--muted)">—</span>
    </button>
    @endforeach
  </div>

  <!-- Panels par catégorie -->
  @foreach(['deces','invalidite','maladie-grave','fonds-urgence','retraite','conseils'] as $i => $cat)
  <div id="recom-panel-{{ $cat }}" style="{{ $i > 0 ? 'display:none' : '' }}">

    @if($cat !== 'conseils')
    <!-- Sélecteur client / conjoint (si couple) -->
    <div id="recom-person-wrap-{{ $cat }}" style="display:none;margin-bottom:16px">
      <div style="display:flex;gap:6px">
        <button class="toggle-btn active" id="recom-person-btn-client-{{ $cat }}"   onclick="switchRecomPerson('{{ $cat }}','client',this)">Client</button>
        <button class="toggle-btn"        id="recom-person-btn-conjoint-{{ $cat }}" onclick="switchRecomPerson('{{ $cat }}','conjoint',this)">Conjoint</button>
      </div>
    </div>

    <!-- Barre de couverture -->
    <div class="card" style="margin-bottom:16px">
      <div class="card-body" style="padding:16px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
          <span style="font-size:13px;font-weight:700;color:var(--navy)">Taux de couverture</span>
          <strong id="recom-coverage-pct-{{ $cat }}" style="font-size:18px;font-weight:800;color:var(--navy)">—</strong>
        </div>
        <div style="height:10px;background:#e9ecef;border-radius:5px;overflow:hidden">
          <div id="recom-coverage-bar-{{ $cat }}" style="height:100%;background:linear-gradient(90deg,#22c55e,#16a34a);border-radius:5px;width:0%;transition:width .4s ease"></div>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:4px">
          <span style="font-size:11px;color:var(--muted)">0%</span>
          <span id="recom-coverage-status-{{ $cat }}" style="font-size:11px;font-weight:600;color:var(--muted)">—</span>
          <span style="font-size:11px;color:var(--muted)">100%+</span>
        </div>
      </div>
    </div>

    <!-- Synthèse -->
    <div style="display:flex;gap:16px;margin-bottom:16px">

      <!-- Besoins -->
      <div class="card" style="flex:1">
        <div class="card-header" style="font-weight:700;font-size:12px;padding:10px 14px;border-bottom:1px solid var(--border);color:var(--muted);text-transform:uppercase">Besoins</div>
        <div id="recom-besoins-{{ $cat }}" style="padding:10px 14px;font-size:13px">
          <div style="color:var(--muted);font-size:12px;text-align:center;padding:12px 0">—</div>
        </div>
      </div>

      <!-- Disponible -->
      <div class="card" style="flex:1">
        <div class="card-header" style="font-weight:700;font-size:12px;padding:10px 14px;border-bottom:1px solid var(--border);color:var(--muted);text-transform:uppercase">Montants disponibles</div>
        <div id="recom-disponible-{{ $cat }}" style="padding:10px 14px;font-size:13px">
          <div style="color:var(--muted);font-size:12px;text-align:center;padding:12px 0">—</div>
        </div>
      </div>

    </div>

    <!-- Manque à gagner -->
    <div id="recom-manque-wrap-{{ $cat }}" class="card" style="margin-bottom:16px">
      <div class="card-body" style="padding:14px 16px;display:flex;justify-content:space-between;align-items:center">
        <span style="font-size:13px;font-weight:700;color:var(--navy)">Manque à gagner</span>
        <strong id="recom-manque-{{ $cat }}" style="font-size:16px;font-weight:800;color:#ef4444">—</strong>
      </div>
    </div>
    @endif

    <!-- Notes / recommandations conseiller -->
    <div class="card" style="margin-bottom:16px">
      <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
        @if($cat === 'conseils') Conseils généraux @else Recommandations du conseiller @endif
        <span style="font-weight:400;color:var(--muted);font-size:12px;margin-left:4px">(facultatif — apparaîtra dans le rapport)</span>
      </div>
      <div class="card-body">
        <textarea
          class="form-input"
          id="recom-notes-{{ $cat }}"
          rows="5"
          style="resize:vertical;font-size:13px"
          placeholder="@if($cat==='conseils')Ajoutez ici des conseils généraux ou des observations pour votre client…@else Ajoutez ici vos recommandations spécifiques pour ce besoin…@endif"
          oninput="recomSaveNotes('{{ $cat }}')"></textarea>
      </div>
    </div>

  </div><!-- /recom-panel -->
  @endforeach

</div><!-- /page-recommandations -->
