<?php

namespace App\Livewire;

use App\Mail\IntakeCompletedMail;
use App\Models\AbfCase;
use App\Models\AbfIntake;
use App\Models\AbfParameter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class IntakeWizard extends Component
{
    public int    $intakeId;
    public string $locale = 'fr';
    public string $step   = 'identite';

    // ─── Step 1 : Identité ────────────────────────────────────────────────────
    public string $prenom     = '';
    public string $nom        = '';
    public string $sexe       = '';
    public string $ddn_jour   = '';
    public string $ddn_mois   = '';
    public string $ddn_annee  = '';
    public string $courriel   = '';
    public string $cellulaire = '';

    // ─── Step 2 : Adresse ─────────────────────────────────────────────────────
    public string $addr_civique  = '';
    public string $addr_rue      = '';
    public string $addr_ville    = '';
    public string $addr_province = '';
    public string $addr_postal   = '';

    // ─── Step 3 : Situation familiale ─────────────────────────────────────────
    public string $etat_civil    = '';
    public int    $nb_enfants    = 0;

    // ─── Step 4 : Conjoint ────────────────────────────────────────────────────
    public string $conj_prenom    = '';
    public string $conj_nom       = '';
    public string $conj_sexe      = '';
    public string $conj_ddn_jour  = '';
    public string $conj_ddn_mois  = '';
    public string $conj_ddn_annee = '';
    public string $conj_courriel  = '';

    // ─── Step 5 : Revenus ─────────────────────────────────────────────────────
    public string $revenu_client  = '';
    public string $revenu_conjoint = '';

    // ─── Step 6 : Actifs ──────────────────────────────────────────────────────
    public bool   $a_propriete       = false;
    public string $valeur_propriete  = '';
    public string $valeur_reer       = '';
    public string $valeur_celi       = '';
    public string $valeur_placements = '';

    // ─── Step 7 : Objectifs ───────────────────────────────────────────────────
    public string $age_retraite        = '';
    public string $age_retraite_conjoint = '';
    public string $objectifs_texte     = '';

    // ─── Steps ordonnés ───────────────────────────────────────────────────────

    protected array $allSteps = [
        'identite',
        'adresse',
        'famille',
        'conjoint',
        'revenus',
        'actifs',
        'objectifs',
    ];

    // Ordre canonique pour s'assurer que les steps configurés restent dans le bon ordre
    private const STEP_ORDER = ['identite', 'adresse', 'famille', 'conjoint', 'revenus', 'actifs', 'objectifs'];

    // ─── Mount ────────────────────────────────────────────────────────────────

    public function mount(int $intakeId, string $locale = 'fr'): void
    {
        $this->intakeId = $intakeId;
        $this->locale   = $locale;

        // Charger les sections activées depuis la configuration admin
        try {
            $p = AbfParameter::allAsMap();
            $raw = $p['intake']['steps_enabled'] ?? null;
            if ($raw) {
                $configured = json_decode($raw, true);
                if (is_array($configured)) {
                    // Toujours inclure identite + conjoint, dans l'ordre canonique
                    $enabled = array_merge(['identite'], $configured, ['conjoint']);
                    $this->allSteps = array_values(array_filter(
                        self::STEP_ORDER,
                        fn($s) => in_array($s, $enabled, true)
                    ));
                }
            }
        } catch (\Throwable) {
            // Garde les steps par défaut si la table n'existe pas encore
        }

        // Pré-remplir depuis les données partiellement sauvegardées
        $intake = AbfIntake::find($intakeId);
        if ($intake && $intake->payload) {
            $this->hydrateFromPayload($intake->payload);
        }

        // Pré-remplir prénom/nom depuis l'intake si connu
        if ($intake) {
            if ($this->prenom === '' && $intake->client_first_name) $this->prenom = $intake->client_first_name;
            if ($this->nom   === '' && $intake->client_last_name)  $this->nom    = $intake->client_last_name;
            if ($this->courriel === '' && $intake->client_email)   $this->courriel = $intake->client_email;
        }
    }

    // ─── Navigation ───────────────────────────────────────────────────────────

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->saveProgress();

        $steps = $this->activeSteps();
        $idx   = array_search($this->step, $steps);

        if ($idx !== false && $idx < count($steps) - 1) {
            $this->step = $steps[$idx + 1];
        }
    }

    public function prevStep(): void
    {
        $steps = $this->activeSteps();
        $idx   = array_search($this->step, $steps);

        if ($idx > 0) {
            $this->step = $steps[$idx - 1];
        }
    }

    /**
     * Retourne les steps actifs (conjoint uniquement si marié/conjoint de fait).
     */
    protected function activeSteps(): array
    {
        $steps = $this->allSteps;
        if (!$this->hasSpouse()) {
            $steps = array_values(array_filter($steps, fn($s) => $s !== 'conjoint'));
        }
        return $steps;
    }

    protected function hasSpouse(): bool
    {
        return in_array($this->etat_civil, ['marie', 'conjoint_fait']);
    }

    public function stepNumber(): int
    {
        return (array_search($this->step, $this->activeSteps()) ?: 0) + 1;
    }

    public function totalSteps(): int
    {
        return count($this->activeSteps());
    }

    public function progressPct(): int
    {
        return (int) round(($this->stepNumber() / $this->totalSteps()) * 100);
    }

    // ─── Validation par step ──────────────────────────────────────────────────

    protected function validateCurrentStep(): void
    {
        match ($this->step) {
            'identite' => $this->validate([
                'prenom'    => 'required|string|max:100',
                'nom'       => 'required|string|max:100',
                'sexe'      => 'required|string',
                'ddn_annee' => 'required|digits:4|integer|min:1920|max:' . (date('Y') - 18),
                'courriel'  => 'required|email|max:255',
                'cellulaire'=> 'required|string|max:30',
            ]),
            'adresse' => $this->validate([
                'addr_ville'    => 'required|string|max:100',
                'addr_province' => 'required|string|max:50',
                'addr_postal'   => 'required|string|max:10',
            ]),
            'famille' => $this->validate([
                'etat_civil' => 'required|string',
            ]),
            'conjoint' => $this->validate([
                'conj_prenom'    => 'required|string|max:100',
                'conj_nom'       => 'required|string|max:100',
                'conj_sexe'      => 'required|string',
                'conj_ddn_annee' => 'required|digits:4|integer|min:1920|max:' . (date('Y') - 18),
            ]),
            default => null,
        };
    }

    // ─── Sauvegarde progressive ───────────────────────────────────────────────

    protected function saveProgress(): void
    {
        AbfIntake::where('id', $this->intakeId)->update([
            'payload' => $this->buildPartialPayload(),
            'status'  => 'in_progress',
        ]);
    }

    protected function buildPartialPayload(): array
    {
        return [
            'prenom'            => $this->prenom,
            'nom'               => $this->nom,
            'sexe'              => $this->sexe,
            'ddn_jour'          => $this->ddn_jour,
            'ddn_mois'          => $this->ddn_mois,
            'ddn_annee'         => $this->ddn_annee,
            'courriel'          => $this->courriel,
            'cellulaire'        => $this->cellulaire,
            'addr_civique'      => $this->addr_civique,
            'addr_rue'          => $this->addr_rue,
            'addr_ville'        => $this->addr_ville,
            'addr_province'     => $this->addr_province,
            'addr_postal'       => $this->addr_postal,
            'etat_civil'        => $this->etat_civil,
            'nb_enfants'        => $this->nb_enfants,
            'conj_prenom'       => $this->conj_prenom,
            'conj_nom'          => $this->conj_nom,
            'conj_sexe'         => $this->conj_sexe,
            'conj_ddn_jour'     => $this->conj_ddn_jour,
            'conj_ddn_mois'     => $this->conj_ddn_mois,
            'conj_ddn_annee'    => $this->conj_ddn_annee,
            'conj_courriel'     => $this->conj_courriel,
            'revenu_client'     => $this->revenu_client,
            'revenu_conjoint'   => $this->revenu_conjoint,
            'a_propriete'       => $this->a_propriete,
            'valeur_propriete'  => $this->valeur_propriete,
            'valeur_reer'       => $this->valeur_reer,
            'valeur_celi'       => $this->valeur_celi,
            'valeur_placements' => $this->valeur_placements,
            'age_retraite'      => $this->age_retraite,
            'age_retraite_conjoint' => $this->age_retraite_conjoint,
            'objectifs_texte'   => $this->objectifs_texte,
        ];
    }

    protected function hydrateFromPayload(array $p): void
    {
        foreach ($p as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    // ─── Soumission finale ────────────────────────────────────────────────────

    public function submit(): void
    {
        $this->validate([
            'age_retraite' => 'nullable|integer|min:50|max:90',
        ]);

        $intake = AbfIntake::with('advisor')->findOrFail($this->intakeId);

        // Construire le payload ABF
        $abfPayload = $this->buildAbfPayload();

        // Créer le dossier ABF
        $case = AbfCase::create([
            'advisor_user_id'   => $intake->advisor_user_id,
            'advisor_code'      => $intake->advisor->advisor_code,
            'client_first_name' => $this->prenom,
            'client_last_name'  => $this->nom,
            'client_birth_date' => $this->buildBirthDate(),
            'status'            => 'nouveau',
            'payload'           => $abfPayload,
        ]);

        // Générer le slug
        $case->slug = $case->generateSlug();
        $case->save();

        // Marquer l'intake comme complété
        $intake->update([
            'status'      => 'completed',
            'abf_case_id' => $case->id,
            'payload'     => $this->buildPartialPayload(),
        ]);

        // Notifier le conseiller
        $this->notifyAdvisor($intake, $case);

        // Rediriger vers la page de remerciement
        redirect()->route('intake.merci', [
            'advisorSlug' => $intake->advisor->slug,
            'token'       => $intake->token,
        ]);
    }

    /**
     * Construit le payload JSON au format ABF complet.
     */
    protected function buildAbfPayload(): array
    {
        $hasSpouse = $this->hasSpouse();

        $actifs  = [];
        $revenus = [];

        // Revenus emploi client
        if ($this->revenu_client !== '' && is_numeric(str_replace([' ', ','], ['', '.'], $this->revenu_client))) {
            $montantNum = (float) str_replace([' ', ','], ['', '.'], $this->revenu_client);
            $revenus[] = [
                'type'       => 'Emploi',
                'owner'      => 'client',
                'isEmploi'   => true,
                'description'=> 'Revenus d\'emploi',
                'montant'    => (string) $montantNum,
                'frequence'  => 'Annuelle',
                'freqFactor' => 1,
                'annuel'     => $montantNum,
            ];
        }

        // Revenus emploi conjoint
        if ($hasSpouse && $this->revenu_conjoint !== '' && is_numeric(str_replace([' ', ','], ['', '.'], $this->revenu_conjoint))) {
            $montantNum = (float) str_replace([' ', ','], ['', '.'], $this->revenu_conjoint);
            $revenus[] = [
                'type'       => 'Emploi',
                'owner'      => 'conjoint',
                'isEmploi'   => true,
                'description'=> 'Revenus d\'emploi',
                'montant'    => (string) $montantNum,
                'frequence'  => 'Annuelle',
                'freqFactor' => 1,
                'annuel'     => $montantNum,
            ];
        }

        // Actifs : propriété principale
        if ($this->a_propriete && $this->valeur_propriete !== '') {
            $val = (float) str_replace([' ', ','], ['', '.'], $this->valeur_propriete);
            $actifs[] = [
                '_type'       => 'Résidence principale',
                '_valeur'     => $val,
                '_owner'      => 'client',
                '_modalType'  => 'bien',
                'description' => 'Résidence principale',
                'valeur'      => (string) $val,
            ];
        }

        // Actifs : REER
        if ($this->valeur_reer !== '') {
            $val = (float) str_replace([' ', ','], ['', '.'], $this->valeur_reer);
            $actifs[] = [
                '_type'       => 'REER',
                '_valeur'     => $val,
                '_owner'      => 'client',
                '_modalType'  => 'placement',
                'description' => 'REER',
                'valeur'      => (string) $val,
            ];
        }

        // Actifs : CELI
        if ($this->valeur_celi !== '') {
            $val = (float) str_replace([' ', ','], ['', '.'], $this->valeur_celi);
            $actifs[] = [
                '_type'       => 'CELI',
                '_valeur'     => $val,
                '_owner'      => 'client',
                '_modalType'  => 'placement',
                'description' => 'CELI',
                'valeur'      => (string) $val,
            ];
        }

        // Actifs : autres placements
        if ($this->valeur_placements !== '') {
            $val = (float) str_replace([' ', ','], ['', '.'], $this->valeur_placements);
            $actifs[] = [
                '_type'       => 'Placements',
                '_valeur'     => $val,
                '_owner'      => 'client',
                '_modalType'  => 'placement',
                'description' => 'Autres placements',
                'valeur'      => (string) $val,
            ];
        }

        // Enfants (placeholders)
        $enfants = [];
        for ($i = 0; $i < (int) $this->nb_enfants; $i++) {
            $enfants[] = [
                'prenom'   => '',
                'nom'      => '',
                'sexe'     => '',
                'jour'     => '',
                'mois'     => '',
                'annee'    => '',
                'relation' => 'enfant',
                'charge'   => 'oui',
            ];
        }

        return [
            'client' => [
                'prenom'         => $this->prenom,
                'nom'            => $this->nom,
                'sexe'           => $this->sexe,
                'ddn_jour'       => $this->ddn_jour,
                'ddn_mois'       => $this->ddn_mois,
                'ddn_annee'      => $this->ddn_annee,
                'etat_civil'     => $this->etat_civil,
                'addr_civique'   => $this->addr_civique,
                'addr_rue'       => $this->addr_rue,
                'addr_ville'     => $this->addr_ville,
                'addr_province'  => $this->addr_province,
                'addr_postal'    => $this->addr_postal,
                'courriel'       => $this->courriel,
                'cellulaire'     => $this->cellulaire,
                'telephone'      => '',
            ],
            'has_spouse' => $hasSpouse,
            'conjoint' => $hasSpouse ? [
                'prenom'     => $this->conj_prenom,
                'nom'        => $this->conj_nom,
                'sexe'       => $this->conj_sexe,
                'ddn_jour'   => $this->conj_ddn_jour,
                'ddn_mois'   => $this->conj_ddn_mois,
                'ddn_annee'  => $this->conj_ddn_annee,
                'courriel'   => $this->conj_courriel,
                'etat_civil' => $this->etat_civil,
            ] : [],
            'enfants' => $enfants,
            'revenus' => $revenus,
            'actifs'  => $actifs,
            'passifs' => [],
            'legal'   => [],
            'retraite' => [
                'ageClient'   => $this->age_retraite ?: '65',
                'typeClient'  => 'age',
                'ageConjoint' => ($hasSpouse && $this->age_retraite_conjoint) ? $this->age_retraite_conjoint : '65',
                'typeConjoint'=> 'age',
            ],
            'navigation' => [
                'done_pages'     => ['infos-perso'],
                'intake_source'  => true,
                'objectifs_client' => $this->objectifs_texte,
            ],
        ];
    }

    protected function buildBirthDate(): ?string
    {
        if ($this->ddn_annee && $this->ddn_mois && $this->ddn_jour) {
            return sprintf('%04d-%02d-%02d', $this->ddn_annee, $this->ddn_mois, $this->ddn_jour);
        }
        if ($this->ddn_annee) {
            return $this->ddn_annee . '-01-01';
        }
        return null;
    }

    // ─── Notifications ────────────────────────────────────────────────────────

    protected function notifyAdvisor(AbfIntake $intake, AbfCase $case): void
    {
        $advisor = $intake->advisor;

        // Email
        try {
            Mail::to($advisor->email)->send(new IntakeCompletedMail($intake, $case));
        } catch (\Throwable $e) {
            Log::error("IntakeCompletedMail error: " . $e->getMessage());
        }

        // Notification Filament (base de données)
        try {
            Notification::make()
                ->title('Nouveau profil client reçu')
                ->body("{$this->prenom} {$this->nom} a rempli son profil. Le dossier ABF est prêt.")
                ->success()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('view')
                        ->label('Ouvrir le dossier')
                        ->url($case->editor_url),
                ])
                ->sendToDatabase($advisor);
        } catch (\Throwable $e) {
            Log::error("Filament notification error: " . $e->getMessage());
        }
    }

    // ─── Traductions ──────────────────────────────────────────────────────────

    public function t(string $key): string
    {
        return IntakeTranslations::get($key, $this->locale);
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.intake-wizard', [
            'steps'       => $this->activeSteps(),
            'stepNum'     => $this->stepNumber(),
            'totalSteps'  => $this->totalSteps(),
            'progressPct' => $this->progressPct(),
            'hasSpouse'   => $this->hasSpouse(),
        ]);
    }
}
