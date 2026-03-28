════════════════════════════════════════════════ -->
<div id="modal-profil">
  <div style="background:white;border-radius:12px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Profil</h4>
      <button onclick="closeProfilModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
    </div>
    <div style="padding:20px 24px">
      <div class="form-group">
        <label class="form-label">Titre professionnel</label>
        <input class="form-input" id="profil-titre-fr" type="text" value="Conseiller en sécurité financière"/>
      </div>
      <div class="form-group">
        <label class="form-label">Professional title</label>
        <input class="form-input" id="profil-titre-en" type="text" value="Financial Security Advisor"/>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
      <button class="btn btn-secondary" onclick="closeProfilModal()">Annuler</button>
      <button class="btn btn-primary" onclick="saveProfilModal()">Enregistrer</button>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════
     MODAL GESTION DE L'IMPÔT
════════════════════════════════════════════════ -->
<div id="modal-impot">
  <div style="background:white;border-radius:14px;width:100%;max-width:680px;box-shadow:0 24px 64px rgba(0,0,0,.28);overflow:hidden;margin:20px;max-height:92vh;display:flex;flex-direction:column">
    <!-- Header -->
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
      <div>
        <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Paramètres fiscaux</h4>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">Québec 2026 — modifiez les taux et plafonds utilisés dans les calculs</div>
      </div>
      <button onclick="closeImpotModal()" style="background:none;border:none;font-size:22px;color:var(--muted);cursor:pointer;padding:0 4px;line-height:1">×</button>
    </div>
    <!-- Body -->
    <div style="padding:20px 24px;overflow-y:auto;flex:1" id="impot-params-body">

      <!-- Paliers fédéraux -->
      <div class="impot-section-title">Paliers d'imposition — Fédéral</div>
      <table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:4px">
        <thead><tr style="background:#f0f2f8">
          <th style="padding:6px 10px;text-align:left;font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Revenu jusqu'à</th>
          <th style="padding:6px 10px;text-align:left;font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Taux marginal (%)</th>
        </tr></thead>
        <tbody id="impot-fed-brackets"></tbody>
      </table>
      <!-- Crédit personnel fédéral -->
      <div style="background:#f8f9fd;border-radius:8px;padding:8px 14px;margin-top:8px">
        <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px">Montant personnel de base — Fédéral</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:8px">
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Montant max ($)</div>
            <input class="form-input" id="fp-fed-baseMax" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Montant min ($)</div>
            <input class="form-input" id="fp-fed-baseMin" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Seuil bas ($)</div>
            <input class="form-input" id="fp-fed-baseThreshLow" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Seuil haut ($)</div>
            <input class="form-input" id="fp-fed-baseThreshHigh" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
        </div>
        <div style="margin-top:8px">
          <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux du crédit (%)</div>
          <input class="form-input" id="fp-fed-creditRate" type="text" style="font-size:12px;padding:5px 8px;width:100px"/>
        </div>
      </div>

      <!-- Paliers québécois -->
      <div class="impot-section-title" style="margin-top:18px">Paliers d'imposition — Québec</div>
      <table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:4px">
        <thead><tr style="background:#f0f2f8">
          <th style="padding:6px 10px;text-align:left;font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Revenu jusqu'à</th>
          <th style="padding:6px 10px;text-align:left;font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Taux marginal (%)</th>
        </tr></thead>
        <tbody id="impot-qc-brackets"></tbody>
      </table>
      <!-- Crédit personnel Québec -->
      <div style="background:#f8f9fd;border-radius:8px;padding:8px 14px;margin-top:8px">
        <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px">Montant personnel de base — Québec</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Montant ($)</div>
            <input class="form-input" id="fp-qc-base" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux du crédit (%)</div>
            <input class="form-input" id="fp-qc-creditRate" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
        </div>
      </div>

      <!-- Cotisations sociales -->
      <div class="impot-section-title" style="margin-top:18px">Cotisations sociales</div>
      <div style="background:#f8f9fd;border-radius:8px;padding:8px 14px">
        <!-- RRQ -->
        <div style="font-size:11px;font-weight:700;color:var(--navy);margin-bottom:6px">RRQ</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr 1fr;gap:8px;margin-bottom:12px">
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Exemption ($)</div>
            <input class="form-input" id="fp-rrq-exemption" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Plafond 1 ($)</div>
            <input class="form-input" id="fp-rrq-ceil1" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux 1 (%)</div>
            <input class="form-input" id="fp-rrq-rate1" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Plafond 2 ($)</div>
            <input class="form-input" id="fp-rrq-ceil2" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux 2 (%)</div>
            <input class="form-input" id="fp-rrq-rate2" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
        </div>
        <!-- AE -->
        <div style="font-size:11px;font-weight:700;color:var(--navy);margin-bottom:6px">Assurance-emploi (AE)</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px">
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Plafond ($)</div>
            <input class="form-input" id="fp-ae-ceil" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux (%)</div>
            <input class="form-input" id="fp-ae-rate" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
        </div>
        <!-- RQAP -->
        <div style="font-size:11px;font-weight:700;color:var(--navy);margin-bottom:6px">RQAP</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Plafond ($)</div>
            <input class="form-input" id="fp-rqap-ceil" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux (%)</div>
            <input class="form-input" id="fp-rqap-rate" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
        </div>
      </div>

    </div>
    <!-- Footer -->
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:#f8f9fd;flex-shrink:0">
      <button class="btn btn-secondary" onclick="impotResetParams()" style="font-size:12px">Rétablir 2026</button>
      <div style="display:flex;gap:8px">
        <button class="btn btn-secondary" onclick="closeImpotModal()">Annuler</button>
        <button class="btn btn-primary" onclick="impotSaveParams()">Enregistrer</button>
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════
     PAGE VALEURS PAR DÉFAUT
════════════════════════════════════════════════ -->
