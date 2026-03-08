@php
/** @var \Livewire\Component|null $lw */
$lw = \Livewire\Livewire::current();

// Sécurité: si pas livewire => rien
if (! $lw) return;

// On veut uniquement EditBlogPost
if (! ($lw instanceof \App\Filament\Resources\BlogPostResource\Pages\EditBlogPost)) {
return;
}

// Sécurité extra: méthodes optionnelles
$isSuperAdmin = method_exists($lw, 'isSuperAdmin') ? (bool) $lw->isSuperAdmin() : false;
if (! $isSuperAdmin) return;

$chars = method_exists($lw, 'estimateCharsSource') ? (int) $lw->estimateCharsSource() : 0;

// IMPORTANT: ne PAS lire $lw->translate_target directement (ça peut crasher selon hydration/opcache)
$target = property_exists($lw, 'translate_target') ? $lw->translate_target : null;
$targetLabel = $target === 'es' ? 'Espagnol (ES)' : ($target === 'ht' ? 'Créole (HT)' : '-');
@endphp

<div class="flex items-center gap-2 flex-wrap justify-end">
    {{-- Quota DeepL --}}
    <x-filament::button
        size="sm"
        color="gray"
        icon="heroicon-m-signal"
        wire:click="deeplUsage"
        type="button">
        Quota DeepL
    </x-filament::button>

    {{-- Traduire ES --}}
    <x-filament::button
        size="sm"
        color="success"
        icon="heroicon-m-language"
        wire:click="openTranslateModal('es')"
        type="button">
        FR → Espagnol (ES)
    </x-filament::button>

    {{-- Traduire HT --}}
    <x-filament::button
        size="sm"
        color="warning"
        icon="heroicon-m-language"
        wire:click="openTranslateModal('ht')"
        type="button">
        FR → Créole (HT)
    </x-filament::button>
</div>

{{-- Modal --}}
<x-filament::modal id="translateModal" width="2xl">
    <x-slot name="heading">
        Traduction DeepL (manuel)
    </x-slot>

    <div class="space-y-4">
        <div class="text-sm text-gray-300">
            Caractères estimés (source FR, HTML retiré): <span class="font-semibold">{{ $chars }}</span>
            <div class="opacity-80 mt-1">Cible: <span class="font-semibold">{{ $targetLabel }}</span></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <label class="flex items-center gap-2">
                <input type="checkbox" class="rounded" wire:model="translate_title">
                <span>Traduire le titre</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="checkbox" class="rounded" wire:model="translate_slug">
                <span>Regénérer le slug</span>
            </label>

            <label class="flex items-center gap-2 md:col-span-2">
                <input type="checkbox" class="rounded" wire:model="translate_content">
                <span>Traduire le contenu (HTML conservé)</span>
            </label>

            <label class="flex items-center gap-2 md:col-span-2">
                <input type="checkbox" class="rounded" wire:model="overwrite_existing">
                <span>Écraser le contenu existant dans la langue cible</span>
            </label>
        </div>
    </div>

    <x-slot name="footerActions">
        <x-filament::button color="gray" wire:click="$dispatch('close-modal', { id: 'translateModal' })">
            Annuler
        </x-filament::button>

        <x-filament::button color="primary" wire:click="confirmTranslate">
            Confirmer la traduction
        </x-filament::button>
    </x-slot>
</x-filament::modal>