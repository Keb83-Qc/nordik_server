@php
    $emailSettings = app(\App\Settings\EmailSettings::class);
@endphp

<x-email.layout
    :headerColor="$emailSettings->advisor_header_color"
    :headerTitle="$emailSettings->advisor_header_title"
    :accentColor="$emailSettings->global_accent_color"
    :logoUrl="$emailSettings->global_logo_url"
    :footerText="$emailSettings->global_footer_text"
    title="Lien de consentement client"
>
    <x-slot name="styles">
    <style>
        h2 { font-size: 18px; font-weight: 700; color: #0E1030; margin-bottom: 14px; }
        p { font-size: 14px; color: #444; line-height: 1.6; margin-bottom: 12px; }
        .box { background: #f9f9f9; border: 1px solid #eee; border-left: 3px solid #C9A050; border-radius: 8px; padding: 16px 20px; margin: 16px 0; }
        .box a { color: #0E1030; word-break: break-all; font-size: 13px; }
        .btn-wrap { text-align: center; margin: 24px 0; }
        .btn { display: inline-block; padding: 12px 28px; background: linear-gradient(135deg, #C9A050, #e0b86a); color: #0E1030 !important; font-weight: 700; font-size: 14px; text-decoration: none; border-radius: 8px; }
    </style>
    </x-slot>

    <h2>Bonjour {{ $advisor->first_name }},</h2>

    <p>Voici ton <strong>lien de consentement client</strong> unique.</p>
    <p>Ce lien permet à tes clients d'accepter les conditions de traitement de données avant de débuter leur soumission auto.</p>

    <div class="box">
        <strong>Ton lien :</strong><br>
        <a href="{{ $link }}">{{ $link }}</a>
    </div>

    <p>Lorsqu'un client utilise ce lien et accepte le consentement, la soumission te sera automatiquement assignée.</p>

    <div class="btn-wrap">
        <a href="{{ $link }}" class="btn">Tester mon lien</a>
    </div>

    <p style="font-size:13px;color:#888;">L'équipe {{ $emailSettings->global_from_name }}</p>
</x-email.layout>
