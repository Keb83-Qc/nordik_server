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

class AdvisorLinkShare extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailDept = 'email_advisor';

    public $advisor;
    public $link;

    public function __construct(User $advisor, string $link)
    {
        $this->advisor = $advisor;
        $this->link    = $link;
    }

    public function envelope(): Envelope
    {
        $settings = app(EmailSettings::class);

        return new Envelope(
            from: new Address($settings->advisor_from_email, $settings->advisor_from_name),
            subject: 'Ton lien de consentement client - VIP GPI',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.advisor-link-share');
    }
}
