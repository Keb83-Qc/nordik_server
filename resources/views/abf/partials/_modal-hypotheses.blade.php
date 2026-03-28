@php
  $p   = $abfParams ?? [];
  $pct = fn($v, $dec=2) => number_format((float)($v ?? 0), $dec, ',', '');
@endphp

<div id="modal-hypotheses" style="display:none;position:fixed;inset:0;z-index:700;background:rgba(14,16,48,.55);align-items:center;justify-content:center">
  <div style="background:white;border-radius:14px;width:100%;max-width:520px;box-shadow:0 24px 64px rgba(0,0,0,.28);overflow:hidden;margin:20px;max-height:92vh;display:flex;flex-direction:column">
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
      <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Hypothèses pour ce parcours</h4>
      <button onclick="closeHypothesesModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;line-height:1;padding:0 4px">×</button>
    </div>
    <div style="padding:20px 24px;overflow-y:auto;flex:1">
      <!-- Reset -->
      <div style="margin-bottom:18px">
        <button class="btn btn-secondary btn-sm" onclick="resetHypotheses()" style="display:inline-flex;align-items:center;gap:6px">
          <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 26 24" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>
          Réinitialiser
        </button>
        <span style="font-size:12px;color:var(--muted);margin-left:8px">aux valeurs par défaut</span>
      </div>
      <!-- Inflation -->
      <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
        <label class="form-label" style="margin:0">Inflation</label>
        <div class="input-sfx" style="max-width:100px">
          <input class="form-input" id="hyp-inflation" type="text" value="{{ $pct($p['hypotheses']['inflation'] ?? 2.10) }}" style="text-align:right"/>
          <span class="sfx">%</span>
        </div>
      </div>
      <!-- Espérance de vie -->
      <div style="padding:12px 0;border-bottom:1px solid var(--border)">
        <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:10px">Espérance de vie</div>
        <div style="display:flex;gap:16px;flex-wrap:wrap">
          <div style="display:flex;align-items:center;gap:8px;flex:1;min-width:180px">
            <label class="form-label" style="margin:0;flex:1" id="hyp-ev-client-label">Client</label>
            <div class="input-sfx" style="max-width:90px">
              <input class="form-input" id="hyp-ev-client" type="text" value="{{ $p['hypotheses']['ev_client'] ?? 94 }}" style="text-align:right" maxlength="3" oninput="this.value=this.value.replace(/\D/g,'')"/>
              <span class="sfx">ans</span>
            </div>
          </div>
          <div id="hyp-ev-conj-wrap" style="display:flex;align-items:center;gap:8px;flex:1;min-width:180px">
            <label class="form-label" style="margin:0;flex:1" id="hyp-ev-conj-label">Conjoint(e)</label>
            <div class="input-sfx" style="max-width:90px">
              <input class="form-input" id="hyp-ev-conj" type="text" value="{{ $p['hypotheses']['ev_conjoint'] ?? 96 }}" style="text-align:right" maxlength="3" oninput="this.value=this.value.replace(/\D/g,'')"/>
              <span class="sfx">ans</span>
            </div>
          </div>
        </div>
      </div>
      <!-- Rendements portefeuilles -->
      <div style="padding:12px 0">
        <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:10px">Rendement net par portefeuille</div>
        <table style="width:100%;border-collapse:collapse;font-size:13px">
          <thead>
            <tr style="border-bottom:1px solid var(--border)">
              <th style="text-align:left;padding:6px 8px;font-weight:600;color:var(--muted);font-size:12px">Portefeuille</th>
              <th style="text-align:right;padding:6px 8px;font-weight:600;color:var(--muted);font-size:12px">Rendement net</th>
            </tr>
          </thead>
          <tbody>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:8px">Prudent</td>
              <td style="padding:8px;text-align:right"><div class="input-sfx" style="max-width:90px;margin-left:auto"><input class="form-input" id="hyp-port-prudent"    type="text" value="{{ $pct($p['portefeuilles']['prudent']    ?? 3.00) }}" style="text-align:right"/><span class="sfx">%</span></div></td>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:8px">Modéré</td>
              <td style="padding:8px;text-align:right"><div class="input-sfx" style="max-width:90px;margin-left:auto"><input class="form-input" id="hyp-port-modere"     type="text" value="{{ $pct($p['portefeuilles']['modere']     ?? 3.30) }}" style="text-align:right"/><span class="sfx">%</span></div></td>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:8px">Équilibré</td>
              <td style="padding:8px;text-align:right"><div class="input-sfx" style="max-width:90px;margin-left:auto"><input class="form-input" id="hyp-port-equilibre"  type="text" value="{{ $pct($p['portefeuilles']['equilibre']  ?? 3.70) }}" style="text-align:right"/><span class="sfx">%</span></div></td>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:8px">Croissance</td>
              <td style="padding:8px;text-align:right"><div class="input-sfx" style="max-width:90px;margin-left:auto"><input class="form-input" id="hyp-port-croissance" type="text" value="{{ $pct($p['portefeuilles']['croissance'] ?? 4.00) }}" style="text-align:right"/><span class="sfx">%</span></div></td>
            </tr>
            <tr>
              <td style="padding:8px">Audacieux</td>
              <td style="padding:8px;text-align:right"><div class="input-sfx" style="max-width:90px;margin-left:auto"><input class="form-input" id="hyp-port-audacieux"  type="text" value="{{ $pct($p['portefeuilles']['audacieux']  ?? 4.30) }}" style="text-align:right"/><span class="sfx">%</span></div></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd;flex-shrink:0">
      <button class="btn btn-secondary" onclick="closeHypothesesModal()">Annuler</button>
      <button class="btn btn-primary" onclick="saveHypotheses()">Enregistrer</button>
    </div>
  </div>
</div>
