<?php

namespace App\Mail;

use App\Models\Submission;
use App\Settings\EmailSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $clientName;
    public string $vehicle;

    public function __construct(
        public Submission $submission,
        public string     $filamentUrl,
    ) {
        $data = $this->submission->data ?? [];

        $this->clientName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')) ?: 'Client';
        $this->vehicle    = trim(
            ($data['vehicle_year'] ?? $data['year'] ?? '-') . ' ' .
            ($data['vehicle_brand_name'] ?? $data['brand'] ?? '-') . ' ' .
            ($data['vehicle_model_name'] ?? $data['model'] ?? '')
        ) ?: '-';
    }

    public function envelope(): Envelope
    {
        $settings = app(EmailSettings::class);

        return new Envelope(
            from: new Address($settings->internal_from_email, $settings->internal_from_name),
            subject: "Nouvelle soumission auto - {$this->clientName}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.submission-notification');
    }
}
