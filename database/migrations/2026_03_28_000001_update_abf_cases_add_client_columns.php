<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('abf_cases', function (Blueprint $table) {
            $table->string('client_first_name', 100)->nullable()->after('advisor_code');
            $table->string('client_last_name', 100)->nullable()->after('client_first_name');
            $table->date('client_birth_date')->nullable()->after('client_last_name');
        });

        Schema::table('abf_cases', function (Blueprint $table) {
            $table->index('client_first_name');
            $table->index('client_last_name');
            $table->index('client_birth_date');
        });

        // Supprimer la contrainte CHECK json_valid sur payload et results
        // pour permettre le chiffrement (les données chiffrées ne sont pas du JSON valide)
        DB::statement('ALTER TABLE abf_cases MODIFY COLUMN `payload` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL');
        DB::statement('ALTER TABLE abf_cases MODIFY COLUMN `results` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL');
    }

    public function down(): void
    {
        Schema::table('abf_cases', function (Blueprint $table) {
            $table->dropIndex(['client_first_name']);
            $table->dropIndex(['client_last_name']);
            $table->dropIndex(['client_birth_date']);
            $table->dropColumn(['client_first_name', 'client_last_name', 'client_birth_date']);
        });

        DB::statement('ALTER TABLE abf_cases MODIFY COLUMN `payload` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`))');
        DB::statement('ALTER TABLE abf_cases MODIFY COLUMN `results` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`results`))');
    }
};
