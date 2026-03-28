@php
  $isAdmin    = auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;
  $p          = $abfParams ?? [];
  $fmt        = fn($v, $dec=2) => number_format((float)($v ?? 0), $dec, ',', ' ');
  $pct        = fn($v, $dec=2) => number_format((float)($v ?? 0), $dec, ',', '');
  $fuSel      = fn($val) => ($p['fonds_urgence']['type']       ?? 'income')              === $val ? 'checked' : '';
  $dcRrSel    = fn($val) => ($p['deces']['rr_type']            ?? 'family')              === $val ? 'checked' : '';
  $dcSalSel   = fn($val) => ($p['deces']['salaire_type']       ?? 'gross')               === $val ? 'checked' : '';
  $dcFreqSel  = fn($val) => ($p['deces']['frequence']          ?? 'yearly')              === $val ? 'checked' : '';
  $invTypeSel = fn($val) => ($p['invalidite']['type']          ?? 'incomeReplacement')   === $val ? 'checked' : '';
  $invSalSel  = fn($val) => ($p['invalidite']['salaire_type']  ?? 'gross')               === $val ? 'checked' : '';
  $mgSel      = fn($val) => ($p['maladie_grave']['niveau']     ?? 'comfort')             === $val ? 'checked' : '';
  $retFreqSel = fn($val) => ($p['retraite']['frequence']       ?? 'yearly')              === $val ? 'checked' : '';
  $retCalcSel = fn($val) => ($p['retraite']['calcul']          ?? 'average')             === $val ? 'checked' : '';
  $rcFmt      = fn($v)   => number_format((float)($v ?? 0), 2, ',', ' ');
@endphp

