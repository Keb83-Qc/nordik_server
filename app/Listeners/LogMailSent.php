<?php

namespace App\Listeners;

use App\Models\SystemLog;
use Illuminate\Mail\Events\MessageSent;

class LogMailSent
{
    public function handle(MessageSent $event): void
    {
        try {
            $data    = $event->data ?? [];
            $dept    = $data['emailDept'] ?? SystemLog::SOURCE_EMAIL_SYSTEM;
            $message = $event->sent->getOriginalMessage();

            $to = collect($event->sent->getEnvelope()->getRecipients())
                ->map(fn($r) => $r->getAddress())
                ->implode(', ');

            $subject = $message->getSubject() ?? '(sans sujet)';

            SystemLog::record('info', 'Email envoyé : ' . $subject, [
                'to'         => $to,
                'subject'    => $subject,
                'department' => $dept,
            ], $dept);
        } catch (\Throwable) {
            // Ne pas laisser un bug de log bloquer l'application
        }
    }
}
