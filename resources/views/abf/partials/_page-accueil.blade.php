<div id="page-accueil">
  <!-- Header iA -->
  <div class="ia-topbar">
    <img src="{{ asset('assets/vip-logo.png') }}" class="ia-logo" alt="VIP GPI"/>
    <div class="ia-topbar-right">
      <button class="ia-btn-secondary" onclick="openProfilModal()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
        Profil
      </button>
      <button class="ia-btn-secondary" onclick="openValeursDefaut()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
        Valeurs par défaut
      </button>
      <button class="ia-btn-secondary" onclick="openImpotModal()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
        Gestion de l'impôt
      </button>
      <button class="ia-btn-secondary" onclick="openRenteConjModal()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
        Rente conjoint survivant
      </button>
      <button class="ia-btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22c1.1 0 2-.9 2-2h-4a2 2 0 0 0 2 2m6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1z"/></svg>
        Nouveautés
      </button>
    </div>
  </div>
  <div class="ia-bottombar"></div>

  <!-- Corps -->
  <div class="ia-landing-body">
    <h1 class="ia-landing-title">Mon parcours financier</h1>
    <div class="ia-two-col">
      <!-- Rechercher un client -->
      <div class="ia-search-section">
        <div class="ia-field-label">Rechercher un client existant</div>
        <div class="ia-search-wrap">
          <svg class="ia-search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 26 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/></svg>
          <input type="text" placeholder="Commencez à taper le nom" id="accueil-search"/>
        </div>
      </div>
      <!-- Nouveau client -->
      <div class="ia-nouveau-section">
        <div class="ia-field-label">Nouveau client</div>
        <button class="ia-demarrer-btn" onclick="demarrerABF()">Démarrer</button>
      </div>
    </div>
    <!-- Derniers parcours -->
    <div class="ia-accordion">
      <div class="ia-accordion-header" onclick="toggleAccordion(this)">
        Derniers parcours financiers réalisés
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
      </div>
      <div class="ia-accordion-body">
        @if(isset($recentCases) && $recentCases->count())
          <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:6px">
            @foreach($recentCases as $case)
              @php
                // Nom du client — colonnes indexées en priorité, fallback payload
                $prenom = $case->client_first_name
                    ?: (data_get($case->payload, 'client.prenom', ''));
                $nom    = $case->client_last_name
                    ?: (data_get($case->payload, 'client.nom', ''));
                $clientLabel = trim(strtoupper($nom) . ' ' . ucfirst(strtolower($prenom)));
                $clientLabel = $clientLabel ?: ('Dossier #' . $case->id);

                // Conjoint
                $hasSpouse    = (bool) data_get($case->payload, 'has_spouse', false);
                $conjPrenom   = data_get($case->payload, 'conjoint.prenom', '');
                $conjNom      = data_get($case->payload, 'conjoint.nom', '');
                $conjLabel    = $hasSpouse ? trim(strtoupper($conjNom) . ' ' . ucfirst(strtolower($conjPrenom))) : '';

                // Date
                $dateRel  = $case->updated_at->locale('fr')->diffForHumans();
                $dateAbs  = $case->updated_at->locale('fr')->isoFormat('D MMM YYYY [à] H[h]mm');
              @endphp
              <li>
                @php $caseIdentifier = $case->slug ?: 'nouveau-' . $case->id; @endphp
                <a href="{{ route('abf.editor.show', ['record' => $caseIdentifier]) }}"
                   style="display:flex;justify-content:space-between;align-items:center;gap:16px;padding:10px 14px;border-radius:8px;background:#f4f6fb;color:#1a2340;text-decoration:none;transition:background .15s"
                   onmouseover="this.style.background='#e8ecf5'" onmouseout="this.style.background='#f4f6fb'">
                  <!-- Noms -->
                  <div style="display:flex;flex-direction:column;gap:2px;min-width:0">
                    <span style="font-size:14px;font-weight:700;color:#1a2340;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                      {{ $clientLabel }}
                    </span>
                    @if($conjLabel)
                      <span style="font-size:12px;color:#7a86a3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        &amp; {{ $conjLabel }}
                      </span>
                    @endif
                  </div>
                  <!-- Date -->
                  <div style="display:flex;flex-direction:column;align-items:flex-end;gap:1px;flex-shrink:0">
                    <span style="font-size:12px;color:#7a86a3" title="{{ $dateAbs }}">{{ $dateRel }}</span>
                    <span style="font-size:11px;color:#b0bac8">{{ $dateAbs }}</span>
                  </div>
                </a>
              </li>
            @endforeach
          </ul>
        @else
          <p style="color:var(--muted);font-size:14px;margin:0">Aucun parcours récent.</p>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════
     MODAL PROFIL
