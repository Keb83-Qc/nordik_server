<div class="chat-wrapper">
    <div class="chat-container">
        {{-- BIENVENUE --}}
        <div class="messages__item">
            <div class="messages__wrapper">
                <div class="agent-avatar__icon">
                    <img src="{{ $agentImage ?? asset('assets/img/agent-default.jpg') }}"
                        onerror="this.src='{{ asset('assets/img/agent-default.jpg') }}'">
                </div>
                <div class="agent-msg">{!! __('homechat.welcome') !!}</div>
            </div>
        </div>

        {{-- Questions / Réponses --}}
        @include('livewire.quote-home-steps')

        <div id="chat-end" style="height:1px;"></div>
    </div>

    {{-- Zone de réponse fixe --}}
    <div class="response-area">
        <div class="response-container mx-auto" wire:loading.class="opacity-50 pe-none">

            <div wire:loading class="text-center text-muted small mb-2">
                <span class="spinner-border spinner-border-sm me-1"></span>
                ...
            </div>

            @if($step === 'occupancy')
            <div class="d-grid gap-2" wire:key="occ">
                <button wire:click="save('occupancy','locataire')" class="btn btn-outline-primary btn-lg">
                    {{ __('homechat.btn_tenant') }}
                </button>
                <button wire:click="save('occupancy','proprietaire')" class="btn btn-outline-primary btn-lg">
                    {{ __('homechat.btn_owner') }}
                </button>
            </div>

            @elseif($step === 'property_type')
            <div class="d-grid gap-2" wire:key="ptype">
                <button wire:click="save('property_type','maison')" class="btn btn-outline-primary btn-lg">
                    {{ __('homechat.btn_house') }}
                </button>
                <button wire:click="save('property_type','condo')" class="btn btn-outline-primary btn-lg">
                    {{ __('homechat.btn_condo') }}
                </button>
                <button wire:click="save('property_type','appartement')" class="btn btn-outline-primary btn-lg">
                    {{ __('homechat.btn_apartment') }}
                </button>
            </div>

            @elseif($step === 'hab_renewal_date')
            <div class="input-group" wire:key="hab-renewal">
                <input type="date" wire:model="hab_renewal_date" class="form-control form-control-lg shadow-sm">
                <button wire:click="submitHabRenewalDate" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'identity')
            <div wire:key="identity">
                <div class="input-group mb-2">
                    <input type="text" wire:model="first_name" class="form-control form-control-lg"
                        placeholder="{{ __('homechat.ph_first_name') }}">
                    <input type="text" wire:model="last_name" class="form-control form-control-lg"
                        placeholder="{{ __('homechat.ph_last_name') }}">
                </div>

                <div class="d-flex gap-2 mb-2 flex-wrap">
                    <button
                        type="button"
                        wire:click="$set('gender','homme')"
                        class="btn flex-grow-1 {{ $gender === 'homme' ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ __('homechat.btn_man') }}
                    </button>

                    <button
                        type="button"
                        wire:click="$set('gender','femme')"
                        class="btn flex-grow-1 {{ $gender === 'femme' ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ __('homechat.btn_woman') }}
                    </button>

                    <button
                        type="button"
                        wire:click="$set('gender','autre')"
                        class="btn flex-grow-1 {{ $gender === 'autre' ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ __('homechat.btn_other') }}
                    </button>

                    <button
                        type="button"
                        wire:click="$set('gender','prefer_not')"
                        class="btn flex-grow-1 {{ $gender === 'prefer_not' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                        {{ __('homechat.btn_prefer_not') }}
                    </button>
                </div>

                <button wire:click="submitIdentity" class="btn btn-primary w-100 btn-lg">
                    <i class="fas fa-paper-plane me-2"></i> {{ __('homechat.btn_send') }}
                </button>
            </div>

            @elseif($step === 'address')
            <div class="input-group" wire:key="addr">
                <input type="text" wire:model="address" class="form-control form-control-lg"
                    placeholder="{{ __('homechat.ph_address') }}">
                <button wire:click="submitAddress" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'living_there')
            <div class="d-flex gap-2" wire:key="living">
                <button wire:click="save('living_there','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_yes') }}
                </button>
                <button wire:click="save('living_there','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_no') }}
                </button>
            </div>

            @elseif($step === 'years_at_address')
            <div class="input-group" wire:key="movein">
                <input type="number" wire:model="years_at_address" class="form-control form-control-lg"
                    placeholder="{{ __('homechat.ph_years_at_address') }}" min="0" max="100">
                <button wire:click="submitYearsAtAddress" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'units_in_building')
            <div class="input-group" wire:key="units">
                <input type="number" wire:model="units_in_building" class="form-control form-control-lg"
                    placeholder="{{ __('homechat.ph_units') }}" min="1">
                <button wire:click="submitUnits" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'contents_amount')
            <div class="input-group" wire:key="contents">
                <input type="number" wire:model="contents_amount" class="form-control form-control-lg"
                    placeholder="{{ __('homechat.ph_contents_amount') }}" min="0" step="500">
                <button wire:click="submitContentsAmount" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'electric_baseboard')
            <div class="d-flex gap-2" wire:key="heat1">
                <button wire:click="save('electric_baseboard','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_yes') }}
                </button>
                <button wire:click="save('electric_baseboard','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_no') }}
                </button>
            </div>

            @elseif($step === 'supp_heating')
            <div class="d-flex gap-2" wire:key="heat2">
                <button wire:click="save('supp_heating','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_yes') }}
                </button>
                <button wire:click="save('supp_heating','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_no') }}
                </button>
            </div>

            @elseif($step === 'years_insured')
            <div class="d-grid gap-2" wire:key="yrsins">
                <button wire:click="save('years_insured','0')" class="btn btn-outline-primary btn-lg">{{ __('homechat.years_insured_0') }}</button>
                <button wire:click="save('years_insured','1_2')" class="btn btn-outline-primary btn-lg">{{ __('homechat.years_insured_1_2') }}</button>
                <button wire:click="save('years_insured','3_5')" class="btn btn-outline-primary btn-lg">{{ __('homechat.years_insured_3_5') }}</button>
                <button wire:click="save('years_insured','6_10')" class="btn btn-outline-primary btn-lg">{{ __('homechat.years_insured_6_10') }}</button>
                <button wire:click="save('years_insured','11_plus')" class="btn btn-outline-primary btn-lg">{{ __('homechat.years_insured_11_plus') }}</button>
            </div>


            @elseif($step === 'years_with_insurer')
            <div class="input-group" wire:key="yrswith">
                <input type="number" wire:model="years_with_insurer" class="form-control form-control-lg"
                    placeholder="{{ __('homechat.ph_years_with_insurer') }}" min="0" max="100">
                <button wire:click="submitYearsWithInsurer" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'current_insurer')
            <div class="input-group" wire:key="insurer">
                <input type="text" wire:model="current_insurer" class="form-control form-control-lg"
                    placeholder="{{ __('homechat.ph_current_insurer') }}">
                <button wire:click="submitCurrentInsurer" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'age')
            <div class="input-group" wire:key="age">
                <input type="number" wire:model="age" class="form-control form-control-lg"
                    placeholder="{{ __('homechat.ph_age') }}" min="16" max="120">
                <button wire:click="submitAge" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'email')
            <div class="input-group" wire:key="email">
                <input type="email" wire:model="email" class="form-control form-control-lg"
                    placeholder="{{ __('homechat.ph_email') }}">
                <button wire:click="submitEmail" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'phone')
            <div class="input-group" wire:key="phone">
                <input type="tel" wire:model="phone" class="form-control form-control-lg"
                    placeholder="{{ __('homechat.ph_phone') }}">
                <button wire:click="submitPhone" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'phone_is_cell')
            <div class="d-flex gap-2" wire:key="cell">
                <button wire:click="save('phone_is_cell','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_yes') }}
                </button>
                <button wire:click="save('phone_is_cell','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_no') }}
                </button>
            </div>

            @elseif($step === 'best_contact_time')
            <div class="d-grid gap-2" wire:key="contact-time">
                <button wire:click="setBestContactTime('matin')" class="btn btn-outline-primary btn-lg">
                    {{ __('homechat.btn_contact_matin') }}
                </button>
                <button wire:click="setBestContactTime('apres_midi')" class="btn btn-outline-primary btn-lg">
                    {{ __('homechat.btn_contact_apres_midi') }}
                </button>
                <button wire:click="setBestContactTime('soir')" class="btn btn-outline-primary btn-lg">
                    {{ __('homechat.btn_contact_soir') }}
                </button>
                <button wire:click="setBestContactTime('nimporte_quand')" class="btn btn-outline-primary btn-lg">
                    {{ __('homechat.btn_contact_nimporte_quand') }}
                </button>
            </div>

            @elseif($step === 'marital_status')
            <div class="d-grid gap-2" wire:key="marital">
                <button wire:click="save('marital_status','celibataire')" class="btn btn-outline-primary btn-lg">{{ __('homechat.marital_single') }}</button>
                <button wire:click="save('marital_status','conjoint')" class="btn btn-outline-primary btn-lg">{{ __('homechat.marital_partner') }}</button>
                <button wire:click="save('marital_status','marie')" class="btn btn-outline-primary btn-lg">{{ __('homechat.marital_married') }}</button>
                <button wire:click="save('marital_status','autre')" class="btn btn-outline-secondary btn-lg">{{ __('homechat.marital_other') }}</button>
            </div>

            @elseif($step === 'employment_status')
            <div class="d-grid gap-2" wire:key="job">
                <button wire:click="save('employment_status','employe')" class="btn btn-outline-primary btn-lg">{{ __('homechat.employment_status_employe') }}</button>
                <button wire:click="save('employment_status','travailleur_autonome')" class="btn btn-outline-primary btn-lg">{{ __('homechat.employment_status_self') }}</button>
                <button wire:click="save('employment_status','etudiant')" class="btn btn-outline-primary btn-lg">{{ __('homechat.employment_status_student') }}</button>
                <button wire:click="save('employment_status','retraite')" class="btn btn-outline-primary btn-lg">{{ __('homechat.employment_status_retired') }}</button>
                <button wire:click="save('employment_status','sans_emploi')" class="btn btn-outline-secondary btn-lg">{{ __('homechat.employment_status_unemployed') }}</button>
            </div>

            @elseif($step === 'education_level')
            <div class="d-grid gap-2" wire:key="edu">
                <button wire:click="save('education_level','secondaire')" class="btn btn-outline-primary btn-lg">{{ __('homechat.edu_highschool') }}</button>
                <button wire:click="save('education_level','college')" class="btn btn-outline-primary btn-lg">{{ __('homechat.edu_college') }}</button>
                <button wire:click="save('education_level','universite')" class="btn btn-outline-primary btn-lg">{{ __('homechat.edu_university') }}</button>
                <button wire:click="save('education_level','autre')" class="btn btn-outline-secondary btn-lg">{{ __('homechat.edu_other') }}</button>
            </div>

            @elseif($step === 'industry')
            <div class="input-group" wire:key="industry">
                <input type="text" wire:model="industry" class="form-control form-control-lg"
                    placeholder="{{ __('homechat.ph_industry') }}">
                <button wire:click="submitIndustry" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step === 'has_ia_products')
            <div class="d-flex gap-2" wire:key="ia">
                <button wire:click="save('has_ia_products','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_yes') }}
                </button>
                <button wire:click="save('has_ia_products','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_no') }}
                </button>
            </div>

            @elseif($step === 'consent_profile')
            <div class="d-grid gap-2" wire:key="cprofile">
                <button wire:click="save('consent_profile','accept')" class="btn btn-outline-primary btn-lg">
                    {{ __('homechat.btn_accept') }}
                </button>
                <button wire:click="save('consent_profile','refuse')" class="btn btn-outline-secondary btn-lg">
                    {{ __('homechat.btn_refuse') }}
                </button>
            </div>

            @elseif($step === 'consent_marketing')
            <div class="d-grid gap-2" wire:key="cmarket">
                <button wire:click="save('consent_marketing','accept')" class="btn btn-outline-primary btn-lg">
                    {{ __('homechat.btn_accept') }}
                </button>
                <button wire:click="save('consent_marketing','refuse')" class="btn btn-outline-secondary btn-lg">
                    {{ __('homechat.btn_refuse') }}
                </button>
            </div>

            @elseif($step === 'marketing_email')
            <div class="d-flex gap-2" wire:key="memail">
                <button wire:click="save('marketing_email','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_yes') }}
                </button>
                <button wire:click="save('marketing_email','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_no') }}
                </button>
            </div>

            @elseif($step === 'consent_credit')
            <div class="d-flex gap-2" wire:key="credit">
                <button wire:click="save('consent_credit','yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_yes') }}
                </button>
                <button wire:click="save('consent_credit','no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('homechat.btn_no') }}
                </button>
            </div>

            @elseif($step === 'final')
            <div wire:key="final" class="text-center p-2">
                <div class="alert alert-success border-0 shadow-sm mb-3">
                    <i class="fas fa-check-circle me-2"></i> {{ __('homechat.final_success_msg') }}
                </div>
                <button wire:click="finalize" class="btn btn-success btn-lg w-100 py-3 shadow border-0">
                    {{ __('homechat.btn_finalize') }}
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- Scroll script identique au auto --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            const chat = document.querySelector('.chat-container');
            const response = document.querySelector('.response-area');

            const applyPadding = () => {
                if (!chat || !response) return;
                const h = response.getBoundingClientRect().height;
                chat.style.paddingBottom = (h + 24) + 'px';
            };

            const scrollToBottom = (behavior = 'smooth') => {
                if (!chat) return;
                chat.scrollTo({
                    top: chat.scrollHeight,
                    behavior
                });
            };

            // Initial
            applyPadding();
            setTimeout(() => scrollToBottom('auto'), 120);

            window.addEventListener('resize', () => {
                applyPadding();
                scrollToBottom('auto');
            });

            // À chaque update livewire
            Livewire.on('scroll-down', () => {
                requestAnimationFrame(() => {
                    applyPadding();
                    scrollToBottom('smooth');
                });
            });
        });
    </script>
</div>