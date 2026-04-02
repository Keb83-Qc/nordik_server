@php
    $emailSettings = app(\App\Settings\EmailSettings::class);
@endphp

<x-email.layout
    :headerColor="$emailSettings->security_header_color"
    :headerTitle="$emailSettings->security_header_title"
    :accentColor="$emailSettings->global_accent_color"
    :logoUrl="$emailSettings->global_logo_url"
    :footerText="$emailSettings->global_footer_text"
    title="Réinitialisation de mot de passe"
>
    <x-slot name="styles">
    <style>
        .greeting { font-size: 20px; font-weight: 700; color: #0E1030; margin-bottom: 14px; }
        .text { font-size: 15px; color: #444; line-height: 1.65; margin-bottom: 16px; }
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; padding: 14px 36px; background: linear-gradient(135deg, #C9A050, #e0b86a); color: #0E1030 !important; font-weight: 700; font-size: 15px; text-decoration: none; border-radius: 8px; letter-spacing: .3px; }
        .info-box { background: #fafafa; border: 1px solid #e8e8e8; border-left: 3px solid #C9A050; border-radius: 6px; padding: 14px 16px; font-size: 13px; color: #666; line-height: 1.55; margin-bottom: 20px; }
        .fallback { font-size: 12px; color: #999; line-height: 1.6; border-top: 1px solid #eee; padding-top: 18px; margin-top: 8px; }
        .fallback a { color: #0E1030; word-break: break-all; }
    </style>
    </x-slot>

    <p class="greeting">Réinitialisation de mot de passe</p>

    <p class="text">
        Vous recevez ce message parce qu'une demande de réinitialisation de mot de passe
        a été soumise pour le compte associé à <strong>{{ $email }}</strong>.
    </p>

    <p class="text">
        Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
        Ce lien est valide pendant <strong>60 minutes</strong>.
    </p>

    <div class="btn-wrap">
        <a href="{{ $url }}" class="btn">Réinitialiser mon mot de passe</a>
    </div>

    <div class="info-box">
        Si vous n'avez pas demandé de réinitialisation, aucune action n'est requise.
        Votre mot de passe actuel reste inchangé.
    </div>

    <div class="fallback">
        Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :<br>
        <a href="{{ $url }}">{{ $url }}</a>
    </div>
</x-email.layout>
