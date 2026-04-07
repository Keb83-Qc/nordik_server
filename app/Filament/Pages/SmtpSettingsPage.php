<?php

namespace App\Filament\Pages;

use App\Settings\SmtpSettings;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Support\Facades\Mail;

class SmtpSettingsPage extends Page
{
    use InteractsWithFormActions;

    protected static ?string $navigationIcon  = 'heroicon-o-server-stack';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $title           = 'SMTP';
    protected static string  $view            = 'filament.pages.smtp-settings';

    public array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(SmtpSettings $settings): void
    {
        $this->data = [
            'mailer'       => $settings->mailer,
            'host'         => $settings->host,
            'port'         => $settings->port,
            'username'     => $settings->username,
            'password'     => $settings->password,
            'encryption'   => $settings->encryption,
            'from_address' => $settings->from_address,
            'from_name'    => $settings->from_name,
        ];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('Serveur')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('mailer')
                            ->label('Mailer')
                            ->options(['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'log' => 'Log (dev)'])
                            ->required(),

                        Forms\Components\TextInput::make('host')
                            ->label('Hôte SMTP')
                            ->required(),

                        Forms\Components\TextInput::make('port')
                            ->label('Port')
                            ->numeric()
                            ->required(),

                        Forms\Components\Select::make('encryption')
                            ->label('Chiffrement')
                            ->options(['tls' => 'TLS', 'ssl' => 'SSL', '' => 'Aucun'])
                            ->required(),
                    ]),

                Forms\Components\Section::make('Authentification')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('username')
                            ->label('Nom d\'utilisateur')
                            ->required(),

                        Forms\Components\TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->revealable()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Expéditeur par défaut')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('from_address')
                            ->label('Adresse courriel')
                            ->email()
                            ->required(),

                        Forms\Components\TextInput::make('from_name')
                            ->label('Nom affiché')
                            ->required(),
                    ]),
            ]);
    }

    public function save(SmtpSettings $settings): void
    {
        $state = $this->form->getState();

        $settings->mailer       = $state['mailer'];
        $settings->host         = $state['host'];
        $settings->port         = (int) $state['port'];
        $settings->username     = $state['username'];
        $settings->password     = $state['password'];
        $settings->encryption   = $state['encryption'];
        $settings->from_address = $state['from_address'];
        $settings->from_name    = $state['from_name'];

        $settings->save();

        // Appliquer immédiatement sans reboot
        config([
            'mail.default'                => $settings->mailer,
            'mail.mailers.smtp.host'       => $settings->host,
            'mail.mailers.smtp.port'       => $settings->port,
            'mail.mailers.smtp.username'   => $settings->username,
            'mail.mailers.smtp.password'   => $settings->password,
            'mail.mailers.smtp.encryption' => $settings->encryption,
            'mail.from.address'            => $settings->from_address,
            'mail.from.name'               => $settings->from_name,
        ]);

        Notification::make()
            ->title('Paramètres SMTP sauvegardés')
            ->success()
            ->send();
    }

    public function testMail(SmtpSettings $settings): void
    {
        $to = auth()->user()->email;

        try {
            Mail::raw('Test SMTP depuis le panneau d\'administration VIP GPI.', function ($msg) use ($to, $settings) {
                $msg->to($to)->subject('Test SMTP');
            });

            Notification::make()
                ->title("Courriel de test envoyé à {$to}")
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Erreur SMTP')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
