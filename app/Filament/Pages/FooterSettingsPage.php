<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FooterSettingsPage extends Page
{
    use InteractsWithFormActions;

    protected static ?string $navigationIcon  = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Site Web';
    protected static ?string $navigationLabel = 'Pied de page (Footer)';
    protected static ?string $title           = 'Gestion du pied de page';
    protected static string  $view            = 'filament.pages.footer-settings';

    public array $data = [];

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public function mount(): void
    {
        $settings = DB::table('settings')->pluck('setting_value', 'setting_key')->toArray();

        $this->data = [
            'footer_copyright'    => $settings['footer_copyright']    ?? '',
            'footer_description'  => $settings['footer_description']  ?? '',
            'footer_description_en' => $settings['footer_description_en'] ?? '',
            'site_address'        => $settings['site_address']        ?? '',
            'site_phone'          => $settings['site_phone']          ?? '',
            'site_email'          => $settings['site_email']          ?? '',
            'facebook_url'        => $settings['facebook_url']        ?? '',
            'linkedin_url'        => $settings['linkedin_url']        ?? '',
        ];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('Informations légales')
                    ->icon('heroicon-o-document-text')
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('footer_copyright')
                            ->label('Texte copyright')
                            ->placeholder('VIP Services Financiers Inc.')
                            ->helperText('Affiché après © 2025 dans le bas de page.')
                            ->maxLength(200),
                    ]),

                Forms\Components\Section::make('Description')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Textarea::make('footer_description')
                            ->label('Description (Français)')
                            ->rows(3)
                            ->maxLength(500),

                        Forms\Components\Textarea::make('footer_description_en')
                            ->label('Description (Anglais)')
                            ->rows(3)
                            ->maxLength(500),
                    ]),

                Forms\Components\Section::make('Coordonnées')
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('site_address')
                            ->label('Adresse')
                            ->placeholder('2990 av. Pierre-Péladeau, Suite 400, Laval, QC H7T 3B3')
                            ->columnSpanFull()
                            ->maxLength(300),

                        Forms\Components\TextInput::make('site_phone')
                            ->label('Téléphone')
                            ->placeholder('579 640-3334')
                            ->tel()
                            ->maxLength(30),

                        Forms\Components\TextInput::make('site_email')
                            ->label('Courriel')
                            ->email()
                            ->placeholder('admin@vipgpi.ca')
                            ->maxLength(100),
                    ]),

                Forms\Components\Section::make('Réseaux sociaux')
                    ->icon('heroicon-o-share')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('facebook_url')
                            ->label('URL Facebook')
                            ->url()
                            ->placeholder('https://facebook.com/vipgpi')
                            ->maxLength(300),

                        Forms\Components\TextInput::make('linkedin_url')
                            ->label('URL LinkedIn')
                            ->url()
                            ->placeholder('https://linkedin.com/company/vipgpi')
                            ->maxLength(300),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['setting_key' => $key],
                ['setting_value' => $value ?? '']
            );
        }

        Cache::forget('app_settings');

        Notification::make()
            ->title('Pied de page mis à jour')
            ->success()
            ->send();
    }
}
