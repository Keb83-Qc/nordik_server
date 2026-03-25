{{-- resources/views/livewire/quote-home-steps.blade.php --}}
{{-- Rendu dynamique depuis la DB — ordre respecte sort_order --}}

@php
$__stepOrder = $this->buildStepOrderFromDb(['identity' => ['first_name', 'last_name', 'gender']]);
$__prevOk    = true;

// Helpers réutilisés pour les labels
$__yn = fn($v) => match($v) { 'yes' => __('homechat.btn_yes'), 'no' => __('homechat.btn_no'), default => (string)$v };
@endphp

@foreach($__stepOrder as $__id => $__fields)
@php
$__fieldsArr = is_array($__fields) ? $__fields : [$__fields];
$__answered  = collect($__fieldsArr)->every(fn($f) => isset($data[$f]) && $data[$f] !== '');
$__isCurrent = ($step === $__id);
$__show      = $__prevOk && ($__answered || $__isCurrent);
@endphp

@if($__show)
<div class="messages__item" wire:key="h-msg-{{ $__id }}">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion($__id) }}</div>
    </div>
</div>

@if($__answered)
<div class="messages__item" wire:key="h-resp-{{ $__id }}">
    <div class="user-message" wire:click='goToStep(@json($__id))'>
        <span>
        @if($__id === 'identity')
            {{ ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '') }}
            @if(!empty($data['gender']))
                — {{ __('homechat.gender_'.$data['gender']) }}
            @endif
        @elseif($__id === 'age')
            {{ $data[$__id] }} {{ __('homechat.years_old') }}
        @elseif($__id === 'years_at_address')
            {{ $data[$__id] }} {{ __('homechat.years_old') }}
        @elseif($__id === 'contents_amount')
            {{ number_format((int)$data[$__id], 0, ',', ' ') }} $
        @elseif($__id === 'years_insured')
            @php $k = 'homechat.years_insured_label_'.$data[$__id]; $t = __($k); echo $t === $k ? $data[$__id] : $t; @endphp
        @elseif($__id === 'best_contact_time')
            @php $k = 'homechat.contact_time_'.$data[$__id]; $t = __($k); echo $t === $k ? $data[$__id] : $t; @endphp
        @elseif($__id === 'occupancy')
            {{ $data[$__id] === 'proprietaire' ? __('homechat.btn_owner') : __('homechat.btn_tenant') }}
        @elseif($__id === 'property_type')
            {{ match($data[$__id] ?? '') { 'maison' => __('homechat.btn_house'), 'condo' => __('homechat.btn_condo'), 'appartement' => __('homechat.btn_apartment'), default => $data[$__id] } }}
        @elseif(in_array($__id, ['living_there','electric_baseboard','supp_heating','phone_is_cell','has_ia_products','marketing_email','consent_credit']))
            {{ $__yn($data[$__id]) }}
        @elseif(in_array($__id, ['consent_profile','consent_marketing','marital_status','employment_status','education_level']))
            @php $k = 'homechat.'.$__id.'_'.$data[$__id]; $t = __($k); echo $t === $k ? $data[$__id] : $t; @endphp
        @else
            {{ $data[$__id] ?? '' }}
        @endif
        </span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif
@endif

@php
$__prevOk = $__answered || $this->isStepSkipped($__id);
@endphp
@endforeach
