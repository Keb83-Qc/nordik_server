<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SmtpSettings extends Settings
{
    public string $mailer;
    public string $host;
    public int    $port;
    public string $username;
    public string $password;
    public string $encryption;
    public string $from_address;
    public string $from_name;

    public static function group(): string
    {
        return 'smtp';
    }
}
