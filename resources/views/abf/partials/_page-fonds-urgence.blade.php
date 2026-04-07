@php
  $_fu     = $abfParams['fonds_urgence'] ?? [];
  $_fuType = $_fu['type'] ?? 'income';
  $_fuMois = $_fu['mois'] ?? 3;
  $_fuCk   = fn($v) => $_fuType === $v ? 'checked' : '';
@endphp
    <!-- ── PAGE: Fonds d'urgence ── -->
    <div id="page-fonds-urgence" class="page">
      <div class="page-title">Fonds d'urgence</div>

      <!-- Mode toggle: Familial / Individuel -->
      <div style="display:inline-flex;border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:20px">
        <button id="fu-btn-familial" onclick="fuModeChange('familial')"
          style="padding:8px 22px;font-size:13px;font-weight:600;border:none;cursor:pointer;background:var(--navy);color:white;transition:all .15s">
          Familial
        </button>
        <button id="fu-btn-individuel" onclick="fuModeChange('individuel')"
          style="padding:8px 22px;font-size:13px;font-weight:600;border:none;cursor:pointer;background:white;color:var(--muted);transition:all .15s">
          Individuel
        </button>
      </div>

      <div style="display:flex;gap:20px;align-items:start">
        <!-- Colonne gauche -->
        <div style="flex:1;min-width:0">

          <!-- Section Objectif (partagée) -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Objectif</div>
            <div class="card-body">

              <!-- Type de calcul -->
              <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
                <label class="fu-radio-pill"><input type="radio" name="fu-type" value="income"   {{ $_fuCk('income') }}   onchange="fuTypeChange()"/> Revenu mensuel</label>
                <label class="fu-radio-pill"><input type="radio" name="fu-type" value="expenses" {{ $_fuCk('expenses') }} onchange="fuTypeChange()"/> Dépenses mensuelles</label>
                <label class="fu-radio-pill"><input type="radio" name="fu-type" value="amount"   {{ $_fuCk('amount') }}   onchange="fuTypeChange()"/> Montant fixe</label>
                <label class="fu-radio-pill"><input type="radio" name="fu-type" value="none"     {{ $_fuCk('none') }}     onchange="fuTypeChange()"/> Aucun</label>
              </div>

              <!-- === INCOME === -->
              <div id="fu-row-income" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <input class="form-input" id="fu-months" type="text" value="{{ $_fuMois }}" style="width:60px;text-align:center" oninput="fuCalc()"/>
                <span id="fu-income-label-familial" style="font-size:13px;color:var(--text)">
                  mois de revenu familial net, correspondant à <strong id="fu-montant-cible-income">0 $</strong>
                </span>
                <span id="fu-income-label-individuel" style="display:none;font-size:13px;color:var(--text)">
                  mois de revenu net —
                  <strong id="fu-income-cible-c">0 $</strong> <span id="fu-income-name-c" style="color:var(--muted)">Client</span>
                  <span id="fu-income-conj-part" style="display:none">
                    / <strong id="fu-income-cible-j">0 $</strong> <span id="fu-income-name-j" style="color:var(--muted)">Conjoint</span>
                  </span>
                </span>
              </div>

              <!-- === EXPENSES familial === -->
              <div id="fu-row-expenses-familial" style="display:none;align-items:center;gap:10px;flex-wrap:wrap">
                <div class="input-sfx" style="max-width:160px">
                  <input class="form-input" id="fu-dep-mensuel" type="text" placeholder="0" oninput="fuCalc()"/>
                  <span class="sfx">$</span>
                </div>
                <span style="font-size:13px;color:var(--muted)">/mois ×</span>
                <input class="form-input" id="fu-months-dep" type="text" value="{{ $_fuMois }}" style="width:60px;text-align:center" oninput="fuCalc()"/>
                <span style="font-size:13px;color:var(--text)">mois = <strong id="fu-montant-cible-dep">0 $</strong></span>
              </div>

              <!-- === EXPENSES individuel === -->
              <div id="fu-row-expenses-individuel" style="display:none;gap:16px">
                <div style="flex:1">
                  <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px" id="fu-exp-label-c">Client</div>
                  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <div class="input-sfx" style="max-width:140px">
                      <input class="form-input" id="fu-dep-mensuel-c" type="text" placeholder="0" oninput="fuCalc()"/>
                      <span class="sfx">$</span>
                    </div>
                    <span style="font-size:13px;color:var(--muted)">/mois ×</span>
                    <input class="form-input" id="fu-months-dep-c" type="text" value="{{ $_fuMois }}" style="width:55px;text-align:center" oninput="fuCalc()"/>
                    <span style="font-size:13px;color:var(--text)">= <strong id="fu-montant-cible-dep-c">0 $</strong></span>
                  </div>
                </div>
                <div style="flex:1" id="fu-exp-conjoint-wrap">
                  <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px" id="fu-exp-label-j">Conjoint</div>
                  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <div class="input-sfx" style="max-width:140px">
                      <input class="form-input" id="fu-dep-mensuel-j" type="text" placeholder="0" oninput="fuCalc()"/>
                      <span class="sfx">$</span>
                    </div>
                    <span style="font-size:13px;color:var(--muted)">/mois ×</span>
                    <input class="form-input" id="fu-months-dep-j" type="text" value="{{ $_fuMois }}" style="width:55px;text-align:center" oninput="fuCalc()"/>
                    <span style="font-size:13px;color:var(--text)">= <strong id="fu-montant-cible-dep-j">0 $</strong></span>
                  </div>
                </div>
              </div>

              <!-- === AMOUNT familial === -->
              <div id="fu-row-amount-familial" style="display:none;align-items:center;gap:10px">
                <div class="input-sfx" style="max-width:180px">
                  <input class="form-input" id="fu-montant-fixe" type="text" placeholder="0" oninput="fuCalc()"/>
                  <span class="sfx">$</span>
                </div>
              </div>

              <!-- === AMOUNT individuel === -->
              <div id="fu-row-amount-individuel" style="display:none;gap:16px">
                <div style="flex:1">
                  <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px" id="fu-amt-label-c">Client</div>
                  <div class="input-sfx" style="max-width:160px">
                    <input class="form-input" id="fu-montant-fixe-c" type="text" placeholder="0" oninput="fuCalc()"/>
                    <span class="sfx">$</span>
                  </div>
                </div>
                <div style="flex:1" id="fu-amt-conjoint-wrap">
                  <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px" id="fu-amt-label-j">Conjoint</div>
                  <div class="input-sfx" style="max-width:160px">
                    <input class="form-input" id="fu-montant-fixe-j" type="text" placeholder="0" oninput="fuCalc()"/>
                    <span class="sfx">$</span>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- ══════════ MODE FAMILIAL ══════════ -->
          <div id="fu-mode-familial-content">

            <div class="card" style="margin-bottom:16px">
              <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Actifs alloués au fonds d'urgence</div>
              <div class="card-body" id="fu-actifs-body"></div>
            </div>

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

          </div><!-- /familial -->

          <!-- ══════════ MODE INDIVIDUEL ══════════ -->
          <div id="fu-mode-individuel-content" style="display:none">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

              <!-- ── Colonne Client ── -->
              <div>
                <div style="font-size:11px;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;padding:7px 12px;background:#eef0f8;border-radius:6px" id="fu-ind-title-c">Client</div>
                <div class="card" style="margin-bottom:12px">
                  <div class="card-header" style="font-weight:700;font-size:13px;padding:10px 14px;border-bottom:1px solid var(--border)">Actifs alloués</div>
                  <div id="fu-actifs-body-c"></div>
                </div>
                <div class="card" style="margin-bottom:12px">
                  <div class="card-header" style="font-weight:700;font-size:13px;padding:10px 14px;border-bottom:1px solid var(--border)">Marge de crédit</div>
                  <div class="card-body">
                    <div class="input-sfx">
                      <input class="form-input" id="fu-marge-c" type="text" value="0" oninput="fuCalc()"/>
                      <span class="sfx">$</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ── Colonne Conjoint ── -->
              <div id="fu-ind-conjoint-col">
                <div style="font-size:11px;font-weight:700;color:#b07c10;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;padding:7px 12px;background:#fdf8ee;border-radius:6px" id="fu-ind-title-j">Conjoint</div>
                <div class="card" style="margin-bottom:12px">
                  <div class="card-header" style="font-weight:700;font-size:13px;padding:10px 14px;border-bottom:1px solid var(--border)">Actifs alloués</div>
                  <div id="fu-actifs-body-j"></div>
                </div>
                <div class="card" style="margin-bottom:12px">
                  <div class="card-header" style="font-weight:700;font-size:13px;padding:10px 14px;border-bottom:1px solid var(--border)">Marge de crédit</div>
                  <div class="card-body">
                    <div class="input-sfx">
                      <input class="form-input" id="fu-marge-j" type="text" value="0" oninput="fuCalc()"/>
                      <span class="sfx">$</span>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div><!-- /individuel -->

        </div><!-- /col gauche -->

        <!-- Colonne droite: Résumé sticky -->
        <div style="width:260px;flex-shrink:0;position:sticky;top:80px">
          <div class="card" id="fu-resume-card">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)" id="fu-resume-title">Résumé</div>

            <!-- ── Résumé familial ── -->
            <div id="fu-resume-familial">
              <table style="width:100%;border-collapse:collapse;font-size:13px" class="card-body" style="padding:0">
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
              <div style="padding:12px 14px;border-top:1px solid var(--border)">
                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);margin-bottom:6px">
                  <span>Couverture</span><span id="fu-r-pct">0 %</span>
                </div>
                <div style="height:10px;background:#e5e7eb;border-radius:5px;overflow:hidden">
                  <div id="fu-r-bar" style="height:100%;width:0%;border-radius:5px;transition:width .4s,background .4s"></div>
                </div>
              </div>
            </div>

            <!-- ── Résumé individuel ── -->
            <div id="fu-resume-individuel" style="display:none">
              <table style="width:100%;border-collapse:collapse;font-size:12px">
                <thead>
                  <tr style="background:#f8f9fb;border-bottom:1px solid var(--border)">
                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px"></th>
                    <th style="padding:8px 10px;text-align:right;font-size:10px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px">Objectif</th>
                    <th style="padding:8px 10px;text-align:right;font-size:10px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px">Couvert</th>
                    <th style="padding:8px 10px;text-align:right;font-size:10px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px">Écart</th>
                  </tr>
                </thead>
                <tbody>
                  <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:9px 10px;font-weight:600;color:var(--navy)" id="fu-ri-label-c">Client</td>
                    <td style="padding:9px 10px;text-align:right;font-size:11px" id="fu-ri-obj-c">—</td>
                    <td style="padding:9px 10px;text-align:right;font-size:11px" id="fu-ri-couv-c">0 $</td>
                    <td style="padding:9px 10px;text-align:right;font-weight:700" id="fu-ri-ecart-c">—</td>
                  </tr>
                  <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:9px 10px;font-weight:600;color:#b07c10" id="fu-ri-label-j">Conjoint</td>
                    <td style="padding:9px 10px;text-align:right;font-size:11px" id="fu-ri-obj-j">—</td>
                    <td style="padding:9px 10px;text-align:right;font-size:11px" id="fu-ri-couv-j">0 $</td>
                    <td style="padding:9px 10px;text-align:right;font-weight:700" id="fu-ri-ecart-j">—</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 10px;font-weight:700;font-size:11px">Total</td>
                    <td style="padding:9px 10px;text-align:right;font-weight:600;font-size:11px" id="fu-ri-obj-total">—</td>
                    <td style="padding:9px 10px;text-align:right;font-weight:600;font-size:11px" id="fu-ri-couv-total">0 $</td>
                    <td style="padding:9px 10px;text-align:right;font-weight:700;font-size:14px" id="fu-ri-ecart-total">—</td>
                  </tr>
                </tbody>
              </table>
              <div style="padding:12px 14px;border-top:1px solid var(--border)">
                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);margin-bottom:6px">
                  <span>Couverture totale</span><span id="fu-ri-pct">0 %</span>
                </div>
                <div style="height:10px;background:#e5e7eb;border-radius:5px;overflow:hidden">
                  <div id="fu-ri-bar" style="height:100%;width:0%;border-radius:5px;transition:width .4s,background .4s"></div>
                </div>
              </div>
            </div>

          </div>
        </div><!-- /col droite -->

      </div><!-- /flex row -->
    </div><!-- /page-fonds-urgence -->
