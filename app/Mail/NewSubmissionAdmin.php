<?php

namespace App\Mail;

use App\Models\ChatStep;
use App\Models\QuotePortal;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class NewSubmissionAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public Submission $submission;
    public ?User $advisor;
    public ?QuotePortal $portal;
    public Collection $chatSteps;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
        $this->advisor    = User::where('advisor_code', $submission->advisor_code)->first();
        $this->portal     = $submission->portal;

        // Charge les steps actifs pour ce type (auto / habitation)
        // Les champs non hardcodés dans l'email seront affichés dynamiquement.
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
        // 1. Nom du conseiller (ou Général)
        $advisorName = $this->advisor ? $this->advisor->first_name : 'Général';

        // 2. Nom du client (Sécurisé avec ??)
        $data = $this->submission->data ?? [];
        $clientName = ($data['first_name'] ?? 'Client') . ' ' . ($data['last_name'] ?? '');

        // 3. Type de soumission (ex: Auto, Habitation) - Première lettre majuscule
        $type = ucfirst($this->submission->type ?? 'Soumission');

        // Résultat : "[Auto] Nouvelle Soumission (Julie) - Jean Dupont"
        // Ajouter le nom du portail partenaire dans le sujet si applicable
        $portalTag = ($this->portal && $this->portal->isPartner())
            ? ' [' . $this->portal->name . ']'
            : '';

        return new Envelope(
            subject: "[$type]{$portalTag} Nouvelle Soumission ($advisorName) - $clientName",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-submission',
        );
    }
}
