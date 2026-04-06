<!-- ── PAGE: Retraite ── -->
<div id="page-retraite" class="page">
  <div class="page-title">Retraite</div>

  <div style="display:flex;gap:20px;align-items:start">

    <!-- ══ Colonne principale ══════════════════════════════════════════ -->
    <div style="flex:1;min-width:0">

      <!-- ── 1. Moment de la retraite ─────────────────────────────── -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          Moment de la retraite
        </div>
        <div class="card-body" style="padding:0">
          <div style="display:grid;grid-template-columns:1fr 130px 90px 1fr;gap:8px;padding:8px 16px;background:#f8f9fd;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;border-bottom:1px solid var(--border)">
            <span>Personne</span><span>Type</span><span>Valeur</span><span>Date estimée</span>
          </div>
          <!-- Ligne client -->
          <div style="display:grid;grid-template-columns:1fr 130px 90px 1fr;gap:8px;padding:12px 16px;align-items:center;border-bottom:1px solid var(--border)">
            <span id="retraite-nom-client" style="font-weight:600;font-size:13px;color:var(--navy)">Client</span>
            <select class="form-select" id="retraite-type-client" style="font-size:12px;padding:5px 8px" onchange="retraiteToggleType('client');retraiteCalc()">
              <option value="age" selected>Âge</option>
              <option value="date">Année</option>
            </select>
            <div>
              <input class="form-input" id="retraite-age-client"   type="text"   value="65" style="text-align:center;padding:5px 4px;width:75px" oninput="retraiteCalc()"/>
              <input class="form-input" id="retraite-annee-client" type="number" min="2025" max="2090" placeholder="Année" style="text-align:center;padding:5px 4px;width:80px;display:none" oninput="retraiteCalc()"/>
            </div>
            <span id="retraite-date-client" style="font-size:12px;color:var(--muted)">—</span>
          </div>
          <!-- Ligne conjoint (masquée si seul) -->
          <div id="retraite-row-conjoint" style="display:none;grid-template-columns:1fr 130px 90px 1fr;gap:8px;padding:12px 16px;align-items:center">
            <span id="retraite-nom-conjoint" style="font-weight:600;font-size:13px;color:var(--gold)">Conjoint</span>
            <select class="form-select" id="retraite-type-conjoint" style="font-size:12px;padding:5px 8px" onchange="retraiteToggleType('conjoint');retraiteCalc()">
              <option value="age" selected>Âge</option>
              <option value="date">Année</option>
            </select>
            <div>
              <input class="form-input" id="retraite-age-conjoint"   type="text"   value="65" style="text-align:center;padding:5px 4px;width:75px" oninput="retraiteCalc()"/>
              <input class="form-input" id="retraite-annee-conjoint" type="number" min="2025" max="2090" placeholder="Année" style="text-align:center;padding:5px 4px;width:80px;display:none" oninput="retraiteCalc()"/>
            </div>
            <span id="retraite-date-conjoint" style="font-size:12px;color:var(--muted)">—</span>
          </div>
        </div>
      </div>

      <!-- ── 2. Objectif de retraite ───────────────────────────────── -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          Objectif
        </div>
        <div class="card-body">

          <!-- Ligne de contrôles globaux -->
          <div style="display:flex;gap:28px;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;flex-wrap:wrap;align-items:flex-start">
            <!-- Type objectif (couple uniquement) -->
            <div id="retraite-goal-type-wrap" style="display:none">
              <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Type</div>
              <div style="display:flex;gap:4px">
                <label class="fu-radio-pill" style="padding:5px 12px;font-size:12px"><input type="radio" name="retraite-goal-type" value="familial"   onchange="retraiteCalc()"/> Familial</label>
                <label class="fu-radio-pill" style="padding:5px 12px;font-size:12px"><input type="radio" name="retraite-goal-type" value="individuel" checked onchange="retraiteCalc()"/> Individuel</label>
                <label class="fu-radio-pill" style="padding:5px 12px;font-size:12px"><input type="radio" name="retraite-goal-type" value="aucun"      onchange="retraiteCalc()"/> Aucun</label>
              </div>
            </div>
            <!-- Cible % ou $ -->
            <div>
              <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Cible</div>
              <div style="display:flex;gap:4px">
                <label class="fu-radio-pill" style="padding:5px 14px;font-size:12px"><input type="radio" name="retraite-target-type" value="pct"     checked onchange="retraiteCalcUpdateUnits()"/> %</label>
                <label class="fu-radio-pill" style="padding:5px 14px;font-size:12px"><input type="radio" name="retraite-target-type" value="montant"         onchange="retraiteCalcUpdateUnits()"/> $</label>
              </div>
            </div>
            <!-- Fréquence -->
            <div>
              <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Fréquence</div>
              <div style="display:flex;gap:4px">
                <label class="fu-radio-pill" style="padding:5px 12px;font-size:12px"><input type="radio" name="retraite-goal-frequency" value="annuel"  checked onchange="retraiteCalc()"/> Annuel</label>
                <label class="fu-radio-pill" style="padding:5px 12px;font-size:12px"><input type="radio" name="retraite-goal-frequency" value="mensuel"        onchange="retraiteCalc()"/> Mensuel</label>
              </div>
            </div>
          </div>

          <div id="retraite-obj-two-col" style="display:grid;grid-template-columns:1fr;gap:20px">

          <!-- Panel client -->
          <div id="retraite-obj-panel-client">
            <div id="retraite-obj-hdr-c" style="display:none;font-size:13px;font-weight:700;color:white;background:var(--navy);border-radius:6px;padding:7px 12px;margin-bottom:12px;text-align:center"></div>
            <div style="font-size:12px;color:var(--muted);margin-bottom:12px">
              Revenu <span class="retraite-freq-label">annuel</span> net actuel : <strong id="retraite-revenu-net-client">—</strong>
            </div>
            <div id="retraite-periodes-client">
              <div class="retraite-periode" data-idx="0" style="padding:12px 14px;background:#f8f9fd;border-radius:8px;margin-bottom:10px">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px">
                  <span id="retraite-nom-inline-client" style="font-weight:600">Le client</span>
                  <span style="color:var(--muted)">vise</span>
                  <input class="form-input" id="retraite-pct-client-0" type="text" value="70" style="width:65px;text-align:center" oninput="retraiteCalc()"/>
                  <span id="retraite-unit-client-0" style="font-size:12px;color:var(--muted)">% du revenu net actuel jusqu'au décès.</span>
                </div>
                <div style="font-size:12px;color:var(--muted);margin-top:5px;padding-left:2px">
                  Ce qui correspond à <strong id="retraite-montant-client-0" style="color:var(--navy)">—</strong>.
                </div>
              </div>
            </div>
            <button class="btn btn-secondary btn-sm" style="margin-bottom:16px" onclick="retraiteAddPeriode('client')">
              <svg viewBox="0 0 26 24" width="13" height="13" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
              Ajouter une période
            </button>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" style="display:flex;align-items:center;gap:6px">
                Désirez-vous épargner automatiquement les revenus qui excèdent les dépenses?
                <span class="abf-tooltip-wrap"><span class="abf-tooltip-icon">&#9432;</span><span class="abf-tooltip-box">Si les revenus dépassent les dépenses planifiées, l'excédent est automatiquement ajouté à l'épargne projetée.</span></span>
              </label>
              <div style="display:flex;gap:8px;margin-top:6px">
                <label class="fu-radio-pill"><input type="radio" name="retraite-save-surplus-c" value="oui" onchange="retraiteCalc()"/> Oui</label>
                <label class="fu-radio-pill"><input type="radio" name="retraite-save-surplus-c" value="non" checked onchange="retraiteCalc()"/> Non</label>
              </div>
            </div>
          </div>

          <!-- Panel conjoint -->
          <div id="retraite-obj-panel-conjoint" style="display:none">
            <div id="retraite-obj-hdr-j" style="font-size:13px;font-weight:700;color:white;background:var(--gold);border-radius:6px;padding:7px 12px;margin-bottom:12px;text-align:center"></div>

            <div style="font-size:12px;color:var(--muted);margin-bottom:12px">
              Revenu <span class="retraite-freq-label">annuel</span> net actuel : <strong id="retraite-revenu-net-conjoint">—</strong>
            </div>
            <div id="retraite-periodes-conjoint">
              <div class="retraite-periode" data-idx="0" style="padding:12px 14px;background:#f8f9fd;border-radius:8px;margin-bottom:10px">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px">
                  <span id="retraite-nom-inline-conjoint" style="font-weight:600">Le conjoint</span>
                  <span style="color:var(--muted)">vise</span>
                  <input class="form-input" id="retraite-pct-conjoint-0" type="text" value="70" style="width:65px;text-align:center" oninput="retraiteCalc()"/>
                  <span id="retraite-unit-conjoint-0" style="font-size:12px;color:var(--muted)">% du revenu net actuel jusqu'au décès.</span>
                </div>
                <div style="font-size:12px;color:var(--muted);margin-top:5px;padding-left:2px">
                  Ce qui correspond à <strong id="retraite-montant-conjoint-0" style="color:var(--navy)">—</strong>.
                </div>
              </div>
            </div>
            <button class="btn btn-secondary btn-sm" style="margin-bottom:16px" onclick="retraiteAddPeriode('conjoint')">
              <svg viewBox="0 0 26 24" width="13" height="13" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
              Ajouter une période
            </button>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label">Désirez-vous épargner automatiquement les revenus qui excèdent les dépenses?</label>
              <div style="display:flex;gap:8px;margin-top:6px">
                <label class="fu-radio-pill"><input type="radio" name="retraite-save-surplus-j" value="oui" onchange="retraiteCalc()"/> Oui</label>
                <label class="fu-radio-pill"><input type="radio" name="retraite-save-surplus-j" value="non" checked onchange="retraiteCalc()"/> Non</label>
              </div>
            </div>
          </div>

          </div><!-- /retraite-obj-two-col -->
        </div>
      </div>

      <!-- ── 3. Dépenses à la retraite ─────────────────────────────── -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
          <span style="display:flex;align-items:center;gap:6px">
            Dépenses à la retraite
            <span class="abf-tooltip-wrap"><span class="abf-tooltip-icon">&#9432;</span><span class="abf-tooltip-box">Dépenses supplémentaires prévues à la retraite : voyages, activités, soins de santé, etc.</span></span>
          </span>
          <button class="btn btn-primary btn-sm" onclick="retraiteAddDepense()">+ Ajouter</button>
        </div>
        <div class="card-body" id="retraite-depenses-list" style="padding:0">
          <p style="padding:14px;font-size:13px;color:var(--muted);margin:0" id="retraite-depenses-empty">Aucune dépense ajoutée.</p>
        </div>
      </div>

      <!-- ── 4. Profil d'investisseur ──────────────────────────────── -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          <span style="display:flex;align-items:center;gap:6px">
            Profil d'investisseur
            <span class="abf-tooltip-wrap"><span class="abf-tooltip-icon">&#9432;</span><span class="abf-tooltip-box">Les actifs sont projetés avec le rendement moyen du profil sélectionné pour chaque période.</span></span>
          </span>
        </div>
        <div class="card-body">
          <div id="retraite-profil-two-col" style="display:grid;grid-template-columns:1fr;gap:20px">
          <div id="retraite-profil-panel-client">
            <div id="retraite-profil-hdr-c" style="display:none;font-size:13px;font-weight:700;color:white;background:var(--navy);border-radius:6px;padding:7px 12px;margin-bottom:12px;text-align:center"></div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label" style="display:flex;align-items:center;gap:6px">Avant la retraite <span class="abf-tooltip-wrap"><span class="abf-tooltip-icon">&#9432;</span><span class="abf-tooltip-box">Profil utilisé jusqu'à l'âge de la retraite.</span></span></label>
                <select class="form-select" id="retraite-profil-avant-client" onchange="retraiteCalc()">
                  <option value="prudent">Prudent (~2.5%)</option>
                  <option value="modere">Modéré (~3.5%)</option>
                  <option value="equilibre" selected>Équilibré (~4.5%)</option>
                  <option value="croissance">Croissance (~5.5%)</option>
                  <option value="audacieux">Audacieux (~6.5%)</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label" style="display:flex;align-items:center;gap:6px">Pendant la retraite <span class="abf-tooltip-wrap"><span class="abf-tooltip-icon">&#9432;</span><span class="abf-tooltip-box">Profil utilisé lors de la phase de décaissement.</span></span></label>
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
          <div id="retraite-profil-panel-conjoint" style="display:none">
            <div id="retraite-profil-hdr-j" style="font-size:13px;font-weight:700;color:white;background:var(--gold);border-radius:6px;padding:7px 12px;margin-bottom:12px;text-align:center"></div>
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
          </div><!-- /retraite-profil-two-col -->
        </div>
      </div>

      <!-- ── 5. Régimes de retraite publics ────────────────────────── -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          <span style="display:flex;align-items:center;gap:6px">
            Régimes de retraite publics
            <span class="abf-tooltip-wrap"><span class="abf-tooltip-icon">&#9432;</span><span class="abf-tooltip-box">RRQ/RPC et Sécurité de vieillesse. Entrez le montant estimé des prestations que vous prévoyez recevoir.</span></span>
          </span>
        </div>
        <div style="padding:0">
          <div id="retraite-regpub-two-col" style="display:grid;grid-template-columns:1fr;gap:0;padding:0">
            <div id="retraite-regpub-panel-client">
              <div id="retraite-regpub-hdr-c" style="display:none;font-size:13px;font-weight:700;color:white;background:var(--navy);padding:7px 16px;text-align:center"></div>
              <div id="retraite-regpub-table-client"></div>
            </div>
            <div id="retraite-regpub-panel-conjoint" style="display:none">
              <div id="retraite-regpub-hdr-j" style="font-size:13px;font-weight:700;color:white;background:var(--gold);padding:7px 16px;text-align:center"></div>
              <div id="retraite-regpub-table-conjoint"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── 6. Régimes à prestations déterminées ──────────────────── -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
          <span style="display:flex;align-items:center;gap:6px">
            Régimes à prestations déterminées
            <span class="abf-tooltip-wrap"><span class="abf-tooltip-icon">&#9432;</span><span class="abf-tooltip-box">Régimes d'employeur à prestations garanties : fonds de pension gouvernemental, régime municipal, etc.</span></span>
          </span>
          <div style="display:flex;gap:8px;align-items:center">
            <div id="retraite-rpd-tabs" style="display:flex;gap:4px"></div>
            <button class="btn btn-primary btn-sm" onclick="openRetraiteRpdModal()">+ Ajouter un régime</button>
          </div>
        </div>
        <div style="padding:0">
          <div id="retraite-rpd-two-col" style="display:grid;grid-template-columns:1fr;gap:0">
            <div id="retraite-rpd-panel-client">
              <div id="retraite-rpd-hdr-c" style="display:none;font-size:13px;font-weight:700;color:white;background:var(--navy);padding:7px 16px;text-align:center"></div>
              <div id="retraite-rpd-list-client"></div>
            </div>
            <div id="retraite-rpd-panel-conjoint" style="display:none">
              <div id="retraite-rpd-hdr-j" style="font-size:13px;font-weight:700;color:white;background:var(--gold);padding:7px 16px;text-align:center"></div>
              <div id="retraite-rpd-list-conjoint"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── 7. Retraits planifiés ─────────────────────────────────── -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
          <span style="display:flex;align-items:center;gap:6px">
            Retraits planifiés
            <span class="abf-tooltip-wrap"><span class="abf-tooltip-icon">&#9432;</span><span class="abf-tooltip-box">Retraits prévus de vos comptes enregistrés ou non enregistrés à la retraite (REER, CELI, FERR, etc.).</span></span>
          </span>
          <div style="display:flex;gap:8px;align-items:center">
            <div id="retraite-retraits-tabs" style="display:flex;gap:4px"></div>
            <div style="position:relative" id="retraite-retraits-menu-wrap">
              <button class="btn btn-primary btn-sm" onclick="toggleRetraitsMenu(event)">+ Ajouter un retrait <svg viewBox="0 0 20 20" width="10" height="10" fill="currentColor" style="margin-left:3px"><path d="M5 7l5 5 5-5z"/></svg></button>
              <div id="retraite-retraits-menu" style="display:none;position:absolute;right:0;top:calc(100% + 4px);background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);z-index:300;min-width:180px;overflow:hidden">
                @foreach(['REER','CELI','FERR','REER collectif','CRI/FRV','RPDB','Non enregistré','Autre'] as $typeRetrait)
                <button onclick="openRetraiteRetraitModal('{{ $typeRetrait }}');document.getElementById('retraite-retraits-menu').style.display='none'" style="display:block;width:100%;text-align:left;padding:9px 14px;border:none;background:none;cursor:pointer;font-size:13px;color:var(--text)" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='none'">{{ $typeRetrait }}</button>
                @endforeach
              </div>
            </div>
          </div>
        </div>
        <div style="padding:0">
          <div id="retraite-retraits-two-col" style="display:grid;grid-template-columns:1fr;gap:0">
            <div id="retraite-retraits-panel-client">
              <div id="retraite-retraits-hdr-c" style="display:none;font-size:13px;font-weight:700;color:white;background:var(--navy);padding:7px 16px;text-align:center"></div>
              <div id="retraite-retraits-list-client"></div>
            </div>
            <div id="retraite-retraits-panel-conjoint" style="display:none">
              <div id="retraite-retraits-hdr-j" style="font-size:13px;font-weight:700;color:white;background:var(--gold);padding:7px 16px;text-align:center"></div>
              <div id="retraite-retraits-list-conjoint"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── 8. Actifs alloués à l'objectif retraite ───────────────── -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
          <span style="display:flex;align-items:center;gap:6px">
            Actifs alloués à l'objectif retraite
            <span class="abf-tooltip-wrap"><span class="abf-tooltip-icon">&#9432;</span><span class="abf-tooltip-box">Actifs financiers dédiés au financement de la retraite. Définissez la stratégie d'investissement pour chaque période et glissez pour réordonner.</span></span>
          </span>
          <button class="btn btn-primary btn-sm" onclick="openRetraiteActifModal()">+ Ajouter un actif</button>
        </div>
        <div style="padding:0">
          <div id="retraite-actifs-list"></div>
        </div>
      </div>

    </div><!-- /col principale -->

    <!-- ══ Sidebar sticky ═════════════════════════════════════════════ -->
    <div style="width:300px;flex-shrink:0;position:sticky;top:80px">
      <div class="card">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          Résumé — Retraite
        </div>
        <div id="retraite-resume-body" style="padding:0">
          <div style="padding:16px 14px;font-size:13px;color:var(--muted)">Complétez les informations pour voir le résumé.</div>
        </div>
      </div>
    </div>

  </div>
