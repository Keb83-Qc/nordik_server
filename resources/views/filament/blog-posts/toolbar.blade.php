<div class="flex flex-wrap gap-2 items-center">
    <x-filament::button
        color="gray"
        icon="heroicon-m-chart-bar"
        wire:click="deeplUsage">
        Quota DeepL
    </x-filament::button>

    <x-filament::button
        color="success"
        icon="heroicon-m-language"
        wire:click="openTranslateModal('es')">
        Traduire FR → Espagnol (ES)
    </x-filament::button>

    <x-filament::button
        color="warning"
        icon="heroicon-m-language"
        wire:click="openTranslateModal('ht')">
        Traduire FR → Créole (HT)
    </x-filament::button>
</div>