<?php

namespace App\Filament\Resources\BugReportResource\Pages;

use App\Filament\Resources\BugReportResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBugReports extends ListRecords
{
    protected static string $resource = BugReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouveau rapport'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous'),

            'pending' => Tab::make('En attente')
                ->query(fn(Builder $q) => $q->where('status', 'pending'))
                ->icon('heroicon-m-clock')
                ->badgeColor('warning'),

            'in_progress' => Tab::make('En cours')
                ->query(fn(Builder $q) => $q->where('status', 'in_progress'))
                ->icon('heroicon-m-arrow-path')
                ->badgeColor('info'),

            'resolved' => Tab::make('Traités')
                ->query(fn(Builder $q) => $q->whereIn('status', ['resolved', 'closed']))
                ->icon('heroicon-m-check-circle')
                ->badgeColor('success'),
        ];
    }
}
