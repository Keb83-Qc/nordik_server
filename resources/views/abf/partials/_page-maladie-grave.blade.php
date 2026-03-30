@php
  $_mg     = $abfParams['maladieGrave'] ?? [];
  $_mgCk   = fn($v, $f) => ($f === $v) ? 'checked' : '';
@endphp
<!-- ── PAGE: Maladie grave ── -->
<div id="page-maladie-grave" class="page">
  <div class="page-title">Maladie grave</div>
  <div class="page-subtitle">Analyse des besoins en cas de maladie grave</div>

  <div style="display:flex;gap:20px;align-items:start">

    <!-- Colonne principale -->
    <div style="flex:1;min-width:0">

      <!-- Onglets client / conjoint -->
      <div id="mg-person-tabs" style="display:none;border-bottom:1px solid var(--border);margin-bottom:16px">
        <button class="deces-person-tab active" id="mg-tab-client" onclick="switchMgTab('client',this)">CLIENT</button>
        <button class="deces-person-tab" id="mg-tab-conjoint" onclick="switchMgTab('conjoint',this)">CONJOINT</button>
      </div>

      <!-- ══ PANEL CLIENT ══ -->
      <div id="mg-panel-client">

        <!-- A — Montants disponibles -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
            Montants disponibles
          </div>
          <div class="card-body">
            <div class="form-group">
              <label class="form-label" style="display:flex;align-items:center;gap:6px">
                Souhaitez-vous utiliser votre assurance invalidité long terme?
                <span class="abf-tooltip-wrap">
                  <span class="abf-tooltip-icon">&#9432;</span>
                  <span class="abf-tooltip-box">Si vous êtes couvert par une assurance invalidité long terme, ce montant peut être utilisé comme source de revenu en cas de maladie grave.</span>
                </span>
              </label>
              <div style="display:flex;gap:8px;margin-top:6px">
                <label class="fu-radio-pill"><input type="radio" name="mg-use-disability-c" id="mg-use-disability-c-oui" value="oui" onchange="mgCalc('client')" /> Oui</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-use-disability-c" id="mg-use-disability-c-non" value="non" checked onchange="mgCalc('client')" /> Non</label>
              </div>
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" style="display:flex;align-items:center;gap:6px">
                Souhaitez-vous utiliser votre fonds d'urgence?
                <span class="abf-tooltip-wrap">
                  <span class="abf-tooltip-icon">&#9432;</span>
                  <span class="abf-tooltip-box">Votre fonds d'urgence peut servir à couvrir une partie des dépenses imprévues liées à une maladie grave.</span>
                </span>
              </label>
              <div style="display:flex;gap:8px;margin-top:6px">
                <label class="fu-radio-pill"><input type="radio" name="mg-use-emergency-c" id="mg-use-emergency-c-oui" value="oui" onchange="mgCalc('client')" /> Oui</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-use-emergency-c" id="mg-use-emergency-c-non" value="non" checked onchange="mgCalc('client')" /> Non</label>
              </div>
            </div>
          </div>
        </div>

        <!-- B — Remplacement du revenu -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <span>Remplacement du revenu en cas de maladie grave</span>
            <div style="display:flex;gap:2px">
              <button id="mg-bn-brut-c" class="toggle-btn active" onclick="setMgBrutNet('client','brut')">Brut</button>
              <button id="mg-bn-net-c"  class="toggle-btn"        onclick="setMgBrutNet('client','net')">Net</button>
            </div>
          </div>
          <div class="card-body">
            <!-- Revenu client -->
            <div style="background:#f8f9fd;border-radius:6px;padding:10px 14px;font-size:13px;margin-bottom:14px">
              <div style="color:var(--muted);font-size:11px;font-weight:700;text-transform:uppercase;margin-bottom:6px">Revenu actuel</div>
              <div id="mg-revenu-actuel-c" style="font-weight:600;color:var(--navy)">—</div>
            </div>

            <!-- Durée de remplacement -->
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Durée du remplacement de revenu</label>
              <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px;margin-top:4px">
                <span id="mg-rr-client-label-c">Le client vise</span>
                <input class="form-input" id="mg-rr-pct-c" type="text" value="70" style="width:70px;text-align:center" oninput="mgCalc('client')"/>
                <span>% du revenu, soit <strong id="mg-rr-montant-c">0 $/mois</strong> pendant</span>
                <div class="input-sfx" style="max-width:100px">
                  <input class="form-input" id="mg-rr-duree-c" type="text" value="12" oninput="mgCalc('client')"/>
                  <span class="sfx" style="font-size:12px">mois</span>
                </div>
              </div>
            </div>

            <!-- Aidant -->
            <div style="border-top:1px solid var(--border);padding-top:14px">
              <div class="form-label" style="display:flex;align-items:center;gap:6px;margin-bottom:8px">
                Aidant
                <span class="abf-tooltip-wrap">
                  <span class="abf-tooltip-icon">&#9432;</span>
                  <span class="abf-tooltip-box">Un aidant peut devoir réduire ou cesser son travail pour soutenir la personne atteinte. Ce revenu manquant doit être pris en compte.</span>
                </span>
              </div>
              <div id="mg-aidant-type-c-group" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px">
                <label class="fu-radio-pill"><input type="radio" name="mg-aidant-type-c" value="conjoint" onchange="mgCalc('client')" /> Conjoint(e)</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-aidant-type-c" value="autre"    onchange="mgCalc('client')" /> Autre</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-aidant-type-c" value="aucun" checked onchange="mgCalc('client')" /> Aucun</label>
              </div>
              <div id="mg-aidant-detail-c" style="display:none">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px">
                  <span>Revenu de l'aidant</span>
                  <div class="input-sfx" style="max-width:150px">
                    <input class="form-input" id="mg-aidant-montant-c" type="text" placeholder="0" oninput="mgCalc('client')"/>
                    <span class="sfx">$/mois</span>
                  </div>
                  <span>pendant</span>
                  <div class="input-sfx" style="max-width:100px">
                    <input class="form-input" id="mg-aidant-duree-c" type="text" value="6" oninput="mgCalc('client')"/>
                    <span class="sfx" style="font-size:12px">mois</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- C — Dépenses supplémentaires -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
            <span style="display:flex;align-items:center;gap:6px">
              Dépenses supplémentaires en cas de maladie grave
              <span class="abf-tooltip-wrap">
                <span class="abf-tooltip-icon">&#9432;</span>
                <span class="abf-tooltip-box">Ces dépenses s'ajoutent au remplacement du revenu. Sélectionnez un niveau de protection ou ajustez les montants manuellement.</span>
              </span>
            </span>
          </div>
          <div class="card-body">
            <!-- Niveau de protection -->
            <div class="form-group" style="margin-bottom:16px">
              <label class="form-label">Niveau de protection</label>
              <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:6px">
                <label class="fu-radio-pill"><input type="radio" name="mg-coverage-c" value="aucun"    checked onchange="mgSetCoverage('client','aucun')"    /> Aucun</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-coverage-c" value="base"           onchange="mgSetCoverage('client','base')"     /> Base</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-coverage-c" value="confort"        onchange="mgSetCoverage('client','confort')"  /> Confort</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-coverage-c" value="superieur"      onchange="mgSetCoverage('client','superieur')"/> Supérieur</label>
              </div>
            </div>

            <!-- Dépenses uniques -->
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:8px">Dépenses uniques</div>
            <div id="mg-depenses-uniques-c">
              @foreach([
                ['id'=>'traitement',   'label'=>'Traitement non assuré',   'tooltip'=>'Médicaments, traitements ou thérapies non couverts par l\'assurance maladie.',    'default'=>5000],
                ['id'=>'equipement',   'label'=>'Équipement médical',      'tooltip'=>'Fauteuil roulant, lit d\'hôpital, matériel orthopédique, etc.',                   'default'=>5000],
                ['id'=>'adaptation',   'label'=>'Adaptation du domicile',  'tooltip'=>'Rampes d\'accès, salle de bain adaptée, élargissement des portes, etc.',          'default'=>12000],
                ['id'=>'vehicule',     'label'=>'Adaptation du véhicule',  'tooltip'=>'Commandes manuelles, rampe d\'accès, équipement spécialisé, etc.',                'default'=>8000],
                ['id'=>'transport',    'label'=>'Frais de transport',      'tooltip'=>'Ambulance, transport médical spécialisé, taxi pour traitements récurrents.',       'default'=>3000],
                ['id'=>'aide-domicile','label'=>'Aide à domicile',         'tooltip'=>'Soins infirmiers à domicile, auxiliaire familiale, aide aux tâches quotidiennes.', 'default'=>0],
              ] as $dep)
              <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid var(--border);font-size:13px">
                <span style="display:flex;align-items:center;gap:6px;color:var(--muted)">
                  {{ $dep['label'] }}
                  <span class="abf-tooltip-wrap">
                    <span class="abf-tooltip-icon">&#9432;</span>
                    <span class="abf-tooltip-box">{{ $dep['tooltip'] }}</span>
                  </span>
                </span>
                <div class="input-sfx" style="max-width:130px">
                  <input class="form-input" id="mg-dep-{{ $dep['id'] }}-c" type="text" value="{{ number_format($dep['default'], 0, '', ' ') }}" oninput="mgCalc('client')"/>
                  <span class="sfx">$</span>
                </div>
              </div>
              @endforeach
            </div>

            <!-- Dépenses récurrentes -->
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-top:16px;margin-bottom:8px">Dépenses récurrentes</div>
            <div id="mg-depenses-recurrentes-c">
              @foreach([
                ['id'=>'soins-professionnel','label'=>'Soins professionnels','tooltip'=>'Physiothérapie, ergothérapie, psychologie, etc.','default'=>0],
                ['id'=>'medicaments-rec',    'label'=>'Médicaments',         'tooltip'=>'Médicaments d\'ordonnance non couverts par le régime public.',   'default'=>0],
              ] as $dep)
              <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid var(--border);font-size:13px">
                <span style="display:flex;align-items:center;gap:6px;color:var(--muted)">
                  {{ $dep['label'] }}
                  <span class="abf-tooltip-wrap">
                    <span class="abf-tooltip-icon">&#9432;</span>
                    <span class="abf-tooltip-box">{{ $dep['tooltip'] }}</span>
                  </span>
                </span>
                <div class="input-sfx" style="max-width:130px">
                  <input class="form-input" id="mg-dep-{{ $dep['id'] }}-c" type="text" value="{{ number_format($dep['default'], 0, '', ' ') }}" oninput="mgCalc('client')"/>
                  <span class="sfx">$/mois</span>
                </div>
              </div>
              @endforeach
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;font-size:13px;font-weight:600;border-top:2px solid var(--border);margin-top:4px">
              <span>Total des dépenses supplémentaires</span>
              <strong id="mg-dep-total-c" style="color:var(--navy)">0 $</strong>
            </div>
          </div>
        </div>

      </div><!-- /mg-panel-client -->

      <!-- ══ PANEL CONJOINT (copie miroir, caché par défaut) ══ -->
      <div id="mg-panel-conjoint" style="display:none">

        <!-- A — Montants disponibles -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
            Montants disponibles
          </div>
          <div class="card-body">
            <div class="form-group">
              <label class="form-label">Souhaitez-vous utiliser l'assurance invalidité long terme?</label>
              <div style="display:flex;gap:8px;margin-top:6px">
                <label class="fu-radio-pill"><input type="radio" name="mg-use-disability-j" id="mg-use-disability-j-oui" value="oui" onchange="mgCalc('conjoint')" /> Oui</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-use-disability-j" id="mg-use-disability-j-non" value="non" checked onchange="mgCalc('conjoint')" /> Non</label>
              </div>
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label">Souhaitez-vous utiliser le fonds d'urgence?</label>
              <div style="display:flex;gap:8px;margin-top:6px">
                <label class="fu-radio-pill"><input type="radio" name="mg-use-emergency-j" id="mg-use-emergency-j-oui" value="oui" onchange="mgCalc('conjoint')" /> Oui</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-use-emergency-j" id="mg-use-emergency-j-non" value="non" checked onchange="mgCalc('conjoint')" /> Non</label>
              </div>
            </div>
          </div>
        </div>

        <!-- B — Remplacement du revenu -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <span>Remplacement du revenu en cas de maladie grave</span>
            <div style="display:flex;gap:2px">
              <button id="mg-bn-brut-j" class="toggle-btn active" onclick="setMgBrutNet('conjoint','brut')">Brut</button>
              <button id="mg-bn-net-j"  class="toggle-btn"        onclick="setMgBrutNet('conjoint','net')">Net</button>
            </div>
          </div>
          <div class="card-body">
            <div style="background:#f8f9fd;border-radius:6px;padding:10px 14px;font-size:13px;margin-bottom:14px">
              <div style="color:var(--muted);font-size:11px;font-weight:700;text-transform:uppercase;margin-bottom:6px">Revenu actuel</div>
              <div id="mg-revenu-actuel-j" style="font-weight:600;color:var(--navy)">—</div>
            </div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Durée du remplacement de revenu</label>
              <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px;margin-top:4px">
                <span id="mg-rr-client-label-j">Le client vise</span>
                <input class="form-input" id="mg-rr-pct-j" type="text" value="70" style="width:70px;text-align:center" oninput="mgCalc('conjoint')"/>
                <span>% du revenu, soit <strong id="mg-rr-montant-j">0 $/mois</strong> pendant</span>
                <div class="input-sfx" style="max-width:100px">
                  <input class="form-input" id="mg-rr-duree-j" type="text" value="12" oninput="mgCalc('conjoint')"/>
                  <span class="sfx" style="font-size:12px">mois</span>
                </div>
              </div>
            </div>
            <div style="border-top:1px solid var(--border);padding-top:14px">
              <div class="form-label" style="margin-bottom:8px">Aidant</div>
              <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px">
                <label class="fu-radio-pill"><input type="radio" name="mg-aidant-type-j" value="conjoint" onchange="mgCalc('conjoint')" /> Conjoint(e)</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-aidant-type-j" value="autre"    onchange="mgCalc('conjoint')" /> Autre</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-aidant-type-j" value="aucun" checked onchange="mgCalc('conjoint')" /> Aucun</label>
              </div>
              <div id="mg-aidant-detail-j" style="display:none">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px">
                  <span>Revenu de l'aidant</span>
                  <div class="input-sfx" style="max-width:150px">
                    <input class="form-input" id="mg-aidant-montant-j" type="text" placeholder="0" oninput="mgCalc('conjoint')"/>
                    <span class="sfx">$/mois</span>
                  </div>
                  <span>pendant</span>
                  <div class="input-sfx" style="max-width:100px">
                    <input class="form-input" id="mg-aidant-duree-j" type="text" value="6" oninput="mgCalc('conjoint')"/>
                    <span class="sfx" style="font-size:12px">mois</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- C — Dépenses supplémentaires -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
            Dépenses supplémentaires en cas de maladie grave
          </div>
          <div class="card-body">
            <div class="form-group" style="margin-bottom:16px">
              <label class="form-label">Niveau de protection</label>
              <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:6px">
                <label class="fu-radio-pill"><input type="radio" name="mg-coverage-j" value="aucun"    checked onchange="mgSetCoverage('conjoint','aucun')"    /> Aucun</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-coverage-j" value="base"           onchange="mgSetCoverage('conjoint','base')"     /> Base</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-coverage-j" value="confort"        onchange="mgSetCoverage('conjoint','confort')"  /> Confort</label>
                <label class="fu-radio-pill"><input type="radio" name="mg-coverage-j" value="superieur"      onchange="mgSetCoverage('conjoint','superieur')"/> Supérieur</label>
              </div>
            </div>
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:8px">Dépenses uniques</div>
            @foreach([
              ['id'=>'traitement',   'label'=>'Traitement non assuré',  'default'=>5000],
              ['id'=>'equipement',   'label'=>'Équipement médical',     'default'=>5000],
              ['id'=>'adaptation',   'label'=>'Adaptation du domicile', 'default'=>12000],
              ['id'=>'vehicule',     'label'=>'Adaptation du véhicule', 'default'=>8000],
              ['id'=>'transport',    'label'=>'Frais de transport',     'default'=>3000],
              ['id'=>'aide-domicile','label'=>'Aide à domicile',        'default'=>0],
            ] as $dep)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid var(--border);font-size:13px">
              <span style="color:var(--muted)">{{ $dep['label'] }}</span>
              <div class="input-sfx" style="max-width:130px">
                <input class="form-input" id="mg-dep-{{ $dep['id'] }}-j" type="text" value="{{ number_format($dep['default'], 0, '', ' ') }}" oninput="mgCalc('conjoint')"/>
                <span class="sfx">$</span>
              </div>
            </div>
            @endforeach
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-top:16px;margin-bottom:8px">Dépenses récurrentes</div>
            @foreach([
              ['id'=>'soins-professionnel','label'=>'Soins professionnels','default'=>0],
              ['id'=>'medicaments-rec',    'label'=>'Médicaments',         'default'=>0],
            ] as $dep)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid var(--border);font-size:13px">
              <span style="color:var(--muted)">{{ $dep['label'] }}</span>
              <div class="input-sfx" style="max-width:130px">
                <input class="form-input" id="mg-dep-{{ $dep['id'] }}-j" type="text" value="{{ number_format($dep['default'], 0, '', ' ') }}" oninput="mgCalc('conjoint')"/>
                <span class="sfx">$/mois</span>
              </div>
            </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;font-size:13px;font-weight:600;border-top:2px solid var(--border);margin-top:4px">
              <span>Total des dépenses supplémentaires</span>
              <strong id="mg-dep-total-j" style="color:var(--navy)">0 $</strong>
            </div>
          </div>
        </div>

      </div><!-- /mg-panel-conjoint -->

    </div><!-- /col principale -->

    <!-- Résumé sidebar sticky -->
    <div style="width:300px;flex-shrink:0;position:sticky;top:80px">
      <div class="card">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          Résumé — Maladie grave
        </div>
        <div id="mg-resume-body" style="padding:16px 14px;font-size:13px;color:var(--muted)">
          Complétez les informations pour voir le résumé.
        </div>
      </div>
    </div>

  </div>
</div><!-- /page-maladie-grave -->
