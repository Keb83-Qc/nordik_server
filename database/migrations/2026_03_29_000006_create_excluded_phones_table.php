<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('excluded_phones', function (Blueprint $table) {
            $table->id();

            // Numéro tel que saisi (pour affichage)
            $table->string('phone', 30);

            // Numéro normalisé — chiffres uniquement, ex: 14185551234
            // Utilisé pour la recherche rapide (unique)
            $table->string('phone_normalized', 20)->unique();

            // Raison de l'exclusion
            $table->enum('reason', [
                'client_request',   // Le client a demandé à ne plus être contacté
                'lnnte_official',   // Inscrit sur la LNNTE officielle du CRTC
                'deceased',         // Décédé
                'competitor',       // Concurrent
                'do_not_disturb',   // Ne pas déranger (ex: shift de nuit, maladie)
                'internal',         // Décision interne
                'other',            // Autre
            ])->default('client_request');

            $table->text('notes')->nullable();

            // Qui a ajouté ce numéro
            $table->foreignId('added_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Expiration optionnelle (null = permanent)
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            // Index pour les recherches fréquentes
            $table->index('reason');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excluded_phones');
    }
};
