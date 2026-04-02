<?php

namespace App\Filament\Pages;

use App\Settings\EmailSettings;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;

class EmailTemplatesPage extends Page
{
    use InteractsWithFormActions;

    protected static ?string $navigationIcon  = 'heroicon-o-paint-brush';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $navigationLabel = 'Gabarits email';
    protected static ?string $title           = 'Gestion des gabarits email';
    protected static string  $view            = 'filament.pages.email-templates';
    protected static ?int    $navigationSort  = 20;

    public array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(EmailSettings $settings): void
    {
        $this->form->fill([
            // Global
            'global_logo_url'      => $settings->global_logo_url,
            'global_from_name'     => $settings->global_from_name,
            'global_from_email'    => $settings->global_from_email,
            'global_header_color'  => $settings->global_header_color,
            'global_accent_color'  => $settings->global_accent_color,
            'global_footer_text'   => $settings->global_footer_text,
            // Système
            'noreply_address'      => $settings->noreply_address,
            'noreply_name'         => $settings->noreply_name,
            'admin_alert_email'    => $settings->admin_alert_email,
            'fallback_admin_email' => $settings->fallback_admin_email,
            'support_url'          => $settings->support_url,
            'career_default_email' => $settings->career_default_email,
            // Internes
            'internal_from_name'    => $settings->internal_from_name,
            'internal_from_email'   => $settings->internal_from_email,
            'internal_header_color' => $settings->internal_header_color,
            'internal_header_title' => $settings->internal_header_title,
            'internal_recipients'   => $settings->internal_recipients,
            // Partenaires
            'partner_from_name'      => $settings->partner_from_name,
            'partner_from_email'     => $settings->partner_from_email,
            'partner_header_title'   => $settings->partner_header_title,
            'partner_fallback_color' => $settings->partner_fallback_color,
            // Sécurité
            'security_from_name'          => $settings->security_from_name,
            'security_from_email'         => $settings->security_from_email,
            'security_header_color'       => $settings->security_header_color,
            'security_header_title'       => $settings->security_header_title,
            'security_access_request_to'  => $settings->security_access_request_to,
            // ABF
            'abf_from_name'    => $settings->abf_from_name,
            'abf_from_email'   => $settings->abf_from_email,
            'abf_header_color' => $settings->abf_header_color,
            'abf_header_title' => $settings->abf_header_title,
            // Alertes
            'alert_from_name'    => $settings->alert_from_name,
            'alert_from_email'   => $settings->alert_from_email,
            'alert_header_color' => $settings->alert_header_color,
            'alert_recipients'   => $settings->alert_recipients,
            // Conseillers
            'advisor_from_name'    => $settings->advisor_from_name,
            'advisor_from_email'   => $settings->advisor_from_email,
            'advisor_header_color' => $settings->advisor_header_color,
            'advisor_header_title' => $settings->advisor_header_title,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Tabs::make('Gabarits email')
                    ->tabs([

                        // ─── Global ──────────────────────────────────────────
                        Tabs\Tab::make('Paramètres globaux')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Section::make('Identité par défaut')
                                    ->description('Valeurs utilisées comme fallback si un département ne les redéfinit pas.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('global_from_name')
                                            ->label('Nom expéditeur')
                                            ->required(),

                                        TextInput::make('global_from_email')
                                            ->label('Adresse expéditeur')
                                            ->email()
                                            ->required(),
                                    ]),

                                Section::make('Apparence globale')
                                    ->columns(2)
                                    ->schema([
                                        ColorPicker::make('global_header_color')
                                            ->label('Couleur d\'en-tête'),

                                        ColorPicker::make('global_accent_color')
                                            ->label('Couleur d\'accent (boutons, soulignements)'),

                                        TextInput::make('global_logo_url')
                                            ->label('URL du logo')
                                            ->url()
                                            ->placeholder('https://...')
                                            ->helperText('Laissez vide pour ne pas afficher de logo.')
                                            ->columnSpanFull(),

                                        TextInput::make('global_footer_text')
                                            ->label('Texte de pied de page')
                                            ->placeholder('VIP Gestion de Patrimoine & Investissement Inc.')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Adresses système')
                                    ->description('Adresses utilisées par le système en arrière-plan.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('noreply_address')
                                            ->label('Adresse no-reply')
                                            ->email()
                                            ->required()
                                            ->helperText('Utilisée pour les messages internes conseillers.'),

                                        TextInput::make('noreply_name')
                                            ->label('Nom no-reply')
                                            ->required(),

                                        TextInput::make('admin_alert_email')
                                            ->label('Destinataire alertes admin')
                                            ->email()
                                            ->required()
                                            ->helperText('Reçoit les bug reports et demandes système.'),

                                        TextInput::make('fallback_admin_email')
                                            ->label('Admin fallback (distribution leads)')
                                            ->email()
                                            ->required()
                                            ->helperText('Reçoit les leads si aucun conseiller n\'est disponible.'),

                                        TextInput::make('support_url')
                                            ->label('URL de support (panneau Filament)')
                                            ->placeholder('mailto:support@vipgpi.ca')
                                            ->helperText('Lien affiché dans la barre d\'aide du portail.')
                                            ->columnSpanFull(),

                                        TextInput::make('career_default_email')
                                            ->label('Email candidatures (carrières)')
                                            ->email()
                                            ->helperText('Email par défaut sur la page des offres d\'emploi.'),
                                    ]),
                            ]),

                        // ─── Soumissions internes ─────────────────────────────
                        Tabs\Tab::make('Soumissions internes')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Expéditeur')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('internal_from_name')
                                            ->label('Nom affiché')
                                            ->required(),

                                        TextInput::make('internal_from_email')
                                            ->label('Adresse courriel')
                                            ->email()
                                            ->required(),
                                    ]),

                                Section::make('En-tête de l\'email')
                                    ->columns(2)
                                    ->schema([
                                        ColorPicker::make('internal_header_color')
                                            ->label('Couleur de fond'),

                                        TextInput::make('internal_header_title')
                                            ->label('Titre de l\'en-tête')
                                            ->placeholder('Nouvelle soumission reçue'),
                                    ]),

                                Section::make('Destinataires')
                                    ->description('Emails qui reçoivent les nouvelles soumissions internes.')
                                    ->schema([
                                        TagsInput::make('internal_recipients')
                                            ->label('Destinataires')
                                            ->placeholder('Ajouter un email...')
                                            ->helperText('Appuyez sur Entrée après chaque adresse.'),
                                    ]),
                            ]),

                        // ─── Soumissions partenaires ──────────────────────────
                        Tabs\Tab::make('Soumissions partenaires')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Section::make('Expéditeur')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('partner_from_name')
                                            ->label('Nom affiché')
                                            ->required(),

                                        TextInput::make('partner_from_email')
                                            ->label('Adresse courriel')
                                            ->email()
                                            ->required(),
                                    ]),

                                Section::make('En-tête de l\'email')
                                    ->description('Le logo et les couleurs du portail partenaire sont utilisés automatiquement. Ces valeurs s\'appliquent si le portail n\'en a pas.')
                                    ->columns(2)
                                    ->schema([
                                        ColorPicker::make('partner_fallback_color')
                                            ->label('Couleur de fallback'),

                                        TextInput::make('partner_header_title')
                                            ->label('Titre de l\'en-tête')
                                            ->placeholder('Nouvelle soumission partenaire'),
                                    ]),
                            ]),

                        // ─── Sécurité & Accès ─────────────────────────────────
                        Tabs\Tab::make('Sécurité & Accès')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Section::make('Expéditeur')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('security_from_name')
                                            ->label('Nom affiché')
                                            ->required(),

                                        TextInput::make('security_from_email')
                                            ->label('Adresse courriel')
                                            ->email()
                                            ->required(),
                                    ]),

                                Section::make('En-tête de l\'email')
                                    ->columns(2)
                                    ->schema([
                                        ColorPicker::make('security_header_color')
                                            ->label('Couleur de fond'),

                                        TextInput::make('security_header_title')
                                            ->label('Titre de l\'en-tête')
                                            ->placeholder('Accès & Sécurité'),
                                    ]),

                                Section::make('Destinataires')
                                    ->description('Emails couverts : réinitialisation de mot de passe, création de compte conseiller, demandes d\'accès.')
                                    ->schema([
                                        TextInput::make('security_access_request_to')
                                            ->label('Destinataire des demandes d\'accès')
                                            ->email()
                                            ->required()
                                            ->helperText('Reçoit les demandes du formulaire public d\'accès conseiller.'),
                                    ]),
                            ]),

                        // ─── Profil financier (ABF) ───────────────────────────
                        Tabs\Tab::make('Profil financier')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make('Expéditeur')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('abf_from_name')
                                            ->label('Nom affiché')
                                            ->required(),

                                        TextInput::make('abf_from_email')
                                            ->label('Adresse courriel')
                                            ->email()
                                            ->required(),
                                    ]),

                                Section::make('En-tête de l\'email')
                                    ->description('Emails couverts : invitation client (intake) et notification de profil complété.')
                                    ->columns(2)
                                    ->schema([
                                        ColorPicker::make('abf_header_color')
                                            ->label('Couleur de fond'),

                                        TextInput::make('abf_header_title')
                                            ->label('Titre de l\'en-tête')
                                            ->placeholder('Profil financier'),
                                    ]),
                            ]),

                        // ─── Alertes système ──────────────────────────────────
                        Tabs\Tab::make('Alertes système')
                            ->icon('heroicon-o-bell-alert')
                            ->schema([
                                Section::make('Expéditeur')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('alert_from_name')
                                            ->label('Nom affiché')
                                            ->required(),

                                        TextInput::make('alert_from_email')
                                            ->label('Adresse courriel')
                                            ->email()
                                            ->required(),
                                    ]),

                                Section::make('En-tête de l\'email')
                                    ->columns(2)
                                    ->schema([
                                        ColorPicker::make('alert_header_color')
                                            ->label('Couleur de fond'),
                                    ]),

                                Section::make('Destinataires')
                                    ->description('Reçoivent les bug reports et demandes système.')
                                    ->schema([
                                        TagsInput::make('alert_recipients')
                                            ->label('Destinataires des alertes')
                                            ->placeholder('Ajouter un email...')
                                            ->helperText('Appuyez sur Entrée après chaque adresse.'),
                                    ]),
                            ]),

                        // ─── Liens conseillers ────────────────────────────────
                        Tabs\Tab::make('Liens conseillers')
                            ->icon('heroicon-o-link')
                            ->schema([
                                Section::make('Expéditeur')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('advisor_from_name')
                                            ->label('Nom affiché')
                                            ->required(),

                                        TextInput::make('advisor_from_email')
                                            ->label('Adresse courriel')
                                            ->email()
                                            ->required(),
                                    ]),

                                Section::make('En-tête de l\'email')
                                    ->description('Email envoyé aux conseillers lorsqu\'ils partagent leur lien de consentement client.')
                                    ->columns(2)
                                    ->schema([
                                        ColorPicker::make('advisor_header_color')
                                            ->label('Couleur de fond'),

                                        TextInput::make('advisor_header_title')
                                            ->label('Titre de l\'en-tête')
                                            ->placeholder('Lien de consentement client'),
                                    ]),
                            ]),

                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public function save(EmailSettings $settings): void
    {
        $state = $this->form->getState();

        // Global
        $settings->global_logo_url     = $state['global_logo_url']     ?? '';
        $settings->global_from_name    = $state['global_from_name']    ?? $settings->global_from_name;
        $settings->global_from_email   = $state['global_from_email']   ?? $settings->global_from_email;
        $settings->global_header_color = $state['global_header_color'] ?? $settings->global_header_color;
        $settings->global_accent_color = $state['global_accent_color'] ?? $settings->global_accent_color;
        $settings->global_footer_text  = $state['global_footer_text']  ?? $settings->global_footer_text;

        // Système
        $settings->noreply_address      = $state['noreply_address']      ?? $settings->noreply_address;
        $settings->noreply_name         = $state['noreply_name']         ?? $settings->noreply_name;
        $settings->admin_alert_email    = $state['admin_alert_email']    ?? $settings->admin_alert_email;
        $settings->fallback_admin_email = $state['fallback_admin_email'] ?? $settings->fallback_admin_email;
        $settings->support_url          = $state['support_url']          ?? $settings->support_url;
        $settings->career_default_email = $state['career_default_email'] ?? $settings->career_default_email;

        // Internes
        $settings->internal_from_name    = $state['internal_from_name']    ?? $settings->internal_from_name;
        $settings->internal_from_email   = $state['internal_from_email']   ?? $settings->internal_from_email;
        $settings->internal_header_color = $state['internal_header_color'] ?? $settings->internal_header_color;
        $settings->internal_header_title = $state['internal_header_title'] ?? $settings->internal_header_title;
        $settings->internal_recipients   = $state['internal_recipients']   ?? [];

        // Partenaires
        $settings->partner_from_name      = $state['partner_from_name']      ?? $settings->partner_from_name;
        $settings->partner_from_email     = $state['partner_from_email']     ?? $settings->partner_from_email;
        $settings->partner_header_title   = $state['partner_header_title']   ?? $settings->partner_header_title;
        $settings->partner_fallback_color = $state['partner_fallback_color'] ?? $settings->partner_fallback_color;

        // Sécurité
        $settings->security_from_name          = $state['security_from_name']          ?? $settings->security_from_name;
        $settings->security_from_email         = $state['security_from_email']         ?? $settings->security_from_email;
        $settings->security_header_color       = $state['security_header_color']       ?? $settings->security_header_color;
        $settings->security_header_title       = $state['security_header_title']       ?? $settings->security_header_title;
        $settings->security_access_request_to  = $state['security_access_request_to']  ?? $settings->security_access_request_to;

        // ABF
        $settings->abf_from_name    = $state['abf_from_name']    ?? $settings->abf_from_name;
        $settings->abf_from_email   = $state['abf_from_email']   ?? $settings->abf_from_email;
        $settings->abf_header_color = $state['abf_header_color'] ?? $settings->abf_header_color;
        $settings->abf_header_title = $state['abf_header_title'] ?? $settings->abf_header_title;

        // Alertes
        $settings->alert_from_name    = $state['alert_from_name']    ?? $settings->alert_from_name;
        $settings->alert_from_email   = $state['alert_from_email']   ?? $settings->alert_from_email;
        $settings->alert_header_color = $state['alert_header_color'] ?? $settings->alert_header_color;
        $settings->alert_recipients   = $state['alert_recipients']   ?? [];

        // Conseillers
        $settings->advisor_from_name    = $state['advisor_from_name']    ?? $settings->advisor_from_name;
        $settings->advisor_from_email   = $state['advisor_from_email']   ?? $settings->advisor_from_email;
        $settings->advisor_header_color = $state['advisor_header_color'] ?? $settings->advisor_header_color;
        $settings->advisor_header_title = $state['advisor_header_title'] ?? $settings->advisor_header_title;

        $settings->save();

        Notification::make()
            ->title('Gabarits email sauvegardés')
            ->success()
            ->send();
    }
}
