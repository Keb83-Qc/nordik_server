  // ── Gestionnaire d'erreurs JS → /log-js-error ─────────────────────
  (function () {
    function sendJsError(data) {
      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        fetch('/log-js-error', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
          body: JSON.stringify(data),
          keepalive: true,
        });
      } catch (_) { /* silencieux */ }
    }

    window.onerror = function (message, source, line, column, error) {
      // Filtrer les erreurs venant de scripts externes (extensions, etc.)
      if (source && !source.includes(window.location.hostname) && !source.includes('abf-editor')) return false;
      sendJsError({
        type: 'js_error',
        message: String(message).slice(0, 300),
        source: String(source || '').slice(0, 300),
        line: line,
        column: column,
        stack: error?.stack ? String(error.stack).slice(0, 600) : '',
        url: window.location.href.slice(0, 300),
      });
      return false;
    };

    window.addEventListener('unhandledrejection', function (event) {
      const reason = event.reason;
      const message = reason instanceof Error ? reason.message : String(reason);
      sendJsError({
        type: 'unhandled_promise',
        message: String(message).slice(0, 300),
        source: '',
        line: '',
        column: '',
        stack: reason instanceof Error && reason.stack ? String(reason.stack).slice(0, 600) : '',
        url: window.location.href.slice(0, 300),
      });
    });
  })();
  // ──────────────────────────────────────────────────────────────────

  const pages = [
    'infos-perso','objectifs','actifs-passifs','revenu-epargne',
    'fonds-urgence','deces','invalidite','maladie-grave',
    'projets','retraite','recommandations','rapport'
  ];
  let current = 0;

  // ── Variables globales pour les sections ajoutées ──────────────
  let _retraiteDepenses = [];
  let _projets = [];
  let _recomNotes = {};
  let _rapportSelectedPhoto = null;
  const _moisNoms = ['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
  const _mgCoveragePresets = {
    aucun:     { traitement:0,    equipement:0,    adaptation:0,     vehicule:0,    transport:0,    'aide-domicile':0,    'soins-professionnel':0, 'medicaments-rec':0 },
    base:      { traitement:5000, equipement:5000, adaptation:0,     vehicule:0,    transport:0,    'aide-domicile':0,    'soins-professionnel':0, 'medicaments-rec':0 },
    confort:   { traitement:5000, equipement:5000, adaptation:12000, vehicule:0,    transport:3000, 'aide-domicile':0,    'soins-professionnel':500,'medicaments-rec':200},
    superieur: { traitement:5000, equipement:5000, adaptation:12000, vehicule:8000, transport:3000, 'aide-domicile':2000, 'soins-professionnel':800,'medicaments-rec':300},
  };

  function syncConjointInfo() {
    if (!document.getElementById('conjoint')?.checked) return;
    // État civil : même valeur que le client
    const clientEC = document.getElementById('client-etat-civil');
    const conjointEC = document.getElementById('conjoint-etat-civil');
    if (clientEC && conjointEC) conjointEC.value = clientEC.value;
    // Adresse : sync champ par champ
    const addrFields = ['civique','rue','type-unite','numero','case','ville','province','postal'];
    addrFields.forEach(f => {
      const src = document.getElementById('client-addr-' + f);
      const dst = document.getElementById('conjoint-addr-' + f);
      if (src && dst) dst.value = src.value;
    });
  }

  function goTo(id, btn) {
    // hide all pages (clear inline style too so it doesn't override the CSS class)
    document.querySelectorAll('.page').forEach(p => { p.classList.remove('active'); p.style.display = ''; });
    document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
    // show target
    document.getElementById('page-' + id)?.classList.add('active');
    if(btn) btn.classList.add('active');
    // update current index
    current = pages.indexOf(id);
    if (id === 'objectifs') renderObjectives();
    if (id === 'actifs-passifs') updateApSidebar();
    if (id === 'revenu-epargne') updateReSidebar();
    if (id === 'fonds-urgence') { fuRenderActifs(); fuCalc(); }
    if (id === 'deces') decesInit();
    if (id === 'invalidite') invaliditeInit();
    if (id === 'maladie-grave') mgInit();
    if (id === 'retraite') retraiteInit();
    if (id === 'recommandations') recomInit();
    if (id === 'rapport') rapportInit();
    // Swap bouton Suivant ↔ Terminer selon la page
    const nextBtn = document.querySelector('.bottom-bar .btn-primary');
    if (nextBtn) {
      if (id === 'rapport') {
        nextBtn.textContent = 'Terminer ✓';
        nextBtn.setAttribute('onclick', 'terminer()');
      } else {
        nextBtn.textContent = 'Suivant →';
        nextBtn.setAttribute('onclick', 'goNext()');
      }
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function terminer() {
    if (window.ABF_SAVE_URL) {
      autoSave(window.ABF_RECORD_ID, window.ABF_SAVE_URL, window.ABF_CSRF_TOKEN, false);
    }
    setTimeout(() => {
      window.location.href = window.ABF_LANDING_URL || '/conseiller/bilan';
    }, 900);
  }

  function validateCurrentPage() {
    // Objectifs : au moins 3 sélectionnés
    if (pages[current] === 'objectifs') {
      const total = Object.values(objState).flat().filter(i => i.checked).length;
      if (total < 3) {
        showToast('Veuillez sélectionner au moins 3 objectifs');
        return false;
      }
      return true;
    }
    if (pages[current] !== 'infos-perso') return true;
    let valid = true;
    const errors = [];

    function checkText(id) {
      const el = document.getElementById(id);
      if (!el) return;
      const empty = !el.value.trim();
      el.classList.toggle('input-error', empty);
      if (empty) { valid = false; if (!errors.length) el.scrollIntoView({behavior:'smooth', block:'center'}); errors.push(id); }
      else el.classList.remove('input-error');
    }
    function checkSelect(id) {
      const el = document.getElementById(id);
      if (!el) return;
      const empty = !el.value;
      el.classList.toggle('input-error', empty);
      if (empty) { valid = false; if (!errors.length) el.scrollIntoView({behavior:'smooth', block:'center'}); errors.push(id); }
      else el.classList.remove('input-error');
    }
    function checkRadio(name) {
      const checked = document.querySelector(`input[name="${name}"]:checked`);
      const group = document.querySelector(`input[name="${name}"]`)?.closest('.radio-group');
      if (group) group.classList.toggle('radio-error', !checked);
      if (!checked) { valid = false; errors.push(name); }
      else if (group) group.classList.remove('radio-error');
    }

    // Client
    checkText('client-prenom');
    checkText('client-nom');
    checkText('client-ddn-jour');
    checkSelect('client-ddn-mois');
    checkText('client-naissance-annee');
    checkSelect('client-etat-civil');
    checkSelect('client-province');
    checkText('client-canada-depuis');
    checkRadio('tabac');
    checkText('client-addr-ville');
    checkText('client-addr-postal');

    // Cellulaire OU téléphone obligatoire (au moins un)
    const clientCell = (document.getElementById('client-cellulaire')?.value || '').trim();
    const clientTel  = (document.getElementById('client-telephone')?.value  || '').trim();
    if (!clientCell && !clientTel) {
      valid = false;
      errors.push('client-cellulaire');
      errors.push('client-telephone');
      ['client-cellulaire', 'client-telephone'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('field-error');
      });
    } else {
      ['client-cellulaire', 'client-telephone'].forEach(id => {
        document.getElementById(id)?.classList.remove('field-error');
      });
    }

    // Conjoint (si plan conjoint)
    if (document.getElementById('conjoint')?.checked) {
      checkText('conjoint-prenom');
      checkText('conjoint-nom');
      checkText('conjoint-ddn-jour');
      checkSelect('conjoint-ddn-mois');
      checkText('conjoint-naissance-annee');
      checkSelect('conjoint-etat-civil');
      checkSelect('conjoint-province');
      checkText('conjoint-canada-depuis');
      checkRadio('co-tabac');
      checkText('conjoint-addr-ville');
      checkText('conjoint-addr-postal');
    }

    if (!valid) showToast('Veuillez remplir tous les champs obligatoires — cellulaire ou téléphone requis');
    return valid;
  }

  function goNext() {
    if (current < pages.length - 1) {
      if (!validateCurrentPage()) return;
      const navItems = document.querySelectorAll('.nav-item');
      // Marquer la page courante comme terminée et débloquer la suivante
      navItems[current]?.classList.add('done');
      navItems[current + 1]?.classList.remove('locked');
      goTo(pages[current + 1], navItems[current + 1]);
      // Sauvegarder APRÈS goTo() pour que current_page = la nouvelle page
      if (window.ABF_SAVE_URL) {
        autoSave(window.ABF_RECORD_ID, window.ABF_SAVE_URL, window.ABF_CSRF_TOKEN, false);
      }
    }
  }

  function goPrev() {
    if (current > 0) {
      goTo(pages[current - 1], document.querySelectorAll('.nav-item')[current - 1]);
    }
  }

  function toggleCollapse(btn) {
    btn.classList.toggle('open');
    btn.nextElementSibling.classList.toggle('open');
  }

  /* ── HELPERS : read client / conjoint names ─────────── */
  function getClientPrenom() {
    return (document.getElementById('client-prenom')?.value || '').trim() || 'le client';
  }
  function getConjointPrenom() {
    const conjointActive = document.getElementById('conjoint')?.checked;
    if (!conjointActive) return null;
    const v = (document.getElementById('conjoint-prenom')?.value || '').trim();
    return v || 'Conjoint(e)';
  }

  /* ── MODAL ENFANT ───────────────────────────────────── */
  let _editingEnfantEl = null;

  function openEnfantModal(editEl) {
    _editingEnfantEl = editEl || null;
    const clientPrenom = getClientPrenom();
    const conjointPrenom = getConjointPrenom();
    // Update relation labels
    document.getElementById('enf-relation-label').textContent = 'Relation avec ' + clientPrenom;
    const conjointCol = document.getElementById('enf-relation-conjoint-col');
    if (conjointPrenom) {
      document.getElementById('enf-relation-conjoint-label').textContent = 'Relation avec ' + conjointPrenom;
      conjointCol.style.display = '';
    } else {
      conjointCol.style.display = 'none';
    }
    // Populate À la charge de
    const chargeSelect = document.getElementById('enf-charge');
    chargeSelect.innerHTML = '<option value="">Sélectionnez…</option>';
    chargeSelect.innerHTML += '<option value="client">' + clientPrenom + '</option>';
    if (conjointPrenom) {
      chargeSelect.innerHTML += '<option value="conjoint">' + conjointPrenom + '</option>';
      chargeSelect.innerHTML += '<option value="both">' + clientPrenom + ' et ' + conjointPrenom + '</option>';
    }
    chargeSelect.innerHTML += '<option value="noone">Aucun</option>';

    // Pre-fill if editing
    if (editEl) {
      document.getElementById('enf-prenom').value            = editEl.dataset.enfPrenom          || '';
      document.getElementById('enf-nom').value               = editEl.dataset.enfNom             || '';
      document.getElementById('enf-sexe').value              = editEl.dataset.enfSexe            || '';
      document.getElementById('enf-jour').value              = editEl.dataset.enfJour            || '';
      document.getElementById('enf-mois').value              = editEl.dataset.enfMois            || '';
      document.getElementById('enf-annee').value             = editEl.dataset.enfAnnee           || '';
      document.getElementById('enf-relation').value          = editEl.dataset.enfRelation        || '';
      document.getElementById('enf-relation-conjoint').value = editEl.dataset.enfRelationConjoint || '';
      document.getElementById('enf-charge').value            = editEl.dataset.charge             || '';
      document.getElementById('enf-submit').textContent = 'Mettre à jour';
    } else {
      document.getElementById('enf-submit').textContent = 'Enregistrer';
    }

    document.getElementById('modal-enfant').classList.add('open');
    document.getElementById('enf-prenom').focus();
  }
  function closeEnfantModal() {
    _editingEnfantEl = null;
    document.getElementById('modal-enfant').classList.remove('open');
    document.getElementById('enf-submit').textContent = 'Enregistrer';
    // Reset fields
    ['enf-prenom','enf-nom','enf-sexe','enf-jour','enf-mois','enf-annee','enf-relation','enf-relation-conjoint','enf-charge']
      .forEach(id => { const el = document.getElementById(id); if(el) el.value = ''; });
  }
  function _buildEnfantItemHTML(nomComplet, relLabel, ddn, sexeLabel, chargeLabel) {
    return `
      <div style="display:flex;align-items:center;gap:10px;flex:1">
        <div style="width:32px;height:32px;border-radius:50%;background:#eef1fc;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0">👤</div>
        <div>
          <div style="font-weight:600;color:var(--navy)">${nomComplet}</div>
          <div style="color:var(--muted);font-size:11px;margin-top:2px">${relLabel} · Né(e) : ${ddn} · ${sexeLabel}${chargeLabel ? ' · À charge : ' + chargeLabel : ''}</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:4px;flex-shrink:0">
        <button onclick="openEnfantModal(this.closest('.enfant-item'))" title="Modifier" style="background:none;border:none;color:var(--muted);cursor:pointer;padding:2px 4px;line-height:1;font-size:14px" onmouseover="this.style.color='var(--navy)'" onmouseout="this.style.color='var(--muted)'">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </button>
        <button onclick="this.closest('.enfant-item').remove()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:18px;line-height:1;padding:0 4px">×</button>
      </div>`;
  }
  function saveEnfant() {
    const prenom = document.getElementById('enf-prenom').value.trim();
    const nom    = document.getElementById('enf-nom').value.trim();
    if (!prenom && !nom) { document.getElementById('enf-prenom').focus(); return; }
    const sexe     = document.getElementById('enf-sexe');
    const jour     = document.getElementById('enf-jour').value;
    const mois     = document.getElementById('enf-mois');
    const annee    = document.getElementById('enf-annee').value;
    const relation         = document.getElementById('enf-relation');
    const relationConjoint = document.getElementById('enf-relation-conjoint');
    const charge           = document.getElementById('enf-charge');

    const nomComplet  = [prenom, nom].filter(Boolean).join(' ');
    const moisText    = mois.options[mois.selectedIndex]?.text;
    const ddn         = [jour, moisText !== 'Mois' ? moisText : '', annee].filter(Boolean).join(' ') || '—';
    const sexeLabel   = sexe.options[sexe.selectedIndex]?.value ? sexe.options[sexe.selectedIndex]?.text : '—';
    const relLabel    = relation.options[relation.selectedIndex]?.value ? relation.options[relation.selectedIndex]?.text : '—';
    const chargeVal   = charge.options[charge.selectedIndex]?.value || '';
    const chargeLabel = charge.options[charge.selectedIndex]?.text || '';

    if (_editingEnfantEl) {
      // Update existing item
      _editingEnfantEl.dataset.enfPrenom   = prenom;
      _editingEnfantEl.dataset.enfNom      = nom;
      _editingEnfantEl.dataset.enfSexe     = sexe.value;
      _editingEnfantEl.dataset.enfJour     = jour;
      _editingEnfantEl.dataset.enfMois     = mois.value;
      _editingEnfantEl.dataset.enfAnnee    = annee;
      _editingEnfantEl.dataset.enfRelation         = relation.value;
      _editingEnfantEl.dataset.enfRelationConjoint = relationConjoint.value;
      _editingEnfantEl.dataset.charge              = chargeVal;
      _editingEnfantEl.innerHTML = _buildEnfantItemHTML(nomComplet, relLabel, ddn, sexeLabel, chargeLabel);
      closeEnfantModal();
      return;
    }

    const list = document.getElementById('enfants-list');
    if (list.classList.contains('list-empty')) { list.classList.remove('list-empty'); list.innerHTML = ''; }

    const item = document.createElement('div');
    item.className = 'enfant-item';
    item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px;gap:12px';
    item.dataset.charge      = chargeVal;
    item.dataset.enfPrenom   = prenom;
    item.dataset.enfNom      = nom;
    item.dataset.enfSexe     = sexe.value;
    item.dataset.enfJour     = jour;
    item.dataset.enfMois     = mois.value;
    item.dataset.enfAnnee    = annee;
    item.dataset.enfRelation         = relation.value;
    item.dataset.enfRelationConjoint = relationConjoint.value;
    item.innerHTML = _buildEnfantItemHTML(nomComplet, relLabel, ddn, sexeLabel, chargeLabel);
    list.appendChild(item);
    closeEnfantModal();
  }
  // Close modal on backdrop click
  document.getElementById('modal-enfant')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-enfant')) closeEnfantModal();
  });

  /* ── LEGAL DROPDOWN ─────────────────────────────────── */
  function toggleLegalMenu(e) {
    e.stopPropagation();
    const dd = document.getElementById('legal-dropdown');
    dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
  }

  /* ── MODAL LÉGAL ─────────────────────────────────────── */
  function openLegalModal(name) {
    document.getElementById('legal-dropdown').style.display = 'none';
    // Set title
    document.getElementById('modal-legal-title').textContent = name;
    // Populate Propriétaire
    const propSelect = document.getElementById('leg-proprietaire');
    propSelect.innerHTML = '<option value="">Sélectionnez…</option>';
    const clientPrenom = getClientPrenom();
    propSelect.innerHTML += '<option value="client">' + clientPrenom + '</option>';
    const conjointPrenom = getConjointPrenom();
    if (conjointPrenom) {
      propSelect.innerHTML += '<option value="conjoint">' + conjointPrenom + '</option>';
    }
    // Reset fields
    ['leg-jour','leg-annee','leg-note'].forEach(id => { const el=document.getElementById(id); if(el) el.value=''; });
    document.getElementById('leg-mois').value = '';
    document.getElementById('leg-type').value = '';
    // Store doc type for save
    document.getElementById('modal-legal').dataset.docType = name;
    document.getElementById('modal-legal').classList.add('open');
    propSelect.focus();
  }
  function closeLegalModal() {
    document.getElementById('modal-legal').classList.remove('open');
  }
  function saveLegal() {
    const docType   = document.getElementById('modal-legal').dataset.docType || '';
    const propSel   = document.getElementById('leg-proprietaire');
    const propText  = propSel.options[propSel.selectedIndex]?.text || '';
    const jour      = document.getElementById('leg-jour').value.trim();
    const moisSel   = document.getElementById('leg-mois');
    const moisText  = moisSel.options[moisSel.selectedIndex]?.text || '';
    const annee     = document.getElementById('leg-annee').value.trim();
    const typeSel   = document.getElementById('leg-type');
    const typeText  = typeSel.options[typeSel.selectedIndex]?.text || '';
    const note      = document.getElementById('leg-note').value.trim();

    const list = document.getElementById('legal-list');
    if (list.classList.contains('list-empty')) { list.classList.remove('list-empty'); list.innerHTML = ''; }
    const item = document.createElement('div');
    item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);font-size:13px;gap:8px';
    item.innerHTML = `
      <span style="display:flex;align-items:center;gap:8px">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
          <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/>
        </svg>
        <span style="color:var(--text);font-weight:500">${docType}</span>
        ${propText ? `<span style="color:var(--muted);font-size:11px">· ${propText}</span>` : ''}
        ${typeText && typeSel.value ? `<span style="color:var(--muted);font-size:11px">· ${typeText}</span>` : ''}
      </span>
      <button onclick="this.closest('div[style]').remove()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:18px;line-height:1;padding:0 4px">×</button>`;
    item.dataset.formJson = JSON.stringify({
      docType, propOwner: propSel.value, propText,
      jour, mois: moisSel.value, moisText, annee,
      legalType: typeSel.value, typeText, note,
    });
    list.appendChild(item);
    closeLegalModal();
  }
  // Close legal modal on backdrop click
  document.getElementById('modal-legal')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-legal')) closeLegalModal();
  });
  // Close all dropdowns when clicking outside
  document.addEventListener('click', () => {
    ['legal-dropdown','placement-dropdown','bien-dropdown','passif-dropdown']
      .forEach(id => { const el = document.getElementById(id); if (el) el.style.display = 'none'; });
  });

  /* ── ACTIFS / PASSIFS ───────────────────────────────── */
  const AP_INSTITUTIONS = ['B2B','Banque Laurentienne','Banque Scotia','Beneva','BMO','BNC',
    'Canada Vie (Great West, London Life)','CIBC','Desjardins','Empire Vie','Fidelity',
    'Financière Sun Life','FTQ','HSBC','iA Groupe financier','IG gestion privée de patrimoine',
    'ING','Investia','Manuvie','Primerica','RBC','Scotia iTRADE','SSQ/La Capitale','Tangerine','TD','Autre'];
  const AP_RENDEMENT = { prudent:'3,00', moderate:'3,50', balanced:'3,70', growth:'4,00', aggressive:'5,00' };

  function apCloseDropdowns() {
    ['placement-dropdown','bien-dropdown','passif-dropdown']
      .forEach(id => { const el=document.getElementById(id); if(el) el.style.display='none'; });
  }
  function toggleApMenu(e, menuId) {
    e.stopPropagation();
    const wasOpen = document.getElementById(menuId)?.style.display === 'block';
    apCloseDropdowns();
    const dd = document.getElementById(menuId);
    if (dd) dd.style.display = wasOpen ? 'none' : 'block';
  }
  let _editingItem = null;

  function apFillProprietaire(selId) {
    const sel = document.getElementById(selId);
    sel.innerHTML = '<option value="">Sélectionnez…</option>';
    sel.innerHTML += '<option value="client">' + getClientPrenom() + '</option>';
    const c = getConjointPrenom();
    if (c) sel.innerHTML += '<option value="conjoint">' + c + '</option>';
  }
  function apFillBienProprietaire(selId) {
    const sel = document.getElementById(selId);
    const cn = getClientPrenom(), cj = getConjointPrenom();
    sel.innerHTML = '<option value="">Sélectionnez…</option>';
    sel.innerHTML += `<option value="client">${cn}</option>`;
    if (cj) {
      sel.innerHTML += `<option value="conjoint">${cj}</option>`;
      sel.innerHTML += `<option value="both">${cn} et ${cj}</option>`;
    }
  }
  function passifPropChange() {
    const val = document.getElementById('pass-proprietaire').value;
    const row = document.getElementById('pass-parts-row');
    row.style.display = val === 'both' ? '' : 'none';
    if (val === 'both') {
      document.getElementById('pass-part-label-client').textContent = getClientPrenom();
      document.getElementById('pass-part-label-conjoint').textContent = getConjointPrenom() || 'conjoint';
      const pc = document.getElementById('pass-part-client');
      const pj = document.getElementById('pass-part-conjoint');
      if (!pc.value) pc.value = '50';
      if (!pj.value) pj.value = '50';
    }
  }
  function bienPropChange() {
    const val = document.getElementById('bien-proprietaire').value;
    const row = document.getElementById('bien-parts-row');
    row.style.display = val === 'both' ? '' : 'none';
    if (val === 'both') {
      document.getElementById('bien-part-label-client').textContent = getClientPrenom();
      document.getElementById('bien-part-label-conjoint').textContent = getConjointPrenom() || 'conjoint';
      const pc = document.getElementById('bien-part-client');
      const pj = document.getElementById('bien-part-conjoint');
      if (!pc.value) pc.value = '50';
      if (!pj.value) pj.value = '50';
    }
  }
  function editApItem(item) {
    _editingItem = item;
    const mtype = item.dataset.modalType || '';
    const fdata = item.dataset.formJson ? JSON.parse(item.dataset.formJson) : null;
    const type  = item.dataset.aptype || '';
    if (mtype === 'bien')           openBienModal(type, fdata);
    else if (mtype === 'placement') openPlacementModal(type, fdata);
    else if (mtype === 'passif')    openPassifModal(type, fdata);
  }
  function apBuildItemInner(type, propText, valeurText, subText, iconColor, bgColor) {
    return `
      <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0">
        <div style="width:32px;height:32px;border-radius:8px;background:${bgColor};display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="${iconColor}" stroke-width="2">
            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
        </div>
        <div style="min-width:0">
          <div style="font-weight:600;color:var(--navy)">${type}</div>
          <div style="color:var(--muted);font-size:11px;margin-top:2px">${[propText,valeurText,subText].filter(Boolean).join(' · ')}</div>
        </div>
      </div>
      <div style="display:flex;gap:2px;flex-shrink:0">
        <button onclick="editApItem(this.closest('[data-valeur]'))" title="Modifier" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:15px;line-height:1;padding:2px 6px">✎</button>
        <button onclick="this.closest('[data-valeur]').remove();updateApSidebar();if(typeof updateEpargneSection==='function')updateEpargneSection()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:18px;line-height:1;padding:0 4px">×</button>
      </div>`;
  }
  function apUpdateItem(item, type, propText, valeurText, subText, valeurNum, owner, modalType, formJson, partClient, partConjoint) {
    item.dataset.valeur    = valeurNum;
    item.dataset.owner     = owner || '';
    item.dataset.modalType = modalType || '';
    item.dataset.formJson  = formJson || '';
    item.dataset.aptype    = type;
    if (partClient   !== undefined) item.dataset.partClient   = partClient;
    if (partConjoint !== undefined) item.dataset.partConjoint = partConjoint;
    const isPassif  = item.closest('#passifs-list') !== null;
    const iconColor = isPassif ? '#ef4444' : 'var(--valid)';
    const bgColor   = isPassif ? '#fef2f2' : '#f0fdf4';
    item.innerHTML  = apBuildItemInner(type, propText, valeurText, subText, iconColor, bgColor);
    updateApSidebar();
    if (!isPassif && typeof updateEpargneSection === 'function') updateEpargneSection();
  }
  function apFillInstitution(selId) {
    const sel = document.getElementById(selId);
    sel.innerHTML = '<option value="">Sélectionnez…</option>';
    AP_INSTITUTIONS.forEach(i => sel.innerHTML += `<option>${i}</option>`);
  }

  // ── SIDEBAR ──────────────────────────────────────────
  function updateApSidebar() {
    const fmt = v => v.toLocaleString('fr-CA', {minimumFractionDigits:0,maximumFractionDigits:0}) + ' $';
    const colorVn = v => v < 0 ? '#ef4444' : 'var(--navy)';
    let totalActifs=0, totalPassifs=0, clientActifs=0, clientPassifs=0, conjointActifs=0, conjointPassifs=0;
    document.querySelectorAll('#actifs-list [data-valeur]').forEach(el => {
      const v = parseFloat(el.dataset.valeur)||0; totalActifs+=v;
      if (el.dataset.owner === 'both') {
        const pc = parseFloat(el.dataset.partClient  ?? 50)/100;
        const pj = parseFloat(el.dataset.partConjoint ?? 50)/100;
        clientActifs += v*pc; conjointActifs += v*pj;
      } else if (el.dataset.owner === 'conjoint') { conjointActifs+=v; } else { clientActifs+=v; }
    });
    document.querySelectorAll('#passifs-list [data-valeur]').forEach(el => {
      const v = parseFloat(el.dataset.valeur)||0; totalPassifs+=v;
      if (el.dataset.owner === 'both') {
        const pc = parseFloat(el.dataset.partClient  ?? 50)/100;
        const pj = parseFloat(el.dataset.partConjoint ?? 50)/100;
        clientPassifs += v*pc; conjointPassifs += v*pj;
      } else if (el.dataset.owner === 'conjoint') { conjointPassifs+=v; } else { clientPassifs+=v; }
    });
    const vn=totalActifs-totalPassifs, cvn=clientActifs-clientPassifs, jvn=conjointActifs-conjointPassifs;
    const set = (id,val,color) => { const el=document.getElementById(id); if(el){el.textContent=val;if(color!==undefined)el.style.color=color;} };
    set('ap-total-vn', fmt(vn), colorVn(vn));
    set('ap-client-name', getClientPrenom());
    set('ap-client-vn',      fmt(cvn),          colorVn(cvn));
    set('ap-client-actifs',  fmt(clientActifs));
    set('ap-client-passifs', fmt(clientPassifs));
    const conjoint = getConjointPrenom();
    const conjBlock = document.getElementById('ap-conjoint-block');
    if (conjBlock) conjBlock.style.display = conjoint ? 'block' : 'none';
    if (conjoint) {
      set('ap-conjoint-name',    conjoint);
      set('ap-conjoint-vn',      fmt(jvn),          colorVn(jvn));
      set('ap-conjoint-actifs',  fmt(conjointActifs));
      set('ap-conjoint-passifs', fmt(conjointPassifs));
    }
  }
  function apAddToList(listId, type, propText, valeurText, subText, valeurNum, owner, modalType, formJson, partClient, partConjoint) {
    const list = document.getElementById(listId);
    if (list.classList.contains('list-empty')) { list.classList.remove('list-empty'); list.innerHTML = ''; }
    const isPassif  = listId === 'passifs-list';
    const iconColor = isPassif ? '#ef4444' : 'var(--valid)';
    const bgColor   = isPassif ? '#fef2f2' : '#f0fdf4';
    const item = document.createElement('div');
    item.dataset.valeur    = valeurNum;
    item.dataset.owner     = owner || '';
    item.dataset.modalType = modalType || '';
    item.dataset.formJson  = formJson || '';
    item.dataset.aptype    = type;
    if (partClient   !== undefined) item.dataset.partClient   = partClient;
    if (partConjoint !== undefined) item.dataset.partConjoint = partConjoint;
    item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px;gap:12px';
    item.innerHTML = apBuildItemInner(type, propText, valeurText, subText, iconColor, bgColor);
    list.appendChild(item);
    updateApSidebar();
    if (listId === 'actifs-list') updateEpargneSection();
  }

  // ── MODAL PLACEMENT ──────────────────────────────────
  const PLAC_BOTH_TYPES  = ['Compte bancaire','Non enregistré','REEE'];
  const PLAC_LEGIS_TYPES = ['CRI','FERR','FRV','REER Immobilisé'];
  function openPlacementModal(type, prefill) {
    if (!prefill) { apCloseDropdowns(); _editingItem = null; }
    document.getElementById('modal-placement').dataset.type = type;
    document.getElementById('plac-title').textContent = type;
    document.getElementById('plac-description').value = prefill?.description ?? type;
    if (PLAC_BOTH_TYPES.includes(type)) apFillBienProprietaire('plac-proprietaire');
    else apFillProprietaire('plac-proprietaire');
    document.getElementById('plac-proprietaire').value = prefill?.owner ?? '';
    const legisRow = document.getElementById('plac-legislation-row');
    if (legisRow) legisRow.style.display = PLAC_LEGIS_TYPES.includes(type) ? '' : 'none';
    document.getElementById('plac-legislation').value = prefill?.legislation ?? '';
    const dateOuvRow = document.getElementById('plac-date-ouverture-row');
    if (dateOuvRow) dateOuvRow.style.display = (type === 'CELIAPP') ? '' : 'none';
    const dateOuvInput = document.getElementById('plac-date-ouverture');
    if (dateOuvInput) dateOuvInput.value = prefill?.dateOuverture ?? '';
    const saveBtn = document.getElementById('plac-save-btn');
    if (saveBtn) saveBtn.disabled = (type === 'CELIAPP' && !prefill?.dateOuverture);
    apFillInstitution('plac-institution');
    document.getElementById('plac-institution').value = prefill?.institution ?? '';
    document.getElementById('plac-valeur').value = prefill?.valeur ?? '';
    document.getElementById('plac-notes').value = prefill?.notes ?? '';
    document.getElementById('plac-portefeuille').value = prefill?.portefeuille ?? 'balanced';
    document.getElementById('plac-rendement').value = prefill?.rendement ?? '3,70';
    document.getElementById('plac-categorie').value = prefill?.categorie ?? '';
    document.getElementById('modal-placement').classList.add('open');
    document.getElementById('plac-valeur').focus();
  }
  function syncRendement() {
    const v = document.getElementById('plac-portefeuille').value;
    document.getElementById('plac-rendement').value = AP_RENDEMENT[v] || '';
  }
  function closePlacementModal() { document.getElementById('modal-placement').classList.remove('open'); _editingItem = null; }
  function savePlacement() {
    const type   = document.getElementById('modal-placement').dataset.type || '';
    const prop   = document.getElementById('plac-proprietaire');
    const propVal = prop.value;
    const propTx = propVal ? prop.options[prop.selectedIndex].text : '';
    const valStr = document.getElementById('plac-valeur').value.trim();
    const valNum = parseFloat(valStr.replace(/\s/g,'').replace(',','.')) || 0;
    const portefeuille = document.getElementById('plac-portefeuille');
    const portTx = portefeuille.options[portefeuille.selectedIndex].text;
    const rendement   = document.getElementById('plac-rendement').value.trim();
    const institution = document.getElementById('plac-institution').value;
    const categorie   = document.getElementById('plac-categorie').value;
    const notes       = document.getElementById('plac-notes').value.trim();
    const descr       = document.getElementById('plac-description').value.trim();
    const sub = portTx + (rendement ? ' · '+rendement+'%' : '');
    const partClient = 50, partConjoint = 50; // placements toujours 50/50
    const legislation = document.getElementById('plac-legislation')?.value ?? '';
    const dateOuverture = document.getElementById('plac-date-ouverture')?.value.trim() ?? '';
    if (type === 'CELIAPP' && !dateOuverture) {
      showToast('La date d\'ouverture est requise pour un CELIAPP');
      return;
    }
    const formJson = JSON.stringify({description:descr, owner:propVal, valeur:valStr, portefeuille:portefeuille.value, rendement, institution, categorie, notes, legislation, dateOuverture});
    if (_editingItem) {
      apUpdateItem(_editingItem, type, propTx, valStr ? valStr+' $' : '', sub, valNum, propVal, 'placement', formJson, partClient, partConjoint);
    } else {
      apAddToList('actifs-list', type, propTx, valStr ? valStr+' $' : '', sub, valNum, propVal, 'placement', formJson, partClient, partConjoint);
    }
    closePlacementModal();
  }
  document.getElementById('modal-placement')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-placement')) closePlacementModal();
  });

  // ── MODAL BIEN ───────────────────────────────────────
  function openBienModal(type, prefill) {
    if (!prefill) { apCloseDropdowns(); _editingItem = null; }
    document.getElementById('modal-bien').dataset.type = type;
    document.getElementById('bien-title').textContent = type;
    document.getElementById('bien-description').value = prefill?.description ?? type;
    apFillBienProprietaire('bien-proprietaire');
    document.getElementById('bien-proprietaire').value = prefill?.owner ?? '';
    bienPropChange();
    if (prefill?.owner === 'both') {
      document.getElementById('bien-part-client').value   = prefill?.partClient   ?? '50';
      document.getElementById('bien-part-conjoint').value = prefill?.partConjoint ?? '50';
    }
    document.getElementById('bien-valeur').value     = prefill?.valeur     ?? '';
    document.getElementById('bien-cout').value       = prefill?.cout       ?? '';
    document.getElementById('bien-croissance').value = prefill?.croissance ?? '';
    document.getElementById('bien-notes').value      = prefill?.notes      ?? '';
    document.getElementById('modal-bien').classList.add('open');
    document.getElementById('bien-valeur').focus();
  }
  function closeBienModal() { document.getElementById('modal-bien').classList.remove('open'); _editingItem = null; }
  function saveBien() {
    const type   = document.getElementById('modal-bien').dataset.type || '';
    const prop   = document.getElementById('bien-proprietaire');
    const propVal = prop.value;
    const propTx  = propVal ? prop.options[prop.selectedIndex].text : '';
    const valStr  = document.getElementById('bien-valeur').value.trim();
    const valNum  = parseFloat(valStr.replace(/\s/g,'').replace(',','.')) || 0;
    const cout    = document.getElementById('bien-cout').value.trim();
    const croiss  = document.getElementById('bien-croissance').value.trim();
    const notes   = document.getElementById('bien-notes').value.trim();
    const descr   = document.getElementById('bien-description').value.trim();
    let partClient = 50, partConjoint = 50;
    if (propVal === 'both') {
      partClient   = parseFloat(document.getElementById('bien-part-client').value)   || 50;
      partConjoint = parseFloat(document.getElementById('bien-part-conjoint').value) || 50;
    }
    const sub = cout ? 'Coût : '+cout+' $' : '';
    const formJson = JSON.stringify({description:descr, owner:propVal, valeur:valStr, cout, croissance:croiss, notes, partClient, partConjoint});
    if (_editingItem) {
      apUpdateItem(_editingItem, type, propTx, valStr ? valStr+' $' : '', sub, valNum, propVal, 'bien', formJson, partClient, partConjoint);
    } else {
      apAddToList('actifs-list', type, propTx, valStr ? valStr+' $' : '', sub, valNum, propVal, 'bien', formJson, partClient, partConjoint);
    }
    closeBienModal();
  }
  document.getElementById('modal-bien')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-bien')) closeBienModal();
  });

  // ── MODAL PASSIF ─────────────────────────────────────
  function openPassifModal(type, prefill) {
    if (!prefill) { apCloseDropdowns(); _editingItem = null; }
    document.getElementById('modal-passif').dataset.type = type;
    document.getElementById('pass-title').textContent = type;
    document.getElementById('pass-description').value = prefill?.description ?? type;
    apFillBienProprietaire('pass-proprietaire');
    document.getElementById('pass-proprietaire').value = prefill?.owner ?? '';
    passifPropChange();
    if (prefill?.owner === 'both') {
      document.getElementById('pass-part-client').value   = prefill?.partClient   ?? '50';
      document.getElementById('pass-part-conjoint').value = prefill?.partConjoint ?? '50';
    }
    apFillInstitution('pass-institution');
    document.getElementById('pass-institution').value        = prefill?.institution   ?? '';
    document.getElementById('pass-solde').value              = prefill?.solde         ?? '';
    document.getElementById('pass-amort-val').value          = prefill?.amortVal      ?? '';
    document.getElementById('pass-amort-unit').value         = prefill?.amortUnit     ?? 'month';
    document.getElementById('pass-taux').value               = prefill?.taux          ?? '';
    document.getElementById('pass-paiement').value           = prefill?.paiement      ?? '';
    document.getElementById('pass-paiement-freq').value      = prefill?.paiementFreq  ?? 'monthly';
    document.getElementById('pass-renouvellement-mois').value  = prefill?.renouvMois  ?? '';
    document.getElementById('pass-renouvellement-annee').value = prefill?.renouvAnnee ?? '';
    document.getElementById('pass-notes').value              = prefill?.notes         ?? '';
    document.querySelectorAll('.calc-tab').forEach((b,i) => b.classList.toggle('active', i===0));
    document.getElementById('modal-passif').classList.add('open');
    document.getElementById('pass-solde').focus();
  }
  function setCalcType(type, btn) {
    document.querySelectorAll('.calc-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  }
  function closePassifModal() { document.getElementById('modal-passif').classList.remove('open'); _editingItem = null; }
  function savePassif() {
    const type   = document.getElementById('modal-passif').dataset.type || '';
    const prop   = document.getElementById('pass-proprietaire');
    const propVal = prop.value;
    const propTx  = propVal ? prop.options[prop.selectedIndex].text : '';
    const solde   = document.getElementById('pass-solde').value.trim();
    const soldeNum = parseFloat(solde.replace(/\s/g,'').replace(',','.')) || 0;
    const taux    = document.getElementById('pass-taux').value.trim();
    const paiement = document.getElementById('pass-paiement').value.trim();
    const freq    = document.getElementById('pass-paiement-freq');
    const freqTx  = freq.options[freq.selectedIndex].text;
    const amortVal   = document.getElementById('pass-amort-val').value.trim();
    const amortUnit  = document.getElementById('pass-amort-unit').value;
    const renouvMois = document.getElementById('pass-renouvellement-mois').value;
    const renouvAnnee = document.getElementById('pass-renouvellement-annee').value.trim();
    const institution = document.getElementById('pass-institution').value;
    const notes  = document.getElementById('pass-notes').value.trim();
    const descr  = document.getElementById('pass-description').value.trim();
    const subParts = [];
    if (taux) subParts.push(taux + ' %');
    if (paiement) subParts.push(paiement + ' $ ' + freqTx);
    let partClient = 50, partConjoint = 50;
    if (propVal === 'both') {
      partClient   = parseFloat(document.getElementById('pass-part-client').value)   || 50;
      partConjoint = parseFloat(document.getElementById('pass-part-conjoint').value) || 50;
    }
    const formJson = JSON.stringify({description:descr, owner:propVal, solde, amortVal, amortUnit, taux, paiement, paiementFreq:freq.value, renouvMois, renouvAnnee, institution, notes, partClient, partConjoint});
    if (_editingItem) {
      apUpdateItem(_editingItem, type, propTx, solde ? solde+' $' : '', subParts.join(' · '), soldeNum, propVal, 'passif', formJson, partClient, partConjoint);
    } else {
      apAddToList('passifs-list', type, propTx, solde ? solde+' $' : '', subParts.join(' · '), soldeNum, propVal, 'passif', formJson, partClient, partConjoint);
    }
    closePassifModal();
  }
  document.getElementById('modal-passif')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-passif')) closePassifModal();
  });

  /* ── OBJECTIFS ─────────────────────────────────────── */
  const OBJECTIVES = [
    {
      id: 'famille', label: 'Famille',
      icon: '👨‍👩‍👧',
      items: [
        { id: 'getMarried',                label: 'Se marier / s\'unir (conjoint de fait)', checked: false },
        { id: 'moveInTogether',            label: 'Prévoir une cohabitation', checked: false },
        { id: 'haveChildren',              label: 'Avoir des enfants', checked: false },
        { id: 'financeChildrenEducation',  label: 'Financer les études des enfants', checked: false },
        { id: 'buySellPropertyFamily',     label: 'Acheter / vendre une propriété', checked: false },
        { id: 'specialProjects',           label: 'Projets spéciaux', checked: false },
      ]
    },
    {
      id: 'travail', label: 'Travail',
      icon: '💼',
      items: [
        { id: 'returnToStudies',    label: 'Reprendre les études', checked: false },
        { id: 'getAPromotion',      label: 'Obtenir une promotion', checked: false },
        { id: 'changeCareers',      label: 'Changer de carrière', checked: false },
        { id: 'withdrawFromJob',    label: 'Se retirer du marché du travail', checked: false },
        { id: 'returnToJob',        label: 'Retourner sur le marché du travail', checked: false },
        { id: 'buySellBusiness',    label: 'Acheter / vendre une entreprise ou un commerce', checked: false },
      ]
    },
    {
      id: 'finances', label: 'Finances',
      icon: '💰',
      items: [
        { id: 'reduceTaxes',          label: 'Diminuer les impôts', checked: false },
        { id: 'repayDebts',           label: 'Rembourser les dettes', checked: false },
        { id: 'repayStudentLoan',     label: 'Rembourser le prêt / marge étudiant', checked: false },
        { id: 'reviewInvestments',    label: 'Revoir les placements actuels', checked: false },
        { id: 'reduceInsecurity',     label: 'Réduire l\'insécurité relative aux finances', checked: false },
        { id: 'buySellPropertyFin',   label: 'Acheter / vendre une propriété', checked: false },
        { id: 'capitalGain',          label: 'Amortir le gain en capital', checked: false },
        { id: 'emergencyFundFin',     label: 'Constituer un fonds d\'urgence', checked: false },
        { id: 'reviewSavings',        label: 'Revoir la méthode d\'épargne', checked: false },
        { id: 'maximizeTax',          label: 'Maximiser fiscalement les revenus de placement', checked: false },
      ]
    },
    {
      id: 'loisirs', label: 'Loisirs',
      icon: '🏖️',
      items: [
        { id: 'planTrip',        label: 'Planifier des voyages, sports et loisirs', checked: false },
        { id: 'volunteer',       label: 'Faire du bénévolat', checked: false },
        { id: 'associations',    label: 'S\'engager dans des associations', checked: false },
      ]
    },
    {
      id: 'retraite', label: 'Retraite',
      icon: '🌅',
      items: [
        { id: 'planRetirementAge',    label: 'Prévoir l\'âge de retraite', checked: false },
        { id: 'maintainLifeStyle',    label: 'Maintenir le niveau de vie', checked: false },
        { id: 'protectInflation',     label: 'Se protéger contre l\'inflation', checked: false },
        { id: 'stayAtHome',           label: 'Rester à domicile le plus longtemps possible', checked: false },
        { id: 'retirementOccupation', label: 'Prévoir une occupation pour la retraite', checked: false },
        { id: 'keepSellProperty',     label: 'Conserver, vendre ou transférer une propriété', checked: false },
        { id: 'diversifyIncome',      label: 'Diversifier vos sources de revenu', checked: false },
      ]
    },
    {
      id: 'protections', label: 'Protections',
      icon: '🛡️',
      items: [
        { id: 'repayLoansDeath',       label: 'Rembourser les emprunts en cas de décès', checked: false },
        { id: 'deathExpenses',         label: 'Prévoir les dépenses liées au décès', checked: false },
        { id: 'familyLifeStyleDeath',  label: 'Maintenir le niveau de vie familial en cas de décès', checked: false },
        { id: 'leaveInheritance',      label: 'Léguer un héritage', checked: false },
        { id: 'taxesAtDeath',          label: 'Prévoir les impôts au décès', checked: false },
        { id: 'makeDonations',         label: 'Planifier des dons', checked: false },
        { id: 'makeWill',              label: 'Rédiger un testament', checked: false },
        { id: 'childLifeInsurance',    label: 'Prévoir une assurance vie pour enfant(s)', checked: false },
        { id: 'protectionMandate',     label: 'Rédiger un mandat de protection', checked: false },
        { id: 'emergencyFundProt',     label: 'Constituer un fonds d\'urgence', checked: false },
        { id: 'lifeStyleDisability',   label: 'Maintenir le niveau de vie en cas d\'invalidité', checked: false },
        { id: 'lifeStyleIllness',      label: 'Maintenir le niveau de vie en cas de maladie grave', checked: false },
        { id: 'illnessExpenses',       label: 'Couvrir les frais associés à la maladie', checked: false },
        { id: 'childDisability',       label: 'Prévoir une couverture pour enfant(s) en cas de maladie grave', checked: false },
      ]
    },
    {
      id: 'autre', label: 'Autre',
      icon: '📋',
      items: []
    },
  ];

  // State: track checked + custom items
  const objState = {};
  OBJECTIVES.forEach(cat => {
    objState[cat.id] = cat.items.map(i => ({ ...i }));
  });

  function countChecked(catId) {
    return objState[catId].filter(i => i.checked).length;
  }

  function renderObjectives() {
    const container = document.getElementById('objectives-container');
    if (!container) return;
    container.innerHTML = '';
    container.style.cssText = 'display:grid;grid-template-columns:repeat(2,1fr);gap:12px;align-items:start';

    OBJECTIVES.forEach(cat => {
      const items = objState[cat.id];
      const checkedCount = items.filter(i => i.checked).length;
      const isOpen = true; // start all open

      const catDiv = document.createElement('div');
      catDiv.className = 'obj-category';
      catDiv.dataset.catId = cat.id;

      // Header
      const hdr = document.createElement('div');
      hdr.className = 'obj-cat-header open';
      hdr.innerHTML = `
        <div class="obj-cat-title">
          <span>${cat.icon}</span>
          <span>${cat.label}</span>
          <span class="obj-cat-badge ${checkedCount === 0 ? 'zero' : ''}">${checkedCount}</span>
        </div>
        <svg class="obj-cat-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="m6 9 6 6 6-6"/>
        </svg>`;
      hdr.addEventListener('click', () => {
        hdr.classList.toggle('open');
        body.classList.toggle('open');
      });

      // Body
      const body = document.createElement('div');
      body.className = 'obj-cat-body open';

      items.forEach((item, idx) => {
        const row = document.createElement('div');
        row.className = 'obj-item';

        const rowHdr = document.createElement('div');
        rowHdr.className = 'obj-item-header';

        // Checkbox SVGs
        const checkBtn = document.createElement('button');
        checkBtn.className = 'obj-check-btn' + (item.checked ? ' checked' : '');
        checkBtn.type = 'button';
        checkBtn.title = item.label;
        checkBtn.innerHTML = `
          <svg class="icon-unchecked" viewBox="0 0 26 24"><path d="M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
          <svg class="icon-checked" viewBox="0 0 26 24"><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2V5c0-1.1-.89-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>`;

        checkBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          objState[cat.id][idx].checked = !objState[cat.id][idx].checked;
          renderObjectives();
        });

        const titleWrap = document.createElement('div');
        titleWrap.className = 'obj-item-title-wrap';
        titleWrap.innerHTML = `<span class="obj-item-title ${item.checked ? 'checked' : ''}">${item.label}</span>`;
        titleWrap.addEventListener('click', () => {
          expandBtn.classList.toggle('open');
          detail.classList.toggle('open');
        });

        const expandBtn = document.createElement('button');
        expandBtn.type = 'button';
        expandBtn.className = 'obj-expand-btn';
        expandBtn.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>`;
        expandBtn.addEventListener('click', () => {
          expandBtn.classList.toggle('open');
          detail.classList.toggle('open');
        });

        rowHdr.appendChild(checkBtn);
        rowHdr.appendChild(titleWrap);
        rowHdr.appendChild(expandBtn);

        // Detail panel
        const detail = document.createElement('div');
        detail.className = 'obj-item-detail';
        detail.innerHTML = `
          <label>Notes / précisions</label>
          <textarea placeholder="Ajoutez des précisions pour cet objectif…">${item.note || ''}</textarea>`;
        detail.querySelector('textarea').addEventListener('input', (e) => {
          objState[cat.id][idx].note = e.target.value;
        });

        row.appendChild(rowHdr);
        row.appendChild(detail);
        body.appendChild(row);
      });

      // Add custom objective button
      const addBtn = document.createElement('button');
      addBtn.type = 'button';
      addBtn.className = 'obj-add-btn';
      addBtn.innerHTML = `
        <svg viewBox="0 0 26 24"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
        Ajouter un objectif personnalisé`;
      addBtn.addEventListener('click', () => {
        const label = prompt('Nom de l\'objectif personnalisé :');
        if (label && label.trim()) {
          objState[cat.id].push({ id: 'custom_' + Date.now(), label: label.trim(), checked: true, custom: true });
          renderObjectives();
        }
      });
      body.appendChild(addBtn);

      catDiv.appendChild(hdr);
      catDiv.appendChild(body);
      container.appendChild(catDiv);
    });
  }

  // Initialize on page load
  document.addEventListener('DOMContentLoaded', () => renderObjectives());

  /* ── REVENU ET ÉPARGNE ───────────────────────────────── */

  let reTabMode = 'annuel'; // 'annuel' | 'mensuel'

  function setReTab(mode, btn) {
    reTabMode = mode;
    document.querySelectorAll('#re-tab-annuel,#re-tab-mensuel').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    updateReSidebar();
  }

  function toggleRevenuDropdown() {
    document.getElementById('revenu-dropdown').classList.toggle('open');
  }

  // Close dropdown on outside click
  document.addEventListener('click', e => {
    const wrap = document.getElementById('revenu-add-wrap');
    if (wrap && !wrap.contains(e.target)) {
      document.getElementById('revenu-dropdown')?.classList.remove('open');
    }
  });

  function reFillProprietaire() {
    const sel = document.getElementById('revenu-proprietaire');
    if (!sel) return;
    sel.innerHTML = '';
    const client = getClientPrenom();
    const conj   = getConjointPrenom();
    sel.appendChild(Object.assign(document.createElement('option'), { value:'', textContent:'Sélectionnez…' }));
    sel.appendChild(Object.assign(document.createElement('option'), { value:'client', textContent: client }));
    if (conj) sel.appendChild(Object.assign(document.createElement('option'), { value:'conjoint', textContent: conj }));
  }

  function openRevenuModal(type) {
    document.getElementById('revenu-dropdown')?.classList.remove('open');
    const isEmploi = type === 'Revenu d\'emploi';
    document.getElementById('revenu-modal-title').textContent = type;
    document.getElementById('modal-revenu').dataset.type = type;
    document.getElementById('revenu-emploi-fields').style.display = isEmploi ? '' : 'none';
    document.getElementById('revenu-autre-fields').style.display  = isEmploi ? 'none' : '';
    // Reset fields
    ['revenu-montant','revenu-profession','revenu-employeur','revenu-embauche-annee','revenu-description',
     'revenu-portion-imposable','revenu-taux-indexation','revenu-debut-mois','revenu-debut-annee'].forEach(id => {
      const el = document.getElementById(id); if(el) el.value = id === 'revenu-portion-imposable' ? '100,00' : id === 'revenu-taux-indexation' ? '0,00' : '';
    });
    document.getElementById('revenu-embauche-mois').value = '';
    document.getElementById('revenu-frequence').value = '12';
    const nonEl = document.getElementById('revenu-indexe-non');
    if (nonEl) nonEl.checked = true;
    const autosaveNon = document.getElementById('revenu-autosave-non');
    if (autosaveNon) autosaveNon.checked = true;
    const finType = document.getElementById('revenu-fin-type');
    if (finType) finType.value = 'retirement';
    reFillProprietaire();
    document.getElementById('modal-revenu').classList.add('open');
    setTimeout(() => document.getElementById('revenu-montant')?.focus(), 50);
  }

  function closeRevenuModal() {
    document.getElementById('modal-revenu').classList.remove('open');
  }

  document.getElementById('modal-revenu')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-revenu')) closeRevenuModal();
  });

  function saveRevenu() {
    const type    = document.getElementById('modal-revenu').dataset.type || '';
    const isEmploi = type === 'Revenu d\'emploi';
    const propSel = document.getElementById('revenu-proprietaire');
    const owner   = propSel.value || 'client';
    const propTx  = owner === 'client' ? getClientPrenom() : (getConjointPrenom() || 'Conjoint(e)');
    const montant = (document.getElementById('revenu-montant').value.trim()) || '0';
    const montantNum = parseFloat(montant.replace(/\s/g,'').replace(',','.')) || 0;

    let description, frequence, freqFactor;
    if (isEmploi) {
      description = document.getElementById('revenu-profession').value.trim() || type;
      frequence   = 'Annuelle';
      freqFactor  = 1;
    } else {
      description = document.getElementById('revenu-description').value.trim() || type;
      const freqSel = document.getElementById('revenu-frequence');
      freqFactor  = parseInt(freqSel.value) || 1;
      const freqMap = {'1':'Annuelle','12':'Mensuelle','26':'Aux deux semaines','52':'Hebdomadaire'};
      frequence   = freqMap[freqSel.value] || 'Annuelle';
    }
    const annuel = montantNum * freqFactor;

    const r = computeImpot(annuel);
    const fmt = n => n.toLocaleString('fr-CA', {maximumFractionDigits:0}) + ' $';
    const netLabel = r ? `<span style="font-size:11px;color:#22c55e;margin-left:4px">(net ${fmt(r.net)})</span>` : '';

    const tbody = document.getElementById('revenu-list');
    const tr = document.createElement('tr');
    tr.dataset.revenuAnnuel = annuel;
    tr.dataset.owner = owner;
    tr.dataset.revenuType = isEmploi ? 'emploi' : 'autre';
    tr.dataset.formJson = JSON.stringify({ type, owner, isEmploi, description, montant, frequence, freqFactor, annuel });
    tr.innerHTML = `
      <td>${propTx}</td>
      <td>${isEmploi ? 'Emploi' : 'Autre'}</td>
      <td>${description}</td>
      <td>${montant} $${netLabel}</td>
      <td>${frequence}</td>
      <td class="col-action">
        <button class="re-action-btn" title="Détail fiscal" onclick="reToggleDetail(this)" style="color:var(--navy)">
          <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <button class="re-action-btn" title="Modifier" onclick="showToast('Modification non disponible dans la démo')">
          <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </button>
        <button class="re-action-btn del" title="Supprimer" onclick="reDeleteRow(this)">
          <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </button>
      </td>`;
    tbody.appendChild(tr);

    // Detail row (collapsed by default)
    const trDetail = document.createElement('tr');
    trDetail.className = 're-detail-row';
    trDetail.style.display = 'none';
    trDetail.innerHTML = r ? `<td colspan="6"><div class="re-detail-inner">
      <div class="re-detail-item"><span class="re-detail-lbl">RRQ</span><span class="re-detail-val deduction">${fmt(r.rrq)}</span></div>
      <div class="re-detail-item"><span class="re-detail-lbl">AE</span><span class="re-detail-val deduction">${fmt(r.ae)}</span></div>
      <div class="re-detail-item"><span class="re-detail-lbl">RQAP</span><span class="re-detail-val deduction">${fmt(r.rqap)}</span></div>
      <div class="re-detail-item"><span class="re-detail-lbl">Impôt fédéral</span><span class="re-detail-val deduction">${fmt(r.fed)}</span></div>
      <div class="re-detail-item"><span class="re-detail-lbl">Impôt Québec</span><span class="re-detail-val deduction">${fmt(r.qc)}</span></div>
      <div class="re-detail-item"><span class="re-detail-lbl">Taux effectif</span><span class="re-detail-val">${r.taux.toFixed(1).replace('.',',')} %</span></div>
      <div class="re-detail-item" style="grid-column:1/3"><span class="re-detail-lbl">Net annuel</span><span class="re-detail-val net">${fmt(r.net)}</span></div>
      <div class="re-detail-item"><span class="re-detail-lbl">Net mensuel</span><span class="re-detail-val net">${fmt(r.net/12)}</span></div>
    </div></td>` : `<td colspan="6"><div style="padding:8px 14px;font-size:12px;color:var(--muted)">Calcul non disponible.</div></td>`;
    tbody.appendChild(trDetail);

    closeRevenuModal();
    updateReSidebar();
  }

  function reToggleDetail(btn) {
    const tr = btn.closest('tr');
    const detail = tr.nextElementSibling;
    if (!detail || !detail.classList.contains('re-detail-row')) return;
    const open = detail.style.display !== 'none';
    detail.style.display = open ? 'none' : '';
    const svg = btn.querySelector('svg');
    if (svg) svg.style.transform = open ? '' : 'rotate(180deg)';
  }

  function reDeleteRow(btn) {
    const tr = btn.closest('tr');
    const detail = tr.nextElementSibling;
    if (detail && detail.classList.contains('re-detail-row')) detail.remove();
    tr.remove();
    updateReSidebar();
  }

  function fmtMoney(n) {
    return n.toLocaleString('fr-CA', { maximumFractionDigits: 0 }) + ' $';
  }

  function updateEpargneSection() {
    const actifItems = document.querySelectorAll('#actifs-list [data-valeur]');
    const emptyDiv   = document.getElementById('epargne-empty');
    const tabsWrap   = document.getElementById('epargne-tabs-wrap');
    if (actifItems.length === 0) {
      if (emptyDiv)  emptyDiv.style.display  = '';
      if (tabsWrap)  tabsWrap.style.display  = 'none';
    } else {
      if (emptyDiv)  emptyDiv.style.display  = 'none';
      if (tabsWrap)  tabsWrap.style.display  = '';
      // Update tab labels
      const cn = getClientPrenom();
      const cj = getConjointPrenom();
      const tabC = document.getElementById('etab-client');
      const tabJ = document.getElementById('etab-conjoint');
      if (tabC) tabC.textContent = cn;
      if (tabJ) { tabJ.textContent = cj || 'Conjoint(e)'; tabJ.style.display = cj ? '' : 'none'; }
      // Build dropdowns
      epBuildDropdown('client');
      if (cj) epBuildDropdown('conjoint');
    }
  }
  function epBuildDropdown(who) {
    const ul = document.getElementById('ep-dd-' + who + '-list');
    if (!ul) return;
    ul.innerHTML = '';
    const actifs = document.querySelectorAll('#actifs-list [data-valeur][data-modal-type="placement"]');
    let count = 0;
    actifs.forEach(item => {
      const owner = item.dataset.owner || 'client';
      if (owner !== who && owner !== 'both') return;
      const type = item.dataset.aptype || '?';
      const name = who === 'client' ? getClientPrenom() : (getConjointPrenom() || 'Conjoint(e)');
      const label = type + ' (' + name + ')';
      const li = document.createElement('li');
      li.innerHTML = `<button class="legal-menu-item" onclick="openEpargneModal('${label.replace(/'/g,"\\'")}','${who}')">${label}</button>`;
      ul.appendChild(li);
      count++;
    });
    if (count === 0) {
      ul.innerHTML = '<li style="padding:10px 16px;color:var(--muted);font-size:13px">Aucun actif disponible</li>';
    }
  }
  function toggleEpargneDropdown(who) {
    const dd = document.getElementById('ep-dd-' + who);
    if (!dd) return;
    const wasOpen = dd.style.display === 'block';
    document.querySelectorAll('#ep-dd-client, #ep-dd-conjoint').forEach(d => d.style.display = 'none');
    if (!wasOpen) {
      epBuildDropdown(who);
      const btn = document.querySelector('#ep-btn-' + who + '-wrap button');
      if (btn) {
        const r = btn.getBoundingClientRect();
        dd.style.position = 'fixed';
        dd.style.top  = (r.bottom + 4) + 'px';
        dd.style.left = r.left + 'px';
        dd.style.zIndex = '9999';
      }
      dd.style.display = 'block';
    }
  }
  function switchEpargneTab(who, btn) {
    document.querySelectorAll('.re-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('epanel-client').style.display   = who === 'client'   ? '' : 'none';
    document.getElementById('epanel-conjoint').style.display = who === 'conjoint' ? '' : 'none';
  }
  let _epargneWho = 'client';
  function openEpargneModal(label, who) {
    document.getElementById('ep-dd-' + who).style.display = 'none';
    _epargneWho = who;
    document.getElementById('ep-modal-title').textContent = label;
    document.getElementById('modal-epargne').dataset.label = label;
    document.getElementById('ep-montant').value = '';
    document.getElementById('ep-frequence').value = '12';
    document.getElementById('ep-indexe-non').checked = true;
    document.getElementById('ep-taux-indexation').value = '0,00';
    document.getElementById('ep-debut-mois').value = '';
    document.getElementById('ep-debut-annee').value = '';
    document.getElementById('ep-fin-type').value = 'retirement';
    document.getElementById('modal-epargne').classList.add('open');
    document.getElementById('ep-montant').focus();
  }
  function closeEpargneModal() {
    document.getElementById('modal-epargne').classList.remove('open');
  }
  document.getElementById('modal-epargne')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-epargne')) closeEpargneModal();
  });
  function saveEpargne() {
    const label   = document.getElementById('modal-epargne').dataset.label || '';
    const montant = parseFloat((document.getElementById('ep-montant').value || '0').replace(',', '.')) || 0;
    const freqSel = document.getElementById('ep-frequence');
    const freqVal = freqSel.value;
    const freqTx  = freqSel.options[freqSel.selectedIndex].text;
    const list    = document.getElementById('ep-list-' + _epargneWho);
    if (!list) { closeEpargneModal(); return; }

    // Calcul du montant annuel
    const annuel = freqVal === 'onetime' ? montant : montant * parseFloat(freqVal);

    const item = document.createElement('div');
    item.dataset.montant = annuel; // montant annuel pour le flux monétaire
    item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);font-size:13px';
    item.innerHTML = `
      <div>
        <div style="font-weight:600;color:var(--navy)">${label}</div>
        <div style="color:var(--muted);font-size:11px;margin-top:2px">${montant ? montant.toLocaleString('fr-CA') + ' $ · ' : ''}${freqTx}</div>
      </div>
      <div style="display:flex;gap:2px">
        <button onclick="editEpargneItem(this.parentElement.parentElement)" title="Modifier" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:15px;padding:2px 5px">✎</button>
        <button onclick="this.parentElement.parentElement.remove();updateReSidebar();" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:18px;padding:0 4px">×</button>
      </div>`;
    list.appendChild(item);
    closeEpargneModal();
    updateReSidebar();
  }
  function editEpargneItem(item) { showToast('Modification disponible dans la version complète'); }

  const CELI_LIMITS = {
    2009:5000,2010:5000,2011:5000,2012:5000,
    2013:5500,2014:5500,2015:10000,
    2016:5500,2017:5500,2018:5500,
    2019:6000,2020:6000,2021:6000,2022:6000,
    2023:6500,2024:7000,2025:7000,2026:7000
  };
  function calcDroitsCeli(who) {
    const curY = new Date().getFullYear();
    const birthYear = parseInt(document.getElementById(who==='client'?'client-naissance-annee':'conjoint-naissance-annee')?.value) || null;
    const residYear = parseInt(document.getElementById(who==='client'?'client-canada-depuis':'conjoint-canada-depuis')?.value) || null;
    let startY = 2009;
    if (residYear && residYear > startY) startY = residYear;
    if (birthYear && birthYear + 18 > startY) startY = birthYear + 18;
    if (startY > curY) return 0;
    let room = 0;
    for (let y = startY; y <= curY; y++) room += CELI_LIMITS[y] || 7000;
    let celiActif = 0;
    document.querySelectorAll('#actifs-list [data-aptype="CELI"]').forEach(item => {
      if (item.dataset.owner === who || item.dataset.owner === 'both') celiActif += parseFloat(item.dataset.valeur) || 0;
    });
    return Math.max(0, room - celiActif);
  }
  function calcDroitsCeliapp(who) {
    const curY = new Date().getFullYear();
    let openingYear = null, celiappActif = 0;
    document.querySelectorAll('#actifs-list [data-aptype="CELIAPP"]').forEach(item => {
      if (item.dataset.owner === who || item.dataset.owner === 'both') {
        celiappActif += parseFloat(item.dataset.valeur) || 0;
        if (!openingYear) { try { const fd = JSON.parse(item.dataset.formJson||'{}'); if (fd.dateOuverture) openingYear = parseInt(fd.dateOuverture); } catch(e){} }
      }
    });
    if (!openingYear) return null;
    const yearsElig = Math.max(0, curY - openingYear + 1);
    const totalRoom = Math.min(yearsElig * 8000, 40000);
    return Math.max(0, totalRoom - celiappActif);
  }
  function calcDroitsReer(who) {
    const MAX_REER = 32490;
    let annuel = 0;
    document.querySelectorAll('#revenu-list tr').forEach(tr => {
      if (tr.dataset.owner === who || (!tr.dataset.owner && who === 'client')) annuel += parseFloat(tr.dataset.revenuAnnuel) || 0;
    });
    return Math.min(Math.floor(annuel * 0.18), MAX_REER);
  }
  function placDateOuvertureChange() {
    const val = document.getElementById('plac-date-ouverture')?.value.trim();
    const btn = document.getElementById('plac-save-btn');
    if (btn) btn.disabled = !val;
  }

  function updateReSidebar() {
    updateEpargneSection();
    const clientPrenom = getClientPrenom();
    const conjointPrenom = getConjointPrenom();

    // Update prefill row owner name
    const prefillOwner = document.getElementById('re-prefill-owner');
    if (prefillOwner) prefillOwner.textContent = clientPrenom;

    // Update droits de cotisation column headers
    const dcClientCol = document.getElementById('dc-client-col');
    if (dcClientCol) dcClientCol.textContent = clientPrenom;
    const dcConjointCol = document.getElementById('dc-conjoint-col');
    if (dcConjointCol) {
      dcConjointCol.textContent = conjointPrenom || '';
      dcConjointCol.style.display = conjointPrenom ? '' : 'none';
    }
    ['dc-conjoint-reer-cell','dc-conjoint-celi-cell','dc-conjoint-celiapp-cell'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.style.display = conjointPrenom ? '' : 'none';
    });

    // Sum annual revenue per owner
    let clientAnnuel = 0, conjointAnnuel = 0;
    document.querySelectorAll('#revenu-list tr').forEach(tr => {
      const val = parseFloat(tr.dataset.revenuAnnuel) || 0;
      if (tr.dataset.owner === 'conjoint') conjointAnnuel += val;
      else clientAnnuel += val;
    });

    const divisor = reTabMode === 'mensuel' ? 12 : 1;
    const freqLabel = reTabMode === 'mensuel' ? 'mensuel' : 'annuel';

    // Animate donut arc (circumference = 2π×32 ≈ 201)
    function setDonut(arcId, totalNum, maxVal) {
      const arc = document.getElementById(arcId);
      if (!arc) return;
      const c = 201;
      const ratio = maxVal > 0 ? Math.min(totalNum / maxVal, 1) : 0;
      arc.style.strokeDashoffset = c - ratio * c;
    }
    const allAnnuel = clientAnnuel + conjointAnnuel;

    // Somme épargne annuelle par propriétaire
    let clientEpargneAnnuel = 0, conjointEpargneAnnuel = 0;
    document.querySelectorAll('#ep-list-client [data-montant]').forEach(el => {
      clientEpargneAnnuel += parseFloat(el.dataset.montant) || 0;
    });
    document.querySelectorAll('#ep-list-conjoint [data-montant]').forEach(el => {
      conjointEpargneAnnuel += parseFloat(el.dataset.montant) || 0;
    });

    // Compute net via tax engine
    const clientImpot   = computeImpot(clientAnnuel);
    const clientNetAnnuel = clientImpot ? clientImpot.net : clientAnnuel;

    // Client sidebar
    document.getElementById('re-client-name').textContent = clientPrenom;
    const clientNetDisp  = clientNetAnnuel / divisor;
    const clientBrutDisp = clientAnnuel / divisor;
    const clientImpotDisp = clientImpot ? clientImpot.total / divisor : 0;
    const clientEpDisp   = clientEpargneAnnuel / divisor;
    const clientDepDisp  = Math.max(0, clientNetDisp - clientEpDisp);
    document.getElementById('re-client-revenu').textContent   = fmtMoney(clientBrutDisp);
    document.getElementById('re-client-impot').textContent    = fmtMoney(clientImpotDisp);
    document.getElementById('re-client-net').textContent      = fmtMoney(clientNetDisp);
    document.getElementById('re-client-epargne').textContent  = fmtMoney(clientEpDisp);
    document.getElementById('re-client-depenses').textContent = fmtMoney(clientDepDisp);
    document.getElementById('re-client-total-label').textContent = fmtMoney(clientNetDisp);
    document.getElementById('re-client-freq-label').textContent  = freqLabel;
    setDonut('re-client-donut-arc', clientNetAnnuel, clientNetAnnuel || 1);

    // Conjoint sidebar
    const conjBlock = document.getElementById('re-conjoint-block');
    if (conjBlock) {
      conjBlock.style.display = conjointPrenom ? '' : 'none';
      if (conjointPrenom) {
        const conjImpot     = computeImpot(conjointAnnuel);
        const conjNetAnnuel = conjImpot ? conjImpot.net : conjointAnnuel;
        document.getElementById('re-conjoint-name').textContent = conjointPrenom;
        const conjNetDisp   = conjNetAnnuel / divisor;
        const conjBrutDisp  = conjointAnnuel / divisor;
        const conjImpotDisp = conjImpot ? conjImpot.total / divisor : 0;
        const conjEpDisp    = conjointEpargneAnnuel / divisor;
        const conjDepDisp   = Math.max(0, conjNetDisp - conjEpDisp);
        document.getElementById('re-conjoint-revenu').textContent   = fmtMoney(conjBrutDisp);
        document.getElementById('re-conjoint-impot').textContent    = fmtMoney(conjImpotDisp);
        document.getElementById('re-conjoint-net').textContent      = fmtMoney(conjNetDisp);
        document.getElementById('re-conjoint-epargne').textContent  = fmtMoney(conjEpDisp);
        document.getElementById('re-conjoint-depenses').textContent = fmtMoney(conjDepDisp);
        document.getElementById('re-conjoint-total-label').textContent = fmtMoney(conjNetDisp);
        document.getElementById('re-conjoint-freq-label').textContent  = freqLabel;
        const allNet = clientNetAnnuel + conjNetAnnuel;
        setDonut('re-conjoint-donut-arc', conjNetAnnuel, allNet || 1);
        setDonut('re-client-donut-arc',   clientNetAnnuel, allNet || 1);
      }
    }

    // Auto-calcul droits de cotisation
    ['client','conjoint'].forEach(who => {
      const sfx = who;
      const reerInput    = document.getElementById('dc-' + sfx + '-reer');
      const celiInput    = document.getElementById('dc-' + sfx + '-celi');
      const celiappInput = document.getElementById('dc-' + sfx + '-celiapp');
      if (who === 'conjoint' && !conjointPrenom) return;
      if (reerInput && !reerInput.dataset.manualOverride)    reerInput.value    = calcDroitsReer(who).toLocaleString('fr-CA');
      if (celiInput && !celiInput.dataset.manualOverride)    celiInput.value    = calcDroitsCeli(who).toLocaleString('fr-CA');
      const celiappVal = calcDroitsCeliapp(who);
      if (celiappInput && !celiappInput.dataset.manualOverride && celiappVal !== null) celiappInput.value = celiappVal.toLocaleString('fr-CA');
    });
  }

  // ── PAGE ACCUEIL ──────────────────────────────────
  function demarrerABF() {
    document.getElementById('page-accueil').style.display = 'none';
  }
  // ── Gestion de l'impôt ────────────────────────
  function openImpotModal()    { openConfigModal('impot'); }
  function openImpotModalFor() { openConfigModal('impot'); }
  function closeImpotModal()   { closeConfigModal(); }

  // ── Hypothèses ────────────────────────────────
  let hypotheses = { evClient:94, evConj:96 };

  function openHypothesesModal() {
    // Mirror Valeurs par défaut data → Hypothèses inputs
    const copy = (from, to) => { const el = document.getElementById(from); if(el) document.getElementById(to).value = el.value; };
    copy('vd-inflation',   'hyp-inflation');
    copy('vd-p-prudent',   'hyp-port-prudent');
    copy('vd-p-modere',    'hyp-port-modere');
    copy('vd-p-equilibre', 'hyp-port-equilibre');
    copy('vd-p-croissance','hyp-port-croissance');
    copy('vd-p-audacieux', 'hyp-port-audacieux');
    // Espérance de vie (local)
    document.getElementById('hyp-ev-client').value = String(hypotheses.evClient);
    document.getElementById('hyp-ev-conj').value   = String(hypotheses.evConj);
    // Labels
    const cp = getClientPrenom(), jp = getConjointPrenom();
    const evClientLbl = document.getElementById('hyp-ev-client-label');
    const evConjWrap  = document.getElementById('hyp-ev-conj-wrap');
    const evConjLbl   = document.getElementById('hyp-ev-conj-label');
    if (evClientLbl) evClientLbl.textContent = cp || 'Client';
    if (evConjWrap)  evConjWrap.style.display = jp ? '' : 'none';
    if (evConjLbl)   evConjLbl.textContent = jp || 'Conjoint(e)';
    document.getElementById('modal-hypotheses').style.display = 'flex';
  }
  function closeHypothesesModal() {
    document.getElementById('modal-hypotheses').style.display = 'none';
  }
  function resetHypotheses() {
    // Reset vd-* fields to defaults then reopen
    const defaults = {'vd-inflation':'2,10','vd-p-prudent':'3,00','vd-p-modere':'3,30','vd-p-equilibre':'3,70','vd-p-croissance':'4,00','vd-p-audacieux':'4,30'};
    Object.entries(defaults).forEach(([id, v]) => { const el = document.getElementById(id); if(el) el.value = v; });
    hypotheses.evClient = 94; hypotheses.evConj = 96;
    openHypothesesModal();
  }
  function saveHypotheses() {
    // Write back to Valeurs par défaut fields
    const copy = (from, to) => { const el = document.getElementById(from); if(el) document.getElementById(to).value = el.value; };
    copy('hyp-inflation',    'vd-inflation');
    copy('hyp-port-prudent', 'vd-p-prudent');
    copy('hyp-port-modere',  'vd-p-modere');
    copy('hyp-port-equilibre','vd-p-equilibre');
    copy('hyp-port-croissance','vd-p-croissance');
    copy('hyp-port-audacieux','vd-p-audacieux');
    // Espérance de vie (local)
    hypotheses.evClient = parseInt(document.getElementById('hyp-ev-client')?.value||'94') || 94;
    hypotheses.evConj   = parseInt(document.getElementById('hyp-ev-conj')?.value||'96')   || 96;
    closeHypothesesModal();
    showToast('Hypothèses enregistrées');
  }
  document.getElementById('modal-hypotheses')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-hypotheses')) closeHypothesesModal();
  });

  // ── Rente conjoint survivant ───────────────────
  let rrqRenteParams = {
    regime: 'rrq',
    annee: 2026,
    m45SansEnfant: 719.50,
    m45AvecEnfant: 1129.95,
    m45Invalide:   1134.61,
    de45a65:       1173.58,
    de65plus:      881.48,
    cppFixedPortion: 217.83
  };
  function rcToggleRegime() {
    const isCpp = document.getElementById('rc-regime-cpp')?.checked;
    document.getElementById('rc-rrq-section').style.display = isCpp ? 'none' : '';
    document.getElementById('rc-rrq-header').style.display  = isCpp ? 'none' : '';
    document.getElementById('rc-cpp-section').style.display = isCpp ? '' : 'none';
  }
  function rcUpdatePanelHelpers() {
    // Show/hide invalide vs rente-defunt based on regime
    const isCpp = rrqRenteParams.regime === 'cpp';
    ['c','j'].forEach(sfx => {
      const invWrap = document.getElementById(`deces-invalide-${sfx}-wrap`);
      const defWrap = document.getElementById(`deces-rente-defunt-${sfx}-wrap`);
      if (invWrap) invWrap.style.display = isCpp ? 'none' : '';
      if (defWrap) defWrap.style.display = isCpp ? '' : 'none';
    });
  }
  function openRenteConjModal()  { openConfigModal('rente'); }
  function closeRenteConjModal() { closeConfigModal(); }
  function saveRenteConjModal() {
    const parse = id => parseFloat((document.getElementById(id)?.value||'0').replace(/\s/g,'').replace(',','.')) || 0;
    rrqRenteParams.regime        = document.querySelector('input[name="rc-regime"]:checked')?.value || 'rrq';
    rrqRenteParams.annee         = parseInt(document.getElementById('rc-annee')?.value) || 2026;
    rrqRenteParams.m45SansEnfant = parse('rc-m45-sans');
    rrqRenteParams.m45AvecEnfant = parse('rc-m45-avec');
    rrqRenteParams.m45Invalide   = parse('rc-m45-inv');
    rrqRenteParams.de45a65       = parse('rc-45-65');
    rrqRenteParams.de65plus      = parse('rc-65plus');
    rrqRenteParams.cppFixedPortion = parse('rc-cpp-fixed');
    rcUpdatePanelHelpers();
    closeRenteConjModal();
    showToast('Paramètres rente mis à jour');
  }
  function rcReset() {
    rrqRenteParams = { regime:'rrq', annee:2026, m45SansEnfant:719.50, m45AvecEnfant:1129.95, m45Invalide:1134.61, de45a65:1173.58, de65plus:881.48, cppFixedPortion:217.83 };
    openRenteConjModal();
  }
  // Vérifie si le survivant (owner = 'client' ou 'conjoint') a des enfants à charge
  function survivorHasChildren(survivorOwner) {
    let found = false;
    document.querySelectorAll('#enfants-list .enfant-item[data-charge]').forEach(el => {
      const c = el.dataset.charge;
      if (c === survivorOwner || c === 'both') found = true;
    });
    return found;
  }

  function resetRenteConj(sfx) {
    const clientBirthYear = parseInt(document.getElementById('client-naissance-annee')?.value) || 0;
    const conjBirthYear   = parseInt(document.getElementById('conjoint-naissance-annee')?.value) || 0;
    const survivorOwner     = sfx === 'c' ? 'conjoint' : 'client';
    const survivorBirthYear = sfx === 'c' ? conjBirthYear : clientBirthYear;
    const hasChildren  = survivorHasChildren(survivorOwner);
    const isInvalide   = document.getElementById(`deces-invalide-${sfx}`)?.checked || false;
    const defuntRente  = parseFloat((document.getElementById(`deces-rente-defunt-${sfx}`)?.value||'0').replace(/\s/g,'').replace(',','.')) || 0;
    const sugg = getRenteConjSuggestion(survivorBirthYear, hasChildren, isInvalide, defuntRente) * 12;
    const field = document.getElementById(`deces-rente-conjoint-${sfx}`);
    if (field) { field.value = Math.round(sugg).toLocaleString('fr-CA'); decesCalc(); }
  }

  // Calcule le montant mensuel de rente suggéré (RRQ ou CPP)
  // deceasedMonthlyPension = rente de retraite mensuelle du défunt (pour CPP)
  // isInvalide = survivant invalide <45 ans (pour RRQ)
  function getRenteConjSuggestion(birthYear, hasChildren, isInvalide, deceasedMonthlyPension) {
    if (!birthYear) return 0;
    const age = new Date().getFullYear() - birthYear;
    if (rrqRenteParams.regime === 'cpp') {
      const d = deceasedMonthlyPension || 0;
      if (age >= 65) return 0.60 * d;
      return rrqRenteParams.cppFixedPortion + 0.375 * d;
    }
    // RRQ
    if (age >= 65) return rrqRenteParams.de65plus;
    if (age >= 45) return rrqRenteParams.de45a65;
    if (isInvalide) return rrqRenteParams.m45Invalide;
    return hasChildren ? rrqRenteParams.m45AvecEnfant : rrqRenteParams.m45SansEnfant;
  }
  function impotRenderParams() {
    const p = fiscalParams;
    const fmtN = n => n === Infinity ? '∞' : String(n);
    // Federal brackets
    const fedTbody = document.getElementById('impot-fed-brackets');
    if (fedTbody) {
      fedTbody.innerHTML = '';
      p.fed.brackets.forEach((b, i) => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--border)';
        tr.innerHTML = `<td style="padding:5px 10px">
          <input class="form-input" id="fp-fed-max-${i}" type="text" value="${b.max === Infinity ? '' : b.max}"
            placeholder="∞ (dernier palier)" style="font-size:12px;padding:5px 8px;width:160px"/>
        </td><td style="padding:5px 10px">
          <input class="form-input" id="fp-fed-rate-${i}" type="text" value="${b.rate}"
            style="font-size:12px;padding:5px 8px;width:80px"/>
        </td>`;
        fedTbody.appendChild(tr);
      });
    }
    // Federal base amounts
    const setVal = (id, v) => { const el = document.getElementById(id); if (el) el.value = v; };
    setVal('fp-fed-baseMax',        p.fed.baseMax);
    setVal('fp-fed-baseMin',        p.fed.baseMin);
    setVal('fp-fed-baseThreshLow',  p.fed.baseThreshLow);
    setVal('fp-fed-baseThreshHigh', p.fed.baseThreshHigh);
    setVal('fp-fed-creditRate',     p.fed.creditRate);
    // Quebec brackets
    const qcTbody = document.getElementById('impot-qc-brackets');
    if (qcTbody) {
      qcTbody.innerHTML = '';
      p.qc.brackets.forEach((b, i) => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--border)';
        tr.innerHTML = `<td style="padding:5px 10px">
          <input class="form-input" id="fp-qc-max-${i}" type="text" value="${b.max === Infinity ? '' : b.max}"
            placeholder="∞ (dernier palier)" style="font-size:12px;padding:5px 8px;width:160px"/>
        </td><td style="padding:5px 10px">
          <input class="form-input" id="fp-qc-rate-${i}" type="text" value="${b.rate}"
            style="font-size:12px;padding:5px 8px;width:80px"/>
        </td>`;
        qcTbody.appendChild(tr);
      });
    }
    // Quebec base
    setVal('fp-qc-base',       p.qc.base);
    setVal('fp-qc-creditRate', p.qc.creditRate);
    // Cotisations
    setVal('fp-rrq-exemption', p.rrq.exemption);
    setVal('fp-rrq-ceil1',     p.rrq.ceil1);
    setVal('fp-rrq-rate1',     p.rrq.rate1);
    setVal('fp-rrq-ceil2',     p.rrq.ceil2);
    setVal('fp-rrq-rate2',     p.rrq.rate2);
    setVal('fp-ae-ceil',       p.ae.ceil);
    setVal('fp-ae-rate',       p.ae.rate);
    setVal('fp-rqap-ceil',     p.rqap.ceil);
    setVal('fp-rqap-rate',     p.rqap.rate);
  }
  function impotSaveParams() {
    const getN = id => parseFloat((document.getElementById(id)?.value || '').replace(/\s/g,'').replace(',','.')) || 0;
    const p = fiscalParams;
    // Federal brackets
    p.fed.brackets.forEach((b, i) => {
      const maxVal = document.getElementById(`fp-fed-max-${i}`)?.value.trim();
      b.max  = (maxVal === '' || maxVal === '∞') ? Infinity : parseFloat(maxVal) || b.max;
      b.rate = parseFloat(document.getElementById(`fp-fed-rate-${i}`)?.value) || b.rate;
    });
    p.fed.baseMax        = getN('fp-fed-baseMax')        || p.fed.baseMax;
    p.fed.baseMin        = getN('fp-fed-baseMin')        || p.fed.baseMin;
    p.fed.baseThreshLow  = getN('fp-fed-baseThreshLow')  || p.fed.baseThreshLow;
    p.fed.baseThreshHigh = getN('fp-fed-baseThreshHigh') || p.fed.baseThreshHigh;
    p.fed.creditRate     = getN('fp-fed-creditRate')     || p.fed.creditRate;
    // Quebec brackets
    p.qc.brackets.forEach((b, i) => {
      const maxVal = document.getElementById(`fp-qc-max-${i}`)?.value.trim();
      b.max  = (maxVal === '' || maxVal === '∞') ? Infinity : parseFloat(maxVal) || b.max;
      b.rate = parseFloat(document.getElementById(`fp-qc-rate-${i}`)?.value) || b.rate;
    });
    p.qc.base       = getN('fp-qc-base')       || p.qc.base;
    p.qc.creditRate = getN('fp-qc-creditRate') || p.qc.creditRate;
    // Cotisations
    p.rrq.exemption = getN('fp-rrq-exemption');
    p.rrq.ceil1     = getN('fp-rrq-ceil1')  || p.rrq.ceil1;
    p.rrq.rate1     = getN('fp-rrq-rate1')  || p.rrq.rate1;
    p.rrq.ceil2     = getN('fp-rrq-ceil2')  || p.rrq.ceil2;
    p.rrq.rate2     = getN('fp-rrq-rate2')  || p.rrq.rate2;
    p.ae.ceil       = getN('fp-ae-ceil')    || p.ae.ceil;
    p.ae.rate       = getN('fp-ae-rate')    || p.ae.rate;
    p.rqap.ceil     = getN('fp-rqap-ceil')  || p.rqap.ceil;
    p.rqap.rate     = getN('fp-rqap-rate')  || p.rqap.rate;
    updateReSidebar();
    closeImpotModal();
    showToast('Paramètres fiscaux mis à jour');
  }
  function impotResetParams() {
    fiscalParams = JSON.parse(JSON.stringify(FISCAL_2026));
    fiscalParams.fed.brackets[4].max = Infinity;
    fiscalParams.qc.brackets[3].max  = Infinity;
    impotRenderParams();
  }
  // ── Paramètres fiscaux (modifiables via Gestion de l'impôt) ──
  const FISCAL_2026 = {
    fed: {
      brackets: [
        { max: 58523,   rate: 14   },
        { max: 117045,  rate: 20.5 },
        { max: 181440,  rate: 26   },
        { max: 258482,  rate: 29   },
        { max: Infinity, rate: 33  }
      ],
      baseMax: 16452, baseMin: 14829,
      baseThreshLow: 173205, baseThreshHigh: 235675,
      creditRate: 15
    },
    qc: {
      brackets: [
        { max: 54345,   rate: 14   },
        { max: 108680,  rate: 19   },
        { max: 132245,  rate: 24   },
        { max: Infinity, rate: 25.75 }
      ],
      base: 18952, creditRate: 14
    },
    rrq:  { exemption: 3500, ceil1: 74600, rate1: 5.4, ceil2: 85000, rate2: 1.0 },
    ae:   { ceil: 68900,  rate: 1.30  },
    rqap: { ceil: 103000, rate: 0.430 }
  };
  let fiscalParams = JSON.parse(JSON.stringify(FISCAL_2026));
  // Fix Infinity after JSON round-trip
  fiscalParams.fed.brackets[4].max = Infinity;
  fiscalParams.qc.brackets[3].max  = Infinity;

  function computeImpot(brut) {
    if (brut <= 0) return null;
    const p = fiscalParams;
    // RRQ
    const rrq = Math.max(0, Math.min(brut, p.rrq.ceil1) - p.rrq.exemption) * (p.rrq.rate1 / 100)
              + Math.max(0, Math.min(brut, p.rrq.ceil2) - p.rrq.ceil1)     * (p.rrq.rate2 / 100);
    // AE
    const ae   = Math.min(brut, p.ae.ceil)   * (p.ae.rate   / 100);
    // RQAP
    const rqap = Math.min(brut, p.rqap.ceil) * (p.rqap.rate / 100);
    // Fédéral
    let fed = 0, prev = 0;
    for (const b of p.fed.brackets) {
      const slice = Math.min(brut, b.max === Infinity ? brut : b.max) - prev;
      if (slice <= 0) break;
      fed += slice * (b.rate / 100);
      prev = b.max === Infinity ? brut : b.max;
      if (b.max === Infinity || brut <= b.max) break;
    }
    const baseFed = brut <= p.fed.baseThreshLow  ? p.fed.baseMax
                  : brut >= p.fed.baseThreshHigh ? p.fed.baseMin
                  : p.fed.baseMax - (brut - p.fed.baseThreshLow) / (p.fed.baseThreshHigh - p.fed.baseThreshLow) * (p.fed.baseMax - p.fed.baseMin);
    fed = Math.max(0, fed - baseFed * (p.fed.creditRate / 100));
    // Québec (cotisations déductibles du revenu imposable)
    const qcRev = brut - rrq - ae - rqap;
    let qc = 0; prev = 0;
    for (const b of p.qc.brackets) {
      const slice = Math.min(qcRev, b.max === Infinity ? qcRev : b.max) - prev;
      if (slice <= 0) break;
      qc += slice * (b.rate / 100);
      prev = b.max === Infinity ? qcRev : b.max;
      if (b.max === Infinity || qcRev <= b.max) break;
    }
    qc = Math.max(0, qc - p.qc.base * (p.qc.creditRate / 100));
    const total = rrq + ae + rqap + fed + qc;
    return { rrq, ae, rqap, fed, qc, total, net: brut - total, taux: total / brut * 100 };
  }

  // ── Modal Configuration unifié ────────────────
  function openConfigModal(tab) {
    switchConfigTab(tab || 'profil');
    if (tab === 'impot') impotRenderParams();
    if (tab === 'rente') _rcPopulateFields();
    document.getElementById('modal-config').classList.add('open');
  }
  function closeConfigModal() {
    document.getElementById('modal-config').classList.remove('open');
  }
  function switchConfigTab(tab) {
    document.querySelectorAll('.cfg-tab-btn').forEach(b => b.classList.toggle('active', b.dataset.tab === tab));
    document.querySelectorAll('.cfg-tab-pane').forEach(p => p.classList.toggle('active', p.id === 'cfg-tab-' + tab));
    document.querySelectorAll('.cfg-footer-pane').forEach(f => f.classList.toggle('active', f.id === 'cfg-footer-' + tab));
    if (tab === 'impot') impotRenderParams();
    if (tab === 'rente') _rcPopulateFields();
  }
  function _rcPopulateFields() {
    const fmt = v => v.toLocaleString('fr-CA', {minimumFractionDigits:2, maximumFractionDigits:2});
    const isCpp = rrqRenteParams.regime === 'cpp';
    const rrqRadio = document.getElementById('rc-regime-rrq');
    const cppRadio = document.getElementById('rc-regime-cpp');
    if (rrqRadio) rrqRadio.checked = !isCpp;
    if (cppRadio) cppRadio.checked = isCpp;
    rcToggleRegime();
    const sv = (id, v) => { const el = document.getElementById(id); if (el) el.value = v; };
    sv('rc-annee',    rrqRenteParams.annee);
    sv('rc-m45-sans', fmt(rrqRenteParams.m45SansEnfant));
    sv('rc-m45-avec', fmt(rrqRenteParams.m45AvecEnfant));
    sv('rc-m45-inv',  fmt(rrqRenteParams.m45Invalide));
    sv('rc-45-65',    fmt(rrqRenteParams.de45a65));
    sv('rc-65plus',   fmt(rrqRenteParams.de65plus));
    sv('rc-cpp-fixed',fmt(rrqRenteParams.cppFixedPortion));
  }
  // Fermeture sur clic fond
  document.getElementById('modal-config')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-config')) closeConfigModal();
  });

  // ── Aliases pour compatibilité (ancien code / boutons existants) ──
  function openProfilModal()  { openConfigModal('profil'); }
  function closeProfilModal() { closeConfigModal(); }
  function saveProfilModal() {
    closeConfigModal();
    showToast('Profil enregistré');
  }
  function openValeursDefaut()  { openConfigModal('valeurs'); }
  function closeValeursDefaut() { closeConfigModal(); }

  /** Collecte tous les champs vd-* et les envoie au serveur pour les persister en DB. */
  function saveValeursDefaut() {
    const v = id => document.getElementById(id)?.value ?? null;
    const r = name => document.querySelector(`input[name="${name}"]:checked`)?.value ?? null;

    const params = {
      fonds_urgence: { type: r('vd-fu'), mois: v('vd-fu-mois') },
      deces:         { funerailles: v('vd-funerailles'), rr_type: r('vd-deces-rr'),
                       rr_pct: v('vd-deces-pct'), salaire_type: r('vd-deces-sal'),
                       frequence: r('vd-deces-freq') },
      invalidite:    { type: r('vd-inv-type'), salaire_type: r('vd-inv-sal'), rr_pct: v('vd-inv-pct') },
      maladie_grave: { niveau: r('vd-mg') },
      retraite:      { rr_pct: v('vd-ret-pct'), frequence: r('vd-ret-freq'), calcul: r('vd-ret-calc') },
      hypotheses:    { inflation: v('vd-inflation') },
      portefeuilles: { prudent: v('vd-p-prudent'), modere: v('vd-p-modere'),
                       equilibre: v('vd-p-equilibre'), croissance: v('vd-p-croissance'),
                       audacieux: v('vd-p-audacieux') },
      abf:           { province_defaut: v('vd-province') },
    };

    const btn    = document.getElementById('vd-save-btn');
    const status = document.getElementById('vd-save-status');
    if (btn) { btn.disabled = true; btn.textContent = 'Enregistrement…'; }
    if (status) { status.textContent = ''; status.style.display = 'none'; }

    fetch(window.ABF_PARAMS_SAVE_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.ABF_CSRF_TOKEN },
      body: JSON.stringify({ params }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.ok) {
        // Mettre à jour ABF_PARAMS en mémoire pour que les sections du formulaire utilisent les nouvelles valeurs
        if (data.params) window.ABF_PARAMS = data.params;
        if (status) { status.textContent = '✓ Enregistré'; status.style.display = 'inline'; status.style.color = 'var(--valid, #22c55e)'; }
        showToast('Valeurs par défaut enregistrées');
        setTimeout(closeValeursDefaut, 800);
      }
    })
    .catch(() => {
      if (status) { status.textContent = 'Erreur réseau'; status.style.display = 'inline'; status.style.color = '#ef4444'; }
    })
    .finally(() => {
      if (btn) { btn.disabled = false; btn.textContent = 'Enregistrer'; }
    });
  }

  /** Remet les valeurs IPF par défaut dans les champs (ne sauvegarde pas). */
  function resetValeursDefaut() {
    const defaults = {
      'vd-fu-mois': '3', 'vd-funerailles': '10 000', 'vd-deces-pct': '70',
      'vd-inv-pct': '70', 'vd-ret-pct': '70', 'vd-inflation': '2,10',
      'vd-p-prudent': '3,00', 'vd-p-modere': '3,30', 'vd-p-equilibre': '3,70',
      'vd-p-croissance': '4,00', 'vd-p-audacieux': '4,30',
    };
    Object.entries(defaults).forEach(([id, val]) => {
      const el = document.getElementById(id); if (el) el.value = val;
    });
    // Radios par défaut
    const setR = (name, val) => { const el = document.querySelector(`input[name="${name}"][value="${val}"]`); if (el) el.checked = true; };
    setR('vd-fu', 'income'); setR('vd-deces-rr', 'family'); setR('vd-deces-sal', 'gross');
    setR('vd-deces-freq', 'yearly'); setR('vd-inv-type', 'incomeReplacement');
    setR('vd-inv-sal', 'gross'); setR('vd-mg', 'comfort'); setR('vd-ret-freq', 'yearly');
    setR('vd-ret-calc', 'average');
  }
  function toggleAccordion(header) {
    const body = header.nextElementSibling;
    body.classList.toggle('open');
  }
  document.getElementById('modal-profil')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-profil')) closeProfilModal();
  });
  document.getElementById('modal-impot')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-impot')) closeImpotModal();
  });

  function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  /* ── Fonds d'urgence ── */
  const FU_ELIGIBLE_TYPES = ['Compte bancaire','CELI','Non enregistré'];

  function fuTypeChange() {
    const type = document.querySelector('input[name="fu-type"]:checked')?.value || 'income';
    document.getElementById('fu-row-income').style.display   = type === 'income'   ? 'flex' : 'none';
    document.getElementById('fu-row-expenses').style.display = type === 'expenses' ? 'flex' : 'none';
    document.getElementById('fu-row-amount').style.display   = type === 'amount'   ? 'flex' : 'none';
    fuCalc();
  }

  function fuRenderActifs() {
    const body = document.getElementById('fu-actifs-body');
    if (!body) return;
    const ownerLabel = o => {
      if (o === 'conjoint') return getConjointPrenom() || 'Conjoint';
      if (o === 'both') return getClientPrenom() + ' & ' + (getConjointPrenom() || 'Conjoint');
      return getClientPrenom();
    };
    const items = [];
    document.querySelectorAll('#actifs-list [data-aptype]').forEach(el => {
      const type = el.dataset.aptype || '';
      if (!FU_ELIGIBLE_TYPES.includes(type)) return;
      const valeur = parseFloat(el.dataset.valeur) || 0;
      items.push({ nom: type, valeur, owner: ownerLabel(el.dataset.owner || 'client') });
    });
    if (items.length === 0) {
      body.style.padding = '';
      body.innerHTML = '<p style="font-size:13px;color:var(--muted)">Aucun compte bancaire, CELI ou placement non enregistré disponible. <a href="#" onclick="goTo(\'actifs-passifs\',document.querySelectorAll(\'.nav-item\')[2]);return false;">Ajouter un actif.</a></p>';
      return;
    }
    body.style.padding = '0';
    body.innerHTML = `
      <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
          <tr style="background:#f8f9fb;border-bottom:2px solid var(--border)">
            <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.4px">Description</th>
            <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.4px">Propriétaire</th>
            <th style="padding:10px 14px;text-align:right;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.4px">Valeur</th>
            <th style="padding:10px 14px;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.4px">Montant alloué</th>
          </tr>
        </thead>
        <tbody>
          ${items.map(it => `
          <tr style="border-bottom:1px solid var(--border)">
            <td style="padding:10px 14px">${it.nom}</td>
            <td style="padding:10px 14px;color:var(--muted)">${it.owner}</td>
            <td style="padding:10px 14px;text-align:right;font-weight:600">${fmtMoney(it.valeur)}</td>
            <td style="padding:10px 14px">
              <div style="display:flex;align-items:center;gap:6px">
                <div class="input-sfx" style="flex:1;max-width:140px">
                  <input class="form-input fu-alloc-input" type="text" value="0" oninput="fuCalc()" style="padding-right:28px"/>
                  <span class="sfx">$</span>
                </div>
                <button onclick="this.closest('tr').querySelector('.fu-alloc-input').value='0';fuCalc()"
                  style="background:none;border:none;cursor:pointer;color:var(--muted);flex-shrink:0;padding:2px;line-height:1;display:flex;align-items:center" title="Effacer">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 24" width="18" height="18" fill="currentColor">
                    <path d="M12 0q-2.484 0-4.676 0.938t-3.82 2.566-2.566 3.82-0.938 4.676 0.938 4.676 2.566 3.82 3.82 2.566 4.676 0.938 4.676-0.938 3.82-2.566 2.566-3.82 0.938-4.676-0.938-4.676-2.566-3.82-3.82-2.566-4.676-0.938zM17.93 15.82l-2.109 2.109-3.82-3.82-3.82 3.82-2.109-2.109 3.82-3.82-3.82-3.82 2.109-2.109 3.82 3.82 3.82-3.82 2.109 2.109-3.82 3.82 3.82 3.82z"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>`).join('')}
        </tbody>
      </table>`;
    fuCalc();
  }

  function fuCalc() {
    const type = document.querySelector('input[name="fu-type"]:checked')?.value || 'income';
    let objectif = 0;

    if (type === 'none') {
      return;
    } else if (type === 'amount') {
      objectif = parseFloat((document.getElementById('fu-montant-fixe')?.value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    } else if (type === 'income') {
      const months = parseFloat(document.getElementById('fu-months')?.value || '3') || 3;
      // Revenu net : somme du net par propriétaire (via computeImpot)
      const annuelNet = getRevenusByOwner('client', true).total + getRevenusByOwner('conjoint', true).total;
      const base = annuelNet / 12;
      objectif = months * base;
      const cible = document.getElementById('fu-montant-cible-income');
      if (cible) cible.textContent = fmtMoney(objectif);
    } else {
      // expenses: montant mensuel saisi × mois saisis
      const depMensuel = parseFloat((document.getElementById('fu-dep-mensuel')?.value || '0').replace(/\s/g,'').replace(',','.')) || 0;
      const months = parseFloat(document.getElementById('fu-months-dep')?.value || '3') || 3;
      objectif = depMensuel * months;
      const cible = document.getElementById('fu-montant-cible-dep');
      if (cible) cible.textContent = fmtMoney(objectif);
    }

    // Somme des montants alloués saisis
    let actifsTotal = 0;
    document.querySelectorAll('#fu-actifs-body .fu-alloc-input').forEach(inp => {
      actifsTotal += parseFloat((inp.value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    });

    const marge = parseFloat((document.getElementById('fu-marge')?.value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const ecart = (actifsTotal + marge) - objectif;

    const card = document.getElementById('fu-resume-card');
    if (card) card.style.display = 'block';
    const el = (id) => document.getElementById(id);
    if (el('fu-r-objectif')) el('fu-r-objectif').textContent = fmtMoney(objectif);
    if (el('fu-r-actifs'))   el('fu-r-actifs').textContent   = fmtMoney(actifsTotal);
    if (el('fu-r-marge'))    el('fu-r-marge').textContent    = fmtMoney(marge);
    if (el('fu-r-ecart')) {
      el('fu-r-ecart').textContent = (ecart >= 0 ? '+' : '') + fmtMoney(ecart);
      el('fu-r-ecart').style.color = ecart >= 0 ? '#22c55e' : '#ef4444';
    }
    // Barre de couverture : rouge → or → vert
    const pct = objectif > 0 ? Math.min(100, Math.round((actifsTotal + marge) / objectif * 100)) : 0;
    const barColor = pct >= 100 ? '#22c55e' : pct >= 50 ? 'var(--gold)' : '#ef4444';
    const pctEl = el('fu-r-pct'), barEl = el('fu-r-bar');
    if (pctEl) pctEl.textContent = pct + ' %';
    if (barEl) { barEl.style.width = pct + '%'; barEl.style.background = barColor; }
  }

  /* ── DÉCÈS ── */
  let _decesAvItems = [];
  const ASSUREURS_LIST = []; // populated via select
  let _decesDepActiveTab = 'client';

  function switchDecesDepTab(who, btn) {
    _decesDepActiveTab = who;
    document.querySelectorAll('.deces-person-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('deces-dep-list').style.display = who === 'client' ? '' : 'none';
    document.getElementById('deces-dep-list-conjoint').style.display = who === 'conjoint' ? '' : 'none';
    // Update dep header
    const hdr = document.getElementById('deces-dep-header');
    if (hdr) {
      const prenom = who === 'client'
        ? (document.getElementById('client-prenom')?.value || 'le client')
        : (document.getElementById('conjoint-prenom')?.value || 'le conjoint');
      hdr.textContent = `Dépenses prévues si ${prenom} décède`;
    }
  }

  let _decesRrActiveTab = 'c';
  function switchDecesRrTab(sfx, btn) {
    _decesRrActiveTab = sfx;
    document.querySelectorAll('.deces-rr-person-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('deces-rr-panel-c').style.display = sfx === 'c' ? '' : 'none';
    document.getElementById('deces-rr-panel-j').style.display = sfx === 'j' ? '' : 'none';
    decesCalc();
  }

  function decesInit() {
    // Update header with client name
    const clientPrenom = document.getElementById('client-prenom')?.value || 'le client';
    const hdr = document.getElementById('deces-dep-header');
    if (hdr) hdr.textContent = `Dépenses prévues si ${clientPrenom} décède`;

    // RRQ inputs
    const rrqBody = document.getElementById('deces-rrq-body');
    if (rrqBody) {
      const conjointChecked = document.getElementById('conjoint')?.checked;
      const conjointPrenom  = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
      let html = '';
      if (conjointChecked) {
        html = `<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div class="form-group" style="margin:0">
            <label class="form-label">${clientPrenom}</label>
            <div class="input-sfx"><input class="form-input" id="deces-rrq-client" type="text" value="0" oninput="decesCalc()"/><span class="sfx">$</span></div>
          </div>
          <div class="form-group" style="margin:0">
            <label class="form-label">${conjointPrenom}</label>
            <div class="input-sfx"><input class="form-input" id="deces-rrq-conjoint" type="text" value="0" oninput="decesCalc()"/><span class="sfx">$</span></div>
          </div>
        </div>`;
      } else {
        html = `<div class="form-group" style="max-width:220px">
          <label class="form-label">${clientPrenom}</label>
          <div class="input-sfx"><input class="form-input" id="deces-rrq-client" type="text" value="0" oninput="decesCalc()"/><span class="sfx">$</span></div>
        </div>`;
      }
      rrqBody.innerHTML = html;
      // Auto-remplir la prestation de décès RRQ (montant fixe maximal = 2 500 $)
      const rrqC = document.getElementById('deces-rrq-client');
      const rrqJ = document.getElementById('deces-rrq-conjoint');
      if (rrqC && !parseFloat(rrqC.value)) rrqC.value = '2500';
      if (rrqJ && !parseFloat(rrqJ.value)) rrqJ.value = '2500';
    }

    // Pre-populate frais funéraires if list empty (valeur depuis ABF_PARAMS ou fallback)
    const _funeraillesDefault = parseFloat(
      String(window.ABF_PARAMS?.deces?.funerailles ?? '10000').replace(/\s/g, '').replace(',', '.')
    ) || 10000;
    const depList = document.getElementById('deces-dep-list');
    if (depList && depList.children.length === 0) {
      _decesDepActiveTab = 'client';
      addDecesDep('Frais funéraires', _funeraillesDefault);
    }

    // Couple-mode adaptations
    const isCouple = document.getElementById('conjoint')?.checked;
    if (isCouple) {
      const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
      const clientPrenom2 = document.getElementById('client-prenom')?.value || 'Client';

      // Show dep tabs with real names
      const tabsEl = document.getElementById('deces-dep-tabs');
      if (tabsEl) {
        tabsEl.style.display = 'flex';
        const tabClient = document.getElementById('deces-dep-tab-client');
        const tabConj = document.getElementById('deces-dep-tab-conjoint');
        if (tabClient) tabClient.textContent = clientPrenom2.toUpperCase();
        if (tabConj) tabConj.textContent = conjointPrenom.toUpperCase();
      }

      // Pre-populate conjoint dep list if empty
      const conjList = document.getElementById('deces-dep-list-conjoint');
      if (conjList && conjList.children.length === 0) {
        _decesDepActiveTab = 'conjoint';
        addDecesDep('Frais funéraires', _funeraillesDefault);
        _decesDepActiveTab = 'client';
      }

      // Show Familial radio and select it by default
      const famPill = document.getElementById('deces-rr-familial-pill');
      if (famPill) {
        famPill.style.display = '';
        const famRadio = famPill.querySelector('input[type=radio]');
        if (famRadio) famRadio.checked = true;
      }

      // Use tabs for RR panels in couple mode
      const rrTabs = document.getElementById('deces-rr-person-tabs');
      if (rrTabs) rrTabs.style.display = 'flex';
      const tabClient = document.getElementById('deces-rr-tab-client');
      const tabConj   = document.getElementById('deces-rr-tab-conjoint');
      if (tabClient) { tabClient.textContent = clientPrenom2.toUpperCase(); tabClient.classList.add('active'); }
      if (tabConj)   { tabConj.textContent   = conjointPrenom.toUpperCase(); tabConj.classList.remove('active'); }
      const panelC = document.getElementById('deces-rr-panel-c');
      const panelJ = document.getElementById('deces-rr-panel-j');
      if (panelC) panelC.style.display = '';   // show client tab by default
      if (panelJ) panelJ.style.display = 'none'; // hide conjoint tab
      const rrForm = document.getElementById('deces-rr-form');
      if (rrForm) { rrForm.style.display = ''; rrForm.style.gridTemplateColumns = ''; rrForm.style.gap = ''; rrForm.style.alignItems = ''; }
      // Hide the "Si X décède" title banners (tabs already show context)
      const titleC = document.getElementById('deces-rr-panel-c-title');
      const titleJ = document.getElementById('deces-rr-panel-j-title');
      if (titleC) titleC.style.display = 'none';
      if (titleJ) titleJ.style.display = 'none';
      // Restore panel-j labels to visible (each tab is independent)
      ['deces-lbl-j-actuels','deces-lbl-j-vises','deces-lbl-j-dispos'].forEach(id => {
        const el = document.getElementById(id); if (el) el.style.visibility = '';
      });

      // Update beneficiaire labels in both panels
      const benLabelC = document.getElementById('deces-rr-beneficiaire-label-c');
      if (benLabelC) benLabelC.textContent = 'Le conjoint survivant désire recevoir';
      const benLabelJ = document.getElementById('deces-rr-beneficiaire-label-j');
      if (benLabelJ) benLabelJ.textContent = 'Le conjoint survivant désire recevoir';
    } else {
      // Reset couple elements hidden
      const tabsEl = document.getElementById('deces-dep-tabs');
      if (tabsEl) tabsEl.style.display = 'none';
      const famPill = document.getElementById('deces-rr-familial-pill');
      if (famPill) famPill.style.display = 'none';
      const rrTabs = document.getElementById('deces-rr-person-tabs');
      if (rrTabs) rrTabs.style.display = 'none';
      const rrFormSolo = document.getElementById('deces-rr-form');
      if (rrFormSolo) { rrFormSolo.style.display = ''; rrFormSolo.style.gridTemplateColumns = ''; rrFormSolo.style.gap = ''; rrFormSolo.style.alignItems = ''; }
      const titleCSolo = document.getElementById('deces-rr-panel-c-title');
      if (titleCSolo) titleCSolo.style.display = 'none';
      const benLabelC = document.getElementById('deces-rr-beneficiaire-label-c');
      if (benLabelC) benLabelC.textContent = 'Le bénéficiaire désire recevoir';
    }

    // Populate assuré dropdown
    const ownerSel = document.getElementById('deces-av-owner');
    if (ownerSel) {
      const clientPrenom2 = document.getElementById('client-prenom')?.value || 'Client';
      ownerSel.innerHTML = `<option value="">Sélectionnez...</option><option value="client">${clientPrenom2}</option>`;
      const conjointChecked = document.getElementById('conjoint')?.checked;
      if (conjointChecked) {
        const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
        ownerSel.innerHTML += `<option value="conjoint">${conjointPrenom}</option>`;
      }
    }

    // Render actifs/passifs lists
    decesRenderActifs();
    decesRenderPassifs();

    // Render revenus actuels
    decesRenderRevenus();

    // Auto-suggérer la rente de conjoint survivant selon l'âge du survivant (RRQ)
    // Panneau-C : client décède → survivant = conjoint
    // Panneau-J : conjoint décède → survivant = client
    const isCouple2 = document.getElementById('conjoint')?.checked;
    const clientBirthYear  = parseInt(document.getElementById('client-naissance-annee')?.value) || 0;
    const conjBirthYear    = parseInt(document.getElementById('conjoint-naissance-annee')?.value) || 0;
    const renteC = document.getElementById('deces-rente-conjoint-c');
    const renteJ = document.getElementById('deces-rente-conjoint-j');
    rcUpdatePanelHelpers(); // show/hide invalide vs rente-defunt inputs
    if (renteC && (!parseFloat(renteC.value) || parseFloat(renteC.value) === 0)) {
      const defC = parseFloat((document.getElementById('deces-rente-defunt-c')?.value||'0').replace(/\s/g,'').replace(',','.')) || 0;
      const invC = document.getElementById('deces-invalide-c')?.checked || false;
      const sugg = isCouple2 && conjBirthYear ? getRenteConjSuggestion(conjBirthYear, survivorHasChildren('conjoint'), invC, defC) * 12 : 0;
      if (sugg) renteC.value = Math.round(sugg).toLocaleString('fr-CA');
    }
    if (renteJ && (!parseFloat(renteJ.value) || parseFloat(renteJ.value) === 0)) {
      const defJ = parseFloat((document.getElementById('deces-rente-defunt-j')?.value||'0').replace(/\s/g,'').replace(',','.')) || 0;
      const invJ = document.getElementById('deces-invalide-j')?.checked || false;
      const sugg = clientBirthYear ? getRenteConjSuggestion(clientBirthYear, survivorHasChildren('client'), invJ, defJ) * 12 : 0;
      if (sugg) renteJ.value = Math.round(sugg).toLocaleString('fr-CA');
    }

    decesCalc();
  }

  function decesRenderActifs() {
    const body = document.getElementById('deces-actifs-body');
    if (!body) return;
    const isCouple = document.getElementById('conjoint')?.checked;
    const clientPrenom = document.getElementById('client-prenom')?.value || 'Client';
    const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
    const items = [];
    document.querySelectorAll('#actifs-list [data-aptype]').forEach(el => {
      const nom = el.dataset.nom || el.querySelector('.ap-item-name')?.textContent || el.dataset.aptype;
      const valeur = parseFloat(el.dataset.valeur) || 0;
      const owner = el.dataset.owner || 'both';
      items.push({ nom, valeur, owner });
    });
    if (items.length === 0) {
      body.innerHTML = '<p style="font-size:13px;color:var(--muted);padding:4px 0">Aucun actif disponible.</p>';
      return;
    }
    const propLabel = o => o === 'client' ? clientPrenom : o === 'conjoint' ? conjointPrenom : o === 'both' ? `${clientPrenom} et ${conjointPrenom}` : '—';
    if (isCouple) {
      let html = `<div style="overflow-x:auto;padding-bottom:4px">
        <table style="width:100%;border-collapse:collapse;font-size:12px">
          <thead>
            <tr style="border-bottom:2px solid var(--border)">
              <th style="padding:8px 12px;text-align:left;font-weight:700;color:var(--muted);white-space:nowrap">Description</th>
              <th style="padding:8px 12px;text-align:left;font-weight:700;color:var(--muted);white-space:nowrap">Propriétaire</th>
              <th style="padding:8px 12px;text-align:right;font-weight:700;color:var(--muted);white-space:nowrap">Valeur</th>
              <th style="padding:8px 12px;text-align:center;font-weight:700;color:var(--muted);white-space:nowrap">Au décès de ${clientPrenom}</th>
              <th style="padding:8px 12px;text-align:center;font-weight:700;color:var(--muted);white-space:nowrap">Au décès de ${conjointPrenom}</th>
            </tr>
          </thead>
          <tbody>`;
      items.forEach(it => {
        html += `<tr style="border-bottom:1px solid var(--border)">
          <td style="padding:8px 12px">${it.nom}</td>
          <td style="padding:8px 12px;color:var(--muted)">${propLabel(it.owner)}</td>
          <td style="padding:8px 12px;text-align:right;font-weight:600">${fmtMoney(it.valeur)}</td>
          <td style="padding:8px 12px;text-align:center"><input type="checkbox" class="deces-actif-chk-c" data-valeur="${it.valeur}" onchange="decesCalc()" style="width:16px;height:16px;cursor:pointer;accent-color:var(--navy)"/></td>
          <td style="padding:8px 12px;text-align:center"><input type="checkbox" class="deces-actif-chk-j" data-valeur="${it.valeur}" onchange="decesCalc()" style="width:16px;height:16px;cursor:pointer;accent-color:var(--navy)"/></td>
        </tr>`;
      });
      html += '</tbody></table></div>';
      body.innerHTML = html;
    } else {
      body.innerHTML = items.map(it =>
        `<div class="fu-actif-row">
          <input type="checkbox" class="fu-actif-check deces-actif-chk-c" data-valeur="${it.valeur}" onchange="decesCalc()"/>
          <label class="fu-actif-name">${it.nom}</label>
          <span class="fu-actif-valeur">${fmtMoney(it.valeur)}</span>
        </div>`
      ).join('');
    }
  }

  function decesRenderPassifs() {
    const body = document.getElementById('deces-passifs-body');
    if (!body) return;
    const isCouple = document.getElementById('conjoint')?.checked;
    const clientPrenom = document.getElementById('client-prenom')?.value || 'Client';
    const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';

    const items = [];
    document.querySelectorAll('#passifs-list [data-valeur]').forEach(el => {
      const nom = el.querySelector('.ap-item-name')?.textContent?.trim() || el.dataset.aptype || 'Passif';
      const valeur = parseFloat(el.dataset.valeur) || 0;
      const owner = el.dataset.owner || 'both';
      items.push({ nom, valeur, owner });
    });

    if (items.length === 0) {
      body.innerHTML = '<p style="font-size:13px;color:var(--muted);padding:4px 0">Aucun passif disponible.</p>';
      return;
    }

    const propLabel = o => o === 'client' ? clientPrenom : o === 'conjoint' ? conjointPrenom : o === 'both' ? `${clientPrenom} et ${conjointPrenom}` : '—';

    if (isCouple) {
      let html = `<div style="overflow-x:auto;padding-bottom:4px">
        <table style="width:100%;border-collapse:collapse;font-size:12px">
          <thead>
            <tr style="border-bottom:2px solid var(--border)">
              <th style="padding:8px 12px;text-align:left;font-weight:700;color:var(--muted);white-space:nowrap">Description</th>
              <th style="padding:8px 12px;text-align:left;font-weight:700;color:var(--muted);white-space:nowrap">Propriétaire</th>
              <th style="padding:8px 12px;text-align:right;font-weight:700;color:var(--muted);white-space:nowrap">Valeur</th>
              <th style="padding:8px 12px;text-align:center;font-weight:700;color:var(--muted);white-space:nowrap">Au décès de ${clientPrenom}</th>
              <th style="padding:8px 12px;text-align:center;font-weight:700;color:var(--muted);white-space:nowrap">Au décès de ${conjointPrenom}</th>
            </tr>
          </thead>
          <tbody>`;
      items.forEach(it => {
        html += `<tr style="border-bottom:1px solid var(--border)">
          <td style="padding:8px 12px">${it.nom}</td>
          <td style="padding:8px 12px;color:var(--muted)">${propLabel(it.owner)}</td>
          <td style="padding:8px 12px;text-align:right;font-weight:600">${fmtMoney(it.valeur)}</td>
          <td style="padding:8px 12px;text-align:center"><input type="checkbox" class="deces-passif-chk-c" data-valeur="${it.valeur}" onchange="decesCalc()" checked style="width:16px;height:16px;cursor:pointer;accent-color:var(--navy)"/></td>
          <td style="padding:8px 12px;text-align:center"><input type="checkbox" class="deces-passif-chk-j" data-valeur="${it.valeur}" onchange="decesCalc()" checked style="width:16px;height:16px;cursor:pointer;accent-color:var(--navy)"/></td>
        </tr>`;
      });
      html += '</tbody></table></div>';
      body.innerHTML = html;
    } else {
      body.innerHTML = items.map(it =>
        `<div class="fu-actif-row">
          <input type="checkbox" class="fu-actif-check deces-passif-chk-c" data-valeur="${it.valeur}" onchange="decesCalc()" checked/>
          <label class="fu-actif-name">${it.nom}</label>
          <span class="fu-actif-valeur">${fmtMoney(it.valeur)}</span>
        </div>`
      ).join('');
    }
  }

  function getRevenusByOwner(owner, isNet, excludeEmploi = false) {
    const items = [];
    document.querySelectorAll('#revenu-list tr[data-revenu-annuel]').forEach(tr => {
      const annuel = parseFloat(tr.dataset.revenuAnnuel) || 0;
      if (!annuel) return;
      const isConj = tr.dataset.owner === 'conjoint';
      if (owner === 'conjoint' ? !isConj : isConj) return;
      if (excludeEmploi && tr.dataset.revenuType === 'emploi') return;
      const desc = tr.querySelector('td:first-child')?.textContent || '';
      const val = isNet ? (computeImpot(annuel)?.net ?? annuel) : annuel;
      items.push({ desc, val });
    });
    return { items, total: items.reduce((s, r) => s + r.val, 0) };
  }

  function decesRenderRevenus() {
    const freq = document.querySelector('input[name="deces-rr-freq"]:checked')?.value || 'annuel';
    const brutNet = document.querySelector('input[name="deces-rr-brutnnet"]:checked')?.value || 'brut';
    const isNet = brutNet === 'net';
    const diviseur = freq === 'mensuel' ? 12 : 1;
    const isCouple = document.getElementById('conjoint')?.checked;
    const type = document.querySelector('input[name="deces-rr-type"]:checked')?.value || 'individuel';
    const isFamilial = type === 'familial';
    const clientPrenom = document.getElementById('client-prenom')?.value || 'Client';
    const conjPrenom   = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
    const labelBN  = isNet ? 'net' : 'brut';
    const labelFrq = freq === 'mensuel' ? 'mensuel' : 'annuel';

    const clientData = getRevenusByOwner('client',   isNet);
    const conjData   = isCouple ? getRevenusByOwner('conjoint', isNet) : { items: [], total: 0 };

    const rowStyle = 'display:flex;justify-content:space-between;padding:3px 0';
    const totalRowStyle = 'display:flex;justify-content:space-between;padding:6px 0 2px;font-weight:700;border-top:1px solid var(--border);margin-top:4px';

    // Render "Revenus actuels" table — Individuel: deceased only; Familial: both persons
    const renderIndivTable = (items, total) => {
      if (!items.length) return '<div style="color:var(--muted)">Aucun revenu enregistré.</div>';
      let h = items.map(r =>
        `<div style="${rowStyle}"><span>${r.desc}</span><span>${fmtMoney(r.val / diviseur)}</span></div>`
      ).join('');
      h += `<div style="${totalRowStyle}"><span>Revenu ${labelBN} ${labelFrq}</span><span>${fmtMoney(total / diviseur)}</span></div>`;
      return h;
    };
    const renderFamilialTable = (d1, name1, d2, name2) => {
      let h = '';
      const subHdr = n => `<div style="font-size:11px;font-weight:700;color:var(--muted);padding:4px 0 3px;text-transform:uppercase">${n}</div>`;
      if (d1.items.length) {
        h += subHdr(name1);
        h += d1.items.map(r => `<div style="${rowStyle}"><span>${r.desc}</span><span>${fmtMoney(r.val / diviseur)}</span></div>`).join('');
      }
      if (d2.items.length) {
        h += subHdr(name2);
        h += d2.items.map(r => `<div style="${rowStyle}"><span>${r.desc}</span><span>${fmtMoney(r.val / diviseur)}</span></div>`).join('');
      }
      if (!d1.items.length && !d2.items.length) return '<div style="color:var(--muted)">Aucun revenu enregistré.</div>';
      const familialTotal = d1.total + d2.total;
      h += `<div style="${totalRowStyle}"><span>Revenu familial ${labelBN} ${labelFrq}</span><span>${fmtMoney(familialTotal / diviseur)}</span></div>`;
      return h;
    };

    const tblC = document.getElementById('deces-revenus-table-c');
    const tblJ = document.getElementById('deces-revenus-table-j');
    if (isFamilial && isCouple) {
      const familialHtml = renderFamilialTable(clientData, clientPrenom, conjData, conjPrenom);
      if (tblC) tblC.innerHTML = familialHtml;
      if (tblJ) tblJ.innerHTML = familialHtml;
    } else {
      if (tblC) tblC.innerHTML = renderIndivTable(clientData.items, clientData.total);
      if (tblJ) tblJ.innerHTML = renderIndivTable(conjData.items,   conjData.total);
    }

    // Render "Revenus disponibles" auto-card
    // Familial+couple: show survivor's income (ex-emploi) as auto-card
    // Individuel: no auto-card
    const cardStyle = 'background:#eef2ff;border-radius:6px;padding:8px 10px;text-align:center';
    const nameStyle = 'font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px';
    const valStyle  = 'font-size:14px;font-weight:700;color:var(--navy)';
    const card = (name, val) =>
      `<div style="${cardStyle}"><div style="${nameStyle}">${name}</div><div style="${valStyle}">${fmtMoney(val / diviseur)}</div></div>`;

    const autoC = document.getElementById('deces-revenu-dispo-auto-c');
    const autoJ = document.getElementById('deces-revenu-dispo-auto-j');
    if (isFamilial && isCouple) {
      // Survivant = still alive → include all income (incl. emploi)
      const conjAllData   = getRevenusByOwner('conjoint', isNet, false);
      const clientAllData = getRevenusByOwner('client',   isNet, false);
      // panel-c = client dies → survivor is conjoint
      if (autoC) autoC.innerHTML = `<div style="padding:4px 0 6px">${card(`Revenu de ${conjPrenom}`, conjAllData.total)}</div>`;
      // panel-j = conjoint dies → survivor is client
      if (autoJ) autoJ.innerHTML = `<div style="padding:4px 0 6px">${card(`Revenu de ${clientPrenom}`, clientAllData.total)}</div>`;
    } else {
      if (autoC) autoC.innerHTML = '';
      if (autoJ) autoJ.innerHTML = '';
    }
  }

  function calcRrPanel(sfx) {
    const freq    = document.querySelector('input[name="deces-rr-freq"]:checked')?.value     || 'annuel';
    const brutNet = document.querySelector('input[name="deces-rr-brutnnet"]:checked')?.value || 'brut';
    const isNet   = brutNet === 'net';
    const isCouple = document.getElementById('conjoint')?.checked;
    const type = document.querySelector('input[name="deces-rr-type"]:checked')?.value || 'individuel';
    const isFamilial = type === 'familial';
    const pctEl = document.getElementById(`deces-rr-pct-${sfx}`);
    if (!pctEl) return 0;

    // Revenues of the person who died (this panel's owner)
    // panel-c = client dies → use client revenues as the base to replace
    // panel-j = conjoint dies → use conjoint revenues
    const deceasedOwner  = sfx === 'c' ? 'client'   : 'conjoint';
    const survivorOwner  = sfx === 'c' ? 'conjoint' : 'client';
    const deceasedData = getRevenusByOwner(deceasedOwner, isNet);
    const conjData     = isCouple ? getRevenusByOwner(survivorOwner, isNet) : { total: 0 };
    // Familial: base = total family income; Individuel: base = deceased only
    const annuelBase = (isFamilial && isCouple) ? deceasedData.total + conjData.total : deceasedData.total;

    // Update "du revenu" / "du revenu familial" text
    const duRevenuEl = document.getElementById(`deces-rr-du-revenu-${sfx}`);
    if (duRevenuEl) duRevenuEl.textContent = (isFamilial && isCouple) ? 'du revenu familial' : 'du revenu';

    const pct        = parseFloat(pctEl.value || '70') / 100;
    const targetType = document.querySelector(`input[name="deces-rr-target-${sfx}"]:checked`)?.value || 'pct';
    const revenuVise = targetType === 'pct'
      ? annuelBase * pct
      : (parseFloat((pctEl.value || '0').replace(/\s/g,'').replace(',','.')) || 0);
    const label = document.getElementById(`deces-rr-vise-label-${sfx}`);
    if (label) label.textContent = fmtMoney(freq === 'mensuel' ? revenuVise / 12 : revenuVise) + (freq === 'mensuel' ? '/mois' : '/an');

    // Survivor's income auto-computed — include emploi (survivor is alive and working)
    // Only in Familial mode (shown as auto-card); Individuel = 0 (manual entry only)
    const dispo = (isFamilial && isCouple) ? getRevenusByOwner(survivorOwner, isNet, false).total : 0;
    const autres    = parseFloat((document.getElementById(`deces-autres-revenus-${sfx}`)?.value  || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const disponible = dispo + autres;
    // Revenu annuel manquant = revenu visé pour 1 an
    const manquantAnnuel = revenuVise;
    const duree = parseFloat(document.getElementById(`deces-rr-duree-${sfx}`)?.value || '10') || 10;
    const taux  = parseFloat((document.getElementById(`deces-rr-taux-${sfx}`)?.value || '3.70').replace(',','.')) / 100 || 0;
    const inflation = parseFloat((document.getElementById('vd-inflation')?.value || '2,10').replace(',','.')) / 100 || 0;
    // Capital nécessaire = A1 * ((1 - ((1+inf)/(1+R))^A2) / (1 - ((1+inf)/(1+R))))
    const ratio = (1 + inflation) / (1 + taux);
    let pv;
    if (Math.abs(ratio - 1) < 0.0001) {
      pv = manquantAnnuel * duree;
    } else {
      pv = manquantAnnuel * (1 - Math.pow(ratio, duree)) / (1 - ratio);
    }
    const m = document.getElementById(`deces-rr-manquant-${sfx}`);
    const p = document.getElementById(`deces-rr-projete-${sfx}`);
    const pd = document.getElementById(`deces-rr-projete-duree-${sfx}`);
    if (m) m.textContent = fmtMoney(manquantAnnuel);
    if (p) p.textContent = fmtMoney(pv);
    if (pd) pd.textContent = duree ? `pendant ${duree} an${duree > 1 ? 's' : ''}` : '';
    return pv;
  }

  function toggleDecesDep() {
    const dd = document.getElementById('deces-dep-dd');
    if (!dd) return;
    const wasOpen = dd.style.display === 'block';
    document.querySelectorAll('#deces-dep-dd').forEach(d => d.style.display = 'none');
    if (!wasOpen) {
      const btn = event.currentTarget;
      const r = btn.getBoundingClientRect();
      dd.style.position = 'fixed';
      dd.style.top = (r.bottom + 4) + 'px';
      dd.style.left = r.left + 'px';
      dd.style.display = 'block';
    }
  }

  function addDecesDep(desc, montantDefault) {
    document.getElementById('deces-dep-dd').style.display = 'none';
    const listId = _decesDepActiveTab === 'conjoint' ? 'deces-dep-list-conjoint' : 'deces-dep-list';
    const list = document.getElementById(listId);
    const uid = Math.random().toString(36).slice(2);
    const row = document.createElement('div');
    row.className = 'deces-dep-row';
    row.dataset.montant = montantDefault;
    row.dataset.desc = desc;
    row.dataset.indexed = 'oui';
    row.innerHTML = `
      <span style="flex:1;color:var(--text)">${desc}</span>
      <div class="input-sfx" style="max-width:140px">
        <input class="form-input" type="text" value="${montantDefault ? montantDefault.toLocaleString('fr-CA') : ''}" placeholder="0"
          oninput="this.closest('[data-montant]').dataset.montant=parseFloat(this.value.replace(/\\s/g,'').replace(',','.'))||0;decesCalc()"/>
        <span class="sfx">$</span>
      </div>
      <div style="display:flex;flex-direction:column;align-items:center;gap:2px">
        <span style="font-size:10px;color:var(--muted);white-space:nowrap">Indexé à l'inflation</span>
        <div style="display:flex;gap:4px">
          <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="deces-idx-${uid}" value="oui" checked onchange="this.closest('.deces-dep-row').dataset.indexed='oui'"/> Oui</label>
          <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="deces-idx-${uid}" value="non" onchange="this.closest('.deces-dep-row').dataset.indexed='non'"/> Non</label>
        </div>
      </div>
      <button onclick="this.closest('.deces-dep-row').remove();decesCalc()" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:18px;padding:0 4px">×</button>
    `;
    list.appendChild(row);
    decesCalc();
  }

  function fillDecesAvOwner() {
    const sel = document.getElementById('deces-av-owner');
    if (!sel) return;
    const cn = getClientPrenom(), cj = getConjointPrenom();
    sel.innerHTML = '<option value="">Sélectionnez…</option>';
    sel.innerHTML += `<option value="client">${cn}</option>`;
    if (cj) {
      sel.innerHTML += `<option value="conjoint">${cj}</option>`;
      sel.innerHTML += `<option value="both">${cn} et ${cj}</option>`;
      document.querySelectorAll('#enfants-list .enfant-item').forEach(el => {
        const prenom = el.dataset.enfPrenom || '';
        const nom = el.dataset.enfNom || '';
        const name = [prenom, nom].filter(Boolean).join(' ');
        if (name) sel.innerHTML += `<option value="enfant-${prenom.toLowerCase()}">${name}</option>`;
      });
    }
  }

  function updateConjointLienLabel() {
    const label = document.getElementById('conjoint-lien-label');
    if (label) label.textContent = 'Relation avec ' + getClientPrenom();
  }

  function openDecesAvModal() {
    fillDecesAvOwner();
    // Reset
    ['deces-av-type','deces-av-assureur'].forEach(id => {
      const el = document.getElementById(id); if(el) el.value = '';
    });
    ['deces-av-montant','deces-av-prime','deces-av-date','deces-av-notes'].forEach(id => {
      const el = document.getElementById(id); if(el) el.value = '';
    });
    document.getElementById('deces-av-exclure').checked = false;
    document.querySelectorAll('input[name="deces-av-benef"]').forEach(r => r.checked = false);
    document.getElementById('modal-deces-av').style.display = 'flex';
  }

  function closeDecesAvModal() {
    document.getElementById('modal-deces-av').style.display = 'none';
  }

  function saveDecesAv() {
    const type     = document.getElementById('deces-av-type').value;
    const owner    = document.getElementById('deces-av-owner').options[document.getElementById('deces-av-owner').selectedIndex]?.text || '';
    const montant  = parseFloat((document.getElementById('deces-av-montant').value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const prime    = parseFloat((document.getElementById('deces-av-prime').value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const assureur = document.getElementById('deces-av-assureur').value;
    const exclure  = document.getElementById('deces-av-exclure').checked;
    const benef    = document.querySelector('input[name="deces-av-benef"]:checked')?.value || '';

    if (!type || !owner || montant <= 0) { showToast('Type, assuré et montant sont requis'); return; }

    const list = document.getElementById('deces-av-list');
    const empty = document.getElementById('deces-av-empty');
    if (empty) empty.remove();

    const row = document.createElement('div');
    row.className = 'deces-av-row';
    row.dataset.montant = exclure ? 0 : montant;
    row.dataset.ownerVal = document.getElementById('deces-av-owner').value;
    row.dataset.formJson = JSON.stringify({
      type, ownerVal: document.getElementById('deces-av-owner').value, owner,
      montant, prime, assureur, exclure, benef,
      notes: document.getElementById('deces-av-notes')?.value.trim() || '',
      date: document.getElementById('deces-av-date')?.value.trim() || '',
    });
    row.innerHTML = `
      <div style="flex:1">
        <div style="font-weight:600">${type} — ${owner}</div>
        <div style="font-size:11px;color:var(--muted)">${assureur || ''}${benef ? ' · Bénéficiaire: '+benef : ''}${exclure ? ' · <em>Exclu de l\'analyse</em>' : ''}</div>
      </div>
      <div style="text-align:right;margin-right:12px">
        <div style="font-weight:700">${fmtMoney(montant)}</div>
        ${prime > 0 ? `<div style="font-size:11px;color:var(--muted)">Prime: ${fmtMoney(prime)}/an</div>` : ''}
      </div>
      <button onclick="this.closest('.deces-av-row').remove();decesCalc()" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:18px;padding:0 4px">×</button>
    `;
    list.appendChild(row);
    closeDecesAvModal();
    decesCalc();
  }

  function decesCalc() {
    decesRenderRevenus();
    const type = document.querySelector('input[name="deces-rr-type"]:checked')?.value || 'individuel';
    const brutNet = document.querySelector('input[name="deces-rr-brutnnet"]:checked')?.value || 'brut';
    const freq = document.querySelector('input[name="deces-rr-freq"]:checked')?.value || 'annuel';

    // Dépenses ponctuelles (both lists)
    let depTotal = 0;
    document.querySelectorAll('#deces-dep-list [data-montant], #deces-dep-list-conjoint [data-montant]').forEach(el => {
      depTotal += parseFloat(el.dataset.montant) || 0;
    });

    // Prestation RRQ
    let rrqTotal = 0;
    const rrqClient = parseFloat((document.getElementById('deces-rrq-client')?.value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const rrqConjoint = parseFloat((document.getElementById('deces-rrq-conjoint')?.value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    rrqTotal = rrqClient + rrqConjoint;

    // Remplacement du revenu (PV)
    let rrCapital = 0;
    if (type !== 'aucun') {
      const isCouple = document.getElementById('conjoint')?.checked;
      rrCapital = calcRrPanel('c');
      if (isCouple) rrCapital += calcRrPanel('j');
      const rrFormEl = document.getElementById('deces-rr-form');
      if (rrFormEl) rrFormEl.style.display = isCouple ? 'grid' : '';
    } else {
      document.getElementById('deces-rr-form').style.display = 'none';
    }

    // Per-person dep totals
    let depTotalC = 0, depTotalJ = 0;
    document.querySelectorAll('#deces-dep-list [data-montant]').forEach(el => depTotalC += parseFloat(el.dataset.montant)||0);
    document.querySelectorAll('#deces-dep-list-conjoint [data-montant]').forEach(el => depTotalJ += parseFloat(el.dataset.montant)||0);

    // Per-person RR capital
    const rrCapC = type !== 'aucun' ? (parseFloat(document.getElementById('deces-rr-projete-c')?.textContent?.replace(/\s/g,'').replace(',','.').replace('$','')) || 0) : 0;
    const rrCapJ = type !== 'aucun' ? (parseFloat(document.getElementById('deces-rr-projete-j')?.textContent?.replace(/\s/g,'').replace(',','.').replace('$','')) || 0) : 0;

    // Capital disponible: AV split by owner, actifs/passifs shared
    let avClient = 0, avConjoint = 0;
    document.querySelectorAll('.deces-av-row[data-montant]').forEach(r => {
      const m = parseFloat(r.dataset.montant) || 0;
      if (r.dataset.ownerVal === 'conjoint') avConjoint += m; else avClient += m;
    });
    let actifsTotalC = 0, actifsTotalJ = 0;
    document.querySelectorAll('.deces-actif-chk-c:checked').forEach(chk => actifsTotalC += parseFloat(chk.dataset.valeur)||0);
    document.querySelectorAll('.deces-actif-chk-j:checked').forEach(chk => actifsTotalJ += parseFloat(chk.dataset.valeur)||0);
    let passifsTotalC = 0, passifsTotalJ = 0;
    document.querySelectorAll('.deces-passif-chk-c:checked').forEach(chk => passifsTotalC += parseFloat(chk.dataset.valeur)||0);
    document.querySelectorAll('.deces-passif-chk-j:checked').forEach(chk => passifsTotalJ += parseFloat(chk.dataset.valeur)||0);

    // Besoin = capital revenu visé + passifs à rembourser + dépenses ponctuelles
    const besoinC = rrCapC + passifsTotalC + depTotalC;
    const besoinJ = rrCapJ + passifsTotalJ + depTotalJ;
    // Disponible = AV + actifs liquidables + prestation RRQ
    const dispoC  = avClient  + actifsTotalC + rrqClient;
    const dispoJ  = avConjoint + actifsTotalJ + rrqConjoint;

    // Render résumé
    const isCouple = document.getElementById('conjoint')?.checked;
    const clientPrenom = document.getElementById('client-prenom')?.value || 'Client';
    const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
    const sections = isCouple
      ? [{name: clientPrenom, besoins: besoinC, disponibles: dispoC}, {name: conjointPrenom, besoins: besoinJ, disponibles: dispoJ}]
      : [{name: clientPrenom, besoins: besoinC, disponibles: dispoC}];
    decesRenderResume(sections);
  }

  function decesRenderResume(sections) {
    const body = document.getElementById('deces-resume-body');
    if (!body) return;
    let html = '';
    sections.forEach((s, i) => {
      const manque = Math.max(0, s.besoins - s.disponibles);
      const pct = s.besoins > 0 ? Math.min(100, Math.round(s.disponibles / s.besoins * 100)) : (s.disponibles > 0 ? 100 : 0);
      const color = pct >= 100 ? '#22c55e' : pct >= 50 ? '#f59e0b' : '#ef4444';
      const border = i < sections.length - 1 ? 'border-bottom:1px solid var(--border);padding-bottom:16px;margin-bottom:16px' : '';
      html += `<div style="${border}">
        <div style="font-size:12px;font-weight:700;color:var(--navy);letter-spacing:.5px;margin-bottom:10px;text-transform:uppercase">${s.name}</div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
          <div style="flex:1;height:8px;background:#e5e7eb;border-radius:4px;overflow:hidden">
            <div style="height:100%;background:${color};border-radius:4px;width:${pct}%;transition:width .3s"></div>
          </div>
          <span style="font-size:12px;font-weight:700;color:${color};min-width:38px;text-align:right">${pct}&nbsp;%</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0">
          <span style="color:var(--muted)">Besoins actuels</span>
          <span style="font-weight:600">${fmtMoney(s.besoins)}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0">
          <span style="color:var(--muted)">Montants disponibles</span>
          <span style="font-weight:600">${fmtMoney(s.disponibles)}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0">
          <span style="color:var(--muted)">Manque à gagner</span>
          <span style="font-weight:600;color:${manque > 0 ? '#ef4444' : '#22c55e'}">${manque > 0 ? fmtMoney(manque) : '—'}</span>
        </div>
      </div>`;
    });
    body.innerHTML = html;
  }

  // Close dropdown on outside click
  document.addEventListener('click', e => {
    const dd = document.getElementById('deces-dep-dd');
    if (dd && !e.target.closest('#deces-dep-dd') && !e.target.closest('button[onclick*="toggleDecesDep"]')) {
      dd.style.display = 'none';
    }
  });

  /* ── INVALIDITÉ ─────────────────────────────────────── */
  let _invalAvList = [];
  let _invalAvEditIdx = -1;

  function invalRrPanelHtml(owner, prenom, isNet) {
    const revMensuel = getRevenusByOwner(owner, isNet).total / 12;
    const pct = parseFloat(window.ABF_PARAMS?.invalidite?.rr_pct ?? '70') || 70;
    const montant = Math.round(revMensuel * pct / 100);
    const brutNetLabel = isNet ? 'net' : 'brut';
    return `<div>
      <div style="font-weight:600;font-size:13px;margin-bottom:10px">${prenom}</div>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px;margin-bottom:8px">
        <input class="form-input" id="inval-rr-pct-${owner}" type="text" value="${pct}" style="width:60px;text-align:center" oninput="invaliditeCalc()"/>
        <span style="white-space:nowrap">% du revenu ${brutNetLabel} de <strong>${fmtMoney(Math.round(revMensuel))}/mois</strong>, soit</span>
      </div>
      <div style="padding:10px 14px;background:#eef2ff;border-radius:6px;font-size:14px;font-weight:700;color:var(--navy)">
        <span id="inval-rr-montant-${owner}">${fmtMoney(montant)}</span>/mois
      </div>
    </div>`;
  }

  function invaliditeInit() {
    const isCouple = document.getElementById('conjoint')?.checked;
    const clientPrenom = document.getElementById('client-prenom')?.value || 'Client';
    const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
    const isNet = document.querySelector('input[name="inval-rr-brutnnet"]:checked')?.value === 'net';

    // Autres sources — onglets noms + visibilité conjoint
    const tabLabelC = document.getElementById('inval-autres-tab-label-c');
    const tabLabelJ = document.getElementById('inval-autres-tab-label-j');
    const tabBtnJ   = document.getElementById('inval-autres-tab-j');
    if (tabLabelC) tabLabelC.textContent = clientPrenom.toUpperCase();
    if (tabLabelJ) tabLabelJ.textContent = conjointPrenom.toUpperCase();
    if (tabBtnJ)   tabBtnJ.style.display = isCouple ? '' : 'none';

    // Dépenses courantes — entêtes colonnes + visibilité colonne conjoint
    const hdrC = document.getElementById('inval-dep-hdr-c');
    const hdrJ = document.getElementById('inval-dep-hdr-j');
    if (hdrC) hdrC.textContent = clientPrenom.toUpperCase();
    if (hdrJ) hdrJ.textContent = conjointPrenom.toUpperCase();
    const depHeader = document.getElementById('inval-dep-header');
    document.querySelectorAll('.inval-dep-j-col').forEach(el => { el.style.display = isCouple ? '' : 'none'; });
    document.querySelectorAll('.inval-dep-row').forEach(el => {
      el.style.gridTemplateColumns = isCouple ? '2fr 1fr 1fr' : '2fr 1fr';
    });
    if (depHeader) depHeader.style.gridTemplateColumns = isCouple ? '2fr 1fr 1fr' : '2fr 1fr';

    // Remplacement du revenu body
    const rrBody = document.getElementById('inval-rr-body');
    if (rrBody) {
      if (isCouple) {
        rrBody.style.padding = '16px';
        rrBody.innerHTML = `<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
          ${invalRrPanelHtml('client', clientPrenom, isNet)}
          ${invalRrPanelHtml('conjoint', conjointPrenom, isNet)}
        </div>`;
      } else {
        rrBody.innerHTML = invalRrPanelHtml('client', clientPrenom, isNet);
      }
    }

    invalRenderAvList();
    invaliditeCalc();
  }

  function setInvalBrutNet(val) {
    const radio = document.querySelector(`input[name="inval-rr-brutnnet"][value="${val}"]`);
    if (radio) radio.checked = true;
    invaliditeInit();
  }

  function invalTypeChange() {
    const type = document.getElementById('inval-av-type')?.value || '';
    const isColl = type === 'collective';
    const indiv = document.getElementById('inval-av-individuelle-fields');
    const coll  = document.getElementById('inval-av-collective-fields');
    if (indiv) indiv.style.display = isColl ? 'none' : '';
    if (coll)  coll.style.display  = isColl ? '' : 'none';
    if (isColl) invalApprocheChange();
  }

  function invalApprocheChange() {
    const approche = document.querySelector('input[name="inval-av-approche"]:checked')?.value || 'pct';
    const isPct = approche === 'pct';
    const pctFields       = document.getElementById('inval-av-coll-pct-fields');
    const formulaContainer = document.getElementById('inval-av-formule-container');
    const montantFields   = document.getElementById('inval-av-coll-montant-fields');
    if (pctFields)        pctFields.style.display        = isPct ? 'grid' : 'none';
    if (formulaContainer) formulaContainer.style.display  = isPct ? ''     : 'none';
    if (montantFields)    montantFields.style.display     = isPct ? 'none' : 'grid';
    if (isPct) invalAutoFillRevenuAssurable();
  }

  function invalAutoFillRevenuAssurable() {
    const approche = document.querySelector('input[name="inval-av-approche"]:checked')?.value || 'pct';
    if (approche !== 'pct') return;
    const type = document.getElementById('inval-av-type')?.value || '';
    if (type !== 'collective') return;
    const owner = document.getElementById('inval-av-proprietaire')?.value || '';
    if (!owner) return;
    const data = getRevenusByOwner(owner, false); // false = brut
    const el = document.getElementById('inval-av-revenu-assurable');
    if (el && data && data.total > 0) el.value = data.total.toLocaleString('fr-CA');
  }

  function invalOwnerChange() {
    invalAutoFillRevenuAssurable();
  }

  function invalNiveauChange() {
    const niveau = document.getElementById('inval-av-prestation-niveau')?.value || '';
    const show = (id, flex) => {
      const el = document.getElementById(id);
      if (el) el.style.display = flex ? 'flex' : '';
    };
    const hide = id => { const el = document.getElementById(id); if (el) el.style.display = 'none'; };

    if (niveau === 'second') {
      // [pct0]% des premiers [amt0]$ du revenu mensuel assurable
      // [pct1]% de l'excédent. Maximum [max]$
      hide('inval-niv-txt1-simple');
      show('inval-niv-txt1-premiers'); show('inval-niv-amt0-wrap'); show('inval-niv-txt1-durevenu');
      show('inval-niv-row2', true);
      show('inval-niv-txt2-exced'); hide('inval-niv-txt2-des'); hide('inval-niv-amt1-wrap'); hide('inval-niv-txt2-suivants');
      hide('inval-niv-row3');
    } else if (niveau === 'third') {
      // [pct0]% des premiers [amt0]$ du revenu mensuel assurable
      // [pct1]% des [amt1]$ suivants
      // [pct2]% de l'excédent. Maximum [max]$
      hide('inval-niv-txt1-simple');
      show('inval-niv-txt1-premiers'); show('inval-niv-amt0-wrap'); show('inval-niv-txt1-durevenu');
      show('inval-niv-row2', true);
      hide('inval-niv-txt2-exced'); show('inval-niv-txt2-des'); show('inval-niv-amt1-wrap'); show('inval-niv-txt2-suivants');
      show('inval-niv-row3', true);
    } else {
      // first or empty: [pct0]% du revenu mensuel assurable. Maximum [max]$
      show('inval-niv-txt1-simple');
      hide('inval-niv-txt1-premiers'); hide('inval-niv-amt0-wrap'); hide('inval-niv-txt1-durevenu');
      hide('inval-niv-row2'); hide('inval-niv-row3');
    }
  }

  function invaliditeApproche() {
    const v = document.querySelector('input[name="inval-approche"]:checked')?.value;
    document.getElementById('inval-rr-section').style.display = v === 'remplacement' ? '' : 'none';
    document.getElementById('inval-dep-section').style.display = v === 'depenses' ? '' : 'none';
    invaliditeCalc();
  }

  function invalAutresTab(sfx) {
    document.querySelectorAll('.inval-autres-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('inval-autres-tab-' + sfx)?.classList.add('active');
    document.getElementById('inval-autres-panel-c').style.display = sfx === 'c' ? '' : 'none';
    document.getElementById('inval-autres-panel-j').style.display = sfx === 'j' ? '' : 'none';
  }

  function toggleInvalInfo() {
    const body = document.getElementById('inval-info-body');
    const chevron = document.getElementById('inval-info-chevron');
    const open = body.style.display !== 'none';
    body.style.display = open ? 'none' : '';
    chevron.style.transform = open ? '' : 'rotate(180deg)';
  }

  function invaliditeCalc() {
    const isCouple = document.getElementById('conjoint')?.checked;
    const clientPrenom = document.getElementById('client-prenom')?.value || 'Client';
    const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
    const approche = document.querySelector('input[name="inval-approche"]:checked')?.value || 'remplacement';
    const isNet = document.querySelector('input[name="inval-rr-brutnnet"]:checked')?.value === 'net';

    let besoinClient = 0, besoinConjoint = 0;

    if (approche === 'remplacement') {
      const pctClient = parseFloat(document.getElementById('inval-rr-pct-client')?.value) || 70;
      const revClient = getRevenusByOwner('client', isNet).total / 12;
      besoinClient = Math.round(revClient * pctClient / 100);
      const elC = document.getElementById('inval-rr-montant-client');
      if (elC) elC.textContent = fmtMoney(besoinClient);

      if (isCouple) {
        const pctConj = parseFloat(document.getElementById('inval-rr-pct-conjoint')?.value) || 70;
        const revConj = getRevenusByOwner('conjoint', isNet).total / 12;
        besoinConjoint = Math.round(revConj * pctConj / 100);
        const elJ = document.getElementById('inval-rr-montant-conjoint');
        if (elJ) elJ.textContent = fmtMoney(besoinConjoint);
      }
    } else {
      const depKeys = ['hypotheque','dettes','loyer','epargne','subsistance','autres'];
      besoinClient = depKeys.reduce((s, k) => s + (parseFloat(document.getElementById(`inval-dep-${k}-c`)?.value?.replace(/\s/g,'').replace(',','.')) || 0), 0);
      if (isCouple) besoinConjoint = depKeys.reduce((s, k) => s + (parseFloat(document.getElementById(`inval-dep-${k}-j`)?.value?.replace(/\s/g,'').replace(',','.')) || 0), 0);
    }

    // Couverture existante par propriétaire
    let couvertureClient = 0, couvertureConjoint = 0;
    _invalAvList.forEach(av => {
      if (av.exclure) return;
      if (av.owner === 'client' || av.owner === 'both') couvertureClient += av.montant;
      if (av.owner === 'conjoint' || av.owner === 'both') couvertureConjoint += av.montant;
    });

    // Autres revenus
    const autresClient = parseFloat(document.getElementById('inval-rev-client')?.value?.replace(/\s/g,'').replace(',','.')) || 0;
    const autresConj = isCouple ? (parseFloat(document.getElementById('inval-rev-conjoint')?.value?.replace(/\s/g,'').replace(',','.')) || 0) : 0;

    const ecartClient = besoinClient - couvertureClient - autresClient;
    const ecartConj = besoinConjoint - couvertureConjoint - autresConj;

    const resume = document.getElementById('inval-resume-body');
    if (!resume) return;

    const rowHtml = (label, val) => `<div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border);font-size:13px"><span style="color:var(--muted)">${label}</span><strong>${val}</strong></div>`;

    const sectionHtml = (title, besoin, couverture, autres, ecart) => {
      const sign = ecart > 0;
      return `<div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:10px 0 4px">${title}</div>
        ${rowHtml('Besoin mensuel estimé', fmtMoney(besoin)+'/mois')}
        ${rowHtml('Couverture existante', fmtMoney(couverture)+'/mois')}
        ${autres ? rowHtml('Autres revenus', fmtMoney(autres)+'/mois') : ''}
        <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:13px">
          <span style="font-weight:600">${sign ? 'Besoin additionnel' : 'Excédent'}</span>
          <strong style="color:${sign ? '#ef4444' : '#22c55e'}">${fmtMoney(Math.abs(ecart))}/mois</strong>
        </div>`;
    };

    let html = sectionHtml(isCouple ? clientPrenom : 'Protection', besoinClient, couvertureClient, autresClient, ecartClient);
    if (isCouple) {
      html += `<div style="border-top:2px solid var(--border);margin:4px 0"></div>`;
      html += sectionHtml(conjointPrenom, besoinConjoint, couvertureConjoint, autresConj, ecartConj);
    }
    resume.innerHTML = html;
  }

  function invalRenderAvList() {
    const list = document.getElementById('inval-av-list');
    if (!list) return;
    if (!_invalAvList.length) {
      list.innerHTML = '<p style="padding:14px;font-size:13px;color:var(--muted);margin:0">Aucune assurance invalidité enregistrée.</p>';
      return;
    }
    const iconEdit = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 24" width="15" height="15" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>`;
    const iconDel  = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 24" width="15" height="15" fill="currentColor"><path d="m6,19.008q0,0.82 0.586,1.406t1.406,0.586l8.016,0q0.82,0 1.406,-0.586t0.586,-1.406l0,-12l-12,0l0,12zm9.492,-15l-0.984,-1.008l-5.016,0l-0.984,1.008l-3.516,0l0,1.992l14.016,0l0,-1.992l-3.516,0z"/></svg>`;
    const cols = 'grid-template-columns:1.2fr 1fr 1fr 1fr 72px';
    const hdrStyle = `display:grid;${cols};padding:8px 16px;background:var(--bg-light,#f8f9fa);border-bottom:1px solid var(--border);font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em`;
    const header = `<div style="${hdrStyle}">
      <div>Type</div><div>Assuré</div>
      <div style="text-align:right">Montant</div>
      <div style="text-align:right">Prime annuelle</div>
      <div></div>
    </div>`;
    const rows = _invalAvList.map((av, i) => {
      const opacity = av.exclure ? ';opacity:.45' : '';
      return `<div style="display:grid;${cols};align-items:center;padding:10px 16px;border-bottom:1px solid var(--border);font-size:13px${opacity}">
        <div style="font-weight:600">${av.typeTx || av.type || '—'}</div>
        <div style="color:var(--muted)">${av.ownerTx || '—'}</div>
        <div style="text-align:right;font-weight:600;color:var(--navy)">${fmtMoney(av.montant)}</div>
        <div style="text-align:right;color:var(--muted)">${fmtMoney(av.prime)}</div>
        <div style="display:flex;gap:4px;justify-content:flex-end">
          <button onclick="openInvalAvEditModal(${i})" title="Modifier" style="background:none;border:none;cursor:pointer;color:var(--muted);padding:4px;border-radius:4px;line-height:1;display:flex" onmouseover="this.style.color='var(--navy)'" onmouseout="this.style.color='var(--muted)'">${iconEdit}</button>
          <button onclick="_invalAvList.splice(${i},1);invalRenderAvList();invaliditeCalc()" title="Supprimer" style="background:none;border:none;cursor:pointer;color:var(--muted);padding:4px;border-radius:4px;line-height:1;display:flex" onmouseover="this.style.color='#e53e3e'" onmouseout="this.style.color='var(--muted)'">${iconDel}</button>
        </div>
      </div>`;
    }).join('');
    list.innerHTML = header + rows;
  }

  function openInvalAvEditModal(idx) {
    const av = _invalAvList[idx];
    if (!av) return;
    openInvalAvModal(); // resets _invalAvEditIdx to -1, must re-set after
    _invalAvEditIdx = idx;
    // Type + owner
    document.getElementById('inval-av-type').value = av.type || '';
    invalTypeChange();
    const propEl = document.getElementById('inval-av-proprietaire');
    if (propEl) propEl.value = av.owner || '';
    // Champs communs
    document.getElementById('inval-av-montant').value = av.montant > 0 ? av.montant : '';
    document.getElementById('inval-av-prime').value = av.prime > 0 ? av.prime : '';
    document.getElementById('inval-av-assureur').value = av.assureur || '';
    document.getElementById('inval-av-date').value = av.date || '';
    const imposableRadio = document.querySelector(`input[name="inval-av-imposable"][value="${av.imposable || 'non'}"]`);
    if (imposableRadio) imposableRadio.checked = true;
    document.getElementById('inval-av-carence-val').value = av.carenceVal || '';
    document.getElementById('inval-av-carence-unit').value = av.carenceUnit || '';
    document.getElementById('inval-av-couverture-unit').value = av.couvertureUnit || '';
    document.getElementById('inval-av-couverture-val').value = av.couvertureVal || '';
    invalCouvertureUnitChange();
    document.getElementById('inval-av-exclure').checked = av.exclure || false;
    document.getElementById('inval-av-notes').value = av.notes || '';
    // Champs collectifs
    if (av.type === 'collective') {
      const appR = document.querySelector(`input[name="inval-av-approche"][value="${av.approche || 'pct'}"]`);
      if (appR) appR.checked = true;
      invalApprocheChange();
      if (av.approche === 'montant') {
        document.getElementById('inval-av-montant-coll').value = av.montant > 0 ? av.montant : '';
      } else {
        document.getElementById('inval-av-prestation-niveau').value = av.prestationNiveau || '';
        document.getElementById('inval-av-revenu-assurable').value = av.revenuAssurable > 0 ? av.revenuAssurable : '';
        document.getElementById('inval-av-pct0').value = av.pct0 > 0 ? av.pct0 : '';
        document.getElementById('inval-av-amt0').value = av.amt0 > 0 ? av.amt0 : '';
        document.getElementById('inval-av-pct1').value = av.pct1 > 0 ? av.pct1 : '';
        document.getElementById('inval-av-amt1').value = av.amt1 > 0 ? av.amt1 : '';
        document.getElementById('inval-av-pct2').value = av.pct2 > 0 ? av.pct2 : '';
        document.getElementById('inval-av-prestation-max').value = av.prestationMax > 0 ? av.prestationMax : '';
        invalNiveauChange();
      }
    }
  }

  function invalCouvertureUnitChange() {
    const unit = document.getElementById('inval-av-couverture-unit')?.value || '';
    const jusqua = document.getElementById('inval-av-couverture-jusqua');
    const suffix = document.getElementById('inval-av-couverture-suffix');
    if (jusqua) jusqua.style.display = unit === 'maxage' ? '' : 'none';
    if (suffix) {
      if (unit === 'week')   suffix.textContent = 'semaines';
      else if (unit === 'year' || unit === 'maxage') suffix.textContent = 'ans';
      else suffix.textContent = '';
    }
  }

  function openInvalAvModal() {
    _invalAvEditIdx = -1;
    apFillBienProprietaire('inval-av-proprietaire');
    document.getElementById('inval-av-type').value = '';
    document.getElementById('inval-av-montant').value = '';
    document.getElementById('inval-av-prime').value = '';
    document.getElementById('inval-av-assureur').value = '';
    document.getElementById('inval-av-date').value = '';
    const noRadio = document.querySelector('input[name="inval-av-imposable"][value="non"]');
    if (noRadio) noRadio.checked = true;
    document.getElementById('inval-av-carence-val').value = '';
    document.getElementById('inval-av-carence-unit').value = '';
    document.getElementById('inval-av-couverture-unit').value = '';
    document.getElementById('inval-av-couverture-val').value = '';
    invalCouvertureUnitChange();
    // Collective-specific fields
    const pctApproche = document.querySelector('input[name="inval-av-approche"][value="pct"]');
    if (pctApproche) pctApproche.checked = true;
    document.getElementById('inval-av-prestation-niveau').value = '';
    document.getElementById('inval-av-revenu-assurable').value = '';
    document.getElementById('inval-av-pct0').value = '';
    document.getElementById('inval-av-amt0').value = '';
    document.getElementById('inval-av-pct1').value = '';
    document.getElementById('inval-av-amt1').value = '';
    document.getElementById('inval-av-pct2').value = '';
    document.getElementById('inval-av-prestation-max').value = '';
    document.getElementById('inval-av-montant-coll').value = '';
    invalNiveauChange();
    invalTypeChange();
    invalApprocheChange();
    document.getElementById('inval-av-exclure').checked = false;
    document.getElementById('inval-av-notes').value = '';
    document.getElementById('modal-inval-av').style.display = 'flex';
    setTimeout(() => document.getElementById('inval-av-type').focus(), 50);
  }
  function closeInvalAvModal() { document.getElementById('modal-inval-av').style.display = 'none'; }
  function saveInvalAv() {
    const typeEl = document.getElementById('inval-av-type');
    const type = typeEl.value;
    const typeTx = typeEl.options[typeEl.selectedIndex]?.text || '';
    // Collective approche détermine la source du montant de prestation
    const approche = document.querySelector('input[name="inval-av-approche"]:checked')?.value || 'pct';
    const montantRaw = (type === 'collective' && approche === 'montant')
      ? (document.getElementById('inval-av-montant-coll')?.value || '0')
      : (document.getElementById('inval-av-montant')?.value || '0');
    const montant = parseFloat(montantRaw.replace(/\s/g,'').replace(',','.')) || 0;
    const prime = parseFloat(document.getElementById('inval-av-prime').value.replace(/\s/g,'').replace(',','.')) || 0;
    const assureurEl = document.getElementById('inval-av-assureur');
    const assureur = assureurEl.value;
    const assureurTx = assureurEl.options[assureurEl.selectedIndex]?.text || '';
    const date = document.getElementById('inval-av-date').value.trim();
    const imposable = document.querySelector('input[name="inval-av-imposable"]:checked')?.value || 'non';
    const carenceVal = document.getElementById('inval-av-carence-val').value.trim();
    const carenceUnitEl = document.getElementById('inval-av-carence-unit');
    const carenceUnit = carenceUnitEl.value;
    const carenceUnitTx = carenceUnitEl.options[carenceUnitEl.selectedIndex]?.text || '';
    const couvertureUnitEl = document.getElementById('inval-av-couverture-unit');
    const couvertureUnit = couvertureUnitEl.value;
    const couvertureUnitTx = couvertureUnitEl.options[couvertureUnitEl.selectedIndex]?.text || '';
    const couvertureVal = document.getElementById('inval-av-couverture-val').value.trim();
    const exclure = document.getElementById('inval-av-exclure').checked;
    const notes = document.getElementById('inval-av-notes').value.trim();
    const prop = document.getElementById('inval-av-proprietaire');
    const owner = prop.value || 'client';
    const ownerTx = prop.options[prop.selectedIndex]?.text || owner;
    // Collective-specific fields (approche already read above)
    const prestationNiveauEl = document.getElementById('inval-av-prestation-niveau');
    const prestationNiveau = prestationNiveauEl.value;
    const prestationNiveauTx = prestationNiveauEl.options[prestationNiveauEl.selectedIndex]?.text || '';
    const revenuAssurable = parseFloat((document.getElementById('inval-av-revenu-assurable').value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const pct0 = parseFloat((document.getElementById('inval-av-pct0').value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const amt0 = parseFloat((document.getElementById('inval-av-amt0').value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const pct1 = parseFloat((document.getElementById('inval-av-pct1').value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const amt1 = parseFloat((document.getElementById('inval-av-amt1').value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const pct2 = parseFloat((document.getElementById('inval-av-pct2').value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const prestationMax = parseFloat((document.getElementById('inval-av-prestation-max').value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    // Prestation mensuelle effective pour les calculs (revenu mensuel assurable = revenuAssurable / 12)
    let effectifMontant = montant;
    if (type === 'collective' && approche === 'pct' && revenuAssurable > 0) {
      const revMensuel = revenuAssurable / 12;
      if (prestationNiveau === 'first') {
        effectifMontant = revMensuel * pct0 / 100;
      } else if (prestationNiveau === 'second') {
        effectifMontant = Math.min(revMensuel, amt0) * pct0 / 100 + Math.max(0, revMensuel - amt0) * pct1 / 100;
      } else if (prestationNiveau === 'third') {
        effectifMontant = Math.min(revMensuel, amt0) * pct0 / 100
          + Math.max(0, Math.min(revMensuel - amt0, amt1)) * pct1 / 100
          + Math.max(0, revMensuel - amt0 - amt1) * pct2 / 100;
      }
      if (prestationMax > 0) effectifMontant = Math.min(effectifMontant, prestationMax);
    }
    const item = { type, typeTx, montant: effectifMontant, prime, assureur, assureurTx, date, imposable, carenceVal, carenceUnit, carenceUnitTx, couvertureUnit, couvertureUnitTx, couvertureVal, exclure, notes, owner, ownerTx, approche, prestationNiveau, prestationNiveauTx, revenuAssurable, pct0, amt0, pct1, amt1, pct2, prestationMax };
    if (_invalAvEditIdx >= 0) {
      _invalAvList[_invalAvEditIdx] = item;
      _invalAvEditIdx = -1;
    } else {
      _invalAvList.push(item);
    }
    invalRenderAvList();
    closeInvalAvModal();
    invaliditeCalc();
  }
  document.getElementById('modal-inval-av')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-inval-av')) closeInvalAvModal();
  });

  /* ── TOPBAR DYNAMIQUE ────────────────────────────────── */
  (function() {
    const dateEl = document.getElementById('abf-topbar-date');
    if (dateEl) {
      const now = new Date();
      const opts = { weekday:'long', day:'numeric', month:'long', year:'numeric' };
      dateEl.textContent = now.toLocaleDateString('fr-CA', opts);
    }
    const userEl = document.getElementById('abf-topbar-user');
    if (userEl && window.ABF_ADVISOR_NAME) {
      userEl.innerHTML = '👤 <strong>' + window.ABF_ADVISOR_NAME + '</strong>';
    }
  })();

  /* ── SÉRIALISATION / PERSISTANCE ─────────────────────── */
  function gatherPayload() {
    const v = id => (document.getElementById(id)?.value || '');
    const radio = name => document.querySelector(`input[name="${name}"]:checked`)?.value || '';

    const enfants = [];
    document.querySelectorAll('#enfants-list .enfant-item[data-charge]').forEach(el => {
      enfants.push({
        prenom: el.dataset.enfPrenom || '', nom: el.dataset.enfNom || '',
        sexe: el.dataset.enfSexe || '', jour: el.dataset.enfJour || '',
        mois: el.dataset.enfMois || '', annee: el.dataset.enfAnnee || '',
        relation: el.dataset.enfRelation || '', relation_conjoint: el.dataset.enfRelationConjoint || '', charge: el.dataset.charge || '',
      });
    });

    const revenus = [];
    document.querySelectorAll('#revenu-list tr[data-form-json]').forEach(tr => {
      try { revenus.push(JSON.parse(tr.dataset.formJson)); } catch {}
    });

    const actifs = [];
    document.querySelectorAll('#actifs-list [data-form-json]').forEach(el => {
      try {
        const obj = JSON.parse(el.dataset.formJson || '{}');
        actifs.push({ ...obj, _type: el.dataset.aptype || '', _valeur: parseFloat(el.dataset.valeur) || 0,
          _owner: el.dataset.owner || '', _modalType: el.dataset.modalType || '',
          _partClient: el.dataset.partClient !== undefined ? parseFloat(el.dataset.partClient) : undefined,
          _partConjoint: el.dataset.partConjoint !== undefined ? parseFloat(el.dataset.partConjoint) : undefined });
      } catch {}
    });

    const passifs = [];
    document.querySelectorAll('#passifs-list [data-form-json]').forEach(el => {
      try {
        const obj = JSON.parse(el.dataset.formJson || '{}');
        passifs.push({ ...obj, _type: el.dataset.aptype || '', _valeur: parseFloat(el.dataset.valeur) || 0,
          _owner: el.dataset.owner || '', _modalType: el.dataset.modalType || '',
          _partClient: el.dataset.partClient !== undefined ? parseFloat(el.dataset.partClient) : undefined,
          _partConjoint: el.dataset.partConjoint !== undefined ? parseFloat(el.dataset.partConjoint) : undefined });
      } catch {}
    });

    const legal = [];
    document.querySelectorAll('#legal-list [data-form-json]').forEach(el => {
      try { legal.push(JSON.parse(el.dataset.formJson)); } catch {}
    });

    const decesDeps = [];
    document.querySelectorAll('#deces-dep-list .deces-dep-row').forEach(row => {
      decesDeps.push({ desc: row.dataset.desc || '', montant: parseFloat(row.dataset.montant) || 0, indexed: row.dataset.indexed || 'oui' });
    });
    const decesDepsConj = [];
    document.querySelectorAll('#deces-dep-list-conjoint .deces-dep-row').forEach(row => {
      decesDepsConj.push({ desc: row.dataset.desc || '', montant: parseFloat(row.dataset.montant) || 0, indexed: row.dataset.indexed || 'oui' });
    });

    const decesAv = [];
    document.querySelectorAll('.deces-av-row[data-form-json]').forEach(el => {
      try { decesAv.push(JSON.parse(el.dataset.formJson)); } catch {}
    });

    return {
      client: {
        prenom: v('client-prenom'), nom: v('client-nom'), sexe: radio('sexe'),
        ddn_jour: v('client-ddn-jour'), ddn_mois: v('client-ddn-mois'),
        ddn_annee: v('client-naissance-annee'), etat_civil: v('client-etat-civil'),
        province: v('client-province'), canada_depuis: v('client-canada-depuis'),
        addr_civique: v('client-addr-civique'), addr_rue: v('client-addr-rue'),
        addr_type_unite: v('client-addr-type-unite'), addr_numero: v('client-addr-numero'),
        addr_case: v('client-addr-case'), addr_ville: v('client-addr-ville'),
        addr_province: v('client-addr-province'), addr_postal: v('client-addr-postal'),
        courriel: v('client-courriel'), cellulaire: v('client-cellulaire'), telephone: v('client-telephone'),
      },
      has_spouse: document.querySelector('input[name="plan"][value="conjoint"]')?.checked || false,
      conjoint: {
        prenom: v('conjoint-prenom'), nom: v('conjoint-nom'), lien: v('conjoint-lien'), sexe: radio('co-sexe'),
        ddn_jour: v('conjoint-ddn-jour'), ddn_mois: v('conjoint-ddn-mois'),
        ddn_annee: v('conjoint-naissance-annee'), etat_civil: v('conjoint-etat-civil'),
        province: v('conjoint-province'), canada_depuis: v('conjoint-canada-depuis'),
        addr_civique: v('conjoint-addr-civique'), addr_rue: v('conjoint-addr-rue'),
        addr_type_unite: v('conjoint-addr-type-unite'), addr_numero: v('conjoint-addr-numero'),
        addr_case: v('conjoint-addr-case'), addr_ville: v('conjoint-addr-ville'),
        addr_province: v('conjoint-addr-province'), addr_postal: v('conjoint-addr-postal'),
        courriel: v('conjoint-courriel'), cellulaire: v('conjoint-cellulaire'), telephone: v('conjoint-telephone'),
      },
      enfants, revenus, actifs, passifs, legal,
      deces: {
        rrq_client: v('deces-rrq-client'), rrq_conjoint: v('deces-rrq-conjoint'),
        autres_revenus_c: v('deces-autres-revenus-c'), autres_revenus_j: v('deces-autres-revenus-j'),
        rr_pct_c: v('deces-rr-pct-c'), rr_pct_j: v('deces-rr-pct-j'),
        rr_duree_c: v('deces-rr-duree-c'), rr_duree_j: v('deces-rr-duree-j'),
        rr_taux_c: v('deces-rr-taux-c'), rr_taux_j: v('deces-rr-taux-j'),
        deps_client: decesDeps, deps_conjoint: decesDepsConj, av: decesAv,
      },
      invalidite: {
        autres_rev_c: v('inval-rev-client'),   autres_rev_j: v('inval-rev-conjoint'),
        ae_c: radio('inval-ae-c'),             ae_j: radio('inval-ae-j'),
        dep_hypotheque_c: v('inval-dep-hypotheque-c'), dep_hypotheque_j: v('inval-dep-hypotheque-j'),
        dep_dettes_c:     v('inval-dep-dettes-c'),     dep_dettes_j:     v('inval-dep-dettes-j'),
        dep_loyer_c:      v('inval-dep-loyer-c'),      dep_loyer_j:      v('inval-dep-loyer-j'),
        dep_epargne_c:    v('inval-dep-epargne-c'),    dep_epargne_j:    v('inval-dep-epargne-j'),
        dep_subsistance_c:v('inval-dep-subsistance-c'),dep_subsistance_j:v('inval-dep-subsistance-j'),
        dep_autres_c:     v('inval-dep-autres-c'),     dep_autres_j:     v('inval-dep-autres-j'),
        av_list: typeof _invalAvList !== 'undefined' ? _invalAvList : [],
      },
      maladieGrave: {
        client: {
          useDisability: document.querySelector('input[name="mg-use-disability-c"]:checked')?.value || 'non',
          useEmergency:  document.querySelector('input[name="mg-use-emergency-c"]:checked')?.value  || 'non',
          brutNet: (document.getElementById('mg-bn-net-c')?.classList.contains('active')) ? 'net' : 'brut',
          rrPct:   v('mg-rr-pct-c'), rrDuree: v('mg-rr-duree-c'),
          aidantType:    document.querySelector('input[name="mg-aidant-type-c"]:checked')?.value || 'aucun',
          aidantMontant: v('mg-aidant-montant-c'), aidantDuree: v('mg-aidant-duree-c'),
          coverage: document.querySelector('input[name="mg-coverage-c"]:checked')?.value || 'aucun',
          depTraitement: v('mg-dep-traitement-c'),    depEquipement: v('mg-dep-equipement-c'),
          depAdaptation: v('mg-dep-adaptation-c'),    depVehicule:   v('mg-dep-vehicule-c'),
          depTransport:  v('mg-dep-transport-c'),     depAide:       v('mg-dep-aide-domicile-c'),
          depSoins:      v('mg-dep-soins-professionnel-c'), depMeds: v('mg-dep-medicaments-rec-c'),
        },
        conjoint: {
          useDisability: document.querySelector('input[name="mg-use-disability-j"]:checked')?.value || 'non',
          useEmergency:  document.querySelector('input[name="mg-use-emergency-j"]:checked')?.value  || 'non',
          brutNet: (document.getElementById('mg-bn-net-j')?.classList.contains('active')) ? 'net' : 'brut',
          rrPct:   v('mg-rr-pct-j'), rrDuree: v('mg-rr-duree-j'),
          aidantType:    document.querySelector('input[name="mg-aidant-type-j"]:checked')?.value || 'aucun',
          aidantMontant: v('mg-aidant-montant-j'), aidantDuree: v('mg-aidant-duree-j'),
          coverage: document.querySelector('input[name="mg-coverage-j"]:checked')?.value || 'aucun',
          depTraitement: v('mg-dep-traitement-j'),    depEquipement: v('mg-dep-equipement-j'),
          depAdaptation: v('mg-dep-adaptation-j'),    depVehicule:   v('mg-dep-vehicule-j'),
          depTransport:  v('mg-dep-transport-j'),     depAide:       v('mg-dep-aide-domicile-j'),
          depSoins:      v('mg-dep-soins-professionnel-j'), depMeds: v('mg-dep-medicaments-rec-j'),
        },
      },
      retraite: {
        ageClient:    v('retraite-age-client'),    typeClient:   v('retraite-type-client'),
        anneeClient:  v('retraite-annee-client'),
        ageConjoint:  v('retraite-age-conjoint'),  typeConjoint: v('retraite-type-conjoint'),
        anneeConjoint:v('retraite-annee-conjoint'),
        goalType:    document.querySelector('input[name="retraite-goal-type"]:checked')?.value || 'individuel',
        profilAvantClient:    v('retraite-profil-avant-client'),
        profilPendantClient:  v('retraite-profil-pendant-client'),
        profilAvantConjoint:  v('retraite-profil-avant-conjoint'),
        profilPendantConjoint:v('retraite-profil-pendant-conjoint'),
        pctClient:   v('retraite-pct-client-0'), pctConjoint: v('retraite-pct-conjoint-0'),
        saveSurplusClient:  document.querySelector('input[name="retraite-save-surplus-c"]:checked')?.value  || 'non',
        saveSurplusConjoint:document.querySelector('input[name="retraite-save-surplus-j"]:checked')?.value || 'non',
        depenses: typeof _retraiteDepenses !== 'undefined' ? _retraiteDepenses : [],
      },
      projets: typeof _projets !== 'undefined' ? _projets : [],
      recommandations: {
        notes: typeof _recomNotes !== 'undefined' ? _recomNotes : {},
      },
      valeurs_defaut: {
        province: v('vd-province'), fu: radio('vd-fu'), fu_mois: v('vd-fu-mois'),
        funerailles: v('vd-funerailles'), deces_rr: radio('vd-deces-rr'),
        deces_pct: v('vd-deces-pct'), deces_sal: radio('vd-deces-sal'),
        deces_freq: radio('vd-deces-freq'), inv_type: radio('vd-inv-type'),
        inv_sal: radio('vd-inv-sal'), inv_pct: v('vd-inv-pct'), mg: radio('vd-mg'),
        ret_pct: v('vd-ret-pct'), ret_freq: radio('vd-ret-freq'), ret_calc: radio('vd-ret-calc'),
        inflation: v('vd-inflation'), p_prudent: v('vd-p-prudent'), p_modere: v('vd-p-modere'),
        p_equilibre: v('vd-p-equilibre'), p_croissance: v('vd-p-croissance'), p_audacieux: v('vd-p-audacieux'),
      },
      hypotheses: typeof hypotheses !== 'undefined' ? { ...hypotheses } : { evClient: 94, evConj: 96 },
      objectifs: JSON.parse(JSON.stringify(objState)),
      navigation: {
        current_page: pages[current],
        done_pages: (() => {
          const done = [];
          document.querySelectorAll('.nav-item').forEach((el, i) => {
            if (el.classList.contains('done') && pages[i]) done.push(pages[i]);
          });
          return done;
        })(),
      },
    };
  }

  function populateFromPayload(p) {
    if (!p) return;
    const sv = (id, val) => { const el = document.getElementById(id); if (el && val !== undefined && val !== null) el.value = val; };
    const sr = (name, val) => { if (!val) return; const el = document.querySelector(`input[name="${name}"][value="${val}"]`); if (el) el.checked = true; };

    // Type de plan (individuel / conjoint)
    if (p.has_spouse) {
      const radio = document.querySelector('input[name="plan"][value="conjoint"]');
      if (radio) { radio.checked = true; document.getElementById('conjoint-section').style.display = 'block'; syncConjointInfo(); }
    } else {
      const radio = document.querySelector('input[name="plan"][value="individuel"]');
      if (radio) radio.checked = true;
    }

    // Client
    const c = p.client || {};
    sv('client-prenom', c.prenom); sv('client-nom', c.nom);
    sv('client-ddn-jour', c.ddn_jour); sv('client-ddn-mois', c.ddn_mois);
    sv('client-naissance-annee', c.ddn_annee); sv('client-etat-civil', c.etat_civil);
    sv('client-province', c.province); sv('client-canada-depuis', c.canada_depuis);
    sv('client-addr-civique', c.addr_civique); sv('client-addr-rue', c.addr_rue);
    sv('client-addr-type-unite', c.addr_type_unite); sv('client-addr-numero', c.addr_numero);
    sv('client-addr-case', c.addr_case); sv('client-addr-ville', c.addr_ville);
    sv('client-addr-province', c.addr_province); sv('client-addr-postal', c.addr_postal);
    sv('client-courriel', c.courriel); sv('client-cellulaire', c.cellulaire); sv('client-telephone', c.telephone);
    if (c.sexe) sr('sexe', c.sexe);

    // Conjoint
    const j = p.conjoint || {};
    sv('conjoint-prenom', j.prenom); sv('conjoint-nom', j.nom); sv('conjoint-lien', j.lien);
    updateConjointLienLabel();
    sv('conjoint-ddn-jour', j.ddn_jour); sv('conjoint-ddn-mois', j.ddn_mois);
    sv('conjoint-naissance-annee', j.ddn_annee); sv('conjoint-etat-civil', j.etat_civil);
    sv('conjoint-province', j.province); sv('conjoint-canada-depuis', j.canada_depuis);
    sv('conjoint-addr-civique', j.addr_civique); sv('conjoint-addr-rue', j.addr_rue);
    sv('conjoint-addr-type-unite', j.addr_type_unite); sv('conjoint-addr-numero', j.addr_numero);
    sv('conjoint-addr-case', j.addr_case); sv('conjoint-addr-ville', j.addr_ville);
    sv('conjoint-addr-province', j.addr_province); sv('conjoint-addr-postal', j.addr_postal);
    sv('conjoint-courriel', j.courriel); sv('conjoint-cellulaire', j.cellulaire); sv('conjoint-telephone', j.telephone);
    if (j.sexe) sr('co-sexe', j.sexe);

    // Enfants
    const enfList = document.getElementById('enfants-list');
    if (enfList && (p.enfants || []).length) {
      enfList.classList.remove('list-empty'); enfList.innerHTML = '';
      const REL = { child:'Enfant', dependent:'Autre', fathermother:'Père-Mère', grandparent:'Grand-parent', grandchild:'Petit-enfant', sibling:'Frère-Sœur', otherrelative:'Parenté', exspouse:'Ex-conjoint(e)' };
      const CHARGE = { client:'Client', conjoint:'Conjoint', both:'Les deux', none:'Non à charge' };
      (p.enfants || []).forEach(d => {
        const item = document.createElement('div');
        item.className = 'enfant-item';
        item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px;gap:12px';
        item.dataset.charge = d.charge || ''; item.dataset.enfPrenom = d.prenom || '';
        item.dataset.enfNom = d.nom || ''; item.dataset.enfSexe = d.sexe || '';
        item.dataset.enfJour = d.jour || ''; item.dataset.enfMois = d.mois || '';
        item.dataset.enfAnnee = d.annee || ''; item.dataset.enfRelation = d.relation || ''; item.dataset.enfRelationConjoint = d.relation_conjoint || '';
        item.innerHTML = _buildEnfantItemHTML(
          [d.prenom, d.nom].filter(Boolean).join(' '),
          REL[d.relation] || d.relation || '—',
          [d.jour, d.mois, d.annee].filter(Boolean).join(' ') || '—',
          d.sexe === 'M' ? 'Masculin' : (d.sexe === 'F' ? 'Féminin' : '—'),
          CHARGE[d.charge] || d.charge || ''
        );
        enfList.appendChild(item);
      });
    }

    // Revenus
    const revTbody = document.getElementById('revenu-list');
    if (revTbody && (p.revenus || []).length) {
      revTbody.innerHTML = '';
      (p.revenus || []).forEach(d => {
        const annuel = d.annuel || 0;
        const r = computeImpot ? computeImpot(annuel) : null;
        const fmt = n => n.toLocaleString('fr-CA', { maximumFractionDigits: 0 }) + ' $';
        const netLabel = r ? `<span style="font-size:11px;color:#22c55e;margin-left:4px">(net ${fmt(r.net)})</span>` : '';
        const tr = document.createElement('tr');
        tr.dataset.revenuAnnuel = annuel;
        tr.dataset.owner = d.owner || 'client';
        tr.dataset.revenuType = d.isEmploi ? 'emploi' : 'autre';
        tr.dataset.formJson = JSON.stringify(d);
        const ownerTx = d.owner === 'conjoint' ? (getConjointPrenom() || 'Conjoint(e)') : getClientPrenom();
        tr.innerHTML = `
          <td>${ownerTx}</td><td>${d.isEmploi ? 'Emploi' : 'Autre'}</td>
          <td>${d.description || ''}</td>
          <td>${d.montant || '0'} $${netLabel}</td>
          <td>${d.frequence || 'Annuelle'}</td>
          <td class="col-action">
            <button class="re-action-btn" title="Détail fiscal" onclick="reToggleDetail(this)" style="color:var(--navy)">
              <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <button class="re-action-btn del" title="Supprimer" onclick="reDeleteRow(this)">
              <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            </button>
          </td>`;
        revTbody.appendChild(tr);
        const trDetail = document.createElement('tr');
        trDetail.className = 're-detail-row'; trDetail.style.display = 'none';
        trDetail.innerHTML = r ? `<td colspan="6"><div class="re-detail-inner">
          <div class="re-detail-item"><span class="re-detail-lbl">RRQ</span><span class="re-detail-val deduction">${fmt(r.rrq)}</span></div>
          <div class="re-detail-item"><span class="re-detail-lbl">AE</span><span class="re-detail-val deduction">${fmt(r.ae)}</span></div>
          <div class="re-detail-item"><span class="re-detail-lbl">RQAP</span><span class="re-detail-val deduction">${fmt(r.rqap)}</span></div>
          <div class="re-detail-item"><span class="re-detail-lbl">Impôt fédéral</span><span class="re-detail-val deduction">${fmt(r.fed)}</span></div>
          <div class="re-detail-item"><span class="re-detail-lbl">Impôt Québec</span><span class="re-detail-val deduction">${fmt(r.qc)}</span></div>
          <div class="re-detail-item"><span class="re-detail-lbl">Taux effectif</span><span class="re-detail-val">${r.taux.toFixed(1).replace('.', ',')} %</span></div>
          <div class="re-detail-item" style="grid-column:1/3"><span class="re-detail-lbl">Net annuel</span><span class="re-detail-val net">${fmt(r.net)}</span></div>
          <div class="re-detail-item"><span class="re-detail-lbl">Net mensuel</span><span class="re-detail-val net">${fmt(r.net / 12)}</span></div>
        </div></td>` : `<td colspan="6"><div style="padding:8px 14px;font-size:12px;color:var(--muted)">Calcul non disponible.</div></td>`;
        revTbody.appendChild(trDetail);
      });
      if (typeof updateReSidebar === 'function') updateReSidebar();
    }

    // Actifs
    if ((p.actifs || []).length) {
      const actifsList = document.getElementById('actifs-list');
      if (actifsList) { actifsList.classList.remove('list-empty'); actifsList.innerHTML = ''; }
      (p.actifs || []).forEach(d => {
        const valNum = d._valeur || 0;
        const valTxt = valNum.toLocaleString('fr-CA') + ' $';
        const sub = d.portefeuille ? (d.portefeuille + (d.rendement ? ' · ' + d.rendement + '%' : '')) : '';
        const { _type, _valeur, _owner, _modalType, _partClient, _partConjoint, ...formData } = d;
        apAddToList('actifs-list', _type, d.description || _type || '', valTxt, sub, valNum, _owner, _modalType, JSON.stringify(formData), _partClient, _partConjoint);
      });
    }

    // Passifs
    if ((p.passifs || []).length) {
      const passifsList = document.getElementById('passifs-list');
      if (passifsList) { passifsList.classList.remove('list-empty'); passifsList.innerHTML = ''; }
      (p.passifs || []).forEach(d => {
        const valNum = d._valeur || 0;
        const valTxt = valNum.toLocaleString('fr-CA') + ' $';
        const { _type, _valeur, _owner, _modalType, _partClient, _partConjoint, ...formData } = d;
        apAddToList('passifs-list', _type, d.description || _type || '', valTxt, '', valNum, _owner, _modalType, JSON.stringify(formData), _partClient, _partConjoint);
      });
    }

    // Documents légaux
    const legalList = document.getElementById('legal-list');
    if (legalList && (p.legal || []).length) {
      legalList.classList.remove('list-empty'); legalList.innerHTML = '';
      (p.legal || []).forEach(d => {
        const item = document.createElement('div');
        item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);font-size:13px;gap:8px';
        item.dataset.formJson = JSON.stringify(d);
        item.innerHTML = `
          <span style="display:flex;align-items:center;gap:8px">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
            <span style="color:var(--text);font-weight:500">${d.docType || ''}</span>
            ${d.propText ? `<span style="color:var(--muted);font-size:11px">· ${d.propText}</span>` : ''}
            ${d.typeText && d.legalType ? `<span style="color:var(--muted);font-size:11px">· ${d.typeText}</span>` : ''}
          </span>
          <button onclick="this.closest('div[style]').remove()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:18px;line-height:1;padding:0 4px">×</button>`;
        legalList.appendChild(item);
      });
    }

    // Décès — champs simples
    const dd = p.deces || {};
    sv('deces-rrq-client', dd.rrq_client); sv('deces-rrq-conjoint', dd.rrq_conjoint);
    sv('deces-autres-revenus-c', dd.autres_revenus_c); sv('deces-autres-revenus-j', dd.autres_revenus_j);
    sv('deces-rr-pct-c', dd.rr_pct_c); sv('deces-rr-pct-j', dd.rr_pct_j);
    sv('deces-rr-duree-c', dd.rr_duree_c); sv('deces-rr-duree-j', dd.rr_duree_j);
    sv('deces-rr-taux-c', dd.rr_taux_c); sv('deces-rr-taux-j', dd.rr_taux_j);

    // Décès — dépenses
    ['client', 'conjoint'].forEach(who => {
      const listId = who === 'conjoint' ? 'deces-dep-list-conjoint' : 'deces-dep-list';
      const deps = who === 'conjoint' ? (dd.deps_conjoint || []) : (dd.deps_client || []);
      if (!deps.length) return;
      const depList = document.getElementById(listId);
      if (depList) depList.innerHTML = '';
      deps.forEach(d => {
        const uid = Math.random().toString(36).slice(2);
        const row = document.createElement('div');
        row.className = 'deces-dep-row';
        row.dataset.montant = d.montant || 0;
        row.dataset.desc = d.desc || '';
        row.dataset.indexed = d.indexed || 'oui';
        const mt = (+(d.montant || 0)).toLocaleString('fr-CA');
        row.innerHTML = `
          <span style="flex:1;color:var(--text)">${d.desc || ''}</span>
          <div class="input-sfx" style="max-width:140px">
            <input class="form-input" type="text" value="${mt}" placeholder="0"
              oninput="this.closest('[data-montant]').dataset.montant=parseFloat(this.value.replace(/\\s/g,'').replace(',','.'))||0;decesCalc()"/>
            <span class="sfx">$</span>
          </div>
          <div style="display:flex;flex-direction:column;align-items:center;gap:2px">
            <span style="font-size:10px;color:var(--muted);white-space:nowrap">Indexé à l'inflation</span>
            <div style="display:flex;gap:4px">
              <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="deces-idx-${uid}" value="oui" ${(d.indexed||'oui')==='oui'?'checked':''} onchange="this.closest('.deces-dep-row').dataset.indexed='oui'"/> Oui</label>
              <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="deces-idx-${uid}" value="non" ${(d.indexed||'oui')==='non'?'checked':''} onchange="this.closest('.deces-dep-row').dataset.indexed='non'"/> Non</label>
            </div>
          </div>
          <button onclick="this.closest('.deces-dep-row').remove();decesCalc()" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:18px;padding:0 4px">×</button>`;
        if (depList) depList.appendChild(row);
      });
    });

    // Décès — assurances vie
    if ((dd.av || []).length) {
      const avList = document.getElementById('deces-av-list');
      if (avList) {
        const empty = document.getElementById('deces-av-empty');
        if (empty) empty.remove();
        (dd.av || []).forEach(d => {
          const montant = d.montant || 0;
          const prime = d.prime || 0;
          const row = document.createElement('div');
          row.className = 'deces-av-row';
          row.dataset.montant = d.exclure ? 0 : montant;
          row.dataset.ownerVal = d.ownerVal || '';
          row.dataset.formJson = JSON.stringify(d);
          row.innerHTML = `
            <div style="flex:1">
              <div style="font-weight:600">${d.type || ''} — ${d.owner || ''}</div>
              <div style="font-size:11px;color:var(--muted)">${d.assureur || ''}${d.benef ? ' · Bénéficiaire: ' + d.benef : ''}${d.exclure ? ' · <em>Exclu de l\'analyse</em>' : ''}</div>
            </div>
            <div style="text-align:right;margin-right:12px">
              <div style="font-weight:700">${typeof fmtMoney === 'function' ? fmtMoney(montant) : montant}</div>
              ${prime > 0 ? `<div style="font-size:11px;color:var(--muted)">Prime: ${typeof fmtMoney === 'function' ? fmtMoney(prime) : prime}/an</div>` : ''}
            </div>
            <button onclick="this.closest('.deces-av-row').remove();decesCalc()" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:18px;padding:0 4px">×</button>`;
          avList.appendChild(row);
        });
      }
    }

    // Valeurs par défaut
    const vd = p.valeurs_defaut || {};
    sv('vd-province', vd.province); sv('vd-fu-mois', vd.fu_mois); sv('vd-funerailles', vd.funerailles);
    sv('vd-deces-pct', vd.deces_pct); sv('vd-inv-pct', vd.inv_pct); sv('vd-ret-pct', vd.ret_pct);
    sv('vd-inflation', vd.inflation); sv('vd-p-prudent', vd.p_prudent); sv('vd-p-modere', vd.p_modere);
    sv('vd-p-equilibre', vd.p_equilibre); sv('vd-p-croissance', vd.p_croissance); sv('vd-p-audacieux', vd.p_audacieux);
    if (vd.fu) sr('vd-fu', vd.fu);
    if (vd.deces_rr) sr('vd-deces-rr', vd.deces_rr);
    if (vd.deces_sal) sr('vd-deces-sal', vd.deces_sal);
    if (vd.deces_freq) sr('vd-deces-freq', vd.deces_freq);
    if (vd.inv_type) sr('vd-inv-type', vd.inv_type);
    if (vd.inv_sal) sr('vd-inv-sal', vd.inv_sal);
    if (vd.mg) sr('vd-mg', vd.mg);
    if (vd.ret_freq) sr('vd-ret-freq', vd.ret_freq);
    if (vd.ret_calc) sr('vd-ret-calc', vd.ret_calc);

    // Hypothèses
    if (p.hypotheses && typeof hypotheses !== 'undefined') {
      hypotheses.evClient = p.hypotheses.evClient ?? 94;
      hypotheses.evConj = p.hypotheses.evConj ?? 96;
    }

    // Invalidité
    const inv = p.invalidite || {};
    sv('inval-rev-client',  inv.autres_rev_c || '');
    sv('inval-rev-conjoint', inv.autres_rev_j || '');
    const setR = (name, val) => { const el = document.querySelector(`input[name="${name}"][value="${val}"]`); if(el) el.checked = true; };
    setR('inval-ae-c', inv.ae_c || 'non');
    setR('inval-ae-j', inv.ae_j || 'non');
    const depKeys = ['hypotheque','dettes','loyer','epargne','subsistance','autres'];
    depKeys.forEach(k => {
      sv(`inval-dep-${k}-c`, inv[`dep_${k}_c`] || '');
      sv(`inval-dep-${k}-j`, inv[`dep_${k}_j`] || '');
    });

    // Maladie grave
    const mg = p.maladieGrave || {};
    ['client','conjoint'].forEach(role => {
      const k = role === 'client' ? 'c' : 'j';
      const d = mg[role] || {};
      const setR = (name, val) => { const el = document.querySelector(`input[name="${name}"][value="${val}"]`); if(el) el.checked = true; };
      setR(`mg-use-disability-${k}`, d.useDisability || 'non');
      setR(`mg-use-emergency-${k}`,  d.useEmergency  || 'non');
      setR(`mg-coverage-${k}`,       d.coverage      || 'aucun');
      setR(`mg-aidant-type-${k}`,    d.aidantType    || 'aucun');
      sv(`mg-rr-pct-${k}`,    d.rrPct   || '70');
      sv(`mg-rr-duree-${k}`,  d.rrDuree || '12');
      sv(`mg-aidant-montant-${k}`, d.aidantMontant || '');
      sv(`mg-aidant-duree-${k}`,   d.aidantDuree  || '6');
      const fields = ['traitement','equipement','adaptation','vehicule','transport','aide-domicile','soins-professionnel','medicaments-rec'];
      const vals = [d.depTraitement,d.depEquipement,d.depAdaptation,d.depVehicule,d.depTransport,d.depAide,d.depSoins,d.depMeds];
      fields.forEach((f,i) => { if(vals[i] !== undefined) sv(`mg-dep-${f}-${k}`, vals[i]); });
      if (d.brutNet === 'net') {
        document.getElementById(`mg-bn-brut-${k}`)?.classList.remove('active');
        document.getElementById(`mg-bn-net-${k}`)?.classList.add('active');
      }
    });

    // Retraite
    const ret = p.retraite || {};
    sv('retraite-age-client',     ret.ageClient    || '65');
    sv('retraite-annee-client',   ret.anneeClient);
    sv('retraite-age-conjoint',   ret.ageConjoint  || '65');
    sv('retraite-annee-conjoint', ret.anneeConjoint);
    if (ret.typeClient)   { const el = document.getElementById('retraite-type-client');   if(el) el.value = ret.typeClient; }
    if (ret.typeConjoint) { const el = document.getElementById('retraite-type-conjoint'); if(el) el.value = ret.typeConjoint; }
    retraiteToggleType('client');
    retraiteToggleType('conjoint');
    if (ret.goalType) { const el = document.querySelector(`input[name="retraite-goal-type"][value="${ret.goalType}"]`); if(el) el.checked = true; }
    sv('retraite-pct-client-0',   ret.pctClient   || '70');
    sv('retraite-pct-conjoint-0', ret.pctConjoint || '70');
    if (ret.saveSurplusClient)   { const el = document.querySelector(`input[name="retraite-save-surplus-c"][value="${ret.saveSurplusClient}"]`);   if(el) el.checked = true; }
    if (ret.saveSurplusConjoint) { const el = document.querySelector(`input[name="retraite-save-surplus-j"][value="${ret.saveSurplusConjoint}"]`); if(el) el.checked = true; }
    ['avant-client','pendant-client','avant-conjoint','pendant-conjoint'].forEach(k => {
      const key = 'profil' + k.replace(/(^|-)(\w)/g,(_,s,c)=>c.toUpperCase());
      const el = document.getElementById(`retraite-profil-${k}`);
      if(el && ret[key]) el.value = ret[key];
    });
    if (Array.isArray(ret.depenses)) {
      _retraiteDepenses = ret.depenses;
      try { retraiteRenderDepenses(); } catch(e) { console.warn('retraiteRenderDepenses error', e); }
    }

    // Projets
    if (Array.isArray(p.projets)) {
      _projets = p.projets;
      try { projetsRender(); } catch(e) { console.warn('projetsRender error', e); }
    }

    // Recommandations notes
    if (p.recommandations?.notes) {
      _recomNotes = p.recommandations.notes;
      Object.entries(_recomNotes).forEach(([cat, text]) => {
        const el = document.getElementById(`recom-notes-${cat}`);
        if (el) el.value = text;
      });
    }
    if (typeof _invalAvList !== 'undefined' && Array.isArray(inv.av_list)) {
      _invalAvList = inv.av_list;
      if (typeof invalRenderAvList === 'function') invalRenderAvList();
    }

    // Objectifs
    if (p.objectifs) {
      Object.keys(p.objectifs).forEach(catId => {
        if (objState[catId]) objState[catId] = p.objectifs[catId];
      });
    }

    // Navigation : restaurer la page et l'état done/locked du nav
    const nav = p.navigation || {};
    const savedPage = nav.current_page;
    const donePagesSet = new Set(nav.done_pages || []);
    if (savedPage && pages.includes(savedPage)) {
      const navItems = document.querySelectorAll('.nav-item');
      const savedIdx = pages.indexOf(savedPage);
      navItems.forEach((el, i) => {
        if (i <= savedIdx) el.classList.remove('locked');
        if (donePagesSet.has(pages[i])) el.classList.add('done');
      });
      goTo(savedPage, navItems[savedIdx]);
    }

    // Recalculs
    setTimeout(() => {
      if (typeof syncConjointInfo === 'function') syncConjointInfo();
      if (typeof updateApSidebar === 'function') updateApSidebar();
      if (typeof updateReSidebar === 'function') updateReSidebar();
      if (typeof decesCalc === 'function') decesCalc();
      if (typeof invaliditeCalc === 'function') invaliditeCalc();
      if (typeof updateEpargneSection === 'function') updateEpargneSection();
    }, 100);
  }

  function autoSave(recordId, saveUrl, csrfToken, silent) {
    const payload = gatherPayload();
    fetch(saveUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: JSON.stringify({ payload }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.ok) {
        // Mettre à jour l'URL et le save_url si le slug a été généré
        if (data.url) {
          history.replaceState(history.state, '', data.url);
        }
        if (data.save_url) {
          window.ABF_SAVE_URL = data.save_url;
        }
        if (!silent) showToast('Brouillon sauvegardé');
      }
    })
    .catch(() => {});
  }

  function initAutoSave(recordId, saveUrl, csrfToken) {
    setInterval(() => autoSave(recordId, saveUrl, csrfToken, true), 30000);
    window.addEventListener('beforeunload', () => autoSave(recordId, saveUrl, csrfToken, true));
  }

  function manualSave() {
    if (!window.ABF_SAVE_URL) return;
    const btn = document.getElementById('btn-manual-save');
    if (btn) { btn.disabled = true; btn.textContent = 'Sauvegarde…'; }
    autoSave(window.ABF_RECORD_ID, window.ABF_SAVE_URL, window.ABF_CSRF_TOKEN, false);
    setTimeout(() => {
      if (btn) { btn.disabled = false; btn.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7l-4-4zm-5 16a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm3-10H5V5h10v4z"/></svg> Sauvegarder'; }
    }, 1500);
  }

  /**
   * Vide TOUS les champs de l'éditeur pour garantir un formulaire
   * 100 % vierge quand on crée un nouveau client.
   * Nécessaire car le navigateur restaure parfois les valeurs de la
   * session précédente (form-state cache / bfcache).
   */
  function clearEditorForm() {
    // 1. Tous les inputs texte/number/email/tel + textareas hors landing page
    document.querySelectorAll(
      'input[type="text"], input[type="number"], input[type="email"], ' +
      'input[type="tel"], input[type="date"], textarea'
    ).forEach(el => {
      if (!el.closest('#page-accueil')) el.value = '';
    });

    // 2. Tous les selects → première option
    document.querySelectorAll('select').forEach(el => {
      if (!el.closest('#page-accueil')) el.selectedIndex = 0;
    });

    // 3. Radios : remettre le plan à "individuel"
    const radioInd = document.querySelector('input[name="plan"][value="individuel"]');
    if (radioInd) radioInd.checked = true;
    const radioConj = document.querySelector('input[name="plan"][value="conjoint"]');
    if (radioConj) radioConj.checked = false;

    // 4. Masquer la section conjoint
    const conjSection = document.getElementById('conjoint-section');
    if (conjSection) conjSection.style.display = 'none';

    // 5. Vider les listes dynamiques
    const listIds = ['enfants-list', 'actifs-list', 'passifs-list', 'revenu-list'];
    const placeholders = {
      'enfants-list' : 'Aucun enfant ou personne à charge ajouté.',
      'actifs-list'  : 'Aucun actif ajouté.',
      'passifs-list' : 'Aucun passif ajouté.',
      'revenu-list'  : '',
    };
    listIds.forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;
      const placeholder = placeholders[id];
      if (placeholder) {
        el.classList.add('list-empty');
        el.textContent = placeholder;
      } else {
        el.innerHTML = '';
      }
    });

    // 6. Décocher tous les checkboxes/radios hors landing (sauf le plan déjà remis)
    document.querySelectorAll('input[type="checkbox"]').forEach(el => {
      if (!el.closest('#page-accueil')) el.checked = false;
    });
    document.querySelectorAll('input[type="radio"]').forEach(el => {
      if (!el.closest('#page-accueil') && el.name !== 'plan') el.checked = false;
    });
  }

  /* ── INITIALISATION LARAVEL ──────────────────────────── */

  // ── Mode landing : "Démarrer" crée le dossier en DB via AJAX,
  //    puis navigue vers l'URL du nouveau dossier (page fraîche du serveur).
  //    Navigation réelle = formulaire garanti vierge, zéro donnée fantôme.
  if (!window.ABF_RECORD_ID && window.ABF_CREATE_URL) {
    let _creating = false;
    window.demarrerABF = async function() {
      if (_creating) return;          // anti double-clic
      _creating = true;
      const btn = document.querySelector('.ia-demarrer-btn');
      if (btn) { btn.textContent = 'Création…'; btn.disabled = true; }

      try {
        const res = await fetch(window.ABF_CREATE_URL, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': window.ABF_CSRF_TOKEN,
            'Accept':       'application/json',
            'Content-Type': 'application/json',
          },
        });
        const data = await res.json();
        if (data.id && data.url) {
          // Navigation réelle → le serveur renvoie une page fraîche et vide
          window.location.href = data.url;
        } else {
          // Réponse inattendue : réactiver le bouton
          if (btn) { btn.textContent = 'Démarrer'; btn.disabled = false; }
          _creating = false;
        }
      } catch (e) {
        console.warn('[ABF] Création dossier échouée:', e);
        if (btn) { btn.textContent = 'Démarrer'; btn.disabled = false; }
        _creating = false;
      }
    };

    // ── Recherche dans la liste des parcours récents ──────
    const searchInput = document.getElementById('accueil-search');
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('.ia-accordion-body li').forEach(li => {
          const text = li.textContent.toLowerCase();
          li.style.display = (!q || text.includes(q)) ? '' : 'none';
        });
      });
    }
  }

  // ── Mode éditeur : toujours cacher page-accueil, puis restaurer données ──
  if (window.ABF_RECORD_ID) {
    document.getElementById('page-accueil').style.display = 'none';
    if (window.ABF_INITIAL_PAYLOAD && window.ABF_INITIAL_PAYLOAD.client?.prenom) {
      populateFromPayload(window.ABF_INITIAL_PAYLOAD);
    }
  }

  if (window.ABF_SAVE_URL) {
    initAutoSave(window.ABF_RECORD_ID, window.ABF_SAVE_URL, window.ABF_CSRF_TOKEN);
  }

  /* ════════════════════════════════════════════════════════
     MALADIE GRAVE
  ════════════════════════════════════════════════════════ */
  function mgInit() {
    const hasSpouse = document.querySelector('input[name="plan"][value="conjoint"]')?.checked;
    const tabs = document.getElementById('mg-person-tabs');
    if (tabs) tabs.style.display = hasSpouse ? 'block' : 'none';

    // Noms
    const cPrenom = document.getElementById('client-prenom')?.value || 'le client';
    const jPrenom = document.getElementById('conjoint-prenom')?.value || 'le conjoint';
    ['c','j'].forEach((k, i) => {
      const nom = i === 0 ? cPrenom : jPrenom;
      const el = document.getElementById(`mg-rr-client-label-${k}`);
      if (el) el.textContent = nom + ' vise';
    });

    // Revenu actuel
    ['client','conjoint'].forEach((role, i) => {
      const k = i === 0 ? 'c' : 'j';
      const brutEl = document.querySelector(`input[name="mg-use-disability-${k}"]:checked`);
      mgUpdateRevenu(role, k);
    });

    // Affichage aidant selon sélection
    ['c','j'].forEach(k => {
      const sel = document.querySelector(`input[name="mg-aidant-type-${k}"]:checked`)?.value;
      const detail = document.getElementById(`mg-aidant-detail-${k}`);
      if (detail) detail.style.display = (sel && sel !== 'aucun') ? 'block' : 'none';
    });

    mgCalc('client');
    if (hasSpouse) mgCalc('conjoint');
  }

  function mgUpdateRevenu(role, k) {
    const brutNet = document.getElementById(`mg-bn-net-${k}`)?.classList.contains('active') ? 'net' : 'brut';
    let revenu = 0;
    // Chercher le revenu dans les données saisies (revenu-epargne)
    document.querySelectorAll('#revenu-list tr[data-form-json]').forEach(tr => {
      try {
        const d = JSON.parse(tr.dataset.formJson);
        const owner = d.owner || d.proprietaire || '';
        const isOwner = (role === 'client' && (owner === 'client' || owner === '')) ||
                        (role === 'conjoint' && owner === 'conjoint');
        if (isOwner) revenu += parseFloat(String(d.montant||0).replace(/\s/g,'').replace(',','.')) || 0;
      } catch {}
    });
    const el = document.getElementById(`mg-revenu-actuel-${k}`);
    if (el) {
      const affiche = (brutNet === 'net') ? Math.round(revenu * 0.72) : revenu;
      el.textContent = affiche > 0 ? fmtMoney(affiche / 12) + '/mois' : 'Non disponible';
    }
    return revenu;
  }

  function switchMgTab(role, btn) {
    document.querySelectorAll('.deces-person-tab').forEach(b => {
      if (b.id && b.id.startsWith('mg-tab-')) b.classList.remove('active');
    });
    btn.classList.add('active');
    document.getElementById('mg-panel-client').style.display  = role === 'client'   ? '' : 'none';
    document.getElementById('mg-panel-conjoint').style.display = role === 'conjoint' ? '' : 'none';
  }

  function setMgBrutNet(role, val) {
    const k = role === 'client' ? 'c' : 'j';
    document.getElementById(`mg-bn-brut-${k}`)?.classList.toggle('active', val === 'brut');
    document.getElementById(`mg-bn-net-${k}`)?.classList.toggle('active', val === 'net');
    mgUpdateRevenu(role, k);
    mgCalc(role);
  }

  function mgSetCoverage(role, level) {
    const k = role === 'client' ? 'c' : 'j';
    const preset = _mgCoveragePresets[level] || _mgCoveragePresets.aucun;
    Object.entries(preset).forEach(([field, val]) => {
      const el = document.getElementById(`mg-dep-${field}-${k}`);
      if (el) el.value = val > 0 ? val.toLocaleString('fr-CA') : '0';
    });
    mgCalc(role);
  }

  function mgCalc(role) {
    const k = role === 'client' ? 'c' : 'j';
    const brutNet = document.getElementById(`mg-bn-net-${k}`)?.classList.contains('active') ? 'net' : 'brut';

    // Aidant detail visibility
    const aidantType = document.querySelector(`input[name="mg-aidant-type-${k}"]:checked`)?.value || 'aucun';
    const aidantDetail = document.getElementById(`mg-aidant-detail-${k}`);
    if (aidantDetail) aidantDetail.style.display = aidantType !== 'aucun' ? 'block' : 'none';

    const pct   = parseFloat(document.getElementById(`mg-rr-pct-${k}`)?.value || '70') / 100;
    const duree = parseFloat(document.getElementById(`mg-rr-duree-${k}`)?.value || '12');

    let revenuBrut = 0;
    document.querySelectorAll('#revenu-list tr[data-form-json]').forEach(tr => {
      try {
        const d = JSON.parse(tr.dataset.formJson);
        const owner = d.owner || '';
        const isOwner = (role === 'client' && (owner === 'client' || owner === '')) ||
                        (role === 'conjoint' && owner === 'conjoint');
        if (isOwner) revenuBrut += parseFloat(String(d.montant||0).replace(/\s/g,'').replace(',','.')) || 0;
      } catch {}
    });
    const revenuMensuel = (brutNet === 'net' ? revenuBrut * 0.72 : revenuBrut) / 12;
    const montantCible  = revenuMensuel * pct;

    const elMontant = document.getElementById(`mg-rr-montant-${k}`);
    if (elMontant) elMontant.textContent = fmtMoney(montantCible) + '/mois';

    // Total dépenses uniques
    const fields = ['traitement','equipement','adaptation','vehicule','transport','aide-domicile'];
    let totalUnique = 0;
    fields.forEach(f => {
      const el = document.getElementById(`mg-dep-${f}-${k}`);
      totalUnique += parseFloat(String(el?.value||'0').replace(/\s/g,'').replace(',','.')) || 0;
    });

    // Total dépenses récurrentes (pour la durée)
    const rec = ['soins-professionnel','medicaments-rec'];
    let totalRec = 0;
    rec.forEach(f => {
      const el = document.getElementById(`mg-dep-${f}-${k}`);
      totalRec += parseFloat(String(el?.value||'0').replace(/\s/g,'').replace(',','.')) || 0;
    });
    const totalDep = totalUnique + (totalRec * duree);

    const elTotal = document.getElementById(`mg-dep-total-${k}`);
    if (elTotal) elTotal.textContent = fmtMoney(totalDep);

    const totalBesoin = (montantCible * duree) + totalDep;

    // Aidant
    let aidantCout = 0;
    if (aidantType !== 'aucun') {
      const aidantMontant = parseFloat(String(document.getElementById(`mg-aidant-montant-${k}`)?.value||'0').replace(/\s/g,'').replace(',','.')) || 0;
      const aidantDuree   = parseFloat(document.getElementById(`mg-aidant-duree-${k}`)?.value || '6');
      aidantCout = aidantMontant * aidantDuree;
    }

    const grandTotal = totalBesoin + aidantCout;

    // Résumé
    const resumeEl = document.getElementById('mg-resume-body');
    if (resumeEl && document.getElementById('mg-panel-' + role)?.style.display !== 'none') {
      resumeEl.innerHTML = `
        <div style="font-size:13px">
          <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--muted)">Remplacement du revenu</span>
            <strong>${fmtMoney(montantCible * duree)}</strong>
          </div>
          <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--muted)">Dépenses supplémentaires</span>
            <strong>${fmtMoney(totalDep)}</strong>
          </div>
          ${aidantCout > 0 ? `<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--muted)">Revenu de l'aidant</span>
            <strong>${fmtMoney(aidantCout)}</strong>
          </div>` : ''}
          <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:14px">
            <span style="font-weight:700;color:var(--navy)">Total estimé</span>
            <strong style="color:var(--gold);font-size:15px">${fmtMoney(grandTotal)}</strong>
          </div>
        </div>`;
    }
  }

  /* ════════════════════════════════════════════════════════
     RETRAITE
  ════════════════════════════════════════════════════════ */
  function retraiteInit() {
    const hasSpouse = document.querySelector('input[name="plan"][value="conjoint"]')?.checked;

    const cPrenom = document.getElementById('client-prenom')?.value || 'le client';
    const jPrenom = document.getElementById('conjoint-prenom')?.value || 'le conjoint';

    // Noms dans les labels
    const nom1 = document.getElementById('retraite-nom-client');
    const nom2 = document.getElementById('retraite-nom-conjoint');
    if (nom1) nom1.textContent = cPrenom;
    if (nom2) nom2.textContent = jPrenom;

    ['retraite-nom-inline-client','retraite-nom-inline-conjoint'].forEach((id,i) => {
      const el = document.getElementById(id);
      if (el) el.textContent = i === 0 ? cPrenom : jPrenom;
    });

    // Onglets couple
    const rowConj = document.getElementById('retraite-row-conjoint');
    if (rowConj) rowConj.style.display = hasSpouse ? 'grid' : 'none';

    const tabs = document.getElementById('retraite-objectif-tabs');
    if (tabs) tabs.style.display = hasSpouse ? 'block' : 'none';

    const profilTabs = document.getElementById('retraite-profil-tabs');
    if (profilTabs) profilTabs.style.display = hasSpouse ? 'block' : 'none';

    const goalWrap = document.getElementById('retraite-goal-type-wrap');
    if (goalWrap) goalWrap.style.display = hasSpouse ? 'flex' : 'none';

    // Revenu net
    const revenu = _retraiteGetRevenu('client');
    const revenuConj = _retraiteGetRevenu('conjoint');
    const rnc = document.getElementById('retraite-revenu-net-client');
    const rnj = document.getElementById('retraite-revenu-net-conjoint');
    if (rnc) rnc.textContent = fmtMoney(revenu * 0.72);
    if (rnj) rnj.textContent = fmtMoney(revenuConj * 0.72);

    retraiteRenderDepenses();
    retraiteCalc();
  }

  function _retraiteGetRevenu(role) {
    let total = 0;
    document.querySelectorAll('#revenu-list tr[data-form-json]').forEach(tr => {
      try {
        const d = JSON.parse(tr.dataset.formJson);
        const owner = d.owner || '';
        const isOwner = (role === 'client' && (owner === 'client' || owner === '')) ||
                        (role === 'conjoint' && owner === 'conjoint');
        if (isOwner) total += parseFloat(String(d.montant||0).replace(/\s/g,'').replace(',','.')) || 0;
      } catch {}
    });
    return total;
  }

  function switchRetraiteObjTab(role, btn) {
    document.querySelectorAll('#retraite-objectif-tabs .deces-person-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('retraite-obj-panel-client').style.display  = role === 'client'   ? '' : 'none';
    document.getElementById('retraite-obj-panel-conjoint').style.display = role === 'conjoint' ? '' : 'none';
    retraiteCalc();
  }

  function switchRetraitProfileTab(role, btn) {
    document.querySelectorAll('#retraite-profil-tabs .deces-person-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('retraite-profil-panel-client').style.display  = role === 'client'   ? '' : 'none';
    document.getElementById('retraite-profil-panel-conjoint').style.display = role === 'conjoint' ? '' : 'none';
  }

  function retraiteAddPeriode(role) {
    const container = document.getElementById(`retraite-periodes-${role}`);
    if (!container) return;
    const idx = container.querySelectorAll('.retraite-periode').length;
    const nom = (role === 'client' ? document.getElementById('client-prenom')?.value : document.getElementById('conjoint-prenom')?.value) || role;
    const div = document.createElement('div');
    div.className = 'retraite-periode';
    div.dataset.idx = idx;
    div.style.cssText = 'padding:12px;background:#f8f9fd;border-radius:8px;margin-bottom:10px;position:relative';
    div.innerHTML = `
      <button onclick="this.parentElement.remove();retraiteCalc()"
        style="position:absolute;top:8px;right:8px;background:none;border:none;cursor:pointer;color:var(--muted);font-size:16px;padding:0 4px">×</button>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px">
        <span>${nom} vise</span>
        <input class="form-input" id="retraite-pct-${role}-${idx}" type="text" value="70" style="width:65px;text-align:center" oninput="retraiteCalc()"/>
        <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="retraite-target-type-${role}-${idx}" value="pct" checked onchange="retraiteCalc()"/> %</label>
        <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="retraite-target-type-${role}-${idx}" value="montant" onchange="retraiteCalc()"/> $</label>
        <span>du revenu net actuel, de l'âge</span>
        <input class="form-input" type="text" value="" placeholder="début" style="width:55px;text-align:center" oninput="retraiteCalc()"/>
        <span>à</span>
        <input class="form-input" type="text" value="" placeholder="fin" style="width:55px;text-align:center" oninput="retraiteCalc()"/>
        <span>ans</span>
      </div>`;
    container.appendChild(div);
  }

  function retraiteAddDepense() {
    document.getElementById('retraite-dep-desc').value = '';
    document.getElementById('retraite-dep-montant').value = '';
    document.getElementById('retraite-dep-debut').value = '';
    document.getElementById('retraite-dep-fin').value = '';
    document.getElementById('modal-retraite-depense').style.display = 'flex';
    delete document.getElementById('modal-retraite-depense').dataset.editIdx;
  }

  function closeRetraiteDepenseModal() {
    document.getElementById('modal-retraite-depense').style.display = 'none';
  }

  function saveRetraiteDepense() {
    const desc    = document.getElementById('retraite-dep-desc').value.trim();
    const montant = parseFloat(String(document.getElementById('retraite-dep-montant').value||'0').replace(/\s/g,'').replace(',','.')) || 0;
    const debut   = document.getElementById('retraite-dep-debut').value.trim();
    const fin     = document.getElementById('retraite-dep-fin').value.trim();
    if (!desc) { showToast('Veuillez saisir une description'); return; }
    const obj = { desc, montant, debut, fin };
    const modal = document.getElementById('modal-retraite-depense');
    if (modal.dataset.editIdx !== undefined) {
      _retraiteDepenses[parseInt(modal.dataset.editIdx)] = obj;
    } else {
      _retraiteDepenses.push(obj);
    }
    retraiteRenderDepenses();
    closeRetraiteDepenseModal();
    retraiteCalc();
  }

  function retraiteRenderDepenses() {
    const list  = document.getElementById('retraite-depenses-list');
    const empty = document.getElementById('retraite-depenses-empty');
    if (!list) return;
    if (!_retraiteDepenses.length) {
      if (empty) empty.style.display = '';
      list.innerHTML = '<p style="padding:14px;font-size:13px;color:var(--muted);margin:0">Aucune dépense ajoutée.</p>';
      return;
    }
    if (empty) empty.style.display = 'none';
    list.innerHTML = _retraiteDepenses.map((d, i) => `
      <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 16px;border-bottom:1px solid var(--border);font-size:13px">
        <div>
          <div style="font-weight:600;color:var(--navy)">${d.desc}</div>
          <div style="font-size:11px;color:var(--muted)">${d.debut||'retraite'} – ${d.fin||'décès'} ans</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
          <strong>${fmtMoney(d.montant)}/an</strong>
          <button onclick="retraiteEditDepense(${i})" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:12px;padding:2px 6px;border:1px solid var(--border);border-radius:4px">Modifier</button>
          <button onclick="_retraiteDepenses.splice(${i},1);retraiteRenderDepenses();retraiteCalc()" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:16px;padding:0 4px">×</button>
        </div>
      </div>`).join('');
  }

  function retraiteEditDepense(idx) {
    const d = _retraiteDepenses[idx];
    if (!d) return;
    document.getElementById('retraite-dep-desc').value    = d.desc    || '';
    document.getElementById('retraite-dep-montant').value = d.montant || '';
    document.getElementById('retraite-dep-debut').value   = d.debut   || '';
    document.getElementById('retraite-dep-fin').value     = d.fin     || '';
    const modal = document.getElementById('modal-retraite-depense');
    modal.dataset.editIdx = idx;
    modal.style.display = 'flex';
  }

  function retraiteToggleType(role) {
    const type     = document.getElementById(`retraite-type-${role}`)?.value || 'age';
    const ageEl    = document.getElementById(`retraite-age-${role}`);
    const anneeEl  = document.getElementById(`retraite-annee-${role}`);
    if (!ageEl || !anneeEl) return;
    if (type === 'date') {
      ageEl.style.display   = 'none';
      anneeEl.style.display = '';
    } else {
      ageEl.style.display   = '';
      anneeEl.style.display = 'none';
    }
  }

  function retraiteCalc() {
    const typeC = document.getElementById('retraite-type-client')?.value   || 'age';
    const typeJ = document.getElementById('retraite-type-conjoint')?.value || 'age';
    const ddnC  = parseInt(document.getElementById('client-naissance-annee')?.value   || '0');
    const ddnJ  = parseInt(document.getElementById('conjoint-naissance-annee')?.value || '0');

    let ageC, ageJ;
    if (typeC === 'date') {
      const anneeC = parseFloat(document.getElementById('retraite-annee-client')?.value || '0');
      ageC = (ddnC > 0 && anneeC > ddnC) ? anneeC - ddnC : 65;
    } else {
      ageC = parseFloat(document.getElementById('retraite-age-client')?.value || '65');
    }
    if (typeJ === 'date') {
      const anneeJ = parseFloat(document.getElementById('retraite-annee-conjoint')?.value || '0');
      ageJ = (ddnJ > 0 && anneeJ > ddnJ) ? anneeJ - ddnJ : 65;
    } else {
      ageJ = parseFloat(document.getElementById('retraite-age-conjoint')?.value || '65');
    }

    // Col 4 : si type=age → affiche l'année estimée ; si type=date → affiche l'âge calculé
    ['client','conjoint'].forEach((role, i) => {
      const type  = i === 0 ? typeC : typeJ;
      const age   = i === 0 ? ageC  : ageJ;
      const ddn   = i === 0 ? ddnC  : ddnJ;
      const dateEl = document.getElementById(`retraite-date-${role}`);
      if (!dateEl) return;
      if (type === 'age') {
        dateEl.textContent = ddn > 0 ? `${ddn + Math.round(age)}` : '—';
      } else {
        const anneeVal = parseFloat(document.getElementById(`retraite-annee-${role}`)?.value || '0');
        dateEl.textContent = anneeVal > 0 ? `${Math.round(age)} ans` : '—';
      }
    });

    // Résumé
    const revenuC = _retraiteGetRevenu('client') * 0.72;
    const pctC    = parseFloat(document.getElementById('retraite-pct-client-0')?.value || '70') / 100;
    const cibleC  = revenuC * pctC;
    const montantEl = document.getElementById('retraite-montant-client-0');
    if (montantEl) montantEl.textContent = fmtMoney(cibleC);

    const revenuJ = _retraiteGetRevenu('conjoint') * 0.72;
    const pctJ    = parseFloat(document.getElementById('retraite-pct-conjoint-0')?.value || '70') / 100;
    const cibleJ  = revenuJ * pctJ;
    const montantJEl = document.getElementById('retraite-montant-conjoint-0');
    if (montantJEl) montantJEl.textContent = fmtMoney(cibleJ);

    const totalDep = _retraiteDepenses.reduce((s, d) => s + (d.montant || 0), 0);

    const resumeEl = document.getElementById('retraite-resume-body');
    if (resumeEl) {
      const hasSpouse = document.querySelector('input[name="plan"][value="conjoint"]')?.checked;
      resumeEl.innerHTML = `
        <div style="font-size:13px">
          <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--muted)">Âge retraite ${document.getElementById('client-prenom')?.value||'client'}</span>
            <strong>${ageC} ans</strong>
          </div>
          ${hasSpouse ? `<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--muted)">Âge retraite ${document.getElementById('conjoint-prenom')?.value||'conjoint'}</span>
            <strong>${ageJ} ans</strong>
          </div>` : ''}
          <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--muted)">Objectif annuel</span>
            <strong>${fmtMoney(cibleC)}</strong>
          </div>
          ${totalDep > 0 ? `<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--muted)">Dépenses retraite</span>
            <strong>${fmtMoney(totalDep)}/an</strong>
          </div>` : ''}
        </div>`;
    }
  }

  /* ════════════════════════════════════════════════════════
     PROJETS
  ════════════════════════════════════════════════════════ */
  function projetsAdd() {
    document.getElementById('projet-edit-id').value = '';
    document.getElementById('projet-description').value = '';
    document.getElementById('projet-montant').value = '';
    document.getElementById('projet-mois').value = '1';
    document.getElementById('projet-annee').value = '';
    document.getElementById('projet-notes').value = '';
    document.getElementById('projet-celiapp').checked = false;
    document.getElementById('modal-projet-title').textContent = 'Nouveau projet';
    document.getElementById('projet-delete-btn').style.display = 'none';
    document.getElementById('modal-projet').style.display = 'flex';
  }

  function closeProjetModal() {
    document.getElementById('modal-projet').style.display = 'none';
  }

  function saveProjet() {
    const desc    = document.getElementById('projet-description').value.trim();
    const montant = parseFloat(String(document.getElementById('projet-montant').value||'0').replace(/\s/g,'').replace(',','.')) || 0;
    const mois    = parseInt(document.getElementById('projet-mois').value) || 1;
    const annee   = document.getElementById('projet-annee').value.trim();
    const notes   = document.getElementById('projet-notes').value.trim();
    const celiapp = document.getElementById('projet-celiapp').checked;
    if (!desc) { showToast('Veuillez saisir une description'); return; }

    const id = document.getElementById('projet-edit-id').value;
    const uid = id || (Date.now().toString(36) + Math.random().toString(36).slice(2));
    const obj = { id: uid, desc, montant, mois, annee, notes, celiapp };

    if (id) {
      const idx = _projets.findIndex(p => p.id === id);
      if (idx !== -1) _projets[idx] = obj;
    } else {
      _projets.push(obj);
    }

    projetsRender();
    closeProjetModal();
  }

  function projetsDelete() {
    const id = document.getElementById('projet-edit-id').value;
    if (!id) return;
    _projets = _projets.filter(p => p.id !== id);
    projetsRender();
    closeProjetModal();
  }

  function projetsEdit(id) {
    const p = _projets.find(x => x.id === id);
    if (!p) return;
    document.getElementById('projet-edit-id').value = p.id;
    document.getElementById('projet-description').value = p.desc || '';
    document.getElementById('projet-montant').value     = p.montant || '';
    document.getElementById('projet-mois').value        = p.mois || '1';
    document.getElementById('projet-annee').value       = p.annee || '';
    document.getElementById('projet-notes').value       = p.notes || '';
    document.getElementById('projet-celiapp').checked   = !!p.celiapp;
    document.getElementById('modal-projet-title').textContent = 'Modifier le projet';
    document.getElementById('projet-delete-btn').style.display = 'inline-flex';
    document.getElementById('modal-projet').style.display = 'flex';
  }

  function projetsRender() {
    const empty   = document.getElementById('projets-empty');
    const panels  = document.getElementById('projets-panels');
    const tabsNav = document.getElementById('projets-tabs-nav');
    if (!tabsNav || !panels) return;

    if (!_projets.length) {
      if (empty)  empty.style.display  = '';
      if (panels) panels.style.display = 'none';
      tabsNav.innerHTML = '';
      return;
    }

    if (empty)  empty.style.display  = 'none';
    if (panels) panels.style.display = '';

    let activeId = _projets[0].id;

    tabsNav.innerHTML = _projets.map((p, i) => `
      <button
        id="proj-tab-${p.id}"
        class="deces-person-tab${i === 0 ? ' active' : ''}"
        onclick="projetsShowPanel('${p.id}',this)"
        style="white-space:nowrap;max-width:160px;overflow:hidden;text-overflow:ellipsis">
        ${p.desc || 'Projet ' + (i+1)}
      </button>`).join('');

    panels.innerHTML = _projets.map((p, i) => `
      <div id="proj-panel-${p.id}" style="${i > 0 ? 'display:none' : ''}">
        <div class="card">
          <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <span>${p.desc}</span>
            <button class="btn btn-secondary btn-sm" onclick="projetsEdit('${p.id}')">Modifier</button>
          </div>
          <div class="card-body">
            ${p.celiapp ? '<div style="display:inline-block;font-size:11px;background:#dcfce7;color:#16a34a;border-radius:4px;padding:2px 8px;margin-bottom:10px;font-weight:600">✓ Admissible CELIAPP / RAP</div>' : ''}
            <div style="font-size:13px">
              <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)">
                <span style="color:var(--muted)">Date prévue</span>
                <strong>${_moisNoms[p.mois] || ''} ${p.annee || '—'}</strong>
              </div>
              <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)">
                <span style="color:var(--muted)">Montant estimé</span>
                <strong>${fmtMoney(p.montant)}</strong>
              </div>
              ${p.notes ? `<div style="padding:8px 0;color:var(--muted);font-size:12px">${p.notes}</div>` : ''}
            </div>
          </div>
        </div>
      </div>`).join('');
  }

  function projetsShowPanel(id, btn) {
    document.querySelectorAll('#projets-tabs-nav .deces-person-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    _projets.forEach(p => {
      const el = document.getElementById(`proj-panel-${p.id}`);
      if (el) el.style.display = p.id === id ? '' : 'none';
    });
  }

  /* ════════════════════════════════════════════════════════
     RECOMMANDATIONS
  ════════════════════════════════════════════════════════ */
  function recomInit() {
    const hasSpouse = document.querySelector('input[name="plan"][value="conjoint"]')?.checked;
    const cats = ['deces','invalidite','maladie-grave','fonds-urgence','retraite'];
    cats.forEach(cat => {
      const wrap = document.getElementById(`recom-person-wrap-${cat}`);
      if (wrap) wrap.style.display = hasSpouse ? 'block' : 'none';
    });
    // Restaurer les notes sauvegardées
    Object.entries(_recomNotes).forEach(([cat, text]) => {
      const el = document.getElementById(`recom-notes-${cat}`);
      if (el) el.value = text;
    });
  }

  function switchRecomTab(cat, btn) {
    document.querySelectorAll('.recom-tab').forEach(b => {
      b.style.color        = 'var(--muted)';
      b.style.borderBottom = '2px solid transparent';
    });
    btn.style.color        = 'var(--navy)';
    btn.style.borderBottom = '2px solid var(--navy)';

    const cats = ['deces','invalidite','maladie-grave','fonds-urgence','retraite','conseils'];
    cats.forEach(c => {
      const el = document.getElementById(`recom-panel-${c}`);
      if (el) el.style.display = c === cat ? '' : 'none';
    });
  }

  function switchRecomPerson(cat, role, btn) {
    document.querySelectorAll(`#recom-person-wrap-${cat} .toggle-btn`).forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    // Ici on pourrait mettre à jour les données affichées pour ce rôle
  }

  function recomSaveNotes(cat) {
    const el = document.getElementById(`recom-notes-${cat}`);
    if (el) _recomNotes[cat] = el.value;
  }

  /* ════════════════════════════════════════════════════════
     RAPPORT
  ════════════════════════════════════════════════════════ */
  function rapportInit() {
    // Nom client + conseiller
    const nomEl = document.getElementById('rapport-client-nom');
    if (nomEl) {
      const prenom = document.getElementById('client-prenom')?.value || '';
      const nom    = document.getElementById('client-nom')?.value    || '';
      nomEl.textContent = [prenom, nom].filter(Boolean).join(' ') || '—';
    }
    const consEl = document.getElementById('rapport-conseiller-nom');
    if (consEl) consEl.textContent = window.ABF_ADVISOR_NAME || '—';

    rapportUpdateCompletude();
  }

  function rapportToggleSection(sectionId) {
    // Pour les sections-parents, synchroniser l'état des sous-options
    const childrenDiv = document.getElementById(`rapport-sec-${sectionId}-children`);
    if (!childrenDiv) return;
    const parent = document.getElementById(`rapport-sec-${sectionId}`);
    const checked = parent?.checked;
    childrenDiv.querySelectorAll('input[type="checkbox"]').forEach(cb => { cb.disabled = !checked; });
  }

  function rapportFilterPhotos(filter) {
    document.querySelectorAll('.rapport-photo-item').forEach(item => {
      const cat = item.dataset.categorie || '';
      item.style.display = (filter === 'Tous' || cat === filter) ? '' : 'none';
    });
    // Mettre à jour le bouton actif
    document.querySelectorAll('#page-rapport .toggle-btn').forEach(btn => {
      btn.classList.toggle('active', btn.textContent.trim() === filter);
    });
  }

  function rapportSelectPhoto(el) {
    // Désélectionner toutes
    document.querySelectorAll('.rapport-photo-item').forEach(item => {
      item.style.borderColor = 'var(--border)';
      const check = item.querySelector('.rapport-photo-check');
      if (check) check.style.display = 'none';
    });
    // Sélectionner celle cliquée
    el.style.borderColor = 'var(--gold)';
    const check = el.querySelector('.rapport-photo-check');
    if (check) check.style.display = 'flex';
    _rapportSelectedPhoto = el.dataset.file;
  }

  function rapportUpdateCompletude() {
    const sections = [
      { id: 'infos-perso',    label: 'Infos personnelles' },
      { id: 'objectifs',      label: 'Objectifs' },
      { id: 'actifs-passifs', label: 'Actifs & passifs' },
      { id: 'revenu-epargne', label: 'Revenus & épargne' },
      { id: 'fonds-urgence',  label: "Fonds d'urgence" },
      { id: 'deces',          label: 'Décès' },
      { id: 'invalidite',     label: 'Invalidité' },
      { id: 'maladie-grave',  label: 'Maladie grave' },
      { id: 'retraite',       label: 'Retraite' },
    ];
    const done  = sections.filter(s => document.querySelector(`.nav-item[onclick*="'${s.id}'"]`)?.classList.contains('done'));
    const total = sections.length;
    const el    = document.getElementById('rapport-completude');
    if (!el) return;
    el.innerHTML = `
      <div style="margin-bottom:8px;font-weight:600;color:var(--navy)">${done.length}/${total} sections complétées</div>
      <div style="height:6px;background:#e9ecef;border-radius:3px;overflow:hidden;margin-bottom:10px">
        <div style="height:100%;background:${done.length===total?'#22c55e':'#c9a050'};width:${Math.round(done.length/total*100)}%;transition:width .3s"></div>
      </div>` +
      sections.map(s => {
        const isDone = document.querySelector(`.nav-item[onclick*="'${s.id}'"]`)?.classList.contains('done');
        return `<div style="display:flex;align-items:center;gap:6px;padding:3px 0;font-size:11px;color:${isDone?'#16a34a':'var(--muted)'}">
          <span>${isDone ? '✓' : '○'}</span> ${s.label}
        </div>`;
      }).join('');
  }

  function rapportGenerer() {
    showToast('Génération du rapport en cours…');
    // À connecter à la route PDF Laravel
  }
