    <!-- ── PAGE: Informations personnelles ── -->
    <div id="page-infos-perso" class="page active">
      <div class="page-title">Informations personnelles</div>
      <div class="page-subtitle">Renseignements du client principal</div>

      <!-- Client -->
      <div class="card">
        <div class="card-header">Client</div>
        <div class="card-body">
          <div class="row">
            <div class="col form-group">
              <label class="form-label required">Prénom</label>
              <input class="form-input" id="client-prenom" type="text" value="" placeholder="Prénom"/>
            </div>
            <div class="col form-group">
              <label class="form-label required">Nom</label>
              <input class="form-input" id="client-nom" type="text" value="" placeholder="Nom de famille"/>
            </div>
          </div>
          <div class="row">
            <div class="col form-group">
              <label class="form-label required">Date de naissance</label>
              <div class="date-row">
                <input class="form-input" id="client-ddn-jour" type="text" value="" placeholder="Jour" style="max-width:70px" maxlength="2" oninput="this.value=this.value.replace(/\D/g,'').slice(0,2)"/>
                <select class="form-select" id="client-ddn-mois">
                  <option value="">Mois</option>
                  <option>Janvier</option><option>Février</option><option>Mars</option>
                  <option>Avril</option><option>Mai</option><option>Juin</option>
                  <option>Juillet</option><option>Août</option><option>Septembre</option>
                  <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                </select>
                <input class="form-input" type="text" value="" placeholder="Année" style="max-width:90px" id="client-naissance-annee" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
              </div>
            </div>
            <div class="col form-group">
              <label class="form-label">Sexe</label>
              <div class="radio-group">
                <div class="radio-pill"><input type="radio" name="sexe" id="masculin" value="M"/><label for="masculin">Masculin</label></div>
                <div class="radio-pill"><input type="radio" name="sexe" id="feminin" value="F"/><label for="feminin">Féminin</label></div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col form-group">
              <label class="form-label required">État civil</label>
              <select class="form-select" id="client-etat-civil" onchange="syncConjointInfo()">
                <option value="">— Sélectionner —</option>
                <option>Marié(e)</option><option>Célibataire</option>
                <option>Divorcé(e)</option><option>Séparé(e)</option>
                <option>Conjoint(e) de fait</option><option>Union civile</option><option>Veuf/veuve</option>
              </select>
            </div>
            <div class="col form-group">
              <label class="form-label required">Province d'imposition</label>
              <select class="form-select" id="client-province">
                <option>Alberta</option><option>Colombie-Britannique</option>
                <option>Île-du-Prince-Édouard</option><option>Manitoba</option>
                <option>Nouveau-Brunswick</option><option>Nouvelle-Écosse</option>
                <option>Nunavut</option><option>Ontario</option>
                <option selected>Québec</option><option>Saskatchewan</option>
                <option>Terre-Neuve-et-Labrador</option>
                <option>Territoires du Nord-Ouest</option><option>Yukon</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col form-group">
              <label class="form-label">Courriel personnel</label>
              <input class="form-input" id="client-courriel" type="email" value="" placeholder="courriel@exemple.com"
                pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Format requis : courriel@domaine.com"/>
            </div>
            <div class="col form-group">
              <label class="form-label required">Réside au Canada depuis</label>
              <input class="form-input" type="text" placeholder="Année (ex: 2010)" id="client-canada-depuis" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
            </div>
          </div>
          <div class="row">
            <div class="col form-group">
              <label class="form-label required">Usage de tabac</label>
              <div class="radio-group">
                <div class="radio-pill"><input type="radio" name="tabac" id="tabac-oui" value="oui"/><label for="tabac-oui">Oui</label></div>
                <div class="radio-pill"><input type="radio" name="tabac" id="tabac-non" value="non" checked/><label for="tabac-non">Non</label></div>
              </div>
            </div>
            <div class="col form-group">
              <label class="form-label">Langue</label>
              <div class="radio-group">
                <div class="radio-pill"><input type="radio" name="langue" id="fr" value="fr" checked/><label for="fr">Français</label></div>
                <div class="radio-pill"><input type="radio" name="langue" id="en" value="en"/><label for="en">Anglais</label></div>
              </div>
            </div>
          </div>
        </div>
        <!-- Informations supplémentaires -->
        <button class="collapse-toggle" onclick="toggleCollapse(this)">
          Informations supplémentaires
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <div class="collapse-body">
          <!-- Téléphones -->
          <div class="row">
            <div class="col form-group">
              <label class="form-label">Cellulaire</label>
              <input class="form-input" id="client-cellulaire" type="tel" value="" placeholder="514 000-0000" maxlength="12"
                oninput="const d=this.value.replace(/\D/g,'').slice(0,10);this.value=d.length<=3?d:d.length<=6?d.slice(0,3)+' '+d.slice(3):d.slice(0,3)+' '+d.slice(3,6)+'-'+d.slice(6)"/>
            </div>
            <div class="col form-group">
              <label class="form-label">Téléphone domicile</label>
              <input class="form-input" id="client-telephone" type="tel" placeholder="514 000-0000" maxlength="12"
                oninput="const d=this.value.replace(/\D/g,'').slice(0,10);this.value=d.length<=3?d:d.length<=6?d.slice(0,3)+' '+d.slice(3):d.slice(0,3)+' '+d.slice(3,6)+'-'+d.slice(6)"/>
            </div>
          </div>
          <!-- Adresse structurée -->
          <div style="margin-bottom:4px">
            <label class="form-label required" style="margin-bottom:10px;display:block">Adresse</label>
            <div class="row">
              <div class="col form-group" style="max-width:120px">
                <label class="form-label">N° civique</label>
                <input class="form-input" type="text" id="client-addr-civique" value="" oninput="syncConjointInfo()"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Rue</label>
                <input class="form-input" type="text" id="client-addr-rue" value="" oninput="syncConjointInfo()"/>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Type d'unité</label>
                <select class="form-select" id="client-addr-type-unite" onchange="syncConjointInfo()">
                  <option value="">—</option><option selected>Appartement</option><option>Suite</option>
                  <option>Bureau</option><option>Unité</option>
                </select>
              </div>
              <div class="col form-group" style="max-width:100px">
                <label class="form-label">Numéro</label>
                <input class="form-input" type="text" id="client-addr-numero" value="" oninput="syncConjointInfo()"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Case postale</label>
                <input class="form-input" type="text" id="client-addr-case" placeholder="—" oninput="syncConjointInfo()"/>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Ville</label>
                <input class="form-input" type="text" id="client-addr-ville" value="" oninput="syncConjointInfo()"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Province</label>
                <select class="form-select" id="client-addr-province" onchange="syncConjointInfo()">
                  <option value="">—</option>
                  <option>Alberta</option><option>Colombie-Britannique</option>
                  <option>Île-du-Prince-Édouard</option><option>Manitoba</option>
                  <option>Nouveau-Brunswick</option><option>Nouvelle-Écosse</option>
                  <option>Nunavut</option><option>Ontario</option>
                  <option selected>Québec</option><option>Saskatchewan</option>
                  <option>Terre-Neuve-et-Labrador</option>
                  <option>Territoires du Nord-Ouest</option><option>Yukon</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label">Code postal</label>
                <input class="form-input" type="text" id="client-addr-postal" value="" placeholder="A1A 1A1" maxlength="7"
                  oninput="const v=this.value.replace(/[^a-zA-Z0-9]/g,'').toUpperCase().slice(0,6);this.value=v.length>3?v.slice(0,3)+' '+v.slice(3):v;syncConjointInfo()"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Pays</label>
                <input class="form-input" type="text" id="client-addr-pays" value="Canada" disabled style="background:#f8f9fd"/>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Type de plan -->
      <div class="card">
        <div class="card-header">Type de plan</div>
        <div class="card-body">
          <div class="radio-group">
            <div class="radio-pill">
              <input type="radio" name="plan" id="individuel" value="individuel" checked
                onchange="document.getElementById('conjoint-section').style.display='none'"/>
              <label for="individuel">Individuel</label>
            </div>
            <div class="radio-pill">
              <input type="radio" name="plan" id="conjoint" value="conjoint"
                onchange="document.getElementById('conjoint-section').style.display='block';syncConjointInfo()"/>
              <label for="conjoint">Conjoint</label>
            </div>
          </div>
        </div>
      </div>

      <!-- Section conjoint(e) — masquée par défaut -->
      <div id="conjoint-section" style="display:none">
        <div class="card">
          <div class="card-header" style="background:#f0f3fa;display:flex;align-items:center;justify-content:space-between">
            <span>Conjoint(e)</span>
            <div style="display:flex;gap:12px;font-size:12px">
              <a href="#" style="color:var(--gold);text-decoration:none;font-weight:600">Supprimer le conjoint</a>
              <a href="#" style="color:var(--navy);text-decoration:none;font-weight:600">Changer le conjoint</a>
            </div>
          </div>
          <div class="card-body">
            <!-- Recherche client existant -->
            <div class="row" style="margin-bottom:8px">
              <div class="col-full form-group">
                <label class="form-label">Rechercher un client existant</label>
                <div style="position:relative">
                  <input class="form-input" type="text" placeholder="Commencez à taper le nom…"
                    style="padding-left:36px"/>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"
                    style="position:absolute;left:10px;top:50%;transform:translateY(-50%)">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                  </svg>
                </div>
              </div>
            </div>
            <!-- Prénom / Nom -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label required">Prénom</label>
                <input class="form-input" id="conjoint-prenom" type="text" placeholder="Prénom"/>
              </div>
              <div class="col form-group">
                <label class="form-label required">Nom</label>
                <input class="form-input" id="conjoint-nom" type="text" placeholder="Nom de famille"/>
              </div>
            </div>
            <!-- Date de naissance / Sexe -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label required">Date de naissance</label>
                <div class="date-row">
                  <input class="form-input" id="conjoint-ddn-jour" type="text" placeholder="Jour" style="max-width:70px" maxlength="2" oninput="this.value=this.value.replace(/\D/g,'').slice(0,2)"/>
                  <select class="form-select" id="conjoint-ddn-mois">
                    <option value="">Mois</option>
                    <option>Janvier</option><option>Février</option><option>Mars</option>
                    <option>Avril</option><option>Mai</option><option>Juin</option>
                    <option>Juillet</option><option>Août</option><option>Septembre</option>
                    <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                  </select>
                  <input class="form-input" type="text" placeholder="Année" style="max-width:90px" id="conjoint-naissance-annee" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
                </div>
              </div>
              <div class="col form-group">
                <label class="form-label">Sexe</label>
                <div class="radio-group">
                  <div class="radio-pill"><input type="radio" name="co-sexe" id="co-masculin"/><label for="co-masculin">Masculin</label></div>
                  <div class="radio-pill"><input type="radio" name="co-sexe" id="co-feminin"/><label for="co-feminin">Féminin</label></div>
                </div>
              </div>
            </div>
            <!-- État civil / Province -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label required">État civil</label>
                <select class="form-select" id="conjoint-etat-civil">
                  <option value="">Sélectionnez…</option>
                  <option>Marié(e)</option><option>Célibataire</option>
                  <option>Divorcé(e)</option><option>Séparé(e)</option>
                  <option>Conjoint(e) de fait</option><option>Union civile</option><option>Veuf/veuve</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label required">Province d'imposition</label>
                <select class="form-select" id="conjoint-province">
                  <option>Alberta</option><option>Colombie-Britannique</option>
                  <option>Île-du-Prince-Édouard</option><option>Manitoba</option>
                  <option>Nouveau-Brunswick</option><option>Nouvelle-Écosse</option>
                  <option>Nunavut</option><option>Ontario</option>
                  <option selected>Québec</option><option>Saskatchewan</option>
                  <option>Terre-Neuve-et-Labrador</option>
                  <option>Territoires du Nord-Ouest</option><option>Yukon</option>
                </select>
              </div>
            </div>
            <!-- Courriel / Année Canada -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Courriel personnel</label>
                <input class="form-input" id="conjoint-courriel" type="email" placeholder="courriel@exemple.com"
                  pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Format requis : courriel@domaine.com"/>
              </div>
              <div class="col form-group">
                <label class="form-label required">Réside au Canada depuis</label>
                <input class="form-input" type="text" placeholder="Année (ex: 2010)" id="conjoint-canada-depuis" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
              </div>
            </div>
            <!-- Tabac / Langue -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label required">Usage de tabac</label>
                <div class="radio-group">
                  <div class="radio-pill"><input type="radio" name="co-tabac" id="co-tabac-oui"/><label for="co-tabac-oui">Oui</label></div>
                  <div class="radio-pill"><input type="radio" name="co-tabac" id="co-tabac-non" checked/><label for="co-tabac-non">Non</label></div>
                </div>
              </div>
              <div class="col form-group">
                <label class="form-label">Langue</label>
                <div class="radio-group">
                  <div class="radio-pill"><input type="radio" name="co-langue" id="co-fr" checked/><label for="co-fr">Français</label></div>
                  <div class="radio-pill"><input type="radio" name="co-langue" id="co-en"/><label for="co-en">Anglais</label></div>
                </div>
              </div>
            </div>
          </div>
          <!-- Adresse conjoint structurée -->
          <div class="card-body" style="padding-top:0">
            <label class="form-label required" style="margin-bottom:10px;display:block">Adresse</label>
            <div class="row">
              <div class="col form-group" style="max-width:120px">
                <label class="form-label">N° civique</label>
                <input class="form-input" type="text" id="conjoint-addr-civique"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Rue</label>
                <input class="form-input" type="text" id="conjoint-addr-rue"/>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Type d'unité</label>
                <select class="form-select" id="conjoint-addr-type-unite">
                  <option value="">—</option><option>Appartement</option><option>Suite</option>
                  <option>Bureau</option><option>Unité</option>
                </select>
              </div>
              <div class="col form-group" style="max-width:100px">
                <label class="form-label">Numéro</label>
                <input class="form-input" type="text" id="conjoint-addr-numero"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Case postale</label>
                <input class="form-input" type="text" id="conjoint-addr-case" placeholder="—"/>
              </div>
            </div>
            <div class="row" style="margin-bottom:0">
              <div class="col form-group">
                <label class="form-label">Ville</label>
                <input class="form-input" type="text" id="conjoint-addr-ville"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Province</label>
                <select class="form-select" id="conjoint-addr-province">
                  <option value="">—</option>
                  <option>Alberta</option><option>Colombie-Britannique</option>
                  <option>Île-du-Prince-Édouard</option><option>Manitoba</option>
                  <option>Nouveau-Brunswick</option><option>Nouvelle-Écosse</option>
                  <option>Nunavut</option><option>Ontario</option>
                  <option selected>Québec</option><option>Saskatchewan</option>
                  <option>Terre-Neuve-et-Labrador</option>
                  <option>Territoires du Nord-Ouest</option><option>Yukon</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label">Code postal</label>
                <input class="form-input" type="text" id="conjoint-addr-postal" placeholder="A1A 1A1" maxlength="7"
                  oninput="const v=this.value.replace(/[^a-zA-Z0-9]/g,'').toUpperCase().slice(0,6);this.value=v.length>3?v.slice(0,3)+' '+v.slice(3):v"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Pays</label>
                <input class="form-input" type="text" id="conjoint-addr-pays" value="Canada" disabled style="background:#f8f9fd"/>
              </div>
            </div>
          </div>
          <!-- Infos supp. conjoint collapsible -->
          <button class="collapse-toggle" onclick="toggleCollapse(this)">
            Informations supplémentaires
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div class="collapse-body">
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Cellulaire</label>
                <input class="form-input" id="conjoint-cellulaire" type="tel" placeholder="514 000-0000" maxlength="12"
                  oninput="const d=this.value.replace(/\D/g,'').slice(0,10);this.value=d.length<=3?d:d.length<=6?d.slice(0,3)+' '+d.slice(3):d.slice(0,3)+' '+d.slice(3,6)+'-'+d.slice(6)"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Téléphone domicile</label>
                <input class="form-input" id="conjoint-telephone" type="tel" placeholder="514 000-0000" maxlength="12"
                  oninput="const d=this.value.replace(/\D/g,'').slice(0,10);this.value=d.length<=3?d:d.length<=6?d.slice(0,3)+' '+d.slice(3):d.slice(0,3)+' '+d.slice(3,6)+'-'+d.slice(6)"/>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enfants -->
      <div class="card">
        <div class="card-header">Enfant(s) et personne(s) à charge</div>
        <div class="card-body">
          <div id="enfants-list" class="list-empty">Aucun enfant ou personne à charge ajouté.</div>
          <button class="btn btn-primary btn-sm" style="margin-top:12px" onclick="openEnfantModal()">
            <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
            Ajouter
          </button>
        </div>
      </div>

      <!-- Renseignements légaux -->
      <div class="card" style="overflow:visible">
        <div class="card-header">Renseignements légaux</div>
        <div class="card-body" style="overflow:visible">
          <div id="legal-list" class="list-empty">Aucun document légal ajouté.</div>
          <!-- Bouton Ajouter + dropdown menu -->
          <div style="position:relative;display:inline-block;margin-top:12px" id="legal-menu-wrapper">
            <button class="btn btn-primary btn-sm" onclick="toggleLegalMenu(event)">
              <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
              Ajouter
            </button>
            <div id="legal-dropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;z-index:200;
              background:white;border:1px solid var(--border);border-radius:8px;
              box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:260px;overflow:hidden">
              <ul style="list-style:none;padding:4px 0;margin:0">
                <li><button class="legal-menu-item" onclick="openLegalModal('Contrat de mariage')">Contrat de mariage</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Contrat de vie commune')">Contrat de vie commune</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Acte d\'union civile')">Acte d'union civile</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Jugement de divorce')">Jugement de divorce</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Jugement de séparation de corps')">Jugement de séparation de corps</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Testament')">Testament</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Mandat de protection')">Mandat de protection</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Ordonnance de pension alimentaire')">Ordonnance de pension alimentaire</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Convention d\'achat/vente')">Convention d'achat/vente</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Engagement financier envers quelqu\'un')">Engagement financier envers quelqu'un</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Autre')">Autre</button></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- ── MODAL : Enfant ou personne à charge ── -->
      <div id="modal-enfant" style="display:none;position:fixed;inset:0;z-index:1000;
        background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:560px;
          box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <!-- Modal header -->
          <div style="padding:20px 24px 16px;border-bottom:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between">
            <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Enfant ou personne à charge</h4>
            <button onclick="closeEnfantModal()" style="background:none;border:none;font-size:20px;
              color:var(--muted);cursor:pointer;line-height:1;padding:0 4px">×</button>
          </div>
          <!-- Modal body -->
          <div style="padding:20px 24px;max-height:70vh;overflow-y:auto">
            <!-- Recherche client -->
            <div class="form-group" style="margin-bottom:16px">
              <label class="form-label">Rechercher un client existant</label>
              <div style="position:relative">
                <input class="form-input" type="text" placeholder="Commencez à taper le nom…" style="padding-left:36px"/>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"
                  style="position:absolute;left:10px;top:50%;transform:translateY(-50%)">
                  <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
              </div>
            </div>
            <!-- Prénom / Nom -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label required">Prénom</label>
                <input class="form-input" id="enf-prenom" type="text"/>
              </div>
              <div class="col form-group">
                <label class="form-label required">Nom</label>
                <input class="form-input" id="enf-nom" type="text"/>
              </div>
            </div>
            <!-- Sexe / Date naissance -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Sexe</label>
                <select class="form-select" id="enf-sexe">
                  <option value="">Sélectionnez…</option>
                  <option value="M">Masculin</option>
                  <option value="F">Féminin</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label">Date de naissance</label>
                <div class="date-row">
                  <input class="form-input" id="enf-jour" type="text" placeholder="Jour" style="max-width:65px" maxlength="2" oninput="this.value=this.value.replace(/\D/g,'').slice(0,2)"/>
                  <select class="form-select" id="enf-mois">
                    <option value="">Mois</option>
                    <option>Janvier</option><option>Février</option><option>Mars</option>
                    <option>Avril</option><option>Mai</option><option>Juin</option>
                    <option>Juillet</option><option>Août</option><option>Septembre</option>
                    <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                  </select>
                  <input class="form-input" id="enf-annee" type="text" placeholder="Année" style="max-width:80px" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
                </div>
              </div>
            </div>
            <!-- Relation / À la charge -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label" id="enf-relation-label">Relation avec le client</label>
                <select class="form-select" id="enf-relation">
                  <option value="">Sélectionnez…</option>
                  <option value="child">Enfant</option>
                  <option value="dependent">Autre</option>
                  <option value="fathermother">Père-Mère</option>
                  <option value="grandparent">Grand-parent</option>
                  <option value="grandchild">Petit-enfant</option>
                  <option value="sibling">Frère-Sœur</option>
                  <option value="otherrelative">Parenté</option>
                  <option value="exspouse">Ex-conjoint(e)</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label">À la charge de</label>
                <select class="form-select" id="enf-charge">
                  <option value="">Sélectionnez…</option>
                </select>
              </div>
            </div>
          </div>
          <!-- Modal footer -->
          <div style="padding:14px 24px;border-top:1px solid var(--border);
            display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closeEnfantModal()">Annuler</button>
            <button class="btn btn-primary" id="enf-submit" onclick="saveEnfant()">Enregistrer</button>
          </div>
        </div>
      </div>
    </div>

      <!-- ── MODAL : Document légal ── -->
      <div id="modal-legal" style="display:none;position:fixed;inset:0;z-index:1000;
        background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:520px;
          box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <!-- Modal header -->
          <div style="padding:20px 24px 16px;border-bottom:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between">
            <h4 id="modal-legal-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Document légal</h4>
            <button onclick="closeLegalModal()" style="background:none;border:none;font-size:20px;
              color:var(--muted);cursor:pointer;line-height:1;padding:0 4px">×</button>
          </div>
          <!-- Modal body -->
          <div style="padding:20px 24px;max-height:70vh;overflow-y:auto">
            <!-- Propriétaire -->
            <div class="form-group">
              <label class="form-label required">Propriétaire</label>
              <select class="form-select" id="leg-proprietaire">
                <option value="">Sélectionnez…</option>
              </select>
            </div>
            <!-- Facultatif section -->
            <div class="modal-facultatif-title">Facultatif</div>
            <!-- Date -->
            <div class="form-group">
              <label class="form-label">Date</label>
              <div class="date-row">
                <input class="form-input" id="leg-jour" type="text" placeholder="Jour" style="max-width:65px" maxlength="2" oninput="this.value=this.value.replace(/\D/g,'').slice(0,2)"/>
                <select class="form-select" id="leg-mois">
                  <option value="">Mois</option>
                  <option>Janvier</option><option>Février</option><option>Mars</option>
                  <option>Avril</option><option>Mai</option><option>Juin</option>
                  <option>Juillet</option><option>Août</option><option>Septembre</option>
                  <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                </select>
                <input class="form-input" id="leg-annee" type="text" placeholder="Année" style="max-width:80px" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
              </div>
            </div>
            <!-- Type -->
            <div class="form-group">
              <label class="form-label">Type</label>
              <select class="form-select" id="leg-type">
                <option value="">Sélectionnez…</option>
                <option value="enfants">Enfants</option>
                <option value="conjoint">Conjoint</option>
              </select>
            </div>
            <!-- Note -->
            <div class="form-group">
              <label class="form-label">Note</label>
              <textarea class="form-input" id="leg-note" rows="3" style="resize:vertical"></textarea>
            </div>
          </div>
          <!-- Modal footer -->
          <div style="padding:14px 24px;border-top:1px solid var(--border);
            display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closeLegalModal()">Annuler</button>
            <button class="btn btn-primary" onclick="saveLegal()">Enregistrer</button>
          </div>
        </div>
      </div>