</div><!-- /page-retraite -->

<!-- ═══ Modal : Dépense retraite ══════════════════════════════════════ -->
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
        <label class="form-label">Période (âge du client)</label>
        <div style="display:flex;gap:8px;align-items:center">
          <span style="font-size:13px;white-space:nowrap">De</span>
          <input class="form-input" id="retraite-dep-debut" type="text" placeholder="retraite" style="max-width:80px;text-align:center"/>
          <span style="font-size:13px">à</span>
          <input class="form-input" id="retraite-dep-fin" type="text" placeholder="décès" style="max-width:80px;text-align:center"/>
          <span style="font-size:13px">ans</span>
        </div>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
      <button class="btn btn-secondary" onclick="closeRetraiteDepenseModal()">Annuler</button>
      <button class="btn btn-primary"   onclick="saveRetraiteDepense()">Enregistrer</button>
    </div>
  </div>
</div>

<!-- ═══ Modal : Éditer régime public ══════════════════════════════════ -->
<div id="modal-retraite-regpub" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
  <div style="background:white;border-radius:12px;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <h4 id="retraite-regpub-modal-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Régime public</h4>
      <button onclick="closeRetraiteRegPubModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
    </div>
    <input type="hidden" id="retraite-regpub-role"/>
    <input type="hidden" id="retraite-regpub-idx"/>
    <div style="padding:20px 24px">
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Montant estimé</label>
          <div class="input-sfx"><input class="form-input" id="retraite-regpub-montant" type="text" placeholder="0"/><span class="sfx">$</span></div>
        </div>
        <div class="col form-group">
          <label class="form-label">Fréquence</label>
          <select class="form-select" id="retraite-regpub-frequence">
            <option value="mensuel">Mensuel</option>
            <option value="annuel">Annuel</option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col form-group">
          <label class="form-label" style="display:flex;align-items:center;gap:6px">Âge de début <span class="abf-tooltip-wrap"><span class="abf-tooltip-icon">&#9432;</span><span class="abf-tooltip-box">Âge auquel vous prévoyez commencer à recevoir cette prestation.</span></span></label>
          <input class="form-input" id="retraite-regpub-debut" type="text" placeholder="65"/>
        </div>
        <div class="col form-group">
          <label class="form-label">Indexé?</label>
          <div style="display:flex;gap:8px;margin-top:8px">
            <label class="fu-radio-pill"><input type="radio" name="retraite-regpub-indexe" value="oui" checked/> Oui</label>
            <label class="fu-radio-pill"><input type="radio" name="retraite-regpub-indexe" value="non"/> Non</label>
          </div>
        </div>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
      <button class="btn btn-secondary" onclick="closeRetraiteRegPubModal()">Annuler</button>
      <button class="btn btn-primary"   onclick="saveRetraiteRegPub()">Enregistrer</button>
    </div>
  </div>
