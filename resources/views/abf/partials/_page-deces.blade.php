<!-- ── PAGE: Décès ── -->
<div id="page-deces" class="page">
  <div class="page-title">Protection en cas de décès</div>
  <div style="display:flex;gap:20px;align-items:start">

    <!-- Colonne gauche -->
    <div style="flex:1;min-width:0">

      <!-- Assurance vie -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
          Assurance vie
          <button class="btn btn-primary btn-sm" onclick="openDecesAvModal()">+ Ajouter</button>
        </div>
        <div class="card-body" id="deces-av-list" style="padding:0">
          <p style="padding:14px;font-size:13px;color:var(--muted);margin:0" id="deces-av-empty">Aucune assurance vie enregistrée.</p>
        </div>
      </div>

      <!-- Prestation de décès RRQ/RPC -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          <span style="display:flex;align-items:center;gap:6px">
            Prestation de décès RRQ/RPC
            <span class="abf-tooltip-wrap">
              <span class="abf-tooltip-icon">&#9432;</span>
              <span class="abf-tooltip-box">La prestation par défaut est la prestation maximale et doit être ajustée à partir du relevé du Régime de rentes du Québec (RRQ) ou du Régime de pensions du Canada (RPC).</span>
            </span>
          </span>
        </div>
        <div class="card-body" id="deces-rrq-body">
          <!-- Rempli par decesInit() -->
        </div>
      </div>

      <!-- Actifs à liquider -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Actifs à liquider en cas de décès</div>
        <div class="card-body" id="deces-actifs-body"></div>
      </div>

      <!-- Passifs à rembourser -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Passifs à rembourser en cas de décès</div>
        <div class="card-body" id="deces-passifs-body"></div>
      </div>

      <!-- Dépenses prévues -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)" id="deces-dep-header">
          Dépenses prévues si [client] décède
        </div>
        <div class="card-body" style="padding-top:0">
          <!-- Tabs (couple seulement) -->
          <div id="deces-dep-tabs" style="display:none;border-bottom:1px solid var(--border);margin-bottom:12px;display:none">
            <button class="deces-person-tab active" id="deces-dep-tab-client" onclick="switchDecesDepTab('client',this)">CLIENT</button>
            <button class="deces-person-tab" id="deces-dep-tab-conjoint" onclick="switchDecesDepTab('conjoint',this)">CONJOINT</button>
          </div>
          <div id="deces-dep-list" style="margin-top:12px">
            <!-- pré-rempli par decesInit() avec Frais funéraires 25 000 $ -->
          </div>
          <div id="deces-dep-list-conjoint" style="display:none;margin-top:12px"></div>
          <div style="position:relative;margin-top:10px">
            <button class="btn btn-primary btn-sm" onclick="toggleDecesDep()">+ Ajouter une dépense</button>
            <div id="deces-dep-dd" style="display:none;position:fixed;z-index:9999;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.15);padding:4px 0;min-width:200px">
              <div class="deces-dep-item" onclick="addDecesDep('Frais funéraires',25000)">Frais funéraires</div>
              <div class="deces-dep-item" onclick="addDecesDep('Fonds d\'urgence',0)">Fonds d'urgence</div>
              <div class="deces-dep-item" onclick="addDecesDep('Héritage',0)">Héritage</div>
              <div class="deces-dep-item" onclick="addDecesDep('Impôts',0)">Impôts</div>
              <div class="deces-dep-item" onclick="addDecesDep('Dons',0)">Dons</div>
              <div class="deces-dep-item" onclick="addDecesDep('Frais juridiques',0)">Frais juridiques</div>
              <div class="deces-dep-item" onclick="addDecesDep('Autre',0)">Autre</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Remplacement du revenu -->
      <div class="card" id="deces-rr-card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Remplacement du revenu en cas de décès</div>
        <div class="card-body">
          <!-- Type / Brut-Net / Annuel-Mensuel -->
          <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px">
            <div>
              <div style="font-size:11px;font-weight:700;color:var(--muted);margin-bottom:6px">TYPE</div>
              <div style="display:flex;gap:6px" id="deces-rr-type-group">
                <label class="fu-radio-pill" id="deces-rr-familial-pill" style="display:none"><input type="radio" name="deces-rr-type" value="familial" onchange="decesCalc()"/> Familial</label>
                <label class="fu-radio-pill"><input type="radio" name="deces-rr-type" value="individuel" checked onchange="decesCalc()"/> Individuel</label>
                <label class="fu-radio-pill"><input type="radio" name="deces-rr-type" value="aucun" onchange="decesCalc()"/> Aucun</label>
              </div>
            </div>
            <div>
              <div style="font-size:11px;font-weight:700;color:var(--muted);margin-bottom:6px">REVENU</div>
              <div style="display:flex;gap:6px">
                <label class="fu-radio-pill"><input type="radio" name="deces-rr-brutnnet" value="brut" checked onchange="decesCalc()"/> Brut</label>
                <label class="fu-radio-pill"><input type="radio" name="deces-rr-brutnnet" value="net" onchange="decesCalc()"/> Net</label>
              </div>
            </div>
            <div>
              <div style="font-size:11px;font-weight:700;color:var(--muted);margin-bottom:6px">FRÉQUENCE</div>
              <div style="display:flex;gap:6px">
                <label class="fu-radio-pill"><input type="radio" name="deces-rr-freq" value="annuel" checked onchange="decesCalc()"/> Annuel</label>
                <label class="fu-radio-pill"><input type="radio" name="deces-rr-freq" value="mensuel" onchange="decesCalc()"/> Mensuel</label>
              </div>
            </div>
          </div>

          <!-- Person tabs for income replacement (couple seulement) -->
          <div id="deces-rr-person-tabs" style="display:none;margin:0 -20px 16px;padding:0 20px;border-bottom:1px solid var(--border)">
            <button class="deces-rr-person-tab active" id="deces-rr-tab-client" onclick="switchDecesRrTab('c',this)">CLIENT</button>
            <button class="deces-rr-person-tab" id="deces-rr-tab-conjoint" onclick="switchDecesRrTab('j',this)">CONJOINT</button>
          </div>

          <div id="deces-rr-form">
            <!-- ── Panel Client ───────────────────────────────── -->
            <div id="deces-rr-panel-c">
              <div id="deces-rr-panel-c-title" style="display:none;font-size:13px;font-weight:700;color:white;background:var(--navy);border-radius:6px;padding:7px 12px;margin-bottom:12px;text-align:center"></div>
              <div style="font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase">Revenus actuels</div>
              <div id="deces-revenus-table-c" style="margin-bottom:16px;background:#f8f9fd;border-radius:6px;padding:10px 14px;font-size:13px"></div>

              <div style="font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase">Revenus visés</div>
              <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:16px;font-size:13px">
                <span id="deces-rr-beneficiaire-label-c">Le bénéficiaire désire recevoir</span>
                <input class="form-input" id="deces-rr-pct-c" type="text" value="70" style="width:80px;text-align:center" oninput="decesCalc()"/>
                <div style="display:flex;gap:4px">
                  <label class="fu-radio-pill" style="padding:5px 10px;font-size:12px"><input type="radio" name="deces-rr-target-c" value="pct" checked onchange="decesCalc()"/> %</label>
                  <label class="fu-radio-pill" style="padding:5px 10px;font-size:12px"><input type="radio" name="deces-rr-target-c" value="montant" onchange="decesCalc()"/> $</label>
                </div>
                <span><span id="deces-rr-du-revenu-c">du revenu</span>, soit <strong id="deces-rr-vise-label-c">0 $</strong> pendant</span>
                <div class="input-sfx" style="max-width:100px">
                  <input class="form-input" id="deces-rr-duree-c" type="text" value="10" oninput="decesCalc()"/>
                  <span class="sfx" style="font-size:12px">ans</span>
                </div>
              </div>

              <div style="font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase">Revenus disponibles</div>
              <div style="background:#f8f9fd;border-radius:6px;padding:10px 14px;margin-bottom:16px">
                <div id="deces-revenu-dispo-auto-c"></div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:13px">
                  <span>Autres revenus</span>
                  <div class="input-sfx" style="max-width:130px"><input class="form-input" id="deces-autres-revenus-c" type="text" value="0" oninput="decesCalc()"/><span class="sfx">$</span></div>
                </div>
              </div>

              <div style="font-size:13px">
                <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border)">
                  <span style="color:var(--muted)">Revenu annuel manquant</span>
                  <strong id="deces-rr-manquant-c">0 $</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border)">
                  <span style="color:var(--muted)">Revenu manquant projeté <span id="deces-rr-projete-duree-c" style="font-size:11px"></span></span>
                  <strong id="deces-rr-projete-c">0 $</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0">
                  <span style="color:var(--muted)">Rendement</span>
                  <div class="input-sfx" style="max-width:100px"><input class="form-input" id="deces-rr-taux-c" type="text" value="3.70" oninput="decesCalc()"/><span class="sfx">%</span></div>
                </div>
              </div>
            </div>

            <!-- ── Panel Conjoint (hidden until couple mode) ─── -->
            <div id="deces-rr-panel-j" style="display:none">
              <div id="deces-rr-panel-j-title" style="display:none;font-size:13px;font-weight:700;color:white;background:var(--gold);border-radius:6px;padding:7px 12px;margin-bottom:12px;text-align:center"></div>
              <div id="deces-lbl-j-actuels" style="font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase">Revenus actuels</div>
              <div id="deces-revenus-table-j" style="margin-bottom:16px;background:#f8f9fd;border-radius:6px;padding:10px 14px;font-size:13px"></div>

              <div id="deces-lbl-j-vises" style="font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase">Revenus visés</div>
              <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:16px;font-size:13px">
                <span id="deces-rr-beneficiaire-label-j">Le bénéficiaire désire recevoir</span>
                <input class="form-input" id="deces-rr-pct-j" type="text" value="70" style="width:80px;text-align:center" oninput="decesCalc()"/>
                <div style="display:flex;gap:4px">
                  <label class="fu-radio-pill" style="padding:5px 10px;font-size:12px"><input type="radio" name="deces-rr-target-j" value="pct" checked onchange="decesCalc()"/> %</label>
                  <label class="fu-radio-pill" style="padding:5px 10px;font-size:12px"><input type="radio" name="deces-rr-target-j" value="montant" onchange="decesCalc()"/> $</label>
                </div>
                <span><span id="deces-rr-du-revenu-j">du revenu</span>, soit <strong id="deces-rr-vise-label-j">0 $</strong> pendant</span>
                <div class="input-sfx" style="max-width:100px">
                  <input class="form-input" id="deces-rr-duree-j" type="text" value="10" oninput="decesCalc()"/>
                  <span class="sfx" style="font-size:12px">ans</span>
                </div>
              </div>

              <div id="deces-lbl-j-dispos" style="font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase">Revenus disponibles</div>
              <div style="background:#f8f9fd;border-radius:6px;padding:10px 14px;margin-bottom:16px">
                <div id="deces-revenu-dispo-auto-j"></div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:13px">
                  <span>Autres revenus</span>
                  <div class="input-sfx" style="max-width:130px"><input class="form-input" id="deces-autres-revenus-j" type="text" value="0" oninput="decesCalc()"/><span class="sfx">$</span></div>
                </div>
              </div>

              <div style="font-size:13px">
                <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border)">
                  <span style="color:var(--muted)">Revenu annuel manquant</span>
                  <strong id="deces-rr-manquant-j">0 $</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border)">
                  <span style="color:var(--muted)">Revenu manquant projeté <span id="deces-rr-projete-duree-j" style="font-size:11px"></span></span>
                  <strong id="deces-rr-projete-j">0 $</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0">
                  <span style="color:var(--muted)">Rendement</span>
                  <div class="input-sfx" style="max-width:100px"><input class="form-input" id="deces-rr-taux-j" type="text" value="3.70" oninput="decesCalc()"/><span class="sfx">%</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /col gauche -->

    <!-- Colonne droite: Résumé sticky -->
    <div style="width:300px;flex-shrink:0;position:sticky;top:80px">
      <div class="card">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Résumé</div>
        <div id="deces-resume-body" style="padding:16px 14px"></div>
      </div>
    </div>

  </div>
