    <!-- ── PAGE: Profil d'investisseur ── -->
    <div id="page-profil-investisseur" class="page">
      <div class="page-title">Profil d'investisseur</div>

      <style>
        /* ── Onglets client/conjoint ── */
        #pi-person-tabs { margin:0 0 20px;border-bottom:2px solid var(--border); }
        .pi-person-tab { background:none;border:none;border-bottom:3px solid transparent;padding:10px 18px;font-size:13px;font-weight:600;cursor:pointer;color:var(--muted);transition:all .15s;margin-bottom:-2px; }
        .pi-person-tab.active { border-bottom-color:var(--navy);color:var(--navy); }

        /* ── Section de question ── */
        .pi-section-header { background:var(--navy);color:#fff;font-size:13px;font-weight:700;padding:9px 16px;border-radius:8px 8px 0 0;margin-top:16px; }
        .pi-questions { border:1px solid var(--border);border-top:none;border-radius:0 0 8px 8px;overflow:hidden;background:var(--surface); }
        .pi-question-row { border-bottom:1px solid var(--border);padding:12px 16px; }
        .pi-question-row:last-child { border-bottom:none; }
        .pi-question-label { font-size:13px;font-weight:600;color:var(--text);margin-bottom:10px; }
        .pi-question-note { font-size:11px;color:var(--muted);margin-top:4px;font-style:italic; }

        /* ── Option radio ── */
        .pi-option { display:flex;align-items:flex-start;gap:10px;padding:7px 10px;border-radius:6px;cursor:pointer;transition:background .12s;width:100%; }
        .pi-option:hover { background:rgba(var(--navy-rgb,14,16,48),.05); }
        .pi-option input[type=radio] { margin-top:2px;accent-color:var(--navy);flex-shrink:0; }
        .pi-option-text { flex:1;font-size:13px;color:var(--text);line-height:1.45; }
        .pi-option-pts { font-size:12px;font-weight:700;color:var(--muted);white-space:nowrap;min-width:60px;text-align:right;padding-top:2px; }
        .pi-option:has(input:checked) { background:rgba(var(--navy-rgb,14,16,48),.06); }
        .pi-option:has(input:checked) .pi-option-text { font-weight:600;color:var(--navy); }
        .pi-option:has(input:checked) .pi-option-pts { color:var(--navy); }

        /* ── Résultat ── */
        .pi-result-card { margin-top:20px;background:var(--surface);border:1px solid var(--border);border-radius:8px;overflow:hidden; }
        .pi-result-header { background:var(--navy);color:#fff;font-size:13px;font-weight:700;padding:9px 16px; }
        .pi-result-body { display:flex;align-items:center;gap:24px;padding:16px 20px;flex-wrap:wrap; }
        .pi-result-score { font-size:28px;font-weight:800;color:var(--navy);line-height:1; }
        .pi-result-score span { font-size:14px;font-weight:400;color:var(--muted); }
        .pi-result-profil { font-size:18px;font-weight:700; }
        .pi-badge-prudent    { color:#2563eb; }
        .pi-badge-modere     { color:#059669; }
        .pi-badge-equilibre  { color:#d97706; }
        .pi-badge-croissance { color:#dc2626; }
        .pi-badge-audacieux  { color:#7c3aed; }
        .pi-result-grille { font-size:11px;color:var(--muted);line-height:1.8;margin-left:auto; }
      </style>

      <!-- Onglets Client / Conjoint (masqués si individuel) -->
      <div id="pi-person-tabs" style="display:none">
        <button class="pi-person-tab active" id="pi-tab-client"   onclick="switchPiTab('client',this)">CLIENT</button>
        <button class="pi-person-tab"        id="pi-tab-conjoint" onclick="switchPiTab('conjoint',this)">CONJOINT</button>
      </div>

      <!-- ───────────── PANEL CLIENT ───────────── -->
      <div id="pi-panel-client">
        @include('abf.partials._profil-investisseur-questions', ['role' => 'client'])
      </div>

      <!-- ───────────── PANEL CONJOINT ───────────── -->
      <div id="pi-panel-conjoint" style="display:none">
        @include('abf.partials._profil-investisseur-questions', ['role' => 'conjoint'])
      </div>

    </div>
