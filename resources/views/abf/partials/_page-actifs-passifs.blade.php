    <!-- ── PAGE: Actifs et passifs ── -->
    <div id="page-actifs-passifs" class="page">
      <div class="page-title">Actifs et passifs</div>
      <div class="page-subtitle">Bilan patrimonial du client</div>

      <div style="display:flex;gap:20px;align-items:start">
      <div style="flex:1;min-width:0"><!-- cards start -->

      <!-- ACTIFS -->
      <div class="card" style="overflow:visible">
        <div class="card-header">Actifs</div>
        <div class="card-body" style="overflow:visible">
          <div id="actifs-list" class="list-empty">Aucun actif ajouté.</div>
          <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px">

            <!-- Placements -->
            <div style="position:relative" id="placement-menu-wrap">
              <button class="btn btn-primary btn-sm" onclick="toggleApMenu(event,'placement-dropdown')">
                <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
                Ajouter un placement
              </button>
              <div id="placement-dropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;z-index:300;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:250px;overflow:hidden">
                <ul style="list-style:none;padding:4px 0;margin:0">
                  <li><button class="legal-menu-item" onclick="openPlacementModal('Compte bancaire')">Compte bancaire</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('Non enregistré')">Non enregistré</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('CELI')">CELI</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('CELIAPP')">CELIAPP</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('REEE')">REEE</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('REER')">REER</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('REER conjoint')">REER conjoint</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('REER collectif')">REER collectif</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('RVER')">RVER</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('RPAC')">RPAC</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('RPA à cotisations déterminées')">RPA à cotisations déterminées</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('FERR')">FERR</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('FRV')">FRV</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('CRI')">CRI</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('REER Immobilisé')">REER Immobilisé</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('RPDB')">RPDB</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('RRS')">RRS</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('Autre actif enregistré')">Autre actif enregistré</button></li>
                </ul>
              </div>
            </div>

            <!-- Biens -->
            <div style="position:relative" id="bien-menu-wrap">
              <button class="btn btn-primary btn-sm" onclick="toggleApMenu(event,'bien-dropdown')">
                <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
                Ajouter un bien
              </button>
              <div id="bien-dropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;z-index:300;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:220px;overflow:hidden">
                <ul style="list-style:none;padding:4px 0;margin:0">
                  <li><button class="legal-menu-item" onclick="openBienModal('Résidence principale')">Résidence principale</button></li>
                  <li><button class="legal-menu-item" onclick="openBienModal('Résidence secondaire')">Résidence secondaire</button></li>
                  <li><button class="legal-menu-item" onclick="openBienModal('Véhicule')">Véhicule</button></li>
                  <li><button class="legal-menu-item" onclick="openBienModal('Immeuble locatif')">Immeuble locatif</button></li>
                  <li><button class="legal-menu-item" onclick="openBienModal('Terrain')">Terrain</button></li>
                  <li><button class="legal-menu-item" onclick="openBienModal('Objet de valeur')">Objet de valeur</button></li>
                  <li><button class="legal-menu-item" onclick="openBienModal('Autre bien')">Autre bien</button></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PASSIFS -->
      <div class="card" style="overflow:visible">
        <div class="card-header">Passifs</div>
        <div class="card-body" style="overflow:visible">
          <div id="passifs-list" class="list-empty">Aucun passif ajouté.</div>
          <div style="margin-top:14px;position:relative;display:inline-block" id="passif-menu-wrap">
            <button class="btn btn-primary btn-sm" onclick="toggleApMenu(event,'passif-dropdown')">
              <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
              Ajouter un passif
            </button>
            <div id="passif-dropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;z-index:300;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:230px;overflow:hidden">
              <ul style="list-style:none;padding:4px 0;margin:0">
                <li><button class="legal-menu-item" onclick="openPassifModal('Carte de crédit')">Carte de crédit</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Marge de crédit')">Marge de crédit</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Compte à payer')">Compte à payer</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Prêt automobile')">Prêt automobile</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Prêt personnel')">Prêt personnel</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Prêt hypothécaire')">Prêt hypothécaire</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Prêt commercial')">Prêt commercial</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Prêt pour investissement')">Prêt pour investissement</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Prêt étudiant')">Prêt étudiant</button></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      </div><!-- cards end -->

      <!-- ── AP SIDEBAR (inline sticky) ── -->
      <div id="ap-sidebar">
        <div class="card">
          <div class="ap-sidebar-section">
            <div class="ap-sb-total">Valeur nette</div>
            <div class="ap-sb-total-val" id="ap-total-vn">0 $</div>
          </div>
          <div class="ap-sidebar-section">
            <div style="font-size:12px;font-weight:700;color:var(--navy);margin-bottom:8px" id="ap-client-name">Client</div>
            <div class="ap-sb-row"><span class="ap-sb-label">Valeur nette</span><span class="ap-sb-val" id="ap-client-vn">0 $</span></div>
            <div class="ap-sb-row"><span class="ap-sb-label">Actifs</span><span class="ap-sb-val" style="color:var(--valid)" id="ap-client-actifs">0 $</span></div>
            <div class="ap-sb-row"><span class="ap-sb-label">Passifs</span><span class="ap-sb-val" style="color:#ef4444" id="ap-client-passifs">0 $</span></div>
          </div>
          <div class="ap-sidebar-section" id="ap-conjoint-block" style="display:none">
            <div style="font-size:12px;font-weight:700;color:var(--navy);margin-bottom:8px" id="ap-conjoint-name">Conjoint(e)</div>
            <div class="ap-sb-row"><span class="ap-sb-label">Valeur nette</span><span class="ap-sb-val" id="ap-conjoint-vn">0 $</span></div>
            <div class="ap-sb-row"><span class="ap-sb-label">Actifs</span><span class="ap-sb-val" style="color:var(--valid)" id="ap-conjoint-actifs">0 $</span></div>
            <div class="ap-sb-row"><span class="ap-sb-label">Passifs</span><span class="ap-sb-val" style="color:#ef4444" id="ap-conjoint-passifs">0 $</span></div>
          </div>
        </div>
      </div>

      </div><!-- flex end -->

      <!-- ── MODAL : Placement ── -->
      <div id="modal-placement" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:560px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <h4 id="plac-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0"></h4>
            <button onclick="closePlacementModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
          </div>
          <div style="padding:20px 24px;max-height:72vh;overflow-y:auto">
            <div class="form-group">
              <label class="form-label">Description</label>
              <input class="form-input" id="plac-description" type="text"/>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Propriétaire</label>
                <select class="form-select" id="plac-proprietaire"><option value="">Sélectionnez…</option></select>
              </div>
              <div class="col form-group">
                <label class="form-label">Valeur</label>
                <div class="input-sfx"><input class="form-input" id="plac-valeur" type="text" placeholder="0"/><span class="sfx">$</span></div>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Portefeuille</label>
                <select class="form-select" id="plac-portefeuille" onchange="syncRendement()">
                  <option value="prudent">Prudent</option>
                  <option value="moderate">Modéré</option>
                  <option value="balanced" selected>Équilibré</option>
                  <option value="growth">Croissance</option>
                  <option value="aggressive">Audacieux</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label">Rendement</label>
                <div class="input-sfx"><input class="form-input" id="plac-rendement" type="text" value="3,70"/><span class="sfx">%</span></div>
              </div>
            </div>
            <div id="plac-legislation-row" style="display:none" class="form-group">
              <label class="form-label">Législation</label>
              <select class="form-select" id="plac-legislation">
                <option value="">Sélectionnez…</option>
                <option value="ab">Alberta</option>
                <option value="bc">Colombie-Britannique</option>
                <option value="pe">Île-du-Prince-Édouard</option>
                <option value="mb">Manitoba</option>
                <option value="nb">Nouveau-Brunswick</option>
                <option value="ns">Nouvelle-Écosse</option>
                <option value="nu">Nunavut</option>
                <option value="on">Ontario</option>
                <option value="qc">Québec</option>
                <option value="sk">Saskatchewan</option>
                <option value="nl">Terre-Neuve-et-Labrador</option>
                <option value="nt">Territoires du Nord-Ouest</option>
                <option value="yt">Yukon</option>
              </select>
            </div>
            <div id="plac-date-ouverture-row" style="display:none" class="form-group">
              <label class="form-label">Date d'ouverture <span style="color:var(--gold)">*</span></label>
              <input class="form-input" id="plac-date-ouverture" type="text" placeholder="Année (ex: 2023)" oninput="placDateOuvertureChange()"/>
            </div>
            <div class="modal-facultatif-title">Facultatif</div>
            <div class="form-group">
              <label class="form-label">Catégorie d'actif</label>
              <select class="form-select" id="plac-categorie">
                <option value="">Sélectionnez…</option>
                <option>Actions</option><option>Fonds communs de placement</option>
                <option>Fonds distincts</option><option>Obligations</option>
                <option>Placements garantis</option><option>Autre</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Institution</label>
              <select class="form-select" id="plac-institution"><option value="">Sélectionnez…</option></select>
            </div>
            <div class="form-group">
              <label class="form-label">Notes</label>
              <textarea class="form-input" id="plac-notes" rows="2" style="resize:vertical"></textarea>
            </div>
          </div>
          <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closePlacementModal()">Annuler</button>
            <button class="btn btn-primary" id="plac-save-btn" onclick="savePlacement()">Enregistrer</button>
          </div>
        </div>
      </div>

      <!-- ── MODAL : Bien ── -->
      <div id="modal-bien" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:540px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <h4 id="bien-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0"></h4>
            <button onclick="closeBienModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
          </div>
          <div style="padding:20px 24px;max-height:72vh;overflow-y:auto">
            <div class="form-group">
              <label class="form-label">Description</label>
              <input class="form-input" id="bien-description" type="text"/>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Propriétaire</label>
                <select class="form-select" id="bien-proprietaire" onchange="bienPropChange()"><option value="">Sélectionnez…</option></select>
              </div>
              <div class="col form-group">
                <label class="form-label">Valeur</label>
                <div class="input-sfx"><input class="form-input" id="bien-valeur" type="text" placeholder="0"/><span class="sfx">$</span></div>
              </div>
            </div>
            <div id="bien-parts-row" style="display:none" class="row">
              <div class="col form-group">
                <label class="form-label">Part de <span id="bien-part-label-client">client</span></label>
                <div class="input-sfx"><input class="form-input" id="bien-part-client" type="number" min="0" max="100" step="0.01" value="50"/><span class="sfx">%</span></div>
              </div>
              <div class="col form-group">
                <label class="form-label">Part de <span id="bien-part-label-conjoint">conjoint</span></label>
                <div class="input-sfx"><input class="form-input" id="bien-part-conjoint" type="number" min="0" max="100" step="0.01" value="50"/><span class="sfx">%</span></div>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Coût d'acquisition</label>
                <div class="input-sfx"><input class="form-input" id="bien-cout" type="text" placeholder="0"/><span class="sfx">$</span></div>
              </div>
              <div class="col form-group">
                <label class="form-label">Taux de croissance</label>
                <div class="input-sfx"><input class="form-input" id="bien-croissance" type="text" placeholder="0"/><span class="sfx">%</span></div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Notes</label>
              <textarea class="form-input" id="bien-notes" rows="2" style="resize:vertical"></textarea>
            </div>
          </div>
          <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closeBienModal()">Annuler</button>
            <button class="btn btn-primary" onclick="saveBien()">Enregistrer</button>
          </div>
        </div>
      </div>

      <!-- ── MODAL : Passif ── -->
      <div id="modal-passif" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:580px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <h4 id="pass-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0"></h4>
            <button onclick="closePassifModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
          </div>
          <div style="padding:20px 24px;max-height:72vh;overflow-y:auto">
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Description</label>
                <input class="form-input" id="pass-description" type="text"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Propriétaire</label>
                <select class="form-select" id="pass-proprietaire" onchange="passifPropChange()"><option value="">Sélectionnez…</option></select>
              </div>
            </div>
            <div id="pass-parts-row" style="display:none" class="row">
              <div class="col form-group">
                <label class="form-label">Part de <span id="pass-part-label-client">client</span></label>
                <div class="input-sfx"><input class="form-input" id="pass-part-client" type="number" min="0" max="100" step="0.01" value="50"/><span class="sfx">%</span></div>
              </div>
              <div class="col form-group">
                <label class="form-label">Part de <span id="pass-part-label-conjoint">conjoint</span></label>
                <div class="input-sfx"><input class="form-input" id="pass-part-conjoint" type="number" min="0" max="100" step="0.01" value="50"/><span class="sfx">%</span></div>
              </div>
            </div>
            <!-- Section calcul -->
            <div style="border:1px solid var(--border);border-radius:8px;margin-bottom:16px;overflow:hidden">
              <div style="padding:10px 14px;background:#f8f9fd;font-size:12px;font-weight:700;color:var(--navy);border-bottom:1px solid var(--border)">Sélectionnez la valeur à calculer</div>
              <div style="padding:12px 14px">
                <div class="calc-tabs">
                  <button class="calc-tab active" onclick="setCalcType('solde',this)">Solde</button>
                  <button class="calc-tab" onclick="setCalcType('amortissement',this)">Amortissement</button>
                  <button class="calc-tab" onclick="setCalcType('taux',this)">Taux</button>
                  <button class="calc-tab" onclick="setCalcType('paiement',this)">Paiement</button>
                </div>
                <div class="row">
                  <div class="col form-group">
                    <label class="form-label">Solde</label>
                    <div class="input-sfx"><input class="form-input" id="pass-solde" type="text" placeholder="0"/><span class="sfx">$</span></div>
                  </div>
                  <div class="col form-group">
                    <label class="form-label">Amortissement</label>
                    <div style="display:flex;gap:6px">
                      <input class="form-input" id="pass-amort-val" type="text" placeholder="0" style="max-width:70px"/>
                      <select class="form-select" id="pass-amort-unit">
                        <option value="month" selected>Mois</option>
                        <option value="year">Années</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col form-group" style="margin-bottom:0">
                    <label class="form-label">Taux</label>
                    <div class="input-sfx"><input class="form-input" id="pass-taux" type="text" placeholder="0,00"/><span class="sfx">%</span></div>
                  </div>
                  <div class="col form-group" style="margin-bottom:0">
                    <label class="form-label">Paiement</label>
                    <div style="display:flex;gap:6px">
                      <div class="input-sfx" style="flex:1"><input class="form-input" id="pass-paiement" type="text" placeholder="0,00"/><span class="sfx">$</span></div>
                      <select class="form-select" id="pass-paiement-freq" style="max-width:130px">
                        <option value="monthly" selected>Mensuel</option>
                        <option value="yearly">Annuel</option>
                        <option value="biweekly">Aux deux semaines</option>
                        <option value="weekly">Hebdomadaire</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Date de renouvellement</label>
              <div style="display:flex;gap:8px">
                <select class="form-select" id="pass-renouvellement-mois" style="max-width:160px">
                  <option value="">Mois</option>
                  <option>Janvier</option><option>Février</option><option>Mars</option>
                  <option>Avril</option><option>Mai</option><option>Juin</option>
                  <option>Juillet</option><option>Août</option><option>Septembre</option>
                  <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                </select>
                <input class="form-input" id="pass-renouvellement-annee" type="text" placeholder="Année" style="max-width:90px"/>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Institution</label>
              <select class="form-select" id="pass-institution"><option value="">Sélectionnez…</option></select>
            </div>
            <div class="form-group">
              <label class="form-label">Notes</label>
              <textarea class="form-input" id="pass-notes" rows="2" style="resize:vertical"></textarea>
            </div>
          </div>
          <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closePassifModal()">Annuler</button>
            <button class="btn btn-primary" onclick="savePassif()">Enregistrer</button>
          </div>
        </div>
      </div>
    </div>

