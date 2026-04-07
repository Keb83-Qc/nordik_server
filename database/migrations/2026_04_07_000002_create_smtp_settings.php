<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateSmtpSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('smtp.mailer',       env('MAIL_MAILER',       'smtp'));
        $this->migrator->add('smtp.host',         env('MAIL_HOST',         'smtp.gmail.com'));
        $this->migrator->add('smtp.port',         (int) env('MAIL_PORT',   587));
        $this->migrator->add('smtp.username',     env('MAIL_USERNAME',     ''));
        $this->migrator->add('smtp.password',     env('MAIL_PASSWORD',     ''));
        $this->migrator->add('smtp.encryption',   env('MAIL_ENCRYPTION',   'tls'));
        $this->migrator->add('smtp.from_address', env('MAIL_FROM_ADDRESS', ''));
        $this->migrator->add('smtp.from_name',    env('MAIL_FROM_NAME',    ''));
    }
}
