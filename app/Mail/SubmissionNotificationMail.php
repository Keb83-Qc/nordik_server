<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubmissionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Submission $submission,
        public string $filamentUrl,
    ) {}

    public function build()
    {
        $data = $this->submission->data ?? [];

        $clientName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        $vehicle = trim(
            ($data['vehicle_year'] ?? $data['year'] ?? '-') . ' ' .
                ($data['vehicle_brand_name'] ?? $data['brand'] ?? '-') . ' ' .
                ($data['vehicle_model_name'] ?? $data['model'] ?? '')
        );

        return $this->subject("Nouvelle soumission auto - {$clientName}")
            ->view('emails.submission-notification')
            ->with([
                'submission' => $this->submission,
                'clientName' => $clientName ?: 'Client',
                'vehicle' => $vehicle ?: '-',
                'filamentUrl' => $this->filamentUrl,
            ]);
    }
}
