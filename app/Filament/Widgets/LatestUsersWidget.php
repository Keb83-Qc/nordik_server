<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestUsersWidget extends BaseWidget
{
    // On peut masquer ce widget pour les non-admins si besoin
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->role_id == 1;
    }

    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 2; // Prend 1 colonne

    protected static ?string $heading = 'Derniers Inscrits';
    protected static string $view = 'filament.widgets.latest-users-widget';

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->with('role')->latest()->limit(5)) // Les 5 derniers
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->circular()
                    ->label(''),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Nom')
                    ->description(fn(User $record) => $record->email),
                Tables\Columns\TextColumn::make('role.name')
                    ->badge()
                    ->color('primary')
                    ->label('Rôle'),
            ])
            ->paginated(false); // Pas de pagination, juste une liste
    }
}
