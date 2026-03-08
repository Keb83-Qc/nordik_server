<?php

namespace App\Filament\Abf\Resources;

use App\Filament\Abf\Resources\AbfCaseResource\Pages;
use App\Filament\Abf\Resources\AbfCaseResource\Steps\AssetsLiabilitiesStep;
use App\Filament\Abf\Resources\AbfCaseResource\Steps\DeathBudgetStep;
use App\Filament\Abf\Resources\AbfCaseResource\Steps\DossierStep;
use App\Filament\Abf\Resources\AbfCaseResource\Steps\HouseholdStep;
use App\Filament\Abf\Resources\AbfCaseResource\Steps\ObjectivesStep;
use App\Filament\Abf\Resources\AbfCaseResource\Steps\ProtectionsStep;
use App\Filament\Abf\Resources\AbfCaseResource\Steps\SummaryStep;
use App\Models\AbfCase;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbfCaseResource extends Resource
{
    protected static ?string $model = AbfCase::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('advisor_user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                DossierStep::make(),
                HouseholdStep::make(),
                ObjectivesStep::make(),
                AssetsLiabilitiesStep::make(),
                ProtectionsStep::make(),
                DeathBudgetStep::make(),
                SummaryStep::make(),
            ])
                ->extraAttributes(['class' => 'abf-wizard'])
                ->persistStepInQueryString('step')
                ->contained(false)
                ->columnSpanFull()
                ->skippable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->state(fn(AbfCase $record) => trim((string) data_get($record->payload, 'client.first_name', '') . ' ' . (string) data_get($record->payload, 'client.last_name', '')) ?: '—')
                    ->searchable(),
                Tables\Columns\IconColumn::make('has_spouse')
                    ->label('Conjoint')
                    ->boolean()
                    ->state(fn(AbfCase $record) => (bool) data_get($record->payload, 'has_spouse', false)),
                Tables\Columns\TextColumn::make('progress_percent')
                    ->label('Progression')
                    ->state(fn(AbfCase $record) => $record->progress_percent)
                    ->suffix('%')
                    ->badge()
                    ->color(fn(AbfCase $record) => match (true) {
                        ($record->progress_percent ?? 0) >= 90 => 'success',
                        ($record->progress_percent ?? 0) >= 50 => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'draft' => 'gray',
                        'completed' => 'warning',
                        'signed' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Dernière modif')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ouvrir'),
                Action::make('open_abf_panel')
                    ->label('Ouvrir ABF (nouvel onglet)')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn($record) => url("/abf/abf-cases/{$record->id}/edit"))
                    ->openUrlInNewTab(),
                Action::make('pdf')
                    ->label('Télécharger PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn($record) => route('abf.pdf', [
                        'locale' => app()->getLocale(),
                        'abfCase' => $record,
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbfCases::route('/'),
            'create' => Pages\CreateAbfCase::route('/create'),
            'edit' => Pages\EditAbfCase::route('/{record}/edit'),
        ];
    }
}
