<?php

namespace App\Filament\Resources\EmailLogResource\Pages;

use App\Filament\Resources\EmailLogResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageEmailLogs extends ManageRecords
{
    protected static string $resource = EmailLogResource::class;

    public function getTabs(): array
    {
        return [
            'tous' => Tab::make('Tous les emails')
                ->query(fn(Builder $query) => $query->where('source', 'like', 'email_%'))
                ->icon('heroicon-m-envelope'),

            'email_internal' => Tab::make('Soumissions internes')
                ->query(fn(Builder $query) => $query->where('source', 'email_internal'))
                ->icon('heroicon-m-inbox-arrow-down')
                ->badgeColor('success'),

            'email_partner' => Tab::make('Soumissions partenaires')
                ->query(fn(Builder $query) => $query->where('source', 'email_partner'))
                ->icon('heroicon-m-building-office')
                ->badgeColor('info'),

            'email_security' => Tab::make('Sécurité & accès')
                ->query(fn(Builder $query) => $query->where('source', 'email_security'))
                ->icon('heroicon-m-shield-check')
                ->badgeColor('warning'),

            'email_abf' => Tab::make('Profil financier')
                ->query(fn(Builder $query) => $query->where('source', 'email_abf'))
                ->icon('heroicon-m-chart-bar')
                ->badgeColor('primary'),

            'email_alert' => Tab::make('Alertes système')
                ->query(fn(Builder $query) => $query->where('source', 'email_alert'))
                ->icon('heroicon-m-bell-alert')
                ->badgeColor('danger'),

            'email_advisor' => Tab::make('Liens conseillers')
                ->query(fn(Builder $query) => $query->where('source', 'email_advisor'))
                ->icon('heroicon-m-user-group')
                ->badgeColor('success'),
        ];
    }
}
