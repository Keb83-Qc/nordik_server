<div class="intake-wizard">

    {{-- Barre de progression --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <small class="text-muted fw-semibold">
                {{ sprintf($this->t('nav.step_of'), $stepNum, $totalSteps) }}
            </small>
            <small class="text-muted">{{ $progressPct }}%</small>
        </div>
        <div class="progress" style="height:6px;border-radius:99px;">
            <div class="progress-bar" role="progressbar" style="width:{{ $progressPct }}%;background:var(--vip-gold);border-radius:99px;" aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    {{-- Titre de l'étape --}}
    <h2 class="intake-step-title mb-4">{{ $this->t('step.' . $step) }}</h2>

    {{-- ─── Erreurs de validation ────────────────────────────────────────── --}}
    @if($errors->any())
        <div class="alert alert-danger py-2 mb-3">
            <ul class="mb-0 ps-3" style="font-size:14px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ─── ÉTAPES ────────────────────────────────────────────────────────── --}}

    @if($step === 'identite')
        {{--
            Honeypots cachés : Chrome remplit les PREMIERS champs correspondants trouvés
            dans le DOM. Ces champs invisibles absorbent l'autofill avant les vrais champs.
        --}}
        <div aria-hidden="true" style="position:absolute;left:-9999px;height:0;overflow:hidden" tabindex="-1">
            <input type="text"  name="hp_given_name"    autocomplete="given-name">
            <input type="text"  name="hp_family_name"   autocomplete="family-name">
            <input type="email" name="hp_email"         autocomplete="email">
            <input type="tel"   name="hp_tel"           autocomplete="tel">
            <input type="text"  name="hp_street"        autocomplete="street-address">
            <input type="text"  name="hp_postal"        autocomplete="postal-code">
        </div>
        <div class="row g-3" autocomplete="off">
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.prenom') }} *</label>
                <input type="text" class="form-control" wire:model="prenom"
                       name="intake_prenom"
                       autocomplete="off"
                       placeholder="{{ $this->t('ph.prenom') }}" required>
                @error('prenom')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.nom') }} *</label>
                <input type="text" class="form-control" wire:model="nom"
                       name="intake_nom"
                       autocomplete="off"
                       placeholder="{{ $this->t('ph.nom') }}" required>
                @error('nom')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.sexe') }} *</label>
                <select class="form-select" wire:model="sexe" name="intake_sexe" autocomplete="sex" required>
                    <option value="">—</option>
                    <option value="Homme">{{ $this->t('sexe.homme') }}</option>
                    <option value="Femme">{{ $this->t('sexe.femme') }}</option>
                </select>
                @error('sexe')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.ddn') }} *</label>
                <div class="d-flex gap-2">
                    <select class="form-select" wire:model="ddn_jour" name="intake_ddn_jour" autocomplete="off">
                        <option value="">JJ</option>
                        @for($d=1;$d<=31;$d++)
                            <option value="{{ str_pad($d,2,'0',STR_PAD_LEFT) }}">{{ str_pad($d,2,'0',STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                    <select class="form-select" wire:model="ddn_mois" name="intake_ddn_mois" autocomplete="off">
                        <option value="">MM</option>
                        @foreach(['01'=>'Jan','02'=>'Fév','03'=>'Mar','04'=>'Avr','05'=>'Mai','06'=>'Jun','07'=>'Jul','08'=>'Aoû','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Déc'] as $v=>$l)
                            <option value="{{ $v }}">{{ $l }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control" wire:model="ddn_annee"
                           name="intake_ddn_annee" autocomplete="off"
                           placeholder="AAAA" min="1920" max="{{ date('Y') - 18 }}" style="max-width:90px;">
                </div>
                @error('ddn_annee')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.courriel') }} *</label>
                <input type="email" class="form-control" wire:model="courriel"
                       name="intake_courriel"
                       autocomplete="off"
                       placeholder="{{ $this->t('ph.courriel') }}" required>
                @error('courriel')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.cellulaire') }} *</label>
                <input type="tel" class="form-control" wire:model="cellulaire"
                       name="intake_cellulaire"
                       autocomplete="off"
                       placeholder="{{ $this->t('ph.cellulaire') }}" required>
                @error('cellulaire')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

    @elseif($step === 'adresse')
        <div class="row g-3">
            <div class="col-4 col-sm-3">
                <label class="form-label">{{ $this->t('field.addr_civique') }}</label>
                <input type="text" class="form-control" wire:model="addr_civique"
                       name="intake_addr_civique" autocomplete="address-line1"
                       placeholder="123">
            </div>
            <div class="col-8 col-sm-9">
                <label class="form-label">{{ $this->t('field.addr_rue') }}</label>
                <input type="text" class="form-control" wire:model="addr_rue"
                       name="intake_addr_rue" autocomplete="address-line2"
                       placeholder="Rue Principale">
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.addr_ville') }} *</label>
                <input type="text" class="form-control" wire:model="addr_ville"
                       name="intake_addr_ville" autocomplete="address-level2"
                       placeholder="Montréal" required>
                @error('addr_ville')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-7 col-sm-4">
                <label class="form-label">{{ $this->t('field.addr_province') }} *</label>
                <select class="form-select" wire:model="addr_province"
                        name="intake_addr_province" autocomplete="address-level1" required>
                    <option value="">—</option>
                    @foreach(['QC'=>'Québec','ON'=>'Ontario','BC'=>'Colombie-Britannique','AB'=>'Alberta','MB'=>'Manitoba','SK'=>'Saskatchewan','NS'=>'Nouvelle-Écosse','NB'=>'Nouveau-Brunswick','NL'=>'T.-N.-L.','PE'=>'Î.-P.-É.','NT'=>'T.N.-O.','YT'=>'Yukon','NU'=>'Nunavut'] as $code=>$name)
                        <option value="{{ $code }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('addr_province')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-5 col-sm-2">
                <label class="form-label">{{ $this->t('field.addr_postal') }} *</label>
                <input type="text" class="form-control text-uppercase" wire:model="addr_postal"
                       name="intake_addr_postal" autocomplete="postal-code"
                       placeholder="H1A 1A1" maxlength="7">
                @error('addr_postal')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

    @elseif($step === 'famille')
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">{{ $this->t('field.etat_civil') }} *</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(['celibataire','marie','conjoint_fait','separe','divorce','veuf'] as $val)
                        <label class="intake-pill {{ $etat_civil === $val ? 'active' : '' }}">
                            <input type="radio" wire:model.live="etat_civil" value="{{ $val }}" style="display:none">
                            {{ $this->t('etat.' . $val) }}
                        </label>
                    @endforeach
                </div>
                @error('etat_civil')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.nb_enfants') }}</label>
                <select class="form-select" wire:model="nb_enfants">
                    @for($i=0;$i<=10;$i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

    @elseif($step === 'conjoint')
        <div class="row g-3">
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.prenom') }} *</label>
                <input type="text" class="form-control" wire:model="conj_prenom" required>
                @error('conj_prenom')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.nom') }} *</label>
                <input type="text" class="form-control" wire:model="conj_nom" required>
                @error('conj_nom')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.sexe') }} *</label>
                <select class="form-select" wire:model="conj_sexe" required>
                    <option value="">—</option>
                    <option value="Homme">{{ $this->t('sexe.homme') }}</option>
                    <option value="Femme">{{ $this->t('sexe.femme') }}</option>
                </select>
                @error('conj_sexe')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.ddn') }} *</label>
                <div class="d-flex gap-2">
                    <select class="form-select" wire:model="conj_ddn_jour">
                        <option value="">JJ</option>
                        @for($d=1;$d<=31;$d++)
                            <option value="{{ str_pad($d,2,'0',STR_PAD_LEFT) }}">{{ str_pad($d,2,'0',STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                    <select class="form-select" wire:model="conj_ddn_mois">
                        <option value="">MM</option>
                        @foreach(['01'=>'Jan','02'=>'Fév','03'=>'Mar','04'=>'Avr','05'=>'Mai','06'=>'Jun','07'=>'Jul','08'=>'Aoû','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Déc'] as $v=>$l)
                            <option value="{{ $v }}">{{ $l }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control" wire:model="conj_ddn_annee" placeholder="AAAA" min="1920" max="{{ date('Y') - 18 }}" style="max-width:90px;">
                </div>
                @error('conj_ddn_annee')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">{{ $this->t('field.courriel') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <input type="email" class="form-control" wire:model="conj_courriel">
            </div>
        </div>

    @elseif($step === 'revenus')
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">{{ $this->t('field.revenu_client') }}</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="revenu_client" placeholder="0" min="0" step="1000">
                </div>
            </div>
            @if($hasSpouse)
            <div class="col-12">
                <label class="form-label">{{ $this->t('field.revenu_conjoint') }}</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="revenu_conjoint" placeholder="0" min="0" step="1000">
                </div>
            </div>
            @endif
            <div class="col-12">
                <div class="alert alert-info py-2" style="font-size:13px;">
                    @if($locale === 'fr') Indiquez vos revenus bruts d'emploi annuels (avant impôts). Vous pouvez laisser vide si inconnu.
                    @elseif($locale === 'en') Enter your annual gross employment income (before taxes). You may leave blank if unknown.
                    @elseif($locale === 'es') Ingrese sus ingresos brutos anuales de empleo (antes de impuestos). Puede dejar en blanco si desconoce.
                    @else Mete revni brit travay anyèl ou (anvan taks). Ou ka kite vid si ou pa konnen.
                    @endif
                </div>
            </div>
        </div>

    @elseif($step === 'actifs')
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">{{ $this->t('field.a_propriete') }}</label>
                <div class="d-flex gap-3">
                    <label class="intake-pill {{ $a_propriete ? 'active' : '' }}">
                        <input type="radio" wire:model.live="a_propriete" value="1" style="display:none">
                        {{ $this->t('yes') }}
                    </label>
                    <label class="intake-pill {{ !$a_propriete ? 'active' : '' }}">
                        <input type="radio" wire:model.live="a_propriete" value="0" style="display:none">
                        {{ $this->t('no') }}
                    </label>
                </div>
            </div>
            @if($a_propriete)
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.valeur_propriete') }}</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="valeur_propriete" placeholder="0" min="0" step="5000">
                </div>
            </div>
            @endif
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.valeur_reer') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="valeur_reer" placeholder="0" min="0" step="1000">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.valeur_celi') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="valeur_celi" placeholder="0" min="0" step="1000">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.valeur_placements') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="valeur_placements" placeholder="0" min="0" step="1000">
                </div>
            </div>
        </div>

    @elseif($step === 'objectifs')
        <div class="row g-3">
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.age_retraite') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <input type="number" class="form-control" wire:model="age_retraite" placeholder="65" min="50" max="90">
            </div>
            @if($hasSpouse)
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.age_retraite_conjoint') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <input type="number" class="form-control" wire:model="age_retraite_conjoint" placeholder="65" min="50" max="90">
            </div>
            @endif
            <div class="col-12">
                <label class="form-label">{{ $this->t('field.objectifs_texte') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <textarea class="form-control" wire:model="objectifs_texte" rows="4" placeholder="{{ $this->t('ph.objectifs') }}"></textarea>
            </div>
        </div>

    @elseif($step === 'autres_revenus')
        <p class="text-muted mb-3" style="font-size:13px;">{{ $this->t('info.autres_revenus') }}</p>
        <div class="row g-3">
            <div class="col-12"><h6 class="fw-bold" style="color:var(--vip-navy);">{{ $this->t('label.vous') }}</h6></div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.autre_revenu_type') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <select class="form-select" wire:model="autre_revenu_client_type">
                    <option value="">— {{ $this->t('label.choisir') }} —</option>
                    <option value="Rente gouvernementale">{{ $this->t('revtype.rente_gouv') }}</option>
                    <option value="Revenu locatif">{{ $this->t('revtype.locatif') }}</option>
                    <option value="Dividendes / intérêts">{{ $this->t('revtype.dividendes') }}</option>
                    <option value="Pension de retraite">{{ $this->t('revtype.pension') }}</option>
                    <option value="Travail autonome">{{ $this->t('revtype.autonome') }}</option>
                    <option value="Autre">{{ $this->t('revtype.autre') }}</option>
                </select>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.montant_annuel') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="autre_revenu_client_montant" placeholder="0" min="0" step="500">
                </div>
            </div>
            @if($hasSpouse)
            <div class="col-12 mt-2"><h6 class="fw-bold" style="color:var(--vip-navy);">{{ $this->t('label.conjoint') }}</h6></div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.autre_revenu_type') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <select class="form-select" wire:model="autre_revenu_conjoint_type">
                    <option value="">— {{ $this->t('label.choisir') }} —</option>
                    <option value="Rente gouvernementale">{{ $this->t('revtype.rente_gouv') }}</option>
                    <option value="Revenu locatif">{{ $this->t('revtype.locatif') }}</option>
                    <option value="Dividendes / intérêts">{{ $this->t('revtype.dividendes') }}</option>
                    <option value="Pension de retraite">{{ $this->t('revtype.pension') }}</option>
                    <option value="Travail autonome">{{ $this->t('revtype.autonome') }}</option>
                    <option value="Autre">{{ $this->t('revtype.autre') }}</option>
                </select>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.montant_annuel') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="autre_revenu_conjoint_montant" placeholder="0" min="0" step="500">
                </div>
            </div>
            @endif
        </div>

    @elseif($step === 'epargne')
        <p class="text-muted mb-3" style="font-size:13px;">{{ $this->t('info.epargne') }}</p>
        <div class="row g-3">
            @if($hasSpouse)
            <div class="col-12"><h6 class="fw-bold" style="color:var(--vip-navy);">{{ $this->t('label.reer_ferr') }}</h6></div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.reer_conjoint') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="reer_conjoint" placeholder="0" min="0" step="1000">
                </div>
            </div>
            @endif
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.ferr_client') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ferr_client" placeholder="0" min="0" step="1000">
                </div>
            </div>
            @if($hasSpouse)
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.ferr_conjoint') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ferr_conjoint" placeholder="0" min="0" step="1000">
                </div>
            </div>
            @endif
            <div class="col-12"><h6 class="fw-bold mt-2" style="color:var(--vip-navy);">{{ $this->t('label.celiapp_pension') }}</h6></div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.celiapp_client') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="celiapp_client" placeholder="0" min="0" step="1000">
                </div>
            </div>
            @if($hasSpouse)
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.celiapp_conjoint') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="celiapp_conjoint" placeholder="0" min="0" step="1000">
                </div>
            </div>
            @endif
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.fonds_pension_client') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="fonds_pension_client" placeholder="0" min="0" step="1000">
                </div>
            </div>
            @if($hasSpouse)
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.fonds_pension_conjoint') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="fonds_pension_conjoint" placeholder="0" min="0" step="1000">
                </div>
            </div>
            @endif
        </div>

    @elseif($step === 'dettes')
        <p class="text-muted mb-3" style="font-size:13px;">{{ $this->t('info.dettes') }}</p>
        <div class="row g-3">
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.dette_hypotheque') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="dette_hypotheque" placeholder="0" min="0" step="1000">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.dette_auto') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="dette_auto" placeholder="0" min="0" step="500">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.dette_cartes') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="dette_cartes" placeholder="0" min="0" step="100">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.dette_marge') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="dette_marge" placeholder="0" min="0" step="500">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.dette_pret_perso') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="dette_pret_perso" placeholder="0" min="0" step="500">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.dette_autres') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="dette_autres" placeholder="0" min="0" step="100">
                </div>
            </div>
        </div>

    @elseif($step === 'assurance_vie')
        <p class="text-muted mb-3" style="font-size:13px;">{{ $this->t('info.assurance_vie') }}</p>
        <div class="row g-3">
            <div class="col-12"><h6 class="fw-bold" style="color:var(--vip-navy);">{{ $this->t('label.vous') }}</h6></div>
            <div class="col-12 col-sm-4">
                <label class="form-label">{{ $this->t('field.ass_type') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <select class="form-select" wire:model="ass_vie_client_type">
                    <option value="">—</option>
                    <option value="Temporaire 10 ans">{{ $this->t('asstype.t10') }}</option>
                    <option value="Temporaire 20 ans">{{ $this->t('asstype.t20') }}</option>
                    <option value="Temporaire 30 ans">{{ $this->t('asstype.t30') }}</option>
                    <option value="Permanente entière">{{ $this->t('asstype.permanente') }}</option>
                    <option value="Universelle">{{ $this->t('asstype.universelle') }}</option>
                    <option value="Autre">{{ $this->t('revtype.autre') }}</option>
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <label class="form-label">{{ $this->t('field.ass_montant') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ass_vie_client_montant" placeholder="0" min="0" step="10000">
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <label class="form-label">{{ $this->t('field.ass_prime_annuelle') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ass_vie_client_prime" placeholder="0" min="0" step="100">
                </div>
            </div>
            @if($hasSpouse)
            <div class="col-12 mt-2"><h6 class="fw-bold" style="color:var(--vip-navy);">{{ $this->t('label.conjoint') }}</h6></div>
            <div class="col-12 col-sm-4">
                <label class="form-label">{{ $this->t('field.ass_type') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <select class="form-select" wire:model="ass_vie_conj_type">
                    <option value="">—</option>
                    <option value="Temporaire 10 ans">{{ $this->t('asstype.t10') }}</option>
                    <option value="Temporaire 20 ans">{{ $this->t('asstype.t20') }}</option>
                    <option value="Temporaire 30 ans">{{ $this->t('asstype.t30') }}</option>
                    <option value="Permanente entière">{{ $this->t('asstype.permanente') }}</option>
                    <option value="Universelle">{{ $this->t('asstype.universelle') }}</option>
                    <option value="Autre">{{ $this->t('revtype.autre') }}</option>
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <label class="form-label">{{ $this->t('field.ass_montant') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ass_vie_conj_montant" placeholder="0" min="0" step="10000">
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <label class="form-label">{{ $this->t('field.ass_prime_annuelle') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ass_vie_conj_prime" placeholder="0" min="0" step="100">
                </div>
            </div>
            @endif
        </div>

    @elseif($step === 'assurance_invalidite')
        <p class="text-muted mb-3" style="font-size:13px;">{{ $this->t('info.assurance_invalidite') }}</p>
        <div class="row g-3">
            <div class="col-12"><h6 class="fw-bold" style="color:var(--vip-navy);">{{ $this->t('label.vous') }}</h6></div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.ass_inv_rente') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ass_inv_client_rente" placeholder="0" min="0" step="100">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.ass_prime_mensuelle') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ass_inv_client_prime" placeholder="0" min="0" step="10">
                </div>
            </div>
            @if($hasSpouse)
            <div class="col-12 mt-2"><h6 class="fw-bold" style="color:var(--vip-navy);">{{ $this->t('label.conjoint') }}</h6></div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.ass_inv_rente') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ass_inv_conj_rente" placeholder="0" min="0" step="100">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.ass_prime_mensuelle') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ass_inv_conj_prime" placeholder="0" min="0" step="10">
                </div>
            </div>
            @endif
        </div>

    @elseif($step === 'assurance_mg')
        <p class="text-muted mb-3" style="font-size:13px;">{{ $this->t('info.assurance_mg') }}</p>
        <div class="row g-3">
            <div class="col-12"><h6 class="fw-bold" style="color:var(--vip-navy);">{{ $this->t('label.vous') }}</h6></div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.ass_montant') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ass_mg_client_montant" placeholder="0" min="0" step="10000">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.ass_prime_annuelle') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ass_mg_client_prime" placeholder="0" min="0" step="100">
                </div>
            </div>
            @if($hasSpouse)
            <div class="col-12 mt-2"><h6 class="fw-bold" style="color:var(--vip-navy);">{{ $this->t('label.conjoint') }}</h6></div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.ass_montant') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ass_mg_conj_montant" placeholder="0" min="0" step="10000">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.ass_prime_annuelle') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="ass_mg_conj_prime" placeholder="0" min="0" step="100">
                </div>
            </div>
            @endif
        </div>

    @elseif($step === 'fonds_urgence')
        <p class="text-muted mb-3" style="font-size:13px;">{{ $this->t('info.fonds_urgence') }}</p>
        <div class="row g-3">
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.fu_montant_actuel') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="fu_montant_actuel" placeholder="0" min="0" step="500">
                </div>
            </div>
        </div>

    @elseif($step === 'retraite')
        <p class="text-muted mb-3" style="font-size:13px;">{{ $this->t('info.retraite') }}</p>
        <div class="row g-3">
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.rev_retraite_mensuel') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="rev_retraite_mensuel" placeholder="0" min="0" step="100">
                </div>
            </div>
            @if($hasSpouse)
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.rev_retraite_conj_mensuel') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="rev_retraite_conj_mensuel" placeholder="0" min="0" step="100">
                </div>
            </div>
            @endif
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.regime_retraite_client') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="regime_retraite_client" placeholder="0" min="0" step="100">
                </div>
            </div>
            @if($hasSpouse)
            <div class="col-12 col-sm-6">
                <label class="form-label">{{ $this->t('field.regime_retraite_conjoint') }} <span class="text-muted small">{{ $this->t('optional') }}</span></label>
                <div class="input-group"><span class="input-group-text">$</span>
                    <input type="number" class="form-control" wire:model="regime_retraite_conjoint" placeholder="0" min="0" step="100">
                </div>
            </div>
            @endif
        </div>

    @elseif($step === 'profil_investisseur')
        <p class="text-muted mb-3" style="font-size:13px;">{{ $this->t('info.profil_investisseur') }}</p>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">{{ $this->t('field.profil_risque') }}</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(['prudent','modere','equilibre','croissance','audacieux'] as $val)
                        <label class="intake-pill {{ $profil_risque === $val ? 'active' : '' }}">
                            <input type="radio" wire:model.live="profil_risque" value="{{ $val }}" style="display:none">
                            {{ $this->t('risque.' . $val) }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">{{ $this->t('field.profil_horizon') }}</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(['court','moyen','long'] as $val)
                        <label class="intake-pill {{ $profil_horizon === $val ? 'active' : '' }}">
                            <input type="radio" wire:model.live="profil_horizon" value="{{ $val }}" style="display:none">
                            {{ $this->t('horizon.' . $val) }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ─── Boutons de navigation ─────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
        @if($stepNum > 1)
            <button type="button" class="btn btn-outline-secondary" wire:click="prevStep" wire:loading.attr="disabled">
                ← {{ $this->t('nav.prev') }}
            </button>
        @else
            <div></div>
        @endif

        @if($stepNum < $totalSteps)
            <button type="button" class="btn intake-btn-primary" wire:click="nextStep" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="nextStep">{{ $this->t('nav.next') }} →</span>
                <span wire:loading wire:target="nextStep">...</span>
            </button>
        @else
            <button type="button" class="btn intake-btn-primary" wire:click="submit" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">✓ {{ $this->t('nav.submit') }}</span>
                <span wire:loading wire:target="submit">...</span>
            </button>
        @endif
    </div>
</div>
