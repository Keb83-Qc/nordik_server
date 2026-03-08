<x-filament::section heading="Dernières procédures" icon="heroicon-o-clipboard-document-list">
    <div class="space-y-3 text-sm">
        <div class="flex items-start justify-between gap-3">
            <div>
                <div class="font-semibold">Procédure - Mise à jour KYC</div>
                <div class="text-gray-500">Publié le {{ now()->subDays(3)->format('d/m/Y') }}</div>
            </div>
            <a class="fi-link" href="#" target="_blank">Voir</a>
        </div>

        <div class="flex items-start justify-between gap-3">
            <div>
                <div class="font-semibold">Procédure - Demande de financement</div>
                <div class="text-gray-500">Publié le {{ now()->subDays(10)->format('d/m/Y') }}</div>
            </div>
            <a class="fi-link" href="#" target="_blank">Voir</a>
        </div>
    </div>
</x-filament::section>