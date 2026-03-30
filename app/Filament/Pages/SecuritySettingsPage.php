<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Support\Facades\Cache;

class SecuritySettingsPage extends Page
{
    use InteractsWithFormActions;

    protected static ?string $navigationIcon  = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $title           = 'Sécurité';
    protected static string  $view            = 'filament.pages.security-settings';

    public array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->data = [
            'two_factor_enabled' => \App\Models\Setting::where('key', 'two_factor_enabled')->value('value') === '1',
        ];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('Authentification à deux facteurs (2FA)')
                    ->description('Lorsque activé, les administrateurs doivent configurer et valider une application TOTP (Google Authenticator, Authy…) pour accéder au panneau.')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->schema([
                        Forms\Components\Toggle::make('two_factor_enabled')
                            ->label('Activer le 2FA pour les administrateurs')
                            ->helperText('S\'applique aux rôles admin et super_admin uniquement. Les conseillers ne sont pas affectés.')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $value = ($state['two_factor_enabled'] ?? false) ? '1' : '0';

        \App\Models\Setting::updateOrCreate(
            ['key' => 'two_factor_enabled'],
            ['value' => $value]
        );

        // Invalide le cache pour que le middleware TwoFactorAuth le prenne en compte immédiatement
        Cache::forget('setting_two_factor_enabled');

        Notification::make()
            ->title('Paramètres de sécurité sauvegardés')
            ->success()
            ->send();
    }
}
