<x-filament-panels::page>
    <div>
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button wire:click="save" color="primary">
                Sauvegarder
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
