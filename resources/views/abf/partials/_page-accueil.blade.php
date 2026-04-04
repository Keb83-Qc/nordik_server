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
      <div id="nouveautes-list" style="overflow-y:auto;padding:24px;display:flex;flex-direction:column;gap:16px">
        @forelse($announcements ?? [] as $ann)
          <div id="ann-{{ $ann->id }}" style="border-left:3px solid #1a2340;padding:12px 12px 12px 16px;border-radius:0 10px 10px 0;background:#f8f9fc;transition:opacity .35s,transform .35s">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:4px">
              <div style="font-weight:700;font-size:0.95rem;color:#1a2340">{{ $ann->title }}</div>
              <button
                onclick="markSeen({{ $ann->id }}, '{{ route('abf.announcement.seen', ['advisorSlug' => auth()->user()->slug ?? 'conseiller', 'id' => $ann->id]) }}')"
                style="flex-shrink:0;background:#f0f4ff;border:1px solid #d0d8ee;color:#1a2340;font-size:0.75rem;font-weight:700;border-radius:20px;padding:3px 12px;cursor:pointer;white-space:nowrap;transition:background .2s"
                onmouseover="this.style.background='#e0e8ff'" onmouseout="this.style.background='#f0f4ff'">
                Vu ✓
              </button>
            </div>
            <div style="font-size:0.78rem;color:#9aa3b5;margin-bottom:10px">
              {{ ($ann->published_at ?? $ann->created_at)->locale('fr')->isoFormat('D MMMM YYYY') }}
            </div>
            @if($ann->body)
              <div style="font-size:0.88rem;color:#444;line-height:1.65">{!! $ann->body !!}</div>
            @endif
          </div>
        @empty
          <p id="no-news-msg" style="color:#9aa3b5;font-size:0.9rem;text-align:center;margin:20px 0">Aucune nouveauté pour le moment.</p>
        @endforelse
      </div>
    </div>
  </div>

  {{-- ─── Modal Intake (lien client) ──────────────────────────────────────── --}}
  <div id="modal-intake" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.45);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:16px;width:min(520px,92vw);box-shadow:0 20px 60px rgba(0,0,0,0.2);overflow:hidden">
      <!-- Header -->
      <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid #e9ecf0;background:#1a2340">
        <div style="display:flex;align-items:center;gap:10px">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#e8b84b"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
          <span style="color:#fff;font-weight:700;font-size:1rem">Créer un lien client</span>
        </div>
        <button onclick="closeIntakeModal()" style="background:none;border:none;cursor:pointer;color:#aab;font-size:1.4rem;line-height:1">&times;</button>
      </div>
      <!-- Corps formulaire -->
      <div id="intake-form-section" style="padding:24px 22px">
        <p style="color:#555;font-size:14px;margin:0 0 18px">Renseignez optionnellement les infos du client. Un lien unique et un code d'accès seront générés. Si vous fournissez un courriel, l'invitation sera envoyée automatiquement.</p>
        <div id="intake-error" style="display:none;background:#fff0f0;border:1px solid #fcc;border-radius:8px;padding:10px 14px;color:#c00;font-size:13px;margin-bottom:14px"></div>
        <form id="intake-form" onsubmit="event.preventDefault();generateIntakeLink()">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
            <div>
              <label style="font-size:12px;font-weight:700;color:#666;display:block;margin-bottom:4px">Prénom</label>
              <input id="intake-prenom" type="text" placeholder="Jean" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;box-sizing:border-box">
            </div>
            <div>
              <label style="font-size:12px;font-weight:700;color:#666;display:block;margin-bottom:4px">Nom</label>
              <input id="intake-nom" type="text" placeholder="Dupont" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;box-sizing:border-box">
            </div>
          </div>
          <div style="margin-bottom:12px">
            <label style="font-size:12px;font-weight:700;color:#666;display:block;margin-bottom:4px">Courriel du client <span style="color:#999;font-weight:400">(pour envoi auto de l'invitation)</span></label>
            <input id="intake-email" type="email" placeholder="jean@exemple.com" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;box-sizing:border-box">
          </div>
          <div style="margin-bottom:20px">
            <label style="font-size:12px;font-weight:700;color:#666;display:block;margin-bottom:4px">Langue du formulaire</label>
            <select id="intake-locale" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;box-sizing:border-box">
              @foreach(\App\Models\Language::where('is_active',true)->orderBy('sort_order')->get() as $lang)
                <option value="{{ $lang->code }}" {{ $lang->is_default ? 'selected' : '' }}>{{ $lang->name }}</option>
              @endforeach
            </select>
          </div>
          <button id="intake-submit-btn" type="submit" style="width:100%;background:#1a2340;color:#fff;border:2px solid #e8b84b;border-radius:8px;padding:10px;font-weight:700;font-size:14px;cursor:pointer;transition:background .2s">
            Générer le lien
          </button>
        </form>
      </div>
      <!-- Résultat -->
      <div id="intake-result" style="display:none;padding:24px 22px">
        <div style="text-align:center;margin-bottom:16px">
          <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#28a745,#20c997);display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#fff"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
          </div>
          <div style="font-weight:800;color:#1a2340;font-size:1rem">Lien généré avec succès !</div>
        </div>
        <div id="intake-email-sent" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;color:#166534;font-size:13px;margin-bottom:14px;text-align:center">
          ✓ Invitation envoyée au client par courriel.
        </div>
        <!-- Lien -->
        <div style="margin-bottom:12px">
          <label style="font-size:12px;font-weight:700;color:#666;display:block;margin-bottom:6px">Lien d'accès</label>
          <div style="display:flex;align-items:center;gap:8px;background:#f8f9fa;border:1px solid #e9ecef;border-radius:8px;padding:10px 12px">
            <code id="intake-link-value" style="flex:1;font-size:12px;word-break:break-all;color:#333"></code>
            <button data-copy="intake-link-value" onclick="copyIntakeField('intake-link-value')" style="flex-shrink:0;background:#1a2340;color:#fff;border:none;border-radius:6px;padding:5px 10px;font-size:12px;cursor:pointer;font-weight:700">Copier</button>
          </div>
        </div>
        <!-- Code -->
        <div style="margin-bottom:20px">
          <label style="font-size:12px;font-weight:700;color:#666;display:block;margin-bottom:6px">Code d'accès <span style="color:#888;font-weight:400">(à envoyer au client séparément)</span></label>
          <div style="display:flex;align-items:center;gap:8px;background:#fffbeb;border:2px solid #e8b84b;border-radius:8px;padding:12px 16px">
            <code id="intake-code-value" style="flex:1;font-size:1.4rem;font-weight:900;letter-spacing:.25em;color:#1a2340;text-align:center"></code>
            <button data-copy="intake-code-value" onclick="copyIntakeField('intake-code-value')" style="flex-shrink:0;background:#1a2340;color:#fff;border:none;border-radius:6px;padding:5px 10px;font-size:12px;cursor:pointer;font-weight:700">Copier</button>
          </div>
        </div>
        <button onclick="openIntakeModal()" style="width:100%;background:transparent;color:#1a2340;border:1px solid #d1d5db;border-radius:8px;padding:9px;font-size:13px;cursor:pointer;font-weight:600">
          + Générer un autre lien
        </button>
      </div>
    </div>
  </div>

  <!-- Header iA -->
  <div class="ia-topbar">
    <img src="{{ asset('assets/vip-logo.png') }}" class="ia-logo" alt="VIP GPI"/>
    <div class="ia-topbar-right">
      <button class="ia-btn-secondary" onclick="openConfigModal('profil')">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19.14 12.94c.04-.3.06-.61.06-.94s-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
        Profil
      </button>
      <button class="ia-btn-secondary" onclick="openIntakeModal()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
        Lien client
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
                @php
                  $caseIdentifier  = $case->slug ?: 'nouveau-' . $case->id;
                  $caseAdvisorSlug = auth()->user()->slug ?? 'conseiller';
                @endphp
                <a href="{{ route('abf.editor.show', ['advisorSlug' => $caseAdvisorSlug, 'record' => $caseIdentifier]) }}"
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
    document.getElementById('modal-nouveautes').style.display = 'flex';
}
function closeNouveautes() {
    document.getElementById('modal-nouveautes').style.display = 'none';
}
document.getElementById('modal-nouveautes').addEventListener('click', function(e) {
    if (e.target === this) closeNouveautes();
});

