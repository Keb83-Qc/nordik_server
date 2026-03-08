@php
$livewire = \Filament\Facades\Filament::getCurrentPanel()?->getCurrentPageLivewire();

// On limite l’affichage à la page EditBlogPost
$isBlogEdit = $livewire instanceof \App\Filament\Resources\BlogPostResource\Pages\EditBlogPost;

// Super admin check (méthode sur la page)
$isSuperAdmin = $isBlogEdit && method_exists($livewire, 'isSuperAdmin') && $livewire->isSuperAdmin();
@endphp

@if($isBlogEdit && $isSuperAdmin)
<div class="flex flex-wrap gap-2 items-center mt-2">
    <x-filament::button
        color="gray"
        icon="heroicon-m-chart-bar"
        wire:click="deeplUsage"
        size="sm">
        Quota DeepL
    </x-filament::button>

    <x-filament::button
        color="success"
        icon="heroicon-m-language"
        wire:click="openTranslateModal('es')"
        size="sm">
        Traduire FR → Espagnol (ES)
    </x-filament::button>

    <x-filament::button
        color="warning"
        icon="heroicon-m-language"
        wire:click="openTranslateModal('ht')"
        size="sm">
        Traduire FR → Créole (HT)
    </x-filament::button>
</div>
@endif

@if($isBlogEdit && $isSuperAdmin)
<x-filament::modal id="translateModal" width="2xl">
    <x-slot name="heading">Traduction DeepL (manuel)</x-slot>

    <div class="space-y-4">
        <div class="text-sm text-gray-400">
            Caractères estimés (source FR, HTML retiré):
            <span class="font-semibold text-gray-200">
                {{ $livewire->estimateCharsSource() }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <x-filament::input.checkbox wire:model.defer="translate_title" label="Traduire le titre" />
            <x-filament::input.checkbox wire:model.defer="translate_content" label="Traduire le contenu (HTML)" />
            <x-filament::input.checkbox wire:model.defer="translate_category" label="Traduire la catégorie" />
            <x-filament::input.checkbox wire:model.defer="overwrite_existing" label="Écraser si déjà traduit" />
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <x-filament::button color="gray" wire:click="$dispatch('close-modal', { id: 'translateModal' })">
                Annuler
            </x-filament::button>

            <x-filament::button color="primary" wire:click="runTranslate">
                Confirmer la traduction
            </x-filament::button>
        </div>
    </div>
</x-filament::modal>
@endif