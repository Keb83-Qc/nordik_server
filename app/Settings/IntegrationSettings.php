<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class IntegrationSettings extends Settings
{
    // ─── DeepL ───────────────────────────────────────────────────────────────
    public string $deepl_api_key;
    public string $deepl_api_url;

    // ─── Zoho People ─────────────────────────────────────────────────────────
    public string $zoho_client_id;
    public string $zoho_client_secret;
    public string $zoho_refresh_token;
    public string $zoho_accounts_url;
    public string $zoho_people_base_url;
    public string $zoho_people_records_path;

    // ─── Courtier assurance ───────────────────────────────────────────────────
    public string $insurance_broker_email;

    public static function group(): string
    {
        return 'integrations';
    }
}
