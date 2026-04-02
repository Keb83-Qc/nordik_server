<?php

namespace App\Mail;

use App\Models\AbfCase;
use App\Models\AbfIntake;
use App\Settings\EmailSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IntakeCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public AbfIntake $intake;
    public AbfCase   $case;

    public function __construct(AbfIntake $intake, AbfCase $case)
    {
        $this->intake = $intake;
        $this->case   = $case;
    }

    public function envelope(): Envelope
    {
        $settings   = app(EmailSettings::class);
        $clientName = trim(($this->intake->client_first_name ?? '') . ' ' . ($this->intake->client_last_name ?? ''));

        $payload = $this->case->payload ?? [];
        if (empty($clientName)) {
            $c = $payload['client'] ?? [];
            $clientName = trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')) ?: 'Nouveau client';
        }

        return new Envelope(
            from: new Address($settings->abf_from_email, $settings->abf_from_name),
            subject: "Nouveau profil client reçu — {$clientName}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.intake-completed');
    }
}
