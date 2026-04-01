<?php

namespace App\Filament\Resources\AbfRecommendationResource\Pages;

use App\Filament\Resources\AbfRecommendationResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListAbfRecommendations extends ListRecords
{
    protected static string $resource = AbfRecommendationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle recommandation'),
        ];
    }

    public function getTabs(): array
    {
        $cats = AbfRecommendationResource::getCategoryOptions();
        $tabs = ['all' => Tab::make('Toutes')];
        foreach ($cats as $value => $label) {
            $tabs[$value] = Tab::make($label)
                ->modifyQueryUsing(fn ($query) => $query->where('category', $value))
                ->badge(fn () => \App\Models\AbfRecommendation::where('category', $value)->where('is_active', true)->count());
        }
        return $tabs;
    }
}
