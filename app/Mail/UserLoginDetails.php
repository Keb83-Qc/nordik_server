<?php

namespace App\Mail;

use App\Models\User;
use App\Settings\EmailSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserLoginDetails extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailDept = 'email_security';

    public EmailSettings $emailSettings;

    public function __construct(
        public User   $user,
        public string $tempPassword,
    ) {
        $this->emailSettings = app(EmailSettings::class);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                $this->emailSettings->security_from_email,
                $this->emailSettings->security_from_name,
            ),
            subject: 'Vos informations de connexion VIP GPI',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.user-login-details');
    }
}
