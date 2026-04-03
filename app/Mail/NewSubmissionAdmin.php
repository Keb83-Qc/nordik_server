<?php

namespace App\Mail;

use App\Models\ChatStep;
use App\Models\QuotePortal;
use App\Models\Submission;
use App\Models\User;
use App\Settings\EmailSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class NewSubmissionAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailDept = 'email_internal';

    public Submission $submission;
    public ?User $advisor;
    public ?QuotePortal $portal;
    public Collection $chatSteps;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
        $this->advisor    = User::where('advisor_code', $submission->advisor_code)->first();
        $this->portal     = $submission->portal;

        $type = $submission->type ?? '';
        $this->chatSteps = in_array($type, ['auto', 'habitation'])
            ? ChatStep::where('chat_type', $type)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
            : collect();
    }

    public function envelope(): Envelope
    {
        $settings = app(EmailSettings::class);

        $advisorName = $this->advisor ? $this->advisor->first_name : 'Général';
        $data        = $this->submission->data ?? [];
        $clientName  = ($data['first_name'] ?? 'Client') . ' ' . ($data['last_name'] ?? '');
        $type        = ucfirst($this->submission->type ?? 'Soumission');
        $portalTag   = ($this->portal && $this->portal->isPartner())
            ? ' [' . $this->portal->name . ']'
            : '';

        return new Envelope(
            from: new Address($settings->internal_from_email, $settings->internal_from_name),
            subject: "[$type]{$portalTag} Nouvelle Soumission ($advisorName) - $clientName",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-submission');
    }
}
