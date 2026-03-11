@php
$yn = function($v) {
return match($v) {
'yes' => __('homechat.btn_yes'),
'no' => __('homechat.btn_no'),
default => (string)$v,
};
};

$labelYearsInsured = function($v) {
$key = 'homechat.years_insured_label_'.$v;
$t = __($key);
return ($t === $key) ? (string)$v : $t;
};

$labelOcc = ($data['occupancy'] ?? '') === 'proprietaire' ? __('homechat.btn_owner') : __('homechat.btn_tenant');

$labelProp = match($data['property_type'] ?? '') {
'maison' => __('homechat.btn_house'),
'condo' => __('homechat.btn_condo'),
'appartement' => __('homechat.btn_apartment'),
default => '',
};
@endphp

{{-- IDENTITÉ — première étape --}}
@if($step === 'identity' || isset($data['first_name']) || isset($data['gender']))
<div class="messages__item" wire:key="h-msg-id">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_identity') }}</div>
    </div>
</div>

@if(isset($data['first_name']) && isset($data['last_name']) && isset($data['gender']))
<div class="messages__item" wire:key="h-resp-id">
    <div class="user-message" wire:click="goToStep('identity')">
        <span>{{ $data['first_name'] }} {{ $data['last_name'] }} — {{ __('homechat.gender_'.$data['gender']) }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- AGE --}}
@if(isset($data['first_name']) && ($step === 'age' || isset($data['age'])))
<div class="messages__item" wire:key="h-msg-age">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_age') }}</div>
    </div>
</div>

@if(isset($data['age']))
<div class="messages__item" wire:key="h-resp-age">
    <div class="user-message" wire:click="goToStep('age')">
        <span>{{ $data['age'] }} {{ __('homechat.years_old') }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- EMAIL --}}
@if(isset($data['age']) && ($step === 'email' || isset($data['email'])))
<div class="messages__item" wire:key="h-msg-email">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_email') }}</div>
    </div>
</div>
@if(isset($data['email']))
<div class="messages__item" wire:key="h-resp-email">
    <div class="user-message" wire:click="goToStep('email')">
        <span>{{ $data['email'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- TÉL --}}
@if(isset($data['email']) && ($step === 'phone' || isset($data['phone'])))
<div class="messages__item" wire:key="h-msg-phone">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_phone') }}</div>
    </div>
</div>
@if(isset($data['phone']))
<div class="messages__item" wire:key="h-resp-phone">
    <div class="user-message" wire:click="goToStep('phone')">
        <span>{{ $data['phone'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- CELL? --}}
@if(isset($data['phone']) && ($step === 'phone_is_cell' || isset($data['phone_is_cell'])))
<div class="messages__item" wire:key="h-msg-cell">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_phone_is_cell') }}</div>
    </div>
</div>
@if(isset($data['phone_is_cell']))
<div class="messages__item" wire:key="h-resp-cell">
    <div class="user-message" wire:click="goToStep('phone_is_cell')">
        <span>{{ $yn($data['phone_is_cell']) }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- MEILLEUR MOMENT DE CONTACT --}}
@if(isset($data['phone_is_cell']) && ($step === 'best_contact_time' || isset($data['best_contact_time'])))
<div class="messages__item" wire:key="h-msg-contact-time">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_best_contact_time') }}</div>
    </div>
</div>
@if(isset($data['best_contact_time']))
<div class="messages__item" wire:key="h-resp-contact-time">
    <div class="user-message" wire:click="goToStep('best_contact_time')">
        @php
        $contactTimeKey = 'homechat.contact_time_' . $data['best_contact_time'];
        $contactTimeLabel = __($contactTimeKey);
        if ($contactTimeLabel === $contactTimeKey) $contactTimeLabel = $data['best_contact_time'];
        @endphp
        <span>{{ $contactTimeLabel }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- OCCUPATION --}}
@if(isset($data['best_contact_time']) && ($step === 'occupancy' || isset($data['occupancy'])))
<div class="messages__item" wire:key="h-msg-occ">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_occupancy') }}</div>
    </div>
</div>
@if(isset($data['occupancy']))
<div class="messages__item" wire:key="h-resp-occ">
    <div class="user-message" wire:click="goToStep('occupancy')">
        <span>{{ $labelOcc }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- TYPE DE PROPRIÉTÉ --}}
@if(isset($data['occupancy']) && ($step === 'property_type' || isset($data['property_type'])))
<div class="messages__item" wire:key="h-msg-ptype">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_property_type') }}</div>
    </div>
</div>
@if(isset($data['property_type']))
<div class="messages__item" wire:key="h-resp-ptype">
    <div class="user-message" wire:click="goToStep('property_type')">
        <span>{{ $labelProp }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- ADRESSE --}}
@if(isset($data['property_type']) && ($step === 'address' || isset($data['address'])))
<div class="messages__item" wire:key="h-msg-addr">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_address') }}</div>
    </div>
