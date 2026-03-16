<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FixUserSlugs extends Command
{
    protected $signature = 'users:fix-slugs {--dry-run : Affiche les changements sans sauvegarder}';
    protected $description = 'Génère les slugs manquants pour tous les conseillers (URL /conseiller/{slug})';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $users = User::query()
            ->whereNull('slug')
            ->orWhere('slug', '')
            ->orderBy('id')
            ->get();

        if ($users->isEmpty()) {
            $this->info('✅ Tous les utilisateurs ont déjà un slug.');
            return self::SUCCESS;
        }

        $this->info("🔍 {$users->count()} utilisateur(s) sans slug" . ($dry ? ' [DRY-RUN]' : '') . ':');
        $this->newLine();

        $fixed = 0;

        foreach ($users as $user) {
            $newSlug = User::generateUniqueSlug($user->first_name, $user->last_name, $user->id);

            $this->line(sprintf(
                '  ID %-5s %-30s → %s',
                $user->id,
                "{$user->first_name} {$user->last_name}",
                $newSlug
            ));

            if (!$dry) {
                $user->slug = $newSlug;
                $user->saveQuietly(); // évite de re-déclencher les events booted

                // Invalider le cache de ce profil
                Cache::forget("team_member_{$newSlug}");
            }

            $fixed++;
        }

        $this->newLine();

        if ($dry) {
            $this->warn("⚠️  Dry-run — aucune modification enregistrée. Relancez sans --dry-run pour appliquer.");
        } else {
            $this->info("✅ {$fixed} slug(s) généré(s) avec succès.");
            Cache::forget('team_index_*'); // purge les listes cachées
        }

        return self::SUCCESS;
    }
}
