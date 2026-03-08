<?php

namespace App\Filament\Abf\Resources\AbfCaseResource\Steps;

use App\Models\AbfCase;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Wizard\Step;

final class DossierStep
{
    public static function make(): Step
    {
        return Step::make('Dossier')
            ->id('dossier')
            ->schema([
                Grid::make(3)->schema([
                    TextInput::make('advisor_code')
                        ->label('Code conseiller')
                        ->disabled()
                        ->dehydrated()
                        ->default(fn() => auth()->user()?->advisor_code),

                    Placeholder::make('advisor_name_display')
                        ->label('Conseiller')
                        ->content(fn(?AbfCase $record) => $record?->advisor?->full_name ?? auth()->user()?->full_name ?? '-'),

                    Select::make('status')
                        ->label('Statut')
                        ->options([
                            'draft' => 'Brouillon',
                            'completed' => 'Complété',
                            'signed' => 'Signé',
                        ])
                        ->default('draft')
                        ->required(),
                ]),

                Section::make('Identification du dossier (PDF)')
                    ->columns(3)
                    ->schema([
                        TextInput::make('payload.document_meta.client_display_name')
                            ->label('Client (affichage dossier / couverture PDF)'),
                        TextInput::make('payload.document_meta.representative_display_name')
                            ->label('Représentant (affichage PDF)')
                            ->default(fn() => auth()->user()?->full_name),
                        DatePicker::make('payload.document_meta.document_date')
                            ->label('Date (PDF)'),
                    ]),

                Textarea::make('payload.context.notes')
                    ->label('Notes (dossier)')
                    ->rows(4),
            ]);
    }
}
