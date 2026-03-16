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
            'tous' => Tab::make('Tous'),

            'connexions' => Tab::make('Connexions')
                ->query(fn(Builder $query) => $query->whereIn('level', ['login', 'login_fail']))
                ->icon('heroicon-m-key')
                ->badgeColor('info'),

            'echecs' => Tab::make('Échecs')
                ->query(fn(Builder $query) => $query->where('level', 'login_fail'))
                ->icon('heroicon-m-exclamation-circle')
                ->badgeColor('danger'),

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
