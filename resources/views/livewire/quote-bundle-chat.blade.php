<div class="chat-wrapper">
    <div class="chat-container">
        {{-- WELCOME --}}
        <div class="messages__item">
            <div class="messages__wrapper">
                <div class="agent-avatar__icon">
                    <img src="{{ $agentImage ?? asset('assets/img/agent-default.jpg') }}"
                        onerror="this.src='{{ asset('assets/img/agent-default.jpg') }}'">
                </div>
                <div class="agent-msg">{!! __('bundlechat.welcome') !!}</div>
            </div>
        </div>

        {{-- Q/A --}}
        @include('livewire.quote-bundle-steps')

        <div id="chat-end" style="height:1px;"></div>
    </div>

    {{-- RESPONSE AREA --}}
    <div class="response-area">
        <div class="response-container mx-auto">

            {{-- =========================
                 COMMON
            ========================== --}}
            @if($step === 'common_identity')
            <div wire:key="common-identity">
                <div class="input-group mb-2">
                    <input type="text" wire:model="first_name" class="form-control form-control-lg"
                        placeholder="{{ __('bundlechat.ph_first_name') }}">
                    <input type="text" wire:model="last_name" class="form-control form-control-lg"
                        placeholder="{{ __('bundlechat.ph_last_name') }}">
                </div>

                <div class="d-flex gap-2 mb-2 flex-wrap">
                    <button wire:click="$set('gender','homme')"
                        class="btn {{ $gender==='homme' ? 'btn-primary' : 'btn-outline-primary' }} flex-grow-1">
                        {{ __('bundlechat.btn_man') }}
                    </button>
                    <button wire:click="$set('gender','femme')"
                        class="btn {{ $gender==='femme' ? 'btn-primary' : 'btn-outline-primary' }} flex-grow-1">
                        {{ __('bundlechat.btn_woman') }}
                    </button>
                    <button wire:click="$set('gender','autre')"
                        class="btn {{ $gender==='autre' ? 'btn-primary' : 'btn-outline-primary' }} flex-grow-1">
                        {{ __('bundlechat.btn_other') }}
                    </button>
                    <button wire:click="$set('gender','prefer_not')"
                        class="btn {{ $gender==='prefer_not' ? 'btn-secondary' : 'btn-outline-secondary' }} flex-grow-1">
                        {{ __('bundlechat.btn_prefer_not') }}
                    </button>
                </div>

                <button wire:click="submitCommonIdentity" class="btn btn-primary w-100 btn-lg">
                    <i class="fas fa-paper-plane me-2"></i> {{ __('bundlechat.btn_send') }}
                </button>
            </div>

            @elseif($step === 'common_age')
            <div class="input-group" wire:key="common-age">
                <input type="number" wire:model="age" class="form-control form-control-lg"
                    placeholder="{{ __('bundlechat.ph_age') }}" min="16" max="120">
                <button wire:click="submitCommonAge" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'common_email')
            <div class="input-group" wire:key="common-email">
                <input type="email" wire:model="email" class="form-control form-control-lg"
                    placeholder="{{ __('bundlechat.ph_email') }}">
                <button wire:click="submitCommonEmail" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'common_phone')
            <div class="input-group" wire:key="common-phone">
                <input type="tel" wire:model="phone" class="form-control form-control-lg"
                    placeholder="{{ __('bundlechat.ph_phone') }}">
                <button wire:click="submitCommonPhone" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            {{-- =========================
                 PROFILE (avant auto, stocké dans habitation)
            ========================== --}}
            @elseif($step === 'hab_marital_status')
            <div class="d-grid gap-2" wire:key="hab-marital">
                <button wire:click="saveHab('marital_status','celibataire')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.marital_single') }}</button>
                <button wire:click="saveHab('marital_status','conjoint')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.marital_partner') }}</button>
                <button wire:click="saveHab('marital_status','marie')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.marital_married') }}</button>
                <button wire:click="saveHab('marital_status','autre')" class="btn btn-outline-secondary btn-lg">{{ __('bundlechat.marital_other') }}</button>
            </div>

            @elseif($step === 'hab_employment_status')
            <div class="d-grid gap-2" wire:key="hab-job">
                <button wire:click="saveHab('employment_status','employe')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.employment_employe') }}</button>
                <button wire:click="saveHab('employment_status','travailleur_autonome')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.employment_self') }}</button>
                <button wire:click="saveHab('employment_status','etudiant')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.employment_student') }}</button>
                <button wire:click="saveHab('employment_status','retraite')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.employment_retired') }}</button>
                <button wire:click="saveHab('employment_status','sans_emploi')" class="btn btn-outline-secondary btn-lg">{{ __('bundlechat.employment_unemployed') }}</button>
            </div>

            @elseif($step === 'hab_education_level')
            <div class="d-grid gap-2" wire:key="hab-edu">
                <button wire:click="saveHab('education_level','secondaire')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.edu_highschool') }}</button>
                <button wire:click="saveHab('education_level','college')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.edu_college') }}</button>
                <button wire:click="saveHab('education_level','universite')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.edu_university') }}</button>
                <button wire:click="saveHab('education_level','autre')" class="btn btn-outline-secondary btn-lg">{{ __('bundlechat.edu_other') }}</button>
            </div>

            @elseif($step === 'hab_industry')
            <div class="input-group" wire:key="hab-industry">
                <input type="text" wire:model="industry" class="form-control form-control-lg"
                    placeholder="{{ __('bundlechat.ph_industry') }}">
                <button wire:click="submitHabIndustry" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'hab_has_ia_products')
            <div class="d-flex gap-2" wire:key="hab-ia">
                <button wire:click="saveHab('has_ia_products','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('bundlechat.btn_yes') }}
                </button>
                <button wire:click="saveHab('has_ia_products','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('bundlechat.btn_no') }}
                </button>
            </div>

            {{-- =========================
                 AUTO
            ========================== --}}
            @elseif($step === 'auto_year')
            <select wire:model.live="vehicle_year" class="form-select form-select-lg shadow-sm" wire:key="auto-year">
                <option value="">{{ __('bundlechat.select_year') }}</option>
                @for ($i = date('Y') + 1; $i >= 1995; $i--)
                <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>

            @elseif($step === 'auto_brand')
            <select wire:model.live="vehicle_brand" class="form-select form-select-lg shadow-sm" wire:key="auto-brand">
                <option value="">{{ __('bundlechat.select_brand') }}</option>
                @foreach($brands as $b)
                <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>

            @elseif($step === 'auto_model')
            <select wire:model.live="vehicle_model" class="form-select form-select-lg shadow-sm" wire:key="auto-model">
                <option value="">{{ __('bundlechat.select_model') }}</option>
                @foreach($models as $m)
                <option value="{{ $m->id }}">{{ $m->name }}</option>
                @endforeach
            </select>

            @elseif($step === 'auto_renewal_date')
            <div class="input-group" wire:key="auto-renewal">
                <input type="date" wire:model="renewal_date" class="form-control form-control-lg shadow-sm">
                <button wire:click="submitAutoRenewalDate" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'auto_usage')
            <div class="d-flex gap-2" wire:key="auto-usage">
                <button wire:click="saveAuto('usage','personnel')"
                    class="btn {{ ($data['auto']['usage'] ?? '')==='personnel' ? 'btn-primary' : 'btn-outline-primary' }} flex-grow-1 py-3">
                    {{ __('bundlechat.btn_personal') }}
                </button>
                <button wire:click="saveAuto('usage','commercial')"
                    class="btn {{ ($data['auto']['usage'] ?? '')==='commercial' ? 'btn-primary' : 'btn-outline-primary' }} flex-grow-1 py-3">
                    {{ __('bundlechat.btn_commercial') }}
                </button>
            </div>

            @elseif($step === 'auto_km_annuel')
            <div class="d-flex gap-2 flex-wrap" wire:key="auto-km">
                <button wire:click="setAutoKm('0-15 000 km')" class="btn btn-outline-primary flex-grow-1 py-3">0-15 000</button>
                <button wire:click="setAutoKm('15-20 000 km')" class="btn btn-outline-primary flex-grow-1 py-3">15-20 000</button>
                <button wire:click="setAutoKm('20 000+ km')" class="btn btn-outline-primary flex-grow-1 py-3">20 000+</button>
            </div>

            @elseif($step === 'auto_existing_products')
            <div wire:key="auto-products" class="d-grid gap-2">
                <button wire:click="setAutoExistingProducts('assurance')" class="btn btn-outline-primary btn-lg">
                    {{ __('bundlechat.btn_products_insurance') }}
                </button>
                <button wire:click="setAutoExistingProducts('placement')" class="btn btn-outline-primary btn-lg">
                    {{ __('bundlechat.btn_products_investments') }}
                </button>
                <button wire:click="setAutoExistingProducts('both')" class="btn btn-outline-primary btn-lg">
                    {{ __('bundlechat.btn_products_both') }}
                </button>
                <button wire:click="setAutoExistingProducts('none')" class="btn btn-outline-secondary btn-lg">
                    {{ __('bundlechat.btn_products_none') }}
                </button>
            </div>

            @elseif($step === 'auto_license_number')
            <div wire:key="auto-license">
                <div class="input-group mb-2">
                    <input type="text" wire:model="license_number" class="form-control form-control-lg"
                        placeholder="{{ __('bundlechat.ph_license') }}">
                    <button wire:click="submitAutoLicense" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <button wire:click="skipAutoLicense" class="btn btn-link btn-sm w-100 text-decoration-none">
                    {{ __('bundlechat.btn_skip_license') }}
                </button>
            </div>

            {{-- =========================
                 HABITATION
            ========================== --}}
            @elseif($step === 'hab_occupancy')
            <div class="d-grid gap-2" wire:key="hab-occ">
                <button wire:click="saveHab('occupancy','locataire')" class="btn btn-outline-primary btn-lg">
                    {{ __('bundlechat.btn_tenant') }}
                </button>
                <button wire:click="saveHab('occupancy','proprietaire')" class="btn btn-outline-primary btn-lg">
                    {{ __('bundlechat.btn_owner') }}
                </button>
            </div>

            @elseif($step === 'hab_property_type')
            <div class="d-grid gap-2" wire:key="hab-ptype">
                <button wire:click="saveHab('property_type','maison')" class="btn btn-outline-primary btn-lg">
                    {{ __('bundlechat.btn_house') }}
                </button>
                <button wire:click="saveHab('property_type','condo')" class="btn btn-outline-primary btn-lg">
                    {{ __('bundlechat.btn_condo') }}
                </button>
                <button wire:click="saveHab('property_type','appartement')" class="btn btn-outline-primary btn-lg">
                    {{ __('bundlechat.btn_apartment') }}
                </button>
            </div>

            @elseif($step === 'hab_address')
            <div class="input-group" wire:key="hab-addr">
                <input type="text" wire:model="address" class="form-control form-control-lg"
                    placeholder="{{ __('bundlechat.ph_address') }}">
                <button wire:click="submitHabAddress" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'hab_living_there')
            <div class="d-flex gap-2" wire:key="hab-live">
                <button wire:click="saveHab('living_there','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('bundlechat.btn_yes') }}
                </button>
                <button wire:click="saveHab('living_there','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('bundlechat.btn_no') }}
                </button>
            </div>

            @elseif($step === 'hab_move_in_date')
            <div class="input-group" wire:key="hab-move">
                <input type="date" wire:model="move_in_date" class="form-control form-control-lg">
                <button wire:click="submitHabMoveInDate" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'hab_units_in_building')
            <div class="input-group" wire:key="hab-units">
                <input type="number" wire:model="units_in_building" class="form-control form-control-lg"
                    placeholder="{{ __('bundlechat.ph_units') }}" min="1">
                <button wire:click="submitHabUnits" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'hab_contents_amount')
            <div class="input-group" wire:key="hab-contents">
                <input type="number" wire:model="contents_amount" class="form-control form-control-lg"
                    placeholder="{{ __('bundlechat.ph_contents_amount') }}" min="0" step="500">
                <button wire:click="submitHabContentsAmount" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'hab_electric_baseboard')
            <div class="d-flex gap-2" wire:key="hab-heat1">
                <button wire:click="saveHab('electric_baseboard','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('bundlechat.btn_yes') }}
                </button>
                <button wire:click="saveHab('electric_baseboard','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('bundlechat.btn_no') }}
                </button>
            </div>

            @elseif($step === 'hab_supp_heating')
            <div class="d-flex gap-2" wire:key="hab-heat2">
                <button wire:click="saveHab('supp_heating','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('bundlechat.btn_yes') }}
                </button>
                <button wire:click="saveHab('supp_heating','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('bundlechat.btn_no') }}
                </button>
            </div>

            @elseif($step === 'hab_years_insured')
            <div class="d-grid gap-2" wire:key="hab-yrsins">
                <button wire:click="saveHab('years_insured','0')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.years_insured_0') }}</button>
                <button wire:click="saveHab('years_insured','1_2')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.years_insured_1_2') }}</button>
                <button wire:click="saveHab('years_insured','3_5')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.years_insured_3_5') }}</button>
                <button wire:click="saveHab('years_insured','6_10')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.years_insured_6_10') }}</button>
                <button wire:click="saveHab('years_insured','11_plus')" class="btn btn-outline-primary btn-lg">{{ __('bundlechat.years_insured_11_plus') }}</button>
            </div>

            @elseif($step === 'hab_years_with_insurer')
            <div class="input-group" wire:key="hab-yrswith">
                <input type="number" wire:model="years_with_insurer" class="form-control form-control-lg"
                    placeholder="{{ __('bundlechat.ph_years_with_insurer') }}" min="0" max="100">
                <button wire:click="submitHabYearsWithInsurer" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'hab_current_insurer')
            <div class="input-group" wire:key="hab-insurer">
                <input type="text" wire:model="current_insurer" class="form-control form-control-lg"
                    placeholder="{{ __('bundlechat.ph_current_insurer') }}">
                <button wire:click="submitHabCurrentInsurer" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            {{-- CONSENTS --}}
            @elseif($step === 'hab_consent_profile')
            <div class="d-grid gap-2" wire:key="hab-cprofile">
                <button wire:click="saveHab('consent_profile','accept')" class="btn btn-outline-primary btn-lg">
                    {{ __('bundlechat.btn_accept') }}
                </button>
                <button wire:click="saveHab('consent_profile','refuse')" class="btn btn-outline-secondary btn-lg">
                    {{ __('bundlechat.btn_refuse') }}
                </button>
            </div>

            @elseif($step === 'hab_consent_marketing')
            <div class="d-grid gap-2" wire:key="hab-cmarket">
                <button wire:click="saveHab('consent_marketing','accept')" class="btn btn-outline-primary btn-lg">
                    {{ __('bundlechat.btn_accept') }}
                </button>
                <button wire:click="saveHab('consent_marketing','refuse')" class="btn btn-outline-secondary btn-lg">
                    {{ __('bundlechat.btn_refuse') }}
                </button>
            </div>

            @elseif($step === 'hab_marketing_email')
            <div class="d-flex gap-2" wire:key="hab-memail">
                <button wire:click="saveHab('marketing_email','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('bundlechat.btn_yes') }}
                </button>
                <button wire:click="saveHab('marketing_email','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('bundlechat.btn_no') }}
                </button>
            </div>

            @elseif($step === 'hab_consent_credit')
            <div class="d-flex gap-2" wire:key="hab-credit">
                <button wire:click="saveHab('consent_credit','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('bundlechat.btn_yes') }}
                </button>
                <button wire:click="saveHab('consent_credit','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('bundlechat.btn_no') }}
                </button>
            </div>

            @elseif($step === 'final')
            <div wire:key="final" class="text-center p-2">
                <div class="alert alert-success border-0 shadow-sm mb-3">
                    <i class="fas fa-check-circle me-2"></i> {{ __('bundlechat.final_success_msg') }}
                </div>
                <button wire:click="finalize" class="btn btn-success btn-lg w-100 py-3 shadow border-0">
                    {{ __('bundlechat.btn_finalize') }}
                </button>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const chat = document.querySelector('.chat-container');
            const response = document.querySelector('.response-area');
            const end = document.getElementById('chat-end');

            const applyPadding = () => {
                if (!chat || !response) return;
                const h = response.getBoundingClientRect().height;
                chat.style.paddingBottom = (h + 24) + 'px';
            };

            const scrollToBottom = (behavior = 'smooth') => {
                if (!chat) return;
                if (end) end.scrollIntoView({
                    behavior,
                    block: 'end'
                });
                chat.scrollTo({
                    top: chat.scrollHeight,
                    behavior
                });
            };

            applyPadding();
            setTimeout(() => scrollToBottom('auto'), 120);

            window.addEventListener('resize', () => {
                applyPadding();
                scrollToBottom('auto');
            });

            Livewire.on('scroll-down', () => {
                requestAnimationFrame(() => {
                    applyPadding();
                    scrollToBottom('smooth');
                });
            });
        });
    </script>
</div>