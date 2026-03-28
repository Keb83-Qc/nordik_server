    <!-- ── PAGE: Revenu et épargne ── -->
    <div id="page-revenu-epargne" class="page">
      <div class="page-title">Revenu et épargne</div>
      <div style="display:flex;gap:20px;align-items:start">

        <!-- ── MAIN COLUMN ── -->
        <div style="flex:1;min-width:0">

          <!-- REVENU CARD -->
          <div class="card" style="overflow:visible">
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
              <span>Revenu</span>
              <div id="revenu-add-wrap">
                <button class="btn btn-primary"
                  style="font-size:12px;padding:6px 14px;display:flex;align-items:center;gap:6px"
                  onclick="toggleRevenuDropdown()">
                  <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:3"><path d="M12 5v14M5 12h14"/></svg>
                  Ajouter un revenu
                  <svg viewBox="0 0 24 24" style="width:11px;height:11px;fill:none;stroke:currentColor;stroke-width:3"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div id="revenu-dropdown">
                  <button class="dd-item" onclick="openRevenuModal('Revenu d\'emploi')">Emploi</button>
                  <button class="dd-item" onclick="openRevenuModal('Autre revenu')">Autre</button>
                </div>
              </div>
            </div>
            <table class="re-table">
              <thead>
                <tr>
                  <th>Propriétaire</th>
                  <th>Type</th>
                  <th>Description</th>
                  <th>Revenu brut</th>
                  <th>Fréquence</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="revenu-list">
              </tbody>
            </table>
          </div>

          <!-- ÉPARGNE CARD -->
          <div class="card" style="overflow:visible">
            <div class="card-header">Épargne</div>
            <!-- Empty state: shown when no actifs in actifs-list -->
            <div id="epargne-empty" class="card-body" style="text-align:center;padding:34px 20px">
              <svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:none;stroke:var(--border);stroke-width:1.5;margin-bottom:10px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <div style="color:var(--muted);font-size:13px;font-weight:600">Aucun actif disponible</div>
              <div style="color:var(--muted);font-size:12px;margin-top:4px">Au moins un actif est requis pour ajouter une épargne.</div>
            </div>
            <!-- Tabs section: shown when actifs exist -->
            <div id="epargne-tabs-wrap" style="display:none">
              <div class="re-tab-bar">
                <button class="re-tab active" id="etab-client" onclick="switchEpargneTab('client',this)">—</button>
                <button class="re-tab" id="etab-conjoint" onclick="switchEpargneTab('conjoint',this)" style="display:none">—</button>
              </div>
              <!-- Client panel -->
              <div id="epanel-client" class="card-body" style="overflow:visible;padding-top:14px">
                <div style="position:relative;display:inline-block" id="ep-btn-client-wrap">
                  <button class="btn btn-primary btn-sm" onclick="toggleEpargneDropdown('client')">
                    <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
                    Ajouter une épargne
                  </button>
                  <div id="ep-dd-client" style="display:none;position:fixed;top:0;left:0;z-index:9999;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:240px;overflow:hidden">
                    <ul id="ep-dd-client-list" style="list-style:none;padding:4px 0;margin:0"></ul>
                  </div>
                </div>
                <div id="ep-list-client" style="margin-top:10px"></div>
              </div>
              <!-- Conjoint panel -->
              <div id="epanel-conjoint" class="card-body" style="display:none;overflow:visible;padding-top:14px">
                <div style="position:relative;display:inline-block" id="ep-btn-conjoint-wrap">
                  <button class="btn btn-primary btn-sm" onclick="toggleEpargneDropdown('conjoint')">
                    <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
                    Ajouter une épargne
                  </button>
                  <div id="ep-dd-conjoint" style="display:none;position:fixed;top:0;left:0;z-index:9999;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:240px;overflow:hidden">
                    <ul id="ep-dd-conjoint-list" style="list-style:none;padding:4px 0;margin:0"></ul>
                  </div>
                </div>
                <div id="ep-list-conjoint" style="margin-top:10px"></div>
              </div>
            </div>
          </div>

          <!-- DROITS DE COTISATION CARD -->
          <div class="card" style="overflow:visible">
            <div class="card-header" style="display:flex;align-items:center;gap:8px">
              Droits de cotisation
              <span class="info-tooltip-wrap">
                <span class="info-tooltip-icon">i</span>
                <span class="info-tooltip-bubble">
                  Votre client peut déterminer ses droits REER ou CELI inutilisés en accédant à son compte en ligne de l'Agence du revenu du Canada (Mon dossier ARC). Les droits REER inutilisés se retrouvent également sur son dernier avis de cotisation fédéral. Vous trouverez les détails de vos droits de participation à un CELIAPP sur votre avis de cotisation ou de nouvelle cotisation.
                </span>
              </span>
            </div>
            <table class="re-table">
              <thead>
                <tr>
                  <th style="width:55%"></th>
                  <th id="dc-client-col">Client</th>
                  <th id="dc-conjoint-col" style="display:none"></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Droits REER/RPAC inutilisés</td>
                  <td>
                    <div class="dc-input-cell">
                      <div class="input-sfx" style="flex:1">
                        <input class="form-input" id="dc-client-reer" type="text" placeholder="0"/>
                        <span class="sfx">$</span>
                      </div>
                      <button class="re-sync-btn" title="Synchroniser" onclick="showToast('Synchronisation non disponible dans la démo')">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                      </button>
                    </div>
                  </td>
                  <td id="dc-conjoint-reer-cell" style="display:none">
                    <div class="dc-input-cell">
                      <div class="input-sfx" style="flex:1">
                        <input class="form-input" id="dc-conjoint-reer" type="text" placeholder="0"/>
                        <span class="sfx">$</span>
                      </div>
                      <button class="re-sync-btn" title="Synchroniser" onclick="showToast('Synchronisation non disponible dans la démo')">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Droits CELI inutilisés</td>
                  <td>
                    <div class="dc-input-cell">
                      <div class="input-sfx" style="flex:1">
                        <input class="form-input" id="dc-client-celi" type="text" placeholder="0"/>
                        <span class="sfx">$</span>
                      </div>
                      <button class="re-sync-btn" title="Synchroniser" onclick="showToast('Synchronisation non disponible dans la démo')">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                      </button>
                    </div>
                  </td>
                  <td id="dc-conjoint-celi-cell" style="display:none">
                    <div class="dc-input-cell">
                      <div class="input-sfx" style="flex:1">
                        <input class="form-input" id="dc-conjoint-celi" type="text" placeholder="0"/>
                        <span class="sfx">$</span>
                      </div>
                      <button class="re-sync-btn" title="Synchroniser" onclick="showToast('Synchronisation non disponible dans la démo')">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Droits CELIAPP inutilisés</td>
                  <td>
                    <div class="dc-input-cell">
                      <div class="input-sfx" style="flex:1">
                        <input class="form-input" id="dc-client-celiapp" type="text" placeholder="0"/>
                        <span class="sfx">$</span>
                      </div>
                      <button class="re-sync-btn" title="Synchroniser" onclick="showToast('Synchronisation non disponible dans la démo')">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                      </button>
                    </div>
                  </td>
                  <td id="dc-conjoint-celiapp-cell" style="display:none">
                    <div class="dc-input-cell">
                      <div class="input-sfx" style="flex:1">
                        <input class="form-input" id="dc-conjoint-celiapp" type="text" placeholder="0"/>
                        <span class="sfx">$</span>
                      </div>
                      <button class="re-sync-btn" title="Synchroniser" onclick="showToast('Synchronisation non disponible dans la démo')">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div><!-- /main column -->

        <!-- ── RE SIDEBAR (inline sticky) ── -->
        <div id="re-sidebar">
          <div class="card">
            <div class="ap-sidebar-section">
              <div class="ap-sb-total">Flux monétaire</div>
              <div class="calc-tabs" style="margin-top:10px">
                <button class="calc-tab active" id="re-tab-annuel" onclick="setReTab('annuel',this)">Annuel</button>
                <button class="calc-tab" id="re-tab-mensuel" onclick="setReTab('mensuel',this)">Mensuel</button>
              </div>
            </div>
            <!-- Client block -->
            <div class="ap-sidebar-section" id="re-client-block">
              <div style="margin-bottom:10px">
                <div style="font-size:12px;font-weight:700;color:var(--navy)" id="re-client-name">Client</div>
              </div>
              <!-- Donut placeholder -->
              <div class="re-donut-wrap">
                <svg id="re-client-donut" width="90" height="90" viewBox="0 0 90 90">
                  <circle cx="45" cy="45" r="32" fill="none" stroke="#e5e7ef" stroke-width="14"/>
                  <circle id="re-client-donut-arc" cx="45" cy="45" r="32" fill="none"
                    stroke="var(--gold)" stroke-width="14"
                    stroke-dasharray="201" stroke-dashoffset="201"
                    transform="rotate(-90 45 45)" style="transition:stroke-dashoffset .4s"/>
                </svg>
                <div style="font-size:18px;font-weight:800;color:var(--navy);margin-top:4px" id="re-client-total-label">0 $</div>
                <div style="font-size:11px;color:var(--muted)" id="re-client-freq-label">annuel</div>
              </div>
              <!-- Legend rows -->
              <div style="margin-top:10px">
                <div class="ap-sb-row" style="margin-bottom:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--gold);flex-shrink:0"></span>
                    Revenu brut
                  </span>
                  <span class="ap-sb-val" id="re-client-revenu">0 $</span>
                </div>
                <div class="ap-sb-row" style="margin-bottom:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:#f97316;flex-shrink:0"></span>
                    Impôt estimé
                  </span>
                  <span class="ap-sb-val" id="re-client-impot">0 $</span>
                </div>
                <div class="ap-sb-row" style="margin-bottom:4px;border-top:1px solid var(--border);padding-top:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--navy)">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--navy);flex-shrink:0"></span>
                    Revenu net
                  </span>
                  <span class="ap-sb-val" style="font-weight:700;color:var(--navy)" id="re-client-net">0 $</span>
                </div>
                <div class="ap-sb-row" style="margin-bottom:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;flex-shrink:0"></span>
                    Épargne
                  </span>
                  <span class="ap-sb-val" id="re-client-epargne">0 $</span>
                </div>
                <div class="ap-sb-row">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;flex-shrink:0"></span>
                    Dépenses
                  </span>
                  <span class="ap-sb-val" id="re-client-depenses">0 $</span>
                </div>
              </div>
            </div>
            <!-- Conjoint block -->
            <div class="ap-sidebar-section" id="re-conjoint-block" style="display:none">
              <div style="margin-bottom:10px">
                <div style="font-size:12px;font-weight:700;color:var(--navy)" id="re-conjoint-name">Conjoint(e)</div>
              </div>
              <div class="re-donut-wrap">
                <svg width="90" height="90" viewBox="0 0 90 90">
                  <circle cx="45" cy="45" r="32" fill="none" stroke="#e5e7ef" stroke-width="14"/>
                  <circle id="re-conjoint-donut-arc" cx="45" cy="45" r="32" fill="none"
                    stroke="var(--navy)" stroke-width="14"
                    stroke-dasharray="201" stroke-dashoffset="201"
                    transform="rotate(-90 45 45)" style="transition:stroke-dashoffset .4s"/>
                </svg>
                <div style="font-size:18px;font-weight:800;color:var(--navy);margin-top:4px" id="re-conjoint-total-label">0 $</div>
                <div style="font-size:11px;color:var(--muted)" id="re-conjoint-freq-label">annuel</div>
              </div>
              <div style="margin-top:10px">
                <div class="ap-sb-row" style="margin-bottom:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--navy);flex-shrink:0"></span>
                    Revenu brut
                  </span>
                  <span class="ap-sb-val" id="re-conjoint-revenu">0 $</span>
                </div>
                <div class="ap-sb-row" style="margin-bottom:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:#f97316;flex-shrink:0"></span>
                    Impôt estimé
                  </span>
                  <span class="ap-sb-val" id="re-conjoint-impot">0 $</span>
                </div>
                <div class="ap-sb-row" style="margin-bottom:4px;border-top:1px solid var(--border);padding-top:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--navy)">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--gold);flex-shrink:0"></span>
                    Revenu net
                  </span>
                  <span class="ap-sb-val" style="font-weight:700;color:var(--navy)" id="re-conjoint-net">0 $</span>
                </div>
                <div class="ap-sb-row" style="margin-bottom:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;flex-shrink:0"></span>
                    Épargne
                  </span>
                  <span class="ap-sb-val" id="re-conjoint-epargne">0 $</span>
                </div>
                <div class="ap-sb-row">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;flex-shrink:0"></span>
                    Dépenses
                  </span>
                  <span class="ap-sb-val" id="re-conjoint-depenses">0 $</span>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /re-sidebar -->

      </div><!-- /flex -->

      <!-- ── MODAL : Revenu ── -->
      <div id="modal-revenu" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <h4 id="revenu-modal-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Revenu d'emploi</h4>
            <button onclick="closeRevenuModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
          </div>
          <div style="padding:20px 24px;max-height:72vh;overflow-y:auto">
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Propriétaire</label>
                <select class="form-select" id="revenu-proprietaire"><option value="">Sélectionnez…</option></select>
              </div>
              <div class="col form-group">
                <label class="form-label">Revenu annuel brut</label>
                <div class="input-sfx">
                  <input class="form-input" id="revenu-montant" type="text" placeholder="0"/>
                  <span class="sfx">$</span>
                </div>
              </div>
            </div>
            <!-- Emploi-specific fields -->
            <div id="revenu-emploi-fields">
              <div class="form-group">
                <label class="form-label">Profession principale</label>
                <input class="form-input" id="revenu-profession" type="text" placeholder="Ex : Infirmière, Technicien…"/>
              </div>
              <div class="form-group">
                <label class="form-label">Employeur</label>
                <input class="form-input" id="revenu-employeur" type="text" placeholder="Nom de l'employeur"/>
              </div>
              <div class="form-group">
                <label class="form-label">Date d'embauche</label>
                <div style="display:flex;gap:8px">
                  <select class="form-select" id="revenu-embauche-mois" style="max-width:160px">
                    <option value="">Mois</option>
                    <option>Janvier</option><option>Février</option><option>Mars</option>
                    <option>Avril</option><option>Mai</option><option>Juin</option>
                    <option>Juillet</option><option>Août</option><option>Septembre</option>
                    <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                  </select>
                  <input class="form-input" id="revenu-embauche-annee" type="text" placeholder="Année" style="max-width:90px"/>
                </div>
              </div>
            </div>
            <!-- Autre-specific fields -->
            <div id="revenu-autre-fields" style="display:none">
              <div class="form-group">
                <label class="form-label">Description</label>
                <input class="form-input" id="revenu-description" type="text" placeholder="Ex : Aide sociale, Pension alimentaire…"/>
              </div>
              <div class="row">
                <div class="col form-group">
                  <label class="form-label">Fréquence</label>
                  <select class="form-select" id="revenu-frequence">
                    <option value="onetime">Une fois</option>
                    <option value="52">Hebdomadaire</option>
                    <option value="26">Aux deux semaines</option>
                    <option value="12" selected>Mensuelle</option>
                    <option value="1">Annuelle</option>
                  </select>
                </div>
                <div class="col form-group">
                  <label class="form-label">Portion imposable</label>
                  <div class="input-sfx"><input class="form-input" id="revenu-portion-imposable" type="text" value="100,00"/><span class="sfx">%</span></div>
                </div>
              </div>
              <div class="row">
                <div class="col form-group">
                  <label class="form-label">Indexé à l'inflation</label>
                  <div style="display:flex;gap:8px;margin-top:4px">
                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                      <input type="radio" name="revenu-indexe" id="revenu-indexe-oui" value="yes"> Oui
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                      <input type="radio" name="revenu-indexe" id="revenu-indexe-non" value="no" checked> Non
                    </label>
                  </div>
                </div>
                <div class="col form-group">
                  <label class="form-label">Taux d'indexation supplémentaire</label>
                  <div class="input-sfx"><input class="form-input" id="revenu-taux-indexation" type="text" value="0,00"/><span class="sfx">%</span></div>
                </div>
              </div>
              <div class="row">
                <div class="col form-group">
                  <label class="form-label">Début</label>
                  <div style="display:flex;gap:6px">
                    <select class="form-select" id="revenu-debut-mois" style="max-width:140px">
                      <option value="">Mois</option>
                      <option>Janvier</option><option>Février</option><option>Mars</option>
                      <option>Avril</option><option>Mai</option><option>Juin</option>
                      <option>Juillet</option><option>Août</option><option>Septembre</option>
                      <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                    </select>
                    <input class="form-input" id="revenu-debut-annee" type="text" placeholder="Année" style="max-width:80px"/>
                  </div>
                </div>
                <div class="col form-group">
                  <label class="form-label">Fin</label>
                  <select class="form-select" id="revenu-fin-type">
                    <option value="retirement">Retraite</option>
                    <option value="death">Décès</option>
                    <option value="age">Âge</option>
                    <option value="date">Date</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Épargner le revenu dans un placement non enregistré</label>
                <div style="display:flex;gap:8px;margin-top:4px">
                  <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                    <input type="radio" name="revenu-autosave" id="revenu-autosave-oui" value="yes"> Oui
                  </label>
                  <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                    <input type="radio" name="revenu-autosave" id="revenu-autosave-non" value="no" checked> Non
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closeRevenuModal()">Annuler</button>
            <button class="btn btn-primary" onclick="saveRevenu()">Enregistrer</button>
          </div>
        </div>
      </div>

      <!-- Modal : Épargne -->
      <div id="modal-epargne" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <h4 id="ep-modal-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0"></h4>
            <button onclick="closeEpargneModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
          </div>
          <div style="padding:20px 24px;max-height:70vh;overflow-y:auto">
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Montant</label>
                <div class="input-sfx"><input class="form-input" id="ep-montant" type="text" placeholder="0"/><span class="sfx">$</span></div>
              </div>
              <div class="col form-group">
                <label class="form-label">Fréquence</label>
                <select class="form-select" id="ep-frequence">
                  <option value="onetime">Une fois</option>
                  <option value="52">Hebdomadaire</option>
                  <option value="26">Aux deux semaines</option>
                  <option value="24">Bi-mensuelle</option>
                  <option value="12" selected>Mensuel</option>
                  <option value="1">Annuel</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Indexé à l'inflation</label>
                <div style="display:flex;gap:8px;margin-top:4px">
                  <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                    <input type="radio" name="ep-indexe" id="ep-indexe-oui" value="yes"> Oui
                  </label>
                  <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                    <input type="radio" name="ep-indexe" id="ep-indexe-non" value="no" checked> Non
                  </label>
                </div>
              </div>
              <div class="col form-group">
                <label class="form-label">Taux d'indexation supplémentaire</label>
                <div class="input-sfx"><input class="form-input" id="ep-taux-indexation" type="text" value="0,00"/><span class="sfx">%</span></div>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Début</label>
                <div style="display:flex;gap:6px">
                  <select class="form-select" id="ep-debut-mois" style="max-width:140px">
                    <option value="">Mois</option>
                    <option>Janvier</option><option>Février</option><option>Mars</option>
                    <option>Avril</option><option>Mai</option><option>Juin</option>
                    <option>Juillet</option><option>Août</option><option>Septembre</option>
                    <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                  </select>
                  <input class="form-input" id="ep-debut-annee" type="text" placeholder="Année" style="max-width:80px"/>
                </div>
              </div>
              <div class="col form-group">
                <label class="form-label">Fin</label>
                <select class="form-select" id="ep-fin-type">
                  <option value="retirement">Retraite</option>
                  <option value="death">Décès</option>
                  <option value="age">Âge</option>
                  <option value="date">Date</option>
                </select>
              </div>
            </div>
          </div>
          <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closeEpargneModal()">Annuler</button>
            <button class="btn btn-primary" onclick="saveEpargne()">Enregistrer</button>
          </div>
        </div>
      </div>
    </div><!-- /page-revenu-epargne -->