</div>
@if(isset($data['address']))
<div class="messages__item" wire:key="h-resp-addr">
    <div class="user-message" wire:click="goToStep('address')">
        <span>{{ $data['address'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- DATE DE RENOUVELLEMENT HABITATION --}}
@if(isset($data['address']) && ($step === 'hab_renewal_date' || isset($data['hab_renewal_date'])))
<div class="messages__item" wire:key="h-msg-hab-renewal">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_hab_renewal_date') }}</div>
    </div>
</div>
@if(isset($data['hab_renewal_date']))
<div class="messages__item" wire:key="h-resp-hab-renewal">
    <div class="user-message" wire:click="goToStep('hab_renewal_date')">
        <span>{{ $data['hab_renewal_date'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- VIT À CETTE ADRESSE --}}
@if(isset($data['hab_renewal_date']) && ($step === 'living_there' || isset($data['living_there'])))
<div class="messages__item" wire:key="h-msg-live">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_living_there') }}</div>
    </div>
</div>
@if(isset($data['living_there']))
<div class="messages__item" wire:key="h-resp-live">
    <div class="user-message" wire:click="goToStep('living_there')">
        <span>{{ $yn($data['living_there']) }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- DATE D'EMMÉNAGEMENT (si living_there = yes) --}}
@if(isset($data['living_there']) && ($step === 'move_in_date' || isset($data['move_in_date'])))
@if(($data['living_there'] ?? '') === 'yes')
<div class="messages__item" wire:key="h-msg-move">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_move_in_date') }}</div>
    </div>
</div>
@if(isset($data['move_in_date']))
<div class="messages__item" wire:key="h-resp-move">
    <div class="user-message" wire:click="goToStep('move_in_date')">
        <span>{{ $data['move_in_date'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif
@endif

{{-- NB D'UNITÉS --}}
@if(isset($data['living_there']) && ($step === 'units_in_building' || isset($data['units_in_building'])))
<div class="messages__item" wire:key="h-msg-units">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_units') }}</div>
    </div>
</div>
@if(isset($data['units_in_building']))
<div class="messages__item" wire:key="h-resp-units">
    <div class="user-message" wire:click="goToStep('units_in_building')">
        <span>{{ $data['units_in_building'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- MONTANT DES BIENS --}}
@if(isset($data['units_in_building']) && ($step === 'contents_amount' || isset($data['contents_amount'])))
<div class="messages__item" wire:key="h-msg-cont">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_contents_amount') }}</div>
    </div>
</div>
@if(isset($data['contents_amount']))
<div class="messages__item" wire:key="h-resp-cont">
    <div class="user-message" wire:click="goToStep('contents_amount')">
        <span>{{ number_format((int)$data['contents_amount'], 0, ',', ' ') }} $</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- CHAUFFAGE PRINCIPAL --}}
@if(isset($data['contents_amount']) && ($step === 'electric_baseboard' || isset($data['electric_baseboard'])))
<div class="messages__item" wire:key="h-msg-heat1">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_electric_baseboard') }}</div>
    </div>
</div>
@if(isset($data['electric_baseboard']))
<div class="messages__item" wire:key="h-resp-heat1">
    <div class="user-message" wire:click="goToStep('electric_baseboard')">
        <span>{{ $yn($data['electric_baseboard']) }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- CHAUFFAGE D'APPOINT --}}
@if(isset($data['electric_baseboard']) && ($step === 'supp_heating' || isset($data['supp_heating'])))
<div class="messages__item" wire:key="h-msg-heat2">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_supp_heating') }}</div>
    </div>
</div>
@if(isset($data['supp_heating']))
<div class="messages__item" wire:key="h-resp-heat2">
    <div class="user-message" wire:click="goToStep('supp_heating')">
        <span>{{ $yn($data['supp_heating']) }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- ANNÉES ASSURANCE --}}
@if(isset($data['supp_heating']) && ($step === 'years_insured' || isset($data['years_insured'])))
<div class="messages__item" wire:key="h-msg-yrsins">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_years_insured') }}</div>
    </div>
</div>
@if(isset($data['years_insured']))
<div class="messages__item" wire:key="h-resp-yrsins">
    <div class="user-message" wire:click="goToStep('years_insured')">
        <span>{{ $labelYearsInsured($data['years_insured']) }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- ANNÉES CHEZ ASSUREUR --}}
@if(isset($data['years_insured']) && ($step === 'years_with_insurer' || isset($data['years_with_insurer'])))
<div class="messages__item" wire:key="h-msg-yrswith">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_years_with_insurer') }}</div>
    </div>
</div>
@if(isset($data['years_with_insurer']))
<div class="messages__item" wire:key="h-resp-yrswith">
    <div class="user-message" wire:click="goToStep('years_with_insurer')">
        <span>{{ $data['years_with_insurer'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- ASSUREUR ACTUEL --}}
@if(isset($data['years_with_insurer']) && ($step === 'current_insurer' || isset($data['current_insurer'])))
<div class="messages__item" wire:key="h-msg-ins">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.q_current_insurer') }}</div>
    </div>
</div>
@if(isset($data['current_insurer']))
<div class="messages__item" wire:key="h-resp-ins">
    <div class="user-message" wire:click="goToStep('current_insurer')">
        <span>{{ $data['current_insurer'] }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

{{-- STATUTS / CONSENTEMENTS --}}
@php
$pairs = [
['marital_status','q_marital_status'],
['employment_status','q_employment_status'],
['education_level','q_education_level'],
['industry','q_industry'],
['has_ia_products','q_has_ia_products'],
['consent_profile','q_consent_profile'],
['consent_marketing','q_consent_marketing'],
['marketing_email','q_marketing_email'],
['consent_credit','q_consent_credit'],
];
$profileVisible = isset($data['current_insurer']);
@endphp

@foreach($pairs as [$field,$qKey])
@if($profileVisible && (isset($data[$field]) || $step === $field))
<div class="messages__item" wire:key="h-msg-{{ $field }}">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('homechat.'.$qKey) }}</div>
    </div>
</div>

@if(isset($data[$field]))
<div class="messages__item" wire:key="h-resp-{{ $field }}">
    <div class="user-message" wire:click='goToStep(@json($field))'>
        <span>
            @php
            $k = 'homechat.'.$field.'_'.$data[$field];
            $t = __($k);
            echo ($t === $k) ? $data[$field] : $t;
            @endphp
        </span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif
@endforeach

