<!-- TOP BAR -->
<div class="topbar">
  <div class="topbar-logo">
    <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
      <rect width="32" height="32" rx="6" fill="#C9A050"/>
      <text x="16" y="22" text-anchor="middle" font-size="14" font-weight="800" fill="#0E1030" font-family="sans-serif">VG</text>
    </svg>
    <span>VIP GPI — ABF</span>
  </div>
  <div class="topbar-right">
    <span id="abf-topbar-date"></span>
    <button id="btn-hypotheses" onclick="openHypothesesModal()" title="Hypothèses" style="background:none;border:1px solid rgba(170,179,204,.35);border-radius:6px;color:#aab3cc;cursor:pointer;display:flex;align-items:center;gap:6px;font-size:12px;padding:5px 10px;line-height:1" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)'" onmouseout="this.style.borderColor='rgba(170,179,204,.35)';this.style.color='#aab3cc'">
      <svg viewBox="64 64 896 896" width="14" height="14" fill="currentColor"><path d="M924.8 625.7l-65.5-56c3.1-19 4.7-38.4 4.7-57.8s-1.6-38.8-4.7-57.8l65.5-56a32.03 32.03 0 0 0 9.3-35.2l-.9-2.6a443.74 443.74 0 0 0-79.7-137.9l-1.8-2.1a32.12 32.12 0 0 0-35.1-9.5l-81.3 28.9c-30-24.6-63.5-44-99.7-57.6l-15.7-85a32.05 32.05 0 0 0-25.8-25.7l-2.7-.5c-52.1-9.4-106.9-9.4-159 0l-2.7.5a32.05 32.05 0 0 0-25.8 25.7l-15.8 85.4a351.86 351.86 0 0 0-99 57.4l-81.9-29.1a32 32 0 0 0-35.1 9.5l-1.8 2.1a446.02 446.02 0 0 0-79.7 137.9l-.9 2.6c-4.5 12.5-.8 26.5 9.3 35.2l66.3 56.6c-3.1 18.8-4.6 38-4.6 57.1 0 19.2 1.5 38.4 4.6 57.1L99 625.5a32.03 32.03 0 0 0-9.3 35.2l.9 2.6c18.1 50.4 44.9 96.9 79.7 137.9l1.8 2.1a32.12 32.12 0 0 0 35.1 9.5l81.9-29.1c29.8 24.5 63.1 43.9 99 57.4l15.8 85.4a32.05 32.05 0 0 0 25.8 25.7l2.7.5a449.4 449.4 0 0 0 159 0l2.7-.5a32.05 32.05 0 0 0 25.8-25.7l15.7-85a350 350 0 0 0 99.7-57.6l81.3 28.9a32 32 0 0 0 35.1-9.5l1.8-2.1c34.8-41.1 61.6-87.5 79.7-137.9l.9-2.6c4.5-12.3.8-26.3-9.3-35zM788.3 465.9c2.5 15.1 3.8 30.6 3.8 46.1s-1.3 31-3.8 46.1l-6.6 40.1 74.7 63.9a370.03 370.03 0 0 1-42.6 73.6L721 702.8l-31.4 25.8c-23.9 19.6-50.5 35-79.3 45.8l-38.1 14.3-17.9 97a377.5 377.5 0 0 1-85 0l-17.9-97.2-37.8-14.5c-28.5-10.8-55-26.2-78.7-45.7l-31.4-25.9-93.4 33.2c-17-22.9-31.2-47.6-42.6-73.6l75.5-64.5-6.5-40c-2.4-14.9-3.7-30.3-3.7-45.5 0-15.3 1.2-30.6 3.7-45.5l6.5-40-75.5-64.5c11.3-26.1 25.6-50.7 42.6-73.6l93.4 33.2 31.4-25.9c23.7-19.5 50.2-34.9 78.7-45.7l37.9-14.3 17.9-97.2c28.1-3.2 56.8-3.2 85 0l17.9 97 38.1 14.3c28.7 10.8 55.4 26.2 79.3 45.8l31.4 25.8 92.8-32.9c17 22.9 31.2 47.6 42.6 73.6L781.8 426l6.5 39.9zM512 326c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm79.2 255.2A111.6 111.6 0 0 1 512 614c-29.9 0-58-11.7-79.2-32.8A111.6 111.6 0 0 1 400 502c0-29.9 11.7-58 32.8-79.2C454 401.6 482.1 390 512 390c29.9 0 58 11.6 79.2 32.8A111.6 111.6 0 0 1 624 502c0 29.9-11.7 58-32.8 79.2z"/></svg>
      Hypothèses
    </button>
    @if($record)
    <button id="btn-manual-save" onclick="manualSave()" title="Sauvegarder maintenant" style="background:rgba(201,160,80,.15);border:1px solid rgba(201,160,80,.5);border-radius:6px;color:var(--gold);cursor:pointer;display:flex;align-items:center;gap:6px;font-size:12px;padding:5px 10px;line-height:1;font-weight:600" onmouseover="this.style.background='rgba(201,160,80,.28)'" onmouseout="this.style.background='rgba(201,160,80,.15)'">
      <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7l-4-4zm-5 16a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm3-10H5V5h10v4z"/></svg>
      Sauvegarder
    </button>
    @endif
    <a href="{{ route('abf.landing', ['advisorSlug' => auth()->user()->slug ?? 'conseiller']) }}" style="background:none;border:1px solid rgba(170,179,204,.35);border-radius:6px;color:#aab3cc;cursor:pointer;display:flex;align-items:center;gap:6px;font-size:12px;padding:5px 10px;line-height:1;text-decoration:none" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)'" onmouseout="this.style.borderColor='rgba(170,179,204,.35)';this.style.color='#aab3cc'">
      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
      Dossiers
    </a>
    <span id="abf-topbar-user">👤 <strong>Jean Tremblay</strong></span>
  </div>
