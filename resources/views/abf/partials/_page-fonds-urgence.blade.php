    <!-- ── PAGE: Fonds d'urgence ── -->
    <div id="page-fonds-urgence" class="page">
      <div class="page-title">Fonds d'urgence</div>

      <div style="display:flex;gap:20px;align-items:start">
        <!-- Colonne gauche -->
        <div style="flex:1;min-width:0">

          <!-- Section Objectif -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Objectif</div>
            <div class="card-body">
              <!-- Radio type -->
              <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
                <label class="fu-radio-pill"><input type="radio" name="fu-type" value="income" checked onchange="fuTypeChange()"/> Revenu mensuel</label>
                <label class="fu-radio-pill"><input type="radio" name="fu-type" value="expenses" onchange="fuTypeChange()"/> Dépenses mensuelles</label>
                <label class="fu-radio-pill"><input type="radio" name="fu-type" value="amount" onchange="fuTypeChange()"/> Montant fixe</label>
                <label class="fu-radio-pill"><input type="radio" name="fu-type" value="none" onchange="fuTypeChange()"/> Aucun</label>
              </div>

              <!-- Revenu mensuel: mois seulement -->
              <div id="fu-row-income" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <input class="form-input" id="fu-months" type="text" value="3" style="width:60px;text-align:center" oninput="fuCalc()"/>
                <span style="font-size:13px;color:var(--text)">mois de revenu familial net, correspondant à <strong id="fu-montant-cible-income">0 $</strong></span>
              </div>

              <!-- Dépenses mensuelles: montant + mois -->
              <div id="fu-row-expenses" style="display:none;align-items:center;gap:10px;flex-wrap:wrap">
                <div class="input-sfx" style="max-width:160px">
                  <input class="form-input" id="fu-dep-mensuel" type="text" placeholder="0" oninput="fuCalc()"/>
                  <span class="sfx">$</span>
                </div>
                <span style="font-size:13px;color:var(--muted)">/mois ×</span>
                <input class="form-input" id="fu-months-dep" type="text" value="3" style="width:60px;text-align:center" oninput="fuCalc()"/>
                <span style="font-size:13px;color:var(--text)">mois, correspondant à <strong id="fu-montant-cible-dep">0 $</strong></span>
              </div>

              <!-- Montant fixe -->
              <div id="fu-row-amount" style="display:none;align-items:center;gap:10px">
                <div class="input-sfx" style="max-width:180px">
                  <input class="form-input" id="fu-montant-fixe" type="text" placeholder="0" oninput="fuCalc()"/>
                  <span class="sfx">$</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Section Actifs alloués -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Actifs alloués au fonds d'urgence</div>
            <div class="card-body" id="fu-actifs-body"></div>
          </div>

          <!-- Section Marge de crédit -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Marge de crédit</div>
            <div class="card-body">
              <div class="form-group" style="max-width:220px">
                <label class="form-label">Montant disponible</label>
                <div class="input-sfx">
                  <input class="form-input" id="fu-marge" type="text" value="0" oninput="fuCalc()"/>
                  <span class="sfx">$</span>
                </div>
              </div>
            </div>
          </div>

        </div><!-- /col gauche -->

        <!-- Colonne droite: Résumé sticky -->
        <div style="width:260px;flex-shrink:0;position:sticky;top:80px">
          <div class="card" id="fu-resume-card">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Résumé</div>
            <div class="card-body" style="padding:0">
              <table style="width:100%;border-collapse:collapse;font-size:13px">
                <tr style="border-bottom:1px solid var(--border)">
                  <td style="padding:10px 14px;color:var(--muted)">Objectif</td>
                  <td style="padding:10px 14px;text-align:right;font-weight:600" id="fu-r-objectif">—</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border)">
                  <td style="padding:10px 14px;color:var(--muted)">Actifs alloués</td>
                  <td style="padding:10px 14px;text-align:right;font-weight:600" id="fu-r-actifs">0 $</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border)">
                  <td style="padding:10px 14px;color:var(--muted)">Marge de crédit</td>
                  <td style="padding:10px 14px;text-align:right;font-weight:600" id="fu-r-marge">0 $</td>
                </tr>
                <tr>
                  <td style="padding:10px 14px;font-weight:700">Écart</td>
                  <td style="padding:10px 14px;text-align:right;font-weight:700;font-size:15px" id="fu-r-ecart">—</td>
                </tr>
              </table>
              <!-- Barre de couverture -->
              <div style="padding:12px 14px;border-top:1px solid var(--border)">
                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);margin-bottom:6px">
                  <span>Couverture</span><span id="fu-r-pct">0 %</span>
                </div>
                <div style="height:10px;background:#e5e7eb;border-radius:5px;overflow:hidden">
                  <div id="fu-r-bar" style="height:100%;width:0%;border-radius:5px;transition:width .4s,background .4s"></div>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /col droite -->

      </div><!-- /flex row -->
    </div><!-- /page-fonds-urgence -->

