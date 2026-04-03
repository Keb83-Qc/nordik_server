<?php

namespace App\Filament\Resources\ConnectionLogResource\Pages;

use App\Filament\Resources\ConnectionLogResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageConnectionLogs extends ManageRecords
{
    protected static string $resource = ConnectionLogResource::class;

    public function getTabs(): array
    {
        return [
            'toutes' => Tab::make('Toutes')
                ->query(fn(Builder $query) => $query->whereIn('level', ['login', 'login_fail']))
                ->icon('heroicon-m-key'),

            'reussies' => Tab::make('Réussies')
                ->query(fn(Builder $query) => $query->where('level', 'login'))
                ->icon('heroicon-m-check-circle')
                ->badgeColor('success'),

            'echouees' => Tab::make('Échouées')
                ->query(fn(Builder $query) => $query->where('level', 'login_fail'))
                ->icon('heroicon-m-x-circle')
                ->badgeColor('danger'),
        ];
    }
}
