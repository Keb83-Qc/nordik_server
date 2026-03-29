<div id="page-accueil">

@if(($announcements ?? collect())->isNotEmpty())
<style>
@keyframes bell-shake {
  0%,55%,100% { transform: rotate(0deg); }
  60%          { transform: rotate(-18deg); }
  65%          { transform: rotate(16deg); }
  70%          { transform: rotate(-12deg); }
  75%          { transform: rotate(10deg); }
  80%          { transform: rotate(-6deg); }
  85%          { transform: rotate(4deg); }
  90%          { transform: rotate(0deg); }
}
@keyframes pulse-ring {
  0%   { box-shadow: 0 0 0 0   rgba(232,184,75,.85); }
  60%  { box-shadow: 0 0 0 7px rgba(232,184,75,0);   }
  100% { box-shadow: 0 0 0 0   rgba(232,184,75,0);   }
}
@keyframes glow-btn {
  0%,100% { box-shadow: 0 0 8px rgba(232,184,75,.35), inset 0 0 0 1px rgba(232,184,75,.45); }
  50%     { box-shadow: 0 0 22px rgba(232,184,75,.75), inset 0 0 0 1px rgba(232,184,75,.8); }
}
@keyframes shimmer-sweep {
  0%   { background-position: -220% center; }
  100% { background-position: 220% center;  }
}
.btn-has-news {
  animation: glow-btn 2.2s ease-in-out infinite !important;
  background: linear-gradient(135deg,rgba(232,184,75,.18) 0%,rgba(232,184,75,.06) 100%) !important;
  overflow: hidden;
}
.btn-has-news::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(100deg,transparent 20%,rgba(255,255,255,.18) 50%,transparent 80%);
  background-size: 220% 100%;
  animation: shimmer-sweep 2.4s linear infinite;
  pointer-events: none;
}
.bell-animated { animation: bell-shake 3.5s ease-in-out infinite; transform-origin: top center; display:inline-block; }
.badge-ring     { animation: pulse-ring 1.6s ease-out infinite; }
</style>
@endif

  {{-- ─── Modal Nouveautés ───────────────────────────────────────────────── --}}
  <div id="modal-nouveautes" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.45);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:16px;width:min(600px,92vw);max-height:80vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,0.2);overflow:hidden">
      <!-- Header modal -->
      <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e9ecf0;background:#1a2340">
        <div style="display:flex;align-items:center;gap:10px">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#e8b84b"><path d="M12 22c1.1 0 2-.9 2-2h-4a2 2 0 0 0 2 2m6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1z"/></svg>
          <span style="color:#fff;font-weight:700;font-size:1rem">Nouveautés</span>
        </div>
        <button onclick="closeNouveautes()" style="background:none;border:none;cursor:pointer;color:#aab;font-size:1.4rem;line-height:1;padding:0">&times;</button>
      </div>
      <!-- Corps scrollable -->
      <div style="overflow-y:auto;padding:24px;display:flex;flex-direction:column;gap:20px">
        @forelse($announcements ?? [] as $ann)
          <div style="border-left:3px solid #1a2340;padding:0 0 0 16px">
            <div style="font-weight:700;font-size:0.95rem;color:#1a2340;margin-bottom:4px">{{ $ann->title }}</div>
            <div style="font-size:0.78rem;color:#9aa3b5;margin-bottom:10px">
              {{ ($ann->published_at ?? $ann->created_at)->locale('fr')->isoFormat('D MMMM YYYY') }}
            </div>
            @if($ann->body)
              <div style="font-size:0.88rem;color:#444;line-height:1.65">{!! $ann->body !!}</div>
            @endif
          </div>
        @empty
          <p style="color:#9aa3b5;font-size:0.9rem;text-align:center;margin:20px 0">Aucune nouveauté pour le moment.</p>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Header iA -->
  <div class="ia-topbar">
    <img src="{{ asset('assets/vip-logo.png') }}" class="ia-logo" alt="VIP GPI"/>
    <div class="ia-topbar-right">
      <button class="ia-btn-secondary" onclick="openConfigModal('profil')">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19.14 12.94c.04-.3.06-.61.06-.94s-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
        Configuration
      </button>
      @php $annCount = ($announcements ?? collect())->count(); @endphp
      <button class="ia-btn-primary {{ $annCount ? 'btn-has-news' : '' }}" onclick="openNouveautes()" style="position:relative">
        <span class="{{ $annCount ? 'bell-animated' : '' }}" style="display:inline-flex;align-items:center">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22c1.1 0 2-.9 2-2h-4a2 2 0 0 0 2 2m6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1z"/></svg>
        </span>
        Nouveautés
        @if($annCount)
          <span class="badge-ring" style="position:absolute;top:-6px;right:-6px;background:#e8b84b;color:#1a2340;font-size:10px;font-weight:900;border-radius:50%;min-width:20px;height:20px;display:flex;align-items:center;justify-content:center;line-height:1;padding:0 3px;border:2px solid #fff">
            {{ $annCount > 9 ? '9+' : $annCount }}
          </span>
        @endif
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

<script>
function openNouveautes() {
    const m = document.getElementById('modal-nouveautes');
    m.style.display = 'flex';
}
function closeNouveautes() {
    document.getElementById('modal-nouveautes').style.display = 'none';
}
document.getElementById('modal-nouveautes').addEventListener('click', function(e) {
    if (e.target === this) closeNouveautes();
});
</script>
