<!-- ── PAGE: Retraite ── -->
<div id="page-retraite" class="page">
  <div class="page-title">Retraite</div>
  <div class="page-subtitle">Planification de la retraite</div>

  <div style="display:flex;gap:20px;align-items:start">

    <!-- Colonne principale -->
    <div style="flex:1;min-width:0">

      <!-- Âge de retraite -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          Âge de retraite visé
        </div>
        <div class="card-body" style="padding:0">
          <!-- En-têtes colonnes -->
          <div style="display:grid;grid-template-columns:1fr 120px 60px 1fr;gap:8px;padding:8px 16px;background:#f8f9fd;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;border-bottom:1px solid var(--border)">
            <span>Personne</span>
            <span>Type</span>
            <span>Âge</span>
            <span>Date estimée</span>
          </div>
          <!-- Ligne client -->
          <div style="display:grid;grid-template-columns:1fr 120px 60px 1fr;gap:8px;padding:12px 16px;align-items:center;border-bottom:1px solid var(--border)">
            <span id="retraite-nom-client" style="font-weight:600;font-size:13px;color:var(--navy)">Client</span>
            <select class="form-select" id="retraite-type-client" style="font-size:12px;padding:5px 8px" onchange="retraiteToggleType('client');retraiteCalc()">
              <option value="age" selected>Âge</option>
              <option value="date">Date</option>
            </select>
            <div>
              <input class="form-input" id="retraite-age-client"   type="text"   value="65" style="text-align:center;padding:5px 4px;width:55px" oninput="retraiteCalc()"/>
              <input class="form-input" id="retraite-annee-client" type="number" min="2025" max="2090" placeholder="Année" style="text-align:center;padding:5px 4px;width:75px;display:none" oninput="retraiteCalc()"/>
            </div>
            <span id="retraite-date-client" style="font-size:12px;color:var(--muted)">—</span>
          </div>
          <!-- Ligne conjoint (masquée si seul) -->
          <div id="retraite-row-conjoint" style="display:none;grid-template-columns:1fr 120px 60px 1fr;gap:8px;padding:12px 16px;align-items:center">
            <span id="retraite-nom-conjoint" style="font-weight:600;font-size:13px;color:var(--gold)">Conjoint</span>
            <select class="form-select" id="retraite-type-conjoint" style="font-size:12px;padding:5px 8px" onchange="retraiteToggleType('conjoint');retraiteCalc()">
              <option value="age" selected>Âge</option>
              <option value="date">Date</option>
            </select>
            <div>
              <input class="form-input" id="retraite-age-conjoint"   type="text"   value="65" style="text-align:center;padding:5px 4px;width:55px" oninput="retraiteCalc()"/>
              <input class="form-input" id="retraite-annee-conjoint" type="number" min="2025" max="2090" placeholder="Année" style="text-align:center;padding:5px 4px;width:75px;display:none" oninput="retraiteCalc()"/>
            </div>
            <span id="retraite-date-conjoint" style="font-size:12px;color:var(--muted)">—</span>
          </div>
        </div>
      </div>

      <!-- Objectif de retraite -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
          <span>Objectif de retraite</span>
          <!-- Type d'objectif (couple seulement) -->
          <div id="retraite-goal-type-wrap" style="display:none;gap:4px" style="display:flex">
            <label class="fu-radio-pill" style="padding:4px 10px;font-size:11px"><input type="radio" name="retraite-goal-type" value="familial"   onchange="retraiteCalc()"/> Familial</label>
            <label class="fu-radio-pill" style="padding:4px 10px;font-size:11px"><input type="radio" name="retraite-goal-type" value="individuel" checked onchange="retraiteCalc()"/> Individuel</label>
            <label class="fu-radio-pill" style="padding:4px 10px;font-size:11px"><input type="radio" name="retraite-goal-type" value="aucun"      onchange="retraiteCalc()"/> Aucun</label>
          </div>
        </div>
        <div class="card-body">
          <!-- Onglets personne -->
          <div id="retraite-objectif-tabs" style="display:none;border-bottom:1px solid var(--border);margin:0 -20px 16px;padding:0 20px">
            <button class="deces-person-tab active" id="retraite-obj-tab-client"   onclick="switchRetraiteObjTab('client',this)">CLIENT</button>
            <button class="deces-person-tab"        id="retraite-obj-tab-conjoint" onclick="switchRetraiteObjTab('conjoint',this)">CONJOINT</button>
          </div>

          <!-- Panel client -->
          <div id="retraite-obj-panel-client">
            <div style="font-size:12px;color:var(--muted);margin-bottom:10px">
              Revenu annuel net actuel : <strong id="retraite-revenu-net-client">—</strong>
            </div>
            <div id="retraite-periodes-client">
              <!-- Période 1 -->
              <div class="retraite-periode" data-idx="0" style="padding:12px;background:#f8f9fd;border-radius:8px;margin-bottom:10px">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px">
                  <span id="retraite-nom-inline-client">Le client</span>
                  <span>vise</span>
                  <input class="form-input" id="retraite-pct-client-0" type="text" value="70" style="width:65px;text-align:center" oninput="retraiteCalc()"/>
                  <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="retraite-target-type-client-0" value="pct" checked onchange="retraiteCalc()"/> %</label>
                  <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="retraite-target-type-client-0" value="montant" onchange="retraiteCalc()"/> $</label>
                  <span>du revenu net actuel</span>
                </div>
                <div style="font-size:12px;color:var(--muted);margin-top:6px">
                  Ce qui correspond à <strong id="retraite-montant-client-0">—</strong>/an
                  — jusqu'au décès
                </div>
              </div>
            </div>
            <button class="btn btn-secondary btn-sm" style="margin-bottom:16px" onclick="retraiteAddPeriode('client')">
              <svg viewBox="0 0 26 24" width="13" height="13" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
              Ajouter une période
            </button>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" style="display:flex;align-items:center;gap:6px">
                Épargner automatiquement les revenus excédentaires?
                <span class="abf-tooltip-wrap">
                  <span class="abf-tooltip-icon">&#9432;</span>
                  <span class="abf-tooltip-box">Si les revenus dépassent les dépenses planifiées à la retraite, l'excédent sera automatiquement ajouté à l'épargne projetée.</span>
                </span>
              </label>
              <div style="display:flex;gap:8px;margin-top:6px">
                <label class="fu-radio-pill"><input type="radio" name="retraite-save-surplus-c" value="oui" onchange="retraiteCalc()"/> Oui</label>
                <label class="fu-radio-pill"><input type="radio" name="retraite-save-surplus-c" value="non" checked onchange="retraiteCalc()"/> Non</label>
              </div>
            </div>
          </div>

          <!-- Panel conjoint -->
          <div id="retraite-obj-panel-conjoint" style="display:none">
            <div style="font-size:12px;color:var(--muted);margin-bottom:10px">
              Revenu annuel net actuel : <strong id="retraite-revenu-net-conjoint">—</strong>
            </div>
            <div id="retraite-periodes-conjoint">
              <div class="retraite-periode" data-idx="0" style="padding:12px;background:#f8f9fd;border-radius:8px;margin-bottom:10px">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px">
                  <span id="retraite-nom-inline-conjoint">Le conjoint</span>
                  <span>vise</span>
                  <input class="form-input" id="retraite-pct-conjoint-0" type="text" value="70" style="width:65px;text-align:center" oninput="retraiteCalc()"/>
                  <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="retraite-target-type-conjoint-0" value="pct" checked onchange="retraiteCalc()"/> %</label>
                  <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="retraite-target-type-conjoint-0" value="montant" onchange="retraiteCalc()"/> $</label>
                  <span>du revenu net actuel</span>
                </div>
                <div style="font-size:12px;color:var(--muted);margin-top:6px">
                  Ce qui correspond à <strong id="retraite-montant-conjoint-0">—</strong>/an
                  — jusqu'au décès
                </div>
              </div>
            </div>
            <button class="btn btn-secondary btn-sm" style="margin-bottom:16px" onclick="retraiteAddPeriode('conjoint')">
              <svg viewBox="0 0 26 24" width="13" height="13" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
              Ajouter une période
            </button>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label">Épargner automatiquement les revenus excédentaires?</label>
              <div style="display:flex;gap:8px;margin-top:6px">
                <label class="fu-radio-pill"><input type="radio" name="retraite-save-surplus-j" value="oui" onchange="retraiteCalc()"/> Oui</label>
                <label class="fu-radio-pill"><input type="radio" name="retraite-save-surplus-j" value="non" checked onchange="retraiteCalc()"/> Non</label>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Dépenses à la retraite -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
          <span style="display:flex;align-items:center;gap:6px">
            Dépenses à la retraite
            <span class="abf-tooltip-wrap">
              <span class="abf-tooltip-icon">&#9432;</span>
              <span class="abf-tooltip-box">Dépenses supplémentaires prévues à la retraite, telles que voyages, activités, soins de santé, etc.</span>
            </span>
          </span>
          <button class="btn btn-primary btn-sm" onclick="retraiteAddDepense()">+ Ajouter</button>
        </div>
        <div class="card-body" id="retraite-depenses-list" style="padding:0">
          <p style="padding:14px;font-size:13px;color:var(--muted);margin:0" id="retraite-depenses-empty">Aucune dépense ajoutée.</p>
        </div>
      </div>

      <!-- Profil d'investisseur -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          <span style="display:flex;align-items:center;gap:6px">
            Profil d'investisseur
            <span class="abf-tooltip-wrap">
              <span class="abf-tooltip-icon">&#9432;</span>
              <span class="abf-tooltip-box">Les actifs sont projetés en utilisant le rendement moyen associé au profil sélectionné pour chaque période (avant et pendant la retraite).</span>
            </span>
          </span>
        </div>
        <div class="card-body">
          <!-- Onglets personne -->
          <div id="retraite-profil-tabs" style="display:none;border-bottom:1px solid var(--border);margin:0 -20px 16px;padding:0 20px">
            <button class="deces-person-tab active" onclick="switchRetraitProfileTab('client',this)">CLIENT</button>
            <button class="deces-person-tab"        onclick="switchRetraitProfileTab('conjoint',this)">CONJOINT</button>
          </div>

          <!-- Panel client -->
          <div id="retraite-profil-panel-client">
            <div class="row">
              <div class="col form-group">
                <label class="form-label" style="display:flex;align-items:center;gap:6px">
                  Avant la retraite
                  <span class="abf-tooltip-wrap">
                    <span class="abf-tooltip-icon">&#9432;</span>
                    <span class="abf-tooltip-box">Profil utilisé pour projeter vos placements jusqu'à l'âge de la retraite.</span>
                  </span>
                </label>
                <select class="form-select" id="retraite-profil-avant-client" onchange="retraiteCalc()">
                  <option value="prudent">Prudent (~2.5%)</option>
                  <option value="modere">Modéré (~3.5%)</option>
                  <option value="equilibre" selected>Équilibré (~4.5%)</option>
                  <option value="croissance">Croissance (~5.5%)</option>
                  <option value="audacieux">Audacieux (~6.5%)</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label" style="display:flex;align-items:center;gap:6px">
                  Pendant la retraite
                  <span class="abf-tooltip-wrap">
                    <span class="abf-tooltip-icon">&#9432;</span>
                    <span class="abf-tooltip-box">Profil utilisé pour projeter vos placements durant la phase de décaissement à la retraite.</span>
                  </span>
                </label>
                <select class="form-select" id="retraite-profil-pendant-client" onchange="retraiteCalc()">
                  <option value="prudent" selected>Prudent (~2.5%)</option>
                  <option value="modere">Modéré (~3.5%)</option>
                  <option value="equilibre">Équilibré (~4.5%)</option>
                  <option value="croissance">Croissance (~5.5%)</option>
                  <option value="audacieux">Audacieux (~6.5%)</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Panel conjoint -->
          <div id="retraite-profil-panel-conjoint" style="display:none">
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Avant la retraite</label>
                <select class="form-select" id="retraite-profil-avant-conjoint" onchange="retraiteCalc()">
                  <option value="prudent">Prudent (~2.5%)</option>
                  <option value="modere">Modéré (~3.5%)</option>
                  <option value="equilibre" selected>Équilibré (~4.5%)</option>
                  <option value="croissance">Croissance (~5.5%)</option>
                  <option value="audacieux">Audacieux (~6.5%)</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label">Pendant la retraite</label>
                <select class="form-select" id="retraite-profil-pendant-conjoint" onchange="retraiteCalc()">
                  <option value="prudent" selected>Prudent (~2.5%)</option>
                  <option value="modere">Modéré (~3.5%)</option>
                  <option value="equilibre">Équilibré (~4.5%)</option>
                  <option value="croissance">Croissance (~5.5%)</option>
                  <option value="audacieux">Audacieux (~6.5%)</option>
                </select>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div><!-- /col principale -->

    <!-- Résumé sidebar sticky -->
    <div style="width:300px;flex-shrink:0;position:sticky;top:80px">
      <div class="card">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          Résumé — Retraite
        </div>
        <div id="retraite-resume-body" style="padding:16px 14px;font-size:13px;color:var(--muted)">
          Complétez les informations pour voir le résumé.
        </div>
      </div>
    </div>

  </div>
