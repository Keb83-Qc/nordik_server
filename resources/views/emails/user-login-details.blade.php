<x-email.layout
    :headerColor="$emailSettings->security_header_color"
    :headerTitle="$emailSettings->security_header_title"
    :accentColor="$emailSettings->global_accent_color"
    :logoUrl="$emailSettings->global_logo_url"
    :footerText="$emailSettings->global_footer_text"
    title="Vos informations de connexion"
>
    <x-slot name="styles">
    <style>
        h1 { font-size: 20px; font-weight: 700; color: #0E1030; margin-bottom: 14px; }
        .text { font-size: 15px; color: #444; line-height: 1.65; margin-bottom: 16px; }
        .credentials { background: #f8f9fa; border: 1px solid #e8e8e8; border-left: 3px solid #C9A050; border-radius: 6px; padding: 16px 20px; margin-bottom: 20px; }
        .credentials li { font-size: 14px; color: #333; margin-bottom: 8px; list-style: none; }
        .credentials li strong { color: #0E1030; display: inline-block; min-width: 160px; }
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; padding: 14px 36px; background: linear-gradient(135deg, #C9A050, #e0b86a); color: #0E1030 !important; font-weight: 700; font-size: 15px; text-decoration: none; border-radius: 8px; }
        .note { font-size: 13px; color: #666; line-height: 1.55; background: #fafafa; border: 1px solid #e8e8e8; border-radius: 6px; padding: 12px 16px; margin-top: 16px; }
    </style>
    </x-slot>

    <h1>Bonjour {{ $user->first_name }},</h1>

    <p class="text">
        Voici vos informations de connexion pour accéder à votre espace conseiller VIP GPI.
    </p>

    <div class="credentials">
        <ul>
            <li><strong>Adresse courriel :</strong> {{ $user->email }}</li>
            <li><strong>Mot de passe temporaire :</strong> {{ $tempPassword }}</li>
        </ul>
    </div>

    <div class="btn-wrap">
        <a href="{{ url('/espace-conseiller') }}" class="btn">Accéder à mon espace conseiller</a>
    </div>

    <div class="note">
        Merci de changer ce mot de passe dès votre première connexion et de mettre à jour vos informations (photo, biographie, téléphone, etc.).
    </div>
</x-email.layout>
