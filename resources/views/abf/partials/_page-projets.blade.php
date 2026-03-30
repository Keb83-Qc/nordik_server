<!-- ── PAGE: Projets ── -->
<div id="page-projets" class="page">
  <div class="page-title">Projets</div>
  <div class="page-subtitle">Projets financiers et achats importants à planifier</div>

  <!-- Onglets projets -->
  <div style="display:flex;align-items:center;gap:8px;border-bottom:2px solid var(--border);margin-bottom:0">
    <div id="projets-tabs-nav" style="display:flex;gap:0;flex:1;overflow-x:auto">
      <!-- Onglets générés dynamiquement par JS -->
    </div>
    <button class="btn btn-primary btn-sm" style="margin-bottom:2px;flex-shrink:0" onclick="projetsAdd()">
      <svg viewBox="0 0 26 24" width="13" height="13" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
      Ajouter un projet
    </button>
  </div>

  <!-- État vide -->
  <div id="projets-empty" style="text-align:center;padding:48px 20px">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" style="margin-bottom:12px;opacity:.3">
      <rect width="24" height="24" rx="6" fill="var(--navy)"/>
      <path d="M12 5v14M5 12h14" stroke="white" stroke-width="2" stroke-linecap="round"/>
    </svg>
    <div style="font-size:14px;font-weight:600;color:var(--navy);margin-bottom:6px">Aucun projet ajouté</div>
    <div style="font-size:13px;color:var(--muted)">Cliquez sur "Ajouter un projet" pour planifier un achat important.</div>
  </div>

  <!-- Contenu des projets (onglets) -->
  <div id="projets-panels" style="display:none;padding-top:16px">
    <!-- Généré dynamiquement -->
  </div>

</div><!-- /page-projets -->

<!-- Modal: Projet -->
<div id="modal-projet" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
  <div style="background:white;border-radius:12px;width:100%;max-width:540px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <h4 id="modal-projet-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Nouveau projet</h4>
      <button onclick="closeProjetModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
    </div>
    <div style="padding:20px 24px">
      <input type="hidden" id="projet-edit-id" value=""/>

      <!-- Achat première propriété -->
      <div class="form-group" style="margin-bottom:14px">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:500;padding:10px 12px;background:#f8f9fd;border-radius:8px;border:1px solid var(--border)">
          <input type="checkbox" id="projet-celiapp" style="width:16px;height:16px;accent-color:var(--navy);flex-shrink:0"/>
          <span>
            Achat d'une première propriété admissible au CELIAPP / RAP
            <span class="abf-tooltip-wrap">
              <span class="abf-tooltip-icon">&#9432;</span>
              <span class="abf-tooltip-box">Si ce projet est l'achat d'une première propriété admissible, les retraits CELIAPP sont non imposables et le RAP du REER peut être utilisé. Un retrait CELIAPP ne réduit pas le plafond de cotisation futur.</span>
            </span>
          </span>
        </label>
      </div>

      <!-- Description -->
      <div class="form-group">
        <label class="form-label">Description du projet</label>
        <input class="form-input" id="projet-description" type="text" placeholder="ex. Achat d'un véhicule, rénovations, voyage…"/>
      </div>

      <div class="row">
        <!-- Montant -->
        <div class="col form-group">
          <label class="form-label">Montant estimé</label>
          <div class="input-sfx">
            <input class="form-input" id="projet-montant" type="text" placeholder="0"/>
            <span class="sfx">$</span>
          </div>
        </div>
        <!-- Date prévue -->
        <div class="col form-group">
          <label class="form-label">Date prévue</label>
          <div style="display:flex;gap:6px">
            <select class="form-select" id="projet-mois" style="flex:1">
              <option value="1">Janvier</option>
              <option value="2">Février</option>
              <option value="3">Mars</option>
              <option value="4">Avril</option>
              <option value="5">Mai</option>
              <option value="6">Juin</option>
              <option value="7">Juillet</option>
              <option value="8">Août</option>
              <option value="9">Septembre</option>
              <option value="10">Octobre</option>
              <option value="11">Novembre</option>
              <option value="12">Décembre</option>
            </select>
            <input class="form-input" id="projet-annee" type="text" placeholder="{{ date('Y') + 2 }}" style="width:75px;text-align:center"/>
          </div>
        </div>
      </div>

      <!-- Notes -->
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Notes <span style="font-weight:400;color:var(--muted)">(facultatif)</span></label>
        <textarea class="form-input" id="projet-notes" rows="2" style="resize:vertical" placeholder="Commentaires supplémentaires…"></textarea>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:#f8f9fd">
      <button id="projet-delete-btn" style="display:none" class="btn btn-sm" onclick="projetsDelete()" style="color:#ef4444;background:none;border:1px solid #ef4444">
        <svg viewBox="0 0 24 24" width="13" height="13" fill="#ef4444"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
        Supprimer
      </button>
      <div style="display:flex;gap:10px;margin-left:auto">
        <button class="btn btn-secondary" onclick="closeProjetModal()">Annuler</button>
        <button class="btn btn-primary" onclick="saveProjet()">Enregistrer</button>
      </div>
    </div>
  </div>
</div>
