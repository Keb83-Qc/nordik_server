<?php

namespace App\Filament\Resources\ChatStepResource\Pages;

use App\Filament\Resources\ChatStepResource;
use App\Models\QuoteType;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChatSteps extends ListRecords
{
    protected static string $resource = ChatStepResource::class;

    /**
     * Onglets générés dynamiquement depuis la table quote_types.
     * Ajouter un nouveau type de soumission crée automatiquement son onglet ici.
     */
    public function getTabs(): array
    {
        $tabs = [
            'all' => ListRecords\Tab::make('Tous'),
        ];

        $quoteTypes = QuoteType::orderBy('sort_order')->get();

        foreach ($quoteTypes as $quoteType) {
            $tabs[$quoteType->slug] = ListRecords\Tab::make($quoteType->getLabel('fr'))
                ->modifyQueryUsing(fn ($query) => $query->where('quote_type_id', $quoteType->id));
        }

        return $tabs;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
