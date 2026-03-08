<?php

namespace App\Filament\Pages;

use App\Settings\MailSettings;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Spatie\LaravelSettings\Settings;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;

class MailSettingsPage extends Page
{
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $title = 'Emails (Prod/Test)';
    protected static string $view = 'filament.pages.mail-settings';

    public array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->isSuperAdmin() ?? false;
    }

    public function mount(MailSettings $settings): void
    {
        $this->data = [
            'submission_to' => $settings->submission_to,
            'test_mode' => $settings->test_mode,
            'test_to' => $settings->test_to,
        ];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('Destinataires')
                    ->schema([
                        Forms\Components\TextInput::make('submission_to')
                            ->label('Destinataire (Production)')
                            ->email()
                            ->required(),

                        Forms\Components\Toggle::make('test_mode')
                            ->label('Mode test (forcer les emails)'),

                        Forms\Components\TextInput::make('test_to')
                            ->label('Destinataire en test (override)')
                            ->email()
                            ->helperText('Si vide, on utilise le destinataire de production.')
                            ->visible(fn($get) => (bool) $get('test_mode')),
                    ]),
            ]);
    }

    public function save(MailSettings $settings): void
    {
        $state = $this->form->getState();

        $settings->submission_to = $state['submission_to'] ?? $settings->submission_to;
        $settings->test_mode = (bool) ($state['test_mode'] ?? false);

        // Choix A: garder test_to nullable (recommandé, voir plus bas)
        $settings->test_to = filled($state['test_to'] ?? null) ? $state['test_to'] : null;

        $settings->save();

        Notification::make()
            ->title('Paramètres email sauvegardés')
            ->success()
            ->send();
    }
}
