<div class="chat-wrapper">
    <div class="chat-container">

        <div class="messages__item">
            <div class="messages__wrapper">
                @php
                    $img = $agentImage ?? asset('assets/img/VIP_Logo_Gold_Gradient10.png');
                @endphp

                <div class="agent-avatar__icon">
                    <img src="{{ $img }}"
                        onerror="this.src='{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}'">
                </div>

                <div class="agent-msg">
                    {!! __('chat.welcome') !!}
                </div>
            </div>
        </div>

        @if ($step !== 'final')
            <div class="messages__item">
                <div class="messages__wrapper">
                    <div class="agent-avatar__icon">
                        <img src="{{ $img }}"
                            onerror="this.src='{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}'">
                    </div>
                    <div class="agent-msg">
                        {{ $this->getQuestion($step) }}
                    </div>
                </div>
            </div>
        @endif

        @if (!empty($data))
            @foreach ($data as $k => $v)
                @if (!str_starts_with((string) $k, '_') && $v !== '')
                    <div class="messages__item messages__item--operator">
                        <div class="messages__wrapper">
                            <div class="message-text">{{ is_scalar($v) ? $v : json_encode($v) }}</div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

        <div id="chat-end" style="height:1px;"></div>
    </div>

    <div class="response-area">
        <div class="response-container mx-auto" wire:loading.class="opacity-50 pe-none">
            @if ($step === 'final')
                <div wire:key="area-final" class="text-center p-2">
                    <div class="alert alert-success border-0 shadow-sm mb-3">
                        <i class="fas fa-check-circle me-2"></i> {{ __('chat.final_success_msg') }}
                    </div>
                    <button wire:click="finalize" class="btn btn-success btn-lg w-100 py-3 shadow border-0">
                        {{ __('chat.btn_finalize') }}
                    </button>
                </div>
            @else
                @php
                    $__cfg = $this->getStepConfig($step);
                    $__locale = app()->getLocale();
                @endphp
                @if ($__cfg)
                    @php $__opts = $__cfg->options ?? []; @endphp
                    @if ($__cfg->input_type === 'select' && !empty($__opts))
                        <div class="d-grid gap-2" wire:key="area-gen-{{ $step }}">
                            @foreach ($__opts as $__opt)
                                @php
                                    $__val = is_array($__opt) ? $__opt['value'] ?? (string) $__opt : (string) $__opt;
                                    $__label =
                                        is_array($__opt) && isset($__opt['label'])
                                            ? (is_array($__opt['label'])
                                                ? $__opt['label'][$__locale] ?? ($__opt['label']['fr'] ?? $__val)
                                                : $__opt['label'])
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
                            <input type="date" wire:model="genericInput"
                                class="form-control form-control-lg shadow-sm">
                            <button wire:click="submitGenericStep('{{ $step }}')" class="btn btn-primary px-4">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    @else
                        <div class="input-group" wire:key="area-gen-{{ $step }}">
                            <input type="text" wire:model="genericInput"
                                class="form-control form-control-lg shadow-sm"
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
</div>