</div>

<!-- ═══ Modal : Régime à prestations déterminées ══════════════════════ -->
<div id="modal-retraite-rpd" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
  <div style="background:white;border-radius:12px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <h4 id="retraite-rpd-modal-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Régime à prestations déterminées</h4>
      <button onclick="closeRetraiteRpdModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
    </div>
    <input type="hidden" id="retraite-rpd-edit-id"/>
    <div style="padding:20px 24px">
      <div class="form-group">
        <label class="form-label">Nom du régime</label>
        <input class="form-input" id="retraite-rpd-nom" type="text" placeholder="ex. Régime de retraite RREGOP…"/>
      </div>
      <div class="form-group">
        <label class="form-label">Assuré(e)</label>
        <select class="form-select" id="retraite-rpd-role">
          <option value="client">Client</option>
          <option value="conjoint">Conjoint(e)</option>
        </select>
      </div>
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Rente annuelle estimée</label>
          <div class="input-sfx"><input class="form-input" id="retraite-rpd-montant" type="text" placeholder="0"/><span class="sfx">$</span></div>
        </div>
        <div class="col form-group">
          <label class="form-label">Fréquence</label>
          <select class="form-select" id="retraite-rpd-frequence">
            <option value="annuel">Annuel</option>
            <option value="mensuel">Mensuel</option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Âge de début</label>
          <input class="form-input" id="retraite-rpd-debut" type="text" placeholder="60"/>
        </div>
        <div class="col form-group">
          <label class="form-label">Indexé?</label>
          <div style="display:flex;gap:8px;margin-top:8px">
            <label class="fu-radio-pill"><input type="radio" name="retraite-rpd-indexe" value="oui" checked/> Oui</label>
            <label class="fu-radio-pill"><input type="radio" name="retraite-rpd-indexe" value="non"/> Non</label>
          </div>
        </div>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Notes</label>
        <textarea class="form-input" id="retraite-rpd-notes" rows="2" placeholder="Remarques…" style="resize:vertical"></textarea>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:#f8f9fd">
      <button id="retraite-rpd-delete-btn" class="btn btn-danger btn-sm" style="display:none" onclick="deleteRetraiteRpd()">Supprimer</button>
      <div style="display:flex;gap:10px;margin-left:auto">
        <button class="btn btn-secondary" onclick="closeRetraiteRpdModal()">Annuler</button>
        <button class="btn btn-primary"   onclick="saveRetraiteRpd()">Enregistrer</button>
      </div>
    </div>
  </div>
