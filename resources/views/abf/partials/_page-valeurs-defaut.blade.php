@php
  $p = $abfParams ?? [];
  $fmt = fn($v, $dec=2) => number_format((float)($v ?? 0), $dec, ',', ' ');
  $pct = fn($v, $dec=2) => number_format((float)($v ?? 0), $dec, ',', '');
  $fuSelected  = fn($val) => ($p['fonds_urgence']['type']    ?? 'income')   === $val ? 'checked' : '';
  $dcRrSel     = fn($val) => ($p['deces']['rr_type']         ?? 'family')   === $val ? 'checked' : '';
  $dcSalSel    = fn($val) => ($p['deces']['salaire_type']    ?? 'gross')    === $val ? 'checked' : '';
  $dcFreqSel   = fn($val) => ($p['deces']['frequence']       ?? 'yearly')   === $val ? 'checked' : '';
  $invTypeSel  = fn($val) => ($p['invalidite']['type']       ?? 'incomeReplacement') === $val ? 'checked' : '';
  $invSalSel   = fn($val) => ($p['invalidite']['salaire_type'] ?? 'gross')  === $val ? 'checked' : '';
  $mgSel       = fn($val) => ($p['maladie_grave']['niveau']  ?? 'comfort')  === $val ? 'checked' : '';
  $retFreqSel  = fn($val) => ($p['retraite']['frequence']    ?? 'yearly')   === $val ? 'checked' : '';
  $retCalcSel  = fn($val) => ($p['retraite']['calcul']       ?? 'average')  === $val ? 'checked' : '';
@endphp

<div id="page-valeurs-defaut" style="display:none">
  <div class="vd-header">
    <div style="font-size:20px;font-weight:800;color:var(--navy, #1a2340)">Valeurs par défaut</div>
    <div style="display:flex;gap:8px;align-items:center">
      <span id="vd-save-status" style="font-size:12px;color:var(--muted);display:none"></span>
      <button class="btn btn-secondary" onclick="closeValeursDefaut()">Annuler</button>
      <button class="btn btn-primary" id="vd-save-btn" onclick="saveValeursDefaut()">Enregistrer</button>
    </div>
  </div>
  <div class="vd-body">
    <!-- Province -->
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

    <!-- Fonds d'urgence -->
    <div class="vd-section-title">Fonds d'urgence</div>
    <div class="vd-radio-group" style="margin-bottom:12px">
      <div class="vd-radio-pill"><input type="radio" name="vd-fu" id="vd-fu-revenu"  value="income"   {{ $fuSelected('income') }}/><label for="vd-fu-revenu">Revenu mensuel</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-fu" id="vd-fu-dep"     value="expenses" {{ $fuSelected('expenses') }}/><label for="vd-fu-dep">Dépenses mensuelles</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-fu" id="vd-fu-montant" value="amount"   {{ $fuSelected('amount') }}/><label for="vd-fu-montant">Montant fixe</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-fu" id="vd-fu-aucun"   value="none"     {{ $fuSelected('none') }}/><label for="vd-fu-aucun">Aucun</label></div>
    </div>
    <div class="vd-inline">
      <div class="input-sfx" style="max-width:80px"><input class="form-input" id="vd-fu-mois" type="text" value="{{ $p['fonds_urgence']['mois'] ?? 3 }}"/></div>
      <span style="font-size:13px;color:#4a5568">Mois</span>
    </div>
    <hr class="vd-divider"/>

    <!-- Décès -->
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
      <span style="font-size:13px;color:#4a5568">du revenu</span>
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

    <!-- Invalidité -->
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
      <span style="font-size:13px;color:#4a5568">du revenu</span>
    </div>
    <hr class="vd-divider"/>

    <!-- Maladie grave -->
    <div class="vd-section-title">Maladie grave</div>
    <div class="vd-section-subtitle">Niveau de protection</div>
    <div class="vd-radio-group">
      <div class="vd-radio-pill"><input type="radio" name="vd-mg" id="vd-mg-aucun"   value="none"    {{ $mgSel('none') }}/><label for="vd-mg-aucun">Aucun</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-mg" id="vd-mg-base"    value="base"    {{ $mgSel('base') }}/><label for="vd-mg-base">Base</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-mg" id="vd-mg-confort" value="comfort" {{ $mgSel('comfort') }}/><label for="vd-mg-confort">Confort</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-mg" id="vd-mg-sup"     value="premium" {{ $mgSel('premium') }}/><label for="vd-mg-sup">Supérieur</label></div>
    </div>
    <hr class="vd-divider"/>

    <!-- Retraite -->
    <div class="vd-section-title">Retraite</div>
    <div class="vd-section-subtitle">Objectif</div>
    <div class="vd-inline">
      <div class="input-sfx" style="max-width:90px"><input class="form-input" id="vd-ret-pct" type="text" value="{{ $p['retraite']['rr_pct'] ?? 70 }}"/><span class="sfx">%</span></div>
      <span style="font-size:13px;color:#4a5568">du revenu net</span>
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

    <!-- Inflation et rendement -->
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
    <div style="margin-top:20px;font-size:13px;color:#4a5568">
      <button style="background:none;border:1px solid var(--border);border-radius:6px;padding:7px 14px;font-size:13px;cursor:pointer;margin-right:8px" onclick="resetValeursDefaut()">↻ Réinitialiser</button>
      aux <a href="https://app.institutpf.org/?locale=fr#/guidelines" target="_blank" style="color:var(--navy, #1a2340)">normes de l'Institut de planification financière</a>
    </div>
  </div>
</div>
