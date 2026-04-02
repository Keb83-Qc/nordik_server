<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadDispatcher
{
    public function assignAdvisor()
    {
        return DB::transaction(function () {
            // 1. Trouver les conseillers éligibles (Actifs)
            $candidates = User::where('accepts_leads', true)->get();

            if ($candidates->isEmpty()) {
                // Fallback : Si personne n'est actif, on retourne un admin ou null
                return User::where('email', app(\App\Settings\EmailSettings::class)->fallback_admin_email)->first();
            }

            // 2. Filtrer ceux qui n'ont pas encore rempli leur quota pour ce cycle
            // Ex: Si le poids est 2 et qu'il a reçu 1, il est éligible.
            // Boucle (max 2 itérations) au lieu d'une récursion pour éviter tout stack overflow
            // si lead_weight = 0 pour tous les conseillers actifs.
            $eligible = collect();
            for ($attempt = 0; $attempt < 2; $attempt++) {
                $eligible = $candidates->filter(fn($user) => $user->leads_received_cycle < $user->lead_weight);

                if ($eligible->isNotEmpty()) {
                    break;
                }

                // 3. FIN DU CYCLE : tout le monde a atteint son quota — on remet à 0 et on re-fetch
                User::where('accepts_leads', true)->update(['leads_received_cycle' => 0]);
                $candidates = User::where('accepts_leads', true)->get();
            }

            // Sécurité : si toujours vide (ex: lead_weight = 0 pour tous), on abandonne proprement
            if ($eligible->isEmpty()) {
                Log::warning('LeadDispatcher: aucun conseiller éligible même après reset du cycle (lead_weight=0?).');
                return User::where('email', app(\App\Settings\EmailSettings::class)->fallback_admin_email)->first();
            }

            // 4. CHOIX DU GAGNANT
            // Parmi les éligibles, on prend celui qui attend depuis le plus longtemps
            // (celui dont last_lead_received_at est le plus vieux ou null)
            $winner = $eligible->sortBy('last_lead_received_at')->first();

            // 5. MISE À JOUR
            $winner->increment('leads_received_cycle');
            $winner->update(['last_lead_received_at' => now()]);

            Log::info("Lead auto assigné à : {$winner->first_name} (Cycle: {$winner->leads_received_cycle}/{$winner->lead_weight})");

            return $winner;
        });
    }
}
