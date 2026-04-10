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
            @php $photoExists = file_exists(public_path('assets/img/abf-covers/' . $photo['file'])); @endphp
            <div
              class="rapport-photo-item"
              data-categorie="{{ $photo['categorie'] }}"
              data-file="{{ $photo['file'] }}"
              onclick="rapportSelectPhoto(this)"
              style="position:relative;cursor:pointer;border-radius:8px;overflow:hidden;border:2px solid var(--border);aspect-ratio:4/3;background:#f0f3fa;display:flex;align-items:center;justify-content:center;transition:border-color .15s">
              @if($photoExists)
                <img src="{{ asset('assets/img/abf-covers/' . $photo['file']) }}" alt="{{ $photo['categorie'] }}"
                  style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block" loading="lazy">
              @else
                <div style="text-align:center;padding:8px;opacity:.4">
                  <svg viewBox="0 0 24 24" width="24" height="24" fill="var(--navy)"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                  <div style="font-size:9px;color:var(--muted);margin-top:4px">{{ $photo['categorie'] }}</div>
                </div>
              @endif
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
  <button id="btn-prev" class="btn btn-secondary" onclick="goPrev()">← Précédent</button>
  <button class="btn btn-primary" onclick="goNext()">Suivant →</button>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<!-- ═══ MODAL : Note de rencontre ═══════════════════════════════════════ -->
<div id="modal-meeting-note" style="display:none;position:fixed;inset:0;z-index:2000;background:rgba(14,16,48,.55);align-items:center;justify-content:center">
  <div style="background:white;border-radius:14px;width:100%;max-width:620px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.28);margin:20px">

    <!-- En-tête -->
    <div style="padding:20px 24px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:white;z-index:1">
      <div>
        <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Note de rencontre</h4>
        <p style="font-size:12px;color:var(--muted);margin:4px 0 0">Complétez les informations avant de terminer l'ABF.</p>
      </div>
      <button onclick="closeMeetingNoteModal()" style="background:none;border:none;font-size:22px;color:var(--muted);cursor:pointer;padding:0 4px;line-height:1">×</button>
    </div>

    <!-- ── ÉTAPE 1 : Formulaire ─────────────────────────────────────── -->
    <div id="meeting-form" style="padding:20px 24px">

      <!-- Type de rencontre -->
      <div class="form-group">
        <label class="form-label" style="font-size:13px;font-weight:700;color:var(--navy)">Type de rencontre</label>
        <div style="display:flex;gap:8px;margin-top:6px">
          <label class="fu-radio-pill">
            <input type="radio" name="meeting-type" value="virtuel" checked onchange="meetingTypeChange()"/> Virtuel
          </label>
          <label class="fu-radio-pill">
            <input type="radio" name="meeting-type" value="presentiel" onchange="meetingTypeChange()"/> Présentiel
          </label>
        </div>
      </div>

      <!-- Lieu (présentiel seulement) -->
      <div id="meeting-lieu-wrap" class="form-group" style="display:none">
        <label class="form-label">Lieu de la rencontre</label>
        <input class="form-input" id="meeting-lieu" type="text" placeholder="Ex : Bureaux Nordik, Montréal"/>
      </div>

      <!-- Personnes présentes -->
      <div class="form-group" style="margin-top:4px">
        <label class="form-label" style="font-size:13px;font-weight:700;color:var(--navy)">Personnes présentes</label>
        <div id="meeting-personnes-list" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;min-height:36px">
          <!-- Rempli par JS -->
        </div>

        <!-- Formulaire d'ajout -->
        <div id="meeting-add-form" style="display:none;margin-top:12px;background:#f8f9fd;border-radius:8px;padding:12px 14px">
          <div style="display:grid;grid-template-columns:160px 1fr;gap:10px;align-items:end">
            <div>
              <label class="form-label" style="font-size:11px">Rôle</label>
              <select class="form-select" id="meeting-add-role">
                <option value="Enfant">Enfant</option>
                <option value="Ami(e)">Ami(e)</option>
                <option value="Avocat(e)">Avocat(e)</option>
                <option value="Comptable">Comptable</option>
                <option value="Notaire">Notaire</option>
                <option value="Autre">Autre</option>
              </select>
            </div>
            <div>
              <label class="form-label" style="font-size:11px">Nom</label>
              <input class="form-input" id="meeting-add-nom" type="text" placeholder="Prénom Nom"
                onkeydown="if(event.key==='Enter'){event.preventDefault();confirmAddMeetingPerson();}"/>
            </div>
          </div>
          <div style="display:flex;gap:8px;margin-top:10px">
            <button class="btn btn-primary btn-sm" onclick="confirmAddMeetingPerson()">Ajouter</button>
            <button class="btn btn-secondary btn-sm" onclick="document.getElementById('meeting-add-form').style.display='none'">Annuler</button>
          </div>
        </div>

        <button class="btn btn-secondary btn-sm" style="margin-top:10px" onclick="document.getElementById('meeting-add-form').style.display='';document.getElementById('meeting-add-nom').focus()">
          <svg viewBox="0 0 26 24" width="13" height="13" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
          Ajouter une personne
        </button>
      </div>

      <!-- Note libre -->
      <div class="form-group" style="margin-top:4px">
        <label class="form-label" style="font-size:13px;font-weight:700;color:var(--navy)">
          Note libre <span style="font-weight:400;color:var(--muted)">(facultatif)</span>
        </label>
        <textarea class="form-input" id="meeting-note-libre" rows="3" style="resize:vertical;margin-top:6px"
          placeholder="Observations, points à suivre, engagements pris…"></textarea>
      </div>

      <!-- Actions -->
      <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:8px;padding-top:16px;border-top:1px solid var(--border)">
        <button class="btn btn-secondary" onclick="closeMeetingNoteModal()">Annuler</button>
        <button class="btn btn-primary" onclick="generateMeetingNote()">
          <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
          Créer la note et terminer
        </button>
      </div>
    </div><!-- /meeting-form -->

    <!-- ── ÉTAPE 2 : Note générée ───────────────────────────────────── -->
    <div id="meeting-result" style="display:none;padding:20px 24px">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
        <div style="width:32px;height:32px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <svg viewBox="0 0 24 24" fill="#16a34a" width="18" height="18"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        </div>
        <div>
          <div style="font-weight:700;font-size:14px;color:var(--navy)">Note créée avec succès</div>
          <div style="font-size:12px;color:var(--muted)">Sauvegardée dans le dossier ABF. Copiez-la pour la coller dans votre outil.</div>
        </div>
      </div>

      <!-- Texte de la note -->
      <div style="position:relative">
        <textarea id="meeting-note-output" rows="16" readonly
          style="width:100%;font-family:monospace;font-size:12px;line-height:1.6;background:#f8f9fd;border:1px solid var(--border);border-radius:8px;padding:14px;resize:none;color:var(--text)"></textarea>
        <button onclick="copyMeetingNote()" id="btn-copy-note"
          style="position:absolute;top:8px;right:8px;background:var(--navy);color:white;border:none;border-radius:6px;padding:5px 12px;font-size:11px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px">
          <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>
          Copier
        </button>
      </div>

      <div style="display:flex;justify-content:flex-end;margin-top:16px">
        <button class="btn btn-primary" onclick="terminerApresNote()">
          Fermer et terminer →
        </button>
      </div>
    </div><!-- /meeting-result -->

  </div>
</div>
