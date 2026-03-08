<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MailSettings extends Settings
{
    public string $submission_to;
    public bool $test_mode;
    public ?string $test_to = null;
    public bool $redirect_all_mail = false;

    public static function group(): string
    {
        return 'mail';
    }

    public function recipientForSubmissions(): string
    {
        if ($this->test_mode && filled($this->test_to)) {
            return $this->test_to;
        }

        return $this->submission_to;
    }
}