function markSeen(id, url) {
    const el = document.getElementById('ann-' + id);
    if (!el) return;

    // Appel AJAX
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                         || '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    });

    // Retrait animé
    el.style.opacity = '0';
    el.style.transform = 'translateX(30px)';
    setTimeout(() => {
        el.remove();
        updateBadge();
    }, 350);
}

// ─── Intake Modal ────────────────────────────────────────────────────────────

function openIntakeModal() {
  document.getElementById('modal-intake').style.display = 'flex';
  document.getElementById('intake-result').style.display = 'none';
  document.getElementById('intake-form-section').style.display = '';
  document.getElementById('intake-error').style.display = 'none';
  document.getElementById('intake-form').reset();
}

function closeIntakeModal() {
  document.getElementById('modal-intake').style.display = 'none';
}

async function generateIntakeLink() {
  const btn     = document.getElementById('intake-submit-btn');
  const errEl   = document.getElementById('intake-error');
  errEl.style.display = 'none';

  const payload = {
    client_first_name: document.getElementById('intake-prenom').value.trim(),
    client_last_name:  document.getElementById('intake-nom').value.trim(),
    client_email:      document.getElementById('intake-email').value.trim(),
    locale:            document.getElementById('intake-locale').value,
    _token:            document.querySelector('meta[name=csrf-token]')?.content || window.ABF_CSRF_TOKEN,
  };

  btn.disabled    = true;
  btn.textContent = '…';

  try {
    const res = await fetch('{{ route('intake.create', ['advisorSlug' => auth()->user()->slug ?? 'conseiller']) }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': payload._token },
      body: JSON.stringify(payload),
    });

    if (!res.ok) throw new Error('Erreur serveur');

    const data = await res.json();

    document.getElementById('intake-link-value').textContent  = data.url;
    document.getElementById('intake-code-value').textContent  = data.access_code;
    document.getElementById('intake-email-sent').style.display = data.email_sent ? '' : 'none';
    document.getElementById('intake-form-section').style.display = 'none';
    document.getElementById('intake-result').style.display = '';

  } catch (e) {
    errEl.textContent = 'Erreur lors de la génération. Veuillez réessayer.';
    errEl.style.display = '';
  } finally {
    btn.disabled    = false;
    btn.textContent = 'Générer le lien';
  }
}

