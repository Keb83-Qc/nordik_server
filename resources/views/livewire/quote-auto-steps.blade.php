{{-- resources/views/livewire/quote-auto-steps.blade.php --}}

{{-- ============================================================
|  IDENTITÉ (Prénom + Nom) — première étape
============================================================ --}}
@if($step === 'identity' || isset($data['first_name']) || isset($data['last_name']))
<div class="messages__item" wire:key="msg-identity">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('identity') }}</div>
    </div>
</div>

@if(isset($data['first_name']) && isset($data['last_name']))
<div class="messages__item" wire:key="resp-identity">
    <div class="user-message" wire:click="goToStep('identity')">
        <span>{{ $data['first_name'] }} {{ $data['last_name'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  ÂGE
============================================================ --}}
@if(isset($data['first_name']) && ($step === 'age' || isset($data['age'])))
<div class="messages__item" wire:key="msg-age">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('age') }}</div>
    </div>
</div>

@if(isset($data['age']))
<div class="messages__item" wire:key="resp-age">
    <div class="user-message" wire:click="goToStep('age')">
        <span>{{ $data['age'] }} {{ __('chat.years_old') }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  EMAIL
============================================================ --}}
@if(isset($data['age']) && ($step === 'email' || isset($data['email'])))
<div class="messages__item" wire:key="msg-email">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('email') }}</div>
    </div>
</div>

@if(isset($data['email']))
<div class="messages__item" wire:key="resp-email">
    <div class="user-message" wire:click="goToStep('email')">
        <span>{{ $data['email'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  TÉLÉPHONE
============================================================ --}}
@if(isset($data['email']) && ($step === 'phone' || isset($data['phone'])))
<div class="messages__item" wire:key="msg-phone">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('phone') }}</div>
    </div>
</div>

@if(isset($data['phone']))
<div class="messages__item" wire:key="resp-phone">
    <div class="user-message" wire:click="goToStep('phone')">
        <span>{{ $data['phone'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  MEILLEUR MOMENT DE CONTACT
============================================================ --}}
@if(isset($data['phone']) && ($step === 'best_contact_time' || isset($data['best_contact_time'])))
<div class="messages__item" wire:key="msg-contact-time">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('best_contact_time') }}</div>
    </div>
</div>

@if(isset($data['best_contact_time']))
<div class="messages__item" wire:key="resp-contact-time">
    <div class="user-message" wire:click="goToStep('best_contact_time')">
        @php
        $contactTimeKey = 'chat.contact_time_' . $data['best_contact_time'];
        $contactTimeLabel = __($contactTimeKey);
        if ($contactTimeLabel === $contactTimeKey) $contactTimeLabel = $data['best_contact_time'];
        @endphp
        <span>{{ $contactTimeLabel }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  ANNÉE
============================================================ --}}
@if(isset($data['best_contact_time']) && ($step === 'year' || isset($data['year'])))
<div class="messages__item" wire:key="msg-year">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('year') }}</div>
    </div>
</div>

@if(isset($data['year']))
<div class="messages__item" wire:key="resp-year">
    <div class="user-message" wire:click="goToStep('year')">
        <span>{{ $data['year'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  MARQUE
============================================================ --}}
@if(isset($data['year']) && ($step === 'brand' || isset($data['brand'])))
<div class="messages__item" wire:key="msg-brand">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('brand') }}</div>
    </div>
</div>

@if(isset($data['brand']))
<div class="messages__item" wire:key="resp-brand">
    <div class="user-message" wire:click="goToStep('brand')">
        <span>{{ $data['brand'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  MODÈLE
============================================================ --}}
@if(isset($data['brand']) && ($step === 'model' || isset($data['model'])))
<div class="messages__item" wire:key="msg-model">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('model') }}</div>
    </div>
</div>

@if(isset($data['model']))
<div class="messages__item" wire:key="resp-model">
    <div class="user-message" wire:click="goToStep('model')">
        <span>{{ $data['model'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  DATE DE RENOUVELLEMENT
============================================================ --}}
@if(isset($data['model']) && ($step === 'renewal_date' || isset($data['renewal_date'])))
<div class="messages__item" wire:key="msg-renewal">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('renewal_date') }}</div>
    </div>
