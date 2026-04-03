<?php

namespace App\Filament\Resources\SystemLogResource\Pages;

use App\Filament\Resources\SystemLogResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Components\Tab; // <--- Import Important
use Illuminate\Database\Eloquent\Builder;

class ManageSystemLogs extends ManageRecords
{
    protected static string $resource = SystemLogResource::class;

    public function getTabs(): array
    {
        return [
            // Tous les logs système (exclut emails et connexions)
            'tous' => Tab::make('Tous')
                ->query(fn(Builder $query) => $query
                    ->where('source', 'not like', 'email_%')
                    ->whereNotIn('level', ['login', 'login_fail'])),

            'info' => Tab::make('Infos')
                ->query(fn(Builder $query) => $query->where('level', 'info'))
                ->icon('heroicon-m-information-circle')
                ->badgeColor('info'),

            'update' => Tab::make('Mises à jour')
                ->query(fn(Builder $query) => $query->where('level', 'update'))
                ->icon('heroicon-m-arrow-path')
                ->badgeColor('success'),

            'error' => Tab::make('Erreurs')
                ->query(fn(Builder $query) => $query->where('level', 'error'))
                ->icon('heroicon-m-exclamation-triangle')
                ->badgeColor('warning'),

            'fatal' => Tab::make('Fatal')
                ->query(fn(Builder $query) => $query->where('level', 'fatal'))
                ->icon('heroicon-m-x-circle')
                ->badgeColor('danger'),
        ];
    }
}
