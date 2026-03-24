<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}
        <x-filament::button type="submit" icon="heroicon-o-check">
            Sauvegarder
        </x-filament::button>
    </form>
</x-filament-panels::page>
