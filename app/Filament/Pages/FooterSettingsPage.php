<?php

namespace App\Filament\Pages;

use App\Http\Middleware\FullPageCache;
use App\Models\Setting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class FooterSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Site Web';
    protected static ?string $navigationLabel = 'Pied de page (Footer)';
    protected static ?string $title           = 'Gestion du pied de page';
    protected static string  $view            = 'filament.pages.footer-settings';

    public ?array $data = [];

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public function mount(): void
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        $this->form->fill([
            'footer_copyright'      => $settings['footer_copyright']      ?? '',
            'footer_description'    => $settings['footer_description']    ?? '',
            'footer_description_en' => $settings['footer_description_en'] ?? '',
            'site_address'          => $settings['site_address']          ?? '',
            'site_phone'            => $settings['site_phone']            ?? '',
            'site_email'            => $settings['site_email']            ?? '',
            'facebook_url'          => $settings['facebook_url']          ?? '',
            'linkedin_url'          => $settings['linkedin_url']          ?? '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations légales')
                    ->columns(1)
                    ->schema([
                        TextInput::make('footer_copyright')
                            ->label('Texte copyright')
                            ->placeholder('VIP Services Financiers Inc.')
                            ->helperText('Affiché après © 2025 dans le bas de page.')
                            ->maxLength(200),
                    ]),

                Section::make('Description')
                    ->columns(1)
                    ->schema([
                        Textarea::make('footer_description')
                            ->label('Description (Français)')
                            ->rows(3)
                            ->maxLength(500),

                        Textarea::make('footer_description_en')
                            ->label('Description (Anglais)')
                            ->rows(3)
                            ->maxLength(500),
                    ]),

                Section::make('Coordonnées')
                    ->columns(2)
                    ->schema([
                        TextInput::make('site_address')
                            ->label('Adresse')
                            ->placeholder('2990 av. Pierre-Péladeau, Suite 400, Laval, QC H7T 3B3')
                            ->columnSpanFull()
                            ->maxLength(300),

                        TextInput::make('site_phone')
                            ->label('Téléphone')
                            ->placeholder('579 640-3334')
                            ->maxLength(30),

                        TextInput::make('site_email')
                            ->label('Courriel')
                            ->email()
                            ->placeholder('admin@vipgpi.ca')
                            ->maxLength(100),
                    ]),

                Section::make('Réseaux sociaux')
                    ->columns(2)
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('URL Facebook')
                            ->placeholder('https://facebook.com/vipgpi')
                            ->maxLength(300),

                        TextInput::make('linkedin_url')
                            ->label('URL LinkedIn')
                            ->placeholder('https://linkedin.com/company/vipgpi')
                            ->maxLength(300),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        Cache::forget('app_settings');
        FullPageCache::clearAll();

        Notification::make()
            ->title('Pied de page mis à jour')
            ->success()
            ->send();
    }
}
