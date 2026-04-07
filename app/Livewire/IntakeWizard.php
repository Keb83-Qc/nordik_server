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
    public string $age_retraite          = '';
    public string $age_retraite_conjoint = '';
    public string $objectifs_texte       = '';

    // ─── Step 8 : Autres revenus ──────────────────────────────────────────────
    public string $autre_revenu_client_montant  = '';
    public string $autre_revenu_client_type     = '';
    public string $autre_revenu_conjoint_montant = '';
    public string $autre_revenu_conjoint_type   = '';

    // ─── Step 9 : Épargne détaillée ───────────────────────────────────────────
    public string $reer_conjoint        = '';
    public string $ferr_client          = '';
    public string $ferr_conjoint        = '';
    public string $celiapp_client       = '';
    public string $celiapp_conjoint     = '';
    public string $fonds_pension_client  = '';
    public string $fonds_pension_conjoint = '';

    // ─── Step 10 : Dettes ─────────────────────────────────────────────────────
    public string $dette_hypotheque  = '';
    public string $dette_auto        = '';
    public string $dette_cartes      = '';
    public string $dette_marge       = '';
    public string $dette_pret_perso  = '';
    public string $dette_autres      = '';

    // ─── Step 11 : Assurance vie ──────────────────────────────────────────────
    public string $ass_vie_client_montant = '';
    public string $ass_vie_client_type    = '';
    public string $ass_vie_client_prime   = '';
    public string $ass_vie_conj_montant   = '';
    public string $ass_vie_conj_type      = '';
    public string $ass_vie_conj_prime     = '';

    // ─── Step 12 : Assurance invalidité ───────────────────────────────────────
    public string $ass_inv_client_rente = '';
    public string $ass_inv_client_prime = '';
    public string $ass_inv_conj_rente   = '';
    public string $ass_inv_conj_prime   = '';

    // ─── Step 13 : Assurance maladie grave ────────────────────────────────────
    public string $ass_mg_client_montant = '';
    public string $ass_mg_client_prime   = '';
    public string $ass_mg_conj_montant   = '';
    public string $ass_mg_conj_prime     = '';

    // ─── Step 14 : Fonds d'urgence ────────────────────────────────────────────
    public string $fu_montant_actuel = '';

    // ─── Step 15 : Retraite ───────────────────────────────────────────────────
    public string $rev_retraite_mensuel      = '';
    public string $rev_retraite_conj_mensuel = '';
    public string $regime_retraite_client    = '';
    public string $regime_retraite_conjoint  = '';

    // ─── Step 16 : Profil d'investisseur ──────────────────────────────────────
    public string $profil_risque   = '';
    public string $profil_horizon  = '';

    // ─── Steps ordonnés ───────────────────────────────────────────────────────

    protected array $allSteps = [
        'identite', 'adresse', 'famille', 'conjoint',
        'revenus', 'autres_revenus', 'epargne',
        'actifs', 'dettes',
        'assurance_vie', 'assurance_invalidite', 'assurance_mg',
        'fonds_urgence', 'retraite', 'objectifs',
        'profil_investisseur',
    ];

    private const STEP_ORDER = [
        'identite', 'adresse', 'famille', 'conjoint',
        'revenus', 'autres_revenus', 'epargne',
        'actifs', 'dettes',
        'assurance_vie', 'assurance_invalidite', 'assurance_mg',
        'fonds_urgence', 'retraite', 'objectifs',
        'profil_investisseur',
    ];

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
        } catch (\Throwable $e) {
            \App\Models\SystemLog::record('warning', '[IntakeWizard.mount] AbfParameter::allAsMap() a échoué', [
                'intake_id' => $intakeId,
                'error'     => $e->getMessage(),
                'trace'     => mb_substr($e->getTraceAsString(), 0, 500),
            ], \App\Models\SystemLog::SOURCE_PUBLIC);
        }

        // Pré-remplir depuis les données partiellement sauvegardées
        try {
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

            \App\Models\SystemLog::record('debug', '[IntakeWizard.mount] composant monté', [
                'intake_id'    => $intakeId,
                'intake_found' => $intake !== null,
                'has_payload'  => $intake && !empty($intake->payload),
                'steps'        => $this->allSteps,
            ], \App\Models\SystemLog::SOURCE_PUBLIC);

        } catch (\Throwable $e) {
            \App\Models\SystemLog::record('error', '[IntakeWizard.mount] erreur hydratation', [
                'intake_id' => $intakeId,
                'error'     => $e->getMessage(),
                'trace'     => mb_substr($e->getTraceAsString(), 0, 500),
            ], \App\Models\SystemLog::SOURCE_PUBLIC);
            throw $e;
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
            'revenus' => $this->validate([
                'revenu_client' => 'required',
            ]),
            'objectifs' => $this->validate([
                'age_retraite' => 'nullable|integer|min:50|max:90',
            ]),
            'profil_investisseur' => $this->validate([
                'profil_risque'  => 'required|in:prudent,modere,equilibre,croissance,audacieux',
                'profil_horizon' => 'required|in:court,moyen,long',
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
            'age_retraite'           => $this->age_retraite,
            'age_retraite_conjoint'  => $this->age_retraite_conjoint,
            'objectifs_texte'        => $this->objectifs_texte,
            // autres_revenus
            'autre_revenu_client_montant'   => $this->autre_revenu_client_montant,
            'autre_revenu_client_type'      => $this->autre_revenu_client_type,
            'autre_revenu_conjoint_montant' => $this->autre_revenu_conjoint_montant,
            'autre_revenu_conjoint_type'    => $this->autre_revenu_conjoint_type,
            // epargne
            'reer_conjoint'         => $this->reer_conjoint,
            'ferr_client'           => $this->ferr_client,
            'ferr_conjoint'         => $this->ferr_conjoint,
            'celiapp_client'        => $this->celiapp_client,
            'celiapp_conjoint'      => $this->celiapp_conjoint,
            'fonds_pension_client'  => $this->fonds_pension_client,
            'fonds_pension_conjoint'=> $this->fonds_pension_conjoint,
            // dettes
            'dette_hypotheque' => $this->dette_hypotheque,
            'dette_auto'       => $this->dette_auto,
            'dette_cartes'     => $this->dette_cartes,
            'dette_marge'      => $this->dette_marge,
            'dette_pret_perso' => $this->dette_pret_perso,
            'dette_autres'     => $this->dette_autres,
            // assurances
            'ass_vie_client_montant' => $this->ass_vie_client_montant,
            'ass_vie_client_type'    => $this->ass_vie_client_type,
            'ass_vie_client_prime'   => $this->ass_vie_client_prime,
            'ass_vie_conj_montant'   => $this->ass_vie_conj_montant,
            'ass_vie_conj_type'      => $this->ass_vie_conj_type,
            'ass_vie_conj_prime'     => $this->ass_vie_conj_prime,
            'ass_inv_client_rente'   => $this->ass_inv_client_rente,
            'ass_inv_client_prime'   => $this->ass_inv_client_prime,
            'ass_inv_conj_rente'     => $this->ass_inv_conj_rente,
            'ass_inv_conj_prime'     => $this->ass_inv_conj_prime,
            'ass_mg_client_montant'  => $this->ass_mg_client_montant,
            'ass_mg_client_prime'    => $this->ass_mg_client_prime,
            'ass_mg_conj_montant'    => $this->ass_mg_conj_montant,
            'ass_mg_conj_prime'      => $this->ass_mg_conj_prime,
            // fonds urgence
            'fu_montant_actuel'          => $this->fu_montant_actuel,
            // retraite
            'rev_retraite_mensuel'       => $this->rev_retraite_mensuel,
            'rev_retraite_conj_mensuel'  => $this->rev_retraite_conj_mensuel,
            'regime_retraite_client'     => $this->regime_retraite_client,
            'regime_retraite_conjoint'   => $this->regime_retraite_conjoint,
            // profil investisseur
            'profil_risque'  => $this->profil_risque,
            'profil_horizon' => $this->profil_horizon,
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

        // Épargne détaillée
        $epargneMap = [
            'reer_conjoint'         => ['REER',      'conjoint'],
            'ferr_client'           => ['FERR',      'client'],
            'ferr_conjoint'         => ['FERR',      'conjoint'],
            'celiapp_client'        => ['CELIAPP',   'client'],
            'celiapp_conjoint'      => ['CELIAPP',   'conjoint'],
            'fonds_pension_client'  => ['Fonds de pension', 'client'],
            'fonds_pension_conjoint'=> ['Fonds de pension', 'conjoint'],
        ];
        foreach ($epargneMap as $prop => [$label, $owner]) {
            $raw = $this->$prop;
            if ($raw !== '' && is_numeric(str_replace([' ', ','], ['', '.'], $raw))) {
                $val = (float) str_replace([' ', ','], ['', '.'], $raw);
                $actifs[] = [
                    '_type'       => $label,
                    '_valeur'     => $val,
                    '_owner'      => $owner,
                    '_modalType'  => 'placement',
                    'description' => $label . ($owner === 'conjoint' ? ' (conjoint)' : ''),
                    'valeur'      => (string) $val,
                ];
            }
        }

        // Autres revenus
        $autresRevenus = [
            ['montant' => $this->autre_revenu_client_montant,   'type' => $this->autre_revenu_client_type,   'owner' => 'client'],
            ['montant' => $this->autre_revenu_conjoint_montant, 'type' => $this->autre_revenu_conjoint_type, 'owner' => 'conjoint'],
        ];
        foreach ($autresRevenus as $ar) {
            if ($ar['montant'] !== '' && is_numeric(str_replace([' ', ','], ['', '.'], $ar['montant']))) {
                $montantNum = (float) str_replace([' ', ','], ['', '.'], $ar['montant']);
                $revenus[] = [
                    'type'        => $ar['type'] ?: 'Autre',
                    'owner'       => $ar['owner'],
                    'isEmploi'    => false,
                    'description' => $ar['type'] ?: 'Autre revenu',
                    'montant'     => (string) $montantNum,
                    'frequence'   => 'Annuelle',
                    'freqFactor'  => 1,
                    'annuel'      => $montantNum,
                ];
            }
        }

        // Dettes / passifs
        $passifs = [];
        $dettesMap = [
            'dette_hypotheque' => 'Hypothèque',
            'dette_auto'       => 'Prêt auto',
            'dette_cartes'     => 'Cartes de crédit',
            'dette_marge'      => 'Marge de crédit',
            'dette_pret_perso' => 'Prêt personnel',
            'dette_autres'     => 'Autres dettes',
        ];
        foreach ($dettesMap as $prop => $label) {
            $raw = $this->$prop;
            if ($raw !== '' && is_numeric(str_replace([' ', ','], ['', '.'], $raw))) {
                $val = (float) str_replace([' ', ','], ['', '.'], $raw);
                $passifs[] = [
                    '_type'       => $label,
                    '_valeur'     => $val,
                    '_owner'      => 'client',
                    '_modalType'  => 'passif',
                    'description' => $label,
                    'solde'       => (string) $val,
                ];
            }
        }

        // Assurances en vigueur
        $assurances = [];
        if ($this->ass_vie_client_montant !== '') {
            $assurances[] = ['categorie' => 'vie', 'owner' => 'client',
                'montant' => $this->ass_vie_client_montant, 'type' => $this->ass_vie_client_type, 'prime' => $this->ass_vie_client_prime];
        }
        if ($hasSpouse && $this->ass_vie_conj_montant !== '') {
            $assurances[] = ['categorie' => 'vie', 'owner' => 'conjoint',
                'montant' => $this->ass_vie_conj_montant, 'type' => $this->ass_vie_conj_type, 'prime' => $this->ass_vie_conj_prime];
        }
        if ($this->ass_inv_client_rente !== '') {
            $assurances[] = ['categorie' => 'invalidite', 'owner' => 'client',
                'rente' => $this->ass_inv_client_rente, 'prime' => $this->ass_inv_client_prime];
        }
        if ($hasSpouse && $this->ass_inv_conj_rente !== '') {
            $assurances[] = ['categorie' => 'invalidite', 'owner' => 'conjoint',
                'rente' => $this->ass_inv_conj_rente, 'prime' => $this->ass_inv_conj_prime];
        }
        if ($this->ass_mg_client_montant !== '') {
            $assurances[] = ['categorie' => 'maladie_grave', 'owner' => 'client',
                'montant' => $this->ass_mg_client_montant, 'prime' => $this->ass_mg_client_prime];
        }
        if ($hasSpouse && $this->ass_mg_conj_montant !== '') {
            $assurances[] = ['categorie' => 'maladie_grave', 'owner' => 'conjoint',
                'montant' => $this->ass_mg_conj_montant, 'prime' => $this->ass_mg_conj_prime];
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
            'passifs' => $passifs,
            'legal'   => [],
            'assurances_en_vigueur' => $assurances,
            'fonds_urgence_actuel'  => $this->fu_montant_actuel,
            'retraite' => [
                'ageClient'           => $this->age_retraite ?: '65',
                'typeClient'          => 'age',
                'ageConjoint'         => ($hasSpouse && $this->age_retraite_conjoint) ? $this->age_retraite_conjoint : '65',
                'typeConjoint'        => 'age',
                'revMensuelClient'    => $this->rev_retraite_mensuel,
                'revMensuelConjoint'  => $this->rev_retraite_conj_mensuel,
                'regimeClient'        => $this->regime_retraite_client,
                'regimeConjoint'      => $this->regime_retraite_conjoint,
            ],
            'profil_investisseur' => [
                'client' => [
                    'profil'        => $this->profil_risque,
                    'score'         => $this->profilToScore($this->profil_risque),
                    'horizon'       => $this->profil_horizon,
                    'intake_source' => true,
                ],
            ],
            'navigation' => [
                'done_pages'       => ['infos-perso'],
                'intake_source'    => true,
                'objectifs_client' => $this->objectifs_texte,
            ],
        ];
    }

    protected function profilToScore(string $profil): int
    {
        return match($profil) {
            'prudent'    => 20,
            'modere'     => 40,
            'equilibre'  => 73,
            'croissance' => 106,
            'audacieux'  => 140,
            default      => 0,
        };
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