</div>

@if(isset($data['renewal_date']))
<div class="messages__item" wire:key="resp-renewal">
    <div class="user-message" wire:click="goToStep('renewal_date')">
        <span>{{ $data['renewal_date'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  USAGE
============================================================ --}}
@if(isset($data['renewal_date']) && ($step === 'usage' || isset($data['usage'])))
<div class="messages__item" wire:key="msg-usage">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('usage') }}</div>
    </div>
</div>

@if(isset($data['usage']))
<div class="messages__item" wire:key="resp-usage">
    <div class="user-message" wire:click="goToStep('usage')">
        @php
        $usageLabel = match($data['usage'] ?? '') {
        'personnel' => __('chat.btn_personal'),
        'commercial' => __('chat.btn_commercial'),
        default => $data['usage'] ?? '',
        };
        @endphp
        <span>{{ $usageLabel }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  KM ANNUEL
============================================================ --}}
@if(isset($data['usage']) && ($step === 'km_annuel' || isset($data['km_annuel'])))
<div class="messages__item" wire:key="msg-km">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('km_annuel') }}</div>
    </div>
</div>

@if(isset($data['km_annuel']))
<div class="messages__item" wire:key="resp-km">
    <div class="user-message" wire:click="goToStep('km_annuel')">
        <span>{{ $data['km_annuel'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  ADRESSE
============================================================ --}}
@if(isset($data['km_annuel']) && ($step === 'address' || isset($data['address'])))
<div class="messages__item" wire:key="msg-addr">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('address') }}</div>
    </div>
</div>

@if(isset($data['address']))
<div class="messages__item" wire:key="resp-addr">
    <div class="user-message" wire:click="goToStep('address')">
        <span>{{ $data['address'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  PRODUITS EXISTANTS
============================================================ --}}
@if(isset($data['address']) && ($step === 'existing_products' || isset($data['existing_products'])))
<div class="messages__item" wire:key="msg-existing-products">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('existing_products') }}</div>
    </div>
</div>

@if(isset($data['existing_products']))
@php
$productsKey = 'chat.products_' . $data['existing_products'];
$productsLabel = __($productsKey);
if ($productsLabel === $productsKey) $productsLabel = $data['existing_products'];
@endphp
<div class="messages__item" wire:key="resp-existing-products">
    <div class="user-message" wire:click="goToStep('existing_products')">
        <span>{{ $productsLabel }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  PROFESSION
============================================================ --}}
@if(isset($data['existing_products']) && ($step === 'profession' || isset($data['profession'])))
<div class="messages__item" wire:key="msg-profession">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('profession') }}</div>
    </div>
</div>

@if(isset($data['profession']))
<div class="messages__item" wire:key="resp-profession">
    <div class="user-message" wire:click="goToStep('profession')">
        <span>{{ $data['profession'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  NUMÉRO DE PERMIS
============================================================ --}}
@if(isset($data['profession']) && ($step === 'license_number' || isset($data['license_number'])))
<div class="messages__item" wire:key="msg-license">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('license_number') }}</div>
    </div>
</div>

@if(isset($data['license_number']))
<div class="messages__item" wire:key="resp-license">
    <div class="user-message" wire:click="goToStep('license_number')">
        @php
        $lic = $data['license_number'] ?? '';
        $licLabel = ($lic === 'not_provided' || $lic === 'Non fourni') ? __('chat.not_provided', [], app()->getLocale()) : $lic;
        @endphp
        <span>{{ $licLabel }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  CONSENTEMENT PROFILAGE
============================================================ --}}
@if(isset($data['license_number']) && ($step === 'consent_profile' || isset($data['consent_profile'])))
<div class="messages__item" wire:key="msg-cprofile">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('consent_profile') }}</div>
    </div>
</div>

