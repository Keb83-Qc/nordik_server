<?php

namespace App\Filament\Abf\Resources\AbfCaseResource\Steps;

use App\Filament\Abf\Forms\GoalsSchema;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Wizard\Step;

final class ObjectivesStep
{
    public static function make(): Step
    {
        return Step::make('Projets & objectifs')
            ->id('objectifs')
            ->schema([
                Section::make('Objectifs')
                    ->schema([
                        CheckboxList::make('payload.goals.selected')
                            ->label('Sélectionnez vos objectifs (minimum 3)')
                            ->options(GoalsSchema::options())
                            ->columns(2)
                            ->live()
                            ->required()
                            ->minItems(3)
                            ->helperText('Veuillez sélectionner au moins 3 objectifs.')
                            ->validationMessages([
                                'required' => 'Veuillez sélectionner au moins 3 objectifs.',
                                'min' => 'Veuillez sélectionner au moins 3 objectifs.',
                            ]),

                        Section::make('Réponses du client')
                            ->columns(2)
                            ->schema(GoalsSchema::answerFields()),

                        Textarea::make('payload.goals.general_notes')
                            ->label('Note générale (optionnel)')
                            ->rows(3),
                    ]),
            ]);
    }
}
