{{-- resources/views/livewire/partials/qa.blade.php --}}

<div class="messages__item" wire:key="q-{{ $baseKey }}">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon">
            <img src="{{ $agentImage }}" onerror="this.src='{{ asset('assets/img/agent-default.jpg') }}'">
        </div>
        <div class="agent-msg">
            {{ __($questionKey) }}
        </div>
    </div>
</div>

@if(!is_null($answerText) && $answerText !== '')
<div class="messages__item" wire:key="a-{{ $baseKey }}">
    <div class="user-message" wire:click="goToStep('{{ $goTo }}')">
        <span>{{ $answerText }}</span>
        <span class="edit-badge"><i class="fas fa-pen"></i></span>
    </div>
</div>
@endif