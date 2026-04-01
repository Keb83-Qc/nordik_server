<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix #8 — Uniformise marketing_email en input_type='radio' partout.
 *
 * Situation initiale :
 *   - auto (id=18)       : input_type='consent', options={"Oui":"Oui","Non":"Non"}
 *   - habitation (id=47) : input_type='consent', options={"Oui":"Oui","Non":"Non"}
 *   - bundle hab_ (id=82): input_type='radio',   options={"Oui":"Oui","Non":"Non"}  ← déjà bon
 *   - bundle common_ (id=93): input_type='consent', options=null
 *
 * Cible : tous à input_type='radio', options={"Oui":"Oui","Non":"Non"}
 * Cohérence UI + valeur stockée identique partout ('Oui' / 'Non').
 */
return new class extends Migration
{
    public function up(): void
    {
        $options = json_encode(['Oui' => 'Oui', 'Non' => 'Non']);

        // auto + habitation : changer consent → radio
        DB::table('chat_steps')
            ->where('identifier', 'marketing_email')
            ->whereIn('chat_type', ['auto', 'habitation'])
            ->update([
                'input_type' => 'radio',
                'options'    => $options,
                'updated_at' => now(),
            ]);

        // bundle common_marketing_email : ajouter les options manquantes
        DB::table('chat_steps')
            ->where('identifier', 'common_marketing_email')
            ->where('chat_type', 'bundle')
            ->update([
                'input_type' => 'radio',
                'options'    => $options,
                'updated_at' => now(),
            ]);

        // Invalide les caches concernés
        foreach (['auto', 'habitation', 'bundle'] as $type) {
            \Illuminate\Support\Facades\Cache::forget("chat_steps_{$type}");
        }
    }

    public function down(): void
    {
        // Restaure auto + habitation en consent
        DB::table('chat_steps')
            ->where('identifier', 'marketing_email')
            ->whereIn('chat_type', ['auto', 'habitation'])
            ->update([
                'input_type' => 'consent',
                'updated_at' => now(),
            ]);

        // Restaure common_marketing_email sans options
        DB::table('chat_steps')
            ->where('identifier', 'common_marketing_email')
            ->where('chat_type', 'bundle')
            ->update([
                'input_type' => 'consent',
                'options'    => null,
                'updated_at' => now(),
            ]);

        foreach (['auto', 'habitation', 'bundle'] as $type) {
            \Illuminate\Support\Facades\Cache::forget("chat_steps_{$type}");
        }
    }
};
