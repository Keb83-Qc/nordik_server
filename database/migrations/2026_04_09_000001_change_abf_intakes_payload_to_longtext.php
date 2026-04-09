<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Change abf_intakes.payload from json to longtext.
 *
 * MySQL's JSON column type validates content on insert — it rejects encrypted
 * strings (which are not valid JSON). Since we want to encrypt the payload
 * with EncryptedJsonCast, we need longtext instead, which accepts any text.
 *
 * Rétrocompatibilité : les données JSON existantes restent lisibles
 * grâce au fallback de EncryptedJsonCast (tente decrypt, sinon json_decode).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('abf_intakes', function (Blueprint $table) {
            $table->longText('payload')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('abf_intakes', function (Blueprint $table) {
            // Note: existantes données chiffrées deviendront invalides si rollback
            $table->json('payload')->nullable()->change();
        });
    }
};
