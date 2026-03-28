@php
  $_inv        = $abfParams['invalidite'] ?? [];
  // DB stocke 'incomeReplacement'/'expensesCoverage' → on mappe vers les valeurs du formulaire
  $_invType    = ['incomeReplacement' => 'remplacement', 'expensesCoverage' => 'depenses'][$_inv['type'] ?? 'incomeReplacement'] ?? 'remplacement';
  $_invBrutNet = $_inv['salaire_type'] ?? 'gross';
  $_invCk      = fn($v, $f) => $f === $v ? 'checked' : '';
  $_invBtnActive = fn($v) => ($_invBrutNet === $v) ? 'active' : '';
@endphp
    <div id="page-invalidite" class="page">
      <div class="page-title">Invalidité</div>
      <div class="page-subtitle">Analyse des besoins en cas d'invalidité</div>
      <div style="display:flex;gap:20px;align-items:start">

        <!-- Colonne gauche -->
        <div style="flex:1;min-width:0">

          <!-- Assurance invalidité -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
              Assurance invalidité
              <button class="btn btn-primary btn-sm" onclick="openInvalAvModal()">+ Ajouter</button>
            </div>
            <div class="card-body" id="inval-av-list" style="padding:0">
              <p style="padding:14px;font-size:13px;color:var(--muted);margin:0">Aucune assurance invalidité enregistrée.</p>
            </div>
          </div>

          <!-- Autres sources de revenu -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Autres sources de revenu</div>
            <div class="card-body">
              <div id="inval-autres-revenus-rows"></div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Êtes-vous couvert par l'assurance-emploi?</label>
                <div style="display:flex;gap:8px;margin-top:4px">
                  <label class="fu-radio-pill"><input type="radio" name="inval-ae" value="oui" onchange="invaliditeCalc()"/> Oui</label>
                  <label class="fu-radio-pill"><input type="radio" name="inval-ae" value="non" checked onchange="invaliditeCalc()"/> Non</label>
                </div>
              </div>
            </div>
          </div>

          <!-- Approche de calcul -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Approche de calcul</div>
            <div class="card-body">
              <div style="display:flex;gap:8px;flex-wrap:wrap">
                <label class="fu-radio-pill"><input type="radio" name="inval-approche" value="remplacement" {{ $_invCk('remplacement', $_invType) }} onchange="invaliditeApproche()"/> Remplacement du revenu</label>
                <label class="fu-radio-pill"><input type="radio" name="inval-approche" value="depenses" {{ $_invCk('depenses', $_invType) }} onchange="invaliditeApproche()"/> Dépenses courantes</label>
              </div>
            </div>
          </div>

          <!-- Remplacement du revenu -->
          <div id="inval-rr-section" class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
              <span>Remplacement du revenu en cas d'invalidité</span>
              <div style="display:flex;gap:2px">
                <button id="inval-bn-brut" class="toggle-btn {{ $_invBtnActive('gross') }}" onclick="setInvalBrutNet('brut')">Brut</button>
                <button id="inval-bn-net" class="toggle-btn {{ $_invBtnActive('net') }}" onclick="setInvalBrutNet('net')">Net</button>
              </div>
            </div>
            <div class="card-body" id="inval-rr-body"></div>
          </div>

          <!-- Dépenses courantes -->
          <div id="inval-dep-section" class="card" style="margin-bottom:16px;display:none">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Dépenses courantes mensuelles</div>
            <div class="card-body">
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Total des dépenses mensuelles</label>
                <div class="input-sfx" style="max-width:200px"><input class="form-input" id="inval-dep-total" type="text" placeholder="0" oninput="invaliditeCalc()"/><span class="sfx">$/mois</span></div>
              </div>
            </div>
          </div>

          <!-- Informations supplémentaires -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);cursor:pointer;display:flex;align-items:center;justify-content:space-between" onclick="toggleInvalInfo()">
              <span>Informations supplémentaires <span style="color:var(--muted);font-weight:400;font-size:12px">(facultatif)</span></span>
              <span id="inval-info-chevron" style="font-size:16px;color:var(--muted);transition:transform .2s">▼</span>
            </div>
            <div class="card-body" id="inval-info-body" style="display:none">
              <div class="form-group">
                <label class="form-label">Niveau de travail</label>
                <div style="display:flex;align-items:center;gap:12px;margin-top:4px">
                  <span style="font-size:12px;color:var(--muted);white-space:nowrap">Physique</span>
                  <input type="range" id="inval-travail-slider" min="0" max="10" value="5" style="flex:1;accent-color:var(--navy)"/>
                  <span style="font-size:12px;color:var(--muted);white-space:nowrap">Administratif</span>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Nombre d'heures travaillées</label>
                <div style="display:flex;gap:8px">
                  <input class="form-input" id="inval-heures-val" type="text" placeholder="40" style="max-width:80px"/>
                  <select class="form-select" id="inval-heures-freq">
                    <option value="semaine" selected>Par semaine</option>
                    <option value="mois">Par mois</option>
                    <option value="annee">Par année</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Exercez-vous un sport ou un loisir à risque?</label>
                <div style="display:flex;gap:8px;margin-top:4px">
                  <label class="fu-radio-pill"><input type="radio" name="inval-sport" value="oui"/> Oui</label>
                  <label class="fu-radio-pill"><input type="radio" name="inval-sport" value="non" checked/> Non</label>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Délai de carence souhaité</label>
                <div style="display:flex;gap:8px">
                  <input class="form-input" id="inval-carence-val" type="text" placeholder="90" style="max-width:80px"/>
                  <select class="form-select" id="inval-carence-unit">
                    <option value="jours" selected>Jours</option>
                    <option value="semaines">Semaines</option>
                    <option value="mois">Mois</option>
                  </select>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Période de couverture souhaitée</label>
                <div style="display:flex;gap:8px">
                  <select class="form-select" id="inval-couverture-type" style="max-width:180px">
                    <option value="semaines">Semaines</option>
                    <option value="annees" selected>Années</option>
                    <option value="age">Âge maximum</option>
                  </select>
                  <input class="form-input" id="inval-couverture-val" type="text" placeholder="2" style="max-width:80px"/>
                </div>
              </div>
            </div>
          </div>

        </div><!-- /col gauche -->

        <!-- Résumé sidebar -->
        <div style="width:300px;flex-shrink:0;position:sticky;top:80px">
          <div class="card">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Résumé</div>
            <div id="inval-resume-body" style="padding:16px 14px;font-size:13px;color:var(--muted)">Complétez les informations pour voir le résumé.</div>
          </div>
        </div>

      </div>
    </div>

    <!-- Modal: Assurance invalidité -->
    <div id="modal-inval-av" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
      <div style="background:white;border-radius:12px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
        <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
          <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Assurance invalidité</h4>
          <button onclick="closeInvalAvModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
        </div>
        <div style="padding:20px 24px">
          <div class="form-group">
            <label class="form-label">Description</label>
            <input class="form-input" id="inval-av-desc" type="text" placeholder="ex. Police individuelle"/>
          </div>
          <div class="form-group">
            <label class="form-label">Montant mensuel</label>
            <div class="input-sfx"><input class="form-input" id="inval-av-montant" type="text" placeholder="0"/><span class="sfx">$/mois</span></div>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Propriétaire</label>
            <select class="form-select" id="inval-av-proprietaire"><option value="">Sélectionnez…</option></select>
          </div>
        </div>
        <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
          <button class="btn btn-secondary" onclick="closeInvalAvModal()">Annuler</button>
          <button class="btn btn-primary" onclick="saveInvalAv()">Enregistrer</button>
        </div>
      </div>
    </div>

