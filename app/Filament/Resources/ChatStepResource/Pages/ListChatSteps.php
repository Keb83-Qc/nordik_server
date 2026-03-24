<?php

namespace App\Filament\Resources\ChatStepResource\Pages;

use App\Filament\Resources\ChatStepResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChatSteps extends ListRecords
{
    protected static string $resource = ChatStepResource::class;

    public function getTabs(): array
    {
        return [
            'auto' => ListRecords\Tab::make('Auto')
                ->modifyQueryUsing(fn ($query) => $query->where('chat_type', 'auto')),

            'habitation' => ListRecords\Tab::make('Habitation')
                ->modifyQueryUsing(fn ($query) => $query->where('chat_type', 'habitation')),

            'bundle' => ListRecords\Tab::make('Bundle (Auto + Habitation)')
                ->modifyQueryUsing(fn ($query) => $query->where('chat_type', 'bundle')),

            'commercial' => ListRecords\Tab::make('Commercial')
                ->modifyQueryUsing(fn ($query) => $query->where('chat_type', 'commercial')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