</div>

<!-- ═══ Modal : Retrait planifié ══════════════════════════════════════ -->
<div id="modal-retraite-retrait" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
  <div style="background:white;border-radius:12px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <h4 id="retraite-retrait-modal-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Retrait planifié</h4>
      <button onclick="closeRetraiteRetraitModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
    </div>
    <input type="hidden" id="retraite-retrait-edit-id"/>
    <div style="padding:20px 24px">
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Type de compte</label>
          <select class="form-select" id="retraite-retrait-type">
            <option value="REER">REER</option>
            <option value="CELI">CELI</option>
            <option value="FERR">FERR</option>
            <option value="REER collectif">REER collectif</option>
            <option value="CRI/FRV">CRI/FRV</option>
            <option value="RPDB">RPDB</option>
            <option value="Non enregistré">Non enregistré</option>
            <option value="Autre">Autre</option>
          </select>
        </div>
        <div class="col form-group">
          <label class="form-label">Titulaire</label>
          <select class="form-select" id="retraite-retrait-role">
            <option value="client">Client</option>
            <option value="conjoint">Conjoint(e)</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <input class="form-input" id="retraite-retrait-desc" type="text" placeholder="ex. Retrait FERR annuel…"/>
      </div>
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Montant</label>
          <div class="input-sfx"><input class="form-input" id="retraite-retrait-montant" type="text" placeholder="0"/><span class="sfx">$</span></div>
        </div>
        <div class="col form-group">
          <label class="form-label">Fréquence</label>
          <select class="form-select" id="retraite-retrait-frequence">
            <option value="annuel">Annuel</option>
            <option value="mensuel">Mensuel</option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Âge début</label>
          <input class="form-input" id="retraite-retrait-debut" type="text" placeholder="65"/>
        </div>
        <div class="col form-group">
          <label class="form-label">Âge fin</label>
          <input class="form-input" id="retraite-retrait-fin" type="text" placeholder="décès"/>
        </div>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:#f8f9fd">
      <button id="retraite-retrait-delete-btn" class="btn btn-danger btn-sm" style="display:none" onclick="deleteRetraiteRetrait()">Supprimer</button>
      <div style="display:flex;gap:10px;margin-left:auto">
        <button class="btn btn-secondary" onclick="closeRetraiteRetraitModal()">Annuler</button>
        <button class="btn btn-primary"   onclick="saveRetraiteRetrait()">Enregistrer</button>
      </div>
    </div>
  </div>
