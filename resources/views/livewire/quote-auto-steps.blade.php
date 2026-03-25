{{-- resources/views/livewire/quote-auto-steps.blade.php --}}
{{-- Rendu dynamique depuis la DB — ordre respecte sort_order --}}

@php
$__stepOrder  = $this->buildStepOrderFromDb(['identity' => ['first_name', 'last_name']]);
$__prevOk     = true; // première étape toujours éligible
@endphp

@foreach($__stepOrder as $__id => $__fields)
@php
// Détermine si ce step est répondu (tous ses champs remplis)
$__fieldsArr = is_array($__fields) ? $__fields : [$__fields];
$__answered  = collect($__fieldsArr)->every(fn($f) => isset($data[$f]) && $data[$f] !== '');
$__isCurrent = ($step === $__id);
$__show      = $__prevOk && ($__answered || $__isCurrent);
@endphp

@if($__show)
<div class="messages__item" wire:key="msg-{{ $__id }}">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ $this->getQuestion($__id) }}</div>
    </div>
</div>

@if($__answered)
<div class="messages__item" wire:key="resp-{{ $__id }}">
    <div class="user-message" wire:click='goToStep(@json($__id))'>
        <span>
        @if($__id === 'identity')
            {{ ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '') }}
        @elseif($__id === 'age')
            {{ $data[$__id] }} {{ __('chat.years_old') }}
        @elseif($__id === 'best_contact_time')
            @php $k = 'chat.contact_time_'.$data[$__id]; $t = __($k); echo $t === $k ? $data[$__id] : $t; @endphp
        @elseif($__id === 'usage')
            {{ match($data[$__id] ?? '') { 'personnel' => __('chat.btn_personal'), 'commercial' => __('chat.btn_commercial'), default => $data[$__id] } }}
        @elseif($__id === 'existing_products')
            @php $k = 'chat.products_'.$data[$__id]; $t = __($k); echo $t === $k ? $data[$__id] : $t; @endphp
        @elseif($__id === 'license_number')
            @php $lic = $data[$__id] ?? ''; echo ($lic === 'not_provided') ? __('chat.not_provided', [], app()->getLocale()) : $lic; @endphp
        @elseif(in_array($__id, ['consent_profile','consent_marketing','marketing_email','consent_credit']))
            @php $k = 'chat.'.$__id.'_'.$data[$__id]; $t = __($k); echo $t === $k ? $data[$__id] : $t; @endphp
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
// Propagation : un step skippé compte comme "ok" pour le suivant
$__prevOk = $__answered || $this->isStepSkipped($__id);
@endphp
@endforeach


{{-- MESSAGE FINAL --}}
@if($step === 'final')
<div class="messages__item" wire:key="msg-finish">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon"><img src="{{ $agentImage }}"></div>
        <div class="agent-msg">{{ __('chat.q_finish') }}</div>
    </div>
</div>
@endif
