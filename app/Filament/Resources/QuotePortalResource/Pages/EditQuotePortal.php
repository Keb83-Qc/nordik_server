<?php

namespace App\Filament\Resources\QuotePortalResource\Pages;

use App\Filament\Resources\QuotePortalResource;
use App\Models\QuotePortal;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditQuotePortal extends EditRecord
{
    protected static string $resource = QuotePortalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Affiche l'URL du portail (pour les partenaires)
            Actions\Action::make('view_url')
                ->label('Voir URL publique')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn () => $this->record->type === 'partner'
                    ? url('/fr/p/' . $this->record->slug . '/quote')
                    : url('/fr/quote')
                )
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->type !== 'internal')
                ->before(function (QuotePortal $record) {
                    if ($record->type === 'internal') {
                        Notification::make()
                            ->title('Impossible de supprimer le portail interne')
                            ->danger()
                            ->send();
                        $this->halt();
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
