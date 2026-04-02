<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateEmailSettings extends SettingsMigration
{
    public function up(): void
    {
        // ─── Global / Système ─────────────────────────────────────────────────
        $this->migrator->add('email.global_logo_url',      '');
        $this->migrator->add('email.global_from_name',     'VIP GPI');
        $this->migrator->add('email.global_from_email',    'no-reply@vipgpi.ca');
        $this->migrator->add('email.global_header_color',  '#0E1030');
        $this->migrator->add('email.global_accent_color',  '#C9A050');
        $this->migrator->add('email.global_footer_text',   'VIP Gestion de Patrimoine & Investissement Inc.');

        $this->migrator->add('email.noreply_address',      'no-reply@vipgpi.ca');
        $this->migrator->add('email.noreply_name',         'VIP GPI');
        $this->migrator->add('email.admin_alert_email',    'web@vipgpi.ca');
        $this->migrator->add('email.fallback_admin_email', 'admin@vipgpi.ca');
        $this->migrator->add('email.support_url',          'mailto:support@vipgpi.ca');
        $this->migrator->add('email.career_default_email', 'candidature@vipgpi.ca');

        // ─── Soumissions internes ─────────────────────────────────────────────
        $this->migrator->add('email.internal_from_name',    'VIP GPI Soumissions');
        $this->migrator->add('email.internal_from_email',   'no-reply@vipgpi.ca');
        $this->migrator->add('email.internal_header_color', '#0E1030');
        $this->migrator->add('email.internal_header_title', 'Nouvelle soumission reçue');
        $this->migrator->add('email.internal_recipients',   []);

        // ─── Soumissions partenaires ──────────────────────────────────────────
        $this->migrator->add('email.partner_from_name',       'VIP GPI Partenaires');
        $this->migrator->add('email.partner_from_email',      'no-reply@vipgpi.ca');
        $this->migrator->add('email.partner_header_title',    'Nouvelle soumission partenaire');
        $this->migrator->add('email.partner_fallback_color',  '#1a2e4a');

        // ─── Sécurité & Accès ────────────────────────────────────────────────
        $this->migrator->add('email.security_from_name',           'VIP GPI');
        $this->migrator->add('email.security_from_email',          'no-reply@vipgpi.ca');
        $this->migrator->add('email.security_header_color',        '#0E1030');
        $this->migrator->add('email.security_header_title',        'Accès & Sécurité');
        $this->migrator->add('email.security_access_request_to',   'claude.goudreau@vipgpi.ca');

        // ─── Profil financier (ABF) ───────────────────────────────────────────
        $this->migrator->add('email.abf_from_name',    'VIP GPI Profil Financier');
        $this->migrator->add('email.abf_from_email',   'no-reply@vipgpi.ca');
        $this->migrator->add('email.abf_header_color', '#0E1030');
        $this->migrator->add('email.abf_header_title', 'Profil financier');

        // ─── Alertes système ─────────────────────────────────────────────────
        $this->migrator->add('email.alert_from_name',    'VIP GPI Système');
        $this->migrator->add('email.alert_from_email',   'no-reply@vipgpi.ca');
        $this->migrator->add('email.alert_header_color', '#0E1030');
        $this->migrator->add('email.alert_recipients',   ['web@vipgpi.ca']);

        // ─── Liens conseillers ────────────────────────────────────────────────
        $this->migrator->add('email.advisor_from_name',    'VIP GPI Soumissions');
        $this->migrator->add('email.advisor_from_email',   'no-reply@vipgpi.ca');
        $this->migrator->add('email.advisor_header_color', '#0E1030');
        $this->migrator->add('email.advisor_header_title', 'Lien de consentement client');
    }
}
