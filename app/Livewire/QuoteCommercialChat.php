<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasChatSteps;
use App\Services\LeadDispatcher;
use Livewire\Component;

class QuoteCommercialChat extends Component
{
    use HasChatSteps;

    protected function chatType(): string
    {
        return 'commercial';
    }
    protected function sessionKey(): string
    {
        return 'current_submission_id_commercial';
    }
    protected function defaultAgentImage(): string
    {
        return asset('assets/img/VIP_Logo_Gold_Gradient10.png');
    }

    protected function validSteps(): array
    {
        return array_keys($this->stepOrder());
    }

    protected function stepOrder(): array
    {
        return $this->buildStepOrderFromDb();
    }

    protected function afterPersist(): void
    {
        $this->calculateStep();
    }

    protected function afterHydrate(): void
    {
        $this->calculateStep();
    }

    public function mount(LeadDispatcher $dispatcher)
    {
        $this->mountChat($dispatcher);
    }

    public function render()
    {
        return view('livewire.quote.commercial-chat');
    }
}