</div>

<!-- ═══ Modal : Actif alloué ══════════════════════════════════════════ -->
<div id="modal-retraite-actif" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
  <div style="background:white;border-radius:12px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <h4 id="retraite-actif-modal-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Actif alloué</h4>
      <button onclick="closeRetraiteActifModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
    </div>
    <input type="hidden" id="retraite-actif-edit-id"/>
    <div style="padding:20px 24px">
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Type</label>
          <select class="form-select" id="retraite-actif-type">
            <option value="REER">REER</option>
            <option value="CELI">CELI</option>
            <option value="FERR">FERR</option>
            <option value="REER collectif">REER collectif</option>
            <option value="CRI/FRV">CRI/FRV</option>
            <option value="RPDB">RPDB</option>
            <option value="Non enregistré">Non enregistré</option>
            <option value="Immeuble">Immeuble</option>
            <option value="Entreprise">Entreprise</option>
            <option value="Autre">Autre</option>
          </select>
        </div>
        <div class="col form-group">
          <label class="form-label">Valeur actuelle</label>
          <div class="input-sfx"><input class="form-input" id="retraite-actif-valeur" type="text" placeholder="0"/><span class="sfx">$</span></div>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <input class="form-input" id="retraite-actif-desc" type="text" placeholder="ex. REER chez Banque XYZ…"/>
      </div>
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Stratégie avant retraite</label>
          <select class="form-select" id="retraite-actif-strat-avant">
            <option value="prudent">Prudent</option>
            <option value="modere">Modéré</option>
            <option value="equilibre" selected>Équilibré</option>
            <option value="croissance">Croissance</option>
            <option value="audacieux">Audacieux</option>
          </select>
        </div>
        <div class="col form-group">
          <label class="form-label">Stratégie pendant retraite</label>
          <select class="form-select" id="retraite-actif-strat-pendant">
            <option value="prudent" selected>Prudent</option>
            <option value="modere">Modéré</option>
            <option value="equilibre">Équilibré</option>
            <option value="croissance">Croissance</option>
            <option value="audacieux">Audacieux</option>
          </select>
        </div>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:#f8f9fd">
      <button id="retraite-actif-delete-btn" class="btn btn-danger btn-sm" style="display:none" onclick="deleteRetraiteActif()">Supprimer</button>
      <div style="display:flex;gap:10px;margin-left:auto">
        <button class="btn btn-secondary" onclick="closeRetraiteActifModal()">Annuler</button>
        <button class="btn btn-primary"   onclick="saveRetraiteActif()">Enregistrer</button>
      </div>
    </div>
  </div>
</div>
