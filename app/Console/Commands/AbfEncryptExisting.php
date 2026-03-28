<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class AbfEncryptExisting extends Command
{
    protected $signature   = 'abf:encrypt-existing {--dry-run : Affiche sans modifier}';
    protected $description = 'Re-chiffre les payload/results existants (JSON brut → encrypted:array)';

    public function handle(): int
    {
        $rows = DB::table('abf_cases')->get(['id', 'payload', 'results']);

        if ($rows->isEmpty()) {
            $this->info('Aucun enregistrement à traiter.');
            return 0;
        }

        $dry = $this->option('dry-run');
        $ok  = 0;
        $skip = 0;

        $this->info("Traitement de {$rows->count()} enregistrement(s)…");

        foreach ($rows as $row) {
            // Détecter si le payload est déjà chiffré (les valeurs chiffrées par Laravel
            // commencent par "eyJ" en base64 = début du token Crypt)
            $alreadyEncrypted = $this->isEncrypted($row->payload);

            if ($alreadyEncrypted) {
                $this->line("  ID {$row->id} — déjà chiffré, ignoré.");
                $skip++;
                continue;
            }

            $payloadArr = json_decode($row->payload, true) ?? [];
            $resultsArr = json_decode($row->results,  true) ?? [];

            if ($dry) {
                $this->line("  [dry-run] ID {$row->id} — payload re-chiffré.");
                $ok++;
                continue;
            }

            DB::table('abf_cases')->where('id', $row->id)->update([
                'payload' => Crypt::encryptString(json_encode($payloadArr)),
                'results' => json_encode($resultsArr), // results non chiffrés
                // Sync colonnes indexées depuis payload existant
                'client_first_name' => $payloadArr['client']['prenom'] ?? null,
                'client_last_name'  => $payloadArr['client']['nom']    ?? null,
                'client_birth_date' => $this->parseDob($payloadArr),
            ]);

            $this->line("  ID {$row->id} — chiffré + colonnes synchronisées.");
            $ok++;
        }

        $this->info("Terminé : {$ok} traité(s), {$skip} ignoré(s).");
        return 0;
    }

    private function isEncrypted(?string $value): bool
    {
        if (! $value) return false;
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    private function parseDob(array $payload): ?string
    {
        $client = $payload['client'] ?? [];
        $moisMap = [
            'Janvier' => '01', 'Février' => '02', 'Mars' => '03',
            'Avril' => '04', 'Mai' => '05', 'Juin' => '06',
            'Juillet' => '07', 'Août' => '08', 'Septembre' => '09',
            'Octobre' => '10', 'Novembre' => '11', 'Décembre' => '12',
        ];

        $jour   = $client['ddn_jour']   ?? null;
        $mois   = $client['ddn_mois']   ?? null;
        $annee  = $client['ddn_annee']  ?? null;

        if (! $jour || ! $mois || ! $annee) return null;

        $moisNum = $moisMap[$mois] ?? null;
        if (! $moisNum) return null;

        return "{$annee}-{$moisNum}-" . str_pad($jour, 2, '0', STR_PAD_LEFT);
    }
}
