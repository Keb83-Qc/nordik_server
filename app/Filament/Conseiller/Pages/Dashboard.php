<?php

namespace App\Filament\Conseiller\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Tableau de bord';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\WelcomeOverview::class,
            \App\Filament\Widgets\DocumentsWidget::class,
            \App\Filament\Widgets\LatestWikiWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        // Layout premium : 1 colonne sur mobile, 2/3/4 sur grand écran
        return [
            'default' => 1,
            'md' => 1,
            'xl' => 1,
            '2xl' => 1,
        ];
    }
}
