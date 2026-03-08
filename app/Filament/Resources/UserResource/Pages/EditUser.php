<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\UserLoginDetails;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * ✅ Actions en HAUT (header)
     * - Retour (au lieu d'annuler)
     * - Sauvegarder
     * - Envoyer identifiants
     * - Supprimer
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Retour')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),

            Actions\Action::make('send_credentials')
                ->label('Envoyer identifiants')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Réinitialiser et envoyer les accès ?')
                ->modalDescription('Un nouveau mot de passe temporaire sera généré, l’ancien ne fonctionnera plus. Le conseiller devra le changer au prochain login.')
                ->modalSubmitActionLabel('Envoyer')
                ->action(function (): void {
                    /** @var User $record */
                    $record = $this->getRecord();

                    $tempPassword = Str::random(10);
                    if (blank($record->email)) {
                        Notification::make()->title("Aucun email")->danger()->send();
                        return;
                    }

                    $record->update([
                        'password' => Hash::make($tempPassword),
                        'must_change_password' => true,
                    ]);

                    try {
                        Mail::to($record->email)->send(new UserLoginDetails($record, $tempPassword));

                        Notification::make()
                            ->title('Courriel envoyé avec succès')
                            ->body("Mot de passe temporaire : {$tempPassword}")
                            ->success()
                            ->persistent()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title("Erreur lors de l'envoi")
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('save')
                ->label('Sauvegarder')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),

            Actions\DeleteAction::make()
                ->label('Supprimer')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->successRedirectUrl($this->getResource()::getUrl('index')),
        ];
    }

    /**
     * ✅ On enlève les actions en bas (plus besoin de scroller)
     */
    protected function getFormActions(): array
    {
        return [];
    }
}