function copyIntakeField(id) {
  const text = document.getElementById(id)?.textContent || '';
  navigator.clipboard.writeText(text).then(() => {
    const btn = document.querySelector(`[data-copy="${id}"]`);
    if (btn) { const orig = btn.textContent; btn.textContent = '✓'; setTimeout(() => btn.textContent = orig, 2000); }
  });
}

function updateBadge() {
    const remaining = document.querySelectorAll('#nouveautes-list [id^="ann-"]').length;
    const badge     = document.querySelector('.badge-ring');
    const btn       = document.querySelector('.btn-has-news');
    const list      = document.getElementById('nouveautes-list');

    // Mettre à jour ou supprimer le badge
    if (badge) {
        if (remaining === 0) {
            badge.remove();
        } else {
            badge.textContent = remaining > 9 ? '9+' : remaining;
        }
    }

    // Supprimer les effets wow si plus rien
    if (remaining === 0 && btn) {
        btn.classList.remove('btn-has-news');
        // Afficher message vide
        if (list && !document.getElementById('no-news-msg')) {
            list.innerHTML = '<p id="no-news-msg" style="color:#9aa3b5;font-size:0.9rem;text-align:center;margin:20px 0">Aucune nouveauté pour le moment.</p>';
        }
    }
}
</script>
