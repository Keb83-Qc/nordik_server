@php
  $p   = $abfParams ?? [];
  $fmt = fn($v, $dec=2) => number_format((float)($v ?? 0), $dec, ',', ' ');
@endphp

<div id="modal-rente-conj">
  <div style="background:white;border-radius:14px;width:100%;max-width:620px;box-shadow:0 24px 64px rgba(0,0,0,.28);overflow:hidden;margin:20px;max-height:92vh;display:flex;flex-direction:column">
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
      <div>
        <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Rente de conjoint survivant — RRQ/RPC</h4>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">Montants maximaux utilisés pour les suggestions automatiques dans la section Décès</div>
      </div>
      <button onclick="closeRenteConjModal()" style="background:none;border:none;font-size:22px;color:var(--muted);cursor:pointer;padding:0 4px;line-height:1">×</button>
    </div>
    <div style="padding:20px 24px;overflow-y:auto;flex:1">
      <!-- Régime -->
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;font-size:13px">
        <span style="font-weight:600;color:var(--navy)">Régime :</span>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="radio" name="rc-regime" id="rc-regime-rrq" value="rrq" checked onchange="rcToggleRegime()"/> RRQ (Québec)</label>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="radio" name="rc-regime" id="rc-regime-cpp" value="cpp" onchange="rcToggleRegime()"/> CPP / RPC (autres provinces)</label>
      </div>
      <!-- Année + source -->
      <div id="rc-rrq-header" style="display:flex;align-items:center;gap:12px;margin-bottom:18px;background:#f0f4ff;border-radius:8px;padding:10px 14px;font-size:13px">
        <span style="color:var(--muted)">Année en vigueur :</span>
        <input class="form-input" id="rc-annee" type="text" value="{{ date('Y') }}" style="width:80px;text-align:center" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
        <span style="color:var(--muted);font-size:12px">(Source : RRQ — Montants maximaux)</span>
      </div>
      <!-- Section CPP -->
      <div id="rc-cpp-section" style="display:none;margin-bottom:18px">
        <div style="background:#f0f4ff;border-radius:8px;padding:14px;font-size:13px">
          <div style="font-weight:600;color:var(--navy);margin-bottom:10px">Paramètres CPP / RPC</div>
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
            <span style="color:var(--muted);white-space:nowrap">Portion fixe mensuelle :</span>
            <div class="input-sfx" style="max-width:130px">
              <input class="form-input" id="rc-cpp-fixed" type="text" value="{{ $fmt($p['rrq']['cpp_fixe'] ?? 217.83) }}"/>
              <span class="sfx">$</span>
            </div>
          </div>
          <div style="background:#eef2ff;border-radius:6px;padding:8px 12px;font-size:12px;color:var(--muted);line-height:1.6">
            <strong style="color:var(--navy)">Formule appliquée :</strong><br/>
            &bull; Survivant &lt; 65 ans : <em>portion fixe + 37,5 % × rente mensuelle du défunt</em><br/>
            &bull; Survivant ≥ 65 ans : <em>60 % × rente mensuelle du défunt</em>
          </div>
        </div>
      </div>
      <!-- Section RRQ -->
      <div id="rc-rrq-section">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
          <thead>
            <tr style="background:#f0f2f8;border-bottom:2px solid var(--border)">
              <th style="padding:9px 12px;text-align:left;font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px">Situation du conjoint survivant</th>
              <th style="padding:9px 12px;text-align:right;font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px">Montant max. mensuel</th>
            </tr>
          </thead>
          <tbody>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:10px 12px">Moins de 45 ans — sans enfant à charge</td>
              <td style="padding:10px 12px;text-align:right">
                <div class="input-sfx" style="max-width:130px;margin-left:auto">
                  <input class="form-input" id="rc-m45-sans" type="text" value="{{ $fmt($p['rrq']['rente_45_sans_conjoint'] ?? 719.50) }}"/>
                  <span class="sfx">$</span>
                </div>
              </td>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:10px 12px">Moins de 45 ans — avec enfant(s) à charge</td>
              <td style="padding:10px 12px;text-align:right">
                <div class="input-sfx" style="max-width:130px;margin-left:auto">
                  <input class="form-input" id="rc-m45-avec" type="text" value="{{ $fmt($p['rrq']['rente_45_avec_conjoint'] ?? 1129.95) }}"/>
                  <span class="sfx">$</span>
                </div>
              </td>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:10px 12px">Moins de 45 ans — invalide</td>
              <td style="padding:10px 12px;text-align:right">
                <div class="input-sfx" style="max-width:130px;margin-left:auto">
                  <input class="form-input" id="rc-m45-inv" type="text" value="{{ $fmt($p['rrq']['rente_45_invalidite'] ?? 1134.61) }}"/>
                  <span class="sfx">$</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div style="margin-top:14px;font-size:12px;color:var(--muted);background:#fff8e6;border-left:3px solid var(--gold);padding:8px 12px;border-radius:0 6px 6px 0">
          Ces montants sont utilisés pour suggérer automatiquement la rente de conjoint survivant lors de l'analyse Décès, selon l'âge du survivant et la présence d'enfants à charge.
        </div>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-shrink:0">
      <button onclick="rcReset()" style="background:none;border:1px solid var(--border);border-radius:6px;padding:7px 14px;font-size:13px;cursor:pointer">↻ Réinitialiser</button>
      <div style="display:flex;gap:10px">
        <button class="btn btn-secondary" onclick="closeRenteConjModal()">Annuler</button>
        <button class="btn btn-primary" onclick="saveRenteConjModal()">Enregistrer</button>
      </div>
    </div>
  </div>
</div>
