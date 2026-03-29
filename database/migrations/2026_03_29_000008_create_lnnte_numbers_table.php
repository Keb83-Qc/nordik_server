<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lnnte_numbers', function (Blueprint $table) {
            $table->id();

            // Numéro original (pour affichage)
            $table->string('phone', 30);

            // Numéro normalisé — chiffres uniquement (ex: 14185551234)
            // Unique pour éviter les doublons lors des imports
            $table->string('phone_normalized', 20)->unique();

            // Date de l'import qui a ajouté ce numéro
            $table->string('import_batch', 100)->nullable()
                ->comment('Identifiant du lot d\'import (ex: 2026-03 CRTC)');

            $table->timestamps();

            $table->index('import_batch');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lnnte_numbers');
    }
};
