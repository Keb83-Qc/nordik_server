<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abf_intakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advisor_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('token', 64)->unique();       // UUID dans l'URL
            $table->string('access_code', 16);           // Code alphanumérique envoyé au client
            $table->string('client_first_name')->nullable();
            $table->string('client_last_name')->nullable();
            $table->string('client_email')->nullable();
            $table->string('locale', 5)->default('fr');
            $table->string('status', 20)->default('pending'); // pending | in_progress | completed
            $table->foreignId('abf_case_id')->nullable()->constrained('abf_cases')->nullOnDelete();
            $table->json('payload')->nullable();         // Données partielles sauvegardées en cours de remplissage
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abf_intakes');
    }
};
