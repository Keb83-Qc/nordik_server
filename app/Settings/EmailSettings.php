<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class EmailSettings extends Settings
{
    // ─── Global / Système ────────────────────────────────────────────────────
    public string $global_logo_url;
    public string $global_from_name;
    public string $global_from_email;
    public string $global_header_color;
    public string $global_accent_color;
    public string $global_footer_text;

    /** Adresse no-reply utilisée pour les messages internes (Message.php) */
    public string $noreply_address;
    public string $noreply_name;

    /** Destinataire des alertes admin (bug reports, demandes système) */
    public string $admin_alert_email;

    /** Fallback advisor si aucun n'accepte des leads (LeadDispatcher) */
    public string $fallback_admin_email;

    /** URL de support affichée dans le panneau Filament */
    public string $support_url;

    /** Email par défaut des offres d'emploi (CareerPage) */
    public string $career_default_email;

    // ─── Soumissions internes ─────────────────────────────────────────────────
    public string $internal_from_name;
    public string $internal_from_email;
    public string $internal_header_color;
    public string $internal_header_title;
    public array  $internal_recipients;

    // ─── Soumissions partenaires ──────────────────────────────────────────────
    public string $partner_from_name;
    public string $partner_from_email;
    public string $partner_header_title;
    public string $partner_fallback_color;

    // ─── Sécurité & Accès ────────────────────────────────────────────────────
    public string $security_from_name;
    public string $security_from_email;
    public string $security_header_color;
    public string $security_header_title;
    public string $security_access_request_to;

    // ─── Profil financier (ABF) ───────────────────────────────────────────────
    public string $abf_from_name;
    public string $abf_from_email;
    public string $abf_header_color;
    public string $abf_header_title;

    // ─── Alertes système ──────────────────────────────────────────────────────
    public string $alert_from_name;
    public string $alert_from_email;
    public string $alert_header_color;
    public array  $alert_recipients;

    // ─── Liens conseillers ────────────────────────────────────────────────────
    public string $advisor_from_name;
    public string $advisor_from_email;
    public string $advisor_header_color;
    public string $advisor_header_title;

    public static function group(): string
    {
        return 'email';
    }
}
