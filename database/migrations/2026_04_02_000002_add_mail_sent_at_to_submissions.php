<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix #3 — Ajoute mail_sent_at à la table submissions.
 * Sert de garde d'idempotence dans finalize() :
 * si mail_sent_at est déjà rempli, on ne renvoie pas l'email.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->timestamp('mail_sent_at')->nullable()->after('is_phone_excluded');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('mail_sent_at');
        });
    }
};
