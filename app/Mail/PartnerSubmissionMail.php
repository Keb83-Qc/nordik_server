<?php

namespace App\Mail;

use App\Models\Submission;
use App\Models\QuotePortal;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public Submission $submission;
    public QuotePortal $portal;
    public ?User $advisor;

    public function __construct(Submission $submission, QuotePortal $portal)
    {
        $this->submission = $submission;
        $this->portal     = $portal;
        $this->advisor    = User::where('advisor_code', $submission->advisor_code)->first();
    }

    public function envelope(): Envelope
    {
        $data       = $this->submission->data ?? [];
        $clientName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')) ?: 'Client';
        $type       = ucfirst($this->submission->type ?? 'Soumission');

        return new Envelope(
            subject: "[{$type}] Nouvelle soumission via {$this->portal->name} — {$clientName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.partner-submission',
        );
    }
}
