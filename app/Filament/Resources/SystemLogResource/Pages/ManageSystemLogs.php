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

            // ── Emails envoyés ──────────────────────────────────────────────
            'emails' => Tab::make('Tous les emails')
                ->query(fn(Builder $query) => $query->where('source', 'like', 'email_%'))
                ->icon('heroicon-m-envelope')
                ->badgeColor('primary'),

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
                ->badgeColor('info'),

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
