<?php

namespace App\Mail;

use App\Models\Message;
use App\Settings\EmailSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Message $message) {}

    public function envelope(): Envelope
    {
        $settings = app(EmailSettings::class);

        $type   = $this->message->data['type'] ?? 'system';
        $prio   = $this->message->data['priority'] ?? null;
        $sender = $this->message->sender
            ? trim($this->message->sender->first_name . ' ' . $this->message->sender->last_name)
            : 'Système';

        $prefix = match ($type) {
            'bug_report' => $prio === 'high' ? '[BUG URGENT]' : '[Bug Report]',
            default      => '[Demande Système]',
        };

        return new Envelope(
            from: new Address($settings->alert_from_email, $settings->alert_from_name),
            subject: "{$prefix} {$this->message->subject} — {$sender}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-alert');
    }
}
