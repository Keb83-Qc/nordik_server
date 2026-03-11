<?php

namespace App\Livewire\Concerns;

use App\Mail\NewSubmissionAdmin;
use App\Models\Submission;
use App\Models\User;
use App\Services\LeadDispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Trait partagé entre QuoteAutoChat, QuoteHomeChat et QuoteBundleChat.
 *
 * Centralise :  mount, persist, calculateStep, finalize, goToStep, fillProperties
 *
 * Le composant doit définir :
 *   - chatType(): string            → 'auto' | 'habitation' | 'bundle'
 *   - sessionKey(): string          → clé session pour l'ID de soumission
 *   - validSteps(): array           → whitelist des noms de steps
 *   - defaultAgentImage(): string   → fallback image
 *   - stepOrder(): array            → array step => field(s) requis
 *   - afterPersist(): void          → hook post-persist (calculateStep custom, etc.)
 */
trait HasChatSteps
{
    public string $step = '';
    public array $data = [];
    public ?Submission $submission = null;

    public ?string $advisorCode = null;
    public string $agentName = 'Julie';
    public ?string $agentImage = null;

    // ──────────────────────────────────────────────
    //  Abstract contract — chaque composant l'implémente
    // ──────────────────────────────────────────────

    abstract protected function chatType(): string;
    abstract protected function sessionKey(): string;
    abstract protected function validSteps(): array;
    abstract protected function defaultAgentImage(): string;

    /**
     * Retourne la map ordonnée step => champ(s) requis.
     * Ex: ['year' => 'year', 'identity' => ['first_name','last_name']]
     */
    abstract protected function stepOrder(): array;

    /**
     * Hook appelé après chaque persist. Le composant peut y recalculer
     * le step, hydrater des modèles, etc.
     */
    abstract protected function afterPersist(): void;

    /**
     * Hook appelé après hydratation depuis la DB (mount avec session existante).
     */
    abstract protected function afterHydrate(): void;

    // ──────────────────────────────────────────────
    //  Mount partagé
    // ──────────────────────────────────────────────

    protected function mountChat(LeadDispatcher $dispatcher): void
    {
        // Consent check
        if (!session('has_consented')) {
            redirect()->route('consent.show', [
                'locale' => app()->getLocale(),
                'code'   => session('current_advisor_code'),
            ]);
            return;
        }

        // Advisor assignment
        if (!session()->has('current_advisor_code')) {
            $assigned = $dispatcher->assignAdvisor();
            if ($assigned) {
                session(['current_advisor_code' => $assigned->advisor_code]);
            }
        }

        $this->advisorCode = session('current_advisor_code');

        $advisor = $this->advisorCode
            ? User::where('advisor_code', $this->advisorCode)->first()
            : null;

        if ($advisor) {
            $this->agentName  = $advisor->first_name;
            $this->agentImage = $advisor->image_url;
        } else {
            $this->agentImage = $this->defaultAgentImage();
        }

        // Hydrate from session
        if (session()->has($this->sessionKey())) {
            $sub = Submission::find(session($this->sessionKey()));
            if ($sub) {
                $this->submission = $sub;
                $this->data = $sub->data ?? $this->data;
                $this->fillPropertiesFromData();
                $this->afterHydrate();
                return;
            }
        }

        // Nouvel utilisateur — calcule la première étape
        $this->calculateStep();
    }

    // ──────────────────────────────────────────────
    //  Persist (flat data — Auto / Home)
    // ──────────────────────────────────────────────

    public function persist(string $key, mixed $value): void
    {
        $this->data[$key] = $value;

        if ($this->submission === null) {
            // Lazy creation: only write to DB once we have an email
            if (!empty($this->data['email'])) {
                $this->submission = Submission::create([
                    'type'         => $this->chatType(),
                    'advisor_code' => $this->advisorCode,
                    'data'         => $this->data,
                ]);
                session([$this->sessionKey() => $this->submission->id]);
            }
            // else: keep data in Livewire snapshot only until email is provided
        } else {
            $this->submission->update(['data' => $this->data]);
        }

        $this->afterPersist();
        $this->dispatch('scroll-down');
    }

    // ──────────────────────────────────────────────
    //  Calculate step (itère stepOrder)
    // ──────────────────────────────────────────────

    public function calculateStep(): void
    {
        $this->step = 'final';

        foreach ($this->stepOrder() as $stepName => $requiredFields) {
            $missing = false;

            if (is_array($requiredFields)) {
                foreach ($requiredFields as $field) {
                    if (!isset($this->data[$field]) || $this->data[$field] === '') {
                        $missing = true;
                        break;
                    }
                }
            } else {
                if (!isset($this->data[$requiredFields]) || $this->data[$requiredFields] === '') {
                    $missing = true;
                }
            }

            // Skip conditionnel — le composant peut override
            if ($missing && $this->shouldSkipStep($stepName)) {
                $missing = false;
            }

            if ($missing) {
                $this->step = $stepName;
                break;
            }
        }
    }

    /**
     * Override dans le composant pour gérer les steps conditionnels.
     */
    protected function shouldSkipStep(string $step): bool
    {
        return false;
    }

    // ──────────────────────────────────────────────
    //  GoToStep — avec whitelist sécurisée
    // ──────────────────────────────────────────────

    public function goToStep(string $name): void
    {
        if (!in_array($name, $this->validSteps(), true)) {
            return;
        }

        $this->step = $name;
        $this->dispatch('scroll-down');
    }

    // ──────────────────────────────────────────────
    //  Fill properties from data
    // ──────────────────────────────────────────────

    protected function fillPropertiesFromData(): void
    {
        foreach ($this->data as $key => $value) {
            if (property_exists($this, $key) && !in_array($key, ['step', 'data', 'submission'], true)) {
                $this->$key = $value;
            }
        }
    }

    // ──────────────────────────────────────────────
    //  Finalize — envoi email + cleanup
    // ──────────────────────────────────────────────

    public function finalize()
    {
        // Fallback: reload from session if Livewire hydration missed the model
        if ($this->submission === null && session()->has($this->sessionKey())) {
            $this->submission = Submission::find(session($this->sessionKey()));
        }

        if ($this->submission === null) {
            Log::error("finalize(): submission introuvable pour {$this->chatType()} advisor={$this->advisorCode}");
            return;
        }

        $recipients = array_filter([
            config('mail.submission_broker_to') ?: config('mail.from.address'),
            User::where('advisor_code', $this->advisorCode)->value('email'),
        ]);
        $recipients = array_values(array_unique($recipients));

        $type = ucfirst($this->chatType());

        if (!empty($recipients)) {
            try {
                Mail::to($recipients)->send(new NewSubmissionAdmin($this->submission));
                Log::info("Soumission {$type} {$this->submission->id} envoyée à : " . implode(', ', $recipients));
            } catch (\Throwable $e) {
                Log::error("Erreur Mail {$type} {$this->submission->id}: " . $e->getMessage());
            }
        } else {
            Log::warning("Aucun destinataire pour {$type} {$this->submission->id}");
        }

        session(['last_advisor_code' => $this->advisorCode]);

        session()->forget([
            $this->sessionKey(),
            'current_advisor_code',
        ]);

        return redirect()->route('quote.success', ['locale' => app()->getLocale()]);
    }
}
