<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Journal d\'activité';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?int    $navigationSort  = 9;
    protected static ?string $modelLabel      = 'Activité';
    protected static ?string $pluralModelLabel = 'Journal d\'activité';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->width('150px'),

                Tables\Columns\TextColumn::make('log_name')
                    ->label('Module')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'user'           => 'info',
                        'abf_case'       => 'warning',
                        'excluded_phone' => 'danger',
                        'taux_commission'=> 'success',
                        'quote_portal'   => 'primary',
                        default          => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'user'           => 'Utilisateur',
                        'abf_case'       => 'Dossier ABF',
                        'excluded_phone' => 'Numéro exclu',
                        'taux_commission'=> 'Taux commission',
                        'quote_portal'   => 'Portail',
                        default          => $state,
                    }),

                Tables\Columns\TextColumn::make('event')
                    ->label('Action')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'created' => 'Créé',
                        'updated' => 'Modifié',
                        'deleted' => 'Supprimé',
                        default   => $state ?? '—',
                    }),

                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Par')
                    ->formatStateUsing(function ($state, Activity $record): string {
                        if (! $record->causer) return '—';
                        return trim(($record->causer->first_name ?? '') . ' ' . ($record->causer->last_name ?? ''))
                            ?: ($record->causer->email ?? '—');
                    })
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHasMorph('causer', '*', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name',  'like', "%{$search}%")
                              ->orWhere('email',      'like', "%{$search}%");
                        });
                    }),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Objet')
                    ->formatStateUsing(function ($state, Activity $record): string {
                        if (! $state) return '—';
                        $short = class_basename($state);
                        $id    = $record->subject_id ? " #{$record->subject_id}" : '';
                        return $short . $id;
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->tooltip(fn (Activity $record): string => $record->description ?? ''),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('Module')
                    ->options([
                        'user'            => 'Utilisateurs',
                        'abf_case'        => 'Dossiers ABF',
                        'excluded_phone'  => 'Numéros exclus',
                        'taux_commission' => 'Taux commissions',
                        'quote_portal'    => 'Portails',
                    ]),

                Tables\Filters\SelectFilter::make('event')
                    ->label('Action')
                    ->options([
                        'created' => 'Créé',
                        'updated' => 'Modifié',
                        'deleted' => 'Supprimé',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->label('Aujourd\'hui')
                    ->query(fn ($query) => $query->whereDate('created_at', today()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('details')
                    ->label('Détails')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('Détails du changement')
                    ->modalContent(fn (Activity $record) => view(
                        'filament.activity-log.details',
                        ['activity' => $record]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer'),
            ])
            ->bulkActions([])
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
