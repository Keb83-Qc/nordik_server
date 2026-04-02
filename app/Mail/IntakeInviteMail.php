<?php

namespace App\Mail;

use App\Models\AbfIntake;
use App\Settings\EmailSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IntakeInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public AbfIntake $intake;

    public function __construct(AbfIntake $intake)
    {
        $this->intake = $intake;
    }

    public function envelope(): Envelope
    {
        $settings = app(EmailSettings::class);

        $subject = match ($this->intake->locale) {
            'en' => 'Your financial profile — VIP GPI',
            'es' => 'Su perfil financiero — VIP GPI',
            'ht' => 'Pwofil finansye ou — VIP GPI',
            default => 'Votre profil financier — VIP GPI',
        };

        return new Envelope(
            from: new Address($settings->abf_from_email, $settings->abf_from_name),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.intake-invite');
    }
}