</div>

<!-- LAYOUT -->
<div class="layout">

  <!-- SIDEBAR -->
  <nav class="sidebar">
    <div class="nav-group">
      <div class="nav-group-title">
        <svg viewBox="0 0 26 24"><path d="M12 0q-2.484 0-4.676 0.938t-3.82 2.566-2.566 3.82-0.938 4.676 0.938 4.676 2.566 3.82 3.82 2.566 4.676 0.938 4.676-0.938 3.82-2.566 2.566-3.82 0.938-4.676-0.938-4.676-2.566-3.82-3.82-2.566-4.676-0.938zM10.148 17.531l-5.531-5.508 2.133-2.133 3.398 3.398 7.195-7.195 2.133 2.109-9.328 9.328z"/></svg>
        Situation actuelle
      </div>
      <button class="nav-item active" onclick="goTo('infos-perso',this)">
        <span class="dot"></span> Informations personnelles
      </button>
      <button class="nav-item locked" onclick="goTo('objectifs',this)">
        <span class="dot"></span> Objectifs
      </button>
      <button class="nav-item locked" onclick="goTo('actifs-passifs',this)">
        <span class="dot"></span> Actifs et passifs
      </button>
      <button class="nav-item locked" onclick="goTo('revenu-epargne',this)">
        <span class="dot"></span> Revenu et épargne
      </button>
      <button class="nav-item locked" onclick="goTo('fonds-urgence',this)">
        <span class="dot"></span> Fonds d'urgence
      </button>
      <button class="nav-item locked" onclick="goTo('deces',this)">
        <span class="dot"></span> Décès
      </button>
      <button class="nav-item locked" onclick="goTo('invalidite',this)">
        <span class="dot"></span> Invalidité
      </button>
      <button class="nav-item locked" onclick="goTo('maladie-grave',this)">
        <span class="dot"></span> Maladie grave
      </button>
      <button class="nav-item locked" onclick="goTo('projets',this)">
        <span class="dot"></span> Projets
      </button>
      <button class="nav-item locked" onclick="goTo('retraite',this)">
        <span class="dot"></span> Retraite
      </button>
    </div>

    <div class="nav-group">
      <div class="nav-group-title">
        <svg viewBox="0 0 26 24"><path d="M12 0q-2.484 0-4.676 0.938t-3.82 2.566-2.566 3.82-0.938 4.676 0.938 4.676 2.566 3.82 3.82 2.566 4.676 0.938 4.676-0.938 3.82-2.566 2.566-3.82 0.938-4.676-0.938-4.676-2.566-3.82-3.82-2.566-4.676-0.938zM10.148 17.531l-5.531-5.508 2.133-2.133 3.398 3.398 7.195-7.195 2.133 2.109-9.328 9.328z"/></svg>
        Résultats
      </div>
      <button class="nav-item locked" onclick="goTo('recommandations',this)">
        <span class="dot"></span> Recommandations
      </button>
      <button class="nav-item locked" onclick="goTo('rapport',this)">
        <span class="dot"></span> Rapport
      </button>
    </div>
  </nav>

  <!-- MAIN -->
  <main class="main">