<div id="modal-config">
  <div style="background:white;border-radius:14px;width:100%;max-width:740px;max-height:90vh;box-shadow:0 24px 64px rgba(0,0,0,.28);margin:20px;display:flex;flex-direction:column;overflow:hidden">

    {{-- ── En-tête ── --}}
    <div style="padding:16px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
      <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Configuration</h4>
      <button onclick="closeConfigModal()" style="background:none;border:none;font-size:22px;color:var(--muted);cursor:pointer;padding:0 4px;line-height:1">×</button>
    </div>

    {{-- ── Onglets ── --}}
    <div class="cfg-tabs" style="flex-shrink:0">
      <button class="cfg-tab-btn active" data-tab="profil"  onclick="switchConfigTab('profil')">Profil</button>
      <button class="cfg-tab-btn"        data-tab="valeurs" onclick="switchConfigTab('valeurs')">Valeurs par défaut</button>
      @if($isAdmin)
      <button class="cfg-tab-btn"        data-tab="impot"   onclick="switchConfigTab('impot')">Gestion de l'impôt</button>
      @endif
      <button class="cfg-tab-btn"        data-tab="rente"   onclick="switchConfigTab('rente')">Rente conjoint survivant</button>
    </div>

    {{-- ── Contenu des onglets (scrollable) ── --}}
    <div style="flex:1;overflow-y:auto;min-height:0">

      {{-- ═══ ONGLET PROFIL ═══ --}}
      <div id="cfg-tab-profil" class="cfg-tab-pane active">
        <div class="form-group">
          <label class="form-label">Titre professionnel</label>
          <input class="form-input" id="profil-titre-fr" type="text" value="Conseiller en sécurité financière" style="max-width:400px"/>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">Professional title</label>
          <input class="form-input" id="profil-titre-en" type="text" value="Financial Security Advisor" style="max-width:400px"/>
        </div>
      </div>

      {{-- ═══ ONGLET VALEURS PAR DÉFAUT ═══ --}}
      <div id="cfg-tab-valeurs" class="cfg-tab-pane">
        {{-- Province --}}
        <div class="form-group">
          <label class="form-label">Province d'imposition</label>
          <select class="form-select" id="vd-province" style="max-width:300px">
            <option>Alberta</option><option>Colombie-Britannique</option>
            <option>Île-du-Prince-Édouard</option><option>Manitoba</option>
            <option>Nouveau-Brunswick</option><option>Nouvelle-Écosse</option>
            <option>Nunavut</option><option>Ontario</option>
            <option selected>Québec</option><option>Saskatchewan</option>
            <option>Terre-Neuve-et-Labrador</option>
            <option>Territoires du Nord-Ouest</option><option>Yukon</option>
          </select>
        </div>
        <hr class="vd-divider"/>

        {{-- Fonds d'urgence --}}
        <div class="vd-section-title">Fonds d'urgence</div>
        <div class="vd-radio-group" style="margin-bottom:12px">
          <div class="vd-radio-pill"><input type="radio" name="vd-fu" id="vd-fu-revenu"  value="income"   {{ $fuSel('income') }}/><label for="vd-fu-revenu">Revenu mensuel</label></div>
          <div class="vd-radio-pill"><input type="radio" name="vd-fu" id="vd-fu-dep"     value="expenses" {{ $fuSel('expenses') }}/><label for="vd-fu-dep">Dépenses mensuelles</label></div>
          <div class="vd-radio-pill"><input type="radio" name="vd-fu" id="vd-fu-montant" value="amount"   {{ $fuSel('amount') }}/><label for="vd-fu-montant">Montant fixe</label></div>
          <div class="vd-radio-pill"><input type="radio" name="vd-fu" id="vd-fu-aucun"   value="none"     {{ $fuSel('none') }}/><label for="vd-fu-aucun">Aucun</label></div>
        </div>
        <div class="vd-inline">
          <div class="input-sfx" style="max-width:80px"><input class="form-input" id="vd-fu-mois" type="text" value="{{ $p['fonds_urgence']['mois'] ?? 3 }}"/></div>
          <span style="font-size:13px;color:var(--muted)">Mois</span>
        </div>
        <hr class="vd-divider"/>

        {{-- Décès --}}
        <div class="vd-section-title">Décès</div>
        <div class="vd-section-subtitle">Frais funéraires</div>
        <div class="input-sfx" style="max-width:160px">
          <input class="form-input" id="vd-funerailles" type="text" value="{{ $fmt($p['deces']['funerailles'] ?? 10000, 0) }}"/>
          <span class="sfx">$</span>
        </div>
        <div class="vd-section-subtitle">Remplacement du revenu</div>
        <div class="vd-inline">
          <div class="vd-radio-group">
            <div class="vd-radio-pill"><input type="radio" name="vd-deces-rr" id="vd-dc-familial" value="family"     {{ $dcRrSel('family') }}/><label for="vd-dc-familial">Familial</label></div>
            <div class="vd-radio-pill"><input type="radio" name="vd-deces-rr" id="vd-dc-indiv"    value="individual" {{ $dcRrSel('individual') }}/><label for="vd-dc-indiv">Individuel</label></div>
          </div>
          <div class="input-sfx" style="max-width:90px"><input class="form-input" id="vd-deces-pct" type="text" value="{{ $p['deces']['rr_pct'] ?? 70 }}"/><span class="sfx">%</span></div>
          <span style="font-size:13px;color:var(--muted)">du revenu</span>
        </div>
        <div class="vd-section-subtitle">Salaire</div>
        <div class="vd-inline">
          <div class="vd-radio-group">
            <div class="vd-radio-pill"><input type="radio" name="vd-deces-sal" id="vd-dc-brut" value="gross" {{ $dcSalSel('gross') }}/><label for="vd-dc-brut">Brut</label></div>
            <div class="vd-radio-pill"><input type="radio" name="vd-deces-sal" id="vd-dc-net"  value="net"   {{ $dcSalSel('net') }}/><label for="vd-dc-net">Net</label></div>
          </div>
          <div class="vd-radio-group">
            <div class="vd-radio-pill"><input type="radio" name="vd-deces-freq" id="vd-dc-annuel"  value="yearly"  {{ $dcFreqSel('yearly') }}/><label for="vd-dc-annuel">Annuel</label></div>
            <div class="vd-radio-pill"><input type="radio" name="vd-deces-freq" id="vd-dc-mensuel" value="monthly" {{ $dcFreqSel('monthly') }}/><label for="vd-dc-mensuel">Mensuel</label></div>
          </div>
        </div>
        <hr class="vd-divider"/>

        {{-- Invalidité --}}
        <div class="vd-section-title">Invalidité</div>
        <div class="vd-section-subtitle">Approche de calcul</div>
        <div class="vd-radio-group" style="margin-bottom:12px">
          <div class="vd-radio-pill"><input type="radio" name="vd-inv-type" id="vd-inv-rr"  value="incomeReplacement" {{ $invTypeSel('incomeReplacement') }}/><label for="vd-inv-rr">Remplacement du revenu</label></div>
          <div class="vd-radio-pill"><input type="radio" name="vd-inv-type" id="vd-inv-dep" value="expensesCoverage"  {{ $invTypeSel('expensesCoverage') }}/><label for="vd-inv-dep">Dépenses courantes</label></div>
        </div>
        <div class="vd-inline">
          <div class="vd-radio-group">
            <div class="vd-radio-pill"><input type="radio" name="vd-inv-sal" id="vd-inv-brut" value="gross" {{ $invSalSel('gross') }}/><label for="vd-inv-brut">Brut</label></div>
            <div class="vd-radio-pill"><input type="radio" name="vd-inv-sal" id="vd-inv-net"  value="net"   {{ $invSalSel('net') }}/><label for="vd-inv-net">Net</label></div>
          </div>
          <div class="input-sfx" style="max-width:90px"><input class="form-input" id="vd-inv-pct" type="text" value="{{ $p['invalidite']['rr_pct'] ?? 70 }}"/><span class="sfx">%</span></div>
          <span style="font-size:13px;color:var(--muted)">du revenu</span>
        </div>
        <hr class="vd-divider"/>

        {{-- Maladie grave --}}
        <div class="vd-section-title">Maladie grave</div>
        <div class="vd-section-subtitle">Niveau de protection</div>
        <div class="vd-radio-group">
          <div class="vd-radio-pill"><input type="radio" name="vd-mg" id="vd-mg-aucun"   value="none"    {{ $mgSel('none') }}/><label for="vd-mg-aucun">Aucun</label></div>
          <div class="vd-radio-pill"><input type="radio" name="vd-mg" id="vd-mg-base"    value="base"    {{ $mgSel('base') }}/><label for="vd-mg-base">Base</label></div>
          <div class="vd-radio-pill"><input type="radio" name="vd-mg" id="vd-mg-confort" value="comfort" {{ $mgSel('comfort') }}/><label for="vd-mg-confort">Confort</label></div>
          <div class="vd-radio-pill"><input type="radio" name="vd-mg" id="vd-mg-sup"     value="premium" {{ $mgSel('premium') }}/><label for="vd-mg-sup">Supérieur</label></div>
        </div>
        <hr class="vd-divider"/>

        {{-- Retraite --}}
        <div class="vd-section-title">Retraite</div>
        <div class="vd-section-subtitle">Objectif</div>
        <div class="vd-inline">
          <div class="input-sfx" style="max-width:90px"><input class="form-input" id="vd-ret-pct" type="text" value="{{ $p['retraite']['rr_pct'] ?? 70 }}"/><span class="sfx">%</span></div>
          <span style="font-size:13px;color:var(--muted)">du revenu net</span>
          <div class="vd-radio-group">
            <div class="vd-radio-pill"><input type="radio" name="vd-ret-freq" id="vd-ret-annuel"  value="yearly"  {{ $retFreqSel('yearly') }}/><label for="vd-ret-annuel">Annuel</label></div>
            <div class="vd-radio-pill"><input type="radio" name="vd-ret-freq" id="vd-ret-mensuel" value="monthly" {{ $retFreqSel('monthly') }}/><label for="vd-ret-mensuel">Mensuel</label></div>
          </div>
        </div>
        <div class="vd-section-subtitle">Approche de calcul du sommaire</div>
        <div class="vd-radio-group">
          <div class="vd-radio-pill"><input type="radio" name="vd-ret-calc" id="vd-ret-moy"   value="average" {{ $retCalcSel('average') }}/><label for="vd-ret-moy">Moyenne</label></div>
          <div class="vd-radio-pill"><input type="radio" name="vd-ret-calc" id="vd-ret-total" value="total"   {{ $retCalcSel('total') }}/><label for="vd-ret-total">Total</label></div>
        </div>
        <hr class="vd-divider"/>

        {{-- Inflation et rendement --}}
        <div class="vd-section-title">Inflation et rendement</div>
        <div class="vd-section-subtitle">Inflation</div>
        <div class="input-sfx" style="max-width:120px;margin-bottom:20px">
          <input class="form-input" id="vd-inflation" type="text" value="{{ $pct($p['hypotheses']['inflation'] ?? 2.10) }}"/>
          <span class="sfx">%</span>
        </div>
        <table class="vd-portfolio-table">
          <thead><tr><th>Portefeuille</th><th>Rendement net</th></tr></thead>
          <tbody>
            <tr><td>Prudent</td>    <td><div class="input-sfx" style="max-width:110px"><input class="form-input" id="vd-p-prudent"    type="text" value="{{ $pct($p['portefeuilles']['prudent']    ?? 3.00) }}"/><span class="sfx">%</span></div></td></tr>
            <tr><td>Modéré</td>     <td><div class="input-sfx" style="max-width:110px"><input class="form-input" id="vd-p-modere"     type="text" value="{{ $pct($p['portefeuilles']['modere']     ?? 3.30) }}"/><span class="sfx">%</span></div></td></tr>
            <tr><td>Équilibré</td>  <td><div class="input-sfx" style="max-width:110px"><input class="form-input" id="vd-p-equilibre"  type="text" value="{{ $pct($p['portefeuilles']['equilibre']  ?? 3.70) }}"/><span class="sfx">%</span></div></td></tr>
            <tr><td>Croissance</td> <td><div class="input-sfx" style="max-width:110px"><input class="form-input" id="vd-p-croissance" type="text" value="{{ $pct($p['portefeuilles']['croissance'] ?? 4.00) }}"/><span class="sfx">%</span></div></td></tr>
            <tr><td>Audacieux</td>  <td><div class="input-sfx" style="max-width:110px"><input class="form-input" id="vd-p-audacieux"  type="text" value="{{ $pct($p['portefeuilles']['audacieux']  ?? 4.30) }}"/><span class="sfx">%</span></div></td></tr>
          </tbody>
        </table>
      </div>{{-- /cfg-tab-valeurs --}}

      {{-- ═══ ONGLET GESTION DE L'IMPÔT ═══ --}}
      @if($isAdmin)
      <div id="cfg-tab-impot" class="cfg-tab-pane">
        <fieldset style="border:none;padding:0">
          <div style="font-size:11px;color:var(--muted);margin-bottom:4px">Québec 2026 — taux et plafonds utilisés dans les calculs</div>

          <div class="impot-section-title">Paliers d'imposition — Fédéral</div>
          <table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:4px">
            <thead><tr style="background:#f0f2f8">
              <th style="padding:6px 10px;text-align:left;font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Revenu jusqu'à</th>
              <th style="padding:6px 10px;text-align:left;font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Taux marginal (%)</th>
            </tr></thead>
            <tbody id="impot-fed-brackets"></tbody>
          </table>
          <div style="background:#f8f9fd;border-radius:8px;padding:8px 14px;margin-top:8px">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px">Montant personnel de base — Fédéral</div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:8px">
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Montant max ($)</div><input class="form-input" id="fp-fed-baseMax" type="text" style="font-size:12px;padding:5px 8px"/></div>
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Montant min ($)</div><input class="form-input" id="fp-fed-baseMin" type="text" style="font-size:12px;padding:5px 8px"/></div>
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Seuil bas ($)</div><input class="form-input" id="fp-fed-baseThreshLow" type="text" style="font-size:12px;padding:5px 8px"/></div>
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Seuil haut ($)</div><input class="form-input" id="fp-fed-baseThreshHigh" type="text" style="font-size:12px;padding:5px 8px"/></div>
            </div>
            <div style="margin-top:8px"><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux du crédit (%)</div><input class="form-input" id="fp-fed-creditRate" type="text" style="font-size:12px;padding:5px 8px;width:100px"/></div>
          </div>

          <div class="impot-section-title" style="margin-top:18px">Paliers d'imposition — Québec</div>
          <table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:4px">
            <thead><tr style="background:#f0f2f8">
              <th style="padding:6px 10px;text-align:left;font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Revenu jusqu'à</th>
              <th style="padding:6px 10px;text-align:left;font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Taux marginal (%)</th>
            </tr></thead>
            <tbody id="impot-qc-brackets"></tbody>
          </table>
          <div style="background:#f8f9fd;border-radius:8px;padding:8px 14px;margin-top:8px">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px">Montant personnel de base — Québec</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Montant ($)</div><input class="form-input" id="fp-qc-base" type="text" style="font-size:12px;padding:5px 8px"/></div>
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux du crédit (%)</div><input class="form-input" id="fp-qc-creditRate" type="text" style="font-size:12px;padding:5px 8px"/></div>
            </div>
          </div>

          <div class="impot-section-title" style="margin-top:18px">Cotisations sociales</div>
          <div style="background:#f8f9fd;border-radius:8px;padding:8px 14px">
            <div style="font-size:11px;font-weight:700;color:var(--navy);margin-bottom:6px">RRQ</div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr 1fr;gap:8px;margin-bottom:12px">
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Exemption ($)</div><input class="form-input" id="fp-rrq-exemption" type="text" style="font-size:12px;padding:5px 8px"/></div>
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Plafond 1 ($)</div><input class="form-input" id="fp-rrq-ceil1" type="text" style="font-size:12px;padding:5px 8px"/></div>
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux 1 (%)</div><input class="form-input" id="fp-rrq-rate1" type="text" style="font-size:12px;padding:5px 8px"/></div>
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Plafond 2 ($)</div><input class="form-input" id="fp-rrq-ceil2" type="text" style="font-size:12px;padding:5px 8px"/></div>
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux 2 (%)</div><input class="form-input" id="fp-rrq-rate2" type="text" style="font-size:12px;padding:5px 8px"/></div>
            </div>
            <div style="font-size:11px;font-weight:700;color:var(--navy);margin-bottom:6px">Assurance-emploi (AE)</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px">
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Plafond ($)</div><input class="form-input" id="fp-ae-ceil" type="text" style="font-size:12px;padding:5px 8px"/></div>
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux (%)</div><input class="form-input" id="fp-ae-rate" type="text" style="font-size:12px;padding:5px 8px"/></div>
            </div>
            <div style="font-size:11px;font-weight:700;color:var(--navy);margin-bottom:6px">RQAP</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Plafond ($)</div><input class="form-input" id="fp-rqap-ceil" type="text" style="font-size:12px;padding:5px 8px"/></div>
              <div><div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux (%)</div><input class="form-input" id="fp-rqap-rate" type="text" style="font-size:12px;padding:5px 8px"/></div>
            </div>
          </div>
        </fieldset>
      </div>{{-- /cfg-tab-impot --}}
      @endif

      {{-- ═══ ONGLET RENTE CONJOINT SURVIVANT ═══ --}}
      <div id="cfg-tab-rente" class="cfg-tab-pane">
        <div style="font-size:11px;color:var(--muted);margin-bottom:16px">Montants maximaux utilisés pour les suggestions automatiques dans la section Décès</div>
        {{-- Régime --}}
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;font-size:13px">
          <span style="font-weight:600;color:var(--navy)">Régime :</span>
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="radio" name="rc-regime" id="rc-regime-rrq" value="rrq" checked onchange="rcToggleRegime()"/> RRQ (Québec)</label>
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="radio" name="rc-regime" id="rc-regime-cpp" value="cpp" onchange="rcToggleRegime()"/> CPP / RPC (autres provinces)</label>
        </div>
        {{-- Année --}}
        <div id="rc-rrq-header" style="display:flex;align-items:center;gap:12px;margin-bottom:18px;background:#f0f4ff;border-radius:8px;padding:10px 14px;font-size:13px">
          <span style="color:var(--muted)">Année en vigueur :</span>
          <input class="form-input" id="rc-annee" type="text" value="{{ date('Y') }}" style="width:80px;text-align:center" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
          <span style="color:var(--muted);font-size:12px">(Source : RRQ — Montants maximaux)</span>
        </div>
        {{-- Section CPP --}}
        <div id="rc-cpp-section" style="display:none;margin-bottom:18px">
          <div style="background:#f0f4ff;border-radius:8px;padding:14px;font-size:13px">
            <div style="font-weight:600;color:var(--navy);margin-bottom:10px">Paramètres CPP / RPC</div>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
              <span style="color:var(--muted);white-space:nowrap">Portion fixe mensuelle :</span>
              <div class="input-sfx" style="max-width:130px">
                <input class="form-input" id="rc-cpp-fixed" type="text" value="{{ $rcFmt($p['rrq']['cpp_fixe'] ?? 217.83) }}"/>
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
        {{-- Section RRQ --}}
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
                <td style="padding:10px 12px;text-align:right"><div class="input-sfx" style="max-width:130px;margin-left:auto"><input class="form-input" id="rc-m45-sans" type="text" value="{{ $rcFmt($p['rrq']['rente_45_sans_conjoint'] ?? 719.50) }}"/><span class="sfx">$</span></div></td>
              </tr>
              <tr style="border-bottom:1px solid var(--border)">
                <td style="padding:10px 12px">Moins de 45 ans — avec enfant(s) à charge</td>
                <td style="padding:10px 12px;text-align:right"><div class="input-sfx" style="max-width:130px;margin-left:auto"><input class="form-input" id="rc-m45-avec" type="text" value="{{ $rcFmt($p['rrq']['rente_45_avec_conjoint'] ?? 1129.95) }}"/><span class="sfx">$</span></div></td>
              </tr>
              <tr style="border-bottom:1px solid var(--border)">
                <td style="padding:10px 12px">Moins de 45 ans — invalide</td>
                <td style="padding:10px 12px;text-align:right"><div class="input-sfx" style="max-width:130px;margin-left:auto"><input class="form-input" id="rc-m45-inv" type="text" value="{{ $rcFmt($p['rrq']['rente_45_invalidite'] ?? 1134.61) }}"/><span class="sfx">$</span></div></td>
              </tr>
              <tr style="border-bottom:1px solid var(--border)">
                <td style="padding:10px 12px">Entre 45 et 65 ans</td>
                <td style="padding:10px 12px;text-align:right"><div class="input-sfx" style="max-width:130px;margin-left:auto"><input class="form-input" id="rc-45-65" type="text" value="{{ $rcFmt($p['rrq']['rente_45_65'] ?? 1173.58) }}"/><span class="sfx">$</span></div></td>
              </tr>
              <tr>
                <td style="padding:10px 12px">65 ans et plus</td>
                <td style="padding:10px 12px;text-align:right"><div class="input-sfx" style="max-width:130px;margin-left:auto"><input class="form-input" id="rc-65plus" type="text" value="{{ $rcFmt($p['rrq']['rente_65_plus'] ?? 881.48) }}"/><span class="sfx">$</span></div></td>
              </tr>
            </tbody>
          </table>
          <div style="margin-top:14px;font-size:12px;color:var(--muted);background:#fff8e6;border-left:3px solid var(--gold);padding:8px 12px;border-radius:0 6px 6px 0">
            Ces montants sont utilisés pour suggérer automatiquement la rente de conjoint survivant lors de l'analyse Décès, selon l'âge du survivant et la présence d'enfants à charge.
          </div>
        </div>
      </div>{{-- /cfg-tab-rente --}}

    </div>{{-- /scrollable --}}

    {{-- ── Pieds de page par onglet ── --}}
    <div style="border-top:1px solid var(--border);background:#f8f9fd;flex-shrink:0">

      {{-- Footer Profil --}}
      <div id="cfg-footer-profil" class="cfg-footer-pane active">
        <div></div>
        <div style="display:flex;gap:8px">
          <button class="btn btn-secondary" onclick="closeConfigModal()">Annuler</button>
          <button class="btn btn-primary" onclick="saveProfilModal()">Enregistrer</button>
        </div>
      </div>

      {{-- Footer Valeurs par défaut --}}
      <div id="cfg-footer-valeurs" class="cfg-footer-pane">
        <button style="background:none;border:1px solid var(--border);border-radius:6px;padding:7px 14px;font-size:13px;cursor:pointer" onclick="resetValeursDefaut()">
          ↻ Réinitialiser aux normes IPF
        </button>
        <div style="display:flex;gap:8px;align-items:center">
          <span id="vd-save-status" style="font-size:12px;color:var(--muted);display:none"></span>
          <button class="btn btn-secondary" onclick="closeConfigModal()">Annuler</button>
          <button class="btn btn-primary" id="vd-save-btn" onclick="saveValeursDefaut()">Enregistrer</button>
        </div>
      </div>

      {{-- Footer Gestion de l'impôt (admin seulement) --}}
      @if($isAdmin)
      <div id="cfg-footer-impot" class="cfg-footer-pane">
        <button class="btn btn-secondary" onclick="impotResetParams()" style="font-size:12px">↻ Rétablir 2026</button>
        <div style="display:flex;gap:8px">
          <button class="btn btn-secondary" onclick="closeConfigModal()">Annuler</button>
          <button class="btn btn-primary" onclick="impotSaveParams()">Enregistrer</button>
        </div>
      </div>
      @endif

      {{-- Footer Rente conjoint survivant --}}
      <div id="cfg-footer-rente" class="cfg-footer-pane">
        <button style="background:none;border:1px solid var(--border);border-radius:6px;padding:7px 14px;font-size:13px;cursor:pointer" onclick="rcReset()">↻ Réinitialiser</button>
        <div style="display:flex;gap:8px">
          <button class="btn btn-secondary" onclick="closeConfigModal()">Annuler</button>
          <button class="btn btn-primary" onclick="saveRenteConjModal()">Enregistrer</button>
        </div>
      </div>

    </div>{{-- /footers --}}
  </div>
</div>
