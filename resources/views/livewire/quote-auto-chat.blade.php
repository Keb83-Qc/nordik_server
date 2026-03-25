<div class="chat-wrapper">
    <div class="chat-container">

        {{-- BIENVENUE --}}
        <div class="messages__item">
            <div class="messages__wrapper">
                @php
                $img = $agentImage ?? '';

                // si l'image legacy /team/ -> default
                if ($img && (str_contains($img, '/team/') || str_starts_with($img, 'team/'))) {
                $img = '';
                }

                $img = $img ?: asset('assets/img/VIP_Logo_Gold_Gradient10.png');
                @endphp

                <div class="agent-avatar__icon">
                    <img src="{{ $img }}" onerror="this.src='{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}'">
                </div>

                <div class="agent-msg">
                    {!! __('chat.welcome') !!}
                </div>
            </div>
        </div>

        {{-- QUESTIONS / RÉPONSES --}}
        @include('livewire.quote-auto-steps')

        <div id="chat-end" style="height:1px;"></div>
    </div>

    {{-- ZONE DE RÉPONSE FIXE --}}
    <div class="response-area">
        <div class="response-container mx-auto" wire:loading.class="opacity-50 pe-none">

            <div wire:loading class="text-center text-muted small mb-2">
                <span class="spinner-border spinner-border-sm me-1"></span>
                {{ __('chat.loading', ['default' => '...']) }}
            </div>

            @if($step == 'year')
            <select wire:model.live="vehicle_year" class="form-select form-select-lg shadow-sm" wire:key="sel-year">
                <option value="">{{ __('chat.select_year') }}</option>
                @for ($i = date('Y') + 1; $i >= 1995; $i--)
                <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>

            @elseif($step == 'brand')
            <select wire:model.live="vehicle_brand" class="form-select form-select-lg shadow-sm" wire:key="sel-brand">
                <option value="">{{ __('chat.select_brand') }}</option>
                @foreach($brands as $brand)
                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>

            @elseif($step == 'model')
            <select wire:model.live="vehicle_model" class="form-select form-select-lg shadow-sm" wire:key="sel-model">
                <option value="">{{ __('chat.select_model') }}</option>
                @foreach($models as $model)
                <option value="{{ $model->id }}">{{ $model->name }}</option>
                @endforeach
            </select>

            @elseif($step == 'renewal_date')
            <div class="input-group" wire:key="area-renewal">
                <input type="date" wire:model="renewal_date" class="form-control form-control-lg shadow-sm">
                <button wire:click="submitRenewalDate" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step == 'usage')
            <div class="d-flex gap-2" wire:key="btn-usage">
                {{-- IMPORTANT: garde des valeurs stables (slug) pour les traductions + emails --}}
                <button wire:click="save('usage','personnel')" class="btn btn-outline-primary flex-grow-1 py-3 shadow-sm">
                    {{ __('chat.btn_personal') }}
                </button>
                <button wire:click="save('usage','commercial')" class="btn btn-outline-primary flex-grow-1 py-3 shadow-sm">
                    {{ __('chat.btn_commercial') }}
                </button>
            </div>

            @elseif($step == 'km_annuel')
            <div class="d-flex gap-2 flex-wrap" wire:key="area-km">
                <button wire:click="setKm('0-15000')" class="btn btn-outline-primary flex-grow-1 py-3">0-15 000</button>
                <button wire:click="setKm('15000-20000')" class="btn btn-outline-primary flex-grow-1 py-3">15-20 000</button>
                <button wire:click="setKm('20000+')" class="btn btn-outline-primary flex-grow-1 py-3">20 000 +</button>
            </div>

            @elseif($step == 'address')
            <div class="input-group" wire:key="area-addr">
                <input type="text" wire:model="address" class="form-control form-control-lg"
                    placeholder="{{ __('chat.placeholder_address') }}">
                <button wire:click="submitAddress" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step == 'identity')
            <div wire:key="area-identity">
                <div class="input-group">
                    <input type="text" wire:model="first_name" class="form-control form-control-lg"
                        placeholder="{{ __('chat.placeholder_firstname') }}">
                    <input type="text" wire:model="last_name" class="form-control form-control-lg"
                        placeholder="{{ __('chat.placeholder_lastname') }}">
                    <button wire:click="submitIdentity" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>

            @elseif($step == 'age')
            <div class="input-group" wire:key="area-age">
                <input type="number" wire:model="age" class="form-control form-control-lg shadow-sm"
                    placeholder="{{ __('chat.placeholder_age') }}" min="16" max="99">
                <button wire:click="submitAge" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step == 'profession')
            <div class="input-group" wire:key="area-profession">
                <input type="text" wire:model="profession" class="form-control form-control-lg shadow-sm"
                    placeholder="{{ __('chat.placeholder_profession') }}">
                <button wire:click="submitProfession" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step == 'existing_products')
            <div wire:key="area-existing-products" class="d-grid gap-2">
                <button wire:click="setExistingProducts('assurance')" class="btn btn-outline-primary btn-lg">
                    {{ __('chat.btn_products_insurance') }}
                </button>
                <button wire:click="setExistingProducts('placement')" class="btn btn-outline-primary btn-lg">
                    {{ __('chat.btn_products_investments') }}
                </button>
                <button wire:click="setExistingProducts('both')" class="btn btn-outline-primary btn-lg">
                    {{ __('chat.btn_products_both') }}
                </button>
                <button wire:click="setExistingProducts('none')" class="btn btn-outline-secondary btn-lg">
                    {{ __('chat.btn_products_none') }}
                </button>
            </div>

            @elseif($step == 'email')
            <div class="input-group" wire:key="area-email">
                <input type="email" wire:model="email" class="form-control form-control-lg shadow-sm"
                    placeholder="{{ __('chat.placeholder_email') }}">
                <button wire:click="submitEmail" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step == 'phone')
            <div class="input-group" wire:key="area-phone">
                <input type="tel" wire:model="phone" class="form-control form-control-lg"
                    placeholder="{{ __('chat.placeholder_phone') }}">
                <button wire:click="submitPhone" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            @elseif($step == 'best_contact_time')
            <div class="d-grid gap-2" wire:key="area-contact-time">
                <button wire:click="setBestContactTime('matin')" class="btn btn-outline-primary btn-lg">
                    {{ __('chat.btn_contact_matin') }}
                </button>
                <button wire:click="setBestContactTime('apres_midi')" class="btn btn-outline-primary btn-lg">
                    {{ __('chat.btn_contact_apres_midi') }}
                </button>
                <button wire:click="setBestContactTime('soir')" class="btn btn-outline-primary btn-lg">
                    {{ __('chat.btn_contact_soir') }}
                </button>
                <button wire:click="setBestContactTime('nimporte_quand')" class="btn btn-outline-primary btn-lg">
                    {{ __('chat.btn_contact_nimporte_quand') }}
                </button>
            </div>

            @elseif($step == 'license_number')
            <div wire:key="area-license">
                <div class="input-group mb-2">
                    <input type="text" wire:model="license_number" class="form-control form-control-lg"
                        placeholder="{{ __('chat.placeholder_license') }}">
                    <button wire:click="submitLicense" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <button wire:click="skipLicense" class="btn btn-link btn-sm w-100 text-decoration-none">
                    {{ __('chat.btn_skip_license') }}
                </button>
            </div>

            @elseif($step == 'consent_profile')
            <div class="d-grid gap-2" wire:key="area-cprofile">
                <button wire:click="setConsentProfile('accept')" class="btn btn-outline-primary btn-lg">
                    {{ __('chat.btn_accept') }}
                </button>
                <button wire:click="setConsentProfile('refuse')" class="btn btn-outline-secondary btn-lg">
                    {{ __('chat.btn_refuse') }}
                </button>
            </div>

            @elseif($step == 'consent_marketing')
            <div class="d-grid gap-2" wire:key="area-cmarket">
                <button wire:click="setConsentMarketing('accept')" class="btn btn-outline-primary btn-lg">
                    {{ __('chat.btn_accept') }}
                </button>
                <button wire:click="setConsentMarketing('refuse')" class="btn btn-outline-secondary btn-lg">
                    {{ __('chat.btn_refuse') }}
                </button>
            </div>

            @elseif($step == 'marketing_email')
            <div class="d-flex gap-2" wire:key="area-memail">
                <button wire:click="setMarketingEmail('yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('chat.btn_yes') }}
                </button>
                <button wire:click="setMarketingEmail('no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('chat.btn_no') }}
                </button>
            </div>

            @elseif($step == 'consent_credit')
            <div class="d-flex gap-2" wire:key="area-credit">
                <button wire:click="setConsentCredit('yes')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('chat.btn_yes') }}
                </button>
                <button wire:click="setConsentCredit('no')" class="btn btn-outline-primary flex-grow-1 py-3">
                    {{ __('chat.btn_no') }}
                </button>
            </div>

            @elseif($step == 'final')
            <div wire:key="area-final" class="text-center p-2">
                <div class="alert alert-success border-0 shadow-sm mb-3">
                    <i class="fas fa-check-circle me-2"></i> {{ __('chat.final_success_msg') }}
                </div>
                <button wire:click="finalize" class="btn btn-success btn-lg w-100 py-3 shadow border-0">
                    {{ __('chat.btn_finalize') }}
                </button>
            </div>

            @else
            {{-- Step générique géré depuis la DB / Filament --}}
            @php
            $__cfg    = $this->getStepConfig($step);
            $__locale = app()->getLocale();
            @endphp
            @if($__cfg)
                @php $__opts = $__cfg->options ?? []; @endphp
                @if($__cfg->input_type === 'select' && !empty($__opts))
                <div class="d-grid gap-2" wire:key="area-gen-{{ $step }}">
                    @foreach($__opts as $__opt)
                    @php
                    $__val   = is_array($__opt) ? ($__opt['value'] ?? (string)$__opt) : (string)$__opt;
                    $__label = is_array($__opt) && isset($__opt['label'])
                        ? (is_array($__opt['label']) ? ($__opt['label'][$__locale] ?? $__opt['label']['fr'] ?? $__val) : $__opt['label'])
                        : $__val;
                    @endphp
                    <button wire:click="selectGenericOption('{{ $step }}', '{{ $__val }}')"
                            class="btn btn-outline-primary btn-lg">
                        {{ $__label }}
                    </button>
                    @endforeach
                </div>
                @elseif($__cfg->input_type === 'date')
                <div class="input-group" wire:key="area-gen-{{ $step }}">
                    <input type="date" wire:model="genericInput" class="form-control form-control-lg shadow-sm">
                    <button wire:click="submitGenericStep('{{ $step }}')" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                @else
                <div class="input-group" wire:key="area-gen-{{ $step }}">
                    <input type="text" wire:model="genericInput" class="form-control form-control-lg shadow-sm"
                           placeholder="{{ $this->getQuestion($step) }}">
                    <button wire:click="submitGenericStep('{{ $step }}')" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                @endif
            @endif
            @endif

        </div>
    </div>

    {{-- ✅ Scroll script (UNE seule fois, propre) --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            const chat = document.querySelector('.chat-container');
            const response = document.querySelector('.response-area');
            if (!chat) return;

            const applyPadding = () => {
                if (!response) return;
                const h = response.getBoundingClientRect().height;
                chat.style.paddingBottom = (h + 24) + 'px';
            };

            const scrollToBottom = (behavior = 'smooth') => {
                const end = document.getElementById('chat-end');
                if (end) end.scrollIntoView({
                    behavior,
                    block: 'end'
                });
                else chat.scrollTo({
                    top: chat.scrollHeight,
                    behavior
                });
            };

            // ✅ Ne force pas le scroll si l’utilisateur est remonté (seuil 140px)
            const isNearBottom = () => {
                return (chat.scrollHeight - chat.scrollTop - chat.clientHeight) < 140;
            };

            const doAutoScroll = (behavior = 'smooth', force = false) => {
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        applyPadding();
                        if (force || isNearBottom()) scrollToBottom(behavior);
                    });
                });
            };

            // init
            applyPadding();
            setTimeout(() => doAutoScroll('auto', true), 120);

            window.addEventListener('resize', () => doAutoScroll('auto', true));

            // Ton event custom
            Livewire.on('scroll-down', () => doAutoScroll('smooth', true));

            // ✅ IMPORTANT : après CHAQUE update Livewire (wire:model.live inclus)
            Livewire.hook('message.processed', () => {
                doAutoScroll('smooth', false);
            });
        });
    </script>
</div>