<?php

namespace App\Filament\Pages;

use App\Settings\IntegrationSettings;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;

class IntegrationSettingsPage extends Page
{
    use InteractsWithFormActions;

    protected static ?string $navigationIcon  = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $title           = 'Intégrations';
    protected static string  $view            = 'filament.pages.integration-settings';

    public array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(IntegrationSettings $settings): void
    {
        $this->data = [
            'deepl_api_key'             => $settings->deepl_api_key,
            'deepl_api_url'             => $settings->deepl_api_url,
            'zoho_client_id'            => $settings->zoho_client_id,
            'zoho_client_secret'        => $settings->zoho_client_secret,
            'zoho_refresh_token'        => $settings->zoho_refresh_token,
            'zoho_accounts_url'         => $settings->zoho_accounts_url,
            'zoho_people_base_url'      => $settings->zoho_people_base_url,
            'zoho_people_records_path'  => $settings->zoho_people_records_path,
            'insurance_broker_email'    => $settings->insurance_broker_email,
        ];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('DeepL (traduction)')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('deepl_api_key')
                            ->label('Clé API')
                            ->password()
                            ->revealable(),

                        Forms\Components\TextInput::make('deepl_api_url')
                            ->label('URL de l\'API')
                            ->url(),
                    ]),

                Forms\Components\Section::make('Zoho People')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('zoho_client_id')
                            ->label('Client ID'),

                        Forms\Components\TextInput::make('zoho_client_secret')
                            ->label('Client Secret')
                            ->password()
                            ->revealable(),

                        Forms\Components\TextInput::make('zoho_refresh_token')
                            ->label('Refresh Token')
                            ->password()
                            ->revealable()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('zoho_accounts_url')
                            ->label('Accounts URL')
                            ->url(),

                        Forms\Components\TextInput::make('zoho_people_base_url')
                            ->label('People Base URL')
                            ->url(),

                        Forms\Components\TextInput::make('zoho_people_records_path')
                            ->label('Records Path')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Courtier assurance')
                    ->schema([
                        Forms\Components\TextInput::make('insurance_broker_email')
                            ->label('Courriel du courtier (transfert soumissions assurance)')
                            ->email(),
                    ]),
            ]);
    }

    public function save(IntegrationSettings $settings): void
    {
        $state = $this->form->getState();

        $settings->deepl_api_key            = $state['deepl_api_key'] ?? '';
        $settings->deepl_api_url            = $state['deepl_api_url'] ?? '';
        $settings->zoho_client_id           = $state['zoho_client_id'] ?? '';
        $settings->zoho_client_secret       = $state['zoho_client_secret'] ?? '';
        $settings->zoho_refresh_token       = $state['zoho_refresh_token'] ?? '';
        $settings->zoho_accounts_url        = $state['zoho_accounts_url'] ?? '';
        $settings->zoho_people_base_url     = $state['zoho_people_base_url'] ?? '';
        $settings->zoho_people_records_path = $state['zoho_people_records_path'] ?? '';
        $settings->insurance_broker_email   = $state['insurance_broker_email'] ?? '';

        $settings->save();

        // Appliquer immédiatement sans reboot
        config([
            'services.deepl.key'                  => $settings->deepl_api_key,
            'services.deepl.url'                  => $settings->deepl_api_url,
            'zoho.auth.client_id'                 => $settings->zoho_client_id,
            'zoho.auth.client_secret'             => $settings->zoho_client_secret,
            'zoho.auth.refresh_token'             => $settings->zoho_refresh_token,
            'zoho.auth.accounts_url'              => $settings->zoho_accounts_url,
            'zoho.people.base_url'                => $settings->zoho_people_base_url,
            'zoho.people.records_path'            => $settings->zoho_people_records_path,
            'mail.insurance_broker_email'         => $settings->insurance_broker_email,
        ]);

        Notification::make()
            ->title('Intégrations sauvegardées')
            ->success()
            ->send();
    }
}