</div><!-- /page-deces -->

<!-- Modal: Assurance vie -->
<div id="modal-deces-av" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
  <div style="background:white;border-radius:12px;width:560px;max-width:95vw;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.25)">
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Assurance vie</h4>
      <button onclick="closeDecesAvModal()" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted)">×</button>
    </div>
    <div style="padding:20px 24px">
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Type</label>
          <select class="form-select" id="deces-av-type">
            <option value="">Sélectionnez...</option>
            <option value="Collective">Collective</option>
            <option value="Temporaire">Temporaire</option>
            <option value="Entière">Entière</option>
            <option value="Universelle">Universelle</option>
            <option value="Avec participation">Avec participation</option>
          </select>
        </div>
        <div class="col form-group">
          <label class="form-label">Assuré</label>
          <select class="form-select" id="deces-av-owner">
            <option value="">Sélectionnez...</option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Montant assuré</label>
          <div class="input-sfx"><input class="form-input" id="deces-av-montant" type="text" placeholder="0"/><span class="sfx">$</span></div>
        </div>
        <div class="col form-group">
          <label class="form-label">Prime annuelle</label>
          <div class="input-sfx"><input class="form-input" id="deces-av-prime" type="text" placeholder="0"/><span class="sfx">$</span></div>
        </div>
      </div>
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Assureur</label>
          <select class="form-select" id="deces-av-assureur">
            <option value="">Sélectionnez...</option>
            <option>Assomption vie</option><option>Banque Laurentienne</option><option>Banque Nationale</option>
            <option>Beneva</option><option>BMO Assurance</option><option>Canada Vie (Great West, London Life)</option>
            <option>Chevaliers de Colomb</option><option>CIBC</option><option>Desjardins Assurances</option>
            <option>Empire Vie</option><option>Financière Sun Life</option><option>Foresters</option>
            <option>Humania</option><option>iA Groupe financier</option>
            <option>iA Groupe financier (anciennement L'Excellence)</option>
            <option>Ivari</option><option>La Capitale</option><option>La Croix Bleue</option>
            <option>Manuvie (Standard Life, First National)</option><option>Médic Construction</option>
            <option>Primerica</option><option>RBC Assurances</option><option>SSQ Assurance</option>
            <option>Tangerine</option><option>TD</option><option>Transamerica</option>
            <option>Union Vie</option><option>Autre</option>
          </select>
        </div>
        <div class="col form-group">
          <label class="form-label">Date d'émission</label>
          <input class="form-input" id="deces-av-date" type="text" placeholder="AAAA-MM-JJ"/>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Bénéficiaires</label>
        <div style="display:flex;gap:6px;flex-wrap:wrap">
          <label class="fu-radio-pill"><input type="radio" name="deces-av-benef" value="conjoint"/> Conjoint</label>
          <label class="fu-radio-pill"><input type="radio" name="deces-av-benef" value="enfants"/> Enfants</label>
          <label class="fu-radio-pill"><input type="radio" name="deces-av-benef" value="succession"/> Succession</label>
          <label class="fu-radio-pill"><input type="radio" name="deces-av-benef" value="parents"/> Parents</label>
          <label class="fu-radio-pill"><input type="radio" name="deces-av-benef" value="autre"/> Autre</label>
        </div>
      </div>
      <div class="form-group" style="margin-top:8px">
        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
          <input type="checkbox" id="deces-av-exclure"/> Exclure de l'analyse décès
        </label>
      </div>
      <div class="form-group">
        <label class="form-label">Notes</label>
        <textarea class="form-input" id="deces-av-notes" rows="3" style="resize:vertical"></textarea>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
      <button class="btn btn-secondary" onclick="closeDecesAvModal()">Annuler</button>
      <button class="btn btn-primary" onclick="saveDecesAv()">Enregistrer</button>
    </div>
  </div>
</div>

