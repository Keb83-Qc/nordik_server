@php
    $emailSettings = app(\App\Settings\EmailSettings::class);
@endphp

<x-email.layout
    :headerColor="$emailSettings->internal_header_color"
    :headerTitle="$emailSettings->internal_header_title"
    :accentColor="$emailSettings->global_accent_color"
    :logoUrl="$emailSettings->global_logo_url"
    :footerText="$emailSettings->global_footer_text"
    title="Soumission auto reçue"
>
    <x-slot name="styles">
    <style>
        p { font-size: 14px; color: #444; line-height: 1.6; margin-bottom: 10px; }
        p strong { color: #0E1030; }
        .btn-wrap { margin: 20px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #0E1030; color: #fff !important; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 700; }
    </style>
    </x-slot>

    <p><strong>Client :</strong> {{ $clientName }}</p>
    <p><strong>Véhicule :</strong> {{ $vehicle }}</p>
    <p><strong>Date :</strong> {{ $submission->created_at->format('d/m/Y H:i') }}</p>

    <div class="btn-wrap">
        <a href="{{ $filamentUrl }}" class="btn">Ouvrir dans le portail</a>
    </div>
</x-email.layout>
