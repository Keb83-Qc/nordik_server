<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}
        <div class="flex gap-3">
            <x-filament::button type="submit">
                Sauvegarder
            </x-filament::button>
            <x-filament::button type="button" wire:click="testMail" color="gray">
                Envoyer un courriel de test
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
