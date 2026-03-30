<?php

namespace App\Filament\Abf\Resources\AbfCaseResource\Pages;

use App\Filament\Abf\Resources\AbfCaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbfCases extends ListRecords
{
    protected static string $resource = AbfCaseResource::class;

    protected static ?string $title = 'Liste - Analyse de Besoins Financiers';

    public function mount(): void
    {
        $this->redirect(route('abf.landing', ['advisorSlug' => auth()->user()->slug ?? 'conseiller']));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('new')
                ->label('Nouveau dossier ABF')
                ->icon('heroicon-o-plus')
                ->url(route('abf.new', ['advisorSlug' => auth()->user()->slug ?? 'conseiller'])),
        ];
    }
}
