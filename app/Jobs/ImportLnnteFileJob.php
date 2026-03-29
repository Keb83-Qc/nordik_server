<?php

namespace App\Jobs;

use App\Models\LnnteNumber;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Importe un fichier LNNTE (CRTC) en arrière-plan.
 *
 * Format accepté :
 *   - Texte brut, un numéro par ligne (ex: fichier officiel CRTC)
 *   - CSV dont la 1ère colonne contient le numéro
 *   - Encodages Windows (\r\n) ou Unix (\n)
 *   - Numéros à 10 chiffres (4185551234) ou formatés ((418) 555-1234)
 *
 * Usage :
 *   ImportLnnteFileJob::dispatch($storagePath, $notes, auth()->user());
 */
class ImportLnnteFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600; // 1 heure max

    public function __construct(
        protected string $storagePath,   // chemin relatif dans storage/app/
        protected string $importBatch,   // ex: "2026-03 CRTC Québec"
        protected User   $user,
    ) {}

    public function handle(): void
    {
        $absolutePath = Storage::path($this->storagePath);

        if (! file_exists($absolutePath)) {
            Log::error("ImportLnnteFileJob: fichier introuvable — {$this->storagePath}");
            $this->notifyUser('danger', 'Fichier introuvable', 'Le fichier LNNTE n\'a pas pu être lu.');
            return;
        }

        $added   = 0;
        $skipped = 0;
        $chunk   = [];
        $now     = now()->toDateTimeString();

        $handle = fopen($absolutePath, 'r');

        if (! $handle) {
            $this->notifyUser('danger', 'Erreur de lecture', 'Impossible d\'ouvrir le fichier.');
            return;
        }

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            // Ignorer lignes vides, commentaires et entêtes texte
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // CSV : prendre la première colonne seulement
            if (str_contains($line, ',')) {
                $line = explode(',', $line)[0];
                $line = trim($line, " \t\n\r\0\x0B\"'");
            }

            // Ignorer les lignes qui ne ressemblent pas à un numéro
            // (entêtes comme "Phone Number", "Numéro", etc.)
            $digitsOnly = preg_replace('/\D/', '', $line);
            if (strlen($digitsOnly) < 7) {
                continue;
            }

            $normalized = LnnteNumber::normalize($line);

            if (empty($normalized)) {
                $skipped++;
                continue;
            }

            $chunk[] = [
                'phone'            => $line,
                'phone_normalized' => $normalized,
                'import_batch'     => $this->importBatch ?: null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];

            // Insertion par lots de 500 pour éviter les dépassements mémoire
            if (count($chunk) >= 500) {
                $inserted = $this->insertChunk($chunk);
                $added   += $inserted;
                $skipped += (count($chunk) - $inserted);
                $chunk    = [];
            }
        }

        fclose($handle);

        // Insérer le dernier lot restant
        if (! empty($chunk)) {
            $inserted = $this->insertChunk($chunk);
            $added   += $inserted;
            $skipped += (count($chunk) - $inserted);
        }

        // Supprimer le fichier temporaire
        Storage::delete($this->storagePath);

        // Notification de fin
        $batch = $this->importBatch ? " (lot : {$this->importBatch})" : '';
        $body  = "**{$added}** numéro(s) importé(s)" .
                 ($skipped > 0 ? ", **{$skipped}** ignoré(s) (doublons ou invalides)" : '') .
                 " dans la table LNNTE officielle{$batch}.";

        $this->notifyUser('success', '✅ Import LNNTE terminé', $body);

        Log::info("ImportLnnteFileJob: {$added} ajoutés, {$skipped} ignorés — user #{$this->user->id}");
    }

    /**
     * Insère un lot en ignorant les doublons (phone_normalized unique).
     * Retourne le nombre de lignes réellement insérées.
     */
    private function insertChunk(array $chunk): int
    {
        $before = DB::table('lnnte_numbers')->count();

        DB::table('lnnte_numbers')->insertOrIgnore($chunk);

        $after = DB::table('lnnte_numbers')->count();

        return max(0, $after - $before);
    }

    /**
     * Envoie une notification Filament à l'utilisateur qui a lancé l'import.
     */
    private function notifyUser(string $status, string $title, string $body): void
    {
        Notification::make()
            ->$status()
            ->title($title)
            ->body($body)
            ->sendToDatabase($this->user);
    }

    public function failed(\Throwable $e): void
    {
        Log::error("ImportLnnteFileJob FAILED: " . $e->getMessage());

        $this->notifyUser(
            'danger',
            'Échec import LNNTE',
            'Une erreur s\'est produite : ' . $e->getMessage()
        );

        // Nettoyage fichier même en cas d'erreur
        Storage::delete($this->storagePath);
    }
}