@if(isset($data['consent_profile']))
<div class="messages__item" wire:key="resp-cprofile">
    <div class="user-message" wire:click="goToStep('consent_profile')">
        @php $k = 'chat.consent_profile_'.$data['consent_profile']; $t = __($k); @endphp
        <span>{{ $t === $k ? $data['consent_profile'] : $t }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  CONSENTEMENT MARKETING
============================================================ --}}
@if(isset($data['consent_profile']) && ($step === 'consent_marketing' || isset($data['consent_marketing'])))
<div class="messages__item" wire:key="msg-cmarket">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('consent_marketing') }}</div>
    </div>
</div>

@if(isset($data['consent_marketing']))
<div class="messages__item" wire:key="resp-cmarket">
    <div class="user-message" wire:click="goToStep('consent_marketing')">
        @php $k = 'chat.consent_marketing_'.$data['consent_marketing']; $t = __($k); @endphp
        <span>{{ $t === $k ? $data['consent_marketing'] : $t }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  MARKETING PAR COURRIEL (si consentement marketing accepté)
============================================================ --}}
@if(isset($data['consent_marketing']) && ($data['consent_marketing'] === 'accept') && ($step === 'marketing_email' || isset($data['marketing_email'])))
<div class="messages__item" wire:key="msg-memail">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('marketing_email') }}</div>
    </div>
</div>

@if(isset($data['marketing_email']))
<div class="messages__item" wire:key="resp-memail">
    <div class="user-message" wire:click="goToStep('marketing_email')">
        @php $k = 'chat.marketing_email_'.$data['marketing_email']; $t = __($k); @endphp
        <span>{{ $t === $k ? $data['marketing_email'] : $t }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  VÉRIFICATION CRÉDIT
============================================================ --}}
@php
$beforeCredit = isset($data['consent_marketing']) && (
    ($data['consent_marketing'] !== 'accept') || isset($data['marketing_email'])
);
@endphp
@if($beforeCredit && ($step === 'consent_credit' || isset($data['consent_credit'])))
<div class="messages__item" wire:key="msg-credit">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion('consent_credit') }}</div>
    </div>
</div>

@if(isset($data['consent_credit']))
<div class="messages__item" wire:key="resp-credit">
    <div class="user-message" wire:click="goToStep('consent_credit')">
        @php $k = 'chat.consent_credit_'.$data['consent_credit']; $t = __($k); @endphp
        <span>{{ $t === $k ? $data['consent_credit'] : $t }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif


{{-- ============================================================
|  STEPS GÉNÉRIQUES — nouveaux steps ajoutés via Filament
|  Affiche les questions/réponses pour tout step non hardcodé ci-dessus
============================================================ --}}
@php
$__autoHandled = ['identity','age','email','phone','best_contact_time','year','brand','model',
                  'renewal_date','usage','km_annuel','address','existing_products','profession',
                  'license_number','consent_profile','consent_marketing','marketing_email','consent_credit'];
@endphp
@foreach($this->buildStepOrderFromDb(['identity' => ['first_name','last_name']]) as $__genId => $__)
    @if(!in_array($__genId, $__autoHandled) && ($step === $__genId || isset($data[$__genId])))
    <div class="messages__item" wire:key="msg-gen-{{ $__genId }}">
        <div class="messages__wrapper">
            <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
            <div class="agent-msg">{{ $this->getQuestion($__genId) }}</div>
        </div>
    </div>
    @if(isset($data[$__genId]))
    <div class="messages__item" wire:key="resp-gen-{{ $__genId }}">
        <div class="user-message" wire:click='goToStep(@json($__genId))'>
            <span>{{ $data[$__genId] }}</span>
            <span class="edit-badge"><i class="fas fa-pen"></i></span>
        </div>
    </div>
    @endif
    @endif
@endforeach


{{-- ============================================================
|  MESSAGE FINAL
============================================================ --}}
@if($step === 'final')
<div class="messages__item" wire:key="msg-finish">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('chat.q_finish') }}</div>
    </div>
</div>
@endif
