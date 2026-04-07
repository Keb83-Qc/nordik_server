<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateIntegrationSettings extends SettingsMigration
{
    public function up(): void
    {
        // ─── DeepL ───────────────────────────────────────────────────────────
        $this->migrator->add('integrations.deepl_api_key', env('DEEPL_API_KEY', ''));
        $this->migrator->add('integrations.deepl_api_url', env('DEEPL_API_URL', 'https://api-free.deepl.com'));

        // ─── Zoho People ─────────────────────────────────────────────────────
        $this->migrator->add('integrations.zoho_client_id',           env('ZOHO_CLIENT_ID',           ''));
        $this->migrator->add('integrations.zoho_client_secret',       env('ZOHO_CLIENT_SECRET',       ''));
        $this->migrator->add('integrations.zoho_refresh_token',       env('ZOHO_REFRESH_TOKEN',       ''));
        $this->migrator->add('integrations.zoho_accounts_url',        env('ZOHO_ACCOUNTS_URL',        'https://accounts.zohocloud.ca'));
        $this->migrator->add('integrations.zoho_people_base_url',     env('ZOHO_PEOPLE_BASE_URL',     'https://people.zohocloud.ca'));
        $this->migrator->add('integrations.zoho_people_records_path', env('ZOHO_PEOPLE_RECORDS_PATH', '/people/api/forms/employee/getRecords'));

        // ─── Courtier assurance ───────────────────────────────────────────────
        $this->migrator->add('integrations.insurance_broker_email', env('INSURANCE_BROKER_EMAIL', ''));
    }
}