</div><!-- /page-retraite -->

<!-- Modal: Dépense retraite -->
<div id="modal-retraite-depense" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
  <div style="background:white;border-radius:12px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Dépense à la retraite</h4>
      <button onclick="closeRetraiteDepenseModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
    </div>
    <div style="padding:20px 24px">
      <div class="form-group">
        <label class="form-label">Description</label>
        <input class="form-input" id="retraite-dep-desc" type="text" placeholder="ex. Voyages, activités…"/>
      </div>
      <div class="form-group">
        <label class="form-label">Montant</label>
        <div class="input-sfx"><input class="form-input" id="retraite-dep-montant" type="text" placeholder="0"/><span class="sfx">$/an</span></div>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Période</label>
        <div style="display:flex;gap:8px;align-items:center">
          <span style="font-size:13px;white-space:nowrap">De</span>
          <input class="form-input" id="retraite-dep-debut" type="text" placeholder="Âge" style="max-width:80px;text-align:center"/>
          <span style="font-size:13px">à</span>
          <input class="form-input" id="retraite-dep-fin" type="text" placeholder="Décès" style="max-width:80px;text-align:center"/>
          <span style="font-size:13px">ans</span>
        </div>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
      <button class="btn btn-secondary" onclick="closeRetraiteDepenseModal()">Annuler</button>
      <button class="btn btn-primary" onclick="saveRetraiteDepense()">Enregistrer</button>
    </div>
  </div>
</div>
